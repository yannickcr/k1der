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
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class ad_forums {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $forums, $DB,  $std;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		$forums->forums_init();

		require ROOT_PATH.'sources/admin/admin_forum_functions.php';

		$this->forumfunc = new admin_forum_functions();

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'new':
				$this->do_form('new');
				break;
			case 'donew':
				$this->do_new();
				break;
			//-----------------------------------------
			case 'edit':
				$this->do_form('edit');
				break;
			case 'doedit':
				$this->do_edit();
				break;
			//-----------------------------------------
			case 'pedit':
				$this->perm_edit_form();
				break;
			case 'pdoedit':
				$this->perm_do_edit();
				break;
			//-----------------------------------------
			case 'reorder':
				$this->reorder_form();
				break;
			case 'doreorder':
				$this->do_reorder();
				break;
			case 'doreordercat':
				$this->do_reorder();
				break;
			//-----------------------------------------
			case 'delete':
				$this->delete_form();
				break;
			case 'dodelete':
				$this->do_delete();
				break;
			//-----------------------------------------
			case 'recount':
				$this->recount();
				break;
			//-----------------------------------------
			case 'empty':
				$this->empty_form();
				break;
			case 'doempty':
				$this->do_empty();
				break;
			//-----------------------------------------
			case 'frules':
				$this->show_rules();
				break;
			case 'dorules':
				$this->do_rules();
				break;
			//-----------------------------------------
			case 'skinedit':
				$this->skin_edit();
				break;
			case 'doskinedit':
				$this->do_skin_edit();
				break;
			//-----------------------------------------
			default:
				$this->show_forums();
				break;
		}

	}


	//-----------------------------------------
	//
	// Edit forum skins
	//
	//-----------------------------------------

	function skin_edit()
	{
		global $ibforums, $DB, $std, $forums;

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("Could not determine the forum ID to empty.");
		}

		$forum = $forums->forum_by_id[ $ibforums->input['f'] ];

		//-----------------------------------------
		// Make sure we have a legal forum
		//-----------------------------------------

		if ( ! $forum['id'] )
		{
			$ibforums->admin->error("Could not resolve that forum ID");
		}

		if ( ! $forum['skin_id'] )
		{
			$forum['skin_id'] = -1;
		}

		//-----------------------------------------
		// Get skins..
		//-----------------------------------------

		$tmp = $ibforums->skin['_setid'];

		$ibforums->skin['_setid'] = $forum['skin_id'];

		require_once( ROOT_PATH.'sources/classes/class_display.php' );
		$display = new display();

		$skin_list = $display->_build_skin_list();

		$ibforums->skin['_setid'] = $tmp;

		//-----------------------------------------
		// Do form..
		//-----------------------------------------

		$ibforums->admin->page_title  = "Forum Skin Options";
		$ibforums->admin->page_detail = "You may choose to either add or remove a skin set to this forum. The skin choice will override the users choice.";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doskinedit'),
																 2 => array( 'act'   , 'forum'  ),
																 3 => array( 'f'     , $ibforums->input['f'] ),
														   ) );


		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Skin choices for forum: {$forum['name']}" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Apply which skin to this forum?</b>" ,
																 "<select class='dropdown' name='fsid'><option value='-1'>--None / Remove All--</option>{$skin_list}</select>"
														 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Apply to all children of this forum (all sub-forums)</b>" ,
																 $ibforums->adskin->form_yes_no( 'apply_to_children' )
														 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Edit forum skin options");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	/*-------------------------------------------------------------------------*/
	// Complete forum skin edit
	/*-------------------------------------------------------------------------*/

	function do_skin_edit()
	{
		global $ibforums, $DB,  $std, $forums;

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("Could not determine the forum ID to apply this skin to.");
		}

		$forum = $forums->forum_by_id[ $ibforums->input['f'] ];

		//-----------------------------------------
		// Make sure we have a legal forum
		//-----------------------------------------

		$DB->do_update( 'forums', array( 'skin_id' => $ibforums->input['fsid'] ), 'id='.$ibforums->input['f'] );

		//-----------------------------------------
		// Find children?
		//-----------------------------------------

		if ( $ibforums->input['apply_to_children'] )
		{
			//-----------------------------------------
			// Get children!
			//-----------------------------------------

			$ids = $forums->forums_get_children( $ibforums->input['f'] );

			if ( count( $ids ) )
			{
				$DB->do_update( 'forums', array( 'skin_id' => $ibforums->input['fsid'] ), 'id IN ('.implode(",",$ids).')' );
			}
		}

		$ibforums->main_msg = "Forum skin updated";

		$this->recache_forums();

		$forums->forums_init();

		$ibforums->input['f'] = "";

		$this->show_forums();
	}


	//-----------------------------------------
	//
	// Show forum rules
	//
	//-----------------------------------------

	function show_rules()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("Could not determine the forum ID to empty.");
		}

		$DB->simple_construct( array( 'select' => 'id, name, show_rules, rules_title, rules_text', 'from' => 'forums', 'where' => "id=".$ibforums->input['f'] ) );
		$DB->simple_exec();

		//-----------------------------------------
		// Make sure we have a legal forum
		//-----------------------------------------

		if ( ! $DB->get_num_rows() )
		{
			$ibforums->admin->error("Could not resolve that forum ID");
		}

		$forum = $DB->fetch_row();

		//-----------------------------------------

		$ibforums->admin->page_title  = "Forum Rules";
		$ibforums->admin->page_detail = "You may edit, add, remove or change the state of the forum rules display";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dorules'),
																 2 => array( 'act'   , 'forum'  ),
																 3 => array( 'f'     , $ibforums->input['f'] ),
														   ) );


		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Forum Rules set up" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Display method</b>" ,
																 $ibforums->adskin->form_dropdown( "show_rules",
																					   array(
																							   0 => array( '0' , 'Don\'t Show' ),
																							   1 => array( '1' , 'Show Link Only' ),
																							   2 => array( '2' , 'Show full text' )
																							),
																					   $forum['show_rules']
																					 )
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rules Title</b>" ,
																 $ibforums->adskin->form_input("title", $std->txt_stripslashes(str_replace( "'", '&#039;', $forum['rules_title'])))
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Rules Text</b><br>(HTML Editing Mode)" ,
																 $ibforums->adskin->form_textarea( "body", $std->txt_stripslashes($forum['rules_text']), 65, 20 )
														)      );

		$ibforums->html .= $ibforums->adskin->end_form("Edit forum rules");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}


	function do_rules()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("Could not determine the forum ID to empty.");
		}

		$rules = array(
						'rules_title'    => $ibforums->admin->make_safe($std->txt_stripslashes($_POST['title'])),
						'rules_text'     => $ibforums->admin->make_safe($std->txt_stripslashes($_POST['body'])),
						'show_rules'     => $ibforums->input['show_rules']
					  );

		$DB->do_update( 'forums', $rules, 'id='.$ibforums->input['f'] );

		$this->recache_forums();

		$ibforums->admin->done_screen("Forum Rules Updated", "Forum Control", "act=forum" );

	}

	//-----------------------------------------
	//
	// RECOUNT FORUM: Recounts topics and posts
	//
	//-----------------------------------------

	function recount($f_override="")
	{
		global $ibforums, $DB,  $std, $forums;

		if ($f_override != "")
		{
			// Internal call, remap

			$ibforums->input['f'] = $f_override;
		}

		require_once( ROOT_PATH.'sources/lib/modfunctions.php' );
		$modfunc = new modfunctions();

		$modfunc->forum_recount($ibforums->input['f']);

		$this->recache_forums();

		$ibforums->admin->save_log("Recounted posts in forum '{$forums->forum_by_id[$ibforums->input['f']]['name']}'");

		$ibforums->admin->done_screen("Forum Resynchronised", "Forum Control", "act=forum", 'redirect' );

	}

	//-----------------------------------------
	//
	// EMPTY FORUM: Removes all topics and posts, etc.
	//
	//-----------------------------------------

	function empty_form()
	{
		global $ibforums, $DB,  $std;

		$form_array = array();

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("Could not determine the forum ID to empty.");
		}

		$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'forums', 'where' => "id=".$ibforums->input['f'] ) );
		$DB->simple_exec();

		//-----------------------------------------
		// Make sure we have a legal forum
		//-----------------------------------------

		if ( !$DB->get_num_rows() )
		{
			$ibforums->admin->error("Could not resolve that forum ID");
		}

		$forum = $DB->fetch_row();

		//-----------------------------------------

		$ibforums->admin->page_title = "Empty Forum '{$forum['name']}'";

		$ibforums->admin->page_detail = "This WILL DELETE ALL TOPICS, POSTS AND POLLS.<br>The forum itself will not be deleted - please ensure you wish to carry out this action before continuing.";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doempty'),
												  2 => array( 'act'   , 'forum'     ),
												  3 => array( 'f'     , $ibforums->input['f']  ),
												  4 => array( 'name' , $forum['name'] ),
											) );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Empty Forum '{$forum['name']}" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Forum to empty: </b>" , $forum['name'] )      );

		$ibforums->html .= $ibforums->adskin->end_form("Empty this forum");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------

	function do_empty()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Get module
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/lib/modfunctions.php' );
		$modfunc = new modfunctions();

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("Could not determine the source forum ID.");
		}

		//-----------------------------------------
		// Check to make sure its a valid forum.
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, posts, topics', 'from' => 'forums', 'where' => "id=".$ibforums->input['f'] ) );
		$DB->simple_exec();

		if ( ! $forum = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not get the forum details for the forum to empty");
		}

		$DB->simple_construct( array( 'select' => 'tid', 'from' => 'topics', 'where' => "forum_id=".$ibforums->input['f'] ) );
		$outer = $DB->simple_exec();

		//-----------------------------------------
		// What to do..
		//-----------------------------------------

		while( $t = $DB->fetch_row($outer) )
		{
			$modfunc->topic_delete($t['tid']);
		}

		//-----------------------------------------
		// Rebuild stats
		//-----------------------------------------

		$modfunc->forum_recount($ibforums->input['f']);
		$modfunc->stats_recount();

		//-----------------------------------------
		// Rebuild forum cache
		//-----------------------------------------

		$this->recache_forums();

		$ibforums->admin->save_log("Emptied forum '{$ibforums->input['name']}' of all posts");

		$ibforums->admin->done_screen("Forum Emptied", "Forum Control", "act=forum", 'redirect' );

	}



	//-----------------------------------------
	//
	// REMOVE FORUM
	//
	//-----------------------------------------

	function delete_form()
	{
		global $ibforums, $DB, $std, $forums;

		$form_array = array();

		$ibforums->input['f'] = intval($ibforums->input['f']);

		if ( ! $ibforums->input['f'] )
		{
			$ibforums->admin->error("Could not determine the forum ID to delete.");
		}

		$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'forums', 'order' => 'position' ) );
		$DB->simple_exec();

		//-----------------------------------------
		// Make sure we have more than 1
		// forum..
		//-----------------------------------------

		if ( $DB->get_num_rows() < 2 )
		{
			$ibforums->admin->error("Can not remove this forum, please create another before attempting to remove this one");
		}

		while ( $r = $DB->fetch_row() )
		{
			if ($r['id'] == $ibforums->input['f'])
			{
				$name = $r['name'];
				continue;
			}
		}

		$form_array = $this->forumfunc->ad_forums_forum_list(1);

		//-----------------------------------------
		// Count the number of topics
		//-----------------------------------------

		$posts = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'topics', 'where' => 'forum_id='.$ibforums->input['f'] ) );

		//-----------------------------------------
		// Count the number of children
		//-----------------------------------------

		$children = $DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'forums', 'where' => 'parent_id='.$ibforums->input['f'] ) );

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->admin->page_title = "Removing forum '$name'";

		$ibforums->admin->page_detail = "Before we remove this forum, if this forum is not empty, we need to determine what to do with any topics and posts you may have left in this forum.";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dodelete'),
																 2 => array( 'act'   , 'forum'     ),
																 3 => array( 'f'     , $ibforums->input['f']  ),
														   ) );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------
		// Main form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Required" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Forum to remove: </b>" , $name )      );

		if ( $posts['count'] )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Move all <i>existing topics and posts in this forum</i> to which forum?</b>" ,
																	$ibforums->adskin->form_dropdown( "MOVE_ID", $form_array )
														  )      );

		}

		if ( $children['count'] )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Move all <i>children forums</i> to which forum?</b>" ,
																	$ibforums->adskin->form_dropdown( "new_parent_id", $form_array )
														  )      );
		}


		$ibforums->html .= $ibforums->adskin->end_form( "Remove Forum" );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// PROCESS DELETE
	//-----------------------------------------

	function do_delete()
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['f']             = intval($ibforums->input['f']);
		$ibforums->input['MOVE_ID']       = intval($ibforums->input['MOVE_ID']);
		$ibforums->input['new_parent_id'] = intval($ibforums->input['new_parent_id']);

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forums', 'where' => "id=".$ibforums->input['f'] ) );
		$DB->simple_exec();

		$forum = $DB->fetch_row();

		if ( ! $ibforums->input['f'] )
		{
			$ibforums->admin->error("Could not determine the source forum ID.");
		}

		if ( ! $ibforums->input['new_parent_id'] )
		{
			$ibforums->input['new_parent_id'] = -1;
		}
		else
		{
			if ( $ibforums->input['new_parent_id'] == $ibforums->input['f'] )
			{
				$ibforums->main_msg = "You cannot move children forums to the forum you're removing!";
				$this->delete_form();
			}
		}

		require_once( ROOT_PATH.'sources/lib/modfunctions.php' );
		$modfunc = new modfunctions();

		//-----------------------------------------
		// Move stuff
		//-----------------------------------------

		if ( $ibforums->input['MOVE_ID'] )
		{
			if ( $ibforums->input['MOVE_ID'] == $ibforums->input['f'] )
			{
				$ibforums->main_msg = "You cannot move topics into the forum you're removing!";
				$this->delete_form();
			}

			//-----------------------------------------
			// Move topics...
			//-----------------------------------------

			$DB->do_update( 'topics', array( 'forum_id' => $ibforums->input['MOVE_ID'] ), 'forum_id='.$ibforums->input['f'] );

			//-----------------------------------------
			// Move polls...
			//-----------------------------------------

			$DB->do_update( 'polls', array( 'forum_id' => $ibforums->input['MOVE_ID'] ), 'forum_id='.$ibforums->input['f'] );

			//-----------------------------------------
			// Move voters...
			//-----------------------------------------

			$DB->do_update( 'voters', array( 'forum_id' => $ibforums->input['MOVE_ID'] ), 'forum_id='.$ibforums->input['f'] );

			$modfunc->forum_recount( $ibforums->input['MOVE_ID'] );
		}

		//-----------------------------------------
		// Delete the forum
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'forums', 'where' => "id=".$ibforums->input['f'] ) );

		//-----------------------------------------
		// Delete any moderators, if any..
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'moderators', 'where' => "forum_id=".$ibforums->input['f'] ) );

		//-----------------------------------------
		// Delete forum subscriptions
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'forum_tracker', 'where' => "forum_id=".$ibforums->input['f'] ) );

		//-----------------------------------------
		// Update children
		//-----------------------------------------

		if ( ! $ibforums->input['new_parent_id'] )
		{
			$ibforums->input['new_parent_id'] = -1;
		}

		$DB->do_update( 'forums', array( 'parent_id' => $ibforums->input['new_parent_id'] ), "parent_id={$ibforums->input['f']}" );

		//-----------------------------------------
		// Rebuild forum cache
		//-----------------------------------------

		$this->recache_forums();

		//-----------------------------------------
		// Rebuild moderator cache
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_moderator.php' );
		$moderator = new ad_moderator();

		$moderator->rebuild_moderator_cache();

		$ibforums->admin->save_log("Removed forum '{$forum['name']}'");

		$ibforums->admin->done_screen("Forum Removed", "Forum Control", "act=forum", 'redirect' );

	}

	//-----------------------------------------
	// DO NEW FORUM
	//-----------------------------------------

	function do_new()
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['name'] = trim($ibforums->input['name']);

		if ($ibforums->input['name'] == "")
		{
			$ibforums->admin->error("You must enter a forum title");
		}

		// Get the new forum id. We could use auto_incrememnt, but we need the ID to use as the default
		// forum position...

		$DB->simple_construct( array( 'select' => 'MAX(id) as top_forum', 'from' => 'forums' ) );
		$DB->simple_exec();

		$row = $DB->fetch_row();

		if ($row['top_forum'] < 1) $row['top_forum'] = 0;

		$row['top_forum']++;

		$perms = $ibforums->admin->compile_forum_perms();

		$perm_array = addslashes(serialize(array(
												  'start_perms'  => $perms['START'],
												  'reply_perms'  => $perms['REPLY'],
												  'read_perms'   => $perms['READ'],
												  'upload_perms' => $perms['UPLOAD'],
												  'show_perms'   => $perms['SHOW']
 								)		  )     );

		$DB->do_insert( 'forums', array (
										  'id'                      => $row['top_forum'],
										  'position'                => $row['top_forum'],
										  'topics'                  => 0,
										  'posts'                   => 0,
										  'last_post'               => "",
										  'last_poster_id'          => "",
										  'last_poster_name'        => "",
										  'name'                    => $ibforums->input['name'],
										  'description'             => $std->my_nl2br( $std->txt_stripslashes($_POST['description']) ),
										  'use_ibc'                 => $ibforums->input['use_ibc'],
										  'use_html'                => $ibforums->input['use_html'],
										  'status'                  => $ibforums->input['status'],
										  'password'                => $ibforums->input['password'],
										  'last_id'                 => "",
										  'last_title'              => "",
										  'sort_key'                => $ibforums->input['sort_key'],
										  'sort_order'              => $ibforums->input['sort_order'],
										  'prune'                   => $ibforums->input['prune'],
										  'show_rules'              => 0,
										  'preview_posts'           => $ibforums->input['preview_posts'],
										  'allow_poll'              => $ibforums->input['allow_poll'],
										  'allow_pollbump'          => $ibforums->input['allow_pollbump'],
										  'inc_postcount'           => $ibforums->input['inc_postcount'],
										  'parent_id'               => $ibforums->input['parent_id'],
										  'sub_can_post'            => $ibforums->input['sub_can_post'],
										  'quick_reply'             => $ibforums->input['quick_reply'],
										  'redirect_on'             => $ibforums->input['redirect_on'],
										  'redirect_hits'           => $ibforums->input['redirect_hits'],
										  'redirect_url'            => $ibforums->input['redirect_url'],
										  'redirect_loc'		      => $ibforums->input['redirect_loc'],
										  'notify_modq_emails'      => $ibforums->input['notify_modq_emails'],
										  'permission_array'        => $perm_array,
										  'permission_showtopic'    => $ibforums->input['permission_showtopic'],
										  'permission_custom_error' => $std->my_nl2br( $std->txt_stripslashes($_POST['permission_custom_error']) ),

								)       );

		$this->recache_forums();

		$ibforums->admin->save_log("Forum '{$ibforums->input['name']}' created");

		$ibforums->admin->done_screen("Forum {$ibforums->input['name']} created", "Forum Control", "act=forum", 'redirect' );
	}



	//-----------------------------------------
	//
	// EDIT FORUM
	//
	//-----------------------------------------

	function do_form($type='edit')
	{
		global $ibforums, $DB, $std, $forums;


		$ibforums->admin->page_detail = "This section will allow you to add or edit an existing forum. If you wish to adjust the forum permissions (who has the ability to
							   			 start, reply and read topics) click on 'Edit Permissions on the Forums and Categories overview.";

		if ( $type == 'edit' )
		{
			if ($ibforums->input['f'] == "")
			{
				$ibforums->admin->error("You didn't choose a forum to edit, duh!");
			}

			$this->forumfunc->exclude_from_list = $ibforums->input['f'];

			$forum = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'forums', 'where' => 'id='.$ibforums->input['f'] ) );

			if ($forum['id'] == "")
			{
				$ibforums->admin->error("Could not retrieve the forum data based on ID {$ibforums->input['f']}");
			}

			$title  = "Editing Forum: {$forum['name']}";
			$button = "Edit Forum";
			$code   = "doedit";

			$basic_title = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
							<tr>
							 <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Basic Settings for {$forum['name']}</td>
							 <td align='right' width='60%'>".
							 $ibforums->adskin->js_make_button("Edit Forum Rules"  , $ibforums->base_url."&act=forum&code=frules&f={$ibforums->input['f']}")."&nbsp;".
						     $ibforums->adskin->js_make_button("Edit Skin Settings", $ibforums->base_url."&act=forum&code=skinedit&f={$ibforums->input['f']}")."&nbsp;".
						     $ibforums->adskin->js_make_button("Recount Forum"     , $ibforums->base_url."&act=forum&code=recount&f={$ibforums->input['f']}")
							 ."&nbsp;&nbsp;</td>
							</tr>
							</table>";


		}
		else
		{
			$f_name = "";

			if ($_GET['name'] != "")
			{
				$f_name = $std->txt_stripslashes(urldecode($_GET['name']));
			}

			if ( $ibforums->input['c'] == 1 )
			{
				$subcanpost = 0;
			}
			else
			{
				$subcanpost = 1;
			}

			if ( ! $ibforums->input['p'] )
			{
				$parentid = -1;
			}
			else
			{
				$parentid = $ibforums->input['p'];
			}

			$forum = array(
							'sub_can_post' => $subcanpost,
							'name'         => $f_name,
							'parent_id'    => $parentid,
							'use_ibc'      => 1,
							'quick_reply'  => 1,
							'allow_poll'   => 1,
							'prune'        => 100,
							'sort_key'     => 'last_post',
							'sort_order'   => 'Z-A',
							'inc_postcount'=> 1,
						  );

			$title  = "Add a forum";
			$button = "Add Forum";
			$code   = "donew";
			$basic_title = 'Basic Settings';
		}

		$forumlist = $this->forumfunc->ad_forums_forum_list();

		//-----------------------------------------

		$ibforums->admin->page_title = $title;

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $code  ),
															   2 => array( 'act'   , 'forum'   ),
															   3 => array( 'f'     , $ibforums->input['f']  ),
															   4 => array( 'name'  , $forum['name'] ),
														 ) );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( $basic_title );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Forum Name</b>" ,
																	  $ibforums->adskin->form_input("name", $forum['name'])
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Forum Description</b><br>You may use HTML - linebreaks 'Auto-Magically' converted to &lt;br&gt;" ,
																	  $ibforums->adskin->form_textarea("description", $std->my_br2nl( $forum['description']) )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Add to which parent?</b><br>" ,
																	  $ibforums->adskin->form_dropdown("parent_id", $forumlist, $forum['parent_id'])
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Forum State</b>" ,
																	  $ibforums->adskin->form_dropdown( "status",
																								array(
																										0 => array( 1, 'Active' ),
																										1 => array( 0, 'Read Only Archive'  ),
																									 ),
																							$forum['status']
																						  )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Act as a normal forum not as a category?</b><br />If you DON'T make this a normal forum, new posts won't be allowed and existing topics and posts won't show.<br><b>If 'no' you can skip the rest of this form as the settings will have no effect and this forum will act like a category.</b>" ,
																	  $ibforums->adskin->form_yes_no(
																						  "sub_can_post",
																						  $forum['sub_can_post'],
																						  array(
																								  'yes' => " onclick=\"ShowHide('main_div', 'maindivoff');\" ",
																								  'no'  => " onclick=\"ShowHide('main_div', 'maindivoff');\" "
																							   )
																						) . $extra
															 )      );

		$ibforums->html .= $ibforums->adskin->end_table();



		$md_show  = ($forum['sub_can_post'] == 1) ? 'show' : 'none';
		$md2_show = ($forum['sub_can_post'] == 1) ? 'none' : 'show';

		$ibforums->html .= "\n<div id='main_div' style='display:$md_show'>\n";

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Forum Redirect Options" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>URL to redirect this forum to</b>" ,
																	  $ibforums->adskin->form_input("redirect_url", $forum['redirect_url'])
															 )      );

		/*$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Target to redirect to?</b><br>Leave blank or use '_self' to load in same browser window" ,
																	  $ibforums->adskin->form_input("redirect_loc", $forum['redirect_loc'])
															 )      );*/

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Switch on the URL redirect?</b><br>If 'yes' you can skip the rest of this form as the settings will have no effect and this forum will act like as a redirect link. Current posts will not be accessible when on." ,
																	  $ibforums->adskin->form_yes_no("redirect_on",
																						 $forum['redirect_on'],
																						  array(
																								  'yes' => " onclick=\"ShowHide('canpost', 'canpostoff');\" ",
																								  'no'  => " onclick=\"ShowHide('canpost', 'canpostoff');\" "
																							   )
																						 )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Redirect clicks to date</b>" ,
																	  $ibforums->adskin->form_input("redirect_hits", $forum['redirect_hits'])
															 )      );

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Permission Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow members who can see the forum but cannot read topics to see the topic list?</b><br />If yes, the member will be able to see the topic titles but will not be able to read the topic posts when clicked." ,
																$ibforums->adskin->form_yes_no("permission_showtopic", $forum['permission_showtopic'] )
													   )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Custom 'Permission Denied' Message</b><br>You may use HTML - linebreaks 'Auto-Magically' converted to &lt;br&gt;.<br />If left blank, a default 'permission denied' error is used." ,
															   $ibforums->adskin->form_textarea("permission_custom_error", $std->my_br2nl( $forum['permission_custom_error']) )
													  )      );

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "{none}"  , "40%" );
		$ibforums->adskin->td_header[] = array( "{none}"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Postable Forum Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow HTML to be posted (when group permissions allow)?</b><br />This will allow HTML to be posted and executed" ,
																	  $ibforums->adskin->form_yes_no("use_html", $forum['use_html'] )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow BBCode to be posted?</b>" ,
																	  $ibforums->adskin->form_yes_no("use_ibc", $forum['use_ibc'] )
															 )      );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Turn on the Quick Reply Box?</b>" ,
																	  $ibforums->adskin->form_yes_no("quick_reply", $forum['quick_reply'] )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow Polls in this forum (when allowed)?</b>" ,
																	  $ibforums->adskin->form_yes_no("allow_poll", $forum['allow_poll'] )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow votes to bump a topic?</b>" ,
																	  $ibforums->adskin->form_yes_no("allow_pollbump", $forum['allow_pollbump'] )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Posts in this forum increase member's cumulative post count?</b>" ,
																	  $ibforums->adskin->form_yes_no("inc_postcount", $forum['inc_postcount'] )
															 )      );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Moderate postings?</b><br>(Requires a moderator to manually add posts/topics to the forum)" ,
																	  $ibforums->adskin->form_dropdown("preview_posts", array(
																										 0 => array( 0, 'No' ),
																										 1 => array( 1, 'Moderate all new topics and all replies' ),
																										 2 => array( 2, 'Moderate new topics but don\'t moderate replies' ),
																										 3 => array( 3, 'Moderate replies but don\'t moderate new topics' ),
																										   ),
																									$forum['preview_posts'] )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Email addresses to send new topic awaiting approval notification?</b><br>(Leave this box empty if you do not require this)<br />Separate many with a comma (add@ress1.com,add@ress2.com)" ,
																	  $ibforums->adskin->form_input("notify_modq_emails", $forum['notify_modq_emails'])
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Require password access?<br>Enter the password here</b><br>(Leave this box empty if you do not require this)" ,
																	  $ibforums->adskin->form_input("password", $forum['password'])
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Default date cut off for topic display</b>" ,
																	  $ibforums->adskin->form_dropdown( "prune",
																								array(
																										0 => array( 1, 'Today' ),
																										1 => array( 5, 'Last 5 days'  ),
																										2 => array( 7, 'Last 7 days'  ),
																										3 => array( 10, 'Last 10 days' ),
																										4 => array( 15, 'Last 15 days' ),
																										5 => array( 20, 'Last 20 days' ),
																										6 => array( 25, 'Last 25 days' ),
																										7 => array( 30, 'Last 30 days' ),
																										8 => array( 60, 'Last 60 days' ),
																										9 => array( 90, 'Last 90 days' ),
																										10=> array( 100,'Show All'     ),
																									 ),
																							$forum['prune']
																						  )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Default sort key</b>" ,
																	  $ibforums->adskin->form_dropdown( "sort_key",
																								array(
																										0 => array( 'last_post', 'Date of the last post' ),
																										1 => array( 'title'    , 'Topic Title' ),
																										2 => array( 'starter_name', 'Topic Starters Name' ),
																										3 => array( 'posts'    , 'Topic Posts' ),
																										4 => array( 'views'    , 'Topic Views' ),
																										5 => array( 'start_date', 'Date topic started' ),
																										6 => array( 'last_poster_name'   , 'Name of the last poster' ),
																									 ),
																							$forum['sort_key']
																						  )
															 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Default sort order</b>" ,
																	  $ibforums->adskin->form_dropdown( "sort_order",
																								array(
																										0 => array( 'Z-A', 'Descending (Z - A, 0 - 10)' ),
																										1 => array( 'A-Z', 'Ascending (A - Z, 10 - 0)' ),
																									 ),
																							$forum['sort_order']
																						  )
															 )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= "\n<!--END MAIN DIV--></div>\n
		                   <div id='maindivoff' class='offdiv' style='display:$md2_show'>
		                     <div class='tableborder'>
						       <div class='maintitle'><a href=\"javascript:ShowHide('main_div', 'maindivoff');\"><img src='{$ibforums->adskin->img_url}/plus.gif'></a>&nbsp;<a href=\"javascript:ShowHide('main_div', 'maindivoff');\">Postable Forum Settings</a></div>
						     </div>
		                 </div><br />\n";


		if ( $type == 'edit' )
		{
			$ibforums->html .= $ibforums->adskin->end_form_standalone("Edit this forum");
		}
		else
		{

			$ibforums->adskin->td_header[] = array( "Name"          , "40%" );
			$ibforums->adskin->td_header[] = array( "Show Forum"    , "12%" );
			$ibforums->adskin->td_header[] = array( "Read Topics"   , "12%" );
			$ibforums->adskin->td_header[] = array( "Reply Topics"  , "12%" );
			$ibforums->adskin->td_header[] = array( "Start Topics"  , "12%" );
			$ibforums->adskin->td_header[] = array( "Upload"        , "12%" );

			$ibforums->html .= $ibforums->adskin->start_table("Permission Access Levels");

			$ibforums->html .= $ibforums->adskin->build_group_perms( $forum['show_perms'], $forum['read_perms'], $forum['start_perms'], $forum['reply_perms'], $forum['upload_perms']);


			$ibforums->html .= $ibforums->adskin->end_form("Create this forum");

			$ibforums->html .= $ibforums->adskin->end_table();
		}

		$ibforums->admin->nav[] = array( 'act=forum', 'Manage Forums' );
		$ibforums->admin->nav[] = array( '', 'Add/Edit Forum' );

		$ibforums->admin->output();


	}


	//-----------------------------------------

	function do_edit()
	{
		global $ibforums, $DB,  $std, $forums;

		$ibforums->input['name'] = trim($ibforums->input['name']);

		if ($ibforums->input['name'] == "")
		{
			$ibforums->admin->error("You must enter a forum title");
		}

		//-----------------------------------------
		// Are we trying to do something stupid
		// like running with scissors or moving
		// the parent of a forum into itself
		// spot?
		//-----------------------------------------

		if ( $ibforums->input['parent_id'] != $forums->forum_by_id[ $ibforums->input['f'] ]['parent_id'] )
		{
			// Get children and make sure we're trying to impossible

			$ids = $forums->forums_get_children( $ibforums->input['f'] );
			$ids[] = $ibforums->input['f'];

			if ( in_array( $ibforums->input['parent_id'], $ids ) )
			{
				$ibforums->admin->error("Sorry, that is not possible. You are attempting to move a parent forum into its own child structure. Please go back and choose a different parent forum.");
			}
		}

		$DB->do_update( 'forums', array (

										  'name'                    => $ibforums->input['name'],
										  'description'             => $std->my_nl2br( $std->txt_stripslashes($_POST['description']) ),
										  'use_ibc'                 => $ibforums->input['use_ibc'],
										  'use_html'                => $ibforums->input['use_html'],
										  'status'                  => $ibforums->input['status'],
										  'password'                => $ibforums->input['password'],
										  'sort_key'                => $ibforums->input['sort_key'],
										  'sort_order'              => $ibforums->input['sort_order'],
										  'prune'                   => $ibforums->input['prune'],
										  'preview_posts'           => $ibforums->input['preview_posts'],
										  'allow_poll'              => $ibforums->input['allow_poll'],
										  'allow_pollbump'          => $ibforums->input['allow_pollbump'],
										  'inc_postcount'           => $ibforums->input['inc_postcount'],
										  'parent_id'               => $ibforums->input['parent_id'],
										  'sub_can_post'            => $ibforums->input['sub_can_post'],
										  'quick_reply'             => $ibforums->input['quick_reply'],
										  'redirect_on'             => $ibforums->input['redirect_on'],
										  'redirect_hits'           => $ibforums->input['redirect_hits'],
										  'redirect_url'            => $ibforums->input['redirect_url'],
										  'redirect_loc'		    => $ibforums->input['redirect_loc'],
										  'notify_modq_emails'      => $ibforums->input['notify_modq_emails'],
										  'permission_showtopic'    => $ibforums->input['permission_showtopic'],
										  'permission_custom_error' => $std->my_nl2br( $std->txt_stripslashes($_POST['permission_custom_error']) ),

								)  , "id={$ibforums->input['f']}"  );

		$ibforums->admin->save_log("Forum '{$ibforums->input['name']}' edited");

		$this->recache_forums();

		$ibforums->admin->done_screen("Forum {$ibforums->input['name']} Edited", "Forum Control", "act=forum", 'redirect' );
	}


	//-----------------------------------------
	//
	// EDIT FORUM
	//
	//-----------------------------------------

	function perm_edit_form()
	{
		global $ibforums, $DB,  $std, $forums;

		//-----------------------------------------
		// check..
		//-----------------------------------------

		if ($ibforums->input['f'] == "")
		{
			$ibforums->admin->error("You didn't choose a forum to edit, duh!");
		}

		//-----------------------------------------
		// Get this forum details
		//-----------------------------------------

		$forum = $forums->forum_by_id[$ibforums->input['f']];

		//-----------------------------------------
		// Next id...
		//-----------------------------------------

		$next     = "";
		$previous = "";

		$relative = $this->get_next_id( $ibforums->input['f'] );

		if ( $relative['next'] > 0 )
		{
			$next = "<input type='submit' name='donext' value='Save and Edit Next' class='realdarkbutton' />";
		}

		if ( $relative['previous'] > 0 )
		{
			$previous = "<input type='submit' name='doprevious' value='Save and Edit Previous' class='realdarkbutton' />";
		}

		//-----------------------------------------
		// check...
		//-----------------------------------------

		if ($forum['id'] == "")
		{
			$ibforums->admin->error("Could not retrieve the forum data based on ID {$ibforums->input['f']}");
		}

		$ibforums->admin->page_title = "Edit permissions for ".$forum['name'];

		$ibforums->admin->page_detail = "<b>Forum access permissions</b><br>(Check box for access, uncheck to not allow access)<br>If you deny read access for a permission mask, they will not see the forum";

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'    , 'pdoedit'              ),
																 2 => array( 'act'     , 'forum'                ),
																 3 => array( 'f'       , $ibforums->input['f']  ),
																 4 => array( 'name'    , $forum['name']         ),
																 5 => array( 'nextid'  , $relative['next']      ),
																 6 => array( 'previd'  , $relative['previous']  ),
														   ) );

		$ibforums->adskin->td_header[] = array( "Name"  , "40%" );
		$ibforums->adskin->td_header[] = array( "Show Forum"    , "12%" );
		$ibforums->adskin->td_header[] = array( "Read Topics"   , "12%" );
		$ibforums->adskin->td_header[] = array( "Reply Topics"  , "12%" );
		$ibforums->adskin->td_header[] = array( "Start Topics"  , "12%" );
		$ibforums->adskin->td_header[] = array( "Upload"        , "12%" );

		$ibforums->html .= $ibforums->adskin->start_table( $forums->forum_by_id[ $forum['parent_id'] ]['name'].' / '.$forum['name'].' / '."Permission Access Levels");

		$ibforums->html .= $ibforums->adskin->build_group_perms( $forum['show_perms'], $forum['read_perms'], $forum['start_perms'], $forum['reply_perms'], $forum['upload_perms']);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->html .= "<div class='tableborder'><div class='pformstrip' align='center'>$previous
							<input type='submit' value='Save Only' class='realbutton' />
							<input type='submit' name='reload' value='Save and Reload' class='realbutton' />
							$next</div></div></form>";

		$ibforums->admin->nav[] = array( 'act=forum', 'Manage Forums' );
		$ibforums->admin->nav[] = array( '', 'Permissions for forum '.$forum['name'] );

		$ibforums->admin->output();


	}

	//-----------------------------------------
	// Get next forum ID
	//-----------------------------------------

	function get_next_id($fid)
	{
		global $forums;

		$nextid = 0;
		$ids    = array();
		$index  = 0;
		$count  = 0;

		foreach( $forums->forum_cache['root'] as $id => $forum_data )
		{
			$ids[ $count ] = $forum_data['id'];

			if ( $forum_data['id'] == $fid )
			{
				$index = $count;
			}

			$count++;

			if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
			{
				foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
				{
					$children = $forums->forums_get_children( $forum_data['id'] );

					$ids[ $count ] = $forum_data['id'];

					if ( $forum_data['id'] == $fid )
					{
						$index = $count;
					}

					$count++;

					if ( is_array($children) and count($children) )
					{
						foreach( $children as $kid )
						{
							$ids[ $count ] = $kid;

							if ( $kid == $fid )
							{
								$index = $count;
							}

							$count++;
						}
					}
				}
			}
		}

		return array( 'next' => $ids[ $index + 1 ], 'previous' => $ids[ $index - 1 ] );
	}

	//-----------------------------------------
	// RECACHE FORUMS
	//-----------------------------------------

	function recache_forums()
	{
		global $ibforums, $DB, $std;

		$std->update_forum_cache();
	}

	//-----------------------------------------
	// SAVE PERM CHANGES
	//-----------------------------------------

	function perm_do_edit()
	{
		global $ibforums, $DB,  $std;

		$perms = $ibforums->admin->compile_forum_perms();

		$DB->do_update( 'forums', array( 'permission_array' => addslashes(serialize(array(
																						   'start_perms'  => $perms['START'],
																						   'reply_perms'  => $perms['REPLY'],
																						   'read_perms'   => $perms['READ'],
																						   'upload_perms' => $perms['UPLOAD'],
																						   'show_perms'   => $perms['SHOW']
							    		)		  						 )         )      ), 'id='.$ibforums->input['f']);



		$ibforums->admin->save_log("Forum access permission edited in '{$ibforums->input['name']}'");

		$this->recache_forums();

		if ( $ibforums->input['doprevious'] and $ibforums->input['previd'] > 0 )
		{
			$ibforums->main_msg = 'Forum permissions edited';

			$ibforums->input['f'] = $ibforums->input['previd'];

			$std->boink_it( $ibforums->base_url."&act=forum&code=pedit&f={$ibforums->input['f']}" );
		}
		else if ( $ibforums->input['donext'] and $ibforums->input['nextid'] > 0 )
		{
			$ibforums->main_msg = 'Forum permissions edited';

			$ibforums->input['f'] = $ibforums->input['nextid'];

			$std->boink_it( $ibforums->base_url."&act=forum&code=pedit&f={$ibforums->input['f']}" );
		}
		else if ( $ibforums->input['reload'] )
		{
			$std->boink_it( $ibforums->base_url."&act=forum&code=pedit&f={$ibforums->input['f']}" );
		}
		else
		{
			$ibforums->admin->done_screen("Forum Access Permissions Edited", "Forum Control", "act=forum", 'redirect' );
		}
	}

	//-----------------------------------------
	//
	// RE-ORDER FORUMS
	//
	//-----------------------------------------

	function reorder_form()
	{
		global $ibforums, $DB,  $std, $forums;

		if ( ! $ibforums->input['f'] )
		{
			$ibforums->admin->error("Cannot go any further, not F passed");
		}

		$ibforums->admin->page_detail = "Simply select the position you require for each forum and submit the form to complete the re-order.";

		$this->forumfunc->type = 'reorder';

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doreorder'),
												  2 => array( 'act'   , 'forum'     ),
										 )      );

		$temp_html = "";

		$this->forumfunc->forum_show_cat($forums->forum_by_id[ $ibforums->input['f'] ]);

		$depth_guide = "";

		if ( is_array( $forums->forum_cache[ $ibforums->input['f'] ] ) )
		{
			foreach( $forums->forum_cache[ $ibforums->input['f'] ] as $id => $forum_data )
			{
				$temp_html .= $this->forumfunc->render_forum($forum_data, $depth_guide);

				$temp_html = $this->forumfunc->forum_build_children( $forum_data['id'], $temp_html, $depth_guide . $forums->depth_guide );
			}
		}

		$ibforums->html .= $temp_html;

		$this->forumfunc->forum_end_cat($forums->forum_by_id[ $ibforums->input['f'] ]);

		$ibforums->html .= $ibforums->adskin->end_form_standalone("Re-order");

		$ibforums->admin->output();

	}


	//-----------------------------------------
	//
	// Re order the root forums
	//
	//-----------------------------------------

	function do_reorder()
	{
		global $ibforums, $DB,  $std, $forums;

		$ids = array();

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^f_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$ids[ $match[1] ] = $ibforums->input[$match[0]];
 				}
 			}
 		}

 		//-----------------------------------------
 		// Save changes
 		//-----------------------------------------

 		if ( count($ids) )
 		{
 			foreach( $ids as $forum_id => $new_position )
 			{
 				$DB->do_update( 'forums', array( 'position' => intval($new_position) ), 'id='.$forum_id );
 			}
 		}

 		// Reload forums

 		$this->recache_forums();

 		$std->boink_it( $ibforums->base_url.'&act=forum' );
	}


	//-----------------------------------------
	//
	// SHOW THE FORUMS WOOHOO, ETC
	//
	//-----------------------------------------

	function show_forums()
	{
		global $ibforums, $DB,  $std, $forums;

		$ibforums->admin->page_title   = "Category and Forums Overview";
		$ibforums->admin->page_detail  = "You can manage your forums from here. Roll your mouse over the icons for more information.";

		//-----------------------------------------
		// Nav
		//-----------------------------------------

		if ( $ibforums->input['f'] )
		{
			$nav = $forums->forums_breadcrumb_nav($ibforums->input['f'], '&act=forum&f=');

			if ( is_array($nav) and count($nav) > 1 )
			{
				array_shift($nav);

				$ibforums->html .= "<div class='navstrip'><a href='{$ibforums->base_url}&act=forum'>Forums</a> &gt; ".implode( " &gt; ", $nav )."</div><br />";
			}
		}

		$this->forumfunc->type = 'manage';

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'doreordercat'),
													             2 => array( 'act'   , 'forum'     ),
													    )      );

		$this->forumfunc->forums_list_forums();

		$choose = "<select name='roots' class='realbutton'>";

		foreach( $forums->forum_cache['root'] as $fid => $fdata )
		{
			$choose .= "<option value='{$fid}'>{$fdata['name']}</option>\n";
		}

		$choose .= "</select>";

		//-----------------------------------------
		// Printy
		//-----------------------------------------

		$html = "<script type='text/javascript'>
				 function gochildrenofthecorn()
				 {
				 	var chosenroot = document.forms[0].roots.options[document.forms[0].roots.selectedIndex].value;

				 	self.location.href = '{$ibforums->base_url}&act=forum&code=reorder&f=' + chosenroot;
				 }
				 </script>
				 <table cellpadding='0' cellspacing='0' width='100%' border='0'>
				 <tr>
				  <td align='left' valign='middle'>{$choose}&nbsp;<input type='button' class='realbutton' value='Reorder Children' onclick='gochildrenofthecorn()'/></td>
				  <td align='right'>".$ibforums->adskin->js_make_button("Add New Root Forum", $ibforums->base_url."&act=forum&code=new&c=1")."
				  &nbsp;&nbsp;<input type='submit' value='Reorder Root Forums' class='realbutton' /></form>
				  </td>
				 </tr>
				 </table>";

		$ibforums->html .= $ibforums->adskin->add_standalone_row($html, 'left');

		$ibforums->admin->nav[] = array( '', 'Manage Forums' );

		$ibforums->admin->output();

	}


}


?>