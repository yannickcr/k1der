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
|   > ICQ / AIM / EMAIL functions
|   > Module written by Matt Mecham
|   > Date started: 28th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class contactmember
{

    var $output    = "";
    var $base_url  = "";
    var $html      = "";

    var $nav       = array();
    var $page_title= "";
    var $email     = "";
    var $forum     = "";

	var $int_error  = "";
	var $int_extra  = "";

    /*-------------------------------------------------------------------------*/
	//
	// Our constructor, load words, load skin
	//
	/*-------------------------------------------------------------------------*/

    function contactmember()
    {
		global $ibforums, $DB, $std, $print;

        // What to do?

        switch($ibforums->input['act'])
        {
        	case 'Mail':
        		$this->mail_member();
        		break;
        	case 'AOL':
        		$this->show_aim();
        		break;
        	case 'ICQ':
        		$this->show_icq();
        		break;
        	case 'MSN':
        		$this->show_msn();
        		break;
        	case 'YAHOO':
        		$this->show_yahoo();
        		break;
        	case 'Invite':
        		$this->invite_member();
        		break;

        	case 'chat':
        		if ( $ibforums->vars['chat_account_no'] )
				{
					$this->chat_display();
				}
				else if ( $ibforums->vars['chat04_account_no'] )
				{
					if ( $ibforums->input['CODE'] == 'update' )
					{
						$this->chat04_refresh();
					}
					else
					{
						$this->chat04_display();
					}
				}
        		break;

        	case 'report':
        		if ($ibforums->input['send'] != 1)
        		{
        			$this->report_form();
        		}
        		else
        		{
        			$this->send_report();
        		}
        		break;

        	case 'boardrules':
        		$this->board_rules();
        		break;

        	default:
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
        		break;
        }

        $print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );

	}

	/*-------------------------------------------------------------------------*/
	// BOARD RULES
	//
	/*-------------------------------------------------------------------------*/


	function board_rules()
	{
		global $ibforums, $DB, $std, $print;

		//-----------------------------------------
		// Get board rule (not cached)
		//-----------------------------------------

		$row = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings', 'where' => "conf_key='gl_guidelines'" ) );

		$ibforums->lang  = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html      = $std->load_template('skin_emails');

		$row['conf_value'] = $std->my_nl2br(stripslashes($row['conf_value']));

		$this->nav[] = $ibforums->vars['gl_title'];

        $this->page_title = $ibforums->vars['gl_title'];

        $this->output .= $this->html->board_rules( $ibforums->vars['gl_title'], $row['conf_value'] );

	}

	/*-------------------------------------------------------------------------*/
	// IP CHAT04: Refresh useronline
	//
	/*-------------------------------------------------------------------------*/

	function chat04_refresh()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Okay: refresh every 90 seconds
		//-----------------------------------------

		if ( $ibforums->lastclick > ( time() - 3600 ) )
		{
			//-----------------------------------------
			// Our last click was more recent than an hour!
			//-----------------------------------------

			if ( ! strstr( $ibforums->location, 'chat' ) )
			{
				//-----------------------------------------
				// And we're no longer in chat
				// .... put 'em back!
				//-----------------------------------------

				$DB->do_update( 'sessions', array( 'location' => 'chat,' ), "id='".$ibforums->my_session."'" );
			}
		}

		//-----------------------------------------
		// Stop cycling after 2 hours of no activity
		//-----------------------------------------

		if ( $ibforums->lastclick > ( time() - 7200 ) )
		{
			//-----------------------------------------
			// Print out the 'blank' gif
			//-----------------------------------------

			@header( "Content-Type: text/html" );
			print "<html><head><meta http-equiv='refresh' content='90; url={$ibforums->base_url}act=chat&CODE=update'></head><body></body></html>";
			exit();
		}

	}

	/*-------------------------------------------------------------------------*/
	// IP CHAT04:
	//
	/*-------------------------------------------------------------------------*/

	function chat04_display()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html     = $std->load_template('skin_emails');

		if ( ! $ibforums->vars['chat04_account_no'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		//-----------------------------------------
		// Get extra settings
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'conf_key,conf_value,conf_default', 'from' => 'conf_settings', 'where' => "conf_key LIKE 'chat04%'" ) );
    	$DB->simple_exec();

    	while( $r = $DB->fetch_row() )
    	{
    		$value = $r['conf_value'] != "" ? $r['conf_value'] : $r['conf_default'];

    		$ibforums->vars[ $r['conf_key'] ] = $value;
    	}

		//-----------------------------------------
		// Got room?
		//-----------------------------------------

		if ( ! $ibforums->vars['chat04_default_room'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		//-----------------------------------------
		// Got service type?
		//-----------------------------------------

		if ( ! $ibforums->vars['chat04_servicetype'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		//-----------------------------------------
		// Make sure it has #
		//-----------------------------------------

		$ibforums->vars['chat04_default_room'] = '#'.str_replace( '#', '', $ibforums->vars['chat04_default_room'] );

		//-----------------------------------------
		// Get service library
		//-----------------------------------------

		require_once( ROOT_PATH.'retail/chatservice.php' );

		$server = 'http://'. $CHAT_SERVER[ $ibforums->vars['chat04_servicetype'] ].'/'. $CHAT_FOLDER[ $ibforums->vars['chat04_servicetype'] ];

		$width  = $ibforums->vars['chat04_width']    ? $ibforums->vars['chat04_width']  : 600;
		$height = $ibforums->vars['chat04_height']   ? $ibforums->vars['chat04_height'] : 350;

		//-----------------------------------------
		// Lang?
		//-----------------------------------------

		$ibforums->vars['chat04_default_lang'] = ( $ibforums->vars['chat04_default_lang'] == "" ) ? 'english.conf' : $ibforums->vars['chat04_default_lang'];

		//-----------------------------------------
		// Text mode
		//-----------------------------------------

		$ibforums->vars['chat04_plainmode'] = ( $ibforums->vars['chat04_plainmode'] ) ? 'PlainText' : 'MegaText';

		//-----------------------------------------
		// Style options..
		//-----------------------------------------

		$style = array(
						'applet_bg' => $ibforums->vars['chat04_style_applet_bg'] ? str_replace( '#', '', $ibforums->vars['chat04_style_applet_bg'] ) : 'BCD0ED',
						'applet_fg' => $ibforums->vars['chat04_style_applet_fg'] ? str_replace( '#', '', $ibforums->vars['chat04_style_applet_fg'] ) : '345487',
						'window_bg' => $ibforums->vars['chat04_style_window_bg'] ? str_replace( '#', '', $ibforums->vars['chat04_style_window_bg'] ) : 'F5F9FD',
						'window_fg' => $ibforums->vars['chat04_style_window_fg'] ? str_replace( '#', '', $ibforums->vars['chat04_style_window_fg'] ) : '345487',
						'font_size' => $ibforums->vars['chat04_style_font_size'] ? str_replace( '#', '', $ibforums->vars['chat04_style_font_size'] ) : '11',
					  );

		//-----------------------------------------
		// Show chat..
		//-----------------------------------------

		if ( $ibforums->input['pop'] )
		{
			$html = $this->html->chat04_pop( $server, $ibforums->vars['chat04_account_no'], $ibforums->vars['chat04_default_room'], $width, $height, $ibforums->vars['chat04_default_lang'], $ibforums->vars['chat04_plainmode'], $style );

			$html = str_replace( '<!--AUTOLOGIN-->'  , $this->ipchat_auto_login()            , $html );
			$html = str_replace( '<!--CUSTOMPARAM-->', $ibforums->vars['chat04_customparams'], $html );

			$print->pop_up_window( "CHAT", $html );

			exit();
		}
		else
		{
			$this->output .= $this->html->chat04_inline( $server, $ibforums->vars['chat04_account_no'], $ibforums->vars['chat04_default_room'], $width, $height, $ibforums->vars['chat04_default_lang'], $ibforums->vars['chat04_plainmode'], $style );
			$this->output = str_replace( '<!--AUTOLOGIN-->'  , $this->ipchat_auto_login()            , $this->output );
			$this->output = str_replace( '<!--CUSTOMPARAM-->', $ibforums->vars['chat04_customparams'], $this->output );
		}

        $this->nav[] = $ibforums->lang['live_chat'];

        $this->page_title = $ibforums->lang['live_chat'];

	}

	/*-------------------------------------------------------------------------*/
	// IPCHAT (NEW) Auto_login
	/*-------------------------------------------------------------------------*/

	function ipchat_auto_login()
	{
		global $ibforums, $DB, $std;

		if ( $ibforums->member['id'] )
		{
			$converge_member = $ibforums->converge->converge_load_member_by_id($ibforums->member['id']);
			$pass = $ibforums->converge->member['converge_pass_hash'];

			$tmpname   = $ibforums->member['name'];
			$namearray = array();
			$name      = "";

			//-----------------------------------------
			// Okay, we need to safe format this name
			//-----------------------------------------

			$tmpname = preg_replace( "#\s#", "_", $tmpname );
			$tmpname = preg_replace( "#(?:[^\w\d\_])#is", "-", $tmpname );

			$return = "<param name='ctrl.LoginOnLoad' value='true'>\n".
      				  "<param name='ctrl.Nickname' value='".$tmpname."'>\n".
      				  "<param name='ctrl.RealName' value='".$ibforums->member['name']."'>\n".
      				  "<param name='ctrl.Password' value='".urlencode("md5pass({$pass}){$ibforums->member['id']}")."'>\n";

      		return $return;
		}
		else
		{
			return;
		}
	}

	/*-------------------------------------------------------------------------*/
	// IP CHAT:
	//
	/*-------------------------------------------------------------------------*/


	function chat_display()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html     = $std->load_template('skin_emails');

		if ( ! $ibforums->vars['chat_account_no'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		if ( ! $ibforums->vars['chat_server_addr'] )
		{
			$ibforums->vars['chat_server_addr'] = 'client1.invisionchat.com';
		}

		$ibforums->vars['chat_server_addr'] = str_replace( 'http://', '', $ibforums->vars['chat_server_addr'] );

		$width  = $ibforums->vars['chat_width']    ? $ibforums->vars['chat_width']  : 600;
		$height = $ibforums->vars['chat_height']   ? $ibforums->vars['chat_height'] : 350;

		$lang   = $ibforums->vars['chat_language'] ? $ibforums->vars['chat_language'] : 'en';

		$user = "";
		$pass = "";

		if ( $ibforums->member['id'] )
		{
			$user = $ibforums->member['name'];

			$converge_member = $ibforums->converge->converge_load_member_by_id($ibforums->member['id']);
			$pass = $ibforums->converge->member['converge_pass_hash'];
		}

		if ( $ibforums->input['pop'] )
		{
			$html = $this->html->chat_pop( $ibforums->vars['chat_server_addr'], $ibforums->vars['chat_account_no'], $lang, $width, $height, $user, $pass );

			$print->pop_up_window( "CHAT", $html );

			exit();
		}
		else
		{
			$this->output .= $this->html->chat_inline( $ibforums->vars['chat_server_addr'], $ibforums->vars['chat_account_no'], $lang, $width, $height, $user, $pass);
		}

        $this->nav[] = $ibforums->lang['live_chat'];

        $this->page_title = $ibforums->lang['live_chat'];

	}




	/*-------------------------------------------------------------------------*/
	// REPORT POST FORM:
	//
	/*-------------------------------------------------------------------------*/


	function report_form()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html     = $std->load_template('skin_emails');

		$pid = intval($ibforums->input['p']);
		$tid = intval($ibforums->input['t']);
		$st  = intval($ibforums->input['st']);

		if ( (!$pid) and (!$tid) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		// Do we have permission to do stuff in this forum? Lets hope so eh?!

		$this->check_access($tid);

		$this->output .= $this->html->report_form($tid, $pid, $st, $this->topic['topic_title']);

        $this->nav[] = "<a href='".$ibforums->base_url."act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>";
        $this->nav[] = $ibforums->lang['report_title'];

        $this->page_title = $ibforums->lang['report_title'];

	}


	function send_report()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html     = $std->load_template('skin_emails');

		$pid = intval($ibforums->input['p']);
		$tid = intval($ibforums->input['t']);
		$fid = intval($ibforums->input['f']);
		$st  = intval($ibforums->input['st']);

		if ( (!$pid) and (!$tid) and (!$fid) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		//-----------------------------------------
		// Make sure we came in via a form.
		//-----------------------------------------

		if ( $_POST['message'] == "" )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form') );
		}

		//-----------------------------------------
		// Get the topic title
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'title', 'from' => 'topics', 'where' => "tid=".$tid ) );
		$DB->simple_exec();

		$topic = $DB->fetch_row();

		if ( ! $topic['title'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}

		//-----------------------------------------
		// Do we have permission to do stuff in this
		// forum? Lets hope so eh?!
		//-----------------------------------------

		$this->check_access($tid);

		$mods = array();
		$fid  = $this->forum['id'];

		//-----------------------------------------
		// Check for mods in this forum
		//-----------------------------------------

		$DB->cache_add_query( 'contact_member_report_get_mods', array( 'fid' => $fid ) );
		$DB->cache_exec_query();

		if ( $DB->get_num_rows() )
		{
			while( $r = $DB->fetch_row() )
			{
				$mods[ $r['id'] ] = $r;
			}
		}
		else
		{
			//-----------------------------------------
			// No mods? Get those super moderators
			//-----------------------------------------

			$DB->cache_add_query( 'contact_member_report_get_supmod', array() );
			$DB->cache_exec_query();

			if ( $DB->get_num_rows() )
			{
				while( $r = $DB->fetch_row() )
				{
					$mods[ $r['id'] ] = $r;
				}
			}
			else
			{
				//-----------------------------------------
				// No supmods? Get those with control panel access
				//-----------------------------------------

				$DB->cache_add_query( 'contact_member_report_get_cpaccess', array() );
				$DB->cache_exec_query();

				while( $r = $DB->fetch_row() )
				{
					$mods[ $r['id'] ] = $r;
				}
			}
		}

		//-----------------------------------------
    	// Get the emailer module
		//-----------------------------------------

		require ROOT_PATH."sources/classes/class_email.php";

		$this->email = new emailer();

		require_once( ROOT_PATH.'sources/lib/msg_functions.php' );

		$this->lib = new msg_functions();
		$this->lib->init();

		//-----------------------------------------
		// Loop and send the mail
		//-----------------------------------------

		$report = trim(stripslashes($ibforums->input['message']));

		foreach( $mods as $idx => $data )
		{
			$this->email->get_template("report_post");

			$this->email->build_message( array(
												'MOD_NAME'     => $data['name'],
												'USERNAME'     => $ibforums->member['name'],
												'TOPIC'        => $topic['title'],
												'LINK_TO_POST' => "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}"."?act=ST&f=$fid&t=$tid&st=$st&#entry$pid",
												'REPORT'       => $report,
											  )
			        					);

			//-----------------------------------------
			// Email?
			//-----------------------------------------

			if ( $ibforums->vars['reportpost_method'] == 'email' )
			{
				$this->email->subject = $ibforums->lang['report_subject'].' '.$ibforums->vars['board_name'];
				$this->email->to      = $data['email'];

				$this->email->send_mail();
			}

			//-----------------------------------------
			// PM?
			//-----------------------------------------

			else
			{
				$this->lib->to_by_id    = $data['id'];
 				$this->lib->from_member = $ibforums->member;
 				$this->lib->msg_title   = $ibforums->lang['report_subject'].' '.$topic['title'];
 				$this->lib->msg_post    = $this->email->message;
				$this->lib->force_pm    = 1;

				$this->lib->send_pm();

				if ( $this->lib->error )
				{
					print $this->error;
					exit();
				}
			}
		}

		$print->redirect_screen( $ibforums->lang['report_redirect'], "act=ST&f=$fid&t=$tid&st=$st&#entry$pid");
	}

	//-----------------------------------------


    function check_access($tid)
    {
		global $ibforums, $DB, $std, $forums, $HTTP_COOKIE_VARS;

		if ( ! $ibforums->member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

		//-----------------------------------------
		// Needs silly a. alias to keep oracle
		// happy
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'a.*,a.title as topic_title', 'from' => 'topics a', 'where' => "a.tid=".$tid ) );
		$DB->simple_exec();

        $this->topic = $DB->fetch_row();

        $this->forum = $forums->forum_by_id[ $this->topic['forum_id'] ];

		$return = 1;

		if ( $std->check_perms($this->forum['read_perms']) == TRUE )
		{
			$return = 0;
		}

		if ($this->forum['password'])
		{
			if ($HTTP_COOKIE_VARS[ $ibforums->vars['cookie_id'].'iBForum'.$this->forum['id'] ] == $this->forum['password'])
			{
				$return = 0;
			}
		}

		if ($return == 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

	}

	/*-------------------------------------------------------------------------*/
	// MSN CONSOLE:
	//
	/*-------------------------------------------------------------------------*/

	function show_msn()
	{
		global $ibforums, $DB, $std, $print;

		$this->html    = $std->load_template('skin_emails');

		$ibforums->lang    = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		//-----------------------------------------

		if (empty($ibforums->member['id']))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

		if ( empty($ibforums->input['MID']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		if (! preg_match( "/^(\d+)$/" , $ibforums->input['MID'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		$DB->query("SELECT name, id, msnname from ibf_members WHERE id='".$ibforums->input['MID']."'");

		$member = $DB->fetch_row();

		//-----------------------------------------

		if (! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}

		//-----------------------------------------

		if (! $member['msnname'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_msn' ) );
		}

		//-----------------------------------------

		$html  = $this->html->pager_header( array( 'TITLE' => 'MSN' ) );

		$html .= $this->html->msn_body( $member['msnname'] );

		$html .= $this->html->end_table();

		$print->pop_up_window( "MSN CONSOLE", $html );

	}

	/*-------------------------------------------------------------------------*/
	// Yahoo! CONSOLE:
	//
	/*-------------------------------------------------------------------------*/

	function show_yahoo()
	{
		global $ibforums, $DB, $std, $print;

		$this->html    = $std->load_template('skin_emails');

		$ibforums->lang    = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		//-----------------------------------------

		if (empty($ibforums->member['id']))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

		if ( empty($ibforums->input['MID']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		if (! preg_match( "/^(\d+)$/" , $ibforums->input['MID'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		$DB->query("SELECT name, id, yahoo from ibf_members WHERE id='".$ibforums->input['MID']."'");

		$member = $DB->fetch_row();

		//-----------------------------------------

		if (! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}

		//-----------------------------------------

		if (! $member['yahoo'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_yahoo' ) );
		}

		//-----------------------------------------

		$html  = $this->html->pager_header( array( 'TITLE' => "Yahoo!" ) );

		$html .= $this->html->yahoo_body( $member['yahoo'] );

		$html .= $this->html->end_table();

		$print->pop_up_window( "YAHOO! CONSOLE", $html );

	}

    /*-------------------------------------------------------------------------*/
	// AOL CONSOLE:
	//
	/*-------------------------------------------------------------------------*/


	function show_aim()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->lang    = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html    = $std->load_template('skin_emails');

		//-----------------------------------------

		if (empty($ibforums->member['id']))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

		if ( empty($ibforums->input['MID']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		if (! preg_match( "/^(\d+)$/" , $ibforums->input['MID'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		$DB->query("SELECT name, id, aim_name from ibf_members WHERE id='".$ibforums->input['MID']."'");

		$member = $DB->fetch_row();

		//-----------------------------------------

		if (! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}

		//-----------------------------------------

		if (! $member['aim_name'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_aol' ) );
		}

		$member['aim_name'] = str_replace(" ", "", $member['aim_name']);

		//-----------------------------------------

		$print->pop_up_window( "AOL CONSOLE", $this->html->aol_body( array( 'AOLNAME' => $member['aim_name'] ) ) );

	}

	/*-------------------------------------------------------------------------*/
	// ICQ CONSOLE:
	//
	/*-------------------------------------------------------------------------*/


	function show_icq()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->lang    = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id);

		$this->html    = $std->load_template('skin_emails');

		//-----------------------------------------

		if (empty($ibforums->member['id'])) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

		if ( empty($ibforums->input['MID']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		if (! preg_match( "/^(\d+)$/" , $ibforums->input['MID'] ) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		$DB->query("SELECT name, id, icq_number from ibf_members WHERE id='".$ibforums->input['MID']."'");

		$member = $DB->fetch_row();

		//-----------------------------------------

		if (! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}

		//-----------------------------------------

		if (! $member['icq_number'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_icq' ) );
		}

		//-----------------------------------------

		$html  = $this->html->pager_header( array( $ibforums->lang['icq_title'] ) );

		$html .= $this->html->icq_body( array( 'UIN' => $member['icq_number'] ) );

		$html .= $this->html->end_table();

		$print->pop_up_window( "ICQ CONSOLE", $html );


	}

	/*-------------------------------------------------------------------------*/
	// MAIL MEMBER:
	//
	// Handles the routines called by clicking on the "email" button when
	// reading topics
	/*-------------------------------------------------------------------------*/

	function mail_member()
	{
		global $ibforums, $DB, $std, $print;

		require "./sources/classes/class_email.php";
		$this->email = new emailer();

		//-----------------------------------------

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_emails', $ibforums->lang_id );

		$this->html     = $std->load_template('skin_emails');

		//-----------------------------------------

		if (empty($ibforums->member['id']))
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

		if ( ! $ibforums->member['g_email_friend'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_member_mail' ) );
		}

		//-----------------------------------------

		if ($ibforums->input['CODE'] == '01')
		{

			$this->mail_member_send();

		}
		else
		{
			// Show the form, booo...

			$this->mail_member_form();

		}

	}

	function mail_member_form($errors="", $extra = "")
	{
		global $ibforums, $DB, $std, $print, $HTTP_POST_VARS;

		$ibforums->input['MID'] = intval($ibforums->input['MID']);

		if ( $ibforums->input['MID'] < 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'name, id, email, hide_email', 'from' => 'members', 'where' => "id=".$ibforums->input['MID'] ) );
		$DB->simple_exec();

		$member = $DB->fetch_row();

		//-----------------------------------------

		if (! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}

		if ($member['hide_email'] == 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'private_email' ) );
		}

		//-----------------------------------------

		if ( $errors != "" )
		{
			$msg = $ibforums->lang[$errors];

			if ( $extra != "" )
			{
				$msg = str_replace( "<#EXTRA#>", $extra, $msg );
			}

			$this->output .= $this->html->errors( $msg );
		}

		//-----------------------------------------

		$this->output .= $ibforums->vars['use_mail_form']
					  ? $this->html->send_form(
												  array(
														  'NAME'   => $member['name'],
														  'TO'     => $member['id'],
														  'subject'=> $ibforums->input['subject'],
														  'content'=> stripslashes(htmlspecialchars($_POST['message'])),
													   )
											   )
					  : $this->html->show_address(
												  array(
														  'NAME'    => $member['name'],
														  'ADDRESS' => $member['email'],
													   )
												 );

		$this->page_title = $ibforums->lang['member_address_title'];
		$this->nav        = array( $ibforums->lang['member_address_title'] );


	}

	//-----------------------------------------

	function mail_member_send()
	{
		global $ibforums, $DB, $std, $print;

		$ibforums->input['to'] = intval($ibforums->input['to']);

		if ( $ibforums->input['to'] == 0 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'invalid_use' ) );
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'name, id, email, hide_email', 'from' => 'members', 'where' => "id=".$ibforums->input['to'] ) );
		$DB->simple_exec();

		$member = $DB->fetch_row();

		//-----------------------------------------
		// Check for schtuff
		//-----------------------------------------

		if (! $member['id'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_such_user' ) );
		}

		//-----------------------------------------

		if ($member['hide_email'] == 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'private_email' ) );
		}

		//-----------------------------------------
		// Check for blanks
		//-----------------------------------------

		$check_array = array (
							   'message'   =>  'no_message',
							   'subject'   =>  'no_subject'
							 );

		foreach ($check_array as $input => $msg)
		{
			if (empty($ibforums->input[$input]))
			{
				$ibforums->input['MID'] = $ibforums->input['to'];
				$this->mail_member_form($msg);
				return;
			}
		}

		//-----------------------------------------
		// Check for spam / delays
		//-----------------------------------------

		$email_check = $this->_allow_to_email( $ibforums->member['id'], $ibforums->member['g_email_limit'] );

		if ( $email_check != TRUE )
		{
			$ibforums->input['MID'] = $ibforums->input['to'];
			$this->mail_member_form( $this->int_error, $this->int_extra);
			return;
		}

		//-----------------------------------------
		// Send the email
		//-----------------------------------------

		$this->email->get_template("email_member");

		$this->email->build_message( array(
											'MESSAGE'     => str_replace( "<br>", "\n", str_replace( "\r", "", $ibforums->input['message'] ) ),
											'MEMBER_NAME' => $member['name'],
											'FROM_NAME'   => $ibforums->member['name']
										  )
									);

		$this->email->subject = $ibforums->input['subject'];
		$this->email->to      = $member['email'];
		$this->email->from    = $ibforums->member['email'];
		$this->email->send_mail();

		//-----------------------------------------
		// Store email in the database
		//-----------------------------------------

		$DB->do_insert( 'email_logs', array(
											'email_subject'      => $ibforums->input['subject'],
											'email_content'      => $ibforums->input['message'],
											'email_date'         => time(),
											'from_member_id'     => $ibforums->member['id'],
											'from_email_address' => $ibforums->member['email'],
											'from_ip_address'	 => $ibforums->input['IP_ADDRESS'],
											'to_member_id'		 => $member['id'],
											'to_email_address'	 => $member['email'],
					  )                   );

		//-----------------------------------------
		// Print the success page
		//-----------------------------------------

		$forum_jump = $std->build_forum_jump();

		$this->output  = $this->html->sent_screen($member['name']);

		$this->output .= $this->html->forum_jump($forum_jump);

		$this->page_title = $ibforums->lang['email_sent'];
		$this->nav        = array( $ibforums->lang['email_sent'] );
	}


	//-----------------------------------------
	// CHECK FLOOD LIMIT
	// Returns TRUE if able to email
	// FALSE if not
	//-----------------------------------------

	function _allow_to_email($member_id, $email_limit)
	{
		global $ibforums, $std, $DB;

		$member_id = intval($member_id);

		if ( ! $member_id )
		{
			$this->int_error = 'gen_error';
			return FALSE;
		}

		list( $limit, $flood ) = explode( ':', $email_limit );

		if ( ! $limit and ! $flood )
		{
			return TRUE;
		}

		//-----------------------------------------
		// Get some stuff from the DB!
		// 1) FLOOD?
		//-----------------------------------------

		if ( $flood )
		{
			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'email_logs',
										  'where'  => "from_member_id=$member_id",
										  'order'  => 'email_date DESC',
										  'limit'  => array(0,1) ) );
			$DB->simple_exec();

			$last_email = $DB->fetch_row();

			if ( $last_email['email_date'] + ($flood * 60) > time() )
			{
				$this->int_error = 'exceeded_flood';
				$this->int_extra = $flood;
				return FALSE;
			}
		}

		if ( $limit )
		{
			$time_range = time() - 86400;

			$DB->simple_construct( array( 'select' => 'count(email_id) as cnt',
										  'from'   => 'email_logs',
										  'where'  => "from_member_id=$member_id AND email_date > $time_range",
								 )      );
			$DB->simple_exec();

			$quota_sent = $DB->fetch_row();

			if ( $quota_sent['cnt'] + 1 > $limit )
			{
				$this->int_error = 'exceeded_quota';
				$this->int_extra = limit;
				return FALSE;
			}
		}

		return TRUE; //0bbdd490f4cef787f15661cff49 If we get here...

	}


}






?>