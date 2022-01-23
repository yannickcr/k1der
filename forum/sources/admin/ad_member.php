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
|   > Admin Forum functions
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


class ad_member {

	var $base_url;
	var $modules = "";

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
    	// Get the sync module
		//-----------------------------------------

		if ( USE_MODULES == 1 )
		{
			require ROOT_PATH."modules/ipb_member_sync.php";

			$this->modules = new ipb_member_sync();
		}

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		$ibforums->admin->nav[] = array( 'act=mem&code=edit', 'Edit Member Search Form' );

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'doform':
				$this->member_do_edit_form();
				break;
			case 'doedit':
				$this->member_do_edit();
				break;
			//-----------------------------------------
			case 'unsuspend':
				$this->member_unsuspend();
				break;
			//-----------------------------------------
			case 'add':
				$this->member_add_form();
				break;
			case 'doadd':
				$this->member_do_add();
				break;
			//-----------------------------------------
			case 'doprune':
				$this->member_doprune();
				break;
			//-----------------------------------------
			// ranks / titles
			//-----------------------------------------
			case 'title':
				$this->titles_start();
				break;
			case 'rank_edit':
				$this->titles_rank_setup('edit');
				break;
			case 'rank_add':
				$this->titles_rank_setup('add');
				break;
			case 'do_add_rank':
				$this->titles_add_rank();
				break;
			case 'do_rank_edit':
				$this->titles_edit_rank();
				break;
			case 'rank_delete':
				$this->titles_delete_rank();
				break;

			//-----------------------------------------
			case 'mod':
				$this->view_mod();
				break;
			case 'domod':
				$this->domod();
				break;
			//-----------------------------------------
			case 'changename':
				$this->member_change_name_start();
				break;
			case 'dochangename':
				$this->member_change_name_complete();
				break;
			//-----------------------------------------

			case 'banmember':
				$this->member_suspend_start();
				break;

			case 'dobanmember':
				$this->member_suspend_complete();
				break;
			//-----------------------------------------
			// Change Passy
			//-----------------------------------------
			case 'changepassword':
				$this->member_password_start();
				break;
			case 'dochangepassword':
				$this->member_password_complete();
				break;
			//-----------------------------------------
			// Member search
			//-----------------------------------------
			case 'search':
				$this->search_form();
				break;
			case 'searchresults':
				$this->search_results();
				break;
			//-----------------------------------------
			// Delete / Prune
			//-----------------------------------------
			case 'member_delete':
				$this->member_delete();
				break;

			default:
				$this->search_form();
				break;
		}

	}

	//-----------------------------------------
	//
	// PASS: START
	//
	//-----------------------------------------

	function member_password_complete()
	{
		global $ibforums, $DB, $std;

		if ( ! $ibforums->input['password'] )
		{
			$ibforums->main_msg = "You must enter a password!";
			$this->member_password_start();
		}

		$salt = $ibforums->converge->generate_password_salt(5);
		$salt = str_replace( '\\', "\\\\", $salt );

		$key  = $ibforums->converge->generate_auto_log_in_key();

		$md5_once = md5( trim($ibforums->input['password']) );

		$converge = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'members_converge', 'where' => 'converge_id='.$ibforums->input['id'] ) );

		$save_array = array();

		if ( $ibforums->input['newsalt'] )
		{
			$save_array['converge_pass_salt'] = $salt;
			$save_array['converge_pass_hash'] = md5( md5($salt) . $md5_once );
		}
		else
		{
			$save_array['converge_pass_hash'] = md5( md5( $converge['converge_pass_salt'] ) . $md5_once );
		}

		$DB->do_update( 'members_converge', $save_array, 'converge_id='.$ibforums->input['id'] );

		if ( $ibforums->input['newkey'] )
		{
			$DB->do_update( 'members', array( 'member_login_key' => $key ), 'id='.$ibforums->input['id'] );
		}

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_query = "";

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_query .= '&'.$bit.'='.trim($ibforums->input[ $bit ]);
		}

		$ibforums->admin->save_log("Members Password Changed ( id: {$ibforums->input['id']} )");

		$ibforums->admin->done_screen("Password Changed", "Member Search", "act=mem".$page_query, "redirect" );
	}

	//-----------------------------------------
	//
	// PASS: START
	//
	//-----------------------------------------

	function member_password_start()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_array = array( 1 => array( 'code'  , 'dochangepassword'  ),
							 2 => array( 'act'   , 'mem'       ),
							 3 => array( 'id'    , $ibforums->input['id']  ),
						   );

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_array[] = array( $bit, trim($ibforums->input[ $bit ]) );
		}

		$ibforums->html .= $ibforums->adskin->start_form( $page_array );

		//-----------------------------------------
		// Get member
		//-----------------------------------------

		$member = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'members', 'where' => 'id='.$ibforums->input['id'] ) );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Change password for member: {$member['name']}" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<strong>Enter the new password</strong>" ,
												  			     $ibforums->adskin->form_input('password' ),
									     			    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Create new password salt?</b><div style='color:gray'>If set to 'yes', a new password salt will be generated. Useful if a member is having trouble logging in.</div>" ,
												  				 $ibforums->adskin->form_yes_no( "newsalt", 1 )
									     				)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Create new log in key?</b><div style='color:gray'>If set to 'yes', a new cookie log in key will be generated. Useful if a member is having trouble logging in. Any current cookies will not work.</div>" ,
												  				 $ibforums->adskin->form_yes_no( "newkey", 1 )
									     				)      );

		$ibforums->html .= $ibforums->adskin->end_form("Change Password");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}



	/*-------------------------------------------------------------------------*/
	//
	// TEMP BANNING
	//
	/*-------------------------------------------------------------------------*/

	function member_suspend_start()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Account Suspension";

		$ibforums->admin->page_detail = "Automated temporary member suspension. Simply choose the duration of the suspension and submit the form below";

		$contents = "{membername},\nYour member account at {$ibforums->vars['board_name']} has been temporarily suspended.\n\nYour account will not be functional until {date_end} (depending on your timezone). This is an automated process and you do not need to do anything to expediate the unsuspension process.\n\nBoard Address: {$ibforums->vars['board_url']}/index.php";

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify a valid member id, please go back and try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "id=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		if ( ! $member = $DB->fetch_row() )
		{
			$ibforums->admin->error("We could not match that ID in the members database");
		}

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_array = array( 1 => array( 'code'  , 'dobanmember'  ),
							 2 => array( 'act'   , 'mem'       ),
							 3 => array( 'mid'   , $ibforums->input['mid']  ),
						   ) ;

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_array[] = array( $bit, trim($ibforums->input[ $bit ]) );
		}

		$ibforums->html .= $ibforums->adskin->start_form( $page_array );

		$ban = $std->hdl_ban_line( $member['temp_ban'] );

		$units = array( 0 => array( 'h', 'Hours' ), 1 => array( 'd', 'Days' ) );

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Member Account Suspension", "Note: If this member is already suspended, any new setting will restart the ban" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<strong>Suspend {$member['name']} for...</strong>" ,
												                 $ibforums->adskin->form_input('timespan', $ban['timespan'], "text", "", '5' ) . '&nbsp;' . $ibforums->adskin->form_dropdown('units', $units, $ban['units'] ),
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email notification to this member?</b><br>(If so, you may edit the email below)" ,
												                 $ibforums->adskin->form_yes_no( "send_email", 0 )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email contents</b><br>(Tags: {membername} = member's name, {date_end} = ban end)" ,
												                 $ibforums->adskin->form_textarea( "email_contents", $contents )
									                    ), "", 'top'       );

		$ibforums->html .= $ibforums->adskin->end_form("Suspend This Account");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	/*-------------------------------------------------------------------------*/
	//
	// SUSPEND COMPLETE
	//
	/*-------------------------------------------------------------------------*/

	function member_suspend_complete()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		$ibforums->admin->page_title = "Account Suspension";

		$ibforums->admin->page_detail = "Automated temporary member suspension. Confirmation and information";

		$ibforums->input['mid'] = intval($ibforums->input['mid']);

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify a valid member id, please go back and try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "id=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		if ( ! $member = $DB->fetch_row() )
		{
			$ibforums->admin->error("We could not match that ID in the members database");
		}

		//-----------------------------------------
		// Work out end date
		//-----------------------------------------

		$ibforums->input['timespan'] = intval($ibforums->input['timespan']);

		if ( $ibforums->input['timespan'] == "" )
		{
			$new_ban = "";
		}
		else
		{
			$new_ban = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->input['timespan']), 'unit' => $ibforums->input['units']  ) );
		}

		$show_ban = $std->hdl_ban_line( $new_ban );

		//-----------------------------------------
		// Update and show confirmation
		//-----------------------------------------

		$DB->do_update( 'members', array( 'temp_ban' => $new_ban ), "id=".$ibforums->input['mid'] );

		// I say, did we choose to email 'dis member?

		if ($ibforums->input['send_email'] == 1)
		{
			// By golly, we did!

			require "./sources/classes/class_email.php";

			$this->email = new emailer();

			$msg = trim($std->txt_stripslashes($_POST['email_contents']));

			$msg = str_replace( "{membername}", $member['name']       , $msg );
			$msg = str_replace( "{date_end}"  , $ibforums->admin->get_date( $show_ban['date_end'], 'LONG') , $msg );

			$this->email->message = $this->email->clean_message($msg);
			$this->email->subject = "Account Suspension Notification";
			$this->email->to      = $member['email'];
			$this->email->send_mail();
		}

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_query = "";

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_query .= '&'.$bit.'='.trim($ibforums->input[ $bit ]);
		}

		$ibforums->admin->save_log("Suspended Member(s) ( {$member['name']} )");

		$ibforums->admin->done_screen("Suspended Member(s)", "Member Search", "act=mem".$page_query, "redirect" );
	}

	/*-------------------------------------------------------------------------*/
	//
	// Unsuspend
	//
	/*-------------------------------------------------------------------------*/

	function member_unsuspend()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify a valid member id, please go back and try again");
		}

		if ($ibforums->input['mid'] == 'all')
		{
			$DB->do_update( 'members', array( 'temp_ban' => $new_ban ), "" );

			$ibforums->admin->save_log("Unsuspended all member accounts");

			$msg = "All Accounts Unsuspended";
		}
		else
		{
			$mid = intval($ibforums->input['mid']);

			$DB->do_update( 'members', array( 'temp_ban' => $new_ban ), "id=$mid" );

			$DB->simple_construct( array( 'select' => 'name', 'from' => 'members', 'where' => "id=$mid" ) );
			$DB->simple_exec();

			$member = $DB->fetch_row();

			$ibforums->admin->save_log("Unsuspended {$member['name']}");

			$msg = "{$member['name']} Unsuspended";
		}

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_query = "&name=".$member['name'];

		$ibforums->admin->done_screen($msg, "Member Search", "act=mem".$page_query, "redirect" );
	}


	/*-------------------------------------------------------------------------*/
	//
	// CHANGE MEMBER NAME
	//
	/*-------------------------------------------------------------------------*/

	function member_change_name_complete()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		$ibforums->input['new_name'] = str_replace( '|', '&#124;', $ibforums->input['new_name'] );

		//-----------------------------------------
		// Check
		//-----------------------------------------

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify a valid member id, please go back and try again");
		}

		if ($ibforums->input['new_name'] == "")
		{
			$this->member_change_name_start("You must enter a new name for this member");
			exit();
		}

		//-----------------------------------------
		// Select
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "id=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		if ( ! $member = $DB->fetch_row() )
		{
			$ibforums->admin->error("We could not match that ID in the members database");
		}

		$mid = $ibforums->input['mid']; // Save me poor ol' carpels

		if ($ibforums->input['new_name'] == $member['name'])
		{
			$this->member_change_name_start("The new name is the same as the old name, that is illogical captain");
			exit();
		}

		//-----------------------------------------
		// Check to ensure that his member name hasn't already been taken.
		//-----------------------------------------

		$new_name = trim($ibforums->input['new_name']);

		$DB->cache_add_query( 'login_getmember', array( 'username' => strtolower($new_name) ) );
		$DB->cache_exec_query();

		if ( $DB->get_num_rows() )
		{
			$this->member_change_name_start("The name '$new_name' already exists, please choose another");
			exit();
		}

		//-----------------------------------------
		// If one gets here, one can assume that the new name is correct for one, er...one.
		// So, lets do the converteroo
		//-----------------------------------------

		$DB->do_update( 'members'       , array( 'name'             => $new_name ), "id="            .$mid );
		$DB->do_update( 'contacts'      , array( 'contact_name'     => $new_name ), "contact_id="    .$mid );
		$DB->do_update( 'forums'        , array( 'last_poster_name' => $new_name ), "last_poster_id=".$mid );
		$DB->do_update( 'moderator_logs', array( 'member_name'      => $new_name ), "member_id="     .$mid );
		$DB->do_update( 'moderators'    , array( 'member_name'      => $new_name ), "member_id="     .$mid );
		$DB->do_update( 'posts'         , array( 'author_name'      => $new_name ), "author_id="     .$mid );
		$DB->do_update( 'sessions'      , array( 'member_name'      => $new_name ), "member_id="     .$mid );
		$DB->do_update( 'topics'        , array( 'starter_name'     => $new_name ), "starter_id="    .$mid );
		$DB->do_update( 'topics'        , array( 'last_poster_name' => $new_name ), "last_poster_id=".$mid );

		//-----------------------------------------
		// Recache moderators
		//-----------------------------------------

		require_once( ROOT_PATH .'sources/admin/ad_moderator.php' );
		$admod = new ad_moderator();

		$admod->rebuild_moderator_cache();

		//-----------------------------------------
		// I say, did we choose to email 'dis member?
		//-----------------------------------------

		if ($ibforums->input['send_email'] == 1)
		{
			//-----------------------------------------
			// By golly, we did!
			//-----------------------------------------

			require "./sources/classes/class_email.php";

			$this->email = new emailer();

			$msg = trim($_POST['email_contents']);

			$msg = str_replace( "{old_name}", $member['name'], $msg );
			$msg = str_replace( "{new_name}", $new_name      , $msg );

			$this->email->message = stripslashes($this->email->clean_message($msg));
			$this->email->subject = "Member Name Change Notification";
			$this->email->to      = $member['email'];
			$this->email->send_mail();
		}

		$ibforums->admin->save_log("Changed Member Name '{$member['name']}' to '$new_name'");

		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);
			$this->modules->on_name_change($mid, $new_name );
		}

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_query = "";

		$ibforums->input['name'] = $new_name;

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_query .= '&'.$bit.'='.trim($ibforums->input[ $bit ]);
		}

		$ibforums->admin->done_screen("Member's Name Changed", "Member Search", "act=mem".$page_query, "redirect" );
	}



	/*-------------------------------------------------------------------------*/
	//
	// Change name complete
	//
	/*-------------------------------------------------------------------------*/

	function member_change_name_start($message="")
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Change Member Name";

		$ibforums->admin->page_detail = "You may enter a new name for this member.";

		//-----------------------------------------
		// check
		//-----------------------------------------

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You must specify a valid member id, please go back and try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "id=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		if ( ! $member = $DB->fetch_row() )
		{
			$ibforums->admin->error("We could not match that ID in the members database");
		}

		$contents = "{old_name},\nAn administrator has changed your member name on {$ibforums->vars['board_name']}.\n\nYour new name is: {new_name}\n\nPlease remember this as you may need to use this new name when you log in next time.\nBoard Address: {$ibforums->vars['board_url']}/index.php";

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_array = array( 1 => array( 'code'  , 'dochangename'  ),
							 2 => array( 'act'   , 'mem'       ),
							 3 => array( 'mid'   , $ibforums->input['mid']  ),
						   );

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_array[] = array( $bit, trim($ibforums->input[ $bit ]) );
		}

		$ibforums->html .= $ibforums->adskin->start_form( $page_array );

		//-----------------------------------------
		// start form
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Change Member Name" );

		if ($message != "")
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Error Message:</b>" ,
												                 	  "<b><span style='color:red'>$message</span></b>",
									                    	 )      );
		}


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Current Member's Name</b>" ,
												                 $member['name'],
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>New Members Name</b>" ,
												                 $ibforums->adskin->form_input( "new_name", $ibforums->input['new_name'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email notification to this member?</b><br>(If so, you may edit the email below)" ,
												                 $ibforums->adskin->form_yes_no( "send_email", 1 )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email contents</b><br>(Tags: {old_name} = current name, {new_name} = new name)" ,
												                 $ibforums->adskin->form_textarea( "email_contents", $contents )
									                    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Change this members name");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}


	//-----------------------------------------
	//
	// Moderation control...
	//
	//-----------------------------------------

	function domod()
	{
		global $ibforums, $DB,  $std;

		$ids = array();

		foreach ($ibforums->input as $k => $v)
		{
			if ( preg_match( "/^mid_(\d+)$/", $k, $match ) )
			{
				if ($ibforums->input[ $match[0] ])
				{
					$ids[] = $match[1];
				}
			}
		}

		//-----------------------------------------

		if ( count($ids) < 1 )
		{
			$ibforums->admin->error("You did not select any members to approve or delete");
		}

		//-----------------------------------------

		if ($ibforums->input['type'] == 'approve')
		{

			//-----------------------------------------

			require ROOT_PATH."sources/classes/class_email.php";

			$email = new emailer();

			$email->get_template("complete_reg");

			$email->build_message( "" );

			//-----------------------------------------

			$DB->cache_add_query( 'member_domod', array( 'ids' => $ids ) );
			$main = $DB->cache_exec_query();

			while( $row = $DB->fetch_row( $main ) )
			{
				if ($row['mgroup'] != $ibforums->vars['auth_group'])
				{
					continue;
				}

				if ($row['real_group'] == "")
				{
					$row['real_group'] = $ibforums->vars['member_group'];
				}

				$DB->do_update( 'members', array( 'mgroup' => $row['real_group'] ), "id=".$row['id'] );

				$email->subject = "Account: {$row['name']}, validated at ".$ibforums->vars['board_name'];

				$email->to = $row['email'];

				$email->send_mail();

				if ( USE_MODULES == 1 )
				{
					$this->modules->register_class(&$this);
					$this->modules->on_group_change($row['id'], $row['real_group']);
				}
			}

			$DB->simple_exec_query( array( 'delete' => 'validating', 'where' => "member_id IN(".implode( ",",$ids ).")" ) );

			$DB->simple_construct( array( 'select' => 'id, name',
										  'from'   => 'members',
										  'where'  => "mgroup <> ".$ibforums->vars['auth_group'],
										  'order'  => 'id DESC',
										  'limit'  => array( 0,1 ) ) );
			$DB->simple_exec();

			$r = $DB->fetch_row();

			$ibforums->admin->save_log("Approved Queued Registrations");

			$ibforums->admin->done_screen( count($ids)." Members Approved", "Manage Registrations", "act=mem&code=mod" );

		}
		else
		{
			$DB->simple_exec_query( array( 'delete' => 'members'        , 'where' => "id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'member_extra'   , 'where' => "id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'message_text'   , 'where' => "msg_author_id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'message_topics' , 'where' => "mt_owner_id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'contacts'       , 'where' => "member_id IN(".implode( ",",$ids ).") or contact_id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'validating'     , 'where' => "member_id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'pfields_content', 'where' => "member_id IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'warn_logs'      , 'where' => "wlog_mid IN(".implode( ",",$ids ).")" ) );
			$DB->simple_exec_query( array( 'delete' => 'members_converge', 'where' => "converge_id IN(".implode( ",",$ids ).")" ) );

			$DB->do_update( 'posts' , array( 'author_id'  => 0 ), "author_id  IN(".implode( ",",$ids ).")" );
			$DB->do_update( 'topics', array( 'starter_id' => 0 ), "starter_id IN(".implode( ",",$ids ).")" );

			if ( USE_MODULES == 1 )
			{
				$this->modules->register_class(&$this);
				$this->modules->on_delete($ids);
			}

			$ibforums->admin->save_log("Denied Queued Registrations");

			$ibforums->admin->done_screen( count($ids)." Members Removed", "Manage Registrations", "act=mem&code=mod" );
		}

	}


	//-----------------------------------------

	function view_mod()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title  = "Manage User Registration/Email Change Queues";

		$ibforums->admin->page_detail = "This section allows you to allow or deny registrations where you have requested that an administrator previews new accounts before allowing full membership. It will also allow you to complete or deny new email address changes.<br><br>This form will also allow you to complete the registrations for those who did not receive an email.";

		$DB->simple_construct( array( 'select' => 'COUNT(vid) as mcount', 'from' => 'validating', 'where' => "lost_pass <> 1" ) );
		$DB->simple_exec();

		$row = $DB->fetch_row();

		$cnt = $row['mcount'] < 1 ? 0 : $row['mcount'];

		$st = intval($ibforums->input['st']);

		$ord = $ibforums->input['ord'] == 'asc' ? 'asc' : 'desc';

		$new_ord  = $ord  == 'asc' ? 'desc' : 'asc';

		switch ($ibforums->input['sort'])
		{
			case 'mem':
				$col = 'm.name';
				break;
			case 'email':
				$col = 'm.email';
				break;
			case 'sent':
				$col = 'v.entry_date';
				break;
			case 'posts':
				$col = 'm.posts';
				break;
			case 'reg':
				$col = 'm.joined';
				break;
			default:
				$col = 'v.entry_date';
				break;
		}

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'domod'  ),
																 2 => array( 'act'   , 'mem'    ),
														)      );

		$ibforums->adskin->td_header[] = array( "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod&st=$st&sort=mem&ord=$new_ord'>Member Name</a>"       , "20%" );
		$ibforums->adskin->td_header[] = array( "Where?"            , "20%" );
		$ibforums->adskin->td_header[] = array( "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod&st=$st&sort=email&ord=$new_ord'>Email Address</a>"     , "15%" );
		$ibforums->adskin->td_header[] = array( "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod&st=$st&sort=sent&ord=$new_ord'>Email Sent</a>"        , "10%" );
		$ibforums->adskin->td_header[] = array( "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod&st=$st&sort=posts&ord=$new_ord'>Posts</a>"             , "10%" );
		$ibforums->adskin->td_header[] = array( "<a href='{$ibforums->adskin->base_url}&act=mem&code=mod&st=$st&sort=reg&ord=$new_ord'>Reg. On</a>"           , "10%" );
		$ibforums->adskin->td_header[] = array( "Age" , "10%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"            , "5%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Users awaiting authorisation" );

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $cnt,
											   'PER_PAGE'    => 75,
											   'CUR_ST_VAL'  => $st,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Multiple Pages",
											   'BASE_URL'    => $ibforums->adskin->base_url."&act=mem&code=mod",
									  )      );

		$ibforums->html .= $ibforums->adskin->add_td_basic( "<b>$cnt users require registration or email change validation</b>", "center", "catrow2");

		if ($cnt > 0)
		{
			$DB->cache_add_query( 'member_view_mod', array( 'col' => $col, 'ord' => $ord, 'st' => $st ) );
			$DB->cache_exec_query();

			while ( $r = $DB->fetch_row() )
			{
				if ($r['coppa_user'] == 1)
				{
					$coppa = ' ( COPPA Request )';
				}
				else
				{
					$coppa = "";
				}

				$where = ( $r['lost_pass'] ? 'Lost Password' : ( $r['new_reg'] ? "Registering" : ( $r['email_chg'] ? "Email Change" : 'N/A' ) ) );

				//$age = floor( ( time() - $r['entry_date'] ) / 86400 );

				$hours  = floor( ( time() - $r['entry_date'] ) / 3600 );

				$days   = intval( $hours / 24 );

				$rhours = intval( $hours - ($days * 24) );

				if ( $r['name'] == "" )
				{
					$r['name'] = "<em>Deleted Member</em>";
				}

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$r['name']."</b>$coppa" ,
																		 "<center>$where</center>",
																		 $r['email'],
																		 "<center>".$std->get_date( $r['entry_date'], 'JOINED' )."</center>",
																		 "<center>{$r['posts']}</center>",
																		 "<center>".$std->get_date( $r['joined'], 'JOINED' )."</center>",
																		 "<center><strong><span style='color:red'>$days d</span>, $rhours h</center>",
																		 "<center><input type='checkbox' name='mid_{$r['member_id']}' value='1'></center>"
															)      );
			}

			$ibforums->html .= $ibforums->adskin->add_td_basic( "$links", "left", "catrow2");

			$ibforums->html .= $ibforums->adskin->add_td_basic("<select name='type' id='dropdown'><option value='approve'>Approve these Accounts</option><option value='delete'>DELETE these accounts</option></select>", "center", "catrow2" );
		}

		$ibforums->html .= $ibforums->adskin->end_form("Go!");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}




	//-----------------------------------------
	//
	// MEMBER RANKS...
	//
	//-----------------------------------------

	function titles_recache()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['ranks'] = array();

		$DB->simple_construct( array( 'select' => 'id, title, pips, posts',
									  'from'   => 'titles',
									  'order'  => "posts DESC",
							)      );

		$DB->simple_exec();

		while ($i = $DB->fetch_row())
		{
			$ibforums->cache['ranks'][ $i['id'] ] = array(
														  'TITLE' => $i['title'],
														  'PIPS'  => $i['pips'],
														  'POSTS' => $i['posts'],
														);
		}

		$std->update_cache( array( 'name' => 'ranks', 'array' => 1, 'deletefirst' => 1 ) );
	}



	function titles_start()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Member Ranking Set Up";

		$ibforums->admin->page_detail = "This section allows you to modify, delete or add extra ranks.<br>If you wish to display pips below the members name, enter the number of pips. If you wish to use a custom image, simply enter the image name in the pips box. Note, these custom images must reside in the 'style_images/{img_dir}/folder_team_icons' directory of your installation";


		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Title"      , "30%" );
		$ibforums->adskin->td_header[] = array( "Min Posts"  , "10%" );
		$ibforums->adskin->td_header[] = array( "Pips"       , "20%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"     , "20%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"     , "20%" );

		//-----------------------------------------
		// Parse macro
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_default=1" ) );
		$DB->simple_exec();

		$mid = $DB->fetch_row();

		$DB->simple_construct( array( 'select' => 'macro_replace', 'from' => 'skin_macro', 'where' => "macro_set=1 AND macro_value='A_STAR'" ) );
		$DB->simple_exec();

    	$row = $DB->fetch_row();

    	$row['A_STAR'] = str_replace( "<#IMG_DIR#>", $mid['set_image_dir'], $row['macro_replace'] );

		$ibforums->html .= $ibforums->adskin->start_table( "Member Titles/Ranks" );

		//-----------------------------------------
		// Lets get on with it...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'titles', 'order' => "posts" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$img = "";

			if ( preg_match( "/^\d+$/", $r['pips'] ) )
			{
				for ($i = 1; $i <= $r['pips']; $i++)
				{
					$img .= $row['A_STAR'];

				}
			}
			else
			{
				$img = "<img src='style_images/{$mid['set_image_dir']}/folder_team_icons/{$r['pips']}' border='0'>";
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$r['title']."</b>" ,
																	 $r['posts'],
																	 $img,
																	 "<a href='{$ibforums->adskin->base_url}&act=mem&code=rank_edit&id={$r['id']}'>Edit</a>",
																	 "<a href='{$ibforums->adskin->base_url}&act=mem&code=rank_delete&id={$r['id']}'>Delete</a>",
															)      );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'do_add_rank'  ),
												 				 2 => array( 'act'   , 'mem'       ),
									   				    )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Add a Member Rank" );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rank Title</b>" ,
												  $ibforums->adskin->form_input( "title" )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Minimum number of posts needed</b>" ,
												  $ibforums->adskin->form_input( "posts" )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Number of pips</b><div class='graytext'>Or pip image - image must be uploaded into 'style_images/{img_dir}/folder_team_icons'</div>" ,
												  $ibforums->adskin->form_input( "pips" )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Add this rank");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	//-----------------------------------------

	function titles_add_rank()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// check for input
		//-----------------------------------------

		foreach( array( 'posts', 'title', 'pips' ) as $field )
		{
			if ($ibforums->input[ $field ] == "")
			{
				$ibforums->admin->error("You must complete the form fully");
			}
		}

		//-----------------------------------------
		// Add it to the DB
		//-----------------------------------------

		$DB->do_insert( 'titles', array(
										 'posts'  => trim($ibforums->input['posts']),
										 'title'  => trim($ibforums->input['title']),
										 'pips'   => trim($ibforums->input['pips']),
							  )       );

		$this->titles_recache();

		$ibforums->admin->done_screen("Rank Added", "Member Ranking Control", "act=mem&code=title", 'redirect' );


	}

	//-----------------------------------------

	function titles_delete_rank()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// check for input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("We could not match that ID");
		}

		$DB->simple_exec_query( array( 'delete' => 'titles', 'where' => "id='".$ibforums->input['id']."'" ) );

		$this->titles_recache();

		$ibforums->admin->save_log("Removed Rank Setting");

		$ibforums->admin->done_screen("Rank Removed", "Member Ranking Control", "act=mem&code=title", 'redirect' );

	}

	//-----------------------------------------

	function titles_edit_rank()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// check for input
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("We could not match that ID");
		}

		//-----------------------------------------

		foreach( array( 'posts', 'title', 'pips' ) as $field )
		{
			if ($ibforums->input[ $field ] == "")
			{
				$ibforums->admin->error("You must complete the form fully");
			}
		}

		//-----------------------------------------
		// Add it to the DB
		//-----------------------------------------

		$DB->do_update( 'titles', array (
										   'posts'  => trim($ibforums->input['posts']),
										   'title'  => trim($ibforums->input['title']),
										   'pips'   => trim($ibforums->input['pips']),
								) , "id='".$ibforums->input['id']."'"  );

		$this->titles_recache();

		$ibforums->admin->save_log("Edited Rank Setting");

		$ibforums->admin->done_screen("Rank Edited", "Member Ranking Control", "act=mem&code=title", 'redirect' );


	}

	//-----------------------------------------

	function titles_rank_setup($mode='edit')
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Member Rank Set Up";

		$ibforums->admin->page_detail = "If you wish to display pips below the members name, enter the number of pips. If you wish to use a custom image, simply enter the image name in the pips box. Note, these custom images must reside in the 'style_images/{img_dir}/folder_team_icons' directory of your installation";

		if ($mode == 'edit')
		{
			$form_code = 'do_rank_edit';

			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("No rank ID was set, please try again");
			}

			$DB->simple_construct( array( 'select' => '*', 'from' => 'titles', 'where' => "id='".$ibforums->input['id']."'" ) );
			$DB->simple_exec();

			$rank = $DB->fetch_row();

			$button = "Complete Edit";
		}
		else
		{
			$form_code = 'do_add_rank';
			$rank = array( 'posts' => "", 'title' => "", 'pips' => "");
			$button = "Add this rank";
		}

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $form_code  ),
																 2 => array( 'act'   , 'mem'       ),
																 3 => array( 'id'    , $rank['id'] ),
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Member Ranks" );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rank Title</b>" ,
												  $ibforums->adskin->form_input( "title", $rank['title'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Minimum number of posts needed</b>" ,
												  $ibforums->adskin->form_input( "posts", $rank['posts'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Number of pips</b><br>(Or pip image)" ,
												  $ibforums->adskin->form_input( "pips", $rank['pips'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------

	function member_prune_confirm($ids=array(), $query)
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Got members?
		//-----------------------------------------

		if ( count($ids) < 101)
		{
			foreach( $ids as $i => $n )
			{
				$member_arr[] = "<a href='index.php?showuser={$n[0]}' target='_blank'>{$n[1]}</a>";
			}
		}

		$ibforums->admin->page_title = "Member Pruning";

		$ibforums->admin->page_detail = "Please confirm your action.";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doprune' ),
												  				 2 => array( 'act'   , 'mem'     ),
												  				 3 => array( 'query' , str_replace( "'", '&#39;', $query ) ),
									     				 )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Member Prune Confirmation" );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Number of members to prune</b>" ,
												  				 count($ids)
									     				)      );

		if ( count($member_arr) > 0 )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Members to prune</b>" ,
												    		  implode( '<br />', $member_arr )
											                )      );
		}

		$ibforums->html .= $ibforums->adskin->end_form("Complete Member Pruning");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}


	//-----------------------------------------
	//
	// COMPLETE PRUNE
	//
	//-----------------------------------------

	function member_doprune()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Make sure we have *something*
		//-----------------------------------------

		$query = trim(urldecode($std->txt_stripslashes($_POST['query'])));

		$query = str_replace( "&lt;" , "<", $query );
		$query = str_replace( "&gt;" , ">", $query );
		$query = str_replace( '&#39;', "'", $query );

		if ($query == "")
		{
			$ibforums->admin->error("Prune query error, no query to use");
		}

		//-----------------------------------------
		// Get the member ids...
		//-----------------------------------------

		$ids = array();

		$DB->query($query);

		if ( $DB->get_num_rows() )
		{
			while ($i = $DB->fetch_row())
			{
				if ( $i['memid'] )
				{
					$ids[] = $i['memid'];
				}
				else if ( $i['id'] )
				{
					$ids[] = $i['id'];
				}
			}
		}
		else
		{
			$ibforums->admin->error("Could not find any members that matched the prune criteria");
		}

		$this->member_delete_do($ids);

		$ibforums->admin->done_screen("Member Account(s) Deleted", "Member Control", "act=mem" );

	}



	/*-------------------------------------------------------------------------*/
	//
	// DELETE MEMBER(S)
	//
	/*-------------------------------------------------------------------------*/

	function member_delete_do($id)
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Sort out thingie
		//-----------------------------------------

		if ( is_array( $id ) )
		{
			$mids = ' IN ('.implode(",",$id).')';
		}
		else
		{
			$mids = ' = '.$id;
		}

		//-----------------------------------------
		// Get avatars / photo
		//-----------------------------------------

		$delete_files = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'member_extra', 'where' => 'id'.$mids ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			if ( $r['photo_type'] == 'upload' and $r['photo_location'] )
			{
				$delete_files[] = $r['photo_location'];
			}

			if ( $r['avatar_type'] == 'upload' and $r['avatar_location'] )
			{
				$delete_files[] = $r['avatar_location'];
			}
		}

		//-----------------------------------------
		// Convert their posts and topics
		// into guest postings..
		//-----------------------------------------

		$DB->do_update( 'posts' , array( 'author_id'  => 0 ), "author_id".$mids );
		$DB->do_update( 'topics', array( 'starter_id' => 0 ), "starter_id".$mids );

		//-----------------------------------------
		// Delete member...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'pfields_content' , 'where' => "member_id".$mids ) );
		$DB->simple_exec_query( array( 'delete' => 'member_extra'    , 'where' => "id".$mids ) );
		$DB->simple_exec_query( array( 'delete' => 'members_converge', 'where' => "converge_id".$mids ) );

		//-----------------------------------------
		// Delete member messages...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'message_topics', 'where' => "mt_owner_id".$mids ) );
		$DB->simple_exec_query( array( 'delete' => 'contacts'      , 'where' => "member_id".$mids." or contact_id".$mids ) );

		//-----------------------------------------
		// Delete member subscriptions.
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'tracker'      , 'where' => "member_id".$mids ) );
		$DB->simple_exec_query( array( 'delete' => 'forum_tracker', 'where' => "member_id".$mids ) );
		$DB->simple_exec_query( array( 'delete' => 'warn_logs'    , 'where' => "wlog_mid" .$mids ) );

		//-----------------------------------------
		// Delete from validating..
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'validating', 'where' => "member_id".$mids ) );
		$DB->simple_exec_query( array( 'delete' => 'members'   , 'where' => "id".$mids ) );

		//-----------------------------------------
		// Delete avatars / photos
		//-----------------------------------------

		if ( count($delete_files) )
		{
			foreach( $delete_files as $file )
			{
				@unlink( $ibforums->vars['upload_dir']."/".$file );
			}
		}

		//-----------------------------------------
		// Get current stats...
		//-----------------------------------------

		$stats = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='stats'" ) );

		$stats = unserialize(stripslashes($stats['cs_value']));

		//-----------------------------------------
		// Rebuild stats
		//-----------------------------------------

		$r = $DB->simple_exec_query( array( 'select' => 'count(*) as members', 'from' => 'members', 'where' => "mgroup <> ".$ibforums->vars['auth_group'] ) );
		$stats['mem_count'] = intval( $r['members'] );

		$r = $DB->simple_exec_query( array( 'select' => 'id, name',
										    'from'   => 'members',
										    'where'  => "mgroup <> ".$ibforums->vars['auth_group'],
										    'order'  => 'id DESC',
										    'limit'  => array( 0, 1 )
								   )      );

		$stats['last_mem_name'] = $r['name'];
		$stats['last_mem_id']   = $r['id'];

		if ( count($stats) > 0 )
		{
			$DB->simple_exec_query( array( 'delete' => 'cache_store', 'where' => "cs_key='stats'" ) );
			$DB->do_insert( 'cache_store', array( 'cs_array' => 1, 'cs_key' => 'stats', 'cs_value' => addslashes(serialize($stats)) ) );
		}

		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);
			$this->modules->on_delete($id);
		}
	}

	/*-------------------------------------------------------------------------*/
	//
	// Delete Members
	//
	/*-------------------------------------------------------------------------*/

	function member_delete()
	{
		global $DB, $std, $ibforums;

		//-----------------------------------------
		// Check input
		//-----------------------------------------

		if ( ! $ibforums->input['mid'] )
		{
			$ibforums->main_msg = "No member found";
			$this->search_form();
		}

		//-----------------------------------------
		// Single or more?
		//-----------------------------------------

		if ( strstr( $ibforums->input['mid'], ',' ) )
		{
			$ids = explode( ',', $ibforums->input['mid'] );
		}
		else
		{
			$ids = array( $ibforums->input['mid'] );
		}

		//-----------------------------------------
		// Get accounts
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => 'id IN ('.implode(",",$ids).')' ) );
		$DB->simple_exec();

		$names = array();

		while ( $r = $DB->fetch_row() )
		{
			$names[] = $r['name'];
		}

		//-----------------------------------------
		// Check
		//-----------------------------------------

		if ( ! count( $names ) )
		{
			$ibforums->main_msg = "No member(s) found";
			$this->search_form();
		}

		//-----------------------------------------
		// Delete
		//-----------------------------------------

		$this->member_delete_do( $ids );

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_query = "";

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_query .= '&'.$bit.'='.trim($ibforums->input[ $bit ]);
		}

		$ibforums->admin->save_log("Deleted Member(s) ( ".implode(",",$names)." )");

		$ibforums->admin->done_screen("Member(s) Deleted", "Member Search", "act=mem".$page_query, "redirect" );

	}


	//-----------------------------------------
	//
	// ADD MEMBER FORM
	//
	//-----------------------------------------

	function member_add_form()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Pre Register a member";

		$ibforums->admin->page_detail = "You may pre-register members using this form.";

		$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'order' => "g_title" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			if ($ibforums->vars['admin_group'] == $r['g_id'])
			{
				if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
				{
					continue;
				}
			}

			$mem_group[] = array( $r['g_id'] , $r['g_title'] );
		}

		//-----------------------------------------
		// Custom profile fields stuff
		//-----------------------------------------

		$required_output = "";
		$optional_output = "";
		$custom_output = "";

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->cache_data  = $ibforums->cache['profilefields'];

    	$fields->init_data();
    	$fields->parse_to_register();

    	if ( count( $fields->out_fields ) )
    	{
    		$ibforums->html .= $ibforums->adskin->add_td_basic( "Custom Profile Fields", "left", "pformstrip" );

			foreach( $fields->out_fields as $id => $data )
			{
				if ( $fields->cache_data[ $id ]['pf_type'] == 'drop' )
				{
					$form_element =  "<select class='dropdown' name='field_{$id}'>{$data}</select>";
				}
				else if ( $fields->cache_data[ $id ]['pf_type'] == 'area' )
				{
					$form_element = $ibforums->adskin->form_textarea( 'field_'.$id, $data );
				}
				else
				{
					$form_element = $ibforums->adskin->form_input( 'field_'.$id, $data );
				}

				$custom_out .= $ibforums->adskin->add_td_row( array( "<b>{$fields->field_names[ $id ]}</b><div class='graytext'>{$fields->field_desc[ $id ]}</div>" , $form_element ) );
			}
		}

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doadd' ),
														    	  2 => array( 'act'   , 'mem'     ),
									  				    )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Member Registration" );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member Name</b>" ,
												  $ibforums->adskin->form_input( "name", $_POST['name'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Password</b>" ,
												  $ibforums->adskin->form_input( "password", $_POST['password'], 'password' )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email Address</b>" ,
												  $ibforums->adskin->form_input( "email", $_POST['email'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member Group</b>" ,
												  $ibforums->adskin->form_dropdown( "mgroup",
																		$mem_group,
												  						$_POST['mgroup'] ? $_POST['mgroup'] : $ibforums->vars['member_group']
												  					  )
									     )      );

		if ($custom_out != "")
		{
			$ibforums->html .= $custom_out;
		}

		$ibforums->html .= $ibforums->adskin->end_form("Register Member");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------
	//
	// Add member
	//
	//-----------------------------------------

	function member_do_add()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check form
		//-----------------------------------------

		foreach( array('name', 'password', 'email', 'mgroup') as $field )
		{
			if ($ibforums->input[ $field ] == "")
			{
				$ibforums->admin->error("You must complete the form fully!");
			}
		}

		//-----------------------------------------
		// Do we already have such a member?
		//-----------------------------------------

		$DB->cache_add_query( 'login_getmember', array( 'username' => strtolower($ibforums->input['name']) ) );
		$DB->cache_exec_query();

		if ( $DB->get_num_rows() )
		{
			$ibforums->main_msg = "We already have a member by that name, please select another";
			$this->member_add_form();
		}

		//-----------------------------------------
		// Is this email addy taken? CONVERGE THIS??
		//-----------------------------------------

		$in_username = trim($ibforums->input['name']);
		$in_password = trim($ibforums->input['password']);
		$in_email    = trim(strtolower($ibforums->input['email']));

		$DB->simple_construct( array( 'select' => 'id', 'from' => 'members', 'where' => "email='".$in_email."'" ) );
		$DB->simple_exec();

		$email_check = $DB->fetch_row();

		if ($email_check['id'])
		{
			$ibforums->main_msg = "We already have a member with that email address, please choose another email address";
			$this->member_add_form();
		}

		$member = array(
						 'name'             => $in_username,
						 'member_login_key' => $ibforums->converge->generate_auto_log_in_key(),
						 'email'            => $in_email,
						 'mgroup'           => $ibforums->input['mgroup'],
						 'posts'            => 0,
						 'joined'           => time(),
						 'ip_address'       => $ibforums->input['IP_ADDRESS'],
						 'time_offset'      => $ibforums->vars['time_offset'],
						 'view_sigs'        => 1,
						 'email_pm'         => 1,
						 'view_img'         => 1,
						 'view_avs'         => 1,
						 'restrict_post'    => 0,
						 'view_pop'         => 1,
						 'msg_total'        => 0,
						 'new_msg'          => 0,
						 'coppa_user'       => 0,
						 'language'         => $ibforums->vars['default_language'],
					   );

		$salt     = $ibforums->converge->generate_password_salt(5);
		$passhash = $ibforums->converge->generate_compiled_passhash( $salt, md5($in_password) );

		$converge = array( 'converge_email'     => $in_email,
						   'converge_joined'    => time(),
						   'converge_pass_hash' => $passhash,
						   'converge_pass_salt' => str_replace( '\\', "\\\\", $salt )
						 );

		//-----------------------------------------
		// Insert: CONVERGE
		//-----------------------------------------

		$DB->do_insert( 'members_converge', $converge );

		//-----------------------------------------
		// Get converges auto_increment user_id
		//-----------------------------------------

		$member_id    = $DB->get_insert_id();
		$member['id'] = $member_id;

		//-----------------------------------------
		// Insert: MEMBERS
		//-----------------------------------------

		$DB->force_data_type = array( 'name' => 'string' );

		$DB->do_insert( 'members', $member );

		//-----------------------------------------
		// Insert: MEMBER EXTRA
		//-----------------------------------------

		$DB->do_insert( 'member_extra', array( 'id' => $member_id, 'vdirs' => 'in:Inbox|sent:Sent Items' ) );

		//-----------------------------------------
		// Insert into the custom profile fields DB
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];

    	$fields->cache_data  = $ibforums->cache['profilefields'];
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_save(1);

		//-----------------------------------------
		// Custom profile field stuff
		//-----------------------------------------

		$fields->out_fields['member_id'] = $member['id'];

		$DB->simple_exec_query( array( 'delete' => 'pfields_content', 'where' => 'member_id='.$member['id'] ) );

		$DB->do_insert( 'pfields_content', $fields->out_fields );

		//-----------------------------------------
		// stats
		//-----------------------------------------

		$stats = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='stats'" ) );

		$stats = unserialize(stripslashes($stats['cs_value']));

		$stats['last_mem_name'] = $in_username;
		$stats['last_mem_id']   = $member['id'];

		if ( count($stats) > 0 )
		{
			$ibforums->cache['stats'] = $stats;
			$std->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 1 ) );
		}

		//-----------------------------------------
		// Log and bog?
		//-----------------------------------------

		$ibforums->admin->save_log("Created new member account for '{$ibforums->input['name']}'");

		$ibforums->input['searchtype'] = 'normal';
		$ibforums->input['gotcount']   = 1;

		$this->search_results();

		//$ibforums->admin->done_screen("Member Account Created", "Member Control", "act=mem&code=edit", 'redirect' );

	}


	/*-------------------------------------------------------------------------*/
	//
	// SEARCH FORM, SEARCH FOR MEMBER
	//
	/*-------------------------------------------------------------------------*/

	function search_form()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Saved results?
		//-----------------------------------------

		if ( ( $ibforums->input['gotcount'] > 1 and $ibforums->input['fromdel'] ) or ( $ibforums->input['gotcount'] and ! $ibforums->input['fromdel'] ) )
		{
			$ibforums->input['searchtype'] = 'normal';
			$this->search_results();
		}

		$ibforums->admin->page_title = "Edit a member";

		$ibforums->admin->page_detail = "Search for a member.";

		$mem_group = array( 0 => array( 'all', 'Any Group') );

		$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'order' => "g_title" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$mem_group[] = array( $r['g_id'] , $r['g_title'] );
		}

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'searchresults' ),
															     2 => array( 'act'   , 'mem'     ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------
		// Printy poos
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Member Search" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member's Name</b><div class='graytext'>This can be left blank if you're using more options below</div>",
												  				 $ibforums->adskin->form_dropdown( 'namewhere', array( 0 => array( 'begin'   , 'Begins with' ),
												  				 													   1 => array( 'is'      , 'Is'          ),
												  				 													   2 => array( 'contains', 'Contains'    ),
												  				 													   3 => array( 'ends'    , 'Ends with'   )
												  				 													 ), $_POST['namewhere']
												  				 								 )
												  				 .'&nbsp;'. $ibforums->adskin->form_input( "name", $_POST['name'] )
									     				)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b><u>OR</u> Member's ID is...</b>" ,
												                 $ibforums->adskin->form_input( "memberid", $_POST['mid'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Type of Search</b>" ,
												                 $ibforums->adskin->form_dropdown( "searchtype", array( 0 => array( 'normal', 'Find Members to Edit or Delete' ),
												                 													    1 => array( 'prune' , 'Find Members to Prune (Mass Delete)' )
												                 													  ), $_POST['searchtype'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_basic( "Optional Search Parameters", "left", "pformstrip" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email Address contains...</b>" ,
												                 $ibforums->adskin->form_input( "email", $_POST['email'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member Suspended</b>" ,
												                 $ibforums->adskin->form_dropdown( "suspended", array( 0=>array('0','Either'),1=>array('yes', 'Yes'),2=>array('no', 'No') ), $_POST['suspended'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>IP Address contains...</b>" ,
												                 $ibforums->adskin->form_input( "ip_address", $_POST['ip_address'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>AIM name contains...</b>" ,
												                 $ibforums->adskin->form_input( "aim_name", $_POST['aim_name'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>ICQ Number contains...</b>" ,
												                 $ibforums->adskin->form_input( "icq_number", $_POST['icq_number'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Yahoo! Identity contains...</b>" ,
												                 $ibforums->adskin->form_input( "yahoo", $_POST['yahoo'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Signature contains...</b>" ,
												                 $ibforums->adskin->form_input( "signature", $_POST['signature'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Less than <em>n</em> posts</b>" ,
												                 $ibforums->adskin->form_input( "posts", $_POST['posts'] )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Registered Between (MM-DD-YYYY)</b><div class='graytext'>Leave the first box blank to range from the earliest record and leave the last box blank to range to the current time now</div>",
												                 $ibforums->adskin->form_simple_input( "registered_first", $_POST['registered_first'], 10 ). ' to ' .$ibforums->adskin->form_simple_input( "registered_last", $_POST['registered_last'], 10 )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Last Post Between (MM-DD-YYYY)</b><div class='graytext'>Leave the first box blank to range from the earliest record and leave the last box blank to range to the current time now</div>" ,
												                 $ibforums->adskin->form_simple_input( "last_post_first", $_POST['last_post_first'], 10 ). ' to ' . $ibforums->adskin->form_simple_input( "last_post_last", $_POST['last_post_last'], 10 )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Last Active Between (MM-DD-YYYY)</b><div class='graytext'>Leave the first box blank to range from the earliest record and leave the last box blank to range to the current time now</div>" ,
												                 $ibforums->adskin->form_simple_input( "last_activity_first", $_POST['last_activity_first'], 10 ). ' to ' . $ibforums->adskin->form_simple_input( "last_activity_last", $_POST['last_activity_last'], 10 )
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Is in group...</b>" ,
												                 $ibforums->adskin->form_dropdown( "mgroup", $mem_group, $_POST['mgroup'] )
									                    )      );

		//-----------------------------------------
    	// Get custom profile information
    	//-----------------------------------------

    	require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_edit();

    	if ( count( $fields->out_fields ) )
    	{
    		$ibforums->html .= $ibforums->adskin->add_td_basic( "Custom Profile Fields", "left", "pformstrip" );

			foreach( $fields->out_fields as $id => $data )
			{
				if ( $fields->cache_data[ $id ]['pf_type'] == 'drop' )
				{
					$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$fields->field_names[ $id ]."</b>" ,
												                			 "<select class='dropdown' name='cm_field_{$id}'><option value=''>Any...</option>{$data}</select>"
									                    			)      );
				}
				else
				{
					$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$fields->field_names[ $id ]."</b>" ,
												                 			 $ibforums->adskin->form_simple_input('cm_field_'.$id, $_POST['field_'.$id], 10 )
												                 	)      );
				}
			}
		}

		$ibforums->html .= $ibforums->adskin->end_form("Find Member");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	/*-------------------------------------------------------------------------*/
	//
	// SEARCH RESULTS
	//
	/*-------------------------------------------------------------------------*/

	function search_results()
	{
		global $ibforums, $DB,  $std;

		$page_query = "";
		$un_all     = "";

		$query = array();

		//-----------------------------------------
		// Member extra?
		//-----------------------------------------

		$member_extra = array( 'aim_name', 'icq_number', 'yahoo', 'signature' );
		$date_keys    = array( 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last' );

		//-----------------------------------------
		// Loopy loo
		//-----------------------------------------

		foreach( array('name', 'memberid', 'email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup') as $bit )
		{
			$ibforums->input[ $bit ] = urldecode(trim($ibforums->input[ $bit ]));

			$page_query .= '&'.$bit.'='.urlencode($ibforums->input[ $bit ]);

			//-----------------------------------------
			// Table?
			//-----------------------------------------

			$table_prefix = in_array( $bit, $member_extra ) ? 'me.' : 'm.';

			if ( $ibforums->input[ $bit ] )
			{
				//-----------------------------------------
				// Time / Date
				//-----------------------------------------

				if ( in_array( $bit, $date_keys ) )
				{
					list( $month, $day, $year ) = explode( '-', $ibforums->input[ $bit ] );

					if ( ! checkdate( $month, $day, $year ) )
					{
						$ibforums->main_msg = "Date out of range (Month: $month, Day: $day, Year: $year). Dates should be in MM-DD-YYYY";
						$this->search_form();
					}

					$time_int = mktime( 0, 0 ,0,$month, $day, $year );
					$tmp_bit  = str_replace( '_first'    , '', $bit );
					$tmp_bit  = str_replace( '_last'     , '', $tmp_bit );
					$tmp_bit  = str_replace( 'registered', 'joined', $tmp_bit );

					if ( strstr( $bit, '_first' ) )
					{
						$query[] = $table_prefix.$tmp_bit.' > '.$time_int;
					}
					else
					{
						$query[] = $table_prefix.$tmp_bit.' < '.$time_int;
					}
				}
				else if ($bit == 'mgroup')
				{
					if ($ibforums->input['mgroup'] != 'all')
					{
						$query[] = $table_prefix."mgroup=".$ibforums->input['mgroup'];
					}
				}
				else if ($bit == 'posts')
				{
					$query[] = $table_prefix."posts <".$ibforums->input[$bit];
				}
				else if ($bit == 'suspended')
				{
					if ( $ibforums->input[$bit] == 'yes' )
					{
						$query[] = $table_prefix."temp_ban > 0";
					}
					else if ( $ibforums->input[$bit] == 'no' )
					{
						$query[] = $table_prefix."temp_ban < 1 or temp_ban='' or temp_ban is null";
					}
				}
				else if ($bit == 'name')
				{
					$start_bit = '%';
					$end_bit   = '%';

					if ( $ibforums->input['namewhere'] == 'begin' )
					{
						$start_bit = '';
					}
					else if ( $ibforums->input['namewhere'] == 'ends' )
					{
						$end_bit   = '';
					}
					else if ( $ibforums->input['namewhere'] == 'is' )
					{
						$end_bit   = '';
						$start_bit = '';
					}

					$query[] = $table_prefix.$bit." LIKE '".$start_bit.$ibforums->input[$bit].$end_bit."'";
				}
				else if ($bit == 'memberid')
				{
					$query[] = $table_prefix."id=".intval($ibforums->input[$bit]);
				}
				else
				{
					$query[] = $table_prefix.$bit." LIKE '%".$ibforums->input[$bit]."%'";
				}
			}
		}

		//-----------------------------------------
		// Custom fields...
		//-----------------------------------------

		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^cm_field_(\d+)$/", $key, $match ) )
 			{
 				if ( $ibforums->input[ $match[0] ] )
 				{
 					$query[]     = 'p.field_'.$match[1]." LIKE '%".$ibforums->input[ $match[0] ]."%'";
 					$page_query .= '&cm_field_'.$match[1].'='.urlencode($ibforums->input[ $match[0] ]);
 				}
 			}
 		}

		//-----------------------------------------
		// get 'owt?
		//-----------------------------------------

		if ( count($query) )
		{
			$rq = ' WHERE '.implode( " AND ", $query );
		}

		//-----------------------------------------
		// On with the show
		//-----------------------------------------

		$st = intval($ibforums->input['st']);

		$DB->cache_add_query( 'member_search_form_one', array( 'rq' => $rq, 'st' => $st ) );
		$query = $DB->cur_query;
		$DB->flush_query();

		$DB->cache_add_query( 'member_search_form_two', array( 'rq' => $rq ) );
		$pquery = $DB->cur_query;
		$DB->flush_query();

		//-----------------------------------------
		// Get the number of results
		//-----------------------------------------

		$DB->cache_add_query( 'member_search_form_count', array( 'rq' => $rq ) );
		$DB->cache_exec_query();

		$count = $DB->fetch_row();

		if ($count['count'] < 1)
		{
			$ibforums->main_msg = "Your search query did not return any matches from the member database.";
			$this->search_form();
		}

		//-----------------------------------------
		// Prune you fookers?
		//-----------------------------------------

		if ( $ibforums->input['searchtype'] != 'normal' )
		{
			$ids = array();

			$DB->query($pquery);

			while ( $r = $DB->fetch_row() )
			{
				$ids[ $r['id'] ] = array( $r['id'], $r['name'] );
			}

			$this->member_prune_confirm($ids, $pquery );
			exit();
		}

		$page_query .= '&searchtype=normal&namewhere='.$ibforums->input['namewhere'].'&gotcount='.$count['count'];

		$ibforums->admin->page_title = "Your Member Search Results";

		$ibforums->admin->page_detail = "Your search results.";

		//-----------------------------------------

		$pages = $std->build_pagelinks( array( 'TOTAL_POSS'  => $count['count'],
											   'PER_PAGE'    => 25,
											   'CUR_ST_VAL'  => $ibforums->input['st'],
											   'L_SINGLE'    => $un_all."Single Page",
											   'L_MULTI'     => $un_all."Multi Page",
											   'BASE_URL'    => $ibforums->adskin->base_url."&act=mem&showsusp={$ibforums->input['showsusp']}&code={$ibforums->input['code']}".$page_query,
											 )
									  );

		//-----------------------------------------
		// Run the query
		//-----------------------------------------

		$ibforums->html .= "
							<div class='tableborder'>
							 <div class='maintitle'>Member Search Results: {$count['count']} result(s) found</div>
							 <table cellpadding='4' cellspacing='0' border='0' width='100%'>
						   ";

		$per_row  = 3;
		$td_width = 100 / $per_row;
		$count    = 0;
		$people   = "<tr align='center'>\n";

		$DB->query($query);

		while ( $r = $DB->fetch_row() )
		{
			$count++;

			$r['id'] = $r['memid'];

			if ( ! $r['temp_ban'] )
			{
				$class = 'tdrow1';

				$suspend_html = "<tr>
								 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_suspend.gif' border='0' /><td>
								 <td width='99%'><a style='text-decoration:none' href='{$ibforums->adskin->base_url}&act=mem&code=banmember&mid={$r['id']}{$page_query}' title='Suspend Member'>Suspend Member</a></td>
								</tr>";

			}
			else
			{
				$s_ban = $std->hdl_ban_line( $r['temp_ban'] );

				$class = 'tdrow2';

				$suspend_html = "<tr>
								 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_suspend.gif' border='0' /><td>
								 <td width='99%'><strong><a style='text-decoration:none' href='{$ibforums->adskin->base_url}&act=mem&code=unsuspend&mid={$r['id']}{$page_query}'>Unsuspend</a></strong>
								 				 <span style='font-size:10px' class='graytext'>(".$ibforums->admin->get_date( $s_ban['date_end'], 'LONG') .$sus_link.")</span>
								 </td>
								</tr>";
			}

			//-----------------------------------------
			// Avatar?
			//-----------------------------------------

			if ( $r['avatar_location'] and $r['avatar_type'] )
			{
				$avatar = $std->get_avatar( $r['avatar_location'], 1, '25x25', $r['avatar_type'] );

				if ( ! strstr( $avatar, 'width=' ) )
				{
					$avatar = str_replace( '<img', "<img width='25' height='25'", $avatar );
				}
			}
			else
			{
				$avatar = "<img src='{$ibforums->adskin->img_url}/memsearch_head.gif' border='0' />";
			}

			$joined = $std->get_date( $r['joined'], 'JOINED' );

			$people .= "<td width='{$td_width}%' align='left' class='$class'>
						  <fieldset>
						  	<legend><strong>{$r['name']}</strong></legend>
						  	<div style='border:1px solid #BBB;background-color:#FFF;margin:2px;padding:1px'>
						  	<table cellpadding='2' cellspacing='0' border='0' width='100%'>
						  	<tr>
						  	 <td width='1%' align='center'>{$avatar}<td>
						  	 <td width='99%'>
						  	  <a style='font-size:12px;font-weight:bold' title='View this members profile' href='index.{$ibforums->vars['php_ext']}?showuser={$r['id']}' target='blank'>{$r['name']}</a>
						  	  &nbsp;<span style='font-size:10px' class='graytext'>({$r['ip_address']})</span>
						  	 </td>
						  	</tr>
						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_email.gif' border='0' /><td>
						  	 <td width='99%'><strong>{$r['email']}</strong></td>
						  	</tr>
						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_group.gif' border='0' /><td>
						  	 <td width='99%'><strong>{$ibforums->cache['group_cache'][$r['mgroup']]['g_title']}</strong> <span style='font-size:10px' class='graytext'>({$r['posts']} Posts)</span></td>
						  	</tr>
						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_posts.gif' border='0' /><td>
						  	 <td width='99%'><strong>Joined: {$joined}</strong></td>
						  	</tr>
						  	</table>
						  	</div>
						  	<div style='border:1px solid #BBB;background-color:#EEE;margin:2px;padding:1px'>
						  	<table cellpadding='4' cellspacing='0' border='0' width='100%'>
						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_changename.gif' border='0' /><td>
						  	 <td width='99%'><strong><a style='text-decoration:none' href='{$ibforums->adskin->base_url}&act=mem&code=doform&mid={$r['id']}{$page_query}' title='Edit this members account'>Edit Member's Profile</a></strong></td>
						  	</tr>
						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_changename.gif' border='0' /><td>
						  	 <td width='99%'><a style='text-decoration:none' href='{$ibforums->adskin->base_url}&act=mem&code=changename&mid={$r['id']}{$page_query}' title='Change this members name'>Change Member's Name</a></td>
						  	</tr>
						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_changename.gif' border='0' /><td>
						  	 <td width='99%'><a style='text-decoration:none' href='{$ibforums->base_url}&act=mem&code=changepassword&id={$r['id']}{$page_query}'>Change/Reset Password</a></td>
						  	</tr>

						  	{$suspend_html}

						  	<tr>
						  	 <td width='1%' align='center'><img src='{$ibforums->adskin->img_url}/memsearch_delete.gif' border='0' /><td>
						  	 <td width='99%'><a style='text-decoration:none' href='#' onclick='maincheckdelete(\"{$ibforums->adskin->base_url}&act=mem&code=member_delete&fromdel=1&mid={$r['id']}{$page_query}\"); return false;' title='Delete Member'>Delete Member</a></td>
						  	</tr>
						   </table>
						   </div>
						  </fieldset>
						 </td>";

			if ($count == $per_row )
			{
				$people .= "</tr>\n\n<tr align='center'>";
				$count   = 0;
			}
		}

		if ( $count > 0 and $count != $per_row )
		{
			for ($i = $count ; $i < $per_row ; ++$i)
			{
				$people .= "<td class='tdrow2'>&nbsp;</td>\n";
			}

			$people .= "</tr>";
		}


		$ibforums->html .= $people;

		$ibforums->html .= "</table>
							<div class='pformstrip' align='right'>{$pages}</div></div>";

		$ibforums->admin->output();


	}

	//-----------------------------------------
	//
	// DO EDIT FORM
	//
	//-----------------------------------------

	function member_do_edit_form()
	{
		global $ibforums, $DB,  $std, $ibforums;

		require_once( ROOT_PATH."sources/lib/post_parser.php" );

		$parser = new post_parser();

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("Could not resolve member id");
		}

		//-----------------------------------------
		// get member info
		//-----------------------------------------

		$DB->cache_add_query( 'member_search_do_edit_form', array( 'mid' => $ibforums->input['mid'] ) );
		$DB->cache_exec_query();

		$mem = $DB->fetch_row();

		$mem['id'] = $mem['memid'];

		//-----------------------------------------
		// check
		//-----------------------------------------

		if ( ! $mem['id'] )
		{
			$ibforums->admin->error("Could not resolve member id");
		}

		$mem_group = array();
		$show_fixed = FALSE;

		$units = array( 0 => array( 'h', 'Hours' ), 1 => array( 'd', 'Days' ) );

		//-----------------------------------------
		// Get groups (USE CACHE FOR CRAPS SAKE)
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'order' => "g_title" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			//-----------------------------------------
			// Ensure only root admins can promote to root admin grou...
			// oh screw it, I can't be bothered explaining stuff tonight
			//-----------------------------------------

			if ($ibforums->vars['admin_group'] == $r['g_id'])
			{
				if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
				{
					continue;
				}
			}

			$mem_group[] = array( $r['g_id'] , $r['g_title'] );
		}

		//-----------------------------------------
		// is this a non root editing a root?
		//-----------------------------------------

		if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
		{
			if ($mem['mgroup'] == $ibforums->vars['admin_group'])
			{
				$show_fixed = TRUE;
			}
		}

		//-----------------------------------------
		// Get langs (USE CACHE DICKHEAD)
		//-----------------------------------------

		$lang_array = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'languages' ) );
		$DB->simple_exec();

		while ( $l = $DB->fetch_row() )
		{
			$lang_array[] = array( $l['ldir'], $l['lname'] );
		}

 		//-----------------------------------------
 		// Get Skins (CACHE)
 		//-----------------------------------------

 		require_once( ROOT_PATH.'sources/classes/class_display.php' );
 		$print = new display();

 		$tmp = $ibforums->skin['_setid'];

 		$ibforums->skin['_setid'] = $mem['skin'];

 		$skin_list = $print->_build_skin_list();

 		$ibforums->skin['_setid'] = $tmp;

 		//-----------------------------------------
		// Fix up langs
		//-----------------------------------------

		if ($ibforums->vars['default_language'] == "")
		{
			$ibforums->vars['default_language'] = 'en';
		}

		//-----------------------------------------
    	// Get custom profile information
    	//-----------------------------------------

		$custom_out = "";

    	require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];
    	$fields->member_data = $mem;
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_edit();

    	if ( count( $fields->out_fields ) )
    	{
    		//$ibforums->html .= $ibforums->adskin->add_td_basic( "Custom Profile Fields", "left", "pformstrip" );

			foreach( $fields->out_fields as $id => $data )
			{
				if ( $fields->cache_data[ $id ]['pf_type'] == 'drop' )
				{
					$form_element =  "<select class='dropdown' name='field_{$id}'>{$data}</select>";
				}
				else if ( $fields->cache_data[ $id ]['pf_type'] == 'area' )
				{
					$form_element = $ibforums->adskin->form_textarea( 'field_'.$id, $data );
				}
				else
				{
					$form_element = $ibforums->adskin->form_input( 'field_'.$id, $data );
				}

				$custom_out .= $ibforums->adskin->add_td_row( array( "<b>{$fields->field_names[ $id ]}</b><div class='graytext'>{$fields->field_desc[ $id ]}</div>" , $form_element ) );
			}
		}

		//-----------------------------------------
		// Perms masks section
		//-----------------------------------------

		$perm_masks = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$perm_masks[] = array( $r['perm_id'], $r['perm_name'] );
		}

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->admin->page_title = "Edit member: ".$mem['name']." (ID: ".$mem['id'].")";

		$ibforums->admin->page_detail = "You may alter the members settings from here.";

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_array = array( 1 => array( 'code'    , 'doedit'        ),
							 2 => array( 'act'     , 'mem'           ),
							 3 => array( 'mid'     , $mem['id']      ),
							 4 => array( 'curemail', $mem['email']   ),
							 5 => array( 'curgroup', $mem['mgroup']  ),
						   );

		foreach( array('name','email','ip_address','aim_name','icq_number','yahoo','signature','posts','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_array[] = array( $bit, trim($ibforums->input[ $bit ]) );
		}

		$ibforums->html .= $ibforums->adskin->start_form( $page_array );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------
		// SECURITY
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Member Security Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>IP address when registered</b>", "<a href='{$ibforums->base_url}&act=mtools&code=learnip&ip={$mem['ip_address']}' title='Find more out about this IP address...'>{$mem['ip_address']}</a>"
															    ." [ <a href='{$ibforums->base_url}&act=mtools&code=showallips&member_id={$mem['id']}'>Show all IP addresses</a> ]"
													    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Remove member's photo</b>" ,
												                 $ibforums->adskin->form_checkbox("remove_photo", 0)
									     		        )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Warn Level</b>" ,
													      		  $ibforums->adskin->form_input("warn_level", $mem['warn_level'])
									    		  	    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Member Title</b>" ,
												  			     $ibforums->adskin->form_input("title", $mem['title'])
									     			    )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Group opts
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Member Group Options" );

		if ($show_fixed != TRUE)
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Primary Member Group</b><br /><div style='color:gray'>Member will appear to be a member of this group to others</div>" ,
																   $ibforums->adskin->form_dropdown( "mgroup", $mem_group, $mem['mgroup'] )
														  )      );

			$arr = explode( ",", $mem['mgroup_others'] );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Secondary Member Groups</b><br />You can select more than one other group.<div style='color:gray'>Member will inherit 'better' permissions of all secondary groups and will inherit permission masks of all secondary groups in positive favor.</div>" ,
																   $ibforums->adskin->form_multiselect( "mgroup_others[]", $mem_group, $arr, 5 )
														  )      );
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Primary Member Group</b>" ,
													  $ibforums->adskin->form_hidden( array( 1 => array( 'mgroup' , $mem['mgroup'] ) ) )."<b>Root Admin</b> (Can't Change)",
											 )      );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Posting
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Posting and Access Restrictions" );

		//-----------------------------------------
		// Sort out perm id stuff
		//-----------------------------------------

		$ibforums->html .=
		"<script type='text/javascript'>

			var show   = '';
		";

		foreach ($perm_masks as $id => $d)
		{
			$ibforums->html .= " 		perms_$d[0] = '$d[1]';\n";
		}

		$ibforums->html .=
		"

		 	function saveit(f)
		 	{
		 		show = '';
		 		for (var i = 0 ; i < f.options.length; i++)
				{
					if (f.options[i].selected)
					{
						tid  = f.options[i].value;
						show += '\\n' + eval('perms_'+tid);
					}
				}

				if ( show != '' )
				{
					document.forms[0].override.checked = true;
				}
				else
				{
					document.forms[0].override.checked = false;
				}
			}

			function show_me()
			{
				if (show == '')
				{
					show = 'No change detected\\nClick on the multi-select box to activate';
				}

				alert('Selected Permission Masks\\n---------------------------------\\n' + show);
			}

		</script>";

		$arr = explode( ",",$mem['org_perm_id'] );

		$ch_ch = ( $mem['org_perm_id'] ) ? 'checked' : '';

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Override group forum permission mask with...</b><br />You may choose more than one.<div style='color:gray'>This will override all permission settings for the primary group and any secondary groups.</div>" ,
																 "<input type='checkbox' name='override' value='1' $ch_ch> <b>Override Group Permission Mask with...</b><br>".
																 $ibforums->adskin->form_multiselect( "permid[]",
																					   $perm_masks,
																					   $arr, 5, 'onfocus="saveit(this)" onchange="saveit(this)"'
																					 )."<br><input style='margin-top:5px' id='editbutton' type='button' onclick='show_me();' value='Show me selected masks'>"
														) , "subforum"   );

		//-----------------------------------------
		// Mod posts bit
		//-----------------------------------------

		$mod_tick = 0;
		$mod_arr  = array();

		if ( $mem['mod_posts'] == 1 )
		{
			$mod_tick = 'checked';
		}
		elseif ($mem['mod_posts'] > 0)
		{
			$mod_arr = $std->hdl_ban_line( $mem['mod_posts'] );

			$hours  = ceil( ( $mod_arr['date_end'] - time() ) / 3600 );

			if ( $hours > 24 and ( ($hours / 24) == ceil($hours / 24) ) )
			{
				$mod_arr['units']    = 'd';
				$mod_arr['timespan'] = $hours / 24;
			}
			else
			{
				$mod_arr['units']    = 'h';
				$mod_arr['timespan'] = $hours;
			}

			$mod_extra = "<br /><span style='color:red'>Restriction in progress - remaining time has been recalculated</span>";
		}


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Require moderator preview of all posts by this member?</b><br>If yes, all posts by this member will be put into the moderation queue. Untick box and clear number box to remove." ,
																 "<input type='checkbox' name='mod_indef' value='1' $mod_tick> Moderator Preview indefinitely
																 <br /><b>or for</b> ".$ibforums->adskin->form_input('mod_timespan', $mod_arr['timespan'], "text", "", '5' ) . '&nbsp;' . $ibforums->adskin->form_dropdown('mod_units', $units, $mod_arr['units'] ).$mod_extra
														)      );


		$post_tick = 0;
		$post_arr  = array();

		if ( $mem['restrict_post'] == 1 )
		{
			$post_tick = 'checked';
		}
		else if( $mem['restrict_post'] > 0 )
		{
			$post_arr = $std->hdl_ban_line( $mem['restrict_post'] );

			$hours  = ceil( ( $post_arr['date_end'] - time() ) / 3600 );

			if ( $hours > 24 and ( ($hours / 24) == ceil($hours / 24) ) )
			{
				$post_arr['units']    = 'd';
				$post_arr['timespan'] = $hours / 24;
			}
			else
			{
				$post_arr['units']    = 'h';
				$post_arr['timespan'] = $hours;
			}

			$post_extra = "<br /><span style='color:red'>Restriction in progress - remaining time has been recalculated</span>";
		}

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Restrict {$mem['name']} from posting?</b><br>Untick box and clear number box to remove restriction." ,
																 "<input type='checkbox' name='post_indef' value='1' $post_tick> Restrict posting indefinitely
																 <br /><b>or for</b> ".$ibforums->adskin->form_input('post_timespan', $post_arr['timespan'], "text", "", '5' ) . '&nbsp;' . $ibforums->adskin->form_dropdown('post_units', $units, $post_arr['units'] ).$post_extra
														) , "subforum"     );


		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		// Settings
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Board Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Language Choice</b>" ,
												  $ibforums->adskin->form_dropdown( "language",
																		$lang_array,
												  						$mem['language'] != "" ? $mem['language'] : $ibforums->vars['default_language']
												  					  )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Skin Choice</b>" , "<select name='skin' class='dropdown'><option value='0'>--None / Use Board Default--</option>$skin_list</select>"
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Hide this members email address?</b>" ,
												  $ibforums->adskin->form_yes_no("hide_email", $mem['hide_email'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email a PM reminder?</b>" ,
												  $ibforums->adskin->form_yes_no("email_pm", $mem['email_pm'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// CONTACT INFO
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Contact Information" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email Address</b>" ,
												  $ibforums->adskin->form_input("email", $mem['email'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>AIM Identity</b>" ,
												  $ibforums->adskin->form_input("aim_name", $mem['aim_name'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>ICQ Number</b>" ,
												  $ibforums->adskin->form_input("icq_number", $mem['icq_number'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Yahoo Identity</b>" ,
												  $ibforums->adskin->form_input("yahoo", $mem['yahoo'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>MSN Identity</b>" ,
												  $ibforums->adskin->form_input("msnname", $mem['msnname'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Website Address</b>" ,
												  $ibforums->adskin->form_input("website", $mem['website'])
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Other
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Other Information" );

		$mem['signature'] = $parser->unconvert( $mem['signature'] );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Avatar</b>" ,
												                 $ibforums->adskin->form_input("avatar", $mem['avatar_location'])
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Avatar Type</b>" ,
												                 $ibforums->adskin->form_dropdown("avatar_type", array( 0 => array( 'local'  , 'Avatar Gallery'  ),
												                 													    1 => array( 'url'    , 'URL Avatar'      ),
												                 													    2 => array( 'upload' , 'Uploaded Avatar' ),
												                 													   ), $mem['avatar_type'])
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Avatar Size</b>" ,
												                 $ibforums->adskin->form_input("avatar_size", $mem['avatar_size'])
									                    )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Post Count</b>" ,
												  $ibforums->adskin->form_input("posts", $mem['posts'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Location</b>" ,
												  $ibforums->adskin->form_input("location", $mem['location'])
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Interests</b>" ,
												  $ibforums->adskin->form_textarea("interests", str_replace( '<br />', "\n",$mem['interests']))
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Signature</b>" ,
												  $ibforums->adskin->form_textarea("signature", $mem['signature'])
									     )      );



		//-----------------------------------------
		// Custom profiles (HATE THIS TOO)
		//-----------------------------------------

		if ($custom_out != "")
		{
			$ibforums->html .= $ibforums->adskin->end_table();

			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Custom Profile Fields" );

			$ibforums->html .= $custom_out;

		}

		//-----------------------------------------
		// Slip n' ship
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->end_form("Edit this member");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}


	//-----------------------------------------
	//
	// Complete Edit
	//-----------------------------------------

	function member_do_edit()
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['mid'] = intval($ibforums->input['mid']);

		$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "id=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		$memb = $DB->fetch_row();

		require_once( ROOT_PATH."sources/lib/post_parser.php" );

		$parser = new post_parser();

		$ibforums->input['signature'] = $parser->convert( array ('TEXT'      => $ibforums->input['signature'],
																 'SMILIES'   => 0,
																 'CODE'      => $ibforums->vars['sig_allow_ibc'],
																 'SIGNATURE' => 1
														)       );

		//-----------------------------------------
		// Perms
		//-----------------------------------------

		if ( $ibforums->input['override'] == 1 )
		{
			$permid = implode( ",", $_POST['permid'] );
		}
		else
		{
			$permid = "";
		}

		$restrict_post = 0;
		$mod_queue     = 0;

		//-----------------------------------------
		// Q
		//-----------------------------------------

		if ( $ibforums->input['mod_indef'] == 1 )
		{
			$mod_queue = 1;
		}
		elseif ( $ibforums->input['mod_timespan'] > 0 )
		{
			$mod_queue = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->input['mod_timespan']), 'unit' => $ibforums->input['mod_units']  ) );
		}

		//-----------------------------------------
		// Post ban
		//-----------------------------------------

		if ( $ibforums->input['post_indef'] == 1 )
		{
			$restrict_post = 1;
		}
		elseif ( $ibforums->input['post_timespan'] > 0 )
		{
			$restrict_post = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->input['post_timespan']), 'unit' => $ibforums->input['post_units']  ) );
		}

		$avatar_type = $ibforums->input['avatar_type'];

		if ( strstr( $ibforums->input['avatar'], 'http://' ) )
		{
			$avatar_type = 'url';
		}

		//-----------------------------------------
		// Throw to the DB
		//-----------------------------------------

		$DB->do_update( 'members', array (
										  'restrict_post'   => $restrict_post,
										  'mgroup'       => $ibforums->input['mgroup'],
										  'title'        => $ibforums->input['title'],
										  'language'     => $ibforums->input['language'],
										  'skin'         => $ibforums->input['skin'],
										  'hide_email'   => $ibforums->input['hide_email'],
										  'email_pm'     => $ibforums->input['email_pm'],
										  'email'        => $ibforums->input['email'],
										  'posts'        => $ibforums->input['posts'],
										  'mod_posts'    => $mod_queue,
										  'org_perm_id'  => $permid,
										  'warn_level'   => $ibforums->input['warn_level'],
										  'mgroup_others' => $_POST['mgroup_others'] ? implode( ",", $_POST['mgroup_others'] ) : '',
								) , 'id='.$ibforums->input['mid']      );

		$DB->do_update( 'member_extra', array (
											   'aim_name'        => $ibforums->input['aim_name'],
											   'icq_number'      => $ibforums->input['icq_number'],
											   'yahoo'           => $ibforums->input['yahoo'],
											   'msnname'         => $ibforums->input['msnname'],
											   'website'         => $ibforums->input['website'],
											   'avatar_location' => $ibforums->input['avatar'],
											   'avatar_size'     => $ibforums->input['avatar_size'],
											   'avatar_type'     => $avatar_type,
											   'location'        => $ibforums->input['location'],
											   'interests'       => $ibforums->input['interests'],
											   'signature'       => $ibforums->input['signature'],
											), 'id='.$ibforums->input['mid'] );

		//-----------------------------------------
		// Moved from validating group?
		//-----------------------------------------

		if ( $ibforums->input['curgroup'] == $ibforums->vars['auth_group'] )
		{
			if ( $ibforums->input['mgroup'] != $ibforums->input['curgroup'] )
			{
				//-----------------------------------------
				// Yes...
				//-----------------------------------------

				$DB->simple_exec_query( array( 'delete' => 'validating', 'where' => "member_id={$ibforums->input['mid']} AND new_reg=1" ) );
			}
		}

		//-----------------------------------------
		// Diff email?
		//-----------------------------------------

		if ( $ibforums->input['email'] != $ibforums->input['curemail'] )
		{
			//-----------------------------------------
			// Is this email addy taken? CONVERGE THIS??
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => '*', 'from' => 'members', 'where' => "email='".$ibforums->input['email']."' and id <> {$ibforums->input['mid']}" ) );
			$DB->simple_exec();

			$email_check = $DB->fetch_row();

			if ($email_check['id'])
			{
				$ibforums->main_msg = "Cannot use this email address, another account is already using it";
				$this->member_do_edit_form();
			}

			$DB->do_update( 'members_converge', array( 'converge_email' => $ibforums->input['email'] ), 'converge_id='.$ibforums->input['mid'] );
		}

		//-----------------------------------------
		// Remove photo?
		//-----------------------------------------

		if ( $ibforums->input['remove_photo'] )
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'member_extra', 'where' => "id=".intval($ibforums->input['mid']) ) );
			$DB->simple_exec();

			if ( $DB->get_num_rows() )
			{
				$DB->do_update( 'member_extra', array( 'photo_location'   => '',
													   'photo_type'       => '',
													   'photo_dimensions' => '',
													 ), 'id='.$ibforums->input['mid'] );
			}
			else
			{
				$DB->do_insert( 'member_extra', array( 'photo_location'   => '',
													   'photo_type'       => '',
													   'photo_dimensions' => '',
													   'id'               => $ibforums->input['mid']
													 )  );
			}

			foreach( array( 'swf', 'jpg', 'jpeg', 'gif', 'png' ) as $ext )
			{
				if ( @file_exists( $ibforums->vars['upload_dir']."/photo-".$ibforums->input['mid'].".".$ext ) )
				{
					@unlink( $ibforums->vars['upload_dir']."/photo-".$ibforums->input['mid'].".".$ext );
				}
			}
		}

		//-----------------------------------------
		// Custom profile field stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
    	$fields = new custom_fields( $DB );

    	$fields->member_id   = $ibforums->member['id'];

    	$fields->cache_data  = $ibforums->cache['profilefields'];
    	$fields->admin       = intval($ibforums->member['g_access_cp']);
    	$fields->supmod      = intval($ibforums->member['g_is_supmod']);

    	$fields->init_data();
    	$fields->parse_to_save();

		//-----------------------------------------
		// Custom profile field stuff
		//-----------------------------------------

		if ( count( $fields->out_fields ) )
		{
			//-----------------------------------------
			// Do we already have an entry in
			// the content table?
			//-----------------------------------------

			$test = $DB->simple_exec_query( array( 'select' => 'member_id', 'from' => 'pfields_content', 'where' => 'member_id='.$ibforums->input['mid'] ) );

			if ( $test['member_id'] )
			{
				//-----------------------------------------
				// We have it, so simply update
				//-----------------------------------------

				$DB->do_update( 'pfields_content', $fields->out_fields, 'member_id='.$ibforums->input['mid'] );
			}
			else
			{
				$fields->out_fields['member_id'] = $ibforums->input['mid'];

				$DB->do_insert( 'pfields_content', $fields->out_fields );
			}
		}

		//-----------------------------------------
		// SYNC modules
		//-----------------------------------------

		if ( USE_MODULES == 1 )
		{
			$this->modules->register_class(&$this);

			if ( $memb['mgroup'] != $ibforums->input['mgroup'] )
			{
				$this->modules->on_group_change($ibforums->input['mid'], $ibforums->input['mgroup']);
			}

			if ( $memb['email'] != $ibforums->input['email'] )
			{
				$this->modules->on_email_change($ibforums->input['mid'], $ibforums->input['email']);
			}

			if ( $memb['signature'] != $ibforums->input['signature'] )
			{
				$this->modules->on_signature_update($memb, $ibforums->input['signature']);
			}

			$mem_array = array(
							    'title'        => $ibforums->input['title'],
								'aim_name'     => $ibforums->input['aim_name'],
								'icq_number'   => $ibforums->input['icq_number'],
								'yahoo'        => $ibforums->input['yahoo'],
								'msnname'      => $ibforums->input['msnname'],
								'website'      => $ibforums->input['website'],
								'location'     => $ibforums->input['location'],
								'interests'    => $ibforums->input['interests'],
								'id'		   => $ibforums->input['mid']
							  );

			$this->modules->on_profile_update($mem_array, $custom_fields);
		}

		//-----------------------------------------
		// Redirect
		//-----------------------------------------

		$page_query = "";

		foreach( array('name','suspended', 'registered_first', 'registered_last','last_post_first', 'last_post_last', 'last_activity_first', 'last_activity_last','mgroup','namewhere','gotcount', 'fromdel') as $bit )
		{
			$page_query .= '&'.$bit.'='.trim($ibforums->input[ $bit ]);
		}

		$ibforums->admin->save_log("Edited Member '{$memb['name']}' account");

		$ibforums->admin->done_screen("Member Edited", "Member Search", "act=mem".$page_query, 'redirect' );

	}

	//-----------------------------------------
	// Do banline (internal
	//-----------------------------------------

	function _do_banline($raw)
	{
		global $std;

		$ban = trim($std->txt_stripslashes($raw));

		$ban = str_replace('|', "&#124;", $ban);

		$ban = preg_replace( "/\n/", '|', str_replace( "\n\n", "\n", str_replace( "\r", "\n", $ban ) ) );

		$ban = preg_replace( "/\|{1,}\s{1,}?/s", "|", $ban );

		$ban = preg_replace( "/^\|/", "", $ban );

		$ban = preg_replace( "/\|$/", "", $ban );

		$ban = str_replace( "'", '&#39;', $ban );

		return $ban;
	}





}


?>