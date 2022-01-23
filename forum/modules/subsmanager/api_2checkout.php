<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v1.2
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Payment Gateway API: 2CHECKOUT
|   > Module written by Matt Mecham
|   > Date started: 8th September 2003
|
| EXPECTED ROUTINES:
| - show_normal_payment_screen  (Show pay options for chosen new sub)
| - show_upgrade_payment_screen (Show pay options for chosen upgrade)
| - validate_payment            (Used by gateway callback tools)
| - acp_return_package_variables (Return custom array of package extras)
| - acp_return_method_variables (Return custom array of method extras)
|
+--------------------------------------------------------------------------
*/

//---------------------------------------
// Security check
//---------------------------------------
		
if ( IPB_CALLED != 1 )
{
	print "You cannot access this module in this manner";
	exit();
}

class gateway {


	var $can_do_recurring_billing = 0;
	var $can_do_upgrades          = 1;
	var $class                    = "";
	
	//-----------------------------------------------
	// register_class($class)
	//
	// Register a $this-> with this class 
	//
	//-----------------------------------------------

	function register_class(&$class)
	{
		$this->class = $class;
	}
	
	
	//---------------------------------------
	// Show NORMAL payment screen
	//---------------------------------------
	
	function show_normal_payment_screen($sub_upgrade, $pay_method, $extra)
	{
		global $ibforums, $std, $DB;
		
		$this->class->method = $pay_method;
		
		//---------------------------------------
		// Check we have chosen package details
		//---------------------------------------
		
		if ( ! $sub_upgrade['sub_id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
		}
		
		//---------------------------------------
		// Pass to 2CO
		//---------------------------------------
		
		$cost = sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->cho_currency['subcurrency_exchange'] );
		
		$std->boink_it("https://www.2checkout.com/cgi-bin/sbuyers/cartpurchase.2c?sid=".$this->class->method['submethod_sid']."&total=".$cost."&cart_order_id=".$sub_upgrade['sub_id']."x".$ibforums->member['id']."x0");
		
		exit();
		
	}
	
	
	
	//---------------------------------------
	// Show payment upgrade screen
	//---------------------------------------
	
	function show_upgrade_payment_screen($sub_current, $sub_upgrade, $pay_method, $extra)
	{
		global $ibforums, $std, $DB;
		
		$this->class->method = $pay_method;
		
		//---------------------------------------
		// Check we can do upgrades
		//---------------------------------------
	
		if ( $this->can_do_upgrades != 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_upgrade' ) );
		}
		
		//---------------------------------------
		// Check we have current package details
		//---------------------------------------
		
		if ( ! $sub_current['sub_id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
		}
		
		//---------------------------------------
		// Check we have upgrade to package details
		//---------------------------------------
		
		if ( ! $sub_upgrade['sub_id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
		}
		
		//--------------------------------------------
		// We're upgrading!! Yay! - Get cur subs
		//--------------------------------------------
		
		$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_member_id={$ibforums->member['id']} AND subtrans_sub_id={$sub_current['sub_id']} AND subtrans_state='paid'");
		
		if ( ! $cur_trans = $DB->fetch_row() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_curid' ) );
		}
		
		//---------------------------------------
		// Pass to 2CO
		//---------------------------------------
		
		$cost = sprintf( "%.2f", ($sub_upgrade['sub_cost'] - $cur_trans['subtrans_paid']) * $this->class->all_currency[ $pay_method['submethod_use_currency'] ]['subcurrency_exchange'] );
		
		$std->boink_it("https://www.2checkout.com/cgi-bin/sbuyers/cartpurchase.2c?sid=".$this->class->method['submethod_sid']."&total=".$cost."&cart_order_id=".$sub_upgrade['sub_id']."x".$ibforums->member['id']."x".$cur_trans['subtrans_id']);
		
		exit();
		
	}

	//---------------------------------------
	// Process return validation
	//
	// Does the donkey work
	//---------------------------------------
	
	function validate_payment()
	{
		global $ibforums, $std, $DB;
		
		//--------------------------------------
		// Are we allowing auto manipulation?
		//--------------------------------------
		
		if ( $this->class->method['submethod_is_auto'] != 1 )
		{
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: IPN set up to return, but ACP settings have auto validate switched off");
		}
		
		//--------------------------------------
		// Test POST data
		//--------------------------------------
		
		if ( empty( $_POST ) )
		{
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: POST DATA EMPTY");
		}
		
		//$this->class->_do_log("{$this->class->method_name}: TEST");
		
		//--------------------------------------
		// Throw back to NOCHEX to verify
		// Based on PayPal example code
		//--------------------------------------
		
		$state = 'AUTHORISED';
		
		/*if ( preg_match( "#https?://([^/]*)(www\.2checkout\.com|2checkout\.com)#i", $_SERVER['HTTP_REFERER'] ) )
		{
			$state = 'AUTHORISED';
		}
		else
		{
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: BAD referrer {$_SERVER['HTTP_REFERER']}");
		}*/
		
		//--------------------------------------
		// Set up....
		//--------------------------------------
		
		$update         = array();
		
		$day_to_seconds = array( 'd' => 86400,
								 'w' => 604800,
								 'm' => 2592000,
								 'y' => 31536000,
							   );
		
		//--------------------------------------
		// Process....
		//--------------------------------------
		
		$txn_id           = trim($_POST['order_number']);
		$payment_amount   = $_POST['total'];
		
		list( $member_id, $new_sub_id, $cur_sub_id) = explode( 'x', $_POST['cart_order_id'] );
		
		$member_id  = intval($member_id);
		$new_sub_id = intval($new_sub_id);
		$cur_sub_id = intval($cur_sub_id);
		
		//--------------------------------------
		// Check for member id
		//--------------------------------------
		
		if ( $member_id > 0 )
		{
			$DB->query("SELECT * FROM ibf_members WHERE id={$member_id}");
			
			$member = $DB->fetch_row();
		}
		
		//--------------------------------------
		// VERIFIED? LETS GO
		//--------------------------------------
		
		if ( $state == 'AUTHORISED' )
		{
			//--------------------------------------
			// Check for txn_id - if already used, this
			// is a dupe from a repeated form submit
			//--------------------------------------
			
			$DB->query("SELECT subtrans_id FROM ibf_subscription_trans WHERE subtrans_trxid='".addslashes($txn_id)."'");
			
			if ( $trans = $DB->fetch_row() )
			{
				// Is this a reversal?
				
				$this->class->_do_log("{$this->class->method_name}: Duplicate transaction ID - failing and exiting transaction");
				
			}
		
			if ( $cur_sub_id > 0 )
			{
				// UPGRADE
				
				if ( $new_sub_id AND $member_id )
				{
					//-----------------------
					// Get new pkg details
					//-----------------------
					
					$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id=$new_sub_id");
					
					$new_sub = $DB->fetch_row();
					
					if ( ! $new_sub['sub_id'] )
					{
						$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: No uprgade to package found");
					}
					
					//-----------------------
					// Get current details
					//-----------------------
					
					$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_member_id=$member_id AND subtrans_id=$cur_sub_id");
					
					$cur_details = $DB->fetch_row();
					
					if ( ! $cur_details['subtrans_id'] )
					{
						$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Upgrade, but no original package found");
					}
					
					if ( ! $member['id'] )
					{
						$this->class->_do_log("{$this->class->method_name}: Could not locate a member id to upgrade");
					}
					
					//-----------------------
					// Update subscription
					//-----------------------
					
					$update['subtrans_paid']     = $new_sub['sub_cost'] - $cur_details['subtrans_paid'];
					$update['subtrans_trxid']    = $txn_id;
					$update['subtrans_sub_id']   = $new_sub['sub_id'];
					$update['subtrans_method']   = '2checkout';
					
					if ( $new_sub['sub_unit'] == 'x' )
					{
						$update['subtrans_end_date'] = 9999999999;
					}
					
					$query = "UPDATE ibf_subscription_trans SET ";
					
					foreach( $update as $f => $v )
					{
						$query .= "$f='$v',";
					}
					
					$query = substr( $query, 0, -1 );
					
					if ( $update['subtrans_paid'] > 0.00 )
					{
						$query .= ", subtrans_cumulative=subtrans_cumulative+{$update['subtrans_paid']}";
					}
					
					$query .= " WHERE subtrans_id=".$cur_details['subtrans_id'];
					
					$DB->query($query);
					
					//-----------------------
					// UPDATE MEMBER
					//-----------------------
					
					if ( $update['subtrans_state'] == 'paid' )
					{
						$this->class->_do_paid_member($new_sub, $member, $cur_details['subtrans_id']);
					}
					else
					{
						$this->class->_do_failed_member($new_sub, $member, $cur_details['subtrans_id']);
					}
					
					$this->class->_do_log("{$this->class->method_name}: Subscription upgrade (one off payment). Set trans_id {$cur_details['subtrans_id']} to {$update['subtrans_state']}, paid {$update['subtrans_paid']}");
				
				}
			}
			else
			{
				//-----------------------
				// Normal Payment (non upgrade)
				//-----------------------
				
				if ( ! $member['id'] )
				{
					$this->class->_do_log("{$this->class->method_name}: Could not locate a member id to upgrade");
				}
				
				//-----------------------
				// Get new pkg details
				//-----------------------
				
				$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id=$new_sub_id");
				
				$new_sub = $DB->fetch_row();
				
				if ( ! $new_sub['sub_id'] )
				{
					$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: No start sub package found");
				}
				
				if ( $payment_currency != $this->class->method['submethod_use_currency'] )
				{
					$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Wrong currency ($payment_currency)");
				}
					
				//-----------------------
				// ONE OFF PAYMENT
				//-----------------------
				
				$update = $this->_do_subs_payment_check_norecurr($new_sub);
				
				//-----------------------
				// Start subscription
				//-----------------------
				
				$update['subtrans_paid']       = $new_sub['sub_cost'];
				$update['subtrans_trxid']      = $txn_id;
				$update['subtrans_sub_id']     = $new_sub['sub_id'];
				$update['subtrans_cumulative'] = $update['subtrans_paid'];
				$update['subtrans_member_id']  = $member['id'];
				$update['subtrans_old_group']  = $member['mgroup'];
				$update['subtrans_start_date'] = time();
				
				if ( $new_sub['sub_unit'] == 'x' )
				{
					$update['subtrans_end_date'] = 9999999999;
				}
				else
				{
					$update['subtrans_end_date']   = time() + ( $new_sub['sub_length'] * $day_to_seconds[ $new_sub['sub_unit'] ] );
				}
					
				$update['subtrans_method']     = '2checkout';
				
				$dbs = $DB->compile_db_insert_string($update);
				
				$DB->query("INSERT INTO ibf_subscription_trans ({$dbs['FIELD_NAMES']}) VALUES({$dbs['FIELD_VALUES']})");
				
				$newid = $DB->get_insert_id();
				
				//-----------------------
				// mark all old subs as dead
				//-----------------------
				
				$DB->query("UPDATE ibf_subscription_trans SET subtrans_state='dead' WHERE subtrans_state='paid' AND subtrans_member_id={$member_id} AND subtrans_id != $newid");
			
				//-----------------------
				// UPDATE MEMBERS
				//-----------------------
					
				if ( $update['subtrans_state'] == 'paid' )
				{
					$this->class->_do_paid_member($new_sub, $member, $cur['subtrans_id']);
				}
				else
				{
					$this->class->_do_failed_member($new_sub, $member, $cur['subtrans_id']);
				}
				
				$this->class->_do_log("{$this->class->method_name}: Subscription started (non recurring). Set trans_id {$cur_details['subtrans_id']} to {$update['subtrans_state']}, paid {$update['subtrans_paid']}");
			
			}
		}
		else
		{
			//-----------------------
			// NOT VERIFIED
			//-----------------------
			
			$this->class->_do_log("{$this->class->method_name}: UPGRADE - NOT VERIFIED, refused from 2Checkout");
		}

	}
	
	
	
	//---------------------------------------
	// Return ACP Package  Variables
	//
	// Returns names for the package custom
	// fields, etc
	//---------------------------------------
	
	function acp_return_package_variables()
	{
	
		$return = array(
						  'subextra_custom_1' => array( 'used' => 0, 'varname' => '' ),
						  'subextra_custom_2' => array( 'used' => 0, 'varname' => '' ),
						  'subextra_custom_3' => array( 'used' => 0, 'varname' => '' ),
						  'subextra_custom_4' => array( 'used' => 0, 'varname' => '' ),
						  'subextra_custom_5' => array( 'used' => 0, 'varname' => '' ),
					   );
					   
		return $return;
	
	}
	
	//---------------------------------------
	// Return ACP Method Variables
	//
	// Returns names for the package custom
	// fields, etc
	//---------------------------------------
	
	function acp_return_method_variables()
	{
	
		$return = array(
						  'submethod_custom_1' => array( 'used' => 0, 'varname' => '' ),
						  'submethod_custom_2' => array( 'used' => 0, 'varname' => '' ),
						  'submethod_custom_3' => array( 'used' => 0, 'varname' => '' ),
						  'submethod_custom_4' => array( 'used' => 0, 'varname' => '' ),
						  'submethod_custom_5' => array( 'used' => 0, 'varname' => '' ),
					   );
					   
		return $return;
	
	}
	
	
}

 
?>