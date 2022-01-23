<?php
/*
+--------------------------------------------------------------------------
|   Invision Power Board v1.2 Module File
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|   > Subscription Module
|   > Module written by Matt Mecham
|   > Date started: 20th August 2003
|
+--------------------------------------------------------------------------
*/


class module extends module_loader
{

	//=====================================
	// Define vars if required
	//=====================================
	
	var $class      = "";
	var $module     = "";
	var $html       = "";
	var $ucp_html   = "";
	var $member     = "";
	var $nav        = "";
	var $page_title = "";
	var $gateway    = "";
	var $method     = "";
	var $method_name = "";
	var $day_to_seconds = array( 'd' => 86400,
								 'w' => 604800,
								 'm' => 2592000,
								 'y' => 31536000,
							   );
							   
	var $all_currency  = array();
	var $def_currency  = array();
	var $cho_currency  = array();
	var $is_from_ucp   = 1;
	
	//=====================================
	// Constructer, called and run by IPB
	//=====================================
	
	function module()
	{
		global $ibforums, $DB, $std, $print;
		
		if ( $ibforums->input['nocp'] )
		{
			$this->is_from_ucp = 0;
		}
		
		//--------------------------------------------
    	// Require the HTML and language modules
    	//--------------------------------------------
    	
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_ucp'             , $ibforums->lang_id );
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_subscriptions'   , $ibforums->lang_id );
		
		$this->html     = $std->load_template('skin_subscriptions');
    	$this->ucp_html = $std->load_template('skin_ucp');
    	
    	//--------------------------------------------
		// Load extra db cache file
		//--------------------------------------------
		
		$DB->load_cache_file( ROOT_PATH.'sources/sql/'.SQL_DRIVER.'_subsm_queries.php', 'sql_subsm_queries' );
		
    	//--------------------------------------------
    	// Get currencies... mmmm currency buns :D
    	//--------------------------------------------
    	
    	$DB->query("SELECT * FROM ibf_subscription_currency");
    	
    	while ( $c = $DB->fetch_row() )
    	{
    		$this->all_currency[ $c['subcurrency_code'] ] = $c;
    		
    		if ( $c['subcurrency_default'] )
    		{
    			$this->def_currency = $c;
    		}
    	}
		
		if ( $ibforums->input['currency'] )
		{
			if ( is_array($this->all_currency[  $ibforums->input['currency'] ]) )
			{
				$this->cho_currency = $this->all_currency[  $ibforums->input['currency'] ];
			}
			else
			{
				$this->cho_currency = $this->def_currency;
			}
		}
		else
		{
			$this->cho_currency = $this->def_currency;
		}
		
		//=====================================
		// Set up structure
		//=====================================
		
		switch( $ibforums->input['CODE'] )
		{
			case 'paymentmethod':
				$this->_load_menu();
				$this->do_payment_method();
				break;
				
			case 'paymentscreen':
				$this->_load_menu();
				$this->do_payment_screen();
				break;
				
			case 'incoming':
				$this->do_validate_payment();
				break;
				
			case 'custom':
				$this->do_custom();
				break;
				
			case 'cancelfromreg':
				$this->cancel_from_reg();
				break;
				
			default:
				$this->_load_menu();
				$this->do_index();
				break;
		}
		
		if ( $this->is_from_ucp )
		{
			$fj = $std->build_forum_jump();
			
			$this->output .= $this->ucp_html->CP_end();
			
			$this->output .= $this->ucp_html->forum_jump($fj, $links);
		}
		else
		{
			$this->output .= $this->html->sub_no_cp_end();
		}
		
		//--------------------------------------
		// Any special message?
		//--------------------------------------
		
		if ( $ibforums->input['msgtype'] == 'fromreg' )
		{
			$msg = $this->html->sub_msg_fromreg();
		}
		else if ( $ibforums->input['msgtype'] == 'force' )
		{
			$msg = $this->html->sub_msg_force();
		}
		else if ( $ibforums->input['msgtype'] == 'general' )
		{
			$msg = $this->html->sub_msg_general();
		}
		else
		{
			$msg = "";
		}
		
		if ( $msg )
		{
			$print->to_print = str_replace( "<!--{MSG}-->", $msg, $print->to_print );
		}
		
		$this->nav[] = "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>";
		$this->nav[] = "<a href='".$ibforums->base_url."act=module&amp;module=subscription&amp;CODE=index'>".$ibforums->lang['s_page_title']."</a>";
    	
    	$print->add_output( $this->output );
        $print->do_output( array( 'TITLE' => $ibforums->lang['s_page_title'], 'JS' => 1, NAV => $this->nav ) );
		
	}
	
	//---------------------------------------------
	// Cancel purchase, remove pkg ID from members
	//---------------------------------------------
	
	function cancel_from_reg()
	{
		global $DB, $ibforums, $std, $print;
		
		$DB->do_update( 'members', array( 'subs_pkg_chosen' => 0 ), 'id='.intval($ibforums->member['id']) );
		
		$std->boink_it( $ibforums->base_url );
	}
	
	//---------------------------------------------
	// Custom handler for API specific routines
	//---------------------------------------------
	
	function do_custom()
	{
		global $DB, $ibforums, $std, $print;
		
		$type = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $ibforums->input['type'] );
		
		if ( $type == "" )
		{
			return;
		}
		
		//--------------------------------------
		// Try to get row in DB
		//--------------------------------------
		
		$DB->cache_add_query( 'mod_custom', array( 'type' => $type ), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		
		$this->method = $DB->fetch_row();
		
		if ( ! $this->method['submethod_id'] )
		{
			return;
		}
		
		//--------------------------------------
		// Prep and load API
		//--------------------------------------
		
		define( 'IPB_CALLED', 1 );
		
		if ( @file_exists( ROOT_PATH . 'modules/subsmanager/api_'.$this->method['submethod_name'].'.php' ) )
		{
			require_once( ROOT_PATH . 'modules/subsmanager/api_'.$this->method['submethod_name'].'.php' );
			
			$this->gateway = new gateway();
			$this->gateway->register_class(&$this);
			$this->gateway->run_custom();
		}
		
	}
	
	//---------------------------------------------
	// Do return payment screen
	//---------------------------------------------
	
	function do_validate_payment()
	{
		global $DB, $ibforums, $std, $print;
		
		$type = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $ibforums->input['type'] );
		
		if ( $type == "" )
		{
			$this->_do_log("Tried to return validate but failed: No type set");
		}
		
		//--------------------------------------
		// Try to get row in DB
		//--------------------------------------
		
		$DB->cache_add_query( 'mod_custom', array( 'type' => $type ), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		
		$this->method = $DB->fetch_row();
		
		if ( ! $this->method['submethod_id'] )
		{
			$this->_do_log("Tried to return validate but failed: No such method as '$type'");
		}
		
		//--------------------------------------
		// Prep and load API
		//--------------------------------------
		
		define( 'IPB_CALLED', 1 );
		
		if ( @file_exists( ROOT_PATH . 'modules/subsmanager/api_'.$this->method['submethod_name'].'.php' ) )
		{
			require_once( ROOT_PATH . 'modules/subsmanager/api_'.$this->method['submethod_name'].'.php' );
			
			$this->gateway = new gateway();
		}
		else
		{
			$this->_do_log("Tried to return validate but failed: No such api as '{$this->method['submethod_name']}'");
		}
		
		//---------------------------------------------
		// Pass off to API handler
		//---------------------------------------------
		
		$this->method_name = strtoupper($this->method['submethod_name']);
		
		$this->gateway->register_class(&$this);
		$this->gateway->validate_payment();
	}
	
	//---------------------------------------------
	// Show API Payment screen
	//---------------------------------------------
	
	function do_payment_screen()
	{
		global $DB, $ibforums, $std, $print;
		
		$cur_id        = intval($ibforums->input['curid']);
		$upgrade       = intval($ibforums->input['upgrade']);
		$sub_chosen    = intval($ibforums->input['sub']);
		$method_chosen = intval($ibforums->input['methodid']);
		
		if ( $sub_chosen < 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_selected' ) );
		}
		
		if ( $method_chosen < 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_nomethod_selected' ) );
		}
		
		$DB->query("SELECT * FROM ibf_subscription_methods WHERE submethod_id=$method_chosen");
		
		$method = $DB->fetch_row();
		
		$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id=$sub_chosen");
		
		$subs   = $DB->fetch_row();
		
		$DB->query("SELECT * FROM ibf_subscription_extra WHERE subextra_sub_id=$sub_chosen AND subextra_method_id=$method_chosen");
		
		$extra  = $DB->fetch_row();
		
		
		//---------------------------------------------
		// Get the gateway info
		//---------------------------------------------
		
		define( 'IPB_CALLED', 1 );
		
		if ( @file_exists( ROOT_PATH . 'modules/subsmanager/api_'.$method['submethod_name'].'.php' ) )
		{
			require_once( ROOT_PATH . 'modules/subsmanager/api_'.$method['submethod_name'].'.php' );
			
			$this->gateway = new gateway();
		}
		else
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_api' ) );
		}
		
		//---------------------------------------------
		// Make sure we don't recurr on lifetime pkgs
		//---------------------------------------------
		
		if ( $subs['sub_unit'] == 'x' )
		{
			$extra['subextra_recurring'] = 0;
		}
		
		if ( $upgrade ) 
		{
			$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_id=$cur_id");
		
			$current = $DB->fetch_row();
			
			$this->gateway->register_class(&$this);
			$this->output .= $this->gateway->show_upgrade_payment_screen($current, $subs, $method, $extra);
		}
		else
		{
			$this->gateway->register_class(&$this);
			$this->output .= $this->gateway->show_normal_payment_screen($subs, $method, $extra);
		}
	}
	
	
	
	//---------------------------------------------
	// Show Available Payment Methods
	//---------------------------------------------
	
	function do_payment_method()
	{
		global $DB, $ibforums, $std, $print;
		
		$cur_id      = intval($ibforums->input['curid']);
		$upgrade     = intval($ibforums->input['upgrade']);
		$sub_chosen  = intval($ibforums->input['sub']);
		$subs        = array();
		$upg_methods = array();
		$all_methods = array();
		
		if ( $sub_chosen < 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_selected' ) );
		}
		
		//--------------------------------------------
    	// Get all packages
		//--------------------------------------------
			
		$DB->query("SELECT * FROM ibf_subscriptions ORDER BY sub_cost");
		
		while ( $s = $DB->fetch_row() )
		{
			$subs[ $s['sub_id'] ] = $s;
		}
		
		//--------------------------------------------
    	// Get all gateways [we can upgrade with]
		//--------------------------------------------
		
		$DB->cache_add_query( 'mod_payment_method', array(), 'sql_subsm_queries' );
		$DB->cache_exec_query();
		
		while ( $m = $DB->fetch_row() )
		{
			if ( $m['submethod_active'] == 1 AND $m['subextra_can_upgrade'] == 1 )
			{
				$upg_methods[ $m['submethod_id'] ] = $m;
			}
			
			$all_methods[ $m['submethod_id'] ] = $m;
		}
		
		if ( $upgrade != 0 )
		{
			//--------------------------------------------
    		// We're upgrading!! Yay! - Get cur subs
			//--------------------------------------------
			
			$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_member_id={$ibforums->member['id']} AND subtrans_sub_id=$cur_id AND subtrans_state='paid'");
			
			if ( ! $cur_trans = $DB->fetch_row() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_curid' ) );
			}
			
			//--------------------------------------------
    		// Check stuff
			//--------------------------------------------
			
			if ( ! is_array( $subs[ $cur_id ] ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_curid' ) );
			}
			
			if ( count($upg_methods) < 1 )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'subs_no_upgrade' ) );
			}
			
			//--------------------------------------------
    		// Still here? Good - summary and show methods
			//--------------------------------------------
			
			$balance  = $subs[ $sub_chosen ]['sub_cost'] - $cur_trans['subtrans_paid'];
			
			$end_date = ( $subs[ $sub_chosen ]['sub_unit'] == 'x' or $subs[ $cur_id ]['sub_unit'] == 'x' )
					  ? $ibforums->lang['no_expire']
					  : $std->get_date( $cur_trans['subtrans_end_date'], 'JOINED' );
			
			$ibforums->lang['sc_upgrade_string'] = sprintf( $ibforums->lang['sc_upgrade_string'],
															$subs[ $cur_trans['subtrans_sub_id'] ]['sub_title'],
															$subs[ $sub_chosen ]['sub_title'],
															$end_date,
															sprintf( "%.2f", $balance * $this->cho_currency['subcurrency_exchange'] ) . ' '.$this->cho_currency['subcurrency_code']
														  );
			
			$this->output .= $this->html->sub_two_upgrade_summary();
			
			$this->output .= $this->html->sub_two_methods_top($sub_chosen, $upgrade, $cur_id, $this->cho_currency['subcurrency_code']);
			
			foreach( $upg_methods as $id => $method )
			{
				$this->output .= $this->html->sub_two_methods_row($id, $method['submethod_title'],$method['submethod_desc']);
			}
			
			$this->output .= $this->html->sub_two_methods_bottom();
			
			$this->output .= $this->html->sub_two_methods_continue_button();
		
		}
		else
		{
			//--------------------------------------------
    		// We're not upgrading!! Boo(bies)! :0
			//--------------------------------------------
			
			$ibforums->lang['sc_normal_string'] = sprintf( $ibforums->lang['sc_normal_string'],
														   $subs[ $sub_chosen ]['sub_title'],
														   sprintf( "%.2f", $subs[ $sub_chosen ]['sub_cost'] * $this->cho_currency['subcurrency_exchange'] ) . ' '.$this->cho_currency['subcurrency_code']
														  );
			
			$this->output .= $this->html->sub_two_normal_summary();
			
			$this->output .= $this->html->sub_two_methods_top($sub_chosen, $upgrade, $cur_id,  $this->cho_currency['subcurrency_code']);
			
			foreach( $all_methods as $id => $method )
			{
				$this->output .= $this->html->sub_two_methods_row($id, $method['submethod_title'],$method['submethod_desc']);
			}
			
			$this->output .= $this->html->sub_two_methods_bottom();
			
			$this->output .= $this->html->sub_two_methods_continue_button();
		}
	}
	
	//---------------------------------------------
	// Show Index (Default subs page)
	//---------------------------------------------
	
	function do_index()
	{
		global $DB, $ibforums, $std, $print;
		
		$current = array();
		$dead    = array();
		$subs    = array();
		
		//--------------------------------------------
    	// Get all packages
		//--------------------------------------------
			
		$DB->query("SELECT * FROM ibf_subscriptions ORDER BY sub_cost");
		
		while ( $s = $DB->fetch_row() )
		{
			$subs[ $s['sub_id'] ] = $s;
		}
		
		//--------------------------------------------
    	// Get all transactions with our memberid
		//--------------------------------------------
		
		$DB->query("SELECT * FROM ibf_subscription_trans WHERE subtrans_member_id={$ibforums->member['id']}");
		
		while ( $r = $DB->fetch_row() )
		{
			if ( $r['subtrans_state'] == 'expired' OR $r['subtrans_state'] == 'dead' OR $r['subtrans_state'] == 'failed' )
			{
				$dead[ $r['subtrans_id'] ] = $r;
			}
			else
			{
				$current[ $r['subtrans_id'] ] = $r;
			}
		}
		
		//--------------------------------------------
    	// Show dead / expired subs
		//--------------------------------------------
			
		if ( count($dead) > 0 )
		{
			$this->output .= $this->html->sub_choose_dead_top();
			
			foreach( $dead as $did => $didnt )
			{
				$end_date = ($subs[ $didnt['subtrans_sub_id'] ]['sub_unit'] == 'x') ? $ibforums->lang['no_expire'] : $std->get_date($cdata['subtrans_end_date'], 'JOINED');
				
				$this->output .= $this->html->sub_choose_dead_row( $did,
																   $subs[ $didnt['subtrans_sub_id'] ]['sub_title'],
																   $std->get_date($didnt['subtrans_start_date'], 'JOINED'),
																   $end_date,
																   sprintf( "%.2f", $didnt['subtrans_paid'] * $this->cho_currency['subcurrency_exchange'] ),
																   $ibforums->lang['pay_'.strtolower($didnt['subtrans_state'])]
																 );
			}
			
			$this->output .= $this->html->sub_choose_dead_bottom();
		}
		
		//--------------------------------------------
    	// We have current subscriptions?
		//--------------------------------------------
		
		$max_cost = 0;
		$max_id   = 0;
		$max_data = 0;
			
		if ( count($current) > 0 )
		{
			$this->output .= $this->html->sub_choose_current_top();
			
			foreach( $current as $cid => $cdata )
			{
				$end_date = ($subs[ $cdata['subtrans_sub_id'] ]['sub_unit'] == 'x') ? $ibforums->lang['no_expire'] : $std->get_date($cdata['subtrans_end_date'], 'JOINED');
				
				$this->output .= $this->html->sub_choose_current_row( $cid,
																	  $subs[ $cdata['subtrans_sub_id'] ]['sub_title'],
																	  $std->get_date($cdata['subtrans_start_date'], 'JOINED'),
																	  $end_date,
																	  sprintf( "%.2f", $cdata['subtrans_paid'] * $this->cho_currency['subcurrency_exchange'] ),
																	  $ibforums->lang['pay_'.strtolower($cdata['subtrans_state'])]
																	 );
																	 
				if ( $subs[ $cdata['subtrans_sub_id'] ]['sub_cost'] > $max_cost )
				{
					$max_cost = $subs[ $cdata['subtrans_sub_id'] ]['sub_cost'];
					$max_id   = $cdata['subtrans_sub_id'];
					$max_data = $cdata;
					$max_state = $cdata['subtrans_state'];
					$max_end   = $subs[ $cdata['subtrans_sub_id'] ]['sub_unit'];
				}
			}
			
			$this->output .= $this->html->sub_choose_current_bottom();
			
			//--------------------------------------------
    		// Do we have any upgradeable packages?
    		// First, check the gateways
    		// CHECK: Are we pending? If so - don't allow
    		// any upgrades until it's paid
			//--------------------------------------------
			
			$can_upgrade = 0;
			
			if ( $max_state != 'pending' )
			{
				$DB->cache_add_query( 'mod_do_index', array(), 'sql_subsm_queries' );
				$DB->cache_exec_query();
							
				while ( $m= $DB->fetch_row() )
				{
					if ( $m['submethod_active'] == 1 AND $m['subextra_can_upgrade'] == 1 )
					{
						$can_upgrade = 1;
						break;
					}
				}
			}
			
			if ( $can_upgrade == 1 )
			{
				//--------------------------------------------
				// So far so good, now lets check if we can
				// have anywhere to go (ie. we're not on the top tier)
				//--------------------------------------------
				
				$DB->query("SELECT * FROM ibf_subscriptions WHERE sub_cost > $max_cost ORDER BY sub_cost");
				
				if ( $DB->get_num_rows() )
				{
					// We have some!
					
					$this->output .= $this->html->sub_choose_upgrade_top($max_id, $this->cho_currency['subcurrency_code']);
					
					while ( $row = $DB->fetch_row() )
					{
						$end_date = ($row['sub_unit'] == 'x' or $max_end == 'x') ? $ibforums->lang['no_expire'] : $std->get_date( $max_data['subtrans_end_date'], 'JOINED' );
						
						$this->output .= $this->html->sub_choose_upgrade_row( $row['sub_id'],
																			  $row['sub_title'],
																			  $row['sub_desc'],
																			  sprintf( "%.2f", ($row['sub_cost'] - $max_cost)  * $this->cho_currency['subcurrency_exchange'] ),
																			  $end_date
																			);
					}
					
					$this->output .= $this->html->sub_choose_upgrade_bottom();
				
				}
				else
				{
					// We don't!
				
				}
			}
		}
		else
		{
			//--------------------------------------------
    		// Show new subs
			//--------------------------------------------
			
			$this->output .= $this->html->sub_choose_new_top($this->cho_currency['subcurrency_code']);
			
			foreach( $subs as $id => $row )
			{
				$duration = $row['sub_length'];
			
				if ( $duration > 1 )
				{
					$duration .= ' '.$ibforums->lang[ 'timep_'.$row['sub_unit'] ];
				}
				else
				{
					$duration .= ' '.$ibforums->lang[ 'time_'.$row['sub_unit'] ];
				}
				
				$end_date = ($row['sub_unit'] == 'x') ? $ibforums->lang['no_expire'] : $duration;
				
				$this->output .= $this->html->sub_choose_new_row( $row['sub_id'],
																  $row['sub_title'],
																  $row['sub_desc'],
																  sprintf( "%.2f", $row['sub_cost']  * $this->cho_currency['subcurrency_exchange'] ),
																  $end_date
																);
			}
			
			$this->output .= $this->html->sub_choose_new_bottom();
		}
		
		$this->output .= $this->html->sub_currency_change_form( $this->_make_currency_dropdown(), $ibforums->base_url.'act=module&amp;module=subscription' );
		
		$this->output .= $this->html->sub_page_bottom();
	}
	
	//---------------------------------------------
	// Load Menu
	//---------------------------------------------
	
	function _load_menu()
	{
		global $DB, $ibforums, $std, $print;
		
		if ( ! $this->is_from_ucp )
		{
			$menu_html = $this->html->sub_no_cp_start();
			$print->add_output( $menu_html );
			return;
		}
		
    	//--------------------------------------------
    	// Check viewing permissions, etc
		//--------------------------------------------
		
		if ( empty($ibforums->member['id']) or $ibforums->member['id'] == "" or $ibforums->member['id'] == 0 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}
		
		// Get more member info..
    	
    	$DB->cache_add_query( 'generic_get_all_member', array( 'mid' => $ibforums->member['id'] ) );
		$DB->cache_exec_query();
    			   
    	$this->member = $DB->fetch_row();
		
		//--------------------------------------------
    	// Print the top button menu
    	//--------------------------------------------
    	
    	$menu_html = $this->ucp_html->Menu_bar($ibforums->base_url);
    	
    	//--------------------------------------------
    	// If no messenger, remove the links!
    	//--------------------------------------------
    	
    	if ( $ibforums->member['g_use_pm'] )
        {
        	//--------------------------------------------
    		// Print folder links
    		//--------------------------------------------
    		
    		$folder_links = "";
    		
			if (empty($this->member['vdirs']))
			{
				$this->member['vdirs'] = "in:Inbox|sent:Sent Items";
			}
			
			foreach( explode( "|", $this->member['vdirs'] ) as $dir )
			{
				list ($id  , $data)  = explode( ":", $dir );
				list ($real, $count) = explode( ";", $data );
				
				if ( ! $id )
				{
					continue;
				}
				
				if ( $count )
				{
					$real .= " ({$count})";
				}
				
				$folder_links .= $this->ucp_html->menu_bar_msg_folder_link($id, $real);
			}
			
			if ( $folder_links != "" )
			{
				$menu_html = str_replace( "<!--IBF.FOLDER_LINKS-->", $folder_links, $menu_html );
			}
        }
		
		//--------------------------------------------
    	// Using Sub Manager?
    	//--------------------------------------------
    	
		if ( @is_dir( ROOT_PATH.'/modules/subsmanager' ) )
		{
			$url  = $ibforums->base_url."act=module&amp;module=subscription&amp;CODE=index";
			$name = $ibforums->lang['new_sub_link'];
			
			$menu_html = str_replace( "<!--IBF.OPTION_LINKS-->", $this->ucp_html->menu_bar_new_link( $url, $name ), $menu_html );
		}
		
    	$print->add_output( $menu_html );
    }
    
    
    //---------------------------------------------
	// Make currency drop down box baby
	//---------------------------------------------
	
	function _make_currency_dropdown()
	{
		global $DB, $ibforums, $std;
		
		$curr_box = $this->html->sub_currency_change_top();
		
		$DB->query("SELECT * FROM ibf_subscription_currency");
		
		while ( $c = $DB->fetch_row() )
		{
			$default = "";
			
			if ( $ibforums->input['currency'] )
			{
				if ( $ibforums->input['currency'] == $c['subcurrency_code'] )
				{
					$default = " selected='selected'";
				}
			}
			else
			{
				if ( $c['subcurrency_default'] )
				{
					$default = " selected='selected'";
				}
			}
			
			$curr_box .= $this->html->sub_currency_change_row( $c['subcurrency_code'], $c['subcurrency_desc'], $default );
		}
		
		$curr_box .= $this->html->sub_currency_change_bottom();
		
		return $curr_box;
	}
	
    //---------------------------------------------
	// Captains log: Unix date: 1002343439
	//---------------------------------------------
	
	function _do_log($msg, $dont_die_for_me_argentina=0)
	{
		global $DB, $ibforums, $std, $print;
		
		$extra = "";
			
		foreach( $_POST as $k => $v )
		{
			if ( $k == 'ccnum' )
			{
				$v = 'xxxx xxxx xxxx xxxx';
			}
			else if ( $k == 'ccid' )
			{
				$v = 'xxx';
			}
			
			$extra .= "\n$k  =  $v;";
		}
		
		if ( is_array( $_GET ) )
		{
			foreach( $_GET as $k => $v )
			{
				if ( $k == 'ccnum' )
				{
					$v = 'xxxx xxxx xxxx xxxx';
				}
				else if ( $k == 'ccid' )
				{
					$v = 'xxx';
				}
				
				$extra .= "\n$k  =  $v;";
			}
		}
		
		$insert = $DB->compile_db_insert_string( array(
														'sublog_date'      => time(),
														'sublog_data'      => $msg,
														'sublog_ipaddress' => $ibforums->input['IP_ADDRESS'],
														'sublog_postdata'  => $extra,
											   )      );
											   
		$DB->query("INSERT INTO ibf_subscription_logs ({$insert['FIELD_NAMES']}) VALUES({$insert['FIELD_VALUES']})");
		
		if ( $this->return_not_die )
		{
			$this->_load_menu();
			$this->do_index();
			
			$fj = $std->build_forum_jump();
		
			$this->output .= $this->ucp_html->CP_end();
			
			$this->output .= $this->ucp_html->forum_jump($fj, $links);
			
			$this->nav[] = "<a href='".$ibforums->base_url."act=UserCP&amp;CODE=00'>".$ibforums->lang['t_title']."</a>";
			$this->nav[] = "<a href='".$ibforums->base_url."act=module&amp;module=subscription&amp;CODE=index'>".$ibforums->lang['s_page_title']."</a>";
			
			$print->add_output("$this->output");
			$print->do_output( array( 'TITLE' => $ibforums->lang['s_page_title'], 'JS' => 1, NAV => $this->nav ) );
		}
		else
		{
			if ( $dont_die_for_me_argentina != 1 )
			{
				exit();
			}
		}
	}
    
    
    
    //---------------------------------------------
	// PAID MEMBER UPGRADE YEAH BABY
	//---------------------------------------------
    
    function _do_paid_member($new_sub, $member, $cur_trx_id="")
    {
    	global $DB, $std, $ibforums;
    	
    	define( 'IPB_CALLED', 1 );
    	
    	if ( $new_sub['sub_unit'] == 'x' )
    	{
    		$end_date = 9999999999;
    	}
    	else
    	{
    		$end_date = 'sub_end'; //time() + ( $new_sub['sub_length'] * $this->day_to_seconds[ $new_sub['sub_unit'] ] );
    	}
    	
    	$query = "UPDATE ibf_members SET sub_end=$end_date";
    	
    	if ( $new_sub['sub_new_group'] )
    	{
    		$query .= ", mgroup={$new_sub['sub_new_group']}";
    	}
    	
    	$query .= " WHERE id={$member['id']}";
    	
    	$DB->query($query);
    	
    	//-----------------------------------
    	// Running Custom code?
    	//-----------------------------------
    	
    	$name = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $new_sub['sub_run_module'] );
    	
    	if ( $name != "" )
    	{
			if ( @file_exists( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' ) )
			{
				require_once( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' );
				
				$this->customsubs = new customsubs();
				
				$this->customsubs->subs_paid($new_sub, $member, $cur_trx_id);
			}
		}
		
		//-----------------------------------
		// Running IPB custom code?
		//-----------------------------------
		
		if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";
			
			$this->modules = new ipb_member_sync();
			
			$this->modules->register_class(&$this);
			$this->modules->on_group_change($member['id'], $new_sub['sub_new_group']);
		}
    }
    
    //---------------------------------------------
	// FAILED MEMBER UPGRADE YEAH BABY
	//---------------------------------------------
    
    function _do_failed_member($new_sub, $member, $cur_trx_id="")
    {
    	global $DB, $std, $ibforums;
    	
    	define( 'IPB_CALLED', 1 );
    	
    	$query = "UPDATE ibf_members SET sub_end=0";
    	
    	if ( $cur_trx_id )
    	{
    		$DB->cache_add_query( 'mod_failed_member', array( 'cur_trx_id' => $cur_trx_id ), 'sql_subsm_queries' );
			$DB->cache_exec_query();
    		
    		$r = $DB->fetch_row();
    		
    		$mgroup = $r['g_id'] ? $r['g_id'] : $ibforums->vars['member_group'];
    		
    		$query .= ", mgroup={$mgroup}";
    	}
    	
    	$query .= " WHERE id={$member['id']}";
    	
    	$DB->query($query);
    	
    	//-----------------------------------
    	// Running Custom code?
    	//-----------------------------------
    	
    	$name = preg_replace( "/[^a-zA-Z0-9\-\_]/", "" , $new_sub['sub_run_module'] );
    	
    	if ( $name != "" )
    	{
			if ( @file_exists( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' ) )
			{
				require_once( ROOT_PATH . 'modules/subsmanager/custom/cus_'.$name.'.php' );
				
				$this->customsubs = new customsubs();
				
				$this->customsubs->subs_failed($new_sub, $member, $cur_trx_id);
			}
		}
		
		//-----------------------------------
		// Running IPB custom code?
		//-----------------------------------
		
		if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";
			
			$this->modules = new ipb_member_sync();
			
			$this->modules->register_class(&$this);
			$this->modules->on_group_change($member['id'], $mgroup);
		}
    }
	
	
}


?>