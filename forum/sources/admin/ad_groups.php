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
|   > Date started: 17th March 2002
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


class ad_groups {

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
			case 'doadd':
				$this->save_group('add');
				break;

			case 'add':
				$this->group_form('add');
				break;

			case 'edit':
				$this->group_form('edit');
				break;

			case 'doedit':
				$this->save_group('edit');
				break;

			case 'delete':
				$this->delete_form();
				break;

			case 'dodelete':
				$this->do_delete();
				break;

			//-----------------------------------------

			case 'fedit':
				$this->forum_perms();
				break;

			case 'pdelete':
				$this->delete_mask();
				break;

			case 'dofedit':
				$this->do_forum_perms();
				break;

			case 'permsplash':
				$this->permsplash();
				break;

			case 'view_perm_users':
				$this->view_perm_users();
				break;

			case 'remove_mask':
				$this->remove_mask();
				break;

			case 'preview_forums':
				$this->preview_forums();
				break;

			case 'dopermadd':
				$this->add_new_perm();
				break;

			case 'donameedit':
				$this->edit_name_perm();
				break;
			//-----------------------------------------



			default:
				$this->main_screen();
				break;
		}

	}

	//-----------------------------------------
	//
	// Member group /forum mask permission form thingy doodle do yes. Viewing Perm users
	//
	//-----------------------------------------

	function delete_mask()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for a valid ID
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the permission mask setty doodle thingy ID, please try again");
		}

		$DB->simple_exec_query( array( 'delete' => 'forum_perms', 'where' => "perm_id=".$ibforums->input['id'] ) );

		$old_id = intval($ibforums->input['id']);

		//-----------------------------------------
		// Remove from forums...
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, permission_array', 'from' => 'forums' ) );
		$get = $DB->simple_exec();

		while( $f = $DB->fetch_row($get) )
		{
			$d_str = "";
			$d_arr = unserialize(stripslashes( $f['permission_array'] ) );

			$perms = unserialize(stripslashes( $f['permission_array'] ) );

			foreach( array( 'read_perms', 'reply_perms', 'start_perms', 'upload_perms', 'show_perms' ) as $perm_bit )
			{
				if ($perms[ $perm_bit ] != '*')
				{
					if ( preg_match( "/(^|,)".$old_id."(,|$)/", $perms[ $perm_bit ]) )
					{
						$perms[ $perm_bit ] = preg_replace( "/(^|,)".$old_id."(,|$)/", "\\1\\2", $perms[ $perm_bit ] );

						$d_arr[ $perm_bit ] = $this->clean_perms( $perms[ $perm_bit ] );
					}
				}
			}

			//-----------------------------------------
			// Do we have anything to save?
			//-----------------------------------------

			if ( count($d_arr) > 0 )
			{
				//-----------------------------------------
				// Sure?..
				//-----------------------------------------

				$string = addslashes(serialize( $d_arr ) );

				if ( strlen($string) > 5)
				{
					$DB->do_update( 'forums', array( 'permission_array' => $string ), 'id='.$f['id'] );
				}
			}
		}

		//-----------------------------------------
		// Recache forums
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_forums.php' );
		$ad_forums = new ad_forums();
		$ad_forums->recache_forums();

		$this->permsplash();
	}

	//-----------------------------------------


	function add_new_perm()
	{
		global $ibforums, $DB,  $std;

		$ibforums->input['new_perm_name'] = trim($ibforums->input['new_perm_name']);

		if ($ibforums->input['new_perm_name'] == "")
		{
			$ibforums->admin->error("You must enter a name");
		}

		$copy_id = $ibforums->input['new_perm_copy'];

		//-----------------------------------------
		// UPDATE DB
		//-----------------------------------------

		$DB->do_insert( 'forum_perms', array( 'perm_name' => $ibforums->input['new_perm_name'] ) );

		$new_id = $DB->get_insert_id();

		if ( $copy_id != 'none' )
		{
			//-----------------------------------------
			// Add new mask to forum accesses
			//-----------------------------------------

			$old_id = intval($copy_id);

			if ( ($new_id > 0) and ($old_id > 0) )
			{
				$DB->simple_construct( array( 'select' => 'id, permission_array', 'from' => 'forums' ) );
				$get = $DB->simple_exec();

				while( $f = $DB->fetch_row($get) )
				{
					$d_str = "";
					$d_arr = unserialize(stripslashes( $f['permission_array'] ) );

					$perms = unserialize(stripslashes( $f['permission_array'] ) );

					foreach( array( 'read_perms', 'reply_perms', 'start_perms', 'upload_perms', 'show_perms' ) as $perm_bit )
					{
						if ( $perms[ $perm_bit ] != '*')
						{
							if ( preg_match( "/(^|,)".$old_id."(,|$)/", $perms[ $perm_bit ]) )
							{
								$d_arr[ $perm_bit ] = $this->clean_perms( $perms[ $perm_bit ] ) . ",".$new_id;
							}
						}
					}

					//-----------------------------------------
					// Do we have anything to save?
					//-----------------------------------------

					if ( count($d_arr) > 0 )
					{
						$string = addslashes(serialize( $d_arr ) );

						//-----------------------------------------
						// Sure?..
						//-----------------------------------------

						if ( strlen($string) > 5)
						{
							$DB->do_update( 'forums', array( 'permission_array' => $string ), 'id='.$f['id'] );
						}
					}
				}
			}
		}

		//-----------------------------------------
		// Recache forums
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_forums.php' );
		$ad_forums = new ad_forums();
		$ad_forums->recache_forums();

		$this->permsplash();
	}

	//-----------------------------------------
	// Preview masks
	//-----------------------------------------

	function preview_forums()
	{
		global $ibforums, $DB,  $std, $forums;

		//-----------------------------------------
		// Check for a valid ID
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the permission mask setty doodle thingy ID, please try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'where' => "perm_id=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		if ( ! $perms = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the permission mask setty doodle thingy ID, please try again");
		}

		//-----------------------------------------
		// What we doin'?
		//-----------------------------------------

		switch( $ibforums->input['t'] )
		{
			case 'start':
				$human_type = 'Start Topics';
				$code_word  = 'start_perms';
				break;

			case 'reply':
				$human_type = 'Reply To Topics';
				$code_word  = 'reply_perms';
				break;

			case 'show':
				$human_type = 'Show Forums';
				$code_word  = 'show_perms';
				break;

			case 'upload':
				$human_type = 'Upload Attachments';
				$code_word  = 'upload_perms';
				break;

			default:
				$human_type = 'View Forum';
				$code_word  = 'read_perms';
				break;
		}

		//-----------------------------------------
		// Get all members using that ID then!
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "$human_type" , "100%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Preview using: " . $perms['perm_name'] );

		$the_html   = "";

		$perm_id    = intval($ibforums->input['id']);

		$theforums  = $this->forumfunc->ad_forums_forum_list(1);

		foreach( $theforums as $i => $v )
		{
			$id   = $v[0];
			$name = $v[1];

			if ($forums->forum_by_id[$id][ $code_word ] == '*')
			{
				$the_html[] = $name;
			}
			else if (preg_match( "/(^|,)".$perm_id."(,|$)/", $forums->forum_by_id[$id][ $code_word ]) )
			{
				$the_html[] = $name;
			}
			else
			{
				//-----------------------------------------
				// CAN'T ACCESS
				//-----------------------------------------

				$the_html[] = "<span style='color:gray'>".$name."</span>";
			}
		}

		$html = implode( "<br />", $the_html );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( $html ) );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'preview_forums' ),
																 2 => array( 'act'   , 'group'   ),
																 3 => array( 'id'    , $ibforums->input['id']      ),
														)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;" , "40%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Legend & Info" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"Can $human_type for this forum",
													"<input type='text' readonly='readonly' style='border:1px solid black;background-color:black;size=30px' name='blah'>"
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"CANNOT $human_type for this forum",
													"<input type='text' readonly='readonly' style='border:1px solid gray;background-color:gray;size=30px' name='blah'>"
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"Test with...",
													$ibforums->adskin->form_dropdown( 't',
																		array( 0 => array( 'start', 'Start Topics'    ),
																			   1 => array( 'reply', 'Reply To Topics' ),
																			   2 => array( 'read' , 'Read Forum'      ),
																			   3 => array( 'show' , 'Show Forum'      ),
																			   4 => array( 'upload', 'Upload Forum'   ),
																			  ), $ibforums->input['t'] )
										 )      );

		$ibforums->html .= $ibforums->adskin->end_form( "Update" );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}

	//===========================================================================

	function remove_mask()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for a valid ID
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the member ID, please try again");
		}

		//-----------------------------------------
		// Get, check and reset
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, name, org_perm_id', 'from' => 'members', 'where' => "id=".intval($ibforums->input['id']) ) );
		$DB->simple_exec();

		if ( ! $mem = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the member ID, please try again");
		}

		if ( $ibforums->input['pid'] == 'all' )
		{
			$DB->do_update( 'members', array( 'org_perm_id' => 0 ), 'id='.intval($ibforums->input['id']));
		}
		else
		{
			$ibforums->input['pid'] = intval($ibforums->input['pid']);

			$pid_array = explode( ",", $mem['org_perm_id'] );

			if ( count($pid_array) < 2 )
			{
				$DB->do_update( 'members', array( 'org_perm_id' => 0 ), 'id='.intval($ibforums->input['id']));
			}
			else
			{
				$new_arr = array();

				foreach( $pid_array as $sid )
				{
					if ( $sid != $ibforums->input['pid'] )
					{
						$new_arr[] = $sid;
					}
				}

				$DB->do_update( 'members', array( 'org_perm_id' => implode(",",$new_arr) ), 'id='.intval($ibforums->input['id']));
			}
		}

		//-----------------------------------------
		// Get all members using that ID then!
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Result" );



		$ibforums->html .= $ibforums->adskin->add_td_row( array( "Removed the custom permission mask from <b>{$mem['name']}</b>." )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}

	//===========================================================================


	function view_perm_users()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for a valid ID
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the permission mask setty doodle thingy ID, please try again");
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'where' => "perm_id=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		if ( ! $perms = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the permission mask setty doodle thingy ID, please try again");
		}

		//-----------------------------------------
		// Get all members using that ID then!
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "User Details" , "50%" );
		$ibforums->adskin->td_header[] = array( "Action"       , "50%" );

		//-----------------------------------------

		$ibforums->html .= "<script language='javascript' type='text/javascript'>
						 <!--
						  function pop_close_and_stop( id )
						  {
						  	opener.location = \"{$ibforums->adskin->base_url}&act=mem&code=doform&mid=\" + id;
						  	self.close();
						  }
						  //-->
						  </script>";

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Members using: " . $perms['perm_name'] );

		$DB->simple_construct( array( 'select' => 'id, name, email, posts, org_perm_id',
									  'from'   => 'members',
									  'where'  => "(org_perm_id IS NOT NULL AND org_perm_id <> 0)",
									  'order'  => 'name' ) );
		$outer = $DB->simple_exec();

		while( $r = $DB->fetch_row($outer) )
		{
			$exp_pid = explode( ",", $r['org_perm_id'] );

			foreach( explode( ",", $r['org_perm_id'] ) as $pid )
			{
				if ( $pid == $ibforums->input['id'] )
				{
					if ( count($exp_pid) > 1 )
					{
						$extra = "<li>Also using: <em style='color:red'>";

						$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'where' => "perm_id IN ({$r['org_perm_id']}) AND perm_id <> {$ibforums->input['id']}" ) );
						$DB->simple_exec();

						while ( $mr = $DB->fetch_row() )
						{
							$extra .= $mr['perm_name'].",";
						}

						$extra = preg_replace( "/,$/", "", $extra );

						$extra .= "</em>";
					}
					else
					{
						$extra = "";
					}

					$ibforums->html .= $ibforums->adskin->add_td_row( array( "<div style='font-weight:bold;font-size:11px;padding-bottom:6px;margin-bottom:3px;border-bottom:1px solid #000'>{$r['name']}</div>
															   <li>Posts: {$r['posts']}
															   <li>Email: {$r['email']}
															   $extra" ,
															  "&#149;&nbsp;<a href='{$ibforums->adskin->base_url}&amp;act=group&amp;code=remove_mask&amp;id={$r['id']}&amp;pid=$pid' title='Remove this mask from the user (will not remove all if they have multimasks'>Remove This Mask</a>
															   <br />&#149;&nbsp;<a href='{$ibforums->adskin->base_url}&amp;act=group&amp;code=remove_mask&amp;id={$r['id']}&amp;pid=all' title='Remove all user masks'>Remove All Masks</a>
															   <br /><br />&#149;&nbsp;<a href='javascript:pop_close_and_stop(\"{$r['id']}\");'>Edit Member</a>",
													 )      );
				}
			}
		}


		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();

	}


	//-----------------------------------------
	//
	// Member group /forum mask permission form thingy doodle do yes.
	//
	//-----------------------------------------


	function permsplash()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Forum Permission Mask [ HOME ]";

		$ibforums->admin->page_detail = "You can manage your forum permission masks from this section.";

		$ibforums->admin->page_detail .= "<br /><b>Used by Groups</b> relates to the member groups that use this permission mask
								<br /><b>Used by Members</b> relates to the number of members that have this permission mask set to over ride the group used permission mask
							    <br /><b>Preview</b> the forums this mask has access to in a quick, convenient format
							   ";


		//-----------------------------------------
		// Get the names for the perm masks w/id
		//-----------------------------------------

		$perms = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			$perms[ $r['perm_id'] ] = $r['perm_name'];
		}

		//-----------------------------------------
		// Get the number of members using this mask
		// as an over ride
		//-----------------------------------------

		$mems = array();

		$DB->cache_add_query( 'groups_permsplash', array() );
		$DB->cache_exec_query();

		while( $r = $DB->fetch_row() )
		{
			if ( strstr($r['org_perm_id'] , "," ) )
			{
				foreach( explode( ",", $r['org_perm_id'] ) as $pid )
				{
					$mems[ $pid ] += $r['count'];
				}
			}
			else
			{
				$mems[ $r['org_perm_id'] ] += $r['count'];
			}
		}

		//-----------------------------------------
		// Get the member group names and the mask
		// they use
		//-----------------------------------------

		$groups = array();

		$DB->simple_construct( array( 'select' => 'g_id, g_title, g_perm_id', 'from' => 'groups' ) );
		$DB->simple_exec();

		while( $r = $DB->fetch_row() )
		{
			if ( strstr($r['g_perm_id'] , "," ) )
			{
				foreach( explode( ",", $r['g_perm_id'] ) as $pid )
				{
					$groups[ $pid ][] = $r['g_title'];
				}
			}
			else
			{
				$groups[ $r['g_perm_id'] ][] = $r['g_title'];
			}
		}

		//-----------------------------------------
		// Print the splash screen
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Mask Name"          , "20%" );
		$ibforums->adskin->td_header[] = array( "Used by Group(s)"   , "20%" );
		$ibforums->adskin->td_header[] = array( "Used by Mem(s)"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Preview"            , "10%" );
		$ibforums->adskin->td_header[] = array( "Edit"               , "15%" );
		$ibforums->adskin->td_header[] = array( "Delete"             , "15%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->js_pop_win();

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Forum Permission Masks" );

		foreach( $perms as $id => $name )
		{
			$groups_used = "";

			$is_active = 0;

			if ( is_array( $groups[ $id ] ) )
			{
				foreach( $groups[ $id ] as $bleh => $g_title )
				{
					$groups_used .= $g_title . "<br />";
				}

				$is_active = 1;

			}
			else
			{
				$groups_used = "<center><i>None</i></center>";
			}

			$mems_used = 0;

			if ( $mems[ $id ] > 0 )
			{
				$is_active = 1;
				$mems_used = $mems[ $id ] . " (<a href='javascript:pop_win(\"&amp;act=group&amp;code=view_perm_users&amp;id=$id\", \"User\", \"500\",\"350\");' title='View the member names of those using this mask in a new window'>View</a>)";
			}

			if ( $is_active > 0 )
			{
				$delete = "<i>Can't, in use</i>";
			}
			else
			{
				$delete = "<a href='{$ibforums->adskin->base_url}&amp;act=group&amp;code=pdelete&amp;id=$id'>Delete</a>";
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>$name</b>" ,
													  "$groups_used",
													  "<center>$mems_used</center>",
													  "<center><a href='javascript:pop_win(\"&amp;act=group&amp;code=preview_forums&amp;id=$id&amp;t=read\", \"400\",\"350\");' title='See what this group can see..'>Preview</a></center>",
													  "<center><a href='{$ibforums->adskin->base_url}&amp;act=group&amp;code=fedit&amp;id=$id'>Edit</a></center>",
													  "<center>$delete</center>",
											 )      );

		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$dlist = array();

		$dlist[] = array( 'none', 'Do not inherit' );

		foreach( $perms as $id => $name )
		{
			$dlist[] = array( $id, $name );
		}

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dopermadd' ),
												  2 => array( 'act'   , 'group'   ),
									     )      );


		$ibforums->adskin->td_header[] = array( "{none}" , "60%" );
		$ibforums->adskin->td_header[] = array( "{none}" , "40%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Create a new permission mask" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Permission Mask Name</b>" ,
												  $ibforums->adskin->form_input( 'new_perm_name' ),
										 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Inherit forum permission mask from...</b>" ,
												 $ibforums->adskin->form_dropdown( 'new_perm_copy', $dlist ),
										 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Create");

		$ibforums->html .= $ibforums->adskin->end_table();



		$ibforums->admin->output();


	}



	function forum_perms()
	{
		global $ibforums, $DB, $std, $forums;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the group ID, please try again");
		}

		//-----------------------------------------

		$ibforums->admin->page_title = "Forum Permission Mask [ EDIT ]";

		$ibforums->admin->page_detail = "You can manage your forum permission masks from this section.";

		$ibforums->admin->page_detail .= "<br />Simply check the boxes to allow permission for that action, or uncheck the box to deny permission for that action.
							   <br /><b>Global</b> indicates that all present and future permission masks have access to that action and as such, cannot be changed";

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'where' => "perm_id=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		$group = $DB->fetch_row();

		$gid   = $group['perm_id'];
		$gname = $group['perm_name'];

		//-----------------------------------------
		//| EDIT NAME
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'donameedit' ),
												  				 2 => array( 'act'   , 'group'   ),
															     3 => array( 'id'    , $gid      ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Rename Group: ".$group['perm_name'] );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Mask Name</b>" ,
												                 $ibforums->adskin->form_input("perm_name", $gname )
									                    )      );

		$ibforums->html .= $ibforums->adskin->end_form("Edit Name");

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//| MAIN FORM
		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dofedit' ),
												                 2 => array( 'act'   , 'group'   ),
												                 3 => array( 'id'    , $gid      ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "Forum Name"   , "25%" );
		$ibforums->adskin->td_header[] = array( "Show"         , "10%" );
		$ibforums->adskin->td_header[] = array( "Read"         , "10%" );
		$ibforums->adskin->td_header[] = array( "Reply"        , "10%" );
		$ibforums->adskin->td_header[] = array( "Start"        , "10%" );
		$ibforums->adskin->td_header[] = array( "Upload"       , "10%" );

		$forum_data = $this->forumfunc->ad_forums_forum_data();

		$ibforums->html .= $ibforums->adskin->start_table( "Forum Access Permissions for ".$group['perm_name'] );

		foreach( $forum_data as $id => $r )
		{
			$show   = "";
			$read   = "";
			$start  = "";
			$reply  = "";
			$upload = "";

			$global = '<center id="mgyellow"><i>Global</i></center>';

			if ($r['show_perms'] == '*')
			{
				$show = $global;
			}
			else if ( preg_match( "/(^|,)".$gid."(,|$)/", $r['show_perms'] ) )
			{
				$show = "<center id='mgyellow'><input type='checkbox' name='show_".$r['id']."' value='1' checked></center>";
			}
			else
			{
				$show = "<center id='mgyellow'><input type='checkbox' name='show_".$r['id']."' value='1'></center>";
			}

			//-----------------------------------------

			$global = '<center id="mgblue"><i>Global</i></center>';

			if ($r['read_perms'] == '*')
			{
				$read = $global;
			}
			else if ( preg_match( "/(^|,)".$gid."(,|$)/", $r['read_perms'] ) )
			{
				$read = "<center id='mgblue'><input type='checkbox' name='read_".$r['id']."' value='1' checked></center>";
			}
			else
			{
				$read = "<center id='mgblue'><input type='checkbox' name='read_".$r['id']."' value='1'></center>";
			}

			//-----------------------------------------

			$global = '<center id="mgred"><i>Global</i></center>';

			if ($r['start_perms'] == '*')
			{
				$start = $global;
			}
			else if ( preg_match( "/(^|,)".$gid."(,|$)/", $r['start_perms'] ) )
			{
				$start = "<center id='mgred'><input type='checkbox' name='start_".$r['id']."' value='1' checked></center>";
			}
			else
			{
				$start = "<center id='mgred'><input type='checkbox' name='start_".$r['id']."' value='1'></center>";
			}

			//-----------------------------------------

			$global = '<center id="mggreen"><i>Global</i></center>';

			if ($r['reply_perms'] == '*')
			{
				$reply = $global;
			}
			else if ( preg_match( "/(^|,)".$gid."(,|$)/", $r['reply_perms'] ) )
			{
				$reply = "<center id='mggreen'><input type='checkbox' name='reply_".$r['id']."' value='1' checked></center>";
			}
			else
			{
				$reply = "<center id='mggreen'><input type='checkbox' name='reply_".$r['id']."' value='1'></center>";
			}

			//-----------------------------------------

			$global = '<center id="memgroup"><i>Global</i></center>';

			if ($r['upload_perms'] == '*')
			{
				$upload = $global;
			}
			else if ( preg_match( "/(^|,)".$gid."(,|$)/", $r['upload_perms'] ) )
			{
				$upload = "<center id='memgroup'><input type='checkbox' name='upload_".$r['id']."' value='1' checked></center>";
			}
			else
			{
				$upload = "<center id='memgroup'><input type='checkbox' name='upload_".$r['id']."' value='1'></center>";
			}

			//-----------------------------------------

			if ( $r['root_forum'] )
			{
				$css = 'pformstrip';
			}
			else
			{
				$css = '';
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array(
																	 "<b>".$r['depthed_name']."</b>",
																	 $show,
																	 $read,
																	 $reply,
																	 $start,
																	 $upload
										 					) ,$css  );

		}

		$ibforums->html .= $ibforums->adskin->end_form("Update Forum Permissions");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}


	function edit_name_perm()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for legal ID
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve that group ID");
		}

		if ( $ibforums->input['perm_name'] == "" )
		{
			$ibforums->admin->error("You must enter a name");
		}

		$gid = $ibforums->input['id'];

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'where' => "perm_id=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		if ( ! $gr = $DB->fetch_row() )
		{
			$ibforums->admin->error("Not a valid group ID");
		}

		$DB->do_update( 'forum_perms', array( 'perm_name' => $ibforums->input['perm_name'] ), 'perm_id='.$ibforums->input['id'] );

		$ibforums->admin->save_log("Forum Access Permissions Name Edited for Mask: '{$gr['perm_name']}'");

		$ibforums->admin->done_screen("Forum Access Permissions Updated", "Permission Mask Control", "act=group&code=permsplash" );
	}



	function do_forum_perms()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Check for legal ID
		//-----------------------------------------

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve that group ID");
		}

		$gid = $ibforums->input['id'];

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'where' => "perm_id=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		if ( ! $gr = $DB->fetch_row() )
		{
			$ibforums->admin->error("Not a valid group ID");
		}

		//-----------------------------------------
		// Pull the forum data..
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forums', 'order' => "position ASC" ) );
		$forum_q = $DB->simple_exec();

		while ( $row = $DB->fetch_row( $forum_q ) )
		{
			$perms = unserialize(stripslashes( $row['permission_array'] ) );

			$read   = "";
			$reply  = "";
			$start  = "";
			$upload = "";
			$show   = "";

			//-----------------------------------------
			// Is this global?
			//-----------------------------------------

			if ($perms['read_perms'] == '*')
			{
				$read = '*';

			}
			else
			{
				//-----------------------------------------
				// Split the set IDs
				//-----------------------------------------

				$read_ids = explode( ",", $perms['read_perms'] );

				if ( is_array($read_ids) )
				{
				   foreach ($read_ids as $i)
				   {
					   //-----------------------------------------
					   // If it's the current ID, skip
					   //-----------------------------------------

					   if ($gid == $i)
					   {
						   continue;
					   }
					   else
					   {
						   $read .= $i.",";
					   }
				   }
				}
				//-----------------------------------------
				// Was the box checked?
				//-----------------------------------------

				if ($ibforums->input[ 'read_'.$row['id'] ] == 1)
				{
					// Add our group ID...

					$read .= $gid.",";
				}

				// Tidy..

				$read = preg_replace( "/,$/", "", $read );
				$read = preg_replace( "/^,/", "", $read );

			}

			//-----------------------------------------
			// Reply topics..
			//-----------------------------------------

			if ($perms['reply_perms'] == '*')
			{
				$reply = '*';
			}
			else
			{
				$reply_ids = explode( ",", $perms['reply_perms'] );

				if ( is_array($reply_ids) )
				{
					foreach ($reply_ids as $i)
					{
						if ($gid == $i)
						{
							continue;
						}
						else
						{
							$reply .= $i.",";
						}
					}

				}

				if ($ibforums->input[ 'reply_'.$row['id'] ] == 1)
				{
					$reply .= $gid.",";
				}

				$reply = preg_replace( "/,$/", "", $reply );
				$reply = preg_replace( "/^,/", "", $reply );
			}

			//-----------------------------------------
			// Start topics..
			//-----------------------------------------

			if ($perms['start_perms'] == '*')
			{
				$start = '*';
			}
			else
			{
				$start_ids = explode( ",", $perms['start_perms'] );

				if ( is_array($start_ids) )
				{

					foreach ($start_ids as $i)
					{
						if ($gid == $i)
						{
							continue;
						}
						else
						{
							$start .= $i.",";
						}
					}

				}

				if ($ibforums->input[ 'start_'.$row['id'] ] == 1)
				{
					$start .= $gid.",";
				}

				$start = preg_replace( "/,$/", "", $start );
				$start = preg_replace( "/^,/", "", $start );
			}

			//-----------------------------------------
			// Upload topics..
			//-----------------------------------------

			if ($perms['upload_perms'] == '*')
			{
				$upload = '*';
			}
			else
			{
				$upload_ids = explode( ",", $perms['upload_perms'] );

				if ( is_array($upload_ids) )
				{

					foreach ($upload_ids as $i)
					{
						if ($gid == $i)
						{
							continue;
						}
						else
						{
							$upload .= $i.",";
						}
					}

				}

				if ($ibforums->input[ 'upload_'.$row['id'] ] == 1)
				{
					$upload .= $gid.",";
				}

				$upload = preg_replace( "/,$/", "", $upload );
				$upload = preg_replace( "/^,/", "", $upload );
			}

			//-----------------------------------------
			// Show topics..
			//-----------------------------------------

			if ($perms['show_perms'] == '*')
			{
				$show = '*';
			}
			else
			{
				$show_ids = explode( ",", $perms['show_perms'] );

				if ( is_array($show_ids) )
				{
					foreach ($show_ids as $i)
					{
						if ($gid == $i)
						{
							continue;
						}
						else
						{
							$show .= $i.",";
						}
					}

				}

				if ($ibforums->input[ 'show_'.$row['id'] ] == 1)
				{
					$show .= $gid.",";
				}

				$show = preg_replace( "/,$/", "", $show );
				$show = preg_replace( "/^,/", "", $show );
			}

			//-----------------------------------------
			// Update the DB...
			//-----------------------------------------

			$DB->do_update( 'forums', array( 'permission_array' => addslashes(serialize(array(
																						   'start_perms'  => $start,
																						   'reply_perms'  => $reply,
																						   'read_perms'   => $read,
																						   'upload_perms' => $upload,
																						   'show_perms'   => $show
							    		)		  						 )         )      ), 'id='.$row['id']);

		}

		//-----------------------------------------
		// Recache forums
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/admin/ad_forums.php' );
		$adforums = new ad_forums();

		$adforums->recache_forums();

		$ibforums->admin->save_log("Forum Access Permissions Edited for Mask: '{$gr['perm_name']}'");

		$ibforums->admin->done_screen("Forum Access Permissions Updated", "Permission Mask Control", "act=group&code=permsplash", 'redirect' );

	}

	//-----------------------------------------
	//
	// Delete a group
	//
	//-----------------------------------------

	function delete_form()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the group ID, please try again");
		}

		if ($ibforums->input['id'] < 5)
		{
			$ibforums->admin->error("You can not move the preset groups. You can rename them and edit the functionality");
		}

		$ibforums->admin->page_title = "Deleting a User Group";

		$ibforums->admin->page_detail = "Please check to ensure that you are attempting to remove the correct group.";


		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'COUNT(id) as users', 'from' => 'members', 'where' => "mgroup=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		$black_adder = $DB->fetch_row();

		if ($black_adder['users'] < 1)
		{
			$black_adder['users'] = 0;
		}

		$DB->simple_construct( array( 'select' => 'g_title', 'from' => 'groups', 'where' => "g_id=".$ibforums->input['id'] ) );
		$DB->simple_exec();

		$group = $DB->fetch_row();

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'where' => "g_id <> ".$ibforums->input['id'] ) );
		$DB->simple_exec();

		$mem_groups = array();

		while ( $r = $DB->fetch_row() )
		{
			$mem_groups[] = array( $r['g_id'], $r['g_title'] );
		}

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'dodelete'  ),
																 2 => array( 'act'   , 'group'     ),
																 3 => array( 'id'    , $ibforums->input['id']   ),
																 4 => array( 'name'  , $group['g_title'] ),
														)      );



		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Removal Confirmation: ".$group['g_title'] );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Number of users in this group</b>" ,
												  "<b>".$black_adder['users']."</b>",
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Move users in this group to...</b>" ,
												  $ibforums->adskin->form_dropdown("to_id", $mem_groups )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Delete this group");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	function do_delete()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the group ID, please try again");
		}

		if ($ibforums->input['to_id'] == "")
		{
			$ibforums->admin->error("No move to group ID was specified. /me cries.");
		}

		// Check to make sure that the relevant groups exist.

		$DB->simple_construct( array( 'select' => 'g_id', 'from' => 'groups', 'where' => "g_id IN(".$ibforums->input['id'].",".$ibforums->input['to_id'].")" ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() != 2 )
		{
			$ibforums->admin->error("Could not resolve the ID's passed to group deletion");
		}

		$DB->do_update( 'members', array( 'mgroup' => $ibforums->input['to_id'] ), 'mgroup='.$ibforums->input['id'] );

		$DB->simple_exec_query( array( 'delete' => 'groups', 'where' => "g_id=".$ibforums->input['id'] ) );

		// Look for promotions in case we have members to be promoted to this group...

		$DB->simple_construct( array( 'select' => 'g_id', 'from' => 'groups', 'where' => "g_promotion LIKE '{$ibforums->input['id']}&%'" ) );
		$prq = $DB->simple_exec();

		while ( $row = $DB->fetch_row($prq) )
		{
			$DB->do_update( 'groups', array( 'g_promotion' => '-1&-1' ), 'g_id='.$row['g_id'] );
		}

		// Remove from moderators table

		$DB->simple_exec_query( array( 'delete' => 'moderators', 'where' => "is_group=1 AND group_id=".$ibforums->input['id'] ) );

		$ibforums->admin->save_log("Member Group '{$ibforums->input['name']}' removed");

		$ibforums->admin->done_screen("Group Removed", "Group Control", "act=group" );

	}


	//-----------------------------------------
	//
	// Save changes to DB
	//
	//-----------------------------------------

	function save_group($type='edit')
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['g_title'] == "")
		{
			$ibforums->admin->error("You must enter a group title.");
		}

		if ($type == 'edit')
		{
			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("Could not resolve the group id");
			}

			if ($ibforums->input['id'] == $ibforums->vars['admin_group'] and $ibforums->input['g_access_cp'] != 1)
			{
				$ibforums->admin->error("You can not remove the ability to access the admin control panel for this group");
			}
		}

		//-----------------------------------------
		// Sort out the perm mask id things
		//-----------------------------------------

		if ( is_array( $_POST['permid'] ) )
		{
			$perm_id = implode( ",", $_POST['permid'] );
		}
		else
		{
			$ibforums->admin->error("No permission masks chosen");
		}

		// Build up the hashy washy for the database ..er.. wase.

		$prefix = preg_replace( "/&#39;/", "'" , $std->txt_safeslashes($_POST['prefix']) );
		$prefix = preg_replace( "/&lt;/" , "<" , $prefix          );
		$suffix = preg_replace( "/&#39;/", "'" , $std->txt_safeslashes($_POST['suffix']) );
		$suffix = preg_replace( "/&lt;/" , "<" , $suffix          );

		$promotion_a = '-1'; //id
		$promotion_b = '-1'; // posts

		if ($ibforums->input['g_promotion_id'] > 0)
		{
			$promotion_a = $ibforums->input['g_promotion_id'];
			$promotion_b = $ibforums->input['g_promotion_posts'];
		}

		//if ($ibforums->input['g_max_messages'] == 0)
		//{
			//$ibforums->input['g_max_messages'] = -1;
		//}

		//list($p_max, $p_width, $p_height) = explode( ":", $group['g_photo_max_vars'] );

		if ( $ibforums->input['g_attach_per_post'] and $ibforums->input['g_attach_max'] > 0 )
		{
			if ( $ibforums->input['g_attach_per_post'] > $ibforums->input['g_attach_max'] )
			{
				$ibforums->main_msg = "You cannot specify a per post limit greater than the globally allowed limit.";
				$this->group_form('edit');
			}
		}

		$ibforums->input['p_max']    = str_replace( ":", "", $ibforums->input['p_max'] );
		$ibforums->input['p_width']  = str_replace( ":", "", $ibforums->input['p_width'] );
		$ibforums->input['p_height'] = str_replace( ":", "", $ibforums->input['p_height'] );

		$db_string = array(
							 'g_view_board'         => $ibforums->input['g_view_board'],
							 'g_mem_info'           => $ibforums->input['g_mem_info'],
							 'g_other_topics'       => $ibforums->input['g_other_topics'],
							 'g_use_search'         => $ibforums->input['g_use_search'],
							 'g_email_friend'       => $ibforums->input['g_email_friend'],
							 'g_invite_friend'      => $ibforums->input['g_invite_friend'],
							 'g_edit_profile'       => $ibforums->input['g_edit_profile'],
							 'g_post_new_topics'    => $ibforums->input['g_post_new_topics'],
							 'g_reply_own_topics'   => $ibforums->input['g_reply_own_topics'],
							 'g_reply_other_topics' => $ibforums->input['g_reply_other_topics'],
							 'g_edit_posts'         => $ibforums->input['g_edit_posts'],
							 'g_edit_cutoff'        => $ibforums->input['g_edit_cutoff'],
							 'g_delete_own_posts'   => $ibforums->input['g_delete_own_posts'],
							 'g_open_close_posts'   => $ibforums->input['g_open_close_posts'],
							 'g_delete_own_topics'  => $ibforums->input['g_delete_own_topics'],
							 'g_post_polls'         => $ibforums->input['g_post_polls'],
							 'g_vote_polls'         => $ibforums->input['g_vote_polls'],
							 'g_use_pm'             => $ibforums->input['g_use_pm'],
							 'g_is_supmod'          => $ibforums->input['g_is_supmod'],
							 'g_access_cp'          => $ibforums->input['g_access_cp'],
							 'g_title'              => trim($ibforums->input['g_title']),
							 'g_can_remove'         => $ibforums->input['g_can_remove'],
							 'g_append_edit'        => $ibforums->input['g_append_edit'],
							 'g_access_offline'     => $ibforums->input['g_access_offline'],
							 'g_avoid_q'            => $ibforums->input['g_avoid_q'],
							 'g_avoid_flood'        => $ibforums->input['g_avoid_flood'],
							 'g_icon'               => trim($std->txt_safeslashes($_POST['g_icon'])),
							 'g_attach_max'         => $ibforums->input['g_attach_max'],
							 'g_avatar_upload'      => $ibforums->input['g_avatar_upload'],
							 'g_calendar_post'      => $ibforums->input['g_calendar_post'],
							 'g_max_messages'       => $ibforums->input['g_max_messages'],
							 'g_max_mass_pm'        => $ibforums->input['g_max_mass_pm'],
							 'g_search_flood'       => $ibforums->input['g_search_flood'],
							 'prefix'               => $prefix,
							 'suffix'               => $suffix,
							 'g_promotion'          => $promotion_a.'&'.$promotion_b,
							 'g_hide_from_list'     => $ibforums->input['g_hide_from_list'],
							 'g_post_closed'        => $ibforums->input['g_post_closed'],
							 'g_perm_id'			=> $perm_id,
							 'g_photo_max_vars'	    => $ibforums->input['p_max'].':'.$ibforums->input['p_width'].':'.$ibforums->input['p_height'],
							 'g_dohtml'			    => $ibforums->input['g_dohtml'],
							 'g_edit_topic'			=> $ibforums->input['g_edit_topic'],
							 'g_email_limit'		=> intval($ibforums->input['join_limit']).':'.intval($ibforums->input['join_flood']),
							 'g_bypass_badwords'    => $ibforums->input['g_bypass_badwords'],
							 'g_can_msg_attach'     => $ibforums->input['g_can_msg_attach'],
							 'g_attach_per_post'    => $ibforums->input['g_attach_per_post'],
						  );

		if ($type == 'edit')
		{
			$DB->do_update( 'groups', $db_string, 'g_id='.$ibforums->input['id'] );

			// Update the title of the group held in the mod table incase it changed.

			$DB->do_update( 'moderators', array( 'group_name' => trim($ibforums->input['g_title']) ), 'group_id='.$ibforums->input['id'] );

			$ibforums->admin->save_log("Edited Group '{$ibforums->input['g_title']}'");

			$this->rebuild_group_cache();

			$ibforums->admin->done_screen("Group Edited", "Group Control", "act=group", 'redirect' );

		}
		else
		{
			$DB->do_insert( 'groups', $db_string );

			$ibforums->admin->save_log("Added Group '{$ibforums->input['g_title']}'");

			$this->rebuild_group_cache();

			$ibforums->admin->done_screen("Group Added", "Group Control", "act=group", 'redirect' );
		}
	}

	//-----------------------------------------
	// Rebuild group cache
	//-----------------------------------------

	function rebuild_group_cache()
	{
		global $ibforums, $std, $DB;

		$ibforums->cache['group_cache'] = array();

		$DB->simple_construct( array( 'select' => "*",
									  'from'   => 'groups'
							 )      );

		$DB->simple_exec();

		while ( $i = $DB->fetch_row() )
		{
			$ibforums->cache['group_cache'][ $i['g_id'] ] = $i;
		}

		$std->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );
	}


	//-----------------------------------------
	// Clean Perm string
	//-----------------------------------------

	function clean_perms($str)
	{
		$str = preg_replace( "/,$/", "", $str );
		$str = str_replace(  ",,", ",", $str );

		return $str;
	}

	//-----------------------------------------
	//
	// Add / edit group
	//
	//-----------------------------------------

	function group_form($type='edit')
	{
		global $ibforums, $DB,  $std;

		$all_groups = array( 0 => array ('none', 'Don\'t Promote') );

		if ($type == 'edit')
		{
			if ($ibforums->input['id'] == "")
			{
				$ibforums->admin->error("No group id to select from the database, please try again.");
			}

			if ( $ibforums->vars['admin_group'] == $ibforums->input['id'] )
			{
				if ( $ibforums->member['mgroup'] != $ibforums->vars['admin_group'] )
				{
					$ibforums->admin->error("Sorry, you are unable to edit that group as it's the root admin group");
				}
			}

			$form_code = 'doedit';
			$button    = 'Complete Edit';

		}
		else
		{
			$form_code = 'doadd';
			$button    = 'Add Group';
		}

		if ($ibforums->input['id'] != "")
		{
			$DB->simple_construct( array( 'select' => '*', 'from' => 'groups', 'where' => "g_id=".$ibforums->input['id'] ) );
			$DB->simple_exec();

			$group = $DB->fetch_row();

			$DB->simple_construct( array( 'select' => 'g_id, g_title',
										  'from'   => 'groups',
										  'where'  => "g_id <> {$ibforums->input['id']}",
										  'order'  => 'g_title' ) );
		}
		else
		{
			$group = array();

			$DB->simple_construct( array( 'select' => 'g_id, g_title',
										  'from'   => 'groups',
										  'order'  => 'g_title' ) );
		}

		//-----------------------------------------
		// sort out the promotion stuff
		//-----------------------------------------

		list($group['g_promotion_id'], $group['g_promotion_posts']) = explode( '&', $group['g_promotion'] );

		if ($group['g_promotion_posts'] < 1)
		{
			$group['g_promotion_posts'] = '';
		}

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			if ( $r['g_id'] == $ibforums->vars['admin_group'] )
			{
				continue;
			}

			$all_groups[] = array( $r['g_id'], $r['g_title'] );
		}

		//-----------------------------------------

		$perm_masks = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$perm_masks[] = array( $r['perm_id'], $r['perm_name'] );
		}

		//-----------------------------------------

		if ($type == 'edit')
		{
			$ibforums->admin->page_title = "Editing User Group ".$group['g_title'];
		}
		else
		{
			$ibforums->admin->page_title = 'Adding a new user group';
			$group['g_title'] = 'New Group';
		}

		$guest_legend = "";

		if ($group['g_id'] == $ibforums->vars['guest_group'])
		{
			$guest_legend = "</b><br><i>(Does not apply to guests)</i>";
		}

		$ibforums->admin->page_detail = "Please double check the information before submitting the form.";


		//-----------------------------------------

		$ibforums->html .= "<script language='javascript'>
						 <!--
						  function checkform() {

						  	isAdmin = document.forms[0].g_access_cp;
						  	isMod   = document.forms[0].g_is_supmod;

						  	msg = '';

						  	if (isAdmin[0].checked == true)
						  	{
						  		msg += 'Members in this group can access the Admin Control Panel\\n\\n';
						  	}

						  	if (isMod[0].checked == true)
						  	{
						  		msg += 'Members in this group are super moderators.\\n\\n';
						  	}

						  	if (msg != '')
						  	{
						  		msg = 'Security Check\\n--------------\\nMember Group Title: ' + document.forms[0].g_title.value + '\\n--------------\\n\\n' + msg + 'Is this correct?';

						  		formCheck = confirm(msg);

						  		if (formCheck == true)
						  		{
						  			return true;
						  		}
						  		else
						  		{
						  			return false;
						  		}
						  	}
						  }
						 //-->
						 </script>\n";

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $form_code  ),
																 2 => array( 'act'   , 'group'     ),
																 3 => array( 'id'    , $ibforums->input['id']   ),
														) , 'adform', "onSubmit='return checkform()'" );


		list($p_max, $p_width, $p_height) = explode( ":", $group['g_photo_max_vars'] );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$prefix = preg_replace( "/'/", "&#39;", $group['prefix'] );
		$prefix = preg_replace( "/</", "&lt;" , $prefix          );
		$suffix = preg_replace( "/'/", "&#39;", $group['suffix'] );
		$suffix = preg_replace( "/</", "&lt;" , $suffix          );

		$ibforums->html .= $ibforums->adskin->start_table( "Global Settings", "Basic Group Settings" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Group Title</b>" ,
												  $ibforums->adskin->form_input("g_title", $group['g_title'] )
									     )      );

		//-----------------------------------------
		// Sort out default array
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
			var show = '';

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

		$arr = explode( ",", $group['g_perm_id'] );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Use Forum Permission Access...</b><br>You may choose more than one" ,
												  $ibforums->adskin->form_multiselect("permid[]", $perm_masks, $arr, 5, 'onfocus="saveit(this)"; onchange="saveit(this)";' )."<a href='javascript:show_me();'>Show me selected masks</a>"
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Group Icon Image</b><div style='color:gray'>Can be a relative link, i.e. <b>style_images/1/folder_team_icons/admin.gif</b><br />or it can a full URL starting with <b>'http://'</b><br/ >Use <b>style_images/<#IMG_DIR#>/folder_team_icons/{image}</b> (replace {image} with the image name) to dynamically load the image from the style_image folder based on the member's skin choice.</div>" ,
												  $ibforums->adskin->form_textarea("g_icon", $group['g_icon'] )
									     )      );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Online List Format [Prefix]</b><br>(Can be left blank)<br>(Example:&lt;span style='color:red'&gt;)" ,
												  $ibforums->adskin->form_input("prefix", $prefix )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Online List Format [Suffix]</b><br>(Can be left blank)<br>(Example:&lt;/span&gt;)" ,
												  $ibforums->adskin->form_input("suffix", $suffix )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Hide this group from the member list?</b>" ,
												  $ibforums->adskin->form_yes_no("g_hide_from_list", $group['g_hide_from_list'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Upload Permissions", "Manage permissions for PM and post uploads, etc" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>GLOBAL: Max total global file space for all uploads (Inc. PMs and posts) (in KB)</b>".$ibforums->adskin->js_help_link('mg_upload')."<div class='graytext'>Enter -1 to disable uploads or enter 0 to disable the limit</div>" ,
																 $ibforums->adskin->form_input("g_attach_max", $group['g_attach_max'] ). ' (currently: '.$std->size_format( $group['g_attach_max'] * 1024 ).')'
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>PER POST: Max total file space allowed in each post or PM (in KB)</b>".$ibforums->adskin->js_help_link('mg_upload')."<div class='graytext'>Enter 0 to disable a per post limit. This number must be less than the global amount.</div>" ,
																 $ibforums->adskin->form_input("g_attach_per_post", $group['g_attach_per_post'] ). ' (currently: '.$std->size_format( $group['g_attach_per_post'] * 1024 ).')'
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>PERSONAL PHOTO: Max upload file size (in KB)</b><br>(Leave blank to disallow uploads)" ,
																 $ibforums->adskin->form_input("p_max", $p_max )."<br />"
																 ."Max Width (px): <input type='text' size='3' class='textinput' name='p_width' value='{$p_width}'> "
																 ."Max Height (px): <input type='text' size='3' class='textinput' name='p_height' value='{$p_height}'>"
														)      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>AVATARS: Allow avatar uploads?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_avatar_upload", $group['g_avatar_upload'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>PMs: Allow PM attachments?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_can_msg_attach", $group['g_can_msg_attach'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Global Permissions", "Restricting what this group can do" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can view board?</b>" ,
												  $ibforums->adskin->form_yes_no("g_view_board", $group['g_view_board'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can view OFFLINE board?</b>" ,
												  $ibforums->adskin->form_yes_no("g_access_offline", $group['g_access_offline'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can view member profiles and the member list?</b>" ,
												  $ibforums->adskin->form_yes_no("g_mem_info", $group['g_mem_info'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can view other members topics?</b>" ,
												  $ibforums->adskin->form_yes_no("g_other_topics", $group['g_other_topics'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can use search?</b>" ,
												  $ibforums->adskin->form_yes_no("g_use_search", $group['g_use_search'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Number of seconds for search flood control</b><br>Stops search abuse, enter 0 or leave blank for no flood control" ,
												  $ibforums->adskin->form_input("g_search_flood", $group['g_search_flood'] )
									     )      );

		list( $limit, $flood ) = explode( ":", $group['g_email_limit'] );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can email members from the board?</b><br />Leave bottom section blank to remove limits $guest_legend</b>" ,
												  $ibforums->adskin->form_yes_no("g_email_friend", $group['g_email_friend'] )
												 ."<br />Only allow ". $ibforums->adskin->form_simple_input("join_limit", $limit, 2 )." emails in a 24hr period"
												 ."<br />...and only allow 1 email every ".$ibforums->adskin->form_simple_input("join_flood", $flood, 2 )." minutes"
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can edit own profile info?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_edit_profile", $group['g_edit_profile'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can use PM system?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_use_pm", $group['g_use_pm'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Max. Number users allowed to mass PM?$guest_legend<br>(Enter 0 or leave blank to disable mass PM)" ,
												  $ibforums->adskin->form_input("g_max_mass_pm", $group['g_max_mass_pm'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Max. Number of storable messages?$guest_legend" ,
												  $ibforums->adskin->form_input("g_max_messages", $group['g_max_messages'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Posting Permissions", "Restrict where this group can post" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can post new topics (where allowed)?</b>" ,
												  $ibforums->adskin->form_yes_no("g_post_new_topics", $group['g_post_new_topics'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can reply to OWN topics?</b>" ,
												  $ibforums->adskin->form_yes_no("g_reply_own_topics", $group['g_reply_own_topics'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can reply to OTHER members topics (where allowed)?</b>" ,
												  $ibforums->adskin->form_yes_no("g_reply_other_topics", $group['g_reply_other_topics'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can edit own posts?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_edit_posts", $group['g_edit_posts'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Edit time restriction (in minutes)?$guest_legend<br>Denies user edit after the time set has passed. Leave blank or enter 0 for no restriction" ,
												  $ibforums->adskin->form_input("g_edit_cutoff", $group['g_edit_cutoff'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow user to remove 'Edited by' legend?$guest_legend</b>" ,
												  $ibforums->adskin->form_yes_no("g_append_edit", $group['g_append_edit'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can delete own posts?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_delete_own_posts", $group['g_delete_own_posts'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can open/close own topics?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_open_close_posts", $group['g_open_close_posts'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can edit own topic title & description?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_edit_topic", $group['g_edit_topic'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can delete own topics?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_delete_own_topics", $group['g_delete_own_topics'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can start new polls (where allowed)?$guest_legend</b>" ,
												  $ibforums->adskin->form_yes_no("g_post_polls", $group['g_post_polls'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can vote in polls (where allowed)?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_vote_polls", $group['g_vote_polls'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can avoid flood control?</b>" ,
												  $ibforums->adskin->form_yes_no("g_avoid_flood", $group['g_avoid_flood'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can avoid moderation queues?</b>" ,
												  $ibforums->adskin->form_yes_no("g_avoid_q", $group['g_avoid_q'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can add events to the calendar?$guest_legend</b>" ,
												  $ibforums->adskin->form_yes_no("g_calendar_post", $group['g_calendar_post'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can post HTML?$guest_legend</b><br />".$ibforums->adskin->js_help_link('mg_dohtml') ,
												  $ibforums->adskin->form_yes_no("g_dohtml", $group['g_dohtml'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can bypass the bad word filter?$guest_legend</b><br />" ,
												  $ibforums->adskin->form_yes_no("g_bypass_badwords", $group['g_bypass_badwords'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Moderation Permissions", "Allow or deny this group moderation abilities" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Is Super Moderator (can moderate anywhere)?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_is_supmod", $group['g_is_supmod'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Can access the Admin CP?$guest_legend" ,
												  $ibforums->adskin->form_yes_no("g_access_cp", $group['g_access_cp'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Allow user group to post in 'closed' topics?" ,
												  $ibforums->adskin->form_yes_no("g_post_closed", $group['g_post_closed'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Group Promotion" );

		if ($group['g_id'] == $ibforums->vars['admin_group'])
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Choose 'Don't Promote' to disable promotions</b><br>".$ibforums->adskin->js_help_link('mg_promote') ,
													  "Feature disable for the root admin group, after all - if you're at the top where can you be promoted to?"
											 )      );
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Choose 'Don't Promote' to disable promotions</b>$guest_legend<br>".$ibforums->adskin->js_help_link('mg_promote') ,
													  'Promote members of this group to: '.$ibforums->adskin->form_dropdown("g_promotion_id", $all_groups, $group['g_promotion_id'] )
													 .'<br>when they reach '.$ibforums->adskin->form_simple_input('g_promotion_posts', $group['g_promotion_posts'] ).' posts'
											 )      );
		}


		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------
	//
	// Show "Management Screen
	//
	//-----------------------------------------

	function main_screen()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "User Groups";

		$ibforums->admin->page_detail = "User Grouping is a quick and powerful way to organise your members. There are 4 preset groups that you cannot remove (Validating, Guest, Member and Admin) although you may edit these at will. A good example of user grouping is to set up a group called 'Moderators' and allow them access to certain forums other groups do not have access to.<br>Forum access allows you to make quick changes to that groups forum read, write and reply settings. You may do this on a forum per forum basis in forum control.";

		$g_array = array();

		$ibforums->adskin->td_header[] = array( "Group Title"    , "30%" );
		$ibforums->adskin->td_header[] = array( "Access ACP?"    , "15%" );
		$ibforums->adskin->td_header[] = array( "Super Mod?"     , "15%" );
		$ibforums->adskin->td_header[] = array( "Members"        , "10%" );
		$ibforums->adskin->td_header[] = array( "Edit Group"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Delete"         , "10%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "User Group Management" );

		$DB->cache_add_query( 'groups_main_screen', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{

			$del  = '&nbsp;';
			$mod  = '&nbsp;';
			$adm  = '&nbsp;';

			if ($r['g_id'] > 4)
			{
				$del = "<center><a href='{$ibforums->base_url}&act=group&code=delete&id=".$r['g_id']."'>Delete</a></center>";
			}
			//-----------------------------------------
			if ($r['g_access_cp'] == 1)
			{
				$adm = '<center><span style="color:red">Yes</span></center>';
			}
			//-----------------------------------------
			if ($r['g_is_supmod'] == 1)
			{
				$mod = '<center><span style="color:red">Yes</span></center>';
			}

			if ($r['g_id'] != 1 and $r['g_id'] != 2)
			{
				$total_linkage = "<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?act=Members&max_results=30&filter={$r['g_id']}&sort_order=asc&sort_key=name&st=0' target='_blank' title='List Users'>".$r['prefix'].$r['g_title'].$r['suffix']."</a>";
			}
			else
			{
				$total_linkage = $r['prefix'].$r['g_title'].$r['suffix'];
			}

			if ( $ibforums->vars['admin_group'] == $r['g_id'] )
			{
				$is_root = " ( ROOT )";
			}
			else
			{
				$is_root = "";
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>$total_linkage</b> $is_root" ,
												      $adm,
												      $mod,
												      "<center>".$r['count']."</center>",
												      "<center><a href='{$ibforums->base_url}&act=group&code=edit&id=".$r['g_id']."'>Edit Group</a></center>",
												      $del

									     )      );

			$g_array[] = array( $r['g_id'], $r['g_title'] );
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic("&nbsp;", "center", "tdrow1");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'add' ),
												  2 => array( 'act'   , 'group'     ),
									     )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Add a new member group" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Base new group on...</b>" ,
												  $ibforums->adskin->form_dropdown("id", $g_array, 3 )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Set up New Group");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}


}


?>