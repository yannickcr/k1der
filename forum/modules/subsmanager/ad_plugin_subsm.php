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
|   > Subscription Manager For IPB (Module Side)
|   > Module written by Matt Mecham
|   > Date started: 19th August 2003
|
|	> Module Version Number: 1.0.0
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

//---------------------------------------
// Carry on!
//---------------------------------------

class ad_plugin_subsm {

	var $base_url;

	function ad_plugin_subsm()
	{
		global $ibforums, $DB, $std;
		
		//---------------------------------------
		// Kill globals - globals bad, Homer good.
		//---------------------------------------
		
		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );
		
		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}
		
		//--------------------------------------------
    	// Get the sync module
		//--------------------------------------------
		
		if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";
			
			$this->modules = new ipb_member_sync();
		}
		
		//--------------------------------------------
		// Load extra db cache file
		//--------------------------------------------
		
		$DB->load_cache_file( ROOT_PATH.'sources/sql/'.SQL_DRIVER.'_subsm_queries.php', 'sql_subsm_queries' );
		
	}
	
	
	//------------------------------------------------
	// Currency Overview - Yeah
	//------------------------------------------------
	
	function currency_index($message="")
	{
		global $ibforums, $DB, $std;
		
		$DB->query("SELECT * FROM ibf_subscription_currency WHERE subcurrency_default=1");
		
		$default = $DB->fetch_row();
		
		//-------------------------------------------
		// Message in a bottle?
		//-------------------------------------------
		
		if ( $message != "" )
		{
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );
		
			$ibforums->html .= $ibforums->adskin->start_table( "Message" );
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( $message )  );
			
			$ibforums->html .= $ibforums->adskin->end_table();
		}
		
		$ibforums->admin->page_detail .= "<br /><br /><strong>Currency Information</strong><br />The currency you set as default becomes the currency the subscriptions are based in. For example, if you chose USD as default, entering a value of 1.00 for a subscription package means that the subscription
							    package costs 1.00 USD. If you choose another default you may want to edit all the subscription values as they will become incorrect. If you choose another default, you will also want to edit the exchange values.<br /><br />For up-to-date currency conversion, visit <a href='http://www.xe.com' target='_blank'>XE.com</a>.";
		
		//-------------------------------------------
		// Quick Jump Table
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'editcurrency'),
												  2 => array( 'act'     , 'msubs'    ),
									     )  );
									     		   
		$ibforums->adskin->td_header[] = array( "Code"         , "10%" );
		$ibforums->adskin->td_header[] = array( "Description"  , "40%" );
		$ibforums->adskin->td_header[] = array( "Conv. Rate"   , "30%" );
		$ibforums->adskin->td_header[] = array( "Default?"     , "10%" );
		$ibforums->adskin->td_header[] = array( "Delete"       , "10%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Available Currencies");
		
		$not_in = ' USD GBP EUR CAD ';
		
		$DB->query("SELECT * FROM ibf_subscription_currency");
		
		while( $c = $DB->fetch_row() )
		{
			
			$checked = $c['subcurrency_default'] == 1 ? " checked='checked'" : "";
			
			$delete_link = "<i>Can't Delete</i>";
			
			if ( ! strstr( $not_in, $c['subcurrency_code'] ) )
			{
				if ( $default['subcurrency_code'] != $c['subcurrency_code'] )
				{
					$delete_link = "<a href='{$ibforums->base_url}&act=msubs&code=deletecurrency&currency=".$c['subcurrency_code']."'>Delete</a>";
				}
			}
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$c['subcurrency_code']}</b>" ,
													  $ibforums->adskin->form_input( 'desc_'.$c['subcurrency_code'], $c['subcurrency_desc'] ),
													  "1 X ".$default['subcurrency_code']." = ".$ibforums->adskin->form_simple_input( 'exchange_'.$c['subcurrency_code'], $c['subcurrency_exchange'], 12 )." ".$c['subcurrency_code'],
													  "<center><input type='radio' name='default' value='{$c['subcurrency_code']}' $checked /></center>",
													  "<center>{$delete_link}</center>"
											 )      );
										 
		}
		
		$ibforums->html .= $ibforums->adskin->add_td_basic( 'Add a new currency', 'left', 'catrow2' );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( $ibforums->adskin->form_simple_input( 'add_code', "", 3 ) ,
												  $ibforums->adskin->form_input( 'add_desc' ),
												  "1 X ".$default['subcurrency_code']." = ".$ibforums->adskin->form_simple_input( 'add_exchange', "", 12 )." <i>new currency</i>",
												  "&nbsp;",
												  "&nbsp;"
										 )      );
										 
		$ibforums->html .= $ibforums->adskin->end_form( "Save Settings" );
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	}
	
	//------------------------------------------------
	// Currency Overview - EDIT
	//------------------------------------------------
	
	function currency_edit()
	{
		global $ibforums, $DB, $std;
		
		$currency = array();
		
		$DB->query("SELECT * FROM ibf_subscription_currency");
		
		while ( $c = $DB->fetch_row() )
		{
			$currency[ $c['subcurrency_code'] ] = $c;
		}
		
		foreach ( $currency as $code => $data )
		{
			if ( $ibforums->input[ 'desc_'.$code ] AND $ibforums->input[ 'exchange_'.$code ] )
			{
				$DB->query("UPDATE ibf_subscription_currency SET subcurrency_desc='{$ibforums->input[ 'desc_'.$code ]}', subcurrency_exchange='{$ibforums->input[ 'exchange_'.$code ]}'
						    WHERE subcurrency_code='$code'");
			}
		}
		
		// Sort out default...
		
		$DB->query("UPDATE ibf_subscription_currency SET subcurrency_default=0");
		$DB->query("UPDATE ibf_subscription_currency SET subcurrency_default=1 WHERE subcurrency_code='{$ibforums->input['default']}'");
				
		// Addition?
		
		if ( $ibforums->input['add_code'] AND $ibforums->input['add_desc'] AND $ibforums->input['add_exchange'] )
		{
			$DB->query("SELECT * FROM ibf_subscription_currency WHERE subcurrency_code='{$ibforums->input['add_code']}'");
			
			if ( $t = $DB->fetch_row() )
			{
				$this->currency_index("You cannot use currency code '{$ibforums->input['add_code']}' as it already exists.");
			}
			
			$DB->do_insert( 'subscription_currency', array( 'subcurrency_code'     => $ibforums->input['add_code'],
														    'subcurrency_desc'     => $ibforums->input['add_desc'],
														    'subcurrency_exchange' => $ibforums->input['add_exchange'] ) );
		}
		
		$ibforums->admin->save_log("Currency: Edited");
		
		$this->currency_index("Currency settings updated");
	}
	
	//------------------------------------------------
	// Currency Overview - DELETE
	//------------------------------------------------
	
	function currency_delete()
	{
		global $ibforums, $DB, $std;
		
		if ( $ibforums->input['currency'] == "" )
		{
			$ibforums->admin->error("Couldn't find a currency to delete.");
		}
		
		$DB->query("DELETE FROM ibf_subscription_currency WHERE subcurrency_code='{$ibforums->input['currency']}'");
		
		$ibforums->admin->save_log("Currency '{$ibforums->input['currency']}' Deleted");
		
		$this->currency_index("Currency '{$ibforums->input['currency']}' Deleted");
	}
	
	
	
	
	//------------------------------------------------
	// Do Overview
	//------------------------------------------------
	
	function do_overview()
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Get packages and get methods
		//-------------------------------------------
		
		$packages = array();
		$methods  = array();
		$subs     = array();
		
		$DB->query("SELECT sub_id, sub_title, sub_cost FROM ibf_subscriptions ORDER BY sub_cost");
		
		while ( $p = $DB->fetch_row() )
		{
			$packages[ $p['sub_id'] ] = $p;
		}
		
		$methods = array();
		
		$DB->query("SELECT * FROM ibf_subscription_methods ORDER BY submethod_title");
		
		while ( $m = $DB->fetch_row() )
		{
			$methods[ $m['submethod_id'] ] = $m;
		}
		
		$DB->query("SELECT * FROM ibf_subscription_extra");
		
		while ( $s = $DB->fetch_row() )
		{
			$subs[ $s['subextra_sub_id'] ][ $s['subextra_method_id'] ] = $s;
		}
		
		$ibforums->html .= "<script type='text/javascript'>
						 function goEditPkg( subID, methodID )
						 {
						 	opener.location = \"{$ibforums->base_url}&act=msubs&code=editpkginfo&method=\" + methodID + \"&sub=\" + subID;
						 }
						 </script>";
		
		//-------------------------------------------
		// Set up the table header
		//-------------------------------------------
		
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "20%" );
		
		$percent = 80 / count($methods);
		
		foreach( $methods as $id => $data )
		{
			$name = $data['submethod_title'];
			
			if ( strlen($name) > 10 )
			{
				$name = substr( $name, 0, 8 ) . '..';
			}
			
			$ibforums->adskin->td_header[] = array( $name  , $percent.'%' );
		}
		
		//-------------------------------------------
		// Start the table
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_table( "Packages / Gateway Overview" );
		
		foreach( $packages as $id => $pack )
		{
			$row = array( 0 => "<b>".$pack['sub_title']."</b>" );
			
			foreach( $methods as $mid => $data )
			{
				$link_colour = $data['submethod_active'] == 1 ? "black" : "gray";
			
				if ( is_array( $subs[ $id ][ $mid ] ) )
				{
					$prod_id = $subs[ $id ][ $mid ]['subextra_product_id'] == "" ? "<i>None</i>" : $subs[ $id ][ $mid ]['subextra_product_id'];
					$recur   = $subs[ $id ][ $mid ]['subextra_recurring'] ? "<span style='color:green'>Recurring: Yes</span>" : "<span style='color:red'>Recurring: No</span>";
					
					$add = "<center>";
					$add .= "Product ID: " . $prod_id;
					$add .= "<br />".$recur;
					$add .= "<br /><a href='javascript:goEditPkg($id, $mid)' style='color:$link_colour'>Edit...</a>";
					$add .= "</center>";
					
					$row[] = $add;
				}
				else
				{	
					$row[] = "<center><a href='javascript:goEditPkg($id, $mid)' style='color:$link_colour'>Set Up?</a></center>";
				}
			}
			
			$ibforums->html .=  $ibforums->adskin->add_td_row( $row );
		}
							     
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->print_popup();
	
	}
	
	//------------------------------------------------
	// Complete pkg / gateway edit
	//------------------------------------------------
	
	function doedit_pkg_gateway_info()
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Incoming!
		//-------------------------------------------
		
		$method_id = intval($ibforums->input['method']);
		$subpkg_id = intval($ibforums->input['sub']);
		
		$this_pkg  = array();
		$this_mtd  = array();
		
		if ( $method_id < 1 )
		{
			$ibforums->admin->error("No method_id passed");
		}
		
		if ( $subpkg_id < 1 )
		{
			$ibforums->admin->error("No subpkg_id passed");
		}
		
		//-------------------------------------------
		// Check
		//-------------------------------------------
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id={$subpkg_id}");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could locate a sub pkg with the id of {$ibforums->input['id']}");
		}
		
		$newbie =  array (
						   'subextra_sub_id'      => $subpkg_id,
						   'subextra_method_id'   => $method_id,
						   'subextra_product_id'  => $ibforums->input['subextra_product_id'],
						   'subextra_can_upgrade' => intval($ibforums->input['subextra_can_upgrade']),
						   'subextra_recurring'   => intval($ibforums->input['subextra_recurring']),
				         );
											  
		foreach( array( 1,2,3,4,5 ) as $id )
		{
			if ( isset($_POST['subextra_custom_'.$id]) )
			{
				$newbie[ 'subextra_custom_'.$id ] = $std->txt_safeslashes( $_POST['subextra_custom_'.$id] );
			}
		}
		
		//-------------------------------------------
		// Do we 'ave a row already my old bean?
		//-------------------------------------------
		
		$DB->query("SELECT subextra_id FROM ibf_subscription_extra WHERE subextra_sub_id={$subpkg_id} AND subextra_method_id={$method_id}");
		
		if ( $DB->get_num_rows() )
		{
			// Already exists, update!
			
			$dbstring = $DB->compile_db_update_string($newbie);
											  
			$DB->query("UPDATE ibf_subscription_extra SET $dbstring WHERE subextra_sub_id={$subpkg_id} AND subextra_method_id={$method_id}");
		}
		else
		{
			// Doesn't exist, go add!
			
			$dbstring = $DB->compile_db_insert_string($newbie);
			
			$DB->query("INSERT INTO ibf_subscription_extra ({$dbstring['FIELD_NAMES']}) VALUES({$dbstring['FIELD_VALUES']})");
			
		}
		
		$ibforums->admin->save_log("Payment specific information for gateway edited");
		
		$this->edit_pkg_gateway_info("Settings saved");
	
	}
	
	//------------------------------------------------
	// Edit/Add a package gateway specific ting man
	//------------------------------------------------
	
	function edit_pkg_gateway_info($message="")
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Incoming!
		//-------------------------------------------
		
		$method_id = intval($ibforums->input['method']);
		$subpkg_id = intval($ibforums->input['sub']);
		
		$this_pkg  = array();
		$this_mtd  = array();
		
		if ( $method_id < 1 )
		{
			$ibforums->admin->error("No method_id passed");
		}
		
		if ( $subpkg_id < 1 )
		{
			$ibforums->admin->error("No subpkg_id passed");
		}
		
		//-------------------------------------------
		// Get packages and get methods
		//-------------------------------------------
		
		$DB->query("SELECT sub_id, sub_title, sub_cost FROM ibf_subscriptions ORDER BY sub_cost");
		
		$packages = array();
		
		while ( $p = $DB->fetch_row() )
		{
			$packages[] = array( $p['sub_id'], $p['sub_title'] );
			
			if ( $p['sub_id'] == $subpkg_id )
			{
				$this_pkg = $p;
			}
		}
		
		$methods = array();
		
		$DB->query("SELECT * FROM ibf_subscription_methods ORDER BY submethod_title");
		
		while ( $m = $DB->fetch_row() )
		{
			$methods[] = array( $m['submethod_id'], $m['submethod_title'] );
			
			if ( $m['submethod_id'] == $method_id )
			{
				$this_mtd = $m;
			}
		}
		
		$DB->query("SELECT * FROM ibf_subscription_extra WHERE subextra_sub_id={$subpkg_id} AND subextra_method_id={$method_id}");
		
		$row = $DB->fetch_row();
		
		//-------------------------------------------
		// Message in a bottle?
		//-------------------------------------------
		
		if ( $message != "" )
		{
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );
		
			$ibforums->html .= $ibforums->adskin->start_table( "Message" );
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( $message )  );
			
			$ibforums->html .= $ibforums->adskin->end_table();
		}
		
		$ibforums->admin->page_detail .= "<br /><br /><strong>Editing Gateway '{$this_mtd['submethod_title']}' specific information for package '{$this_pkg['sub_title']}'.</strong>";
		
		//-------------------------------------------
		// Quick Jump Table
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'editpkginfo'),
														         2 => array( 'act'     , 'msubs'    ),
									    			    )  );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Quick Jump");
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Payment Gateway</b>" ,
												 				 $ibforums->adskin->form_dropdown( 'method', $methods, $this_mtd['submethod_id'] )
										 				)      );
										 
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Subscription Package</b>" ,
												 				 $ibforums->adskin->form_dropdown( 'sub', $packages, $this_pkg['sub_id'] )
										 				)      );
												 
		$ibforums->html .= $ibforums->adskin->end_form( "Go!" );
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		//-------------------------------------------
		// Carry on!
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'doeditpkg'),
																 2 => array( 'act'     , 'msubs'    ),
																 3 => array( 'method'  , $method_id ),
																 4 => array( 'sub'     , $subpkg_id ),
														)  );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "{$this_mtd['submethod_title']} -&gt; {$this_pkg['sub_title']}");
		
		//---------------------------------
		// Load the API...
		//---------------------------------
		
		$custom = array();
		
		if ( @file_exists( ROOT_PATH . 'modules/subsmanager/api_'.$this_mtd['submethod_name'].'.php' ) )
		{
			require_once( ROOT_PATH . 'modules/subsmanager/api_'.$this_mtd['submethod_name'].'.php' );
			
			$this->gateway = new gateway();
			
			//----------------------------------
			// Sort out the custom method fields
			//----------------------------------
			
			$form = $this->gateway->acp_return_package_variables();
			
			foreach( $form as $name => $data )
			{
				if ( $data['used'] != 0 )
				{
					$custom[] = $ibforums->adskin->add_td_row( array( "<b>{$data['formname']}</b><br />{$data['formextra']}</b>" ,
														  $ibforums->adskin->form_input( $name, $row[ $name ] )
												 )      );
				}
			}
			
		}
		else
		{
			$ibforums->admin->error("Could not locate the API in: ".ROOT_PATH . 'modules/subsmanager/api_'.$row['submethod_name'].'.php');
		}
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Subscription / Gateway Combo: Product ID</b><br />This is not required by all combinations" ,
												  $ibforums->adskin->form_input("subextra_product_id", $row['subextra_product_id'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can be recurringly billed for this package using this gateway?</b><br />This is not required by all combinations and will not count if the package never expires" ,
												  $this->gateway->can_do_recurring_billing == 0 ? "This gateway does not support recurring billing" : $ibforums->adskin->form_yes_no("subextra_recurring", $row['subextra_recurring'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can upgrade from this package using this gateway?</b><br />This is not required by all combinations" ,
												  $this->gateway->can_do_upgrades == 0 ? "This gateway does not support upgrading packages" : $ibforums->adskin->form_yes_no("subextra_can_upgrade", $row['subextra_can_upgrade'] )
									     )      );
		
		if ( count( $custom ) > 0 )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( 'Gateway Specific Settings', 'left', 'catrow2' );
			
			$ibforums->html .= implode( "\n", $custom );
		}
									     
		$ibforums->html .= $ibforums->adskin->end_form( "Save Settings" );
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	}
	
	//------------------------------------------------
	// Remove Package: You can do iiiiit! I know.
	//------------------------------------------------
	
	function do_remove_package()
	{
		global $ibforums, $DB, $std;
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("Could locate a subscription package with the id of {$ibforums->input['id']}");
		}
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id={$ibforums->input['id']}");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could locate a subscription package with the id of {$ibforums->input['id']}");
		}
		
		$this->_unsub_members($ibforums->input['id'], 'all', 'dead');
		
		$DB->query("DELETE FROM ibf_subscriptions WHERE sub_id={$ibforums->input['id']}");
		
		$ibforums->admin->save_log("Subscription Package {$row['sub_title']} removed");
		
		$std->boink_it( $ibforums->base_url."&act=msubs" );
	}
	
	//------------------------------------------------
	// Remove Package: Step One
	//------------------------------------------------
	
	function remove_package()
	{
		global $ibforums, $DB, $std;
		
		$time = time();
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("Could locate a subscription package with the id of {$ibforums->input['id']}");
		}
		
		$query = "SELECT COUNT(*) as total FROM ibf_subscription_trans WHERE subtrans_sub_id={$ibforums->input['id']}";
		
		$DB->query($query);
		
		$row = $DB->fetch_row();
		
		$total = intval( $row['total'] );
		
		$DB->query("SELECT sub_title FROM ibf_subscriptions WHERE sub_id={$ibforums->input['id']}");
		
		$sub = $DB->fetch_row();
		
	
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doremovepackage'  ),
												  2 => array( 'act'   , 'msubs'            ),
												  4 => array( 'id'    , $ibforums->input['id']          ),
									     )  );
									     
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Remove Package '{$sub['sub_title']}' Confirmation" );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Members subscribed to {$sub['sub_title']}: $total</b><br /><br />Deleting this package will remove all subscribed members and return them back to their previous group. It will also mark all transactions currently subscribed to this package as 'dead'
												   Please note that if the group that they were in no longer exists, they will be moved into the default member group."
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->end_form("Remove Package");
										 
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	
	}
	
	
	
	//------------------------------------------------
	// Remove Members: You can do iiiiit!
	//------------------------------------------------
	
	function do_remove_members()
	{
		global $ibforums, $DB, $std;
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("Could locate a subscription package with the id of {$ibforums->input['id']}");
		}
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id={$ibforums->input['id']}");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could locate a subscription package with the id of {$ibforums->input['id']}");
		}
		
		$this->_unsub_members($ibforums->input['id'], $ibforums->input['type']);
		
		$ibforums->admin->save_log("Members unsubscribed from package {$row['sub_title']} using type {$ibforums->input['type']}");
		
		$std->boink_it( $ibforums->base_url."&act=msubs" );
	}
	
	
	//------------------------------------------------
	// Remove Members: Step One
	//------------------------------------------------
	
	function remove_members()
	{
		global $ibforums, $DB, $std;
		
		$time = time();
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("Could locate a subscription package with the id of {$ibforums->input['id']}");
		}
		
		if ( $ibforums->input['type'] != 'all' )
		{
			$query = "SELECT COUNT(*) as total FROM ibf_subscription_trans WHERE subtrans_end_date < $time AND subtrans_sub_id={$ibforums->input['id']}";
		}
		else
		{
			$query = "SELECT COUNT(*) as total FROM ibf_subscription_trans WHERE subtrans_sub_id={$ibforums->input['id']}";
		}
		
		$DB->query($query);
		
		$row = $DB->fetch_row();
		
		$total = intval( $row['total'] );
		
		if ( $total < 1 )
		{
			$ibforums->admin->error("There are no members to remove.");
		}
	
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doremovemembers'  ),
												  2 => array( 'act'   , 'msubs'            ),
												  3 => array( 'type'  , $ibforums->input['type']        ),
												  4 => array( 'id'    , $ibforums->input['id']          ),
									     )  );
									     
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Unsubscription Confirmation" );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Members to unsubscribe: $total</b><br /><br />Unsubscribing members will mark their transaction as 'expired' and return them to the group they were in before they subscribed.
												   Please note that if the group that they were in no longer exists, they will be moved into the default member group."
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->end_form("Unsubscribe");
										 
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	
	}
	
	
	//------------------------------------------------
	// Subscription Package: Add Package
	//------------------------------------------------
	
	function do_add_package()
	{
		global $ibforums, $DB, $std;
		
		if ( $ibforums->input['sub_title'] == "" )
		{
			$ibforums->admin->error("You must enter a valid title for this subscription package");
		}
		
		if ( $ibforums->input['sub_cost'] == "" )
		{
			$ibforums->admin->error("You must enter a valid cost for this subscription package");
		}
		
		if ( $ibforums->input['sub_noexpire'] )
		{
			$ibforums->input['sub_unit']   = 'x';
			$ibforums->input['sub_length'] = 0;
		}
		
		$newbie = $DB->compile_db_insert_string( array (
														 'sub_title'          => $ibforums->input['sub_title'],
														 'sub_desc'           => $std->txt_safeslashes(trim($_POST['sub_desc'])),
														 'sub_new_group'      => $ibforums->input['sub_new_group'],
														 'sub_length'         => $ibforums->input['sub_length'],
														 'sub_unit'           => $ibforums->input['sub_unit'],
														 'sub_cost'			  => $ibforums->input['sub_cost'],
														 'sub_run_module'	  => $ibforums->input['sub_run_module'],
											  )        );
											  
		$DB->query("INSERT INTO ibf_subscriptions ({$newbie['FIELD_NAMES']}) VALUES({$newbie['FIELD_VALUES']})");
		
		$ibforums->admin->save_log("Subscription Package '{$ibforums->input['sub_title']}' created");
		
		$ibforums->admin->done_screen("Subscription Package Created", "IPB Subscriptions Manager", "act=msubs", 'redirect' );
	
	}
	
	//------------------------------------------------
	// Subscription Package: Complete Edit
	//------------------------------------------------
	
	function do_edit_package()
	{
		global $ibforums, $DB, $std;
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("Could locate a payment gateway with the id of {$ibforums->input['id']}");
		}
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id={$ibforums->input['id']}");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could locate a payment gateway with the id of {$ibforums->input['id']}");
		}
		
		if ( $ibforums->input['sub_title'] == "" )
		{
			$ibforums->admin->error("You must enter a valid title for this subscription package");
		}
		
		if ( $ibforums->input['sub_cost'] == "" )
		{
			$ibforums->admin->error("You must enter a valid cost for this subscription package");
		}
		
		if ( $ibforums->input['sub_noexpire'] )
		{
			$ibforums->input['sub_unit']   = 'x';
			$ibforums->input['sub_length'] = 0;
		}
		
		$newbie = $DB->compile_db_update_string( array (
														 'sub_title'          => $ibforums->input['sub_title'],
														 'sub_desc'           => $std->txt_safeslashes(trim($_POST['sub_desc'])),
														 'sub_new_group'      => $ibforums->input['sub_new_group'],
														 'sub_length'         => $ibforums->input['sub_length'],
														 'sub_unit'           => $ibforums->input['sub_unit'],
														 'sub_cost'			  => $ibforums->input['sub_cost'],
														 'sub_run_module'	  => $ibforums->input['sub_run_module'],
														
											  )        );
											  
		$DB->query("UPDATE ibf_subscriptions SET $newbie WHERE sub_id={$row['sub_id']}");
		
		$ibforums->admin->save_log("Subscription Package '{$row['sub_title']}' edited");
		
		$ibforums->admin->done_screen("Subscription Package edited", "IPB Subscriptions Manager", "act=msubs", 'redirect' );
	
	}
	
	
	//------------------------------------------------
	// Subscription Package: Alter Form (edit/new)
	//------------------------------------------------
	
	function alter_package_form($type='edit')
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Get packages and get methods
		//-------------------------------------------
		
		$DB->query("SELECT sub_id, sub_title, sub_cost FROM ibf_subscriptions ORDER BY sub_cost");
		
		$packages = array();
		
		while ( $p = $DB->fetch_row() )
		{
			$packages[] = array( $p['sub_id'], $p['sub_title'] );
			
			if ( $p['sub_id'] == $subpkg_id )
			{
				$this_pkg = $p;
			}
		}
		
		$methods = array();
		
		$DB->query("SELECT * FROM ibf_subscription_methods ORDER BY submethod_title");
		
		while ( $m = $DB->fetch_row() )
		{
			$methods[] = array( $m['submethod_id'], $m['submethod_title'] );
			
			if ( $m['submethod_id'] == $method_id )
			{
				$this_mtd = $m;
			}
		}
		
		if ( $type == 'edit' )
		{
			if ( ! $ibforums->input['id'] )
			{
				$ibforums->admin->error("Could not locate a payment gateway with the id of {$ibforums->input['id']}");
			}
			
			$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id={$ibforums->input['id']}");
			
			if ( ! $row = $DB->fetch_row() )
			{
				$ibforums->admin->error("Could not locate a payment gateway with the id of {$ibforums->input['id']}");
			}
			
			$submit = 'Edit Package';
			$code   = 'doeditpackage';
			$table  = "Edit Package '{$row['sub_title']}'";
		}
		else
		{
			$row = array();
			$submit = "Add Package";
			$code   = "doaddpackage";
			$table  = "Add new subscription package";
		}
		
		foreach( explode( ",", $row['sub_payment_allow'] ) as $p )
		{
			$allow_payment[$p] = 1;
		}
		
		//-------------------------------------------
		// Grab member groups
		//-------------------------------------------
		
		$groups = array( 0 => array( 0, "--Don't Change Group--" ) );
		
		$DB->query("SELECT g_id, g_title FROM ibf_groups ORDER BY g_title");
		
		while ( $r = $DB->fetch_row() )
		{
			$groups[] = array( $r['g_id'], $r['g_title'] );
		}
		
		//-------------------------------------------
		// Show form
		//-------------------------------------------
		
		$subchecked = $row['sub_unit'] == 'x' ? "checked='checked'" : '';
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $code    ),
												  2 => array( 'act'   , 'msubs'  ),
												  3 => array( 'id'    , $ibforums->input['id']),
									     )  );
									     
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( $table );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Package Name</b>" ,
												  $ibforums->adskin->form_input("sub_title", $row['sub_title'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Package Description</b><br />HTML is allowed" ,
												  $ibforums->adskin->form_textarea("sub_desc", $row['sub_desc'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Subscription Duration</b>" ,
												  $ibforums->adskin->form_simple_input("sub_length", $row['sub_length'] ) .
												  " ". $ibforums->adskin->form_dropdown( 'sub_unit',
												  	      array( 0 => array( 'w', 'Weeks' ), 1 => array( 'm', 'Months' ), 2 => array( 'y', 'Years' ) ),
												          $row['sub_unit'] )
												 ." <label for='neverexpire'><b>OR</b> <input type='checkbox' id='neverexpire' value='1' name='sub_noexpire' $subchecked /> never expire.</label>",
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Subscription Costs</b><br />Numerics and decimal points only please. Prices in your chosen default currency" ,
												  $ibforums->adskin->form_simple_input("sub_cost", $row['sub_cost'] , 7)
									     )      );
									     
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>New Member Group</b><br />Select the member group that the member will be moved into when payment has been cleared." ,
												  $ibforums->adskin->form_dropdown( 'sub_new_group' , $groups , $row['sub_new_group'] ),
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Run custom module on subscription start/end?</b><br />Optional - Advanced users only" ,
												  "<b>./modules/subsmanager/custom/cus_</b>".$ibforums->adskin->form_simple_input("sub_run_module", $row['sub_run_module'] , 7) ."<b>.php</b><br />(File must be in this format and location)"
									     )      );
									     
		
		$ibforums->html .= $ibforums->adskin->end_form($submit);
										 
		$ibforums->html .= $ibforums->adskin->end_table();
		
		//-------------------------------------------
		// Quick Jump Table
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'editpkginfo'),
												  2 => array( 'act'     , 'msubs'    ),
									     )  );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Edit Subscription / Gateway Specific Information");
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Payment Gateway</b>" ,
												  $ibforums->adskin->form_dropdown( 'method', $methods, $row['submethod_id'] )
										 )      );
										 
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Subscription Package</b>" ,
												  $ibforums->adskin->form_dropdown( 'sub', $packages, $row['sub_id'] )
										 )      );
												 
		$ibforums->html .= $ibforums->adskin->end_form( "Go!" );
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	
	}
	
	
	//------------------------------------------------
	// Payment gateway: Complete Edit
	//------------------------------------------------
	
	function do_edit_method()
	{
		global $ibforums, $DB, $std;
		
		$ibforums->admin->page_detail .= "<br /><b>Please make sure that you have correctly set up any third party payment gateway before allowing them here in IPB";
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("The chickens have escaped, there's feathers everywhere!");
		}
		
		$DB->query("SELECT * FROM ibf_subscription_methods WHERE submethod_id={$ibforums->input['id']}");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could locate a payment gateway with the id of {$ibforums->input['id']}");
		}
		
		if ( $ibforums->input['submethod_title'] == "" )
		{
			$ibforums->admin->error("You must enter a valid title for this payment gateway");
		}
		
		//if ( $ibforums->input['submethod_email'] == "" )
		//{
		//	$ibforums->admin->error("You must enter a valid email address for this payment gateway");
		//}
		
		$newbie =  array (
						   'submethod_title'    => $ibforums->input['submethod_title'],
						   'submethod_desc'     => $std->txt_safeslashes( $_POST['submethod_desc'] ),
						   'submethod_email'    => $ibforums->input['submethod_email'],
						   'submethod_active'   => $ibforums->input['submethod_active'],
						   'submethod_sid'	    => $ibforums->input['submethod_sid'],
						   'submethod_is_cc'    => $ibforums->input['submethod_is_css'],
						   'submethod_is_auto'  => $ibforums->input['submethod_is_auto'],
						   'submethod_use_currency' => $ibforums->input['submethod_use_currency'],
				         );
											  
		foreach( array( 1,2,3,4,5 ) as $id )
		{
			if ( isset($_POST['submethod_custom_'.$id]) )
			{
				$newbie[ 'submethod_custom_'.$id ] = $std->txt_safeslashes( $_POST['submethod_custom_'.$id] );
			}
		}
		
		$dbstring = $DB->compile_db_update_string($newbie);
											  
		$DB->query("UPDATE ibf_subscription_methods SET $dbstring WHERE submethod_id={$row['submethod_id']}");
		
		$ibforums->admin->save_log("Payment gateway '{$row['submethod_title']}' edited");
		
		$ibforums->admin->done_screen("Payment gateway edited", "IPB Subscriptions Manager", "act=msubs" );
	
	}
	
	
	//------------------------------------------------
	// Payment gateway: Edit Form
	//------------------------------------------------
	
	function edit_method()
	{
		global $ibforums, $DB, $std;
		
		
		$ibforums->admin->page_detail .= "<br /><b>Please make sure that you have correctly set up any third party payment gateway before allowing them here in IPB";
		
		if ( ! $ibforums->input['id'] )
		{
			$ibforums->admin->error("The chickens have escaped, there's feathers everywhere!");
		}
		
		$DB->query("SELECT * FROM ibf_subscription_methods WHERE submethod_id={$ibforums->input['id']}");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could locate a payment gateway with the id of {$ibforums->input['id']}");
		}
		
		$currency = array();
		$this_cur = "";
		
		$DB->query("SELECT * FROM ibf_subscription_currency");
		
		while ( $c = $DB->fetch_row() )
		{
			$currency[] = array( $c['subcurrency_code'], $c['subcurrency_desc'] );
			
			if ( $c['subcurrency_default'] )
			{
				$this_cur = $c['subcurrency_code'];
			}
		}
		
		//---------------------------------
		// Load the API...
		//---------------------------------
		
		$custom = array();
		
		if ( @file_exists( ROOT_PATH . 'modules/subsmanager/api_'.$row['submethod_name'].'.php' ) )
		{
			require_once( ROOT_PATH . 'modules/subsmanager/api_'.$row['submethod_name'].'.php' );
			
			$this->gateway = new gateway();
			
			//----------------------------------
			// Sort out the custom method fields
			//----------------------------------
			
			$form = $this->gateway->acp_return_method_variables();
			
			foreach( $form as $name => $data )
			{
				if ( $data['used'] != 0 )
				{
					$custom[] = $ibforums->adskin->add_td_row( array( "<b>{$data['formname']}</b><br />{$data['formextra']}</b>" ,
														  $ibforums->adskin->form_input( $name, $row[ $name ] )
												 )      );
				}
			}
			
		}
		else
		{
			$ibforums->admin->error("Could not locate the API in: ".ROOT_PATH . 'modules/subsmanager/api_'.$row['submethod_name'].'.php');
		}
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doeditmethod'  ),
												  2 => array( 'act'   , 'msubs'          ),
												  3 => array( 'id'    , $ibforums->input['id']       ),
									     )  );
									     
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "{$row['submethod_title']}'s Gateway Settings" );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Gateway Public Name</b>" ,
												  $ibforums->adskin->form_input("submethod_title", $row['submethod_title'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Gateway Public Description</b>" ,
												  $ibforums->adskin->form_textarea("submethod_desc", $row['submethod_desc'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Gateway Associated Email Address OR associated transaction key</b><br />This is not needed for all gateways." ,
												  $ibforums->adskin->form_input("submethod_email", $row['submethod_email'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Gateway Store / Seller ID</b><br />This is not needed for all gateways." ,
												  $ibforums->adskin->form_input("submethod_sid", $row['submethod_sid'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Gateway auto-completes order via return validation?</b><br />If the gateway does not support a return validation method, then make sure this is off." ,
												  $ibforums->adskin->form_yes_no("submethod_is_auto", $row['submethod_is_auto'] )
									     )      );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Gateway default currency?</b><br />Select the currency this gateway uses." ,
												  $ibforums->adskin->form_dropdown("submethod_use_currency", $currency, $row['submethod_use_currency'] )
									     )      );
									     
		if ( count( $custom ) > 0 )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( 'Gateway Specific Settings', 'left', 'catrow2' );
			
			$ibforums->html .= implode( "\n", $custom );
		}
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Activate this Gateway?</b><br>This will allow your members to use this option." ,
												  $ibforums->adskin->form_yes_no("submethod_active", $row['submethod_active'] )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->end_form("Edit Settings");
										 
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	
	}
	
	//------------------------------------------------
	// Show index screen
	//------------------------------------------------
		
	function index_screen()
	{
		global $ibforums, $DB, $std;
		
		$packages_cache = array();
		
		$pack_dropdown  = "";
		
		$DB->query("SELECT * FROM ibf_subscriptions ORDER BY sub_cost ASC");
		
		while( $row = $DB->fetch_row() )
		{
			$packages_cache[ $row['sub_id'] ] = $row;
			
			$pack_dropdown .= "<option value='{$row['sub_id']}'>{$row['sub_title']}</option>";
		}
		
		$ibforums->admin->page_detail .= "<br /><br />You may activate any or all of the default gateways and one of the additionally installed gateways.";
		
		$ibforums->html .= $ibforums->adskin->js_pop_win();
		
		//---------------------------------------
		// Show set up bit foist (Gangsta stylee)
		//---------------------------------------
		
		$trans   = array();
		
		$dead    = array();
		
		$pending = array();
		
		$DB->cache_add_query( 'intro_get_all', array(), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		
		while ( $t = $DB->fetch_row() )
		{
			$trans[ $t['subtrans_method'] ] = $t;
		}
		
		$DB->cache_add_query( 'intro_get_failed_dead', array(), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		            
		while ( $t = $DB->fetch_row() )
		{
			$dead[ $t['subtrans_method'] ] = $t;
		}
		
		$DB->cache_add_query( 'intro_get_failed_pending', array(), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		            
		while ( $t = $DB->fetch_row() )
		{
			$pending[ $t['subtrans_method'] ] = $t;
		}
		
		//---------------------------------------
		// Show gateways
		//---------------------------------------
		
		$ibforums->adskin->td_header[] = array( "Payment Gateway"  , "20%" );
		$ibforums->adskin->td_header[] = array( "Set Up"           , "10%" );
		$ibforums->adskin->td_header[] = array( "Active"           , "10%" );
		$ibforums->adskin->td_header[] = array( "Transactions"     , "10%" );
		$ibforums->adskin->td_header[] = array( "Current"          , "10%" );
		$ibforums->adskin->td_header[] = array( "Pending"          , "10%" );
		$ibforums->adskin->td_header[] = array( "Failed"           , "10%" );
		$ibforums->adskin->td_header[] = array( "Edit Gateway Info" , "20%" );
		
		$types = array();
		$types['default'] = array();
		$types['custom']  = array();
		
		$outer = $DB->query("SELECT * FROM ibf_subscription_methods");
		
		$total_income  = 0;
		
		$total_dead    = 0;
		
		$total_pending = 0;
		
		$total_gateway = 0;
		
		$total_email   = 0;
		
		$ibforums->html .= $ibforums->adskin->start_table( "Available Payment Gateways" );
		
		$ibforums->html .= $ibforums->adskin->add_td_basic( 'Default Gateways', 'left', 'catrow2' );
		
		while ( $row = $DB->fetch_row( $outer ) )
		{
			$active = "<span style='color:red;font-weight:bold'>X</span>";
			
			if ( $row['submethod_active'] == 1 )
			{
				$active = "<span style='color:green;font-weight:bold'>Y</span>";
			}
			
			if ( $row['submethod_email'] != "" )
			{
				$total_email++;
			}
			
			$total_gateway++;
			
			$total_income  += $trans[ $row['submethod_name'] ]['revenue'];
			
			$total_dead    += $dead[ $row['submethod_name'] ]['revenue'];
			
			$total_pending += $pending[ $row['submethod_name'] ]['revenue'];
			
			$total          = $trans[ $row['submethod_name'] ]['total'] + $dead[ $row['submethod_name'] ]['total'] + $pending[ $row['submethod_name'] ]['total'];
			
			$temp = $ibforums->adskin->add_td_row( array( "<b>{$row['submethod_title']}</b>" ,
											  "<center><a href='{$ibforums->base_url}&act=msubs&code=editmethod&id={$row['submethod_id']}'>Set Up</a></center>",
											  "<center>$active</center>",
											  "<center>&nbsp;{$total}&nbsp;</center>",
											  "<center><span style='color:green'>". number_format( $trans[ $row['submethod_name'] ]['revenue'], 2, ".", "," )."</span></center>",
											  "<center><span style='color:orange'>". number_format( $pending[ $row['submethod_name'] ]['revenue'], 2, ".", "," )."</span></center>",
											  "<center><span style='color:red'>". number_format( $dead[ $row['submethod_name'] ]['revenue'], 2, ".", "," )."</span></center>",
											  "<center><select onchange=\"var manid=this.options[this.selectedIndex].value;if(manid != -1){ window.location='{$ibforums->base_url}&act=msubs&code=editpkginfo&method={$row['submethod_id']}&sub='+manid;}\" class='dropdown'><option value='-1'>--Choose--</option>{$pack_dropdown}</select></center>",
									 )      );
									 
			if ( preg_match( "/ ".$row['submethod_name']. "/", " paypal nochex manual " ) )
			{
				$types['default'][] = $temp;
			}
			else
			{
				$types['custom'][] = $temp;
			}
		}
		
		$ibforums->html .= implode( "\n", $types['default'] );
		
		if ( count( $types['custom'] ) > 0 )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( 'Additional Installed Gateways', 'left', 'catrow2' );
			$ibforums->html .= implode( "\n", $types['custom'] );
		}
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( array("<div align='right'><b>Cumulative Revenue: (". number_format( $total_income + $total_dead + $total_pending, 2, ".", "," ).")</b></div>", 4) ,
												  "<center><a href='{$ibforums->base_url}&act=msubs&code=dosearch&status=paid'>". number_format( $total_income, 2, ".", "," )."</a></center>",
												  "<center><a href='{$ibforums->base_url}&act=msubs&code=dosearch&status=pending'>". number_format( $total_pending, 2, ".", "," )."</a></center>",
												  "<center><a href='{$ibforums->base_url}&act=msubs&code=dosearch&status=failed'>". number_format( $total_dead, 2, ".", "," )."</a></center>",
												  "",
										 )      );
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		//---------------------------------------
		// Show available plans...
		//---------------------------------------
		
		$expired = array();
		$active  = array();
		
		$time = time();
		
		$DB->cache_add_query( 'intro_plans_a', array( 'time' => $time ), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		            
		while ( $t = $DB->fetch_row() )
		{
			$expired[ $t['subtrans_sub_id'] ] = $t['total'];
		}
		
		$DB->cache_add_query( 'intro_plans_b', array( 'time' => $time ), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		            
		while ( $t = $DB->fetch_row() )
		{
			$active[ $t['subtrans_sub_id'] ] = $t['total'];
		}
		
		$ibforums->adskin->td_header[] = array( "Subscription Plan"  , "28%" );
		$ibforums->adskin->td_header[] = array( "Cost"               , "10%" );
		$ibforums->adskin->td_header[] = array( "Duration"           , "10%" );
		$ibforums->adskin->td_header[] = array( "Active Members"     , "10%" );
		$ibforums->adskin->td_header[] = array( "Expired Members"    , "12%" );
		$ibforums->adskin->td_header[] = array( "Edit"               , "10%" );
		$ibforums->adskin->td_header[] = array( "Unsubscribe"        , "10%" );
		$ibforums->adskin->td_header[] = array( "Delete"             , "10%" );
		
		$duration = array( 'w' => "Week", 'm' => "Month", 'y' => "Year" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Subscription Packages" );
		
		
		foreach ( $packages_cache as $id => $row )
		{
			if ( $row['sub_unit'] != 'x' )
			{
				$duration_text = "{$row['sub_length']} {$duration[ $row['sub_unit'] ]}(s)";
			}
			else
			{
				$duration_text = "Never Expire";
			}
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$row['sub_title']}</b><br /><i>{$row['sub_desc']}</i><br /><a href='{$ibforums->base_url}&act=msubs&code=dosearch&package={$row['sub_id']}' style='color:green'>Show members</a> &middot; <a href='javascript:pop_win(\"&act=msubs&code=overview&package={$row['sub_id']}\", \"Overview\", 600,200)' style='color:green'>Launch Overview</a>" ,
													  "<center>". number_format( $row['sub_cost'], 2, ".", "," )."</center>",
													  "<center>$duration_text</center>",
													  "<center>".intval($active[ $row['sub_id'] ])."</center>",
													  "<center><span style='color:red'>".intval($expired[ $row['sub_id'] ])."</span></center>",
													  "<center><a href='{$ibforums->base_url}&act=msubs&code=editpackage&id={$row['sub_id']}'>Edit</a></center>",
													  "<center><a href='{$ibforums->base_url}&act=msubs&code=removemembers&type=all&id={$row['sub_id']}'>All<a/>, <a href='{$ibforums->base_url}&act=msubs&code=removemembers&type=expired&id={$row['sub_id']}'>Expired</a></center>",
													  "<center><a href='{$ibforums->base_url}&act=msubs&code=removepackage&id={$row['sub_id']}'>Delete</a></center>",
											 )      );
		}
		
		$ibforums->html .= $ibforums->adskin->add_td_basic("<a href='{$ibforums->admin->base_url}&act=msubs&code=addpackage' class='fauxbutton'>ADD NEW SUBSCRIPTION PACKAGE</a></center>", "center", "pformstrip");

		$ibforums->html .= $ibforums->adskin->end_table();
		
		//---------------------------------------
		// Show tools and stuff
		//---------------------------------------
		
		$packages = array( 0 => array( 'all', 'All packages' ) );
		
		foreach( $packages_cache as $sub_id => $sub_data )
		{
			$packages[] = array( $sub_id, $sub_data['sub_title'] );
		}
		
		$state = array(
						0 => array( 'any'    , 'Any'  ),
						1 => array( 'paid'   , 'Paid' ),
						2 => array( 'failed' , 'Failed' ),
						3 => array( 'expired', 'Expired' ),
						4 => array( 'dead'   , 'Dead' ),
						5 => array( 'pending', 'Pending'),
					  );
					  
		$fields = array(
						0 => array( 'name'     , 'Member Name'     ),
						1 => array( 'trxid'    , 'Transaction ID'  ),
						2 => array( 'paid'     , 'Amount Paid'     ),
						3 => array( 'subscrid' , 'Subscription ID' ),
					   );
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dosearch'  ),
												  2 => array( 'act'   , 'msubs'     ),
									     )  );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "50%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "50%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Find / Edit Transactions" );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Payment status is</b>" ,
												  $ibforums->adskin->form_dropdown("status", $state )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>For subscription package</b>" ,
												  $ibforums->adskin->form_dropdown("package", $packages )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Where...</b><br />Optional - leave blank to ignore" ,
												  $ibforums->adskin->form_dropdown("searchtype", $fields )
												  ." contains... ".$ibforums->adskin->form_simple_input("search", "", 10 )
									     )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Where subscription is due to expire within the next...</b><br />Optional - leave blank to ignore" ,
												  $ibforums->adskin->form_simple_input("expiredays", "", 4)." days"
									     )      );
		
		$ibforums->html .= $ibforums->adskin->add_td_basic("<a href='{$ibforums->admin->base_url}&act=msubs&code=addtransaction' class='fauxbutton'>Manually Add New Transaction</a></center>", "center", "tdrow1");


		$ibforums->html .= $ibforums->adskin->end_form("Go!");
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		//----------------------------------------------------
		// Search logs..
		//----------------------------------------------------
		
		$fields = array(
					    0 => array( 'none', 'Any field' ),
						1 => array( 'post', 'POST data' ),
						2 => array( 'msg' , 'Message'   ),
					   );
					   
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'searchlog'  ),
												  2 => array( 'act'   , 'msubs'     ),
									     )  );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "50%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "50%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Search Transaction Logs" );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Where...</b><br />Optional - leave blank to ignore" ,
												  $ibforums->adskin->form_dropdown("searchtype", $fields )
												  ." contains... ".$ibforums->adskin->form_simple_input("search", "", 10 )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search Logs");
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
		
	}
	
	//------------------------------------------------
	// Do search
	//------------------------------------------------
	
	function do_search()
	{
		global $ibforums, $DB, $std;
		
		$ibforums->admin->page_detail .= "<br /><br /><b>Important Note!</b> Updating the transaction status will complete the transaction and change the member's user group.<br />
								For example, if you change the status to 'paid' - this will move the member into the new group specified in the subscription package. If you
								change the status to 'failed', 'pending' or 'expired', this will move the member back into their previous group or the default member group if
								their previous group no longer exists. If this is not desired, edit each transaction separately.
								<br />
								If the package has since been deleted, the member group will not be changed.";
		
		$st  = intval( $ibforums->input['st'] );
		$end = 50;
		
		$expiredays = intval( $ibforums->input['expiredays'] );
		$searchtype = trim($ibforums->input['searchtype']);
		$search     = trim($ibforums->input['search']);
		$package    = intval(trim($ibforums->input['package']));
		$status     = trim($ibforums->input['status']);
		
		$qstring    = "expiredays={$expiredays}&searchtype={$searchtype}&search={$search}&package={$package}&status={$status}";
		
		$query = array();
		
		if ( $expiredays > 0 )
		{
			$date    = time() + $expiredays * 86400;
			$query[] = "s.subtrans_end_date < $date";
		}
		
		if ( $search != "" )
		{
			switch ( $searchtype )
			{
				case 'name':
					$DB->cache_add_query( 'get_lower_like', array( 'name' => $search ), 'sql_subsm_queries' );
					$DB->cache_exec_query();
					
					$ids = array();
					
					while( $mem = $DB->fetch_row() )
					{
						$ids[] = $mem['id'];
					}
					
					if ( count($ids) > 0 )
					{
						$query[] = "s.subtrans_member_id IN (".implode(",", $ids ).")";
					}
					break;
				case 'trxid':
					$query[] = 's.subtrans_trxid="'.$search.'"';
					break;
				case 'paid':
					$query[] = "s.subtrans_paid='".$search."'";
				
				default:
					break;
			}
		}
		
		if ( $package > 0 )
		{
			$query[] = "s.subtrans_sub_id=$package";
		}
		
		if ( $status != "" AND $status != "any" )
		{
			$query[] = "s.subtrans_state='$status'";
		}
		
		if ( count($query) > 0 )
		{
			$middle_query = "WHERE ".implode( " AND ", $query );
		}
		
		$DB->cache_add_query( 'do_search', array( 'query' => $middle_query, 'st' => $st, 'end' => $end ), 'sql_subsm_queries' );
		$final_query = $DB->cur_query;
		$DB->flush_query();
		
		//-------------------------------------------
		// Get a count...
		//-------------------------------------------
		
		$DB->query("SELECT COUNT(*) as count FROM ibf_subscription_trans s ".$middle_query);
		
		$t = $DB->fetch_row();
		
		$cnt = intval( $t['count'] );
		
		//-------------------------------------------
		// Page links...
		//-------------------------------------------
		
		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $cnt,
											   'PER_PAGE'    => 50,
											   'CUR_ST_VAL'  => $st,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Multiple Pages",
											   'BASE_URL'    => $ibforums->base_url."&act=msubs&code=dosearch&".$qstring,
									  )      );
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'domodifytrans'),
												  2 => array( 'act'     , 'msubs'        ),
												  3 => array( 'qstring' , $qstring       ),
									     )  );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"       , "3%" );
		$ibforums->adskin->td_header[] = array( "Member Name"  , "20%" );
		$ibforums->adskin->td_header[] = array( "Email"        , "20%" );
		$ibforums->adskin->td_header[] = array( "Package"      , "15%" );
		$ibforums->adskin->td_header[] = array( "Paid"         , "10%" );
		$ibforums->adskin->td_header[] = array( "Started"      , "10%" );
		$ibforums->adskin->td_header[] = array( "Expires"      , "10%" );
		$ibforums->adskin->td_header[] = array( "Status"       , "12%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Transactions Found" );
		
		$ibforums->html .= $ibforums->adskin->add_td_basic( "$links", "right", "catrow2");
		
		$DB->query( $final_query );
		
		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No matches found", "center");
		}
		else
		{
			while ( $row = $DB->fetch_row() )
			{
				if ( $row['sub_title'] == "" )
				{
					$row['sub_title'] = "<i>Since Deleted</i>";
				}
				
				if ( $row['id'] == "" )
				{
					$row['name']  = "<i>Member Deleted (ID: {$row['subtrans_member_id']})</i>";
					$row['email'] = "<i>Member Since Deleted</i>";
				}
				
				$color = "";
				
				switch( $row['subtrans_state'] )
				{
					case 'paid':
						$color = 'green';
						break;
					case 'dead':
						$color = 'gray';
						break;
					case 'pending':
						$color = 'orange';
						break;
					case 'failed':
						$color = 'red';
						break;
					case 'expired':
						$color = 'gray';
						break;
					default:
						$color = 'black';
						break;
				}
				
				$end_date = $row['sub_unit'] == 'x' ? 'Lifetime' : $std->get_date( $row['subtrans_end_date'], 'JOINED' );
				
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<center><input type='checkbox' name='subtrans_id_{$row['subtrans_id']}' value='1' /></center>" ,
														  "<b><a href='{$ibforums->vars['board_url']}/index.php?showuser={$row['subtrans_member_id']}' target='_blank'>{$row['name']}</a></b><br /><span style='color:green'>[ <a href='{$ibforums->base_url}&act=msubs&code=edittransaction&id={$row['subtrans_id']}' style='color:green'>Edit Transaction</a> ]</span>",
														  "{$row['email']}",
														  "{$row['sub_title']}",
														  "{$row['subtrans_paid']}",
														  "<center>" . $std->get_date( $row['subtrans_start_date'], 'JOINED' ) . "</center>",
														  "<center>" . $end_date . "</center>",
														  "<center><span style='color:$color'>" . strtoupper( $row['subtrans_state'] ) . "</span></center>",
												 )      );
			}
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( array( "<div align='right'><a href='{$ibforums->admin->base_url}&act=msubs&code=addtransaction' class='fauxbutton'>Manually Add New Transaction</a> &nbsp; &nbsp; &nbsp; &nbsp; <input type='submit' id='button' name='delete' value='DELETE' /> or <b>update selected entries to</b></div>", 7 ) ,
																			$ibforums->adskin->form_dropdown( 'updateto', array( 0 => array( 'paid'   , 'Paid'    ),
																																 1 => array( 'pending', 'Pending' ),
																																 2 => array( 'failed' , 'Failed'  ),
																																 3 => array( 'expired', 'Expired' ) )
																											)
											 				)      );
											 
		}
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->html .= $ibforums->adskin->end_form_standalone("Update");
		
		$ibforums->admin->output();
	}
	
	//------------------------------------------------
	// Do search log
	//------------------------------------------------
	
	function do_search_log()
	{
		global $ibforums, $DB, $std;
		
		$st  = intval( $ibforums->input['st'] );
		$end = 50;
		
		$searchtype = trim($ibforums->input['searchtype']);
		$search     = trim($ibforums->input['search']);
		
		$qstring    = "searchtype={$searchtype}&search={$search}";
		
		$query = array();
		
		if ( $search != "" )
		{
			switch ( $searchtype )
			{
				case 'post':
					$query[] = 'sublog_postdata LIKE "%'.$search.'%"';
					break;
				case 'msg':
					$query[] = "sublog_data LIKE '%".$search."%'";
				
				default:
					break;
			}
		}
		
		$ibforums->html .= $ibforums->adskin->js_pop_win();
		
		if ( count($query) > 0 )
		{
			$middle_query = "WHERE ".implode( " AND ", $query );
		}
		
		$DB->cache_add_query( 'do_search_two', array( 'query' => $middle_query, 'st' => $st, 'end' => $end ), 'sql_subsm_queries' );
		$final_query = $DB->cur_query;
		$DB->flush_query();
		
		//-------------------------------------------
		// Get a count...
		//-------------------------------------------
		
		$DB->query("SELECT COUNT(*) as count FROM ibf_subscription_logs ".$middle_query);
		
		$t = $DB->fetch_row();
		
		$cnt = intval( $t['count'] );
		
		//-------------------------------------------
		// Page links...
		//-------------------------------------------
		
		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $cnt,
											   'PER_PAGE'    => 50,
											   'CUR_ST_VAL'  => $st,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Multiple Pages",
											   'BASE_URL'    => $ibforums->base_url."&act=msubs&code=searchlog&".$qstring,
									  )      );
		
		$ibforums->adskin->td_header[] = array( "ID"           , "5%" );
		$ibforums->adskin->td_header[] = array( "Message"      , "55%" );
		$ibforums->adskin->td_header[] = array( "IP"           , "10%" );
		$ibforums->adskin->td_header[] = array( "POST"         , "10%" );
		$ibforums->adskin->td_header[] = array( "Date"         , "20%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( "Transaction Entries Found" );
		
		$ibforums->html .= $ibforums->adskin->add_td_basic( "$links", "right", "catrow2");
		
		$DB->query( $final_query );
		
		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No matches found", "center");
		}
		else
		{
			while ( $row = $DB->fetch_row() )
			{
				
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<center>{$row['sublog_id']}</center>" ,
														  "{$row['sublog_data']}",
														  "{$row['sublog_ipaddress']}",
														  "<center><a href='javascript:pop_win(\"&act=msubs&code=searchlogview&id={$row['sublog_id']}\", \"PostData\", 300,500)'>View</a></center>",
														  "<center>" . $std->get_date( $row['sublog_date'], 'SHORT' ) . "</center>",
												 )      );
			}
											 
		}
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	}
	
	//------------------------------------------------
	// Show POST DATA
	//------------------------------------------------
	
	function do_search_log_view()
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Get log
		//-------------------------------------------
		
		$id = intval($ibforums->input['id']);
		
		$DB->query("SELECT * FROM ibf_subscription_logs WHERE sublog_id=$id");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Cannot get sub log entry, no record for id $id");
		}
		
		$post_data = explode( "\n", $row['sublog_postdata'] );
		
		//-------------------------------------------
		// Set up the table header
		//-------------------------------------------
		
		$ibforums->adskin->td_header[] = array( "Key"    , "20%" );
		$ibforums->adskin->td_header[] = array( "Value"  , "80%" );
		
		//-------------------------------------------
		// Start the table
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_table( "POST DATA FOR TR_ID: $id" );
		
		foreach( $post_data as $j => $data )
		{
			list( $key, $value ) = explode( "=", $data, 2 );
			
			if ( $key == "" )
			{
				continue;
			}
			
			$ibforums->html .=  $ibforums->adskin->add_td_row( array( trim($key), preg_replace( "/;$/", "", trim($value) ) ) );
		}
							     
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->print_popup();
	
	}
									     
	//------------------------------------------------
	// Save a transaction!
	//------------------------------------------------
	
	function save_transaction($type='edit')
	{
		global $ibforums, $DB, $std;
		
		$save = array();
		
		if ( $type == 'edit' )
		{
			if ( $ibforums->input['id'] == "" )
			{
				$ibforums->admin->error("No ID was passed - please go back and try again");
			}
			
			$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_id=".intval($ibforums->input['id']));
			
			$subtrans = $DB->fetch_row();
			
			$save['subtrans_member_id'] = $subtrans['subtrans_member_id'];
			
			$DB->query("SELECT * FROM ibf_members WHERE id={$save['subtrans_member_id']}");
			
			$mem = $DB->fetch_row();
			
		}
		else
		{
			if ( $ibforums->input['membername'] == "" )
			{
				$this->edit_transaction( $type, "You must enter a valid name" );
			}
			
			$name = strtolower( str_replace( '|', "&#124;", $ibforums->input['membername'] ) );
			
			$DB->cache_add_query( 'get_lower_name', array( 'name' => $name ), 'sql_subsm_queries' );
			$DB->cache_exec_query();
			
			if ( ! $mem = $DB->fetch_row() )
			{
				$this->edit_transaction( $type, "Could not locate a member called '{$ibforums->input['membername']}'" );
			}
			
			$save['subtrans_member_id']  = $mem['id'];
			$save['subtrans_start_date'] = time();
		}
		
		//-------------------------------------------
		// Check...
		//-------------------------------------------
		
		$date_count = 0;
		
		foreach( array( 'month', 'day', 'year' ) as $i )
		{
			if ( $ibforums->input[ $i ] )
			{
				$date_count++;
			}
		}
		
		if ( $date_count > 0 and $date_count < 3 )
		{
			$this->edit_transaction( $type, "You must complete the expiry date fully" );
		}
		
		if ( $ibforums->input['subtrans_paid'] == "" )
		{
			$this->edit_transaction( $type, "Please enter a valid total for the amount paid" );
		}
		
		if ( $date_count )
		{
			if ( ! checkdate( $ibforums->input['month'], $ibforums->input['day'] , $ibforums->input['year'] ) )
			{
				$this->edit_transaction( $type, "You have entered an impossible expiry date - please check your input" );
			}
		
			$new_expiry = mktime( 23, 59, 59, $ibforums->input['month'], $ibforums->input['day'], $ibforums->input['year'] );
			
			if ( $new_expiry < time() )
			{
				$this->edit_transaction( $type, "You cannot set an expiry date before the start of the subscription." );
			}
		}
		else
		{
			$new_expiry = 9999999999;
		}
		
		$save['subtrans_method']     = $ibforums->input['subtrans_method'];
		$save['subtrans_end_date']   = $new_expiry;
		$save['subtrans_sub_id']     = $ibforums->input['subtrans_sub_id'];
		$save['subtrans_state']      = $ibforums->input['subtrans_state'];
		$save['subtrans_old_group']  = $ibforums->input['subtrans_old_group'];
		$save['subtrans_paid']       = $ibforums->input['subtrans_paid'];
		$save['subtrans_cumulative'] = $ibforums->input['subtrans_paid'];
		
		$DB->query("SELECT * FROM ibf_subscription_currency WHERE subcurrency_default=1");
		$default = $DB->fetch_row();
		
		$save['subtrans_currency']  = $default['subcurrency_code'];
		
		if ( $type == 'edit' )
		{
			$db_string = $DB->compile_db_update_string( $save );
			
			$DB->query("UPDATE ibf_subscription_trans SET $db_string WHERE subtrans_id={$ibforums->input['id']}");
		}
		else
		{
			$db_string = $DB->compile_db_insert_string( $save );
			
			$DB->query("INSERT INTO ibf_subscription_trans ({$db_string['FIELD_NAMES']}) VALUES({$db_string['FIELD_VALUES']})");
			
			$ibforums->input['id'] = $DB->get_insert_id();
		}
		
		//----------------------------------------
		// Sort out member
		//----------------------------------------
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id={$ibforums->input['subtrans_sub_id']}");
		
		$sub = $DB->fetch_row();
		
		if ( $sub['sub_new_group'] )
		{
			if ( $ibforums->input['subtrans_state'] == 'paid' )
			{
				$DB->do_update( "members", array( 'mgroup'  => intval($sub['sub_new_group']),
												  'sub_end' => $new_expiry,
												), "id={$save['subtrans_member_id']}" );
			}
			
			if ( USE_MODULES == 1 )
			{
				$this->modules->register_class(&$this);
				$this->modules->on_group_change($save['subtrans_member_id'], $sub['sub_new_group']);
			}
			
			$name = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $sub['sub_run_module'] );
    	
			if ( $name != "" )
			{
				if ( @file_exists( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' ) )
				{
					require_once( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' );
					
					$this->customsubs = new customsubs();
					
					$this->customsubs->subs_paid($sub, $mem, $ibforums->input['id']);
				}
			}
		}
		else
		{
			$DB->do_update( "members", array( 'sub_end' => $new_expiry ), "id={$save['subtrans_member_id']}" );
		}
		
		$std->boink_it( $ibforums->base_url."&act=msubs&code=dosearch" );
	
	}
	
	
	
	
	//------------------------------------------------
	// Edit a transaction!
	//------------------------------------------------
	
	function edit_transaction($type='edit', $error="")
	{
		global $ibforums, $DB, $std;
		
		
		//-------------------------------------------
		// Set up
		//-------------------------------------------
		
		$state = array(
						1 => array( 'paid'   , 'Paid' ),
						2 => array( 'failed' , 'Failed' ),
						3 => array( 'expired', 'Expired' ),
						4 => array( 'dead'   , 'Dead' ),
						5 => array( 'pending', 'Pending'),
					  );
		
		$DB->query("SELECT sub_id, sub_title, sub_cost FROM ibf_subscriptions ORDER BY sub_cost");
		
		$packages = array();
		
		while ( $p = $DB->fetch_row() )
		{
			$packages[] = array( $p['sub_id'], $p['sub_title']." ({$p['sub_cost']})" );
		}
		
		$methods = array();
		
		$DB->query("SELECT * FROM ibf_subscription_methods ORDER BY submethod_title");
		
		while ( $m = $DB->fetch_row() )
		{
			$methods[] = array( $m['submethod_name'], $m['submethod_title'] );
		}
		
		$groups = array( 0 => array( 0, "--Don't Change Group--" ) );
		
		$DB->query("SELECT g_id, g_title FROM ibf_groups ORDER BY g_title");
		
		while ( $r = $DB->fetch_row() )
		{
			$groups[] = array( $r['g_id'], $r['g_title'] );
		}
		
		//-------------------------------------------
		// Do the twist and shout.
		//-------------------------------------------
		
		if ( $type == 'edit' )
		{
			if ( $ibforums->input['id'] == "" )
			{
				$ibforums->admin->error("No ID was passed - please go back and try again");
			}
			
			$DB->cache_add_query( 'edit_trans', array( 'id' => $ibforums->input['id'] ), 'sql_subsm_queries' );
			$DB->cache_exec_query();
			
			if ( ! $row = $DB->fetch_row() )
			{
				$ibforums->admin->error("Could not find a subscription transaction with the id {$ibforums->input['id']}");
			}
			
			if ( $row['name'] == "" )
			{
				$row['name'] = "<i>Member Since Deleted (ID: {$row['subtrans_member_id']})</i>";
			}
			
			$code   = "doedittransaction";
			$button = "Complete Edit";
			$table  = "Edit Transaction";
			$name   = $row['name'];
			
			if ( $row['sub_unit'] == 'x' )
			{
				$month = '';
				$day   = '';
				$year  = '';
			}
			else
			{
				list( $month, $day, $year ) = explode( ",", gmdate( 'n,j,Y', $row['subtrans_end_date'] ) );
			}
			
		}
		else
		{
			$code   = "doaddtransaction";
			$button = "Complete Entry";
			$table  = "Add Transaction";
			$name   = $ibforums->adskin->form_input( 'membername' , $ibforums->input['membername']). " <a href='index.php?act=Members' target='_blank'>Find Members</a>";
			$row    = array();
			
			list( $month, $day, $year ) = explode( ",", gmdate( 'n,j,Y' ) );
		}
		
		//-------------------------------------------
		// Error?
		//-------------------------------------------
		
		if ( $error != "" )
		{
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );
		
			$ibforums->html .= $ibforums->adskin->start_table( "Error" );
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( $error )  );
			
			$ibforums->html .= $ibforums->adskin->end_table();
		}
		
		//-------------------------------------------
		// Carry on!
		//-------------------------------------------
		
		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , $code     ),
																 2 => array( 'act'     , 'msubs'   ),
																 3 => array( 'id'      , $ibforums->input['id'] ),
														)      );
									     		   
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$ibforums->html .= $ibforums->adskin->start_table( $table );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member Name</b>" ,
											               	     $name,
									                    )      );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Package Subscribed</b>" ,
												                 $ibforums->adskin->form_dropdown("subtrans_sub_id", $packages, $ibforums->input['subtrans_sub_id'] == "" ? $row['subtrans_sub_id'] : $ibforums->input['subtrans_sub_id'])
									                    )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Previous Group</b>" ,
												                 $ibforums->adskin->form_dropdown("subtrans_old_group", $groups, $ibforums->input['subtrans_old_group'] == "" ? $row['subtrans_old_group'] : $ibforums->input['subtrans_old_group'] )
									                    )      );
		
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Amount Paid</b><br />Numerics and decimal points only please. Prices in your currency default" ,
												                 $ibforums->adskin->form_simple_input("subtrans_paid", $ibforums->input['subtrans_paid'] == "" ? $row['subtrans_paid'] : $ibforums->input['subtrans_paid'] , 7)
									                    )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Payment Method</b>" ,
												                 $ibforums->adskin->form_dropdown("subtrans_method", $methods, $ibforums->input['subtrans_method'] == "" ? $row['subtrans_method'] : $ibforums->input['subtrans_method'] )
									                    )      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Payment Status</b>" ,
																 $ibforums->adskin->form_dropdown("subtrans_state", $state, $ibforums->input['subtrans_state'] == "" ? $row['subtrans_state'] : $ibforums->input['subtrans_state'] )
														)      );
									     
		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Expires</b><br />MM DD YYYY<div class='graytext'>Leave all fields blank to never expire this transaction</div>" ,
																 $ibforums->adskin->form_simple_input("month", $ibforums->input['month'] == "" ? $month : $ibforums->input['month'], 2 )." ".
																 $ibforums->adskin->form_simple_input("day"  , $ibforums->input['day']   == "" ? $day   : $ibforums->input['day']  , 2 )." ".
																 $ibforums->adskin->form_simple_input("year" , $ibforums->input['year']  == "" ? $year  : $ibforums->input['year'] , 4 )." (Max: 2037)"
														)      );
									     
		$ibforums->html .= $ibforums->adskin->end_form( $button );
		
		$ibforums->html .= $ibforums->adskin->end_table();
		
		$ibforums->admin->output();
	}
	
	//------------------------------------------------
	// DELETE teh trannies!
	//------------------------------------------------
	
	function do_delete_trans()
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Grab member groups
		//-------------------------------------------
		
		$groups = array();
		
		$DB->query("SELECT g_id, g_title FROM ibf_groups ORDER BY g_title");
		
		while ( $r = $DB->fetch_row() )
		{
			$groups[ $r['g_id'] ] = 1;
		}
		
		$ids = explode( ",", $ibforums->input['ids'] );
		
		$id_count = count($ids);
		
		if ( $id_count < 1 )
		{
			$ibforums->admin->error("You did not select any transactions to modify");
		}
		
		$DB->cache_add_query( 'delete_trans', array( 'ids' => $ids ), 'sql_subsm_queries' );
		$outer = $DB->cache_exec_query();
		
		while ( $row = $DB->fetch_row( $outer ) )
		{
			if ( $row['subtrans_state'] == 'paid' )
			{
				$change_to_group = intval($row['subtrans_old_group']);
			}
			
			if ( $change_to_group > 0 )
			{
				if ( $groups[ $change_to_group ] != 1 )
				{
					$change_to_group = $INFO['member_group'];
				}
				
				if ( $row['subtrans_member_id'] != "" )
				{
					$DB->do_update( "members", array( 'mgroup'  => $change_to_group,
													  'sub_end' => 0,
													), "id={$row['subtrans_member_id']}" );
				}
				
				if ( USE_MODULES == 1 )
				{
					$this->modules->register_class(&$this);
					$this->modules->on_group_change($row['subtrans_member_id'], $change_to_group);
				}
			}
			else
			{
				$DB->do_update( "members", array( 'sub_end' => 0 ), "id={$row['subtrans_member_id']}" );
			}
			
			$name = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $row['sub_run_module'] );
    	
			if ( $name != "" )
			{
				if ( @file_exists( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' ) )
				{
					require_once( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' );
					
					$this->customsubs = new customsubs();
					
					if ( $row['subtrans_state'] == 'paid' )
					{
						$this->customsubs->subs_failed($row, $row, $row['subtrans_id']);
					}
				}
			}
		}
		
		$DB->query("DELETE FROM ibf_subscription_trans WHERE subtrans_id IN (".implode(",", $ids).")");
		
		$ibforums->admin->save_log("$id_count subscription transactions deleted");
	
		$std->boink_it( $ibforums->base_url."&act=msubs&code=dosearch" );
	}
	
	//------------------------------------------------
	// Modify teh trannies!
	//------------------------------------------------
	
	function do_modify_trans()
	{
		global $ibforums, $DB, $std;
		
		$day_to_seconds = array( 'd' => 86400,
								 'w' => 604800,
								 'm' => 2592000,
								 'y' => 31536000,
							   );
		
		//-------------------------------------------
		// Grab member groups
		//-------------------------------------------
		
		$groups = array();
		
		$DB->query("SELECT g_id, g_title FROM ibf_groups ORDER BY g_title");
		
		while ( $r = $DB->fetch_row() )
		{
			$groups[ $r['g_id'] ] = 1;
		}
		
		//-------------------------------------------
		// Get incoming IDS
		//-------------------------------------------
		
		$ids = array();
		
		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^subtrans_id_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}
		
		if ( count($ids) < 1 )
		{
			$ibforums->admin->error("You did not select any transactions to modify");
		}
		
		$id_string = implode( ",", $ids );
		
		$id_count  = count($ids);
		
		//---------------------------------------
		// Was delete pressed?
		// How the hell should I know?
		// What is this, the magic oracle??
		//---------------------------------------
		
		if ( $ibforums->input['delete'] != "" )
		{
			$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'dotransdelete' ),
																	 2 => array( 'act'     , 'msubs'         ),
																	 3 => array( 'ids'     , $id_string      ),
															)  );
									     		   
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );
			
			$ibforums->html .= $ibforums->adskin->start_table( "Removal Confirmation" );
			
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Transactions to remove: $id_count</b><br /><br />Deleting these transactions will remove all subscribed members and return them back to their previous group.
												       Please note that if the group that they were in no longer exists, they will be moved into the default member group. The cumulative revenue will be recalculated."
									         )      );
			
			$ibforums->html .= $ibforums->adskin->end_form( "Delete" );
			
			$ibforums->html .= $ibforums->adskin->end_table();
			
			$ibforums->admin->output();
		}
		else
		{
			
			$DB->cache_add_query( 'delete_trans', array( 'ids' => $ids ), 'sql_subsm_queries' );
			$outer = $DB->cache_exec_query();
			
			while ( $row = $DB->fetch_row( $outer ) )
			{
				$change_to_group = 0;
				
				if ( $ibforums->input['updateto'] == 'paid' )
				{
					if ( $row['subtrans_state'] != 'paid' )
					{
						$change_to_group = intval($row['sub_new_group']);
					}
					
					if ( $row['sub_unit'] == 'x' )
					{
						$change_date = 9999999999;
					}
					else
					{
						$change_date = time() + ( $row['sub_length'] * $day_to_seconds[ $row['sub_unit'] ] );
					}
				}
				else
				{
					//-----------------------
					// Was it paid?
					//-----------------------
					
					if ( $row['subtrans_state'] == 'paid' )
					{
						$change_to_group = intval($row['subtrans_old_group']);
					}
					
					$change_date     = 0;
				}
				
				if ( $change_to_group > 0 )
				{
					if ( $groups[ $change_to_group ] != 1 )
					{
						$change_to_group = $INFO['member_group'];
					}
					
					if ( $row['sub_id'] != "" and $row['subtrans_member_id'] != "" )
					{
						$DB->do_update( "members", array( 'mgroup'  => $change_to_group,
													      'sub_end' => $change_date,
													    ), "id={$row['subtrans_member_id']}" );
					}
					
					if ( USE_MODULES == 1 )
					{
						$this->modules->register_class(&$this);
						$this->modules->on_group_change($row['subtrans_member_id'], $change_to_group);
					}
				}
				else
				{
					if ( $row['sub_id'] != "" and $row['subtrans_member_id'] != "" )
					{
						$DB->do_update( "members", array( 'sub_end' => $change_date ), "id={$row['subtrans_member_id']}" );
					}
				}
				
				$DB->query("UPDATE ibf_subscription_trans SET subtrans_state='{$ibforums->input['updateto']}' WHERE subtrans_id={$row['subtrans_id']}");
				
				$name = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $row['sub_run_module'] );
    	
				if ( $name != "" )
				{
					if ( @file_exists( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' ) )
					{
						require_once( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' );
						
						$this->customsubs = new customsubs();
						
						if ( $ibforums->input['updateto'] == 'paid' )
						{
							//---------------------------
							// New is paid and current is paid?
							//---------------------------
							
							if ( $row['subtrans_state'] != 'paid' )
							{
								$this->customsubs->subs_paid($row, $row, $row['subtrans_id']);
							}
						}
						else
						{
							//---------------------------
							// Changing from paid to not paid?
							//---------------------------
							
							if ( $row['subtrans_state'] == 'paid' )
							{
								$this->customsubs->subs_failed($row, $row, $row['subtrans_id']);
							}
						}
					}
				}
			}
			
			$ibforums->admin->save_log("$id_count subscription transactions updated to {$ibforums->input['updateto']}");
		
			$std->boink_it( $ibforums->base_url."&act=msubs&code=dosearch&".trim($_POST['qstring']) );
		}
		
	}
	
	
	
	
	function _unsub_members($sub_id, $type='all', $mark='expired')
	{
		global $ibforums, $DB, $std;
		
		//-------------------------------------------
		// Grab member groups
		//-------------------------------------------
		
		$groups = array();
		
		$DB->query("SELECT g_id, g_title FROM ibf_groups ORDER BY g_title");
		
		while ( $r = $DB->fetch_row() )
		{
			$groups[ $r['g_id'] ] = 1;
		}
		
		//-------------------------------------------
		// I'm a little query!
		//-------------------------------------------
		
		$qe = "";
		
		if ( $type != 'all' )
		{
			$qe = " AND s.subtrans_end_date < ".time();
		}
		
		$DB->cache_add_query( 'unsub_members', array( 'id' => $ibforums->input['id'], 'qe' => $qe), 'sql_subsm_queries' );
		$outer = $DB->cache_exec_query();
		
		while ( $row = $DB->fetch_row( $outer ) )
		{
			if ( $mark == 'paid' )
			{
				$change_date = time() + ( $row['sub_length'] * $day_to_seconds[ $row['sub_unit'] ] );
			}
			else
			{
				$change_date = 0;
			}
			
			//---------------------
			// If we're not paid, 
			// leave alone..
			//---------------------
			
			if ( $row['subtrans_state'] != 'paid' )
			{
				$row['subtrans_old_group'] = 0;
			}
			
			if ( intval($row['subtrans_old_group']) > 0 )
			{
				if ( $groups[ $row['subtrans_old_group'] ] != 1 )
				{
					$row['subtrans_old_group'] = $INFO['member_group'];
				}
				
				if ( ! $row['subtrans_member_id'] )
				{
					continue;
				}
				
				$DB->do_update( "members", array( 'mgroup'  => $row['subtrans_old_group'],
												  'sub_end' => $change_date,
												), "id={$row['subtrans_member_id']}" );
				if ( USE_MODULES == 1 )
				{
					$this->modules->register_class(&$this);
					$this->modules->on_group_change($row['subtrans_member_id'], $change_to_group);
				}
			}
			else
			{
				$DB->do_update( "members", array(  'sub_end' => $change_date ), "id={$row['subtrans_member_id']}" );
			}
			
			$DB->do_update( "subscription_trans", array( 'subtrans_state' => $mark ), "subtrans_id={$row['subtrans_id']}" );
												
			$name = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $row['sub_run_module'] );
    	
			if ( $name != "" )
			{
				if ( @file_exists( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' ) )
				{
					require_once( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' );
					
					$this->customsubs = new customsubs();
					
					$this->customsubs->subs_failed($row, $row, $row['subtrans_id']); // Your boat?
				}
			}
		}
	}
	
	
	
		
}


?>