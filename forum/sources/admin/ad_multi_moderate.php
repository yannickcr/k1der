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
|   > Topic Multi-Moderation
|   > Module written by Matt Mecham
|   > Date started: 14th May 2003
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

class ad_multi_moderate
{

	var $base_url;
	var $forumfunc = "";

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

		$ibforums->admin->nav[] = array( 'act=multimod', 'Topic multi-moderation home' );

		switch($ibforums->input['code'])
		{

			case 'list':
				$this->list_current();
				break;

			case 'new':
				$this->do_form('new');
				break;

			case 'edit':
				$this->do_form('edit');
				break;

			case 'donew':
				$this->do_save('new');
				break;

			case 'doedit':
				$this->do_save('edit');
				break;

			case 'delete':
				$this->do_delete();
				break;

			//-----------------------------------------

			default:
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// Rebuild Cache
	//-----------------------------------------

	function rebuild_cache()
	{
		global $ibforums, $DB, $std;

		$ibforums->cache['multimod'] = array();

		$DB->simple_construct( array(
								 'select' => '*',
								 'from'   => 'topic_mmod',
								 'order'  => 'mm_title'
						 )      );

		$DB->simple_exec();

		while ($i = $DB->fetch_row())
		{
			$ibforums->cache['multimod'][ $i['mm_id'] ] = $i;
		}

		$std->update_cache( array( 'name' => 'multimod', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	// DELETE!
	//-----------------------------------------

	function do_delete()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the MMOD ID, please try again");
		}

		$DB->simple_exec_query( array( 'delete' => 'topic_mmod', 'where' => "mm_id=".intval($ibforums->input['id']) ) );

		$this->rebuild_cache();

		$ibforums->admin->save_log("Topic Multi-Mod removed");

		$std->boink_it($ibforums->adskin->base_url."&act=multimod");

	}

	//-----------------------------------------
	// SAVE!
	//-----------------------------------------

	function do_save($type='new')
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		$forums = array();

		$ibforums->input['id'] = intval($ibforums->input['id']);

		if ( $type == 'edit' )
		{
			if ( $ibforums->input['id'] < 1 )
			{
				$ibforums->admin->error("You must use a valid id");
			}
		}

		if ( $ibforums->input['mm_title'] == "" )
		{
			$ibforums->admin->error("You must enter a valid title");
		}

		//-----------------------------------------
		// Check for forums...
		//-----------------------------------------

		$forums = $this->get_activein_forums();

		if ( ! $forums )
		{
			$ibforums->admin->error("You must select some forums to activate with this multi-moderation suite");
		}

		if ( $ibforums->input['topic_move'] == 'n' )
		{
			$ibforums->admin->error("Incorrect forum chosen in the 'move to' section of the topic multi-moderation. Please note that you cannot choose to move the topic to a category");
		}

		$save = array(
						'mm_title'              => $ibforums->input['mm_title'],
						'mm_enabled'            => 1,
						'topic_state'           => $ibforums->input['topic_state'],
						'topic_pin'	            => $ibforums->input['topic_pin'],
						'topic_move'            => $ibforums->input['topic_move'],
						'topic_move_link'       => $ibforums->input['topic_move_link'],
						'topic_title_st'        => $ibforums->admin->make_safe($_POST['topic_title_st']),
						'topic_title_end'       => $ibforums->admin->make_safe($_POST['topic_title_end']),
						'topic_reply'           => $ibforums->input['topic_reply'],
						'topic_reply_content'   => $ibforums->admin->make_safe($_POST['topic_reply_content']),
						'topic_reply_postcount' => $ibforums->input['topic_reply_postcount'],
						'mm_forums'             => $forums,
						'topic_approve'         => $ibforums->input['topic_approve'],
					 );

		if ( $type == 'edit' )
		{
			$mm_id = $ibforums->input['id'];

			$DB->do_update( 'topic_mmod', $save, 'mm_id='.$mm_id );
		}
		else
		{
			$DB->do_insert( 'topic_mmod', $save );

			$mm_id = $DB->get_insert_id();
		}

		$ibforums->admin->save_log("Update topic multi-moderation entries ($type)");

		$this->rebuild_cache();

		$std->boink_it($ibforums->base_url."&act=multimod");


	}

	//-----------------------------------------
	// SHOW MM FORM
	//-----------------------------------------

	function do_form($type='new')
	{
		global $ibforums, $DB,  $std, $forums;

		$ibforums->admin->page_detail = "Multi moderation allows you to combine moderation actions to create easy to use shortcuts to several moderation options.";
		$ibforums->admin->page_title  = "Topic Multi-Moderation";

		$form_code   = 'donew';
		$description = 'Add a new topic multi-moderation';
		$button      = "Add New Multi-Moderation";

		if ( $type == 'edit' )
		{
			$id = intval($ibforums->input['id']);

			$DB->simple_construct( array( 'select' => '*', 'from' => 'topic_mmod', 'where' => "mm_id=$id" ) );
			$DB->simple_exec();

			if ( ! $topic_mm = $DB->fetch_row() )
			{
				$ibforums->admin->error("Could not retrieve the information ($id)");
			}

			$form_code   = 'doedit';
			$description = 'Edit the topic multi-moderation';
			$button      = "Edit Multi-Moderation";
		}

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $form_code ),
																 2 => array( 'act'   , 'multimod' ),
																 3 => array( 'id'    , $id        ),
														)      );

		//-----------------------------------------

		$state_dd = array(
						  0 => array( 'leave', 'Leave' ),
						  1 => array( 'close', 'Close' ),
						  2 => array( 'open' , 'Open'  ),
					   );

		$pin_dd   = array(
						  0 => array( 'leave', 'Leave' ),
						  1 => array( 'pin'  , 'Pin'   ),
						  2 => array( 'unpin', 'Unpin' ),
					    );

		$app_dd   = array(
						  0 => array( '0', 'Leave' ),
						  1 => array( '1', 'Approve (Set Visible)'   ),
						  2 => array( '2', 'Unapprove (Set Invisibe)' ),
					    );

		//-----------------------------------------



		$forum_html = "<select name='forums[]' class='textinput' size='15' multiple='multiple'>\n";

		$forum_html .= $topic_mm['mm_forums'] == '*'
				     ? "<option value='all' selected='selected'>-- ALL FORUMS --</option>\n"
					 : "<option value='all'>-- ALL FORUMS --</option>\n";

		$forum_jump = $this->forumfunc->ad_forums_forum_data();

		foreach ( $forum_jump as $idx => $i )
		{
			if ( strstr( ",".$topic_mm['mm_forums'].",", ",".$i['id']."," ) and $topic_mm['mm_forums'] != '*' )
			{
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = "";
			}

			if ( $i['redirect_on'] == 1 )
			{
				continue;
			}

			$fporum_jump[] = array( $i['id'], $i['depthed_name'] );

			$forum_html  .= "<option value=\"{$i['id']}\" $selected>{$i['depthed_name']}</option>\n";

		}

		$forum_html  .= "</select>";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"   , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Topic Multi-Moderation", $description );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Title for this Multi-Moderation Suite?</b>" ,
												  $ibforums->adskin->form_input("mm_title", $topic_mm['mm_title'] )
									     )      );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Active in Forums...</b><br>You may choose more than one" ,
												  $forum_html
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_basic( 'Moderation Options', 'left', 'pformstrip' );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Add to <i>START</i> of topic title?</b>" ,
												  $ibforums->adskin->form_input("topic_title_st", $topic_mm['topic_title_st'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Add to <i>END</i> of topic title?</b>" ,
												  $ibforums->adskin->form_input("topic_title_end", $topic_mm['topic_title_end'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Alter topic state?</b>" ,
												  $ibforums->adskin->form_dropdown("topic_state", $state_dd, $topic_mm['topic_state'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Alter pinned state?</b>" ,
												  $ibforums->adskin->form_dropdown("topic_pin", $pin_dd, $topic_mm['topic_pin'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Alter approved state?</b>" ,
												  $ibforums->adskin->form_dropdown("topic_approve", $app_dd, $topic_mm['topic_approve'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Move topic?</b>" ,
					    						  $ibforums->adskin->form_dropdown("topic_move", array_merge( array( 0 => array('-1', 'Don\'t Move' ) ), $fporum_jump ), $topic_mm['topic_move'] )
					    						  ."<br />".$ibforums->adskin->form_checkbox('topic_move_link', $topic_mm['topic_move_link'] )."<strong>Leave a link to the source topic?</strong>"
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_basic( 'Post Options', 'left', 'pformstrip' );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Add a reply to the topic?</b><br>HTML enabled" ,
												  "Enable this reply? &nbsp;".$ibforums->adskin->form_yes_no('topic_reply', $topic_mm['topic_reply'] )
												  ."<br />"
												  . $ibforums->adskin->form_textarea("topic_reply_content", $topic_mm['topic_reply_content'] )
												  ."<br />".$ibforums->adskin->form_checkbox('topic_reply_postcount', $topic_mm['topic_reply_postcount'] )."<strong>Increment poster's post count?</strong>"
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form($button);

		$ibforums->html .= $ibforums->adskin->end_table();



		$ibforums->admin->output();

	}


	//-----------------------------------------
	// SHOW ALL AVAILABLE MM's
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_detail = "Multi moderation allows you to combine moderation actions to create easy to use shortcuts to several moderation options.";
		$ibforums->admin->page_title  = "Topic Multi-Moderation";


		$ibforums->adskin->td_header[] = array( "Title"  , "50%" );
		$ibforums->adskin->td_header[] = array( "Edit"   , "25%" );
		$ibforums->adskin->td_header[] = array( "Remove" , "25%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Current Topic Multi-Moderation" );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topic_mmod', 'order' => "mm_title" ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$ibforums->html .= $ibforums->adskin->add_td_row( array(
																		 "<strong>{$row['mm_title']}</strong>",
																		 "<center><a href='{$ibforums->base_url}&amp;act=multimod&amp;code=edit&amp;id={$row['mm_id']}'>Edit</a></center>",
																		 "<center><a href='{$ibforums->base_url}&amp;act=multimod&amp;code=delete&amp;id={$row['mm_id']}'>Remove</a></center>",
																)      );
			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("<center>None set up</center>");
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic("<a href='{$ibforums->base_url}&amp;act=multimod&amp;code=new' class='fauxbutton'>Add New</a>", 'center', 'pformstrip');


		$ibforums->html .= $ibforums->adskin->end_table();


		$ibforums->admin->output();

	}


	//-----------------------------------------
    // Get the active in forums
    //-----------------------------------------

    function get_activein_forums()
    {
		global $ibforums, $DB, $std, $forums, $HTTP_POST_VARS;

    	$forumids = array();

    	//-----------------------------------------
    	// Check for an array
    	//-----------------------------------------

    	if ( is_array( $_POST['forums'] )  )
    	{

    		if ( in_array( 'all', $_POST['forums'] ) )
    		{
    			//-----------------------------------------
    			// Searching all forums..
    			//-----------------------------------------

    			return '*';
    		}
    		else
    		{
				//-----------------------------------------
				// Go loopy loo
				//-----------------------------------------

				foreach( $_POST['forums'] as $l )
				{
					if ( $forums->forum_by_id[ $l ] )
					{
						$forumids[] = intval($l);
					}
				}

				//-----------------------------------------
				// Do we have cats? Give 'em to Charles!
				//-----------------------------------------

				if ( count( $forumids  ) )
				{
					foreach( $forumids  as $f )
					{
						$children = $forums->forums_get_children( $f );

						if ( is_array($children) and count($children) )
						{
							$forumids  = array_merge( $forumids , $children );
						}
					}
				}
				else
				{
					//-----------------------------------------
					// No forums selected / we have available
					//-----------------------------------------

					return;
				}
    		}
		}
		else
		{
			//-----------------------------------------
			// Not an array...
			//-----------------------------------------

			if ( $ibforums->input['forums'] == 'all' )
			{
				return '*';
			}
			else
			{
				if ( $ibforums->input['forums'] != "" )
				{
					$l = intval($ibforums->input['forums']);

					//-----------------------------------------
					// Single forum
					//-----------------------------------------

					if ( $forums->forum_by_id[ $l ] )
					{
						$forumids[] = intval($l);
					}

					if ( $ibforums->input['searchsubs'] == 1 )
					{
						$children = $forums->forums_get_children( $f );

						if ( is_array($children) and count($children) )
						{
							$forumids  = array_merge( $forumids , $children );
						}
					}
				}
			}
		}

		return implode( ",", $forumids );
    }
}


?>