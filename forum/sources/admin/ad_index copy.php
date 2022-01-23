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
|   > Admin "welcome" screen functions
|   > Module written by Matt Mecham
|   > Date started: 1st march 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class ad_index {

	var $mysql_version = "";

	function auto_run()
	{
		global $DB, $std, $ibforums;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------
		// continue...
		//-----------------------------------------

		$ibforums->admin->page_title  = "Welcome to the Invision Power Board Administration CP";
		$ibforums->admin->page_detail = "Clicking on one of the links in the left menu pane will show you the relevant options for that administration category.<br />Each option will contain further information on configuration, etc.";


		//-----------------------------------------
		// Get MySQL & PHP Version
		//-----------------------------------------

		$DB->sql_get_version();

   		$phpv = phpversion();

   		$phpmethod = @php_sapi_name();

   		//-----------------------------------------
   		// Upgrade history?
   		//-----------------------------------------

   		$upgrade_history = array();
   		$latest_version  = array();

   		$DB->simple_construct( array( 'select' => '*', 'from' => 'upgrade_history', 'order' => 'upgrade_version_id DESC' ) );
   		$DB->simple_exec();

   		while( $r = $DB->fetch_row() )
   		{
   			if ( $r['upgrade_version_id'] > $latest_version['upgrade_version_id'] )
   			{
   				$latest_version = $r;
   			}

   			$upgrade_history[] = $r;
   		}

		//-----------------------------------------
		// Got reg code?
		//-----------------------------------------

		$reg_html = "";
		$reg_end  = "";

		//-----------------------------------------
		// Got real version number?
		//-----------------------------------------

		if ( $ibforums->version == 'v2.0.3 ' )
		{
			$ibforums->version = 'v'.$latest_version['upgrade_version_human'];
		}

		if ( $ibforums->acpversion == '20013' )
		{
			$ibforums->acpversion = $latest_version['upgrade_version_id'];
		}

		$version_info = "<a href='http://www.invisionboard.com/download/' target='_blank'><img border='0' src='http://www.invisionboard.com/download/versioncheck/?v={$ibforums->acpversion}&l=".urlencode($ibforums->vars['ipb_reg_number'])."' vspace='10'></a>
		<br /><b><a href='http://www.php.net' target='_blank'>PHP</a> VERSION:</b> $phpv ($phpmethod), <b>SQL:</b> (".strtoupper(SQL_DRIVER).") ".$DB->true_version."
		<br />IPB Version {$ibforums->version} (ID: {$ibforums->acpversion})";

		if ( $ibforums->vars['ipb_reg_number'] )
		{
			list( $a, $b, $c, $d, $e ) = explode( '-', $ibforums->vars['ipb_reg_number'] );

			if ( strlen($e) > 9 )
			{
				$reg_end = "Licensed until: <span style='color:green'>". $ibforums->admin->get_date( $e, 'SHORT' )."</span>";
			}
			else
			{
				$reg_end = "Licensed for life";
			}

			$reg_html = "<div style='border:1px dotted #555;padding:6px;background-color:#EEF2F7;'>
							<b style='font-size:12px;color:#336699'>Licensed Invision Power Board</b>
							<br />Thank you for purchasing Invision Power Board!
							<br /><br />Please visit your <a href='http://customer.invisionpower.com' target='_blank'>client area</a> for news, updates and support.
							<br />$reg_end
						</div>";
		}
		else
		{
			$reg_html = "<div style='border:1px dotted #555;padding:6px;background-color:#EEF2F7;'>
							<b style='font-size:12px;color:#AA0000'>Unlicensed Invision Power Board</b>
							<br />This copy of Invision Power Board is unlicensed.
							<br /><br />Why purchase? <a href='http://www.invisionboard.com/?whyregister' target='_blank'>click here</a> to find out!
						</div>";
		}

		//-----------------------------------------
		// Notepad
		//-----------------------------------------

		if ( $ibforums->input['save'] == 1 )
		{
			$DB->do_update( 'cache_store', array( 'cs_value' => $std->txt_stripslashes($_POST['notes']) ), "cs_key='adminnotes'" );
		}

		$text = "You can use this section to keep notes for yourself and other admins, etc.";

		$DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='adminnotes'" ) );
		$DB->simple_exec();

		if ( ! $notes = $DB->fetch_row() )
		{
			$DB->do_insert( 'cache_store', array( 'cs_key' => 'adminnotes', 'cs_value' => $text ) );

			$notes = array( 'cs_key' => 'adminnotes', 'cs_value' => $text );
		}

		$ad_notes = "<form action='{$ibforums->base_url}&act=index&save=1' method='post'>
					 <textarea name='notes' style='background-color:#F9FFA2;border:1px solid #CCC;width:95%;font-family:verdana;font-size:10px' rows='7' cols='25'>".stripslashes($notes['cs_value'])."</textarea>
				     <div align='center'><input type='submit' value='Save Admin Notes' style='background-color:#F9FFA2;border:1px solid #999;;font-family:verdana;font-size:10px' /></div>
				     </form>";

		//-----------------------------------------
		// Printy-poos
		//-----------------------------------------

		$ibforums->html .= "<table width='100%' border='0' cellpadding='0' cellspacing='0'>
						 <tr>
						  <td width='49%' valign='middle' align='center' style='padding:6px;background-color:#FAFFAF;'>{$ad_notes}</td>
						  <td style='width:10px'>&nbsp;</td>
						  <td width='49%' valign='top' align='left'>{$version_info}<br /><br />{$reg_html}</td>
						 </tr>
						 </table><br />\n";

		//-----------------------------------------
		// Make sure the uploads path is correct
		//-----------------------------------------

		$uploads_size = 0;

		if ($dh = opendir( $ibforums->vars['upload_dir'] ))
		{
			while ( $file = readdir( $dh ) )
			{
				if ( !preg_match( "/^..?$|^index/i", $file ) )
				{
					$uploads_size += @filesize( $ibforums->vars['upload_dir'] . "/" . $file );
				}
			}
			closedir( $dh );
		}

		//-----------------------------------------
		// This piece of code from Jesse's (jesse@jess.on.ca) contribution
		// to the PHP manual @ php.net posted without license
		//-----------------------------------------

		if ($uploads_size >= 1048576)
		{
			$uploads_size = round($uploads_size / 1048576 * 100 ) / 100 . " mb";
		}
		else if ($uploads_size >= 1024)
		{
			$uploads_size = round($uploads_size / 1024 * 100 ) / 100 . " k";
		}
		else
		{
			$uploads_size = $uploads_size . " bytes";
		}

		//-----------------------------------------
		// Trashed skin?
		//-----------------------------------------

		$skinmsg = $DB->simple_exec_query( array( "select" => '*', 'from' =>  "cache_store", "where" => "cs_key='skinpanic'" ) );

		if ( $skinmsg['cs_value'] == 'rebuildemergency' )
		{
			$ibforums->html .= "<div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
								<span style='font-size:20px;font-weight:bold'>Warning: A Skin error occured</span>
								<br /><br />
								Either you or one of your members encountered a skin error. The following took place
								automatically:
								<ul>
								 <li>They were asked to clear their skin settings</li>
								 <li>They were asked to click a link to attempt access in the ACP</li>
								 <li>The ACP picked up the skin error and rebuilt the skin ID cache, the default skin and it may have turned on safe mode skins</li>
								</ul>
								<b>What to do now</b>
								<ul>
								 <li>Firstly, if you don't wish to use safe mode skins, check the CHMOD value of the 'skin_cache' directory to make sure IPB can write into that directory</li>
								 <li>If the permissions are correct, check your 'System Settings -&gt; General Configuration' settings to check the value of 'Safe Mode Skins' - disable if not required</li>
								 <li>As a precaution, rebuild all your skins by following the link below</li>
								</ul>
								<b>&gt;&gt; <a href='{$ibforums->base_url}&act=sets&code=rebuildalltemplates&removewarning=1'>REBUILD ALL SKIN CACHES & REMOVE THIS WARNING</a> &lt;&lt;</b>
								</div><br /><br />";
		}

		if ( $skinmsg['cs_value'] == 'rebuildupgrade' )
		{
			$ibforums->html .= "<div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
								<span style='font-size:20px;font-weight:bold'>An upgrade has been performed</span>
								<br /><br />
								You'll need to update all your skin caches to ensure the new template bits have been added correctly.
								<br /><br /><b>&gt;&gt; <a href='{$ibforums->base_url}&act=sets&code=rebuildalltemplates&removewarning=1'>REBUILD ALL SKIN CACHES & REMOVE THIS WARNING</a> &lt;&lt;</b>
								</div><br /><br />";
		}

		//-----------------------------------------
		// INSTALLER PRESENT?
		//-----------------------------------------

		$sm_install = 0;
		$lock_file  = 0;

		if ( @file_exists( ROOT_PATH . 'install/index.php' ) )
		{
			$sm_install = 1;
		}

		if ( @file_exists( ROOT_PATH . 'install/install.lock' ) )
		{
			$lock_file = 1;
		}

		if ( $sm_install == 1 )
		{
			if ( $lock_file != 1 )
			{
				$ibforums->html .= "<div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
				                   <span style='font-size:20px;font-weight:bold'>Warning: Unlocked Installer Still Present</span>
				                   <br /><br /><span style='font-size:14px;'>Remove <b>install/index.php</b> from your server at once!
				                   <br />Leaving it on your server WILL compromise the security of your system.</span></div><br /><br />";
			}
			else
			{
				$ibforums->html .= "<div style='color:red;border:1px solid red;background:#FFE1E2;padding:10px'>
				                   <span style='font-size:14px;font-weight:bold'>Warning: Installer Still Present</span>
				                   <br /><br /><span style='font-size:10px;'>Although the installer appears to be locked, we recommend you remove it
				                   from your server for security.
				                   <br />Simply remove <b>install/index.php</b> from your installation to remove this message.</span></div><br /><br />";
			}
		}


		//-----------------------------------------
		// BOARD OFFLINE?
		//-----------------------------------------

		if ($ibforums->vars['board_offline'])
		{

			$ibforums->adskin->td_header[] = array( "&nbsp;", "100%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Offline Notice" );


			$ibforums->html .= $ibforums->adskin->add_td_row( array( "Your board is currently offline<br><br>&raquo; <a href='{$ibforums->base_url}&act=op&code=findsetting&key=boardoffline%2Fonline'>Turn Board Online</a>"
											 )      );

			$ibforums->html .= $ibforums->adskin->end_table();

			$ibforums->html .= $ibforums->adskin->add_td_spacer();
		}

		//-----------------------------------------
		// Quick clicks + Upgrade History
		//-----------------------------------------

		$ibforums->html .= "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						    <tr>
						     <td width='49%' valign='top'>";

		$ibforums->html .= $ibforums->adskin->start_form();

		$ibforums->adskin->td_header[] = array( "{none}"  , "30%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "30%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Quick Clicks" );

		$ibforums->html .= "

					<script language='javascript'>
					<!--
					  function edit_member() {

						if (document.forms[1].username.value == \"\") {
							alert(\"You must enter a username!\");
						} else {
							window.parent.body.location = '{$ibforums->adskin->base_url}' + '&act=mem&code=searchresults&searchtype=normal&name=' + escape(document.forms[1].username.value);
						}
					  }

					  function new_forum() {

						if (document.forms[1].forum_name.value == \"\") {
							alert(\"You must enter a forum name!\");
						} else {
							window.parent.body.location = '{$ibforums->adskin->base_url}' + '&act=forum&code=new&name=' + escape(document.forms[1].forum_name.value);
						}
					  }

					  function phplookup() {

						if (document.forms[1].phpfunc.value == \"\") {
							alert(\"You must enter a PHP function!\");
						} else {
							window.parent.body.location = 'http://www.php.net/' + escape(document.forms[1].phpfunc.value);
						}
					  }
					//-->

					</script>
					<form name='DOIT' action=''>

		";

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Edit Member:",
																 "<input type='text' style='width:100%' class='textinput' name='username' value='Enter name here' onfocus='this.value=\"\"'>",
																 "<input type='button' value='Find Member' id='button' onClick='edit_member()'>"
														)      );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Add New Forum:",
																 "<input type='text' style='width:100%' name='forum_name' class='textinput' value='Forum title here' onfocus='this.value=\"\"'>",
																 "<input type='button' value='Add Forum' id='button' onClick='new_forum()'>"
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "PHP Function:",
																 "<input type='text' style='width:100%' name='phpfunc' class='textinput' value='PHP function here' onfocus='this.value=\"\"'>",
																 "<input type='button' value='Look-up' id='button' onClick='phplookup()'>"
														)      );

		$ibforums->html .= "</form>";

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= "</td>
		 				    <td style='width:10px'>&nbsp;</td>
		 				    <td width='49%' valign='top'>";

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Upgrade History" );

		foreach( $upgrade_history as $r )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( $r['upgrade_version_human'] .' ('.$r['upgrade_version_id'].')',
																	 $std->get_date( $r['upgrade_date'], 'SHORT' )
															)      );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= "</td>
							</tr>
							</table>";

		//-----------------------------------------
		// ADMINS USING CP
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Name"           , "20%" );
		$ibforums->adskin->td_header[] = array( "IP Address"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Log In"         , "20%" );
		$ibforums->adskin->td_header[] = array( "Last Click"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Location"       , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Administrators using the CP" );

		$t_time = time() - 60*10;

		$DB->simple_construct( array( 'select' => 'session_member_name, session_location, session_log_in_time, session_running_time, session_ip_address',
									  'from'   => 'admin_sessions',
									  'where'  => "session_running_time > $t_time" ) );
		$DB->simple_exec();

		$time_now = time();

		$seen_name = array();

		while ( $r = $DB->fetch_row() )
		{
			if ( $seen_name[ $r['session_member_name'] ] == 1 )
			{
				continue;
			}
			else
			{
				$seen_name[ $r['session_member_name'] ] = 1;
			}

			$log_in = $time_now - $r['session_log_in_time'];
			$click  = $time_now - $r['session_running_time'];

			if ( ($log_in / 60) < 1 )
			{
				$log_in = sprintf("%0d", $log_in) . " seconds ago";
			}
			else
			{
				$log_in = sprintf("%0d", ($log_in / 60) ) . " minutes ago";
			}

			if ( ($click / 60) < 1 )
			{
				$click = sprintf("%0d", $click) . " seconds ago";
			}
			else
			{
				$click = sprintf("%0d", ($click / 60) ) . " minutes ago";
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array (
														$r['session_member_name'],
														"<center><a href='javascript:alert(\"Host Name: ".@gethostbyaddr($r['session_ip_address'])."\")' title='Get host name'>".$r['session_ip_address']."</a></center>",
														"<center>".$log_in."</center>",
														"<center>".$click."</center>",
														"<center>".$r['session_location']."</center>",
											 )       );
		}



		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_spacer();

		//-----------------------------------------


		if ($ibforums->member['mgroup'] == $ibforums->vars['admin_group'])
		{
			//-----------------------------------------
			// LAST 5 Admin Actions
			//-----------------------------------------

			$ibforums->adskin->td_header[] = array( "Member Name"            , "20%" );
			$ibforums->adskin->td_header[] = array( "Action Performed"        , "40%" );
			$ibforums->adskin->td_header[] = array( "Time of action"         , "20%" );
			$ibforums->adskin->td_header[] = array( "IP address"             , "20%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Last 5 Admin Actions" );

			$DB->cache_add_query( 'index_admin_logs', array() );
			$DB->cache_exec_query();

			if ( $DB->get_num_rows() )
			{
				while ( $rowb = $DB->fetch_row() )
				{
					$rowb['ctime'] = $ibforums->admin->get_date( $rowb['ctime'] );

					$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$rowb['name']}</b>",
															  "{$rowb['note']}",
															  "{$rowb['ctime']}",
															  "{$rowb['ip_address']}",
													 )      );


				}
			}
			else
			{
				$ibforums->html .= $ibforums->adskin->add_td_basic("<center>No results</center>");
			}

			$ibforums->html .= $ibforums->adskin->end_table();

			//-----------------------------------------

			$ibforums->html .= $ibforums->adskin->add_td_spacer();
		}

		//-----------------------------------------
		// Bots stuff
		//-----------------------------------------

		if ( $ibforums->vars['spider_sense'] )
		{
			//-----------------------------------------
			// Get bot names
			//-----------------------------------------

			foreach( explode( "\n", $ibforums->vars['search_engine_bots'] ) as $bot )
			{
				list($ua, $n) = explode( "=", $bot );

				$this->bot_map[ strtolower($ua) ] = $n;
			}

			$ibforums->adskin->td_header[] = array( "Search Bot"   , "20%" );
			$ibforums->adskin->td_header[] = array( "Date"         , "25%" );
			$ibforums->adskin->td_header[] = array( "Query"        , "20%" );
			$ibforums->adskin->td_header[] = array( "Query"        , "35%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Last 10 Search Engine Spiders Hits" );

			$DB->simple_construct( array( 'select' => '*', 'from' => 'spider_logs', 'order' => 'entry_date DESC', 'limit' => array( 0,10) ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<strong>".$this->bot_map[ strtolower($r['bot']) ]."&nbsp;</strong>",
																		 $ibforums->admin->get_date( $r['entry_date'], 'SHORT' ),
																		 $r['ip_address'].'&nbsp;',
																		 $r['query_string'].'&nbsp;'
																)      );
			}

			$ibforums->html .= $ibforums->adskin->end_table();

			$ibforums->html .= $ibforums->adskin->add_td_spacer();
		}

		//-----------------------------------------
		// Stats
		//-----------------------------------------

		$reg = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as reg', 'from' => 'validating', 'where' => 'lost_pass <> 1' ) );

		$reg['reg'] = intval( $reg['reg'] );

		$coppa = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as coppa', 'from' => 'validating', 'where' => 'coppa_user=1' ) );

		$coppa['coppa'] = intval( $coppa['coppa'] );



		$ibforums->adskin->td_header[] = array( "Definition", "25%" );
		$ibforums->adskin->td_header[] = array( "Value"     , "25%" );
		$ibforums->adskin->td_header[] = array( "Definition", "25%" );
		$ibforums->adskin->td_header[] = array( "Value"     , "25%" );

		$ibforums->html .= $ibforums->adskin->start_table( "System Overview" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Total Unique Topics"    , intval($ibforums->cache['stats']['total_topics']),
																 "Total Replies to topics", intval($ibforums->cache['stats']['total_replies']),
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Total Members", intval($ibforums->cache['stats']['mem_count']), "Public Upload Folder Size", $uploads_size ) );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod'>Users awaiting validation</a>" , $reg['reg'],
																 "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod'>COPPA Requests</a> from 'Users awaiting validation' total", $coppa['coppa'],
														)      );

		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->admin->output();

	}

}


?>