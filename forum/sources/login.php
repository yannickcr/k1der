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
|   > Log in / log out module
|   > Module written by Matt Mecham
|   > Date started: 14th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class login {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $login_html = "";
    var $modules    = "";

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_login', $ibforums->lang_id);

    	$this->login_html = $std->load_template('skin_login');

    	if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";

			$this->modules = new ipb_member_sync();
		}

    	//-----------------------------------------
    	// Are we enforcing log ins?
    	//-----------------------------------------

    	if ($ibforums->vars['force_login'] == 1)
    	{
    		$msg = 'admin_force_log_in';
    	}
    	else
    	{
    		$msg = "";
    	}

    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------

    	switch($ibforums->input['CODE'])
    	{
    		case '01':
    			$this->do_log_in();
    			break;
    		case '02':
    			$this->log_in_form();
    			break;
    		case '03':
    			$this->do_log_out();
    			break;

    		case '04':
    			$this->markforum();
    			break;

    		case '05':
    			$this->markboard();
    			break;

    		case '06':
    			$this->delete_cookies();
    			break;

    		case 'autologin':
    			$this->auto_login();
    			break;

    		default:
    			$this->log_in_form($msg);
    			break;
    	}

    	//-----------------------------------------
    	// If we have any HTML to print, do so...
    	//-----------------------------------------

    	$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );

 	}

 	/*-------------------------------------------------------------------------*/
 	// AUTO LOG IN
 	/*-------------------------------------------------------------------------*/

 	function auto_login()
 	{
		global $ibforums, $DB, $std, $print, $sess;

 		//-----------------------------------------
 		// Universal routine.
 		// If we have cookies / session created, simply return to the index screen
 		// If not, return to the log in form
 		//-----------------------------------------

 		$ibforums->member = $sess->authorise();

 		//-----------------------------------------
 		// If there isn't a member ID set, do a quick check ourselves.
 		// It's not that important to do the full session check as it'll
 		// occur when they next click a link.
 		//-----------------------------------------

 		if ( ! $ibforums->member['id'] )
 		{
			$mid = intval($std->my_getcookie('member_id'));
			$pid = $std->my_getcookie('pass_hash');

			If ( $mid and $pid )
			{
				$DB->simple_construct( array( 'select' => '*',
									          'from'   => 'members',
									          'where'  => "id=$mid and member_login_key='$pid'"
									 )      );
				$DB->simple_exec();

				if ( $member = $DB->fetch_row() )
				{
					$ibforums->member = $member;
					$ibforums->session_id = "";
					$std->my_setcookie('session_id', '0', -1 );
				}
			}
 		}

 		$true_words  = $ibforums->lang['logged_in'];
 		$false_words = $ibforums->lang['not_logged_in'];
 		$method = 'no_show';

 		if ($ibforums->input['fromreg'] == 1)
 		{
 			$true_words  = $ibforums->lang['reg_log_in'];
 			$false_words = $ibforums->lang['reg_not_log_in'];
 			$method = 'show';
 		}
 		else if ($ibforums->input['fromemail'] == 1)
 		{
 			$true_words  = $ibforums->lang['email_log_in'];
 			$false_words = $ibforums->lang['email_not_log_in'];
 			$method = 'show';
 		}
 		else if ($ibforums->input['frompass'] == 1)
 		{
 			$true_words  = $ibforums->lang['pass_log_in'];
 			$false_words = $ibforums->lang['pass_not_log_in'];
 			$method = 'show';
 		}

 		if ($ibforums->member['id'])
 		{
 			if ($method == 'show')
 			{
 				$print->redirect_screen( $true_words, "" );
 			}
 			else
 			{
 				$std->boink_it($ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext']);
 			}
 		}
 		else
 		{
 			if ($method == 'show')
 			{
 				$print->redirect_screen( $false_words, 'act=Login&CODE=00' );
 			}
 			else
 			{
 				$std->boink_it($ibforums->base_url.'&act=Login&CODE=00');
 			}
 		}
 	}

 	/*-------------------------------------------------------------------------*/
 	// DELETE IPB COOKIES
 	/*-------------------------------------------------------------------------*/

 	function delete_cookies()
 	{
		global $ibforums, $DB, $std, $HTTP_COOKIE_VARS;

 		if (is_array($HTTP_COOKIE_VARS))
 		{
 			foreach( $HTTP_COOKIE_VARS as $cookie => $value)
 			{
 				if (preg_match( "/^(".$ibforums->vars['cookie_id']."ibforum.*$)/i", $cookie, $match))
 				{
 					$std->my_setcookie( str_replace( $ibforums->vars['cookie_id'], "", $match[0] ) , '-', -1 );
 				}
 			}
 		}

 		$std->my_setcookie('pass_hash' , '-1');
 		$std->my_setcookie('member_id' , '-1');
 		$std->my_setcookie('session_id', '-1');
 		$std->my_setcookie('topicsread', '-1');
 		$std->my_setcookie('anonlogin' , '-1');
 		$std->my_setcookie('forum_read', '-1');

		$std->boink_it($ibforums->base_url);
		exit();
	}

 	/*-------------------------------------------------------------------------*/
 	// MARK ALL AS READ
 	/*-------------------------------------------------------------------------*/

 	function markboard()
 	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
        // Reset cookie (yum)
        //-----------------------------------------

        $ibforums->forum_read[0] = time();

		$std->hdl_forum_read_cookie('set');

		$std->boink_it($ibforums->base_url.'act=idx');
	}

    /*-------------------------------------------------------------------------*/
    // MARK FORUM AS READ
    /*-------------------------------------------------------------------------*/

    function markforum()
    {
		global $ibforums, $DB, $std, $forums;

        $ibforums->input['f'] = intval($ibforums->input['f']);

        if ( ! $ibforums->input['f'] )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'missing_files' ) );
        }

        $f = $forums->forum_by_id[ $ibforums->input['f'] ];

        if ( ! $f['id'] )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'missing_files' ) );
        }

        $children = $forums->forums_get_children( $f['id'] );

        if ( $ibforums->input['i'] )
        {
			if ( is_array( $children ) and count($children) )
			{
				foreach( $children as $id )
				{
					$ibforums->forum_read[ $id ] = time();
				}
			}
        }

        //-----------------------------------------
        // Reset cookie (yum)
        //-----------------------------------------

        $ibforums->forum_read[ $ibforums->input['f'] ] = time();

		$std->hdl_forum_read_cookie('set');

		//-----------------------------------------
        // Are we getting kicked back to the root forum (if sub forum) or index?
        //-----------------------------------------

        if ( ( count($children) > 1) AND ($ibforums->input['i'] != 1) )
        {
        	//-----------------------------------------
        	// Its a sub forum, lets go redirect to parent forum
        	//-----------------------------------------

        	$std->boink_it($ibforums->base_url."showforum=".$f['parent_id']);
        }
        else
        {
        	$std->boink_it($ibforums->base_url);
        }
    }

    /*-------------------------------------------------------------------------*/
    // LOG IN FORM
    /*-------------------------------------------------------------------------*/

    function log_in_form($message="")
    {
		global $ibforums, $DB, $std, $print, $HTTP_REFERER;

        //-----------------------------------------
		// Are they banned?
		//-----------------------------------------

		if ( is_array( $ibforums->cache['banfilters'] ) and count( $ibforums->cache['banfilters'] ) )
		{
			foreach ($ibforums->cache['banfilters'] as $ip)
			{
				$ip = str_replace( '\*', '.*', preg_quote($ip, "/") );

				if ( preg_match( "/^$ip$/", $ibforums->input['IP_ADDRESS'] ) )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'you_are_banned', 'INIT' => 1 ) );
				}
			}
		}

        if ($message != "")
        {
        	$message = $ibforums->lang[ $message ];
        	$message = preg_replace( "/<#NAME#>/", "<b>{$ibforums->input[UserName]}</b>", $message );

			$this->output .= $this->login_html->errors($message);
		}

		$this->output .= $this->login_html->ShowForm( $ibforums->lang['please_log_in'], htmlentities(urldecode($HTTP_REFERER)) );

		$this->nav        = array( $ibforums->lang['log_in'] );
	 	$this->page_title = $ibforums->lang['log_in'];

		$print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );

        exit();
    }

    /*-------------------------------------------------------------------------*/
    // DO LOG IN
    /*-------------------------------------------------------------------------*/

    function do_log_in()
    {
		global $DB, $ibforums, $std, $print, $sess;

    	$url = "";

    	//-----------------------------------------
		// More unicode..
		//-----------------------------------------

		$len_u = $std->txt_stripslashes($_POST['UserName']);

		$len_u = preg_replace("/&#([0-9]+);/", "-", $len_u );

		$len_p = $std->txt_stripslashes($_POST['PassWord']);

		$len_p = preg_replace("/&#([0-9]+);/", "-", $len_p );

    	//-----------------------------------------
    	// Make sure the username and password were entered
    	//-----------------------------------------

    	if ( $_POST['UserName'] == "" )
    	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_username' ) );
    	}

     	if ( $_POST['PassWord'] == "" )
     	{
    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'pass_blank' ) );
    	}

		//-----------------------------------------
		// Check for input length
		//-----------------------------------------

		if ( $ibforums->vars['converge_login_method'] == 'username' )
		{
			if (strlen($len_u) > 32)
			{
				$std->Error( array( LEVEL => 1, MSG => 'username_long' ) );
			}

			$username = strtolower(str_replace( '|', '&#124;', $ibforums->input['UserName']) );
		}
		else
		{
			$email    = strtolower( trim( $ibforums->input['UserName'] ) );
		}

		if (strlen($len_p) > 32)
		{
			$std->Error( array( LEVEL => 1, MSG => 'pass_too_long' ) );
		}

		$password = md5( $ibforums->input['PassWord'] );

		//-----------------------------------------
		// NAME LOG IN
		//-----------------------------------------

		if ( $ibforums->vars['converge_login_method'] == 'username' )
		{
			$DB->cache_add_query( 'login_getmember', array( 'username' => $username ) );
			$DB->cache_exec_query();

			$member = $DB->fetch_row();

			//-----------------------------------------
			// Got a username?
			//-----------------------------------------

			if ( empty($member['id']) or ($member['id'] == "") )
			{
				$this->log_in_form( 'wrong_name' );
			}

			$ibforums->converge->converge_load_member($member['email']);

			if ( ! $ibforums->converge->member['converge_id'] )
			{
				$this->log_in_form( 'wrong_name' );
			}
		}
		//-----------------------------------------
		// EMAIL LOG IN
		//-----------------------------------------
		else
		{
			//-----------------------------------------
			// No email, but username?
			//-----------------------------------------

			if ( ! $email and $username )
			{
				$email = $username;
			}

			$ibforums->converge->converge_load_member( $email );

			if ( $ibforums->converge->member['converge_id'] )
			{
				$member = $DB->simple_exec_query( array( 'select' => 'id, name, email, mgroup, member_login_key, ip_address, login_anonymous',
														 'from'   => 'members',
														 'where'  => "id=".$ibforums->converge->member['converge_id']
												)      );
			}
			else
			{
				$this->log_in_form( 'wrong_name' );
			}
		}

		//-----------------------------------------
		// Check password...
		//-----------------------------------------

		if ( $ibforums->converge->converge_authenticate_member( $password ) != TRUE )
		{
			$this->log_in_form( 'wrong_pass' );
		}

		//-----------------------------------------
		// We in a validating group?
		// still need this?
		//-----------------------------------------

		if ($member['mgroup'] == $ibforums->vars['auth_group'])
		{
			//$this->log_in_form( 'need_validation' );
		}

		//-----------------------------------------
		// No member log in key?
		//-----------------------------------------

		if ( ! $member['member_login_key'] )
		{
			$member['member_login_key'] = $ibforums->converge->generate_auto_log_in_key();

			$DB->do_update( 'members', array( 'member_login_key' => $member['member_login_key'] ), 'id='.$member['id'] );
		}

		//-----------------------------------------
		// Cookie me softly?
		//-----------------------------------------

		if ($ibforums->input['CookieDate'])
		{
			$std->my_setcookie("member_id"   , $member['id']              , 1);
			$std->my_setcookie("pass_hash"   , $member['member_login_key'], 1);
		}

		//-----------------------------------------
		// Update profile if IP addr missing
		//-----------------------------------------

		if ( $member['ip_address'] == "" OR $member['ip_address'] == '127.0.0.1' )
		{
			$DB->simple_construct( array( 'update' => 'members',
										  'set'    => "ip_address='{$ibforums->input['IP_ADDRESS']}'",
										  'where'  => "id={$member['id']}"
								 )      );

			$DB->simple_exec();
		}

		//-----------------------------------------
		// Create / Update session
		//-----------------------------------------

		$poss_session_id = "";

		if ( $cookie_id = $std->my_getcookie('session_id') )
		{
			$poss_session_id = $std->my_getcookie('session_id');
		}
		else if ( $ibforums->input['s'] )
		{
			$poss_session_id = $ibforums->input['s'];
		}

		if ($poss_session_id)
		{
			$session_id = $poss_session_id;

			//-----------------------------------------
			// Delete any old sessions with this users IP
			// addy that doesn't match our session ID.
			//-----------------------------------------

			$DB->simple_construct( array( 'delete' => 'sessions',
										  'where'  => "ip_address='".$ibforums->input['IP_ADDRESS']."' AND id <> '$session_id'"
								 )      );

			$DB->simple_shutdown_exec();


			$DB->do_shutdown_update( 'sessions',
							array (
									'member_name'  => $member['name'],
									'member_id'    => $member['id'],
									'running_time' => time(),
									'member_group' => $member['mgroup'],
									'login_type'   => $ibforums->input['Privacy'] ? 1 : 0
								  ),
							"id='".$session_id."'"
						);
		}
		else
		{
			$session_id = md5( uniqid(microtime()) );

			//-----------------------------------------
			// Delete any old sessions with this users IP addy.
			//-----------------------------------------

			$DB->simple_construct( array( 'delete' => 'sessions',
										  'where'  => "ip_address='".$ibforums->input['IP_ADDRESS']."'"
								 )      );

			$DB->simple_shutdown_exec();

			$DB->do_shutdown_insert( 'sessions',
									 array (
											 'id'           => $session_id,
											 'member_name'  => $member['name'],
											 'member_id'    => $member['id'],
											 'running_time' => time(),
											 'member_group' => $member['mgroup'],
											 'ip_address'   => substr($ibforums->input['IP_ADDRESS'], 0, 50),
											 'browser'      => substr($std->clean_value($_SERVER['HTTP_USER_AGENT']), 0, 50),
											 'login_type'   => $ibforums->input['Privacy'] ? 1 : 0
								  )       );
		}

		$ibforums->member           = $member;
		$ibforums->session_id       = $session_id;

		if ($ibforums->input['referer'] && ($ibforums->input['act'] != 'Reg'))
		{
			$url = str_replace( '&amp;', '&', $ibforums->input['referer'] );
			$url = str_replace( "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}", "", $url );
			$url = preg_replace( "!^\?!"       , ""   , $url );
			$url = preg_replace( "!s=(\w){32}!", ""   , $url );
			$url = preg_replace( "!act=(login|reg|lostpass)!i", "", $url );
		}

		//-----------------------------------------
		// set our privacy status
		//-----------------------------------------

		$DB->simple_construct( array( 'update' => 'members',
									  'set'    => "login_anonymous='".intval($ibforums->input['Privacy'])."&1'",
									  'where'  => "id={$member['id']}"
							 )      );

		$DB->simple_shutdown_exec();

		//-----------------------------------------
		// Clear out any passy change stuff
		//-----------------------------------------

		$DB->simple_construct( array( 'delete' => 'validating',
									  'where'  => "member_id={$ibforums->member['id']} AND lost_pass=1"
							 )      );

		$DB->simple_shutdown_exec();

		//-----------------------------------------
		// Redirect them to either the board
		// index, or where they came from
		//-----------------------------------------

		$std->my_setcookie("session_id", $ibforums->session_id, -1);

		$this->logged_in = 1;

		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);
			$this->modules->on_login($member);
		}

		if ( $ibforums->input['return'] != "" )
		{
			$return = urldecode($ibforums->input['return']);

			if ( preg_match( "#^http://#", $return ) )
			{
				$std->boink_it($return);
			}
		}

		//-----------------------------------------
		// Check for dupemail
		//-----------------------------------------

		$member_extra = $DB->simple_exec_query( array( 'select' => 'bio', 'from' => 'member_extra', 'where' => 'id='.$member['id'] ) );

		if ( $member_extra['bio'] == 'dupemail' )
		{
			$print->redirect_screen( "{$ibforums->lang[thanks_for_login]} {$ibforums->member['name']}", 'act=usercp' );
		}
		else
		{
			$print->redirect_screen( "{$ibforums->lang[thanks_for_login]} {$ibforums->member['name']}", $url );
		}
	}

	/*-------------------------------------------------------------------------*/
	// DO LOG OUT
	/*-------------------------------------------------------------------------*/

	function do_log_out()
	{
		global $std, $ibforums, $DB, $print, $sess;

		$DB->simple_construct( array( 'update' => 'sessions',
									  'set'    => "member_name='',member_id='0',login_type='0'",
									  'where'  => "id='". $sess->session_id ."'"
							 )      );

		$DB->simple_shutdown_exec();

		list( $privacy, $loggedin ) = explode( '&', $ibforums->member['login_anonymous'] );


		$DB->simple_construct( array( 'update' => 'members',
									  'set'    => "login_anonymous='{$privacy}&0', last_visit=".time().", last_activity=".time(),
									  'where'  => "id=".$ibforums->member['id']
							 )      );

		$DB->simple_shutdown_exec();

		//-----------------------------------------
		// Set some cookies
		//-----------------------------------------

		$std->my_setcookie( "member_id" , "0"  );
		$std->my_setcookie( "pass_hash" , "0"  );
		$std->my_setcookie( "anonlogin" , "-1" );

		if ( is_array($_COOKIE) )
 		{
 			foreach( $_COOKIE as $cookie => $value )
 			{
 				if ( preg_match( "/^(".$ibforums->vars['cookie_id']."ipbforumpass_.*$)/i", $cookie, $match) )
 				{
 					$std->my_setcookie( str_replace( $ibforums->vars['cookie_id'], "", $match[0] ) , '-', -1 );
 				}
 			}
 		}

		//-----------------------------------------
		// Redirect...
		//-----------------------------------------

		$url = "";

		if ( $ibforums->input['return'] != "" )
		{
			$return = urldecode($ibforums->input['return']);

			if ( preg_match( "#^http://#", $return ) )
			{
				$std->boink_it($return);
			}
		}

		$print->redirect_screen( $ibforums->lang['thanks_for_logout'], "" );
	}

}

?>