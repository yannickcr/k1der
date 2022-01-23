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
|   > Admin Category functions
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

class ad_moderator {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std, $forums;

		$forums->forums_init();

		require ROOT_PATH.'sources/admin/admin_forum_functions.php';

		$this->forumfunc = new admin_forum_functions();

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'add':
				$this->add_one();
				break;
			case 'add_two':
				$this->add_two();
				break;
			case 'add_final':
				$this->mod_form('add');
				break;
			case 'doadd':
				$this->add_mod();
				break;

			case 'edit':
				$this->mod_form('edit');
				break;

			case 'doedit':
				$this->do_edit();
				break;

			case 'remove':
				$this->do_delete();
				break;

			default:
				$this->show_list();
				break;
		}

	}

	//-----------------------------------------
	//
	// DELETE MODERATOR
	//
	//-----------------------------------------

	function do_delete()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You did not choose a valid moderator ID");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "mid=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		$mod = $DB->fetch_row();

		if ( $mod['is_group'] )
		{
			$name = 'Group: '.$mod['group_name'];
		}
		else
		{
			$name = $mod['member_name'];
		}

		$DB->simple_exec_query( array( 'delete' => 'moderators', 'where' => "mid=".intval($ibforums->input['mid']) ) );

		$this->rebuild_moderator_cache();

		$ibforums->admin->save_log("Removed Moderator '{$name}'");

		$ibforums->admin->done_screen("Moderator Removed", "Moderator Control", "act=mod", 'redirect' );

	}


	//-----------------------------------------
	//
	// EDIT MODERATOR
	//
	//-----------------------------------------

	function do_edit()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You did not choose a valid moderator ID");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "mid=".intval($ibforums->input['mid']) ) );
		$DB->simple_exec();

		$mod = $DB->fetch_row();

		//-----------------------------------------
		// Build Mr Hash
		//-----------------------------------------

		$DB->do_update( 'moderators', array(
											'forum_id'     => $ibforums->input['forum_id'],
											'edit_post'    => $ibforums->input['edit_post'],
											'edit_topic'   => $ibforums->input['edit_topic'],
											'delete_post'  => $ibforums->input['delete_post'],
											'delete_topic' => $ibforums->input['delete_topic'],
											'view_ip'      => $ibforums->input['view_ip'],
											'open_topic'   => $ibforums->input['open_topic'],
											'close_topic'  => $ibforums->input['close_topic'],
											'mass_move'    => $ibforums->input['mass_move'],
											'mass_prune'   => $ibforums->input['mass_prune'],
											'move_topic'   => $ibforums->input['move_topic'],
											'pin_topic'    => $ibforums->input['pin_topic'],
											'unpin_topic'  => $ibforums->input['unpin_topic'],
											'post_q'       => $ibforums->input['post_q'],
											'topic_q'      => $ibforums->input['topic_q'],
											'allow_warn'   => $ibforums->input['allow_warn'],
											'split_merge'  => $ibforums->input['split_merge'],
											'edit_user'    => $ibforums->input['edit_user'],
											'can_mm'	   => $ibforums->input['can_mm'],
										) , 'mid='.intval($ibforums->input['mid']) );

		$this->rebuild_moderator_cache();

		$ibforums->admin->save_log("Edited Moderator '{$mod['member_name']}'");

		$ibforums->admin->done_screen("Moderator Edited", "Moderator Control", "act=mod", 'redirect' );

	}

	//-----------------------------------------
	//
	// ADD MODERATOR
	//
	//-----------------------------------------

	function add_mod()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['fid'] == "")
		{
			$ibforums->admin->error("You did not choose any forums to add this member to");
		}

		//-----------------------------------------
		// Build Mr Hash
		//-----------------------------------------

		$mr_hash = array(
							'edit_post'    => $ibforums->input['edit_post'],
							'edit_topic'   => $ibforums->input['edit_topic'],
							'delete_post'  => $ibforums->input['delete_post'],
							'delete_topic' => $ibforums->input['delete_topic'],
							'view_ip'      => $ibforums->input['view_ip'],
							'open_topic'   => $ibforums->input['open_topic'],
							'close_topic'  => $ibforums->input['close_topic'],
							'mass_move'    => $ibforums->input['mass_move'],
							'mass_prune'   => $ibforums->input['mass_prune'],
							'move_topic'   => $ibforums->input['move_topic'],
							'pin_topic'    => $ibforums->input['pin_topic'],
							'unpin_topic'  => $ibforums->input['unpin_topic'],
							'post_q'       => $ibforums->input['post_q'],
							'topic_q'      => $ibforums->input['topic_q'],
							'allow_warn'   => $ibforums->input['allow_warn'],
							'split_merge'  => $ibforums->input['split_merge'],
							'edit_user'    => $ibforums->input['edit_user'],
							'can_mm'	   => $ibforums->input['can_mm'],
						);

		$forum_ids = array();

		$DB->simple_construct( array( 'select' => 'id', 'from' => 'forums', 'where' => "id IN(".$ibforums->input['fid'].")" ) );
		$DB->simple_exec();

		while( $i = $DB->fetch_row() )
		{
			$forum_ids[ $i['id'] ] = $i['id'];
		}

		//-----------------------------------------

		if ($ibforums->input['mod_type'] == 'group')
		{

			if ($ibforums->input['gid'] == "")
			{
				$ibforums->admin->error("We could not match that group ID");
			}

			$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'where' => "g_id='".$ibforums->input['gid']."'" ) );
			$DB->simple_exec();

			if ( ! $group = $DB->fetch_row() )
			{
				$ibforums->admin->error("We could not match that group ID");
			}

			//-----------------------------------------
			// Already using this group on this forum?
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "forum_id IN(".$ibforums->input['fid'].") and group_id={$ibforums->input['gid']}" ) );
			$DB->simple_exec();

			while( $f = $DB->fetch_row() )
			{
				unset($forum_ids[ $f['forum_id'] ]);
			}

			$mr_hash['member_name'] = '-1';
			$mr_hash['member_id']   = '-1';
			$mr_hash['group_id']    = $group['g_id'];
			$mr_hash['group_name']  = $group['g_title'];
			$mr_hash['is_group']    = 1;

			$ad_log = "Added Group '{$group['g_title']}' as a moderator";

		}
		else
		{

			if ($ibforums->input['mem'] == "")
			{
				$ibforums->admin->error("You did not choose a member to add as a moderator");
			}

			$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "id=".intval($ibforums->input['mem']) ) );
			$DB->simple_exec();

			if ( ! $mem = $DB->fetch_row() )
			{
				$ibforums->admin->error("Could not match that member name so there.");
			}

			//-----------------------------------------
			// Already using this member on this forum?
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "forum_id IN(".$ibforums->input['fid'].") and member_id={$ibforums->input['mem']}" ) );
			$DB->simple_exec();

			while( $f = $DB->fetch_row() )
			{
				unset($forum_ids[ $f['forum_id'] ]);
			}

			$mr_hash['member_name'] = $mem['name'];
			$mr_hash['member_id']   = $mem['id'];
			$mr_hash['is_group']    = 0;

			$ad_log = "Added Member '{$mem['name']}' as a moderator";

		}

		//-----------------------------------------
		// Check for legal forums
		//-----------------------------------------

		if ( count($forum_ids) == 0)
		{
			$ibforums->admin->error("You did not select any forums that do not have this group or member already moderating.");
		}

		//-----------------------------------------
		// Loopy loopy
		//-----------------------------------------

		foreach ($forum_ids as $cartman)
		{
			$mr_hash['forum_id'] = $cartman;

			$DB->force_data_type = array( 'member_name' => 'string' );

			$DB->do_insert( 'moderators', $mr_hash );
		}

		$ibforums->admin->save_log($ad_log);

		$this->rebuild_moderator_cache();

		$ibforums->admin->done_screen("Moderator Added", "Moderator Control", "act=mod", 'redirect' );

	}

	//-----------------------------------------
	//
	// Rebuild moderator cache
	//
	//-----------------------------------------

	function rebuild_moderator_cache()
	{
		global $ibforums, $std, $DB;

		$ibforums->cache['moderators'] = array();

		$DB->simple_construct( array( 'select' => "*",
									  'from'   => 'moderators'
							 )      );

		$DB->simple_exec();

		while ( $i = $DB->fetch_row() )
		{
			$ibforums->cache['moderators'][ $i['mid'] ] = $i;
		}

		$std->update_cache( array( 'name' => 'moderators', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	//
	// ADD FINAL, display the add / edit form
	//
	//-----------------------------------------

	function mod_form( $type='add' )
	{
		global $ibforums, $DB,  $std;

		$group = array();

		if ($type == 'add')
		{
			if ($ibforums->input['fid'] == "")
			{
				$ibforums->admin->error("You did not choose any forums to add this member to");
			}

			$mod   = array();
			$names = array();

			//-----------------------------------------

			$DB->simple_construct( array( 'select' => 'name', 'from' => 'forums', 'where' => "id IN(".$ibforums->input['fid'].")" ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$names[] = $r['name'];
			}

			$thenames = implode( ", ", $names );

			//-----------------------------------------

			$button = "Add this moderator";

			$form_code = 'doadd';

			if ($ibforums->input['mod_type'] == 'group')
			{
				$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'where' => "g_id='".$ibforums->input['mod_group']."'" ) );
				$DB->simple_exec();

				if (! $group = $DB->fetch_row() )
				{
					$ibforums->admin->error("Could not find that group to add as a moderator");
				}

				$ibforums->admin->page_detail = "Adding <b>group: {$group['g_title']}</b> as a moderator to: $thenames";
				$ibforums->admin->page_title = "Add a moderator group";
			}
			else
			{

				if ($ibforums->input['MEMBER_ID'] == "")
				{
					$ibforums->admin->error("Could not resolve the member id bucko");
				}
				else
				{
					$DB->simple_construct( array( 'select' => 'name, id', 'from' => 'members', 'where' => "id=".intval($ibforums->input['MEMBER_ID']) ) );
					$DB->simple_exec();

					if ( ! $mem = $DB->fetch_row() )
					{
						$ibforums->admin->error("That member ID does not resolve");
					}

					$member_id   = $mem['id'];
					$member_name = $mem['name'];
				}

				$ibforums->admin->page_detail = "Adding a $member_name as a moderator to: $thenames";
				$ibforums->admin->page_title = "Add a moderator";

			}

		}
		else
		{
			if ($ibforums->input['mid'] == "")
			{
				$ibforums->admin->error("You must choose a valid moderator to edit.");
			}

			$button    = "Edit this moderator";

			$form_code = "doedit";

			$ibforums->admin->page_title  = "Editing a moderator";
			$ibforums->admin->page_detail = "Please check the information carefully before submitting the form";

			$DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "mid=".intval($ibforums->input['mid']) ) );
			$DB->simple_exec();

			if ( ! $mod = $DB->fetch_row() )
			{
				$ibforums->admin->error("Could not retrieve that moderators record");
			}

			$member_id   = $mod['member_id'];
			$member_name = $mod['member_name'];
		}


		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'     , $form_code ),
																 2 => array( 'act'      , 'mod'      ),
																 3 => array( 'mid'      , $mod['mid']),
																 4 => array( 'fid'      , $ibforums->input['fid'] ),
																 5 => array( 'mem'      , $member_id ),
																 6 => array( 'mod_type' , $ibforums->input['mod_type'] ),
																 7 => array( 'gid'      , $group['g_id'] ),
																 8 => array( 'gname'    , $group['g_name'] ),
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "General Settings" );

		//-----------------------------------------

		if ($type == 'edit')
		{
			$forums = array();

			$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'forums', 'order' => "position" ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$forums[] = array( $r['id'], $r['name'] );
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Moderates forum...</b>" ,
												  $ibforums->adskin->form_dropdown( "forum_id", $forums, $mod['forum_id'] )
									     )      );
		}

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can edit others posts/polls?</b>" ,
												  $ibforums->adskin->form_yes_no("edit_post", $mod['edit_post'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can edit others topic titles?</b>" ,
												  $ibforums->adskin->form_yes_no("edit_topic", $mod['edit_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can delete others posts?</b>" ,
												  $ibforums->adskin->form_yes_no("delete_post", $mod['delete_post'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can delete others topics/polls?</b>" ,
												  $ibforums->adskin->form_yes_no("delete_topic", $mod['delete_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can view posters IP addresses?</b>" ,
												  $ibforums->adskin->form_yes_no("view_ip", $mod['view_ip'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can open locked topics?</b>" ,
												  $ibforums->adskin->form_yes_no("open_topic", $mod['open_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can close open topics?</b>" ,
												  $ibforums->adskin->form_yes_no("close_topic", $mod['close_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can move topics?</b>" ,
												  $ibforums->adskin->form_yes_no("move_topic", $mod['move_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can pin topics?</b>" ,
												  $ibforums->adskin->form_yes_no("pin_topic", $mod['pin_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can unpin topics?</b>" ,
												  $ibforums->adskin->form_yes_no("unpin_topic", $mod['unpin_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can split / merge topics?</b>" ,
												  $ibforums->adskin->form_yes_no("split_merge", $mod['split_merge'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Moderator Control Panel Settings" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can mass move topics?</b>" ,
												  $ibforums->adskin->form_yes_no("mass_move", $mod['mass_move'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can mass prune topics?</b>" ,
												  $ibforums->adskin->form_yes_no("mass_prune", $mod['mass_prune'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can set topics as visible and invisible?</b>" ,
												  $ibforums->adskin->form_yes_no("topic_q", $mod['topic_q'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can set posts as visible and invisible?</b>" ,
												  $ibforums->adskin->form_yes_no("post_q", $mod['post_q'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Advanced Settings" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can warn other users?</b>" ,
												  $ibforums->adskin->form_yes_no("allow_warn", $mod['allow_warn'] )
									     )      );

		//$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can edit user avatars and signatures?</b>" ,
		//										  $ibforums->adskin->form_yes_no("edit_user", $mod['edit_user'] )
		//							     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can use topic multi-moderation?</b><br>".$ibforums->adskin->js_help_link('mod_mmod', 'Important Information' ) ,
												  $ibforums->adskin->form_yes_no("can_mm", $mod['can_mm'] )
									     )      );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}


	//-----------------------------------------
	//
	// ADD step one: Look up a member
	//
	//-----------------------------------------

	function add_one()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Grab and serialize the input
		//-----------------------------------------

		$fid      = "";
		$fidarray = array();

		foreach ($ibforums->input as $k => $v)
		{
			if ( preg_match( "/^add_(\d+)$/", $k, $match ) )
			{
				if ($ibforums->input[ $match[0] ])
				{
					$fidarray[] = $match[1];
				}
			}
		}

		if ( count($fidarray) < 1 )
		{
			$ibforums->admin->error("You must select a forum, or forums to add a moderator to. You can do this by checking the checkboxes to the left of the forum name");
		}

		$fid = implode( "," ,$fidarray );

		$ibforums->admin->page_title = "Add a moderator";

		$ibforums->admin->page_detail = "Please find a member or group to moderate the forums you previously selected.";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'add_two' ),
												  2 => array( 'act'   , 'mod'     ),
												  3 => array( 'fid'   , $fid      ),
												  4 => array( 'mod_type' , $ibforums->input['mod_type'] ),
									     )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		if ($ibforums->input['mod_type'] == 'member')
		{

			$ibforums->html .= $ibforums->adskin->start_table( "Search for a member" );


			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Enter part or all of the usersname</b>" ,
													  $ibforums->adskin->form_input( "USER_NAME" )
											 )      );

			$ibforums->html .= $ibforums->adskin->end_form("Find Member");

			$ibforums->html .= $ibforums->adskin->end_table();

		}
		else
		{
			// Get the group ID's and names

			$mem_group = array();

			$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'order' => "g_title" ) );
			$DB->simple_exec();

			while ( $r = $DB->fetch_row() )
			{
				$mem_group[] = array( $r['g_id'] , $r['g_title'] );
			}

			$ibforums->html .= $ibforums->adskin->start_table( "Choose a group as a moderator" );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Select a group</b>" ,
													  $ibforums->adskin->form_dropdown( "mod_group", $mem_group )
											 )      );

			$ibforums->html .= $ibforums->adskin->end_form("Add this group");

			$ibforums->html .= $ibforums->adskin->end_table();

		}

		$ibforums->admin->output();


	}

	//-----------------------------------------
	//
	// REFINE MEMBER SEARCH
	//
	//-----------------------------------------

	function add_two()
	{
		global $ibforums, $DB,  $std;

		// Are we adding a group as a mod? If so, bounce straight to the mod perms form

		if ($ibforums->input['mod_type'] == 'group')
		{
			$this->mod_form();
			exit();
		}

		// Else continue as normal.

		if ($ibforums->input['USER_NAME'] == "")
		{
			$ibforums->admin->error("You didn't choose a member name to look for!");
		}

		$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "name LIKE '".$ibforums->input['USER_NAME']."%'" ) );
		$DB->simple_exec();

		if (! $DB->get_num_rows() )
		{
			$ibforums->admin->error("Sorry, we could not find any members that matched the search string you entered");
		}

		$form_array = array();

		while ( $r = $DB->fetch_row() )
		{
			$form_array[] = array( $r['id'] , $r['name'] );
		}

		$ibforums->admin->page_title = "Add a moderator";

		$ibforums->admin->page_detail = "Please select the correct member name from the selection below to add as a moderator to the previously selected forums.";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'add_final' ),
																 2 => array( 'act'   , 'mod'    ),
																 3 => array( 'fid'   , $ibforums->input['fid']),
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Search for a member" );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Choose from the matches...</b>" ,
												  $ibforums->adskin->form_dropdown( "MEMBER_ID", $form_array )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Choose Member");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}


	//-----------------------------------------
	//
	// SHOW LIST
	// Renders a complete listing of all the forums and categories w/mods.
	//
	//-----------------------------------------

	function show_list()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Moderator Control Overview";
		$ibforums->admin->page_detail  = "This section allows you to edit, remove and add new moderators to your forums";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'add' ),
																 2 => array( 'act'   , 'mod'   ),
														)      );

		//-----------------------------------------
		// Grab the moderators
		//-----------------------------------------

		$this->forumfunc->moderators = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'moderators' ) );
		$DB->simple_exec();

		while ($r = $DB->fetch_row())
		{
			$this->forumfunc->moderators[] = $r;
		}

		//-----------------------------------------
		// Loop and print
		//-----------------------------------------

		$this->forumfunc->type     = 'moderator';
		$this->forumfunc->show_all = 1;
		$this->forumfunc->forums_list_forums();

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table("Add Moderator");

		$ibforums->html .= $ibforums->adskin->add_td_basic( "<b>Type of moderator to add:</b> &nbsp;" . $ibforums->adskin->form_dropdown( "mod_type",
																				  array(
																						 0 => array( 'member', 'Single Member' ),
																						 1 => array( 'group', 'Member Group'   )
																					   )
																				  ) , "center" );

		$ibforums->html .= $ibforums->adskin->end_form("Add a moderator to the selected forums");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}
}


?>