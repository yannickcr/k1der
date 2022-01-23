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
|   > Payment Gateway API: MANUAL
|   > Module written by Matt Mecham
|   > Date started: 21th August 2003
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
	// HANDLE CUSTOM STUFF
	//---------------------------------------
	
	function run_custom()
	{
		global $ibforums, $std, $DB, $print;
		
		switch( $ibforums->input['mode'] )
		{
			case 'ticket':
				$this->do_ticket();
				break;
		}
	}
	
	
	//---------------------------------------
	// Pop up meh ticket
	//---------------------------------------
	
	function do_ticket()
	{
		global $ibforums, $std, $DB, $print;
		
		$sub_id  = intval( $ibforums->input['sid'] );
		$tick_id = intval( $ibforums->input['tickid'] );
		$upgrade = intval( $ibforums->input['upgrade'] );
		
		//---------------------------------------
		// Check for pending subscription
		//---------------------------------------
		
		$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_member_id={$ibforums->member['id']} AND subtrans_state='pending' AND subtrans_end_date > ".time() );
		
		if ( $trx = $DB->fetch_row() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'sub_already' ) );
		}
		
		
		if ( $sub_id < 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
		}
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id=$sub_id");
		
		if ( ! $sub = $DB->fetch_row() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
		}
		
		//-------------------------
		// start array
		//-------------------------
		
		$use_meh = array(
							'subtrans_sub_id'     => $sub_id,
							'subtrans_member_id'  => $ibforums->member['id'],
							'subtrans_old_group'  => $ibforums->member['mgroup'],
							'subtrans_paid'       => $sub['sub_cost'],
							'subtrans_cumulative' => $sub['sub_cost'],
							'subtrans_method'	  => 'manual',
							'subtrans_start_date' => time(),
							'subtrans_end_date'   => time() + ( $sub['sub_length'] * $this->class->day_to_seconds[ $sub['sub_unit'] ] ),
							'subtrans_state'      => 'pending'
					    );
					    
		if ( $sub['sub_unit'] == 'x' )
		{
			$use_meh['subtrans_end_date'] = 9999999999;
		}
		
		if ( $upgrade == 1 )
		{
			//-------------------------
			// Check out me bad self
			//-------------------------
			
			if ( $tick_id < 1 )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
			}
			
			$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_id=$tick_id");
			
			if ( ! $trans = $DB->fetch_row() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_fail', 'EXTRA' => 'no_curid' ) );
			}
			
			$use_meh['subtrans_paid'] = $sub['sub_cost'] - $trans['subtrans_paid'];
			
			unset($use_meh['sub_cumulative']);
			unset($use_meh['sub_start_date']);
			unset($use_meh['sub_end_date']);
			
			$dbs = $DB->compile_db_update_string($use_meh);
			
			$DB->query("UPDATE ibf_subscription_trans SET $dbs, subtrans_cumulative=subtrans_cumulative+{$sub['sub_cost']} WHERE subtrans_id=$tick_id");
			
			$sub_title_extra = '('. $ibforums->lang['gw_upgrade'] .')';
			
		}
		else
		{
			//-------------------------
			// Chow-mow!
			//-------------------------
			
			$dbs = $DB->compile_db_insert_string($use_meh);
			
			$DB->query("INSERT INTO ibf_subscription_trans ({$dbs['FIELD_NAMES']}) VALUES({$dbs['FIELD_VALUES']})");
			
			$tick_id = $DB->get_insert_id();
			
		}
		
		$cost = sprintf( "%.2f", $use_meh['subtrans_paid'] * $this->class->cho_currency['subcurrency_exchange'] ) . ' '.$this->class->cho_currency['subcurrency_code'];
		
		$html = $this->class->html->show_ticket($sub, $tick_id, $cost, $sub_title_extra);
		
		$print->pop_up_window("TICKET", $html );
		
	}
	
	//---------------------------------------
	// Do wop diddy
	//---------------------------------------
	
	function show_normal_payment_screen($sub_upgrade, $pay_method, $extra)
	{
		global $ibforums, $std, $DB;
		
		
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
		
		$cost = sprintf( "%.2f", $sub_upgrade['sub_cost'] * $this->class->cho_currency['subcurrency_exchange'] );
		
		$ibforums->lang['sc_normal_string'] = sprintf( $ibforums->lang['sc_normal_string'],
													   $sub_upgrade['sub_title'],
													   $cost . ' ' . $this->class->cho_currency['subcurrency_code']
													 );
		
		
		$ibforums->lang['post_manual_more'] = sprintf( $ibforums->lang['post_manual_more'],
													   $pay_method['submethod_custom_1'],
													   $pay_method['submethod_custom_2'],
													   $pay_method['submethod_custom_3'],
													   $pay_method['submethod_custom_4'],
													   $pay_method['submethod_custom_5'] );
		
		return $this->class->html->do_manual_normal_screen(
															$sub_upgrade['sub_id'], 
															$ibforums->lang['sc_normal_string'],
															$this->class->cho_currency['subcurrency_code']
														  );
		
		
	}
	
	
	
	//---------------------------------------
	// Show payment upgrade screen
	//---------------------------------------
	
	function show_upgrade_payment_screen($sub_current, $sub_upgrade, $pay_method, $extra)
	{
		global $ibforums, $std, $DB;
		
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
		
		$balance = $sub_upgrade['sub_cost'] - $cur_trans['subtrans_paid'];
		
		$end_date = ( $sub_upgrade['sub_unit'] == 'x' or $sub_current['sub_unit'] == 'x' )
					  ? $ibforums->lang['no_expire']
					  : $std->get_date( $cur_trans['subtrans_end_date'], 'JOINED' );
					  
		$ibforums->lang['sc_upgrade_string'] = sprintf( $ibforums->lang['sc_upgrade_string'],
														$sub_current['sub_title'],
														$sub_upgrade['sub_title'],
														$end_date,
														'&#036;'.$balance
													  );
													  
		$ibforums->lang['post_manual_more'] = sprintf( $ibforums->lang['post_manual_more'],
													   $pay_method['submethod_custom_1'],
													   $pay_method['submethod_custom_2'],
													   $pay_method['submethod_custom_3'],
													   $pay_method['submethod_custom_4'],
													   $pay_method['submethod_custom_5'] );
		
		
		
		return $this->class->html->do_manual_upgrade_screen(
															 $sub_upgrade['sub_id'], 
															 $cur_trans['subtrans_id'],
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
						  'subextra_custom_1' => array( 'used' => 0, 'formname' => '' ),
						  'subextra_custom_2' => array( 'used' => 0, 'formname' => '' ),
						  'subextra_custom_3' => array( 'used' => 0, 'formname' => '' ),
						  'subextra_custom_4' => array( 'used' => 0, 'formname' => '' ),
						  'subextra_custom_5' => array( 'used' => 0, 'formname' => '' ),
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
						  'submethod_custom_1' => array( 'used' => 1, 'varname' => 'payname' , 'formname' => 'Name to address payment to' ),
						  'submethod_custom_2' => array( 'used' => 1, 'varname' => 'address1', 'formname' => 'Payment Address (1)' ),
						  'submethod_custom_3' => array( 'used' => 1, 'varname' => 'address2', 'formname' => 'Payment Address (2)' ),
						  'submethod_custom_4' => array( 'used' => 1, 'varname' => 'address3', 'formname' => 'Payment Address (3)' ),
						  'submethod_custom_5' => array( 'used' => 1, 'varname' => 'address4', 'formname' => 'Payment Address (4)' ),
					   );
					   
		return $return;
	
	}
	
	
}

 
?>