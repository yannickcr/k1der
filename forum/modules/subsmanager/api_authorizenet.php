<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v1.3
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
|   > Payment Gateway API: AUTHORIZE.NET
|   > Module written by Matt Mecham
|   > Date started: April 2004
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
	var $can_do_upgrades          = 0;
	var $class                    = "";
	var $txn_key                  = '';
	var $fp_hash                  = '';
	var $fp_time                  = '';
	var $fp_seq                   = '';
	
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
		
		$this->txn_key = $pay_method['submethod_email'];								 
		$this->fp_seq  = rand(0,1000);
		$this->fp_time = time();
		$this->amount  = sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->all_currency[ $this->class->method['submethod_use_currency'] ]['subcurrency_exchange'] );									 
		$this->fp_hash = $this->calculatefp( $pay_method['submethod_sid'], $this->txn_key, $this->amount, $this->fp_seq, $this->fp_time, $this->class->method['submethod_use_currency'] );
		
		return $this->class->html->do_authorizenet_normal_screen(
															 $this->class->method['submethod_use_currency'],
															 $ibforums->member['id'],
															 $sub_upgrade['sub_id'], 
															 $this->amount,
															 $sub_upgrade['sub_title'],
															 $ibforums->lang['sc_normal_string'],
															 $pay_method['submethod_sid'],
															 $this->fp_hash,
															 $this->fp_seq,
															 $this->fp_time
															 
														   );
		
		
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
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: Safshop set up to return, but ACP settings have auto validate switched off");
		}
		
		//--------------------------------------
		// Test POST data
		//--------------------------------------
		
		if ( empty( $_POST ) )
		{
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: POST DATA EMPTY");
		}
		
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
		
		$an_response_code = $_POST['x_response_code'];
		$an_response_desc = $_POST['x_response_reason_text'];
		$an_txn_id        = $_POST['x_trans_id'];
		$an_amount        = $_POST['x_amount'];
		
		if ( $an_response_code != 1 )
		{
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate AUTHORIZE.NET but failed: {$an_response_desc}");
		}
		
		list( $member_id, $new_sub_id) = explode( 'x', $_POST['x_cust_id'] );
		
		$member_id  = intval($member_id);
		$new_sub_id = intval($new_sub_id);
		
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
		
		if ( $an_response_code )
		{
			//--------------------------------------
			// Check for txn_id - if already used, this
			// is a dupe from a repeated form submit
			//--------------------------------------
			
			$DB->query("SELECT subtrans_id FROM ibf_subscription_trans WHERE subtrans_trxid='".addslashes($an_txn_id)."'");
			
			if ( $trans = $DB->fetch_row() )
			{
				// Is this a reversal?
				
				$this->class->_do_log("{$this->class->method_name}: Duplicate transaction ID for AUTHORIZE.NET - failing and exiting transaction");
				
			}
		
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
				$this->class->_do_log("{$this->class->method_name}: Tried to return validate AUTHORIZE.NET but failed: No start sub package found");
			}
			
			//-----------------------
			// Start subscription
			//-----------------------
		
			$update['subtrans_state']      = 'paid';
			$update['subtrans_paid']       = $new_sub['sub_cost'];
			$update['subtrans_trxid']      = $an_trx_id;
			$update['subtrans_sub_id']     = $new_sub['sub_id'];
			$update['subtrans_cumulative'] = $new_sub['sub_cost'];
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
			
			$update['subtrans_method']     = 'authorizenet';
			
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
			
			$this->class->_do_log("{$this->class->method_name}: AUTHORIZE.NET Subscription started (non recurring). Set trans_id {$cur_details['subtrans_id']} to {$update['subtrans_state']}, paid {$update['subtrans_paid']}");
		
			
		}
		else
		{
			//-----------------------
			// NOT VERIFIED
			//-----------------------
			
			$this->class->_do_log("{$this->class->method_name}: NOT VERIFIED, refused from AUTHORIZE.NET");
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
	
	function hmac ($key, $data)
	{
	   // RFC 2104 HMAC implementation for php.
	   // Creates an md5 HMAC.
	   // Eliminates the need to install mhash to compute a HMAC
	   // Hacked by Lance Rushing
	
	   $b = 64; // byte length for md5
	   if (strlen($key) > $b) {
		   $key = pack("H*",md5($key));
	   }
	   $key  = str_pad($key, $b, chr(0x00));
	   $ipad = str_pad('', $b, chr(0x36));
	   $opad = str_pad('', $b, chr(0x5c));
	   $k_ipad = $key ^ $ipad ;
	   $k_opad = $key ^ $opad;
	
	   return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
	}
	
	function calculatefp ($loginid, $txnkey, $amount, $sequence, $tstamp, $currency = "")
	{
  		return ($this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency));
	}

	
	
}

 
?>