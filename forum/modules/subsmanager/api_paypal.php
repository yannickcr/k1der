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
|   > Payment Gateway API: PAYPAL
|   > Module written by Matt Mecham
|   > Date started: 21th August 2003
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


	var $can_do_recurring_billing = 1;
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
		// Format the info string
		//---------------------------------------
		
		$ibforums->lang['sc_normal_string'] = sprintf( $ibforums->lang['sc_normal_string'],
													   $sub_upgrade['sub_title'],
													   sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->cho_currency['subcurrency_exchange'] ).' '.$this->class->cho_currency['subcurrency_code']
													 );
		
		if ( $extra['subextra_recurring'] == 1 )
		{
			return $this->class->html->do_paypal_normal_recurring_screen(
																 $this->class->method['submethod_use_currency'],
																 $ibforums->member['id'],
																 $sub_upgrade['sub_id'], 
																 strtoupper($sub_upgrade['sub_unit']),
																 $sub_upgrade['sub_length'],
																 sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->all_currency[ $this->class->method['submethod_use_currency'] ]['subcurrency_exchange'] ),
																 $pay_method['submethod_email'],
																 $sub_upgrade['sub_title'],
																 $ibforums->lang['sc_normal_string']
															   );
		}
		else
		{
			return $this->class->html->do_paypal_normal_screen(
																 $this->class->method['submethod_use_currency'],
																 $ibforums->member['id'],
																 $sub_upgrade['sub_id'], 
																 sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->all_currency[ $this->class->method['submethod_use_currency'] ]['subcurrency_exchange'] ),
																 $pay_method['submethod_email'],
																 $sub_upgrade['sub_title'],
																 $ibforums->lang['sc_normal_string']
															   );
		
		}
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
		// Format the info string
		//---------------------------------------
		
		$balance = ($sub_upgrade['sub_cost'] * $this->class->all_currency[ $pay_method['submethod_use_currency'] ]['subcurrency_exchange']) - $cur_trans['subtrans_paid'];
		
		$end_date = ( $sub_upgrade['sub_unit'] == 'x' or $sub_current['sub_unit'] == 'x' )
					  ? $ibforums->lang['no_expire']
					  : $std->get_date( $cur_trans['subtrans_end_date'], 'JOINED' );
					  
		$ibforums->lang['sc_upgrade_string'] = sprintf( $ibforums->lang['sc_upgrade_string'],
														$sub_current['sub_title'],
														$sub_upgrade['sub_title'],
														$end_date,
														sprintf( "%.2f", $balance * $this->class->cho_currency['subcurrency_exchange'] ).' '.$this->class->cho_currency['subcurrency_code']
													  );
		
		$time_left_to_run = $cur_trans['subtrans_end_date'] - time();
		
		$time_left_to_run = ceil($time_left_to_run / 86400);
		$time_left_units  = 'D';
		
		
		if ( $time_left_to_run < 1 )
		{
			$time_left_to_run = 1;
		}
		else if ( $time_left_to_run > 30 )
		{
			$time_left_units = 'M';
			
			$time_left_to_run = ceil($time_left_to_run / 30);
		}
		
		if ( $extra['subextra_recurring'] == 1 )
		{
			return $this->class->html->do_paypal_upgrade_recurring_screen(
																 $this->class->method['submethod_use_currency'],
																 $ibforums->member['id'],
																 $sub_upgrade['sub_id'], 
																 $balance,
																 $time_left_to_run,
																 $time_left_units,
																 strtoupper($sub_upgrade['sub_unit']),
																 $sub_upgrade['sub_length'],
																 sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->all_currency[ $this->class->method['submethod_use_currency'] ]['subcurrency_exchange'] ),
																 $pay_method['submethod_email'],
																 $sub_upgrade['sub_title']. ' ('.$ibforums->lang['gw_upgrade'].')',
																 $cur_trans['subtrans_id'],
																 $ibforums->lang['sc_upgrade_string']
															   );
		}
		else
		{
			return $this->class->html->do_paypal_upgrade_screen(
																 $this->class->method['submethod_use_currency'],
																 $ibforums->member['id'],
																 $sub_upgrade['sub_id'], 
																 $balance,
																 $pay_method['submethod_email'],
																 $sub_upgrade['sub_title']. ' ('.$ibforums->lang['gw_upgrade'].')',
																 $cur_trans['subtrans_id'],
																 $ibforums->lang['sc_upgrade_string']
															   );
		
		}
		
		
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
		// Throw back to PayPal to verify
		// Based on PayPal example code
		//--------------------------------------
		
		$state = 'INVALID';
		
		$post_back[] = 'cmd=_notify-validate';
		
		foreach ($_POST as $key => $val)
		{
			$post_back[] = $key . '=' . urlencode ($val);
		}
		
		$post_back_str = implode('&', $post_back);
		
		// Post the ..er.. post
		
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($post_back_str) . "\r\n\r\n";
		
		$sock = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
		
		@socket_set_timeout($sock, 30);
		
		fputs ($sock, $header . $post_back_str);
		
		while ( ! feof( $sock ) )
		{
			$result = fgets( $sock, 1024 );
			
			if (strcmp($result, 'VERIFIED') == 0)
			{
				$state = 'VERIFIED';
				break;
			}
			else if (strcmp($result, 'INVALID') == 0)
			{
				$state = 'INVALID';
				break;
			}
		}
		
		fclose($sock);
		
		
		//--------------------------------------
		// Set up....
		//--------------------------------------
		
		$update         = array();
		$payment_status = array(
								 'Completed' => 'paid',
								 'Failed'    => 'failed',
								 'Denied'    => 'failed',
								 'Refunded'  => 'failed'
								);
								
		$day_to_seconds = array( 'd' => 86400,
								 'w' => 604800,
								 'm' => 2592000,
								 'y' => 31536000,
							   );
		
		
		//--------------------------------------
		// Process....
		//--------------------------------------
		
		$new_sub_id       = intval($_POST['item_number']);
		$member_id        = intval($_POST['custom']);
		$temp             = trim($_POST['invoice']);
		$payment_status   = $_POST['payment_status'];
		$payment_amount   = $_POST['mc_gross'];
		$txn_id           = $_POST['txn_id'];
		$payment_currency = $_POST['mc_currency'];
		$receiver_email   = $_POST['receiver_email'];
		$payer_email      = $_POST['payer_email'];
		$subscribe_id     = $_POST['subscr_id'];
		
		//--------------------------------------
		// Fix ticket: #56264
		// Second POST - we can ignore
		//--------------------------------------
		
		if ( ! $txn_id and $_POST['txn_type'] == 'subscr_signup' )
		{
			exit();
		}
		
		list( $cur_sub_id, ) = explode( 'x', $temp );
		
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
		
		if ( $state == 'VERIFIED' )
		{
			//--------------------------------------
			// Check for txn_id - if already used, this
			// is a dupe from a repeated form submit
			//--------------------------------------
			
			$DB->query("SELECT subtrans_id FROM ibf_subscription_trans WHERE subtrans_trxid='".addslashes($txn_id)."'");
			
			if ( $trans = $DB->fetch_row() )
			{
				// Is this a reversal?
				
				if ( $_POST['payment_status'] == 'Refunded' )
				{
					//-----------------------------
					// Update trans
					//-----------------------------
					
					$DB->query("UPDATE ibf_subscription_trans SET subtrans_state='failed' WHERE subtrans_id={$trans['subtrans_id']}");
					
					$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id=$new_sub_id");
						
					$new_sub = $DB->fetch_row();
					
					$this->class->_do_failed_member($new_sub, $member, $trans['subtrans_id']);
					
					//-----------------------------
					// Write Log
					//-----------------------------
					
					$this->class->_do_log("{$this->class->method_name}: Reversal Completed");
					exit();
				}
				else
				{
					$this->class->_do_log("{$this->class->method_name}: Duplicate transaction ID - failing and exiting transaction");
					exit();
				}
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
					
					if ( $payment_currency != $this->class->method['sub_use_currency'] )
					{
						$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Wrong currency ($payment_currency)");
					}
					
					if ( ! $member['id'] )
					{
						$this->class->_do_log("{$this->class->method_name}: Could not locate a member id to upgrade");
					}
					
					//-----------------------
					// What to do...
					//-----------------------
					
					if ( strstr( $_POST['txn_type'], 'subscr_' ) )
					{
						//-----------------------
						// SUBSCRIPTION
						//-----------------------
						
						$update = $this->_do_subs_payment_check($new_sub, $cur_details);
						
						//-----------------------
						// Update subscription
						//-----------------------
						
						$update['subtrans_paid']     = $update['subtrans_paid'] * $this->class->def_currency['subcurrency_exchange'];
						
						$update['subtrans_trxid']    = $txn_id;
						$update['subtrans_sub_id']   = $new_sub['sub_id'];
						$update['subtrans_subscrid'] = $_POST['subscr_id'];
						
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
						// UPDATE MEMBERS
						//-----------------------
						
						if ( $update['subtrans_state'] == 'paid' )
						{
							$this->class->_do_paid_member($new_sub, $member, $cur_details['subtrans_id']);
						}
						else
						{
							$this->class->_do_failed_member($new_sub, $member, $cur_details['subtrans_id']);
						}
						
						$this->class->_do_log("{$this->class->method_name}: Subscription upgrade. Set trans_id {$cur_details['subtrans_id']} to {$update['subtrans_state']}, paid {$update['subtrans_paid']}");
						
					}
					else if ( $_POST['txn_type'] == 'web_accept' )
					{
						
						//-----------------------
						// Normal One off upgrade transaction
						//-----------------------
						
						$update = $this->_do_subs_payment_check_norecurr($new_sub, $cur_details);
						
						//-----------------------
						// Update subscription
						//-----------------------
						
						$update['subtrans_paid'] = $update['subtrans_paid'] * $this->class->def_currency['subcurrency_exchange'];
						
						$update['subtrans_trxid']    = $txn_id;
						$update['subtrans_sub_id']   = $new_sub['sub_id'];
						$update['subtrans_subscrid'] = $_POST['subscr_id'];
						
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
					else
					{
						$this->class->_do_log("{$this->class->method_name}: Subscription Start. Unknown trx_type: {$_POST['txn_type']}");
					}
					
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
					
				if ( strstr( $_POST['txn_type'], 'subscr_' ) )
				{
					//-----------------------
					// SUBSCRIPTION START
					//-----------------------
					
					$update = $this->_do_subs_payment_check($new_sub);
					
					//-----------------------
					// Start subscription
					//-----------------------
					
					$update['subtrans_paid'] = $update['subtrans_paid'] * $this->class->def_currency['subcurrency_exchange'];
					
					$update['subtrans_trxid']      = $txn_id;
					$update['subtrans_sub_id']     = $new_sub['sub_id'];
					$update['subtrans_subscrid']   = $_POST['subscr_id'];
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
					
					//-----------------------
					// Just updating a subsc?
					//-----------------------
					
					$no_insert = 0;
					
					if ( $subscribe_id )
					{
						$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_subscrid='".addslashes($subscribe_id)."'");
						
						if ( $subid = $DB->fetch_row() )
						{
							// Okay, existing subscription we're updating, so...
							
							unset($upgrade['subtrans_start_date']);
							unset($upgrade['subtrans_end_date']);
							unset($upgrade['subtrans_old_group']);
							unset($upgrade['subtrans_cumulative']);
							
							$dbs = $DB->compile_db_update_string($update);
						
							$DB->query("UPDATE ibf_subscription_trans SET $dbs WHERE subtrans_subscrid='".addslashes($subscribe_id)."'");
							
							$no_insert = 1;
						}
					}
					
					if ( $no_insert == 0 )
					{
						$dbs = $DB->compile_db_insert_string($update);
						
						$DB->query("INSERT INTO ibf_subscription_trans ({$dbs['FIELD_NAMES']}) VALUES({$dbs['FIELD_VALUES']})");
						
						$newid = $DB->get_insert_id();
						
						//-----------------------
						// mark all old subs as dead
						//-----------------------
						
						$DB->query("UPDATE ibf_subscription_trans SET subtrans_state='dead' WHERE subtrans_state='paid' AND subtrans_member_id={$member_id} AND subtrans_id != $newid");
					}
					
					//-----------------------
					// UPDATE MEMBERS
					//-----------------------
					
					if ( $update['subtrans_state'] == 'paid' )
					{
						$this->class->_do_paid_member($new_sub, $member, $newid);
					}
					else
					{
						$this->class->_do_failed_member($new_sub, $member, $newid);
					}
					
					$this->class->_do_log("{$this->class->method_name}: Subscription started. Set trans_id {$cur_details['subtrans_id']} to {$update['subtrans_state']}, paid {$update['subtrans_paid']}");
					
				}
				else if ( $_POST['txn_type'] == 'web_accept' )
				{
					//-----------------------
					// ONE OFF PAYMENT
					//-----------------------
					
					$update = $this->_do_subs_payment_check_norecurr($new_sub);
					
					//-----------------------
					// Start subscription
					//-----------------------
					
					$update['subtrans_paid'] = $update['subtrans_paid'] * $this->class->def_currency['subcurrency_exchange'];
					
					$update['subtrans_trxid']      = $txn_id;
					$update['subtrans_sub_id']     = $new_sub['sub_id'];
					$update['subtrans_subscrid']   = $_POST['subscr_id'];
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
					
					//-----------------------
					// Just updating a subsc?
					//-----------------------
					
					$no_insert = 0;
					
					$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_member_id=$member_id AND subtrans_sub_id={$new_sub['sub_id']} and subtrans_state='pending'");
					
					if ( $cur = $DB->fetch_row() )
					{
						// Okay, existing subscription we're updating, so...
						
						unset($upgrade['subtrans_start_date']);
						unset($upgrade['subtrans_end_date']);
						unset($upgrade['subtrans_old_group']);
						unset($upgrade['subtrans_cumulative']);
						
						$dbs = $DB->compile_db_update_string($update);
					
						$DB->query("UPDATE ibf_subscription_trans SET $dbs WHERE subtrans_id='{$cur['subtrans_id']}'");
						
						$no_insert = 1;
						
					}
					
					if ( $no_insert == 0 )
					{
						$dbs = $DB->compile_db_insert_string($update);
						
						$DB->query("INSERT INTO ibf_subscription_trans ({$dbs['FIELD_NAMES']}) VALUES({$dbs['FIELD_VALUES']})");
						
						$newid = $DB->get_insert_id();
						
						//-----------------------
						// mark all old subs as dead
						//-----------------------
						
						$DB->query("UPDATE ibf_subscription_trans SET subtrans_state='dead' WHERE subtrans_state='paid' AND subtrans_member_id={$member_id} AND subtrans_id != $newid");
					}
					
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
		}
		else
		{
			//-----------------------
			// NOT VERIFIED
			//-----------------------
			
			$this->class->_do_log("{$this->class->method_name}: UPGRADE - NOT VERIFIED, refused from PayPal");
		}

	
	}
	
	
	function _do_subs_payment_check_norecurr($new_sub, $cur_details=array())
	{
	
		$balance = $new_sub['sub_cost'] - $cur_details['subtrans_paid'];
		
		$update = array();
		$update['subtrans_method'] = 'paypal';
	
		if ( $_POST['payment_status'] == 'Completed' )
		{
			// Check amount..
			
			if ( $_POST['mc_gross'] == $new_sub['sub_cost'] )
			{
				//-----------------------
				// Paid correct amount
				//-----------------------
				
				$update['subtrans_paid']  = $_POST['mc_gross'];
				$update['subtrans_state'] = 'paid';
			}
			else
			{
				if ( $cur_details['subtrans_id'] )
				{
					// Upgrading....
					
					if ( $_POST['mc_gross'] == $balance )
					{
						$update['subtrans_paid']  = $_POST['mc_gross'];
						$update['subtrans_state'] = 'paid';
					}
					
				}
				else
				{
					//-----------------------
					// Incorrect amount
					//-----------------------
					
					$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Wrong payment amount. Looking for {$new_sub['sub_cost']}, got {$_POST['mc_gross']}");
				}
			}
			
		}
		else if ( $_POST['payment_status'] == 'Pending' )
		{
			//-----------------------
			// Failed...
			//-----------------------
			
			$update['subtrans_state'] = 'pending';
		}
		else
		{
			//-----------------------
			// End of subscription
			//-----------------------
			
			$update['subtrans_state'] = 'failed';
		}
		
		return $update;
	}
	
	
	
	
	function _do_subs_payment_check($new_sub, $cur_details=array())
	{
		$update = array();
		$update['subtrans_method'] = 'paypal';
		
		$balance = $new_sub['sub_cost'] - $cur_details['subtrans_paid'];
	
		if ( $_POST['txn_type'] == 'subscr_signup' OR $_POST['txn_type'] == 'subscr_payment' )
		{
			// Check amount..
			
			if ( $_POST['amount1'] )
			{
				//-----------------------
				// First period, get amount.
				//-----------------------
				
				if ( $balance != $_POST['amount1'] )
				{
					$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Wrong upgrade subs amount. Looking for $balance, got {$_POST['amount1']}");
				}
				
				$update['subtrans_paid']  = $_POST['amount1'];
				$update['subtrans_state'] = 'paid';
			}
			else if ( $_POST['amount3'] )
			{
				//-----------------------
				// Real subscription
				//-----------------------
				
				if ( $new_sub['sub_cost'] != $_POST['amount3'] )
				{
					$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Wrong upgrade subs amount. Looking for {$new_sub['sub_cost']}, got {$_POST['amount3']}");
				}
				
				$update['subtrans_paid']  = $new_sub['sub_cost'];
				$update['subtrans_state'] = 'paid';
			}
			else
			{
				//-----------------------
				// If all else fails..
				//-----------------------
				
				if ( $new_sub['sub_cost'] != $_POST['mc_gross'] AND $_POST['mc_gross'] != $balance )
				{
					$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Wrong upgrade subs amount. Looking for {$new_sub['sub_cost']}, got {$_POST['mc_gross']}");
				}
				
				$update['subtrans_paid']  = $_POST['mc_gross'];
				$update['subtrans_state'] = 'paid';
			}
			
		}
		else if ( $_POST['txn_type'] == 'subscr_failed' )
		{
			//-----------------------
			// Failed...
			//-----------------------
			
			$update['subtrans_state'] = 'failed';
		}
		else if ( $_POST['txn_type'] == 'subscr_cancel' )
		{
			//-----------------------
			// Dead...
			//-----------------------
			
			$update['subtrans_state'] = 'dead';
		}
		else if ( $_POST['txn_type'] == 'subscr_eot' )
		{
			//-----------------------
			// End of subscription
			//-----------------------
			
			$update['subtrans_state'] = 'dead';
		}
		
		return $update;
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