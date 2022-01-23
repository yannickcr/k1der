<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Payment Gateway API: PROTX
|   > Module written by Matt Mecham
|   > Date started: 18th May 2004
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
		$this->class->return_not_die = 1;
	}
	
	
	//---------------------------------------
	// Show NORMAL payment screen
	//---------------------------------------
	
	function show_normal_payment_screen($sub_upgrade, $pay_method, $extra)
	{
		global $ibforums, $std, $DB;
		
		$this->class->method = $pay_method;
		
		//---------------------------------------
		// Set up some basics
		//---------------------------------------
		
		$this->class->method['protx_user'] = $this->class->method['submethod_custom_1'];
		$this->class->method['protx_pass'] = $this->class->method['submethod_custom_2'];
		
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
		
		//---------------------------------------
		// Generate crypt string
		//---------------------------------------
		
		$plain  = "VendorTxCode=" . rand(0,1000)."x{$ibforums->member['id']}x{$sub_upgrade['sub_id']}x0" . "&";
		$plain .= "Amount=" . sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->all_currency[ $this->class->method['submethod_use_currency'] ]['subcurrency_exchange'] ) . "&";
		$plain .= "Currency=" . $this->class->method['submethod_use_currency'] . "&";
		$plain .= "Description={$sub_upgrade['sub_title']}&";
		$plain .= "SuccessURL=" . "{$ibforums->base_url}act=module&module=subscription&CODE=incoming&type=protx" . "&";
		$plain .= "FailureURL=" . "{$ibforums->base_url}act=module&module=subscription&CODE=incoming&type=protx" . "&";
		$plain .= "CustomerName=&";
		$plain .= "CustomerEmail=" . $ibforums->member['email'] . "&";
		$plain .= "VendorEMail=" . $pay_method['submethod_email'] . "&";
		$plain .= "DeliveryAddress=&";
      	$plain .= "DeliveryPostCode=&";
      	$plain .= "BillingAddress=&";
      	$plain .= "BillingPostCode=";
      
		$crypt = base64_encode( $this->_simple_xor( $plain, $this->class->method['protx_pass'] ) );
                            
		return $this->class->html->do_protx_normal_screen(
															$crypt,
															$this->class->method['protx_user'],
															$sub_upgrade['sub_title'],
															$ibforums->lang['sc_normal_string']
														  );
		
		
		
		
	}
	
	
	
	//---------------------------------------
	// Show payment upgrade screen
	//---------------------------------------
	
	function show_upgrade_payment_screen($sub_current, $sub_upgrade, $pay_method, $extra)
	{
		global $ibforums, $std, $DB;
		
		$this->class->method = $pay_method;
		
		//---------------------------------------
		// Set up some basics
		//---------------------------------------
		
		$this->class->method['protx_user'] = $this->class->method['submethod_custom_1'];
		$this->class->method['protx_pass'] = $this->class->method['submethod_custom_2'];
		
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
														sprintf( "%.2f", $balance * $this->class->cho_currency['subcurrency_exchange'] ).' '.$this->class->all_currency[ $pay_method['submethod_use_currency'] ]['subcurrency_code']
													  );
		
		//---------------------------------------
		// Generate crypt string
		//---------------------------------------
		
		$plain  = "VendorTxCode=" . rand(0,1000)."x{$ibforums->member['id']}x{$sub_upgrade['sub_id']}x{$cur_trans['subtrans_id']}" . "&";
		$plain .= "Amount=" . $balance . "&";
		$plain .= "Currency=" . $this->class->method['submethod_use_currency'] . "&";
		$plain .= "Description={$sub_upgrade['sub_title']}&";
		$plain .= "SuccessURL=" . "{$ibforums->base_url}act=module&module=subscription&CODE=incoming&type=protx" . "&";
		$plain .= "FailureURL=" . "{$ibforums->base_url}act=module&module=subscription&CODE=incoming&type=protx" . "&";
		$plain .= "CustomerName=&";
		$plain .= "CustomerEmail=" . $ibforums->member['email'] . "&";
		$plain .= "VendorEMail=" . $pay_method['submethod_email'] . "&";
		$plain .= "DeliveryAddress=&";
      	$plain .= "DeliveryPostCode=&";
      	$plain .= "BillingAddress=&";
      	$plain .= "BillingPostCode=";
      
		$crypt = base64_encode( $this->_simple_xor( $plain, $this->class->method['protx_pass'] ) );
                              
		return $this->class->html->do_protx_upgrade_screen(
															$crypt,
															$this->class->method['protx_user'],
															$sub_upgrade['sub_title'],
															$ibforums->lang['sc_upgrade_string']
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
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: IPN set up to return, but ACP settings have auto validate switched off");
		}
		
		//--------------------------------------
		// Test GET data
		//--------------------------------------
		
		if ( empty( $_GET ) )
		{
			$this->class->_do_log("{$this->class->method_name}: Tried to return validate but failed: GET DATA EMPTY");
		}
		
		//---------------------------------------
		// Set up some basics
		//---------------------------------------
		
		$this->class->method['protx_user'] = $this->class->method['submethod_custom_1'];
		$this->class->method['protx_pass'] = $this->class->method['submethod_custom_2'];
		
		//--------------------------------------
		// Process GET data
		//--------------------------------------
		
      	$values = $this->_get_token( $this->_simple_xor( base64_decode($_GET['crypt']), $this->class->method['protx_pass'] ) );
		
		if ( $values['Status'] != 'OK' )
		{
			$this->class->_do_log("{$this->class->method_name}: Failed: Status: {$values['Status']} - {$values['StatusDetail']}");
		}
		
		//array("Status","StatusDetail","VendorTxCode","VPSTxID","TxAuthNo","Amount","AVSCV2")
		
		list( $tix, $mid, $sid, $cur_sub_id  ) = explode( "x", $values['VendorTxCode'] );
		
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
		
		$new_sub_id       = intval($sid);
		$member_id        = intval($mid);
		$payment_amount   = $values['Amount'];
		$txn_id           = $values['VPSTxID'];
		
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
		// Check for txn_id - if already used, this
		// is a dupe from a repeated form submit
		//--------------------------------------
		
		$DB->query("SELECT subtrans_id FROM ibf_subscription_trans WHERE subtrans_trxid='".addslashes($txn_id)."'");
		
		if ( $trans = $DB->fetch_row() )
		{
			$this->class->_do_log("{$this->class->method_name}: Duplicate transaction ID - failing and exiting transaction");
			exit();
		}
	
		if ( $cur_sub_id > 0 )
		{
			//--------------------------------------
			// UPGRADE
			//--------------------------------------
			
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
				// Update subscription
				//-----------------------
				
				$update['subtrans_paid']       = $payment_amount;
				$update['subtrans_state']      = 'paid';
				$update['subtrans_trxid']      = $txn_id;
				$update['subtrans_sub_id']     = $new_sub['sub_id'];
				$update['subtrans_paid']       = $payment_amount;
				$update['subtrans_state']      = 'paid';
				
				if ( $new_sub['sub_unit'] == 'x' )
				{
					$update['subtrans_end_date'] = 9999999999;
				}
				else
				{
					//$update['subtrans_end_date']   = time() + ( $new_sub['sub_length'] * $day_to_seconds[ $new_sub['sub_unit'] ] );
				}
				
				$update['subtrans_method']     = 'protx';
				
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
			
			//-----------------------
			// ONE OFF PAYMENT
			//-----------------------
			
			$update['subtrans_paid']       = $payment_amount;
			$update['subtrans_state']      = 'paid';
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
				
			$update['subtrans_method']     = 'protx';
			
			//-----------------------
			// Just updating a subsc?
			//-----------------------
			
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
	
	
	
	
	
	
	
	function _simple_xor($instr, $k)
	{
     	$kList = array();
    	$output = "";

    	for($i = 0; $i < strlen($k); $i++)
    	{
     		$kList[$i] = ord(substr($k, $i, 1));
      	}

      	for($i = 0; $i < strlen($instr); $i++)
      	{
        	$output.= chr(ord(substr($instr, $i, 1)) ^ ($kList[$i % strlen($k)]));
      	}

      	return $output;
    }

	function _get_token($thisString)
	{

		$Tokens = array("Status","StatusDetail","VendorTxCode","VPSTxID","TxAuthNo","Amount","AVSCV2");

		$output = array();
		$resultArray = array();

		for ($i = count($Tokens)-1; $i >= 0 ; $i--)
		{
			$start = strpos($thisString, $Tokens[$i]);
			
        	if ($start !== false)
        	{
          		$resultArray[$i]->start = $start;
          		$resultArray[$i]->token = $Tokens[$i];
       		}
      	}

      	sort($resultArray);

      	for ($i = 0; $i<count($resultArray); $i++)
      	{
        	$valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
        	if ($i==(count($resultArray)-1))
        	{
          		$output[$resultArray[$i]->token] = substr($thisString, $valueStart);
        	}
        	else
        	{
          		$valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
          		$output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
        	}
      	}

      	return $output;
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
						  'submethod_custom_1' => array( 'used' => 1, 'formname' => 'Protx User Name', 'formextra' => 'This is the username assigned to your Protx account' ),
						  'submethod_custom_2' => array( 'used' => 1, 'formname' => 'Protx Encryption Password' , 'formextra' => 'This is the password assigned to your Protx account  to encrypt the form data' ),
						  'submethod_custom_3' => array( 'used' => 0, 'varname' => '' ),
						  'submethod_custom_4' => array( 'used' => 0, 'varname' => '' ),
						  'submethod_custom_5' => array( 'used' => 0, 'varname' => '' ),
					   );
					   
		return $return;
	
	}
	
	
}

 
?>