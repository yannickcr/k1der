<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Admin Framework for IPS Services
|   > Module written by Matt Mecham
|   > Date started: 17 February 2003
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class ad_plugins {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		if ( TRIAL_VERSION )
		{
			print "This feature is disabled in the trial version.";
			exit();
		}

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------

		// Make sure we're a root admin, or else!

		if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
		{
			$ibforums->admin->error("Sorry, these functions are for the root admin group only");
		}

		switch($ibforums->input['code'])
		{
			case 'ipchat':
				$this->chat_splash();
				break;
			case 'ipchat04':
				$this->chat04_splash();
				break;
			case 'dochat04':
				$this->chat_config_save();
				break;
			case 'chatframe':
				$this->chat_frame();
				break;
			case 'chatsave':
				$this->chat_save();
				break;
			case 'dochat':
				$this->chat_config_save();
				break;
			case 'dorefreshchat':
				$this->chat_refresh_online();
				break;

			//-----------------------------------------

			case 'reg':
				$this->reg_splash();
				break;
			case 'regframe':
				$this->reg_frame();
				break;
			case 'regsave':
				$this->reg_save();
				break;
			case 'doreg':
				$this->reg_config_save();
				break;

			//-----------------------------------------

			case 'copy':
				$this->copy_splash();
				break;
			case 'copyframe':
				$this->copy_frame();
				break;
			case 'copysave':
				$this->copy_save();
				break;
			case 'docopy':
				$this->copy_config_save();
				break;

			default:
				exit();
				break;
		}

	}


	//-----------------------------------------
	// Copyright removal Splash
	//-----------------------------------------

	function copy_splash()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------

		if ( $ibforums->vars['ipb_copy_number'] )
		{
			$this->copy_config();
		}
		else
		{
			/*$frames = "<html>
		   			 <head><title>Invision Power Board: Registration Set up</title></head>
					   <frameset rows='*,100' frameborder='yes' border='1' framespacing='0'>
					   	<frame name='chat_top'   scrolling='auto' src='http://customer.invisionpower.com/ipb/copy/redirect_acp.php'>
					   	<frame name='chat_bottom'  scrolling='auto' src='{$ibforums->adskin->base_url}&act=pin&code=copyframe'>
					   </frameset>
				   </html>";*/

			print $frames;
			exit();
		}

	}

	//-----------------------------------------

	function copy_frame()
	{
		global $ibforums, $DB,  $std;

		$html = "<html>
				  <head>
				   <title>Invision Power Board Order Box</title>
				    <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$ibforums->skin_url}/acp_css.css\" />
				  </head>
				  <body marginheight='0' marginwidth='0' leftmargin='0' topmargin='0' bgcolor='#4C77B6' style='margin:0px;border:0px;border:6px outset #4C77B6'>
				   <br /><br />
				  <table cellpadding=4 cellspacing=0 border=0 align='center'>
				  <form action='{$ibforums->adskin->base_url}&act=pin&code=copysave' method='POST' target='body'>
				  <tr>
				   <td valign='middle' align='left'><b style='color:white'>Already paid for copyright removal?</b></td>
				   <td valign='middle' align='left'><input type='text' size=50 name='ipb_copy_number' value='enter your IPB copyright removal key here...' onClick=\"this.value='';\"></td>
				   <td valign='middle' align='left'><input type='submit' class='realdarkbutton' value='Continue...'></td>
				  </tr>
				  </table>
				  </form>
				  <br /><br />
				  </body>
				 </html>";

		echo $html;

		exit();

	}

	//-----------------------------------------

	function copy_save()
	{
		global $ibforums, $DB,  $std;

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );
		$settings = new ad_settings();

		$acc_number = trim($ibforums->input['ipb_copy_number']);

		if ( stristr( $acc_number, ',pass=' ) )
		{
			list( $acc_number, $pass ) = explode( ',pass=', $acc_number );

			if ( md5(strtolower($pass)) == 'b1c4780a00e7d010b0eca0b695398c02' )
			{
				$DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_copy_number'" );
				$DB->do_update( 'conf_settings', array( 'conf_value' => 1           ), "conf_key='ips_cp_purchase'" );
				$settings->setting_rebuildcache();

				$this->copy_config('new');

				exit();
			}
			else
			{
				$ibforums->admin->error("The override password was incorrect. Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.");
			}
		}

		if ( $acc_number == "" )
		{
			$ibforums->admin->error("Sorry, that is not a valid IPB Copyright key, please hit 'back' in your browser and try again.");
		}

		$response = 1;

		if ( $response == "" )
		{
			$ibforums->admin->error("There was no response back from the Invision Power Services registration server, this might be because of the following:
			               <ul>
			               <li>Your PHP version does not allow remote connections</li>
			               <li>The Invision Power Services registration server is offline</li>
			               <li>You are running this IPB on a server without an internet connection</li>
			               </ul>
			               <br />
			               Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.
			             ");
		}
		else if ( $response == '0' )
		{
			$ibforums->admin->error("The registration key you entered is not valid, this might be because of the following:
			               <ul>
			               <li>You incorrectly entered the registration key</li>
			               <li>You mistakenly used your customer center password instead of the registration key</li>
			               <li>Your registration licence is no longer valid</li>
			               </ul>
			               <br />
			               Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.
			             ");
		}
		else if ( $response == '1' )
		{
			$DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_copy_number'" );
			$DB->do_update( 'conf_settings', array( 'conf_value' => 1           ), "conf_key='ips_cp_purchase'" );

			$settings->setting_rebuildcache();
		}

		$this->copy_config('new');
	}

	//-----------------------------------------

	function copy_config($type="")
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "&nbsp;";
		$ibforums->admin->page_title  = "IPB Copyright Confirmation";

		if ( $type == "new" )
		{
			$ibforums->admin->page_detail .= "<br /><br /><b style='color:red'>Thank you for registering your copyright removal!</b>";
		}

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"    , "100%" );


		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Configuration" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "The copyright should now be removed from the bottom of the IPB pages.<br /><br />If this is not the case, please contact our after sales staff immediately."
													    )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------

	function copy_config_save()
	{
		global $ibforums, $DB,  $std;

		$new = array(
						'ipb_reg_show' => $ibforums->input['ipb_reg_show'],
						'ipb_reg_name' => $ibforums->input['ipb_reg_name'],
					);


		$ibforums->admin->rebuild_config( $new );

		$ibforums->admin->done_screen("IPB Registration Configuration Updated", "IPB Registration Configuration Updated", "act=pin&code=reg" );
	}




	//-----------------------------------------
	// Registration Splash
	//-----------------------------------------

	function reg_splash()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------

		if ( $ibforums->vars['ipb_reg_number'] )
		{
			$this->reg_config();
		}
		else
		{
			/*$frames = "<html>
		   			 <head><title>Invision Power Board: Registration Set up</title></head>
					   <frameset rows='*,95' frameborder='yes' border='1' framespacing='0'>
					   	<frame name='chat_top'   scrolling='auto' src='http://www.invisionboard.com/?whyregister'>
					   	<frame name='chat_bottom'  scrolling='auto' src='{$ibforums->adskin->base_url}&act=pin&code=regframe'>
					   </frameset>
				   </html>";*/

			print $frames;
			exit();
		}

	}

	//-----------------------------------------

	function reg_frame()
	{
		global $ibforums, $DB,  $std;

		$html = "<html>
				  <head>
				   <title>Invision Power Board Order Box</title>
				   <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$ibforums->skin_url}/acp_css.css\" />
				  </head>
				  <body marginheight='0' marginwidth='0' leftmargin='0' topmargin='0' bgcolor='#4C77B6' style='margin:0px;border:0px;border:6px outset #4C77B6'>
				  <br /><br />
				  <table cellpadding='4' cellspacing='0' border='0' align='center'>
				  <form action='{$ibforums->adskin->base_url}&act=pin&code=regsave' method='POST' target='body'>
				  <tr>
				   <td valign='middle' align='left'><b style='color:white'>Already Registered?</b></td>
				   <td valign='middle' align='left'><input type='text' size='50' name='ipb_reg_number' value='enter your IPB registration key here...' onClick=\"this.value='';\"></td>
				   <td valign='middle' align='left'><input type='submit' class='realdarkbutton' value='Continue...'></td>
				  </tr>
				  </table>
				  </form>
				  <br /><br />
				  </body>
				 </html>";

		echo $html;

		exit();

	}

	//-----------------------------------------

	function reg_save()
	{
		global $ibforums, $DB,  $std;

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );
		$settings = new ad_settings();

		$acc_number = trim($ibforums->input['ipb_reg_number']);

		if ( stristr( $acc_number, ',pass=' ) )
		{
			list( $acc_number, $pass ) = explode( ',pass=', $acc_number );

			if ( md5(strtolower($pass)) == 'b1c4780a00e7d010b0eca0b695398c02' )
			{
				$DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_reg_number'" );
				$settings->setting_rebuildcache();

				$this->reg_config('new');

				exit();
			}
			else
			{
				$ibforums->admin->error("The override password was incorrect. Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.");
			}
		}

		if ( $acc_number == "" )
		{
			$ibforums->admin->error("Sorry, that is not a valid IPB registration key, please hit 'back' in your browser and try again.");
		}

		$response = 1;

		if ( $response == "" )
		{
			$ibforums->admin->error("There was no response back from the Invision Power Services registration server, this might be because of the following:
			               <ul>
			               <li>Your PHP version does not allow remote connections</li>
			               <li>The Invision Power Services registration server is offline</li>
			               <li>You are running this IPB on a server without an internet connection</li>
			               </ul>
			               <br />
			               Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.
			             ");
		}
		else if ( $response == '0' )
		{
			$ibforums->admin->error("The registration key you entered is not valid, this might be because of the following:
			               <ul>
			               <li>You incorrectly entered the registration key</li>
			               <li>You mistakenly used your customer center password instead of the registration key</li>
			               <li>Your registration licence is no longer valid</li>
			               </ul>
			               <br />
			               Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.
			             ");
		}
		else if ( $response == '1' )
		{
			$DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_reg_number'" );
			$settings->setting_rebuildcache();
		}

		$this->reg_config('new');
	}

	//-----------------------------------------

	function reg_config($type="")
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may edit the configuration below to suit";
		$ibforums->admin->page_title  = "IPB Registration Configuration";

		if ( $type == "new" )
		{
			$ibforums->admin->page_detail .= "<br /><br /><b style='color:red'>Thank you for registering!</b>";
		}

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

		//-----------------------------------------
		// START THE FORM
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doreg' ),
											                     2 => array( 'act'   , 'pin'    ),
									                    )      );

		//-----------------------------------------
		// get group ID
		//-----------------------------------------

		$conf_group = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings_titles', 'where' => "conf_title_keyword='ipbreg'" ) );

		$DB->query("SELECT * FROM ibf_conf_settings WHERE conf_group='{$conf_group['conf_title_id']}' ORDER BY conf_position, conf_title");

		while ( $r = $DB->fetch_row() )
		{
			$conf_entry[ $r['conf_id'] ] = $r;

			if ( $r['conf_end_group'] )
			{
				$in_g = 0;
			}

			if ( $in_g )
			{
				$adsettings->in_group[] = $r['conf_id'];
			}

			if ( $r['conf_start_group'] )
			{
				$in_g = 1;
			}
		}

		$title = "Settings for group: IPB Registration";

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->html .=  "<div class='tableborder'>
							   <div class='maintitle'>
							   <table cellpadding='0' cellspacing='0' border='0' width='100%'>
							   <tr>
								<td align='left' width='70%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>$title</td>
								<td align='right' nowrap='nowrap' width='30%'>";

		$ibforums->html .= "&nbsp;&nbsp;</td>
						   </tr>
						   </table>
						   </div>
						   ";

		//-----------------------------------------
		// Loopy loo
		//-----------------------------------------

		foreach( $conf_entry as $id => $r )
		{
			$ibforums->html .= $adsettings->_setting_process_entry( $r );
		}

		$ibforums->html .= "<input type='hidden' name='settings_save' value='".implode(",",$adsettings->key_array)."' />";

		$ibforums->html .= "<div class='pformstrip' align='center'><input type='submit' value='Update Settings' class='realdarkbutton' /></div></div></form>";

		$ibforums->admin->output();

	}

	//-----------------------------------------

	function reg_config_save()
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['id'] = 'ipbreg';

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

 		$adsettings->setting_update( 1 );

		$ibforums->admin->done_screen("IPB Registration Configuration Updated", "IPB Registration Configuration Updated", "act=pin&code=reg", 'redirect' );
	}

	//-----------------------------------------
	// FORCE REFRESH ONLINE LIST
	//-----------------------------------------

	function chat_refresh_online()
	{
		global $ibforums, $DB,  $std;

		$time = time();
		$member_ids = array();
		$final = "";

		$server_url = 'http://'.str_replace( 'http://', '', $ibforums->vars['chat_server_addr'] ).'/ipc_who.pl?id='.$ibforums->vars['chat_account_no'].'&pw='.$ibforums->vars['chat_pass_md5'];

		if ( $data = @file( $server_url ) )
		{
			if ( count($data) > 0 )
			{
				$hits_left = array_shift($data);
			}

			foreach( $data as $t )
			{
				$t = strtolower(trim($t));
				$t = str_replace( '_', ' ', $t );
				$t = str_replace( '"', '&quot;', $t );

				$new[] = $t;
			}

			$name_string = implode( '","', $new );

			if ( count($new) > 0 )
			{
				$DB->query("SELECT m.id, m.name, m.mgroup FROM ibf_members m
							WHERE lower(name) IN (\"".$name_string."\") ORDER BY m.name");

				while ( $m = $DB->fetch_row() )
				{
					$g = $ibforums->cache['group_cache'][ $m['mgroup'] ];
					$member_ids[] = "<a href=\"{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?showuser={$m['id']}\">{$g['prefix']}{$m['name']}{$g['suffix']}</a>";
				}

				if ( count( $member_ids ) )
				{
					$final = implode( ",\n", $member_ids );

					$final = preg_replace( "/,\n?$/s", "", $final );
				}
			}

			$final .= '|&|'.intval(count($member_ids));

			$DB->query("UPDATE ibf_cache_store SET cs_value='".addslashes($final)."', cs_extra='{$hits_left}&{$time}' WHERE cs_key='chatstat'");
		}

		$this->chat_config();

	}

	//-----------------------------------------
	// CHAT SPLASH
	//-----------------------------------------

	function chat_splash()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------

		if ( $ibforums->vars['chat_account_no'] )
		{
			$this->chat_config();
		}
		else if ( $ibforums->vars['chat04_account_no'] )
		{
			$this->chat04_config();
		}
		else
		{
			/*$frames = "<html>
		   			 <head><title>Invision Power Board: Chat Set up</title></head>
					   <frameset rows='*,100' frameborder='yes' border='1' framespacing='0'>
					   	<frame name='chat_top'   scrolling='auto' src='http://chat.invisionsitetools.com'>
					   	<frame name='chat_bottom'  scrolling='auto' src='{$ibforums->adskin->base_url}&act=pin&code=chatframe'>
					   </frameset>
				   </html>";*/

			print $frames;
			exit();
		}

	}

	//-----------------------------------------

	function chat_frame()
	{
		global $ibforums, $DB,  $std;

		$html = "<html>
				  <head>
				   <title>Invision Power Board Order Box</title>
				   <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$ibforums->skin_url}/acp_css.css\" />
				  </head>
				   <body marginheight='0' marginwidth='0' leftmargin='0' topmargin='0' bgcolor='#4C77B6' style='margin:0px;border:0px;border:6px outset #4C77B6'>
				   <br /><br />
				  <table cellpadding=4 cellspacing=0 border=0 align='center'>
				  <form action='{$ibforums->adskin->base_url}&act=pin&code=chatsave' method='POST' target='body'>
				  <tr>
				   <td valign='middle' align='left'><b style='color:white'>Ordered IP Chat?</b></td>
				   <td valign='middle' align='left'><input type='text' size=35 name='account_no' value='enter your GROUP NAME here...' onClick=\"this.value='';\"></td>
				   <td valign='middle' align='left'><input type='submit' class='realdarkbutton' value='Continue...'></td>
				  </tr>
				  </table>
				  </form>
				  <br /><br />
				  </body>
				 </html>";

		echo $html;

		exit();

	}

	//-----------------------------------------

	function chat_save()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

		$acc_number = $ibforums->input['account_no'];

		if ( $acc_number == "" )
		{
			$ibforums->admin->error("Sorry, that is not a valid IP Chat account number");
		}

		if ( ! preg_match( "#[a-z]#", strtolower($acc_number) ) )
		{
			// is numerical  - legacy chat
			$DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='chat_account_no'" );
			$DB->do_update( 'conf_settings', array( 'conf_value' => '' )         , "conf_key='chat04_account_no'" );
			$adsettings->setting_rebuildcache();
			$this->chat_config();

		}
		else
		{
			// is alpha - new chat
			$DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='chat04_account_no'" );
			$DB->do_update( 'conf_settings', array( 'conf_value' => '' )         , "conf_key='chat_account_no'" );
			$adsettings->setting_rebuildcache();
			$this->chat04_config();
		}
	}


	//-----------------------------------------
	// LEGACY CHAT (SAVE)
	//-----------------------------------------

	function chat_config_save()
	{
		global $ibforums, $DB,  $std;

		$acc_number = intval($ibforums->input['chat_account_no']);

		$ibforums->input['id'] = 'chat';

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

 		$adsettings->setting_update( 1 );

 		if ( $ibforums->input['chat_pass_md5_new'] != "" )
 		{
 			$new['chat_pass_md5'] = md5(trim($ibforums->input['chat_pass_md5_new']));

 			$DB->do_update( 'conf_settings', array( 'conf_value' => $new['chat_pass_md5']  ), "conf_key='chat_pass_md5'" );
 			$DB->do_update( 'conf_settings', array( 'conf_value' => '' ), "conf_key='chat_pass_md5_new'" );
 			$adsettings->setting_rebuildcache();

 		}
 		else
 		{
 			if ( $ibforums->input['chat_pass_md5'] != "" )
			{
				$new['chat_pass_md5'] = md5(trim($ibforums->input['chat_pass_md5_new']));
				$DB->do_update( 'conf_settings', array( 'conf_value' => $new['chat_pass_md5'] ), "conf_key='chat_pass_md5'" );
				$DB->do_update( 'conf_settings', array( 'conf_value' => '' ), "conf_key='chat_pass_md5_new'" );
				$adsettings->setting_rebuildcache();
			}
 		}

		$ibforums->admin->done_screen("IP Chat Configurations Updated", "IP Chat Configuration", "act=pin&code=ipchat", 'redirect' );
	}


	//-----------------------------------------
	// LEGACY CHAT
	//-----------------------------------------

	function chat_config()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may edit the configuration below to suit";
		$ibforums->admin->page_title  = "Invision Power Chat Configuration";

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

		//-----------------------------------------
		// START THE FORM
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dochat' ),
											                     2 => array( 'act'   , 'pin'    ),
									                    )      );

		//-----------------------------------------
		// get group ID
		//-----------------------------------------

		$conf_group = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings_titles', 'where' => "conf_title_keyword='chat'" ) );

		$DB->query("SELECT * FROM ibf_conf_settings WHERE conf_group='{$conf_group['conf_title_id']}' ORDER BY conf_position, conf_title");

		while ( $r = $DB->fetch_row() )
		{
			$conf_entry[ $r['conf_id'] ] = $r;

			if ( $r['conf_end_group'] )
			{
				$in_g = 0;
			}

			if ( $in_g )
			{
				$adsettings->in_group[] = $r['conf_id'];
			}

			if ( $r['conf_start_group'] )
			{
				$in_g = 1;
			}
		}

		$title = "Settings for group: Invision Chat (Legacy)";

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->html .=  "<div class='tableborder'>
							   <div class='maintitle'>
							   <table cellpadding='0' cellspacing='0' border='0' width='100%'>
							   <tr>
								<td align='left' width='70%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>$title</td>
								<td align='right' nowrap='nowrap' width='30%'>";

		$ibforums->html .= "&nbsp;&nbsp;</td>
						   </tr>
						   </table>
						   </div>
						   ";

		//-----------------------------------------
		// Check DB
		//-----------------------------------------

		$row = array( 'cs_extra' => '0&0' );

		$DB->query("SELECT * FROM ibf_cache_store WHERE cs_key='chatstat'");

		if ( ! $row = $DB->fetch_row() )
		{
			$DB->do_insert( 'cache_store', array( 'cs_extra' => '0&0', 'cs_key' => 'chatstat' ) );
		}

		list( $hits, $time ) = explode( '&', $row['cs_extra'] );

		if ( $time > 0 )
		{
			if ( $hits < 1 )
			{
				$hits = 0;
			}
		}

		$expire = "";

		//-----------------------------------------
		// Hits will expire...
		//-----------------------------------------

		if ( $ibforums->vars['chat_who_save'] > 0 )
		{
			$expire = ($hits * $ibforums->vars['chat_who_save']) * 60;

			$expire = $ibforums->admin->get_date( time() + $expire, 'SHORT' );
		}

		//-----------------------------------------
		// Check server...
		//-----------------------------------------

		if ( $ibforums->vars['chat_account_no'] )
		{
			$lookup = "http://client.invisionchat.com/ipc_srv_lookup.pl?id=".$ibforums->vars['chat_account_no'];

			if ( ! $data = trim( implode( '', @file( $lookup ) ) ) )
			{
				$server_name = "<span style='color:red;font-weight:bold'>Auto-lookup failed.</span> <a href='http://www.invisionboard.com/acp/chatcheck.php?id={$ibforums->vars['chat_account_no']}'>Click here to manually check</a><br />";
			}

			if ( ! strstr( $data, "invisionchat.com" ) )
			{
				$server_name = "<span style='color:red;font-weight:bold'>Auto-lookup failed.</span> <a href='http://www.invisionboard.com/acp/chatcheck.php?id={$ibforums->vars['chat_account_no']}'>Click here to manually check</a><br />";
				$data        = '';
			}

			$ibforums->vars['chat_server_addr'] = $data;

			if ( ! $ibforums->vars['chat_server_addr'] )
			{
				$ibforums->vars['chat_server_addr'] = 'client1.invisionchat.com';
			}

			$server_name .= "Checked: ". $ibforums->admin->get_date( time(), 'SHORT' );
		}

		//-----------------------------------------
		// Loopy loo
		//-----------------------------------------

		$require_pass = 1;

		foreach( $conf_entry as $id => $r )
		{
			//-----------------------------------------
			// Server..
			//-----------------------------------------

			if ( $r['conf_key'] == 'chat_server_addr' )
			{
				$r['conf_value'] = $data;
				$r['conf_description'] = str_replace( '<!--SERVER-->', $server_name, $r['conf_description'] );
			}

			//-----------------------------------------
			// Passy
			//-----------------------------------------

			if ( $r['conf_key'] == 'chat_pass_md5' )
			{
				if ( $r['conf_value'] != '' )
				{
					$r['conf_value'] = '';
					$require_pass = 0;
				}
				else
				{
					continue;
				}

			}

			if ( $r['conf_key'] == 'chat_pass_md5_new' and ! $require_pass )
			{
				continue;
			}

			$ibforums->html .= $adsettings->_setting_process_entry( $r );
		}

		$ibforums->html .= "<input type='hidden' name='settings_save' value='".implode(",",$adsettings->key_array)."' />";

		$ibforums->html .= "<div class='pformstrip' align='center'><input type='submit' value='Update Settings' class='realdarkbutton' /></div></div></form>";

		//-----------------------------------------
		// Whos Chatting
		//-----------------------------------------

		if ( $ibforums->vars['chat_who_on'] and $ibforums->vars["chat_pass_md5"] )
		{
			$ibforums->adskin->td_header[] = array( "{none}" , "100%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Who's Chatting?" );

			$DB->query("SELECT * FROM ibf_cache_store WHERE cs_key='chatstat'");

			$chat_row = $DB->fetch_row();

			if ( strstr( $chat_row['cs_value'], $ibforums->vars['board_url'] ) )
			{
				list ($names, $count) = explode( '|&|', $chat_row['cs_value'] );

				$ibforums->html .= $ibforums->adskin->add_td_row( array( stripslashes($names) ) );
			}
			else
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "No users currently chatting" ) );
			}

			$ibforums->html .= $ibforums->adskin->add_td_basic( "<a href='{$ibforums->base_url}&act=pin&code=dorefreshchat'>Force Refresh Now</a>", 'right', 'catrow2' );

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		$ibforums->admin->output();

	}


	//-----------------------------------------
	// NEW CHAT (SAVE)
	//-----------------------------------------

	function chat04_config_save()
	{
		global $ibforums, $DB,  $std;

		$acc_number = intval($ibforums->input['chat04_account_no']);

		$ibforums->input['id'] = 'chat';

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

 		$adsettings->setting_update( 1 );

		$ibforums->admin->done_screen("IP Chat Configurations Updated", "IP Chat Configuration", "act=pin&code=ipchat04", 'redirect' );
	}


	//-----------------------------------------
	// NEW CHAT
	//-----------------------------------------

	function chat04_config()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "You may edit the configuration below to suit";
		$ibforums->admin->page_title  = "Invision Power Chat Configuration";

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_settings.php' );

		$adsettings = new ad_settings();

		//-----------------------------------------
		// START THE FORM
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dochat04' ),
											                     2 => array( 'act'   , 'pin'    ),
									                    )      );

		//-----------------------------------------
		// get group ID
		//-----------------------------------------

		$conf_group = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings_titles', 'where' => "conf_title_keyword='chat04'" ) );

		$DB->query("SELECT * FROM ibf_conf_settings WHERE conf_group='{$conf_group['conf_title_id']}' ORDER BY conf_position, conf_title");

		while ( $r = $DB->fetch_row() )
		{
			$conf_entry[ $r['conf_id'] ] = $r;

			if ( $r['conf_end_group'] )
			{
				$in_g = 0;
			}

			if ( $in_g )
			{
				$adsettings->in_group[] = $r['conf_id'];
			}

			if ( $r['conf_start_group'] )
			{
				$in_g = 1;
			}
		}

		$title = "Settings for group: Invision Chat";

		//-----------------------------------------
		// start table
		//-----------------------------------------

		$ibforums->html .=  "<div class='tableborder'>
							   <div class='maintitle'>
							   <table cellpadding='0' cellspacing='0' border='0' width='100%'>
							   <tr>
								<td align='left' width='70%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>$title</td>
								<td align='right' nowrap='nowrap' width='30%'>";

		$ibforums->html .= "&nbsp;&nbsp;</td>
						   </tr>
						   </table>
						   </div>
						   ";

		//-----------------------------------------
		// Check DB
		//-----------------------------------------

		$row = array( 'cs_extra' => '0&0' );

		$DB->query("SELECT * FROM ibf_cache_store WHERE cs_key='chatstat'");

		if ( ! $row = $DB->fetch_row() )
		{
			$DB->do_insert( 'cache_store', array( 'cs_extra' => '0&0', 'cs_key' => 'chatstat' ) );
		}

		list( $hits, $time ) = explode( '&', $row['cs_extra'] );

		if ( $time > 0 )
		{
			if ( $hits < 1 )
			{
				$hits = 0;
			}
		}

		$expire = "";

		//-----------------------------------------
		// Hits will expire...
		//-----------------------------------------

		if ( $ibforums->vars['chat04_who_save'] > 0 )
		{
			$expire = ($hits * $ibforums->vars['chat04_who_save']) * 60;

			$expire = $ibforums->admin->get_date( time() + $expire, 'SHORT' );
		}

		//-----------------------------------------
		// Loopy loo
		//-----------------------------------------

		$require_pass = 1;

		foreach( $conf_entry as $id => $r )
		{
			$ibforums->html .= $adsettings->_setting_process_entry( $r );
		}

		$ibforums->html .= "<input type='hidden' name='settings_save' value='".implode(",",$adsettings->key_array)."' />";

		$ibforums->html .= "<div class='pformstrip' align='center'><input type='submit' value='Update Settings' class='realdarkbutton' /></div></div></form>";

		//-----------------------------------------
		// Whos Chatting
		//-----------------------------------------

		if ( $ibforums->vars['chat04_who_on'] and $ibforums->vars["chat04_pass_md5"] )
		{
			$ibforums->adskin->td_header[] = array( "{none}" , "100%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Who's Chatting?" );

			$DB->query("SELECT * FROM ibf_cache_store WHERE cs_key='chatstat'");

			$chat_row = $DB->fetch_row();

			if ( strstr( $chat_row['cs_value'], $ibforums->vars['board_url'] ) )
			{
				list ($names, $count) = explode( '|&|', $chat_row['cs_value'] );

				$ibforums->html .= $ibforums->adskin->add_td_row( array( stripslashes($names) ) );
			}
			else
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "No users currently chatting" ) );
			}

			$ibforums->html .= $ibforums->adskin->add_td_basic( "<a href='{$ibforums->base_url}&act=pin&code=dorefreshchat04'>Force Refresh Now</a>", 'right', 'catrow2' );

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		$ibforums->admin->output();

	}



	//-----------------------------------------
	//
	// Save config. Does the hard work, so you don't have to.
	//
	//-----------------------------------------

	function save_config( $new )
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;


	}

	//-----------------------------------------
	//
	// Common header: Saves writing the same stuff out over and over
	//
	//-----------------------------------------

	function common_header( $formcode = "", $section = "", $extra = "" )
	{
		global $ibforums, $DB,  $std;

		$extra = $extra ? $extra."<br>" : $extra;

		$ibforums->admin->page_detail = $extra . "Please check the data you are entering before submitting the changes";
		$ibforums->admin->page_title  = "Plug In Configuration [ $section ]";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $formcode ),
												  2 => array( 'act'   , 'pin'     ),
									     )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Settings" );

	}

	//-----------------------------------------
	//
	// Common footer: Saves writing the same stuff out over and over
	//
	//-----------------------------------------

	function common_footer( $button="Submit Changes" )
	{
		global $ibforums, $DB,  $std;

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}
}


?>