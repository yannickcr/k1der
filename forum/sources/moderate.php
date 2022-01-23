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
|   > Moderation core module
|   > Module written by Matt Mecham
|   > Date started: 19th February 2002
|
|   > Module Version 1.0.0
|   > DBA Checked: Wed 19 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class moderate
{
    var $output    = "";
    var $base_url  = "";
    var $html      = "";

    var $moderator = "";
    var $modfunc   = "";
    var $forum     = array();
    var $topic     = array();

    var $upload_dir  = "";
	var $trash_forum = 0;
	var $trash_inuse = 0;

    /*-------------------------------------------------------------------------*/
	// Our constructor, load words, load skin, print the topic listing
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $forums, $DB, $std, $print;

        $post_array      = array( '04', '02', '20', '22', 'resync', 'prune_start', 'prune_finish', 'prune_move', 'editmember' );
        $not_forum_array = array( 'editmember' );

        //-----------------------------------------
        // Make sure this is a POST request
        // not a naughty IMG redirect
        //-----------------------------------------

        if ( ! in_array( $ibforums->input['CODE'], $post_array ) )
        {
			if ($_POST['act'] == '')
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'incorrect_use') );
			}
		}

		//-----------------------------------------
        // Nawty, Nawty!
        //-----------------------------------------

        if ($ibforums->input['CODE'] != '02' and $ibforums->input['CODE'] != '05')
        {
			if ($ibforums->input['auth_key'] != $std->return_md5_check() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'del_post') );
			}
		}

        //-----------------------------------------
		// Compile the language file
		//-----------------------------------------

        $ibforums->lang  = $std->load_words($ibforums->lang, 'lang_mod', $ibforums->lang_id);

        $this->html      = $std->load_template('skin_mod');

        //-----------------------------------------
        // Check the input
        //-----------------------------------------

        if ( ! in_array( $ibforums->input['CODE'], $not_forum_array ) )
        {
        	//-----------------------------------------
        	// t
        	//-----------------------------------------

			if ($ibforums->input['t'])
			{
				$ibforums->input['t'] = intval($ibforums->input['t']);

				if ( ! $ibforums->input['t'] )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
				}
				else
				{
					$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.intval($ibforums->input['t']) ) );
					$DB->simple_exec();

					$this->topic = $DB->fetch_row();

					if (empty($this->topic['tid']))
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
					}
				}
			}

			//-----------------------------------------
			// p
			//-----------------------------------------

			if ($ibforums->input['p'])
			{
				$ibforums->input['p'] = intval($ibforums->input['p']);

				if (! $ibforums->input['p'] )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
				}
			}

			//-----------------------------------------
			// F?
			//-----------------------------------------

			$ibforums->input['f'] = intval($ibforums->input['f']);

			if ( ! $ibforums->input['f'] )
			{
				$std->Error( array( 'LEVEL' => 1,'MSG' => 'missing_files') );
			}

			$ibforums->input['st'] = intval($ibforums->input['st']);

			//-----------------------------------------
			// Get the forum info based on the forum ID,
			//-----------------------------------------

			$this->forum = $forums->forum_by_id[ $ibforums->input['f'] ];

			$this->base_url = $ibforums->base_url;

			//-----------------------------------------
			// Are we a moderator?
			//-----------------------------------------

			if ( $ibforums->member['_moderator'][ $ibforums->input['f'] ] )
			{
				$this->moderator = $ibforums->member['_moderator'][ $ibforums->input['f'] ];
			}
        }

        //-----------------------------------------
        // Load mod module...
        //-----------------------------------------

        require( ROOT_PATH.'sources/lib/modfunctions.php');

        $this->modfunc = new modfunctions();

        $this->modfunc->init($this->forum);

        $this->upload_dir = $ibforums->vars['upload_dir'];

        //-----------------------------------------
        // Trash-can set up
        //-----------------------------------------

        if ( $ibforums->vars['forum_trash_can_enable'] and $ibforums->vars['forum_trash_can_id'] )
        {
        	if ( $ibforums->cache['forum_cache'][ $ibforums->vars['forum_trash_can_id'] ]['sub_can_post'] )
        	{
        		if ( $ibforums->member['mgroup'] == $ibforums->vars['admin_group'] )
        		{
        			$this->trash_forum = $ibforums->vars['forum_trash_can_use_radmin'] ? $ibforums->vars['forum_trash_can_id'] : 0;
        		}
        		else if ( $ibforums->member['g_access_cp'] )
        		{
        			$this->trash_forum = $ibforums->vars['forum_trash_can_use_admin'] ? $ibforums->vars['forum_trash_can_id'] : 0;
        		}
        		else if ( $ibforums->member['g_is_supmod'] )
        		{
        			$this->trash_forum = $ibforums->vars['forum_trash_can_use_smod'] ? $ibforums->vars['forum_trash_can_id'] : 0;
        		}
        		else if ( $ibforums->member['is_mod'] )
        		{
        			$this->trash_forum = $ibforums->vars['forum_trash_can_use_mod'] ? $ibforums->vars['forum_trash_can_id'] : 0;
        		}
        	}
        }

        //-----------------------------------------
        // Convert the code ID's into something
        // use mere mortals can understand....
        //-----------------------------------------

        switch ($ibforums->input['CODE'])
        {
        	case '02':
        		$this->move_form();
        		break;
        	case '03':
        		$this->delete_form();
        		break;
        	case '04':
        		$this->delete_post();
        		break;
        	case '05':
        		$this->edit_form();
        		break;
        	case '00':
        		$this->close_topic();
        		break;
        	case '01':
        		$this->open_topic();
        		break;
        	case '08':
        		$this->delete_topic();
        		break;
        	case '12':
        		$this->do_edit();
        		break;
        	case '14':
        		$this->do_move();
        		break;
        	case '15':
        		$this->pin_topic();
        		break;
        	case '16':
        		$this->unpin_topic();
        		break;
        	case '17':
        		$this->rebuild_topic();
        		break;
        	//-----------------------------------------
        	// Poll Edit
        	//-----------------------------------------
        	case '20':
        		$this->poll_edit_form();
        		break;
        	case '21':
        		$this->poll_edit_do();
        		break;
        	//-----------------------------------------
        	// Poll Delete
        	//-----------------------------------------
        	case '22':
        		$this->poll_delete_form();
        		break;
        	case '23':
        		$this->poll_delete_do();
        		break;
        	//-----------------------------------------
        	// Unsubscribe
        	//-----------------------------------------
        	case '30':
        		$this->unsubscribe_all_form();
        		break;
        	case '31':
        		$this->unsubscribe_all();
        		break;
        	//-----------------------------------------
        	// Merge Start
        	//-----------------------------------------
        	case '60':
        		$this->merge_start();
        		break;
        	case '61':
        		$this->merge_complete();
        		break;
        	//-----------------------------------------
        	// Topic History
        	//-----------------------------------------
        	case '90':
        		$this->topic_history();
        		break;
        	//-----------------------------------------
        	// Multi---
        	//-----------------------------------------
        	case 'topicchoice':
        		$this->multi_topic_modify();
        		break;
        	//-----------------------------------------
        	// Multi---
        	//-----------------------------------------
        	case 'postchoice':
        		$this->multi_post_modify();
        		break;
        	//-----------------------------------------
        	// Resynchronize Forum
        	//-----------------------------------------
        	case 'resync':
        		$this->resync_forum();
        		break;
        	//-----------------------------------------
        	// Prune / Move Topics
        	//-----------------------------------------
        	case 'prune_start':
        		$this->prune_start();
        		break;
        	case 'prune_finish':
        		$this->prune_finish();
        		break;
        	case 'prune_move':
        		$this->prune_move();
        		break;
        	//-----------------------------------------
        	// Add. topic view func.
        	//-----------------------------------------
        	case 'topic_approve':
        		$this->topic_approve_alter('approve');
        		break;
        	case 'topic_unapprove':
        		$this->topic_approve_alter('unapprove');
        		break;
        	//-----------------------------------------
        	// Edit member
        	//-----------------------------------------
        	case 'editmember':
        		$this->edit_member();
        		break;
        	default:
        		$this->moderate_error();
        		break;
        }

        // If we have any HTML to print, do so...

    	$print->add_output( $this->output );
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
	}

	/*-------------------------------------------------------------------------*/
	// Edit member
	/*-------------------------------------------------------------------------*/

	function edit_member()
	{
		global $DB, $std, $forums, $ibforums;

		$mid = intval($ibforums->input['mid']);

		//-----------------------------------------
		// Check Permissions
		//-----------------------------------------

		if ( ! $ibforums->member['g_is_supmod'] )
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Get post parser
		//-----------------------------------------

		require_once ( ROOT_PATH."sources/lib/post_parser.php");

        $parser = new post_parser();

		//-----------------------------------------
		// Got anyfink?
		//-----------------------------------------

		if ( ! $mid )
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Get member
		//-----------------------------------------

		$DB->cache_add_query( 'profile_get_all' , array( 'mid' => $mid ) );

		$DB->cache_exec_query();

		$member = $DB->fetch_row();

		//-----------------------------------------
		// Show Form?
		//-----------------------------------------

		if ( ! $ibforums->input['checked'] )
		{
			$this->output .= $this->html_start_form( array( 1 => array( 'CODE'   , 'editmember'),
															4 => array( 'mid'    , $mid        ),
															5 => array( 'checked', 1           ),
												   )      );

    		//-----------------------------------------
			// No editing of admins!
			//-----------------------------------------

			if ( ! $ibforums->member['g_access_cp'] )
			{
				if ( $ibforums->cache['group_cache'][ $member['mgroup'] ]['g_access_cp'] )
				{
					$this->moderate_error('cp_admin_user');
					return;
				}
			}

			$editable['signature']  = $parser->unconvert($member['signature']);
			$editable['location']   = $member['location'];
			$editable['interests']  = $std->my_br2nl($member['interests']);
			$editable['website']    = $member['website'];
			$editable['id']         = $member['id'];
			$editable['name']       = $member['name'];
			$editable['aim_name']   = $member['aim_name'];
			$editable['icq_number'] = $member['icq_number'];
			$editable['msnname']    = $member['msnname'];
			$editable['yahoo']      = $member['yahoo'];

			$optional_output = "";

			//-----------------------------------------
			// Profile fields
			//-----------------------------------------

			require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
			$fields = new custom_fields( $DB );

			$fields->member_id   = $ibforums->member['id'];
			$fields->mem_data_id = $member['id'];
			$fields->admin       = intval($ibforums->member['g_access_cp']);
			$fields->supmod      = intval($ibforums->member['g_is_supmod']);

			$fields->init_data();
			$fields->parse_to_edit();

			foreach( $fields->out_fields as $id => $data )
			{
				if ( $fields->cache_data[ $id ]['pf_type'] == 'drop' )
				{
					$form_element = $this->html->field_dropdown( 'field_'.$id, $data );
				}
				else if ( $fields->cache_data[ $id ]['pf_type'] == 'area' )
				{
					$form_element = $this->html->field_textarea( 'field_'.$id, $data );
				}
				else
				{
					$form_element = $this->html->field_textinput( 'field_'.$id, $data );
				}

				$optional_output .= $this->html->field_entry( $fields->field_names[ $id ], $fields->field_desc[ $id ], $form_element );
			}

			//-----------------------------------------
			// Show?
			//-----------------------------------------

			$this->output .= $this->html->edit_user_form($editable, $optional_output);

			$this->page_title = $ibforums->lang['cp_em_title'];
			$this->nav[]      = "<a href='{$ibforums->base_url}showuser={$mid}'>{$ibforums->lang['cp_vp_title']}</a>";
			$this->nav[]      = $ibforums->lang['cp_em_title'];

		}
		//-----------------------------------------
		// Do edit
		//-----------------------------------------
		else
		{
			$ibforums->input['signature'] = $parser->convert(  array( 'TEXT'      => $ibforums->input['signature'],
																	  'SMILIES'   => 0,
																	  'CODE'      => $ibforums->vars['sig_allow_ibc'],
																	  'HTML'      => $ibforums->vars['sig_allow_html'],
																	  'SIGNATURE' => 1
															)       );

			if ($parser->error != "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => $parser->error) );
			}

			$bet = array(  'website'     => $ibforums->input['website'],
						   'icq_number'  => $ibforums->input['icq_number'],
						   'aim_name'    => $ibforums->input['aim_name'],
						   'yahoo'       => $ibforums->input['yahoo'],
						   'msnname'     => $ibforums->input['msnname'],
						   'location'    => $ibforums->input['location'],
						   'interests'   => $ibforums->input['interests'],
						   'signature'   => $ibforums->input['signature'],
						);

			if ($ibforums->input['avatar'] == 1)
			{
				$bet['avatar_location'] = "";
				$bet['avatar_size']     = "";
				$this->bash_uploaded_avatars($mid);
			}

			if ($ibforums->input['photo'] == 1)
			{
				$this->bash_uploaded_photos($mid);
				$bet['photo_type']       = '';
				$bet['photo_location']   = '';
				$bet['photo_dimensions'] = '';
			}

			//-----------------------------------------
			// Write it to the DB.
			//-----------------------------------------

			if ( $mem = $DB->simple_exec_query( array( 'select' => 'id', 'from' => 'member_extra', 'where' => 'id='.$mid ) ) )
			{
				$DB->do_update( 'member_extra', $bet, 'id='.$mid );
			}
			else
			{
				$bet['id'] = $mid;
				$DB->do_insert( 'member_extra', $bet );
			}

			//-----------------------------------------
			// Custom profile field stuff
			//-----------------------------------------

			require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
			$fields = new custom_fields( $DB );

			$fields->member_id   = $ibforums->member['id'];

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

				$test = $DB->simple_exec_query( array( 'select' => 'member_id', 'from' => 'pfields_content', 'where' => 'member_id='.$mid ) );

				if ( $test['member_id'] )
				{
					//-----------------------------------------
					// We have it, so simply update
					//-----------------------------------------

					$DB->do_update( 'pfields_content', $fields->out_fields, 'member_id='.$mid );
				}
				else
				{
					$fields->out_fields['member_id'] = $mid;

					$DB->do_insert( 'pfields_content', $fields->out_fields );
				}
			}

			//-----------------------------------------
			// Member sync?
			//-----------------------------------------

			if ( USE_MODULES == 1 )
			{
				require ROOT_PATH."modules/ipb_member_sync.php";

				$this->modules = new ipb_member_sync();

				$bet['id'] = $mid;
				$this->modules->register_class(&$this);
				$this->modules->on_profile_update($bet, $custom_fields);
			}

			$this->moderate_log("Edited Profile for: {$member['name']}");

			$std->boink_it( $ibforums->base_url.'act=mod&CODE=editmember&auth_key='.$std->return_md5_check().'&mid='.$mid.'&tid='.time() );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Post act
	/*-------------------------------------------------------------------------*/

	function multi_post_modify()
	{
		global $DB, $std, $forums, $ibforums, $print;

		$this->pids  = $this->get_pids();

		if ( count( $this->pids ) )
		{
			switch ( $ibforums->input['tact'] )
			{
				case 'approve':
					$this->multi_approve_post(1);
					break;
				case 'unapprove':
					$this->multi_approve_post(0);
					break;
				case 'delete':
					$this->multi_delete_post();
					break;
				case 'merge':
					$this->multi_merge_post();
					break;
				case 'split':
					$this->multi_split_topic();
					break;
				case 'move':
					$this->multi_move_post();
					break;
				default:

					break;
			}
		}

		$std->my_setcookie('modpids', '', 0);

		if ( $this->topic['tid'] )
		{
			$print->redirect_screen( $ibforums->lang['cp_redirect_posts'], "showtopic=".$this->topic['tid'].'&st='.intval($ibforums->input['st']) );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Move Posts
	/*-------------------------------------------------------------------------*/

	function multi_move_post()
	{
		global $std, $ibforums, $DB, $print, $forums;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['split_merge'] == 1)
		{
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if ( ! $this->topic['tid'] )
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Get post parser
		//-----------------------------------------

		require_once ( ROOT_PATH."sources/lib/post_parser.php");

        $this->parser = new post_parser();

		if ( $ibforums->input['checked'] != 1 )
		{
			$jump_html = $std->build_forum_jump(0,1);

			$this->output .= $this->html_start_form( array( 1 => array( 'CODE'   , 'postchoice'        ),
															2 => array( 't'      , $this->topic['tid'] ),
															3 => array( 'f'      , $this->forum['id']  ),
															4 => array( 'tact'   , 'move' ),
															5 => array( 'checked', 1      ),
												   )      );

			$this->output .= $this->html->table_top( $ibforums->lang['cmp_title'].": ".$this->forum['name']." -&gt; ".$this->topic['title'] );

			$this->output .= $this->html->move_post_body();

			//-----------------------------------------
			// Display the posty wosty's
			//-----------------------------------------

			$DB->simple_construct( array(
										  'select' => 'post, pid, post_date, author_id, author_name',
										  'from'   => 'posts',
										  'where'  => "pid IN (".implode(",", $this->pids).")",
										  'order'  => 'post_date'
								 )      );

			$post_query = $DB->simple_exec();

			$post_count = 0;

			while ( $row = $DB->fetch_row($post_query) )
			{
				//-----------------------------------------
				// Limit posts to 200 chars to stop shite
				// loads of pages
				//-----------------------------------------

				if ( strlen($row['post']) > 800 )
				{
					$row['post']   = $this->parser->unconvert($row['post']);
					$row['post']   = substr($row['post'], 0, 800) . '...';
				}

				$row['date']   = $std->get_date( $row['post_date'], 'LONG' );

				$row['st_top_bit'] = sprintf( $ibforums->lang['st_top_bit'], $row['author_name'], $row['date'] );

				$row['post_css'] = $post_count % 2 ? 'row1' : 'row2';

				$this->output .= $this->html->split_row( $row );

				$post_count++;
			}

			//-----------------------------------------
			// print my bottom, er, the bottom
			//-----------------------------------------

			$this->output .= $this->html->split_end_form( $ibforums->lang['cmp_submit'] );

			$this->page_title = $ibforums->lang['cmp_title'].": ".$this->topic['title'];

			$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
								 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
							   );

			$print->add_output( $this->output );
        	$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
		}
		else
		{
			//-----------------------------------------
			// PROCESS Check the input
			//-----------------------------------------

			if ( ! intval($ibforums->input['topic_url']) )
			{
				preg_match( "/(\?|&amp;)(t|showtopic)=(\d+)($|&amp;)/", $ibforums->input['topic_url'], $match );

				$old_id = intval(trim($match[3]));
			}
			else
			{
				$old_id = intval($ibforums->input['topic_url']);
			}

			if ($old_id == "")
			{
				$ibforums->input['checked'] = 0;
				$this->output = $this->html->warn_errors( $ibforums->lang['cmp_notopic'] );
				$this->multi_move_post();
			}

			//-----------------------------------------
			// Grab topic
			//-----------------------------------------

			$move_to_topic = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.$old_id ) );

			if ( ! $move_to_topic['tid'] or ! $forums->forum_by_id[ $move_to_topic['forum_id'] ]['id'] )
			{
				$ibforums->input['checked'] = 0;
				$this->output = $this->html->warn_errors( $ibforums->lang['cmp_notopic'] );
				$this->multi_move_post();
			}

			//-----------------------------------------
			// Get the post ID's to split
			//-----------------------------------------

			$ids = array();

			foreach ($ibforums->input as $key => $value)
			{
				if ( preg_match( "/^post_(\d+)$/", $key, $match ) )
				{
					if ($ibforums->input[$match[0]])
					{
						$ids[] = $match[1];
					}
				}
			}

			$affected_ids = count($ids);

			//-----------------------------------------
			// Do we have enough?
			//-----------------------------------------

			if ($affected_ids < 1)
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'split_not_enough' ) );
			}

			//-----------------------------------------
			// Do we choose too many?
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => 'count(pid) as cnt', 'from' => 'posts', 'where' => "topic_id={$this->topic['tid']}" ) );
			$DB->simple_exec();

			$count = $DB->fetch_row();

			if ( $affected_ids >= $count['cnt'] )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'split_too_much' ) );
			}

			//-----------------------------------------
			// Complete the PID string
			//-----------------------------------------

			$pid_string = implode( ",", $ids );

			//-----------------------------------------
			// Move the posts
			//-----------------------------------------

			$DB->do_update( 'posts', array( 'topic_id' => $move_to_topic['tid'], 'new_topic' => 0 ), "pid IN($pid_string)" );

			//-----------------------------------------
			// Move the posts
			//-----------------------------------------

			$DB->do_update( 'posts', array( 'new_topic' => 0 ), "topic_id={$this->topic['tid']}" );

			//-----------------------------------------
			// Rebuild the topics
			//-----------------------------------------

			$this->modfunc->rebuild_topic($move_to_topic['tid']);
			$this->modfunc->rebuild_topic($this->topic['tid']);

			//-----------------------------------------
			// Update the forum(s)
			//-----------------------------------------

			$this->modfunc->forum_recount($this->topic['forum_id']);

			if ($this->topic['forum_id'] != $move_to_topic['forum_id'])
			{
				$this->modfunc->forum_recount($move_to_topic['forum_id']);
			}

			$this->moderate_log("Moved posts from '{$this->topic['title']}' to '{$move_to_topic['title']}'");
		}
	}

	/*-------------------------------------------------------------------------*/
	// Split topic
	/*-------------------------------------------------------------------------*/

	function multi_approve_post($approve=1)
	{
		global $std, $ibforums, $DB, $print;

		$approve_topic = 1;
		$queued_post   = 0;

		if ( $approve != 1 )
		{
			$approve_topic = 0;
			$queued_post   = 1;
		}

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['post_q'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1)
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Did we get the first post too?
		//-----------------------------------------

		if ( strstr( ",".implode(",",$this->pids).",", ",".$this->topic['topic_firstpost']."," ) )
		{
			$DB->do_update( 'topics', array( 'approved' => $approve_topic ), 'tid='.$this->topic['tid'] );

			//-----------------------------------------
			// Don't actually un-approve first post
			// But allow approve
			//-----------------------------------------

			if ( $queued_post )
			{
				$tmp = $this->pids;

				$this->pids = array();

				foreach( $tmp as $t )
				{
					if ( $t != $this->topic['topic_firstpost'] )
					{
						$this->pids[] = $t;
					}
				}
			}
		}

		if ( count($this->pids) )
		{
			$DB->do_update( 'posts', array( 'queued' => $queued_post ), 'pid IN ('. implode(",", $this->pids) .')' );
		}

		$this->modfunc->rebuild_topic( $this->topic['tid'] );
		$this->modfunc->forum_recount( $this->topic['forum_id'] );
		$this->modfunc->stats_recount();
	}

	/*-------------------------------------------------------------------------*/
	// Split topic
	/*-------------------------------------------------------------------------*/

	function multi_split_topic()
	{
		global $std, $ibforums, $DB, $print, $forums;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}
		else if ( $this->moderator['split_merge'] == 1 or $this->trash_inuse == 1 )
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1)
		{
			$this->moderate_error();
		}

		if ( ! $this->topic['tid'] )
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Get post parser
		//-----------------------------------------

		require "./sources/lib/post_parser.php";

        $this->parser = new post_parser();

		if ( $ibforums->input['checked'] != 1 )
		{
			$jump_html = $std->build_forum_jump(0,1);

			$this->output = $this->html_start_form( array( 1 => array( 'CODE', 'postchoice' ),
														   2 => array( 't' , $this->topic['tid'] ),
														   3 => array( 'f' , $this->forum['id']  ),
														   4 => array( 'tact', 'split' ),
														   5 => array( 'checked', 1    ),

												  )      );

			$this->output .= $this->html->table_top( $ibforums->lang['st_top'].": ".$this->forum['name']." -&gt; ".$this->topic['title'] );

			$this->output .= $this->html->split_body( $jump_html );

			//-----------------------------------------
			// Display the posty wosty's
			//-----------------------------------------

			$DB->simple_construct( array(
										  'select' => 'post, pid, post_date, author_id, author_name',
										  'from'   => 'posts',
										  'where'  => "pid IN (".implode(",", $this->pids).")",
										  'order'  => 'post_date'
								 )      );

			$post_query = $DB->simple_exec();

			$post_count = 0;

			while ( $row = $DB->fetch_row($post_query) )
			{
				// Limit posts to 800 chars to stop shite loads of pages

				if ( strlen($row['post']) > 800 )
				{
					$row['post']   = $this->parser->unconvert($row['post']);
					$row['post']   = substr($row['post'], 0, 800) . '...';
				}

				$row['date']   = $std->get_date( $row['post_date'], 'LONG' );

				$row['st_top_bit'] = sprintf( $ibforums->lang['st_top_bit'], $row['author_name'], $row['date'] );

				$row['post_css'] = $post_count % 2 ? 'row1' : 'row2';

				$this->output .= $this->html->split_row( $row );

				$post_count++;
			}

			//-----------------------------------------
			// print my bottom, er, the bottom
			//-----------------------------------------

			$this->output .= $this->html->split_end_form( $ibforums->lang['st_submit'] );

			$this->page_title = $ibforums->lang['st_top']." ".$this->topic['title'];

			$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
								 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
							   );

			$print->add_output( $this->output );
        	$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
		}
		else
		{
			//-----------------------------------------
			// PROCESS Check the input
			//-----------------------------------------

			if ($ibforums->input['title'] == "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
			}

			//-----------------------------------------
			// Get the post ID's to split
			//-----------------------------------------

			$ids = array();

			foreach ($ibforums->input as $key => $value)
			{
				if ( preg_match( "/^post_(\d+)$/", $key, $match ) )
				{
					if ($ibforums->input[$match[0]])
					{
						$ids[] = $match[1];
					}
				}
			}

			$affected_ids = count($ids);

			//-----------------------------------------
			// Do we have enough?
			//-----------------------------------------

			if ($affected_ids < 1)
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'split_not_enough' ) );
			}

			//-----------------------------------------
			// Do we choose too many?
			//-----------------------------------------

			$DB->simple_construct( array( 'select' => 'count(pid) as cnt', 'from' => 'posts', 'where' => "topic_id={$this->topic['tid']}" ) );
			$DB->simple_exec();

			$count = $DB->fetch_row();

			if ( $affected_ids >= $count['cnt'] )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'split_too_much' ) );
			}

			//-----------------------------------------
			// Complete the PID string
			//-----------------------------------------

			$pid_string = implode( ",", $ids );

			//-----------------------------------------
			// Check the forum we're moving this too
			//-----------------------------------------

			$ibforums->input['fid'] = intval($ibforums->input['fid']);

			if ($ibforums->input['fid'] != $this->forum['id'])
			{
				if ( $this->trash_inuse )
				{
					$f = $ibforums->cache['forum_cache'][ $ibforums->input['fid'] ];
				}
				else
				{
					$f = $forums->forum_by_id[ $ibforums->input['fid'] ];
				}

				if ( ! $f['id'] )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_no_forum' ) );
				}

				if ($f['sub_can_post'] != 1)
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'forum_no_post_allowed' ) );
				}
			}

			//-----------------------------------------
			// Complete a new dummy topic
			//-----------------------------------------

			$DB->do_insert( 'topics',  array(
											 'title'            => $ibforums->input['title'],
											 'description'      => $ibforums->input['desc'] ,
											 'state'            => 'open',
											 'posts'            => 0,
											 'starter_id'       => 0,
											 'starter_name'     => 0,
											 'start_date'       => time(),
											 'last_poster_id'   => 0,
											 'last_poster_name' => 0,
											 'last_post'        => time(),
											 'icon_id'          => 0,
											 'author_mode'      => 1,
											 'poll_state'       => 0,
											 'last_vote'        => 0,
											 'views'            => 0,
											 'forum_id'         => $ibforums->input['fid'],
											 'approved'         => 1,
											 'pinned'           => 0,
							)               );

			$new_topic_id = $DB->get_insert_id();

			//-----------------------------------------
			// Move the posts
			//-----------------------------------------

			$DB->do_update( 'posts', array( 'topic_id' => $new_topic_id, 'new_topic' => 0 ), "pid IN($pid_string)" );

			//-----------------------------------------
			// Move the posts
			//-----------------------------------------

			if ( $this->trash_inuse )
			{
				$DB->do_update( 'posts', array( 'queued' => 0 ), "topic_id=$new_topic_id" );
			}

			$DB->do_update( 'posts', array( 'new_topic' => 0 ), "topic_id={$this->topic['tid']}" );

			//-----------------------------------------
			// Rebuild the topics
			//-----------------------------------------

			$this->modfunc->rebuild_topic($new_topic_id);
			$this->modfunc->rebuild_topic($this->topic['tid']);

			//-----------------------------------------
			// Update the forum(s)
			//-----------------------------------------

			$this->modfunc->forum_recount($this->topic['forum_id']);

			if ($this->topic['forum_id'] != $ibforums->input['fid'])
			{
				$this->modfunc->forum_recount($ibforums->input['fid']);
			}

			if ( $this->trash_inuse )
			{
				$this->moderate_log("Applied trash can for deleted post '{$this->topic['title']}'");
			}
			else
			{
				$this->moderate_log("Split topic '{$this->topic['title']}'");
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// Multi merge post
	/*-------------------------------------------------------------------------*/

	function multi_merge_post()
	{
		global $std, $ibforums, $DB, $print, $forums;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}
		else if ($this->moderator['delete_post'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if ( ! count( $this->pids ) )
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Load LIB
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/lib/post_parser.php' );
		$parser = new post_parser();

		//-----------------------------------------
		// Form or print?
		//-----------------------------------------

		if ( ! $ibforums->input['checked'] )
		{
			//-----------------------------------------
			// Get post data
			//-----------------------------------------

			$master_post = "";
			$dropdown    = "";
			$author      = "";
			$seen_author = array();
			$upload_html = "";

			//-----------------------------------------
			// MOVE INTO DB CLASS
			//-----------------------------------------

			$DB->cache_add_query( 'moderate_get_topics', array( 'pids' => $this->pids ) );
			$DB->cache_exec_query();

			while ( $p = $DB->fetch_row() )
			{
				if ( $std->check_perms( $forums->forum_by_id[ $p['forum_id'] ]['read_perms']) == TRUE )
				{
					$master_post  .= "\n\n".$parser->unconvert( trim($p['post']) );

					$dropdown     .= "\n<option value='{$p['pid']}'>".$std->get_date( $p['post_date'], 'LONG') ." (#{$p['pid']})</option>";

					if ( ! $seen_author[ $p['author_id'] ] )
					{
						$author .= "\n<option value='{$p['author_id']}'>{$p['author_name']} (#{$p['pid']})</option>";
						$seen_author[ $p['author_id'] ] = 1;
					}
				}
			}

			//-----------------------------------------
			// Get Attachment Data
			//-----------------------------------------

			$DB->simple_construct( array( "select" => '*', 'from' => 'attachments', 'where' => "attach_pid IN (".implode(",", $this->pids).")" ) );
			$DB->simple_exec();

			while( $row = $DB->fetch_row() )
			{
				$row['image'] = $ibforums->cache['attachtypes'][ $row['attach_ext'] ]['atype_img'];
				$row['size']  = $std->size_format( $row['attach_filesize'] );

				if ( strlen( $row['attach_file'] ) > 40 )
				{
					$row['attach_file'] = substr( $row['attach_file'], 0, 35 ) .'...';
				}

				$upload_html .= $this->html->uploadbox_entry($row);
			}

			//-----------------------------------------
			// Print form
			//-----------------------------------------

			$this->output .= $this->html->merge_post_form( trim($master_post), $dropdown, $author, $std->return_md5_check(), $upload_html );

			if ( $this->topic['tid'] )
			{
				$this->nav[] = "<a href='{$ibforums->base_url}showtopic={$this->topic['tid']}'>{$this->topic['title']}</a>";
			}

			$this->nav[]      = $ibforums->lang['cm_title'];

			$this->page_title = $ibforums->lang['cm_title'];

			$print->add_output( $this->output );
        	$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );
		}
		else
		{
			//-----------------------------------------
			// DO THE THING, WITH THE THING!!
			//-----------------------------------------

			$ibforums->input['postdate'] = intval($ibforums->input['postdate']);

			if ( ! $ibforums->input['selectedpids'] and ! $ibforums->input['postdate'] and ! $ibforums->input['postauthor'] and ! $ibforums->input['Post'] )
			{
				$this->moderate_error();
			}

			$post = $parser->convert( array( 'TEXT'    => $ibforums->input['Post'],
											 'SMILIES' => 1,
											 'CODE'    => 1,
											 'HTML'    => 0
								    )      );

			//-----------------------------------------
			// Post to keep...
			//-----------------------------------------

			$posts          = array();
			$author         = array();
			$post_to_delete = array();
			$new_post_key   = md5(time());
			$topics         = array();
			$forums         = array();

			$DB->cache_add_query( 'moderate_get_topics', array( 'pids' => $this->pids ) );
			$DB->cache_exec_query();

			while ( $p = $DB->fetch_row() )
			{
				$posts[ $p['pid'] ] = $p;

				$topics[ $p['topic_id'] ] = $p['topic_id'];
				$forums[ $p['forum_id'] ] = $p['forum_id'];

				if ( $p['author_id'] == $ibforums->input['postauthor'] )
				{
					$author = array( 'id' => $p['author_id'], 'name' => $p['author_name'] );
				}

				if ( $p['pid'] != $ibforums->input['postdate'] )
				{
					$post_to_delete[] = $p['pid'];
				}
			}

			//-----------------------------------------
			// Update main post...
			//-----------------------------------------

			$DB->do_update( 'posts', array( 'author_id'   => $author['id'],
											'author_name' => $author['name'],
											'post'        => $post,
											'post_key'    => $new_post_key,
											'post_parent' => 0
										  ), 'pid='.$ibforums->input['postdate']
						 );

			//-----------------------------------------
			// Fix attachments
			//-----------------------------------------

			$attach_keep = array();
			$attach_kill = array();

			foreach ($ibforums->input as $key => $value)
			{
				if ( preg_match( "/^attach_(\d+)$/", $key, $match ) )
				{
					if ( $ibforums->input[$match[0]] == 'keep' )
					{
						$attach_keep[] = $match[1];
					}
					else
					{
						$attach_kill[] = $match[1];
					}
				}
			}

			//-----------------------------------------
			// Keep
			//-----------------------------------------

			if ( count( $attach_keep ) )
			{
				$DB->do_update( 'attachments',
								array( 'attach_pid' => $ibforums->input['postdate'], 'attach_post_key' => $new_post_key, 'attach_member_id' => $author['id'] ),
								'attach_id IN('.implode(",",$attach_keep).')' );
			}

			//-----------------------------------------
			// Kill
			//-----------------------------------------

			if ( count( $attach_kill ) )
			{
				$DB->simple_construct( array( "select" => '*', 'from' => 'attachments',  'where' => 'attach_id IN('.implode(",",$attach_kill).')') );
				$DB->simple_exec();

				while ( $killmeh = $DB->fetch_row() )
				{
					if ( $killmeh['attach_location'] )
					{
						@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_location'] );
					}
					if ( $killmeh['attach_thumb_location'] )
					{
						@unlink( $ibforums->vars['upload_dir']."/".$killmeh['attach_thumb_location'] );
					}
				}

				$DB->simple_construct( array( 'delete' => 'attachments', 'where' => 'attach_id IN('.implode(",",$attach_kill).')' ) );
				$DB->simple_exec();
			}

			//-----------------------------------------
			// Kill old posts
			//-----------------------------------------

			if ( count($post_to_delete) )
			{
				$DB->simple_construct( array( 'delete' => 'posts', 'where' => 'pid IN('.implode(",",$post_to_delete).')' ) );
				$DB->simple_exec();
			}

			foreach( $topics as $t )
			{
				$this->modfunc->rebuild_topic($t, 0);
			}

			foreach( $forums as $f )
			{
				$this->modfunc->forum_recount($f);
			}

			$this->modfunc->stats_recount();
		}
	}

	/*-------------------------------------------------------------------------*/
	// Multi delete post
	/*-------------------------------------------------------------------------*/

	function multi_delete_post()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}
		else if ($this->moderator['delete_post'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		//-----------------------------------------
		// Check to make sure that this isn't the first post in the topic..
		//-----------------------------------------

		foreach( $this->pids as $p )
		{
			if ( $this->topic['topic_firstpost'] == $p )
			{
				$this->moderate_error('no_delete_post');
			}
		}

		if ( $this->trash_forum and $this->trash_forum != $this->forum['id'] )
		{
			//-----------------------------------------
			// Set up and pass to split topic handler
			//-----------------------------------------

			$ibforums->input['checked'] = 1;
			$ibforums->input['fid']     = $this->trash_forum;
			$ibforums->input['title']   = "From: ".$this->topic['title'];
			$ibforums->input['desc']    = "From Topic ID: ".$this->topic['tid'];

			foreach( $this->pids as $p )
			{
				$ibforums->input[ 'post_'.$p ] = 1;
			}

			$this->trash_inuse = 1;

			$this->multi_split_topic();

			$this->trash_inuse = 0;
		}
		else
		{
			$this->modfunc->post_delete( $this->pids );
			$this->modfunc->forum_recount( $this->topic['forum_id'] );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Alter approved thingy
	/*-------------------------------------------------------------------------*/

	function topic_approve_alter($type='approve')
	{
		global $std, $ibforums, $DB, $print;

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['post_q'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		$approve_int = $type == 'approve' ? 1 : 0;

		$DB->do_update( 'topics', array( 'approved' => $approve_int ), 'tid='.$this->topic['tid'] );

		$this->modfunc->forum_recount( $this->forum['id'] );
		$this->modfunc->stats_recount();

		$this->moderate_log("Approved a topic (tid: ".$this->topic['tid'].")");

		$print->redirect_screen( $ibforums->lang['redirect_modified'], "showtopic=".$this->topic['tid']."&st=".$ibforums->input['st'] );
	}

	/*-------------------------------------------------------------------------*/
	// Prune move
	/*-------------------------------------------------------------------------*/

	function prune_move()
	{
		global $std, $ibforums, $DB, $print, $forums;

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['mass_move'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		//-----------------------------------------
		// Carry on...
		//-----------------------------------------

		$db_query = $this->modfunc->sql_prune_create( $this->forum['id'], $ibforums->input['starter'], $ibforums->input['state'], $ibforums->input['posts'], $ibforums->input['dateline'], $ibforums->input['ignore_pin'] );

		$DB->query($db_query);

		if ( ! $num_rows = $DB->get_num_rows() )
		{
			$this->moderate_error('cp_error_no_topics');
			return;
		}

		$tid_array = array();

		while ($row = $DB->fetch_row())
		{
			$tid_array[] = $row['tid'];
		}

		//-----------------------------------------

		$source = $this->forum['id'];
		$moveto = $ibforums->input['df'];

		//-----------------------------------------
		// Check for an attempt to move into a subwrap forum
		//-----------------------------------------

		$f = $forums->forum_by_id[ $moveto ];

		if ( $f['sub_can_post'] != 1 )
		{
			$this->moderate_error('cp_error_no_subforum');
			return;
		}

		$this->modfunc->topic_move( $tid_array, $source, $moveto );

		$this->moderate_log("Mass moved topics");

		//-----------------------------------------
		// Resync the forums..
		//-----------------------------------------

		$this->modfunc->forum_recount($source);

		$this->modfunc->forum_recount($moveto);

		//-----------------------------------------
		// Show results..
		//-----------------------------------------

		$this->output .= $this->html->mod_simple_page( $ibforums->lang['cp_results'], $ibforums->lang['cp_result_move'].$num_rows );

		$print->pop_up_window( "", $this->output );
	}

	/*-------------------------------------------------------------------------*/
	// Do prune
	/*-------------------------------------------------------------------------*/

	function prune_finish()
	{
		global $std, $ibforums, $DB, $print;

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['mass_prune'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		//-----------------------------------------
		// Carry on...
		//-----------------------------------------

		$db_query = $this->modfunc->sql_prune_create( $this->forum['id'], $ibforums->input['starter'], $ibforums->input['state'], $ibforums->input['posts'], $ibforums->input['dateline'], $ibforums->input['ignore_pin'] );

		$batch = $DB->query($db_query);

		if ( ! $num_rows = $DB->get_num_rows() )
		{
			$this->moderate_error('cp_error_no_topics');
			return;
		}

		$tid_array = array();

		while ( $tid = $DB->fetch_row() )
		{
			$tid_array[] = $tid['tid'];
		}

		$this->modfunc->topic_delete($tid_array);

		$this->moderate_log("Pruned Forum");

		// Show results..

		$this->output .= $this->html->mod_simple_page( $ibforums->lang['cp_results'], $ibforums->lang['cp_result_del'].$num_rows );

		$print->pop_up_window( "", $this->output );
	}

	/*-------------------------------------------------------------------------*/
	// Prune / Move Start
	/*-------------------------------------------------------------------------*/

	function prune_start()
	{
		global $DB, $std, $forums, $ibforums, $print;

		//-----------------------------------------
		// Check permissions
		//-----------------------------------------

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['mass_prune'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		//-----------------------------------------
		// Are we checking first?
		//-----------------------------------------

		if ($ibforums->input['check'] == 1)
		{
			$link      = "";
			$link_text = $ibforums->lang['cp_prune_dorem'];

			$tcount = $DB->simple_exec_query( array( 'select' => 'COUNT(*) as tcount', 'from' => 'topics', 'where' => "approved=1 and forum_id={$this->forum['id']}" ) );

			//-----------------------------------------
			// date...
			//-----------------------------------------

			if ($ibforums->input['dateline'])
			{
				$date      = time() - $ibforums->input['dateline']*60*60*24;
				$db_query .= " AND last_post < $date";

				$link .= "&dateline=$date";
			}

			//-----------------------------------------
			// Member...
			//-----------------------------------------

			if ( $ibforums->input['member'] )
			{
				$DB->query("SELECT id FROM ibf_members WHERE name='".$ibforums->input['member']."'");

				if (! $mem = $DB->fetch_row() )
				{
					$this->moderate_error('cp_error_no_mem');
					return;
				}
				else
				{
					$db_query .= " AND starter_id='".$mem['id']."'";
					$link     .= "&starter={$mem['id']}";
				}
			}

			//-----------------------------------------
			// Posts / Topic type
			//-----------------------------------------

			if ($ibforums->input['posts'])
			{
				$db_query .= " AND posts < '".$ibforums->input['posts']."'";
				$link     .= "&posts={$ibforums->input['posts']}";
			}

			if ($ibforums->input['topic_type'] != 'all')
			{
				$db_query .= " AND state='".$ibforums->input['topic_type']."'";
				$link     .= "&state={$ibforums->input['topic_type']}";
			}

			if ($ibforums->input['ignore_pin'] == 1)
			{
				$db_query .= " AND pinned <> 1";
				$link     .= "&ignore_pin=1";
			}

			$DB->simple_construct( array( 'select' => 'COUNT(*) as count',
										  'from'   => 'topics',
										  'where'  => "approved=1 and forum_id='".$this->forum['id']."'" . $db_query ) );

			$DB->simple_exec();

			$count = $DB->fetch_row();

			//-----------------------------------------
			// Prune?
			//-----------------------------------------

			if ($ibforums->input['df'] == 'prune')
			{
				$link = "act=mod&f={$this->forum['id']}&CODE=prune_finish&".$link;
			}
			else
			{
				if ( $ibforums->input['df'] == $this->forum['id'] )
				{
					$this->moderate_error('cp_same_forum');
					return;
				}
				else if ($ibforums->input['df'] == -1)
				{
					$this->moderate_error('cp_no_forum');
					return;
				}

				$link      = "act=mod&f={$this->forum['id']}&CODE=prune_move&df=".$ibforums->input['df'].$link;
				$link_text = $ibforums->lang['cp_prune_domove'];
			}

			//-----------------------------------------
			// Build data
			//-----------------------------------------

			$confirm_data = array( 'tcount'    => $tcount['tcount'],
								   'count'     => $count['count'],
								   'link'      => $link,
								   'link_text' => $link_text,
								   'show'      => 1 );
		}

		$select = "<select name='topic_type' class='forminput'>";

		foreach( array( 'open', 'closed', 'link', 'all' ) as $type )
		{
			if ($ibforums->input['topic_type'] == $type)
			{
				$selected = ' selected';
			}
			else
			{
				$selected = '';
			}

			$select .= "<option value='$type'".$selected.">".$ibforums->lang['cp_pday_'.$type]."</option>";
		}

		$select .= "</select>\n";

		$html_forums  = "<option value='prune'>{$ibforums->lang['cp_ac_prune']}</option>";

		$html_forums .= $std->build_forum_jump(0,0,1);

		//-----------------------------------------
		// Make current destination forum this one if selected
		// before
		//-----------------------------------------

		if ($ibforums->input['df'])
		{
			$html_forums = preg_replace( "/<option value=\"".$ibforums->input['df']."\"/", "<option value=\"".$ibforums->input['df']."\" selected", $html_forums );
		}

		$this->output .= $this->html->prune_splash($this->forum, $html_forums, $select, $std->return_md5_check(), $confirm_data );

		$print->pop_up_window( "", $this->output );
	}

	/*-------------------------------------------------------------------------*/
	// Resynchronise Forum
	/*-------------------------------------------------------------------------*/

	function resync_forum()
	{
		global $DB, $std, $forums, $ibforums, $print;

		$this->modfunc->forum_recount( $this->forum['id'] );
		$this->modfunc->stats_recount();

		$print->redirect_screen( $ibforums->lang['cp_resync'], "showforum=".$this->forum['id'] );
	}

	/*-------------------------------------------------------------------------*/
	// Topic act
	/*-------------------------------------------------------------------------*/

	function multi_topic_modify()
	{
		global $DB, $std, $forums, $ibforums, $print;

		$this->tids  = $this->get_tids();

		if ( count( $this->tids ) )
		{
			switch ( $ibforums->input['tact'] )
			{
				case 'close':
					$this->multi_alter_topics('close_topic', "state='closed'");
					break;
				case 'open':
					$this->multi_alter_topics('open_topic', "state='open'");
					break;
				case 'pin':
					$this->multi_alter_topics('pin_topic', "pinned=1");
					break;
				case 'unpin':
					$this->multi_alter_topics('unpin_topic', "pinned=0");
					break;
				case 'approve':
					$this->multi_alter_topics('topic_q', "approved=1");
					break;
				case 'unapprove':
					$this->multi_alter_topics('topic_q', "approved=0");
					break;
				case 'delete':
					$this->multi_alter_topics('delete_topic');
					break;
				case 'move':
					$this->multi_start_checked_move();
					return;
					break;
				case 'domove':
					$this->multi_complete_checked_move();
					break;
				case 'merge':
					$this->multi_topic_merge();
					break;
				default:
					$this->multi_topic_mmod();
					break;
			}
		}

		$std->my_setcookie('modtids', '', 0);

		if ( $this->forum['id'] )
		{
			$print->redirect_screen( $ibforums->lang['cp_redirect_topics'], "showforum=".$this->forum['id'] );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Multi Merge Topics
	/*-------------------------------------------------------------------------*/

	function multi_topic_merge()
	{
		global $std, $ibforums, $DB, $print;

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['delete_topic'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		if ( count($this->tids) < 2 )
		{
			$this->moderate_error();  ### NEEDS CUSTOM MESSAGE
			return;
		}

		//-----------------------------------------
		// Get the topics in ascending date order
		//-----------------------------------------

		$topics = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid IN ('.implode( ",",$this->tids ).')', 'order' => 'start_date asc' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$topics[] = $r;
		}

		//-----------------------------------------
		// Check...
		//-----------------------------------------

		if ( count($topics) < 2 )
		{
			$this->moderate_error();  ### NEEDS CUSTOM MESSAGE
			return;
		}

		//-----------------------------------------
		// Get topic ID for first topic 'master'
		//-----------------------------------------

		$first_topic = array_shift( $topics );

		$main_topic_id = $first_topic['tid'];

		$merge_ids = array();

		foreach( $topics as $t )
		{
			$merge_ids[] = $t['tid'];
		}

		//-----------------------------------------
		// Update the posts, remove old polls, subs and topic
		//-----------------------------------------

		$DB->do_update( 'posts', array( 'topic_id' => $main_topic_id ), 'topic_id IN ('.implode(",",$merge_ids).")" );

		$DB->simple_exec_query( array( 'delete' => 'polls', 'where' => "tid IN (".implode(",",$merge_ids).")") );

		$DB->simple_exec_query( array( 'delete' => 'voters', 'where' => "tid IN (".implode(",",$merge_ids).")") );

		$DB->simple_exec_query( array( 'delete' => 'tracker', 'where' => "topic_id IN (".implode(",",$merge_ids).")") );

		$DB->simple_exec_query( array( 'delete' => 'topics', 'where' => "tid IN (".implode(",",$merge_ids).")") );

		//-----------------------------------------
		// Update the newly merged topic
		//-----------------------------------------

		$this->modfunc->rebuild_topic( $main_topic_id );
		$this->modfunc->forum_recount( $this->forum['id'] );
		$this->modfunc->stats_recount();
	}


	/*-------------------------------------------------------------------------*/
	// Complete move dUdE
	/*-------------------------------------------------------------------------*/

	function multi_complete_checked_move()
	{
		global $std, $ibforums, $DB, $print;

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['move_topic'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		$dest_id   = intval($ibforums->input['df']);
		$source_id = $this->forum['id'];

		$this->tids = array();

		//-----------------------------------------
		// Check for input..
		//-----------------------------------------

		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^TID_(\d+)$/", $key, $match ) )
 			{
 				if ( $ibforums->input[$match[0]] )
 				{
 					$this->tids[] = $match[1];
 				}
 			}
 		}

		//-----------------------------------------
		// Check for input..
		//-----------------------------------------

		if ($source_id == "")
		{
			$this->moderate_error('cp_error_move');
			return;
		}

		//-----------------------------------------

		if ($dest_id == "" or $dest_id == -1)
		{
			$this->moderate_error('cp_error_move');
			return;
		}

		//-----------------------------------------

		if ($source_id == $dest_id)
		{
			$this->moderate_error('cp_error_move');
			return;
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, sub_can_post, name', 'from' => 'forums', 'where' => "id IN(".$source_id.",".$dest_id.")" ) );
		$DB->simple_exec();

		if ( $DB->get_num_rows() != 2 )
		{
			$this->moderate_error('cp_error_move');
			return;
		}

		$source_name = "";
		$dest_name   = "";

		//-----------------------------------------
		// Check for an attempt to move into a subwrap forum
		//-----------------------------------------

		while ( $f = $DB->fetch_row() )
		{
			if ($f['id'] == $source_id)
			{
				$source_name = $f['name'];
			}
			else
			{
				$dest_name = $f['name'];
			}

			if ( ( $f['sub_can_post'] != 1 ) OR $f['redirect_on'] == 1 )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'forum_no_post_allowed' ) );
			}
		}

		$this->modfunc->topic_move( $this->tids, $source_id, $dest_id );

		//-----------------------------------------
		// Resync the forums..
		//-----------------------------------------

		$this->modfunc->forum_recount($source_id);

		$this->modfunc->forum_recount($dest_id);

		$this->moderate_log("Moved topics from $source_name to $dest_name");
	}


	/*-------------------------------------------------------------------------*/
	// Start move form
	/*-------------------------------------------------------------------------*/

	function multi_start_checked_move()
	{
		global $std, $ibforums, $DB, $print;

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator['move_topic'] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		$jump_html = $std->build_forum_jump('no_html');

		$this->output .= $this->html_start_form( array( 1 => array( 'CODE', 'topicchoice'     ),
														2 => array( 'f' , $this->forum['id']  ),
														3 => array( 'tact', 'domove'          ),
											   )      );

		$this->output .= $this->html->move_checked_form_start( $this->forum['name'] );

		$DB->simple_construct( array( 'select' => 'title, tid', 'from' => 'topics', 'where' => "forum_id=".$this->forum['id']." AND tid IN(".implode(",", $this->tids).")" ) );
		$DB->simple_exec();

		while( $row = $DB->fetch_row() )
		{
			$this->output .=  $this->html->move_checked_form_entry($row['tid'],$row['title']);
		}

		$this->output .= $this->html->move_checked_form_end($jump_html);

		$this->page_title = $ibforums->lang['cp_ttitle'];

		$this->nav = array ( "<a href='{$ibforums->base_url}act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>" );
	}

	/*-------------------------------------------------------------------------*/
	// MULTI-MOD!
	/*-------------------------------------------------------------------------*/

	function multi_topic_mmod()
	{
		global $std, $ibforums, $DB, $print;

		//-----------------------------------------
		// Issit coz i is black?
		//-----------------------------------------

		if ( ! strstr( $ibforums->input['tact'], 't_' ) )
		{
			$this->moderate_error('stupid_beggar');
		}

		$this->mm_id = intval( str_replace( 't_', "", $ibforums->input['tact'] ) );

		//-----------------------------------------
		// Init modfunc module
		//-----------------------------------------

		$this->modfunc->init( $this->forum, "", $this->moderator );

        //-----------------------------------------
		// Do we have permission?
		//-----------------------------------------

		if ( $this->modfunc->mm_authorize() != TRUE )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cp_no_perms') );
		}

        require( ROOT_PATH.'sources/lib/post_parser.php');

        $this->parser  = new post_parser(1);

        $this->mm_data = $ibforums->cache['multimod'][ $this->mm_id ];

        if ( ! $this->mm_data )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_mmid') );
        }

		//-----------------------------------------
        // Does this forum have this mm_id
        //-----------------------------------------

		if ( $this->modfunc->mm_check_id_in_forum( $this->forum['id'], $this->mm_data ) != TRUE )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_mmid') );
		}

		//-----------------------------------------
        // Still here? We're damn good to go sir!
        //-----------------------------------------

        $this->modfunc->stm_init();

        //-----------------------------------------
        // Open close?
        //-----------------------------------------

        if ( $this->mm_data['topic_state'] != 'leave' )
        {
        	if ( $this->mm_data['topic_state'] == 'close' )
        	{
        		$this->modfunc->stm_add_close();
        	}
        	else if ( $this->mm_data['topic_state'] == 'open' )
        	{
        		$this->modfunc->stm_add_open();
        	}
        }

        //-----------------------------------------
        // pin no-pin?
        //-----------------------------------------

        if ( $this->mm_data['topic_pin'] != 'leave' )
        {
        	if ( $this->mm_data['topic_pin'] == 'pin' )
        	{
        		$this->modfunc->stm_add_pin();
        	}
        	else if ( $this->mm_data['topic_pin'] == 'unpin' )
        	{
        		$this->modfunc->stm_add_unpin();
        	}
        }

        //-----------------------------------------
        // Approve / Unapprove
        //-----------------------------------------

        if ( $this->mm_data['topic_approve'] )
        {
        	if ( $this->mm_data['topic_approve'] == 1 )
        	{
        		$this->modfunc->stm_add_approve();
        	}
        	else if ( $this->mm_data['topic_approve'] == 2 )
        	{
        		$this->modfunc->stm_add_unapprove();
        	}
        }

        //-----------------------------------------
        // Update what we have so far...
        //-----------------------------------------

        $this->modfunc->stm_exec( $this->tids );

        //-----------------------------------------
        // Topic title (1337 - I am!)
        //-----------------------------------------

        $pre = "";
		$end = "";

        if ( $this->mm_data['topic_title_st'] )
        {
        	$pre =  preg_replace( "/'/", "\\'", $this->mm_data['topic_title_st'] );
        }

        if ( $this->mm_data['topic_title_end'] )
        {
        	$end =  preg_replace( "/'/", "\\'", $this->mm_data['topic_title_end'] );
        }

        $DB->cache_add_query( 'moderate_concat_title', array( 'pre'  => $pre,
															  'end'  => $end,
															  'tids' => $this->tids ) );
		$DB->cache_exec_query();

        //-----------------------------------------
        // Add reply?
        //-----------------------------------------

        if ( $this->mm_data['topic_reply'] and $this->mm_data['topic_reply_content'] )
        {
       		$move_ids = array();

       		foreach( $this->tids as $tid )
       		{
       			$move_ids[] = array( $tid, $this->forum['id'] );
       		}

        	$this->modfunc->auto_update = FALSE;  // Turn off auto forum re-synch, we'll manually do it at the end

        	$this->modfunc->topic_add_reply(
        									 $this->parser->convert( array(
																		   'TEXT'    => $this->mm_data['topic_reply_content'],
																		   'CODE'    => 1,
																		   'SMILIES' => 1,
															       )      )
										    , $move_ids
										    , $this->mm_data['topic_reply_postcount']
										   );
		}

		//-----------------------------------------
        // Move topic?
        //-----------------------------------------

        if ( $this->mm_data['topic_move'] )
        {
        	//-----------------------------------------
        	// Move to forum still exist?
        	//-----------------------------------------

        	$DB->simple_construct( array( 'select' => 'id, sub_can_post, name', 'from' => 'forums', 'where' => "id=".$this->mm_data['topic_move'] ) );
			$outer = $DB->simple_exec();

        	if ( $r = $DB->fetch_row( $outer ) )
        	{
        		if ( $r['sub_can_post'] != 1 )
        		{
        			$DB->do_update( 'topic_mmod', array( 'topic_move' => 0 ), "mm_id=".$this->mm_id );
        		}
        		else
        		{
        			if ( $r['id'] != $this->forum['id'] )
        			{
        				$this->modfunc->topic_move( $this->tids, $this->forum['id'], $r['id'], $this->mm_data['topic_move_link'] );

        				$this->modfunc->forum_recount( $r['id'] );
        			}
        		}
        	}
        	else
        	{
        		$DB->do_update( 'topic_mmod', array( 'topic_move' => 0 ), "mm_id=".$this->mm_id );
        	}
        }

        //-----------------------------------------
        // Recount root forum
        //-----------------------------------------

        $this->modfunc->forum_recount( $this->forum['id'] );

        $this->moderate_log("Applied multi-mod '{$this->mm_data['mm_title']}' on forum {$this->forum['name']}");
	}

	/*-------------------------------------------------------------------------*/
	// Alter the topics, yay!
	/*-------------------------------------------------------------------------*/

	function multi_alter_topics($mod_action="", $sql="")
	{
		global $std, $ibforums, $DB, $print;

		if ( ! $mod_action )
		{
			$this->moderate_error();
			return;
		}

		$pass = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$pass = 1;
		}
		else if ($this->moderator[$mod_action] == 1)
		{
			$pass = 1;
		}
		else
		{
			$pass = 0;
		}

		if ($pass == 0)
		{
			$this->moderate_error();
			return;
		}

		if ( $mod_action != 'delete_topic' )
		{
			$DB->simple_exec_query( array( 'update' => 'topics', 'set' => $sql, 'where' => "tid IN(".implode(",",$this->tids).")" ) );

			$this->moderate_log("Altered topics ($sql) (".implode(",",$this->tids).")");

		}
		else
		{
			if ( $this->trash_forum and $this->trash_forum != $this->forum['id'] )
			{
				//-----------------------------------------
				// Move, don't delete
				//-----------------------------------------

				$this->modfunc->topic_move($this->tids, $this->forum['id'], $this->trash_forum);
				$this->modfunc->forum_recount($this->trash_forum);
				$this->moderate_log("Applied trash can to delete topic id:".implode(",",$this->tids));
			}
			else
			{
				$this->modfunc->topic_delete($this->tids);
				$this->moderate_log("Deleted topics (IDs: ".implode(",",$this->tids).")");
			}
		}

		if ( $mod_action == 'delete_topic' or $mod_action == 'topic_q' and $this->forum['id'] )
		{
			$this->modfunc->forum_recount( $this->forum['id'] );
			$this->modfunc->stats_recount();
		}
	}

	/*-------------------------------------------------------------------------*/
	// TOPIC HISTORY:
	/*-------------------------------------------------------------------------*/

	function topic_history()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_access_cp'] == 1) {
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$tid = intval($ibforums->input['t']);

		//-----------------------------------------
		// Get all info for this topic-y-poos
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.intval($tid) ) );
		$DB->simple_exec();

		$topic = $DB->fetch_row();

		if ($topic['last_post'] == $topic['start_date'])
		{
			$avg_posts = 1;
		}
		else
		{
			$avg_posts = round( ($topic['posts'] + 1) / ((( $topic['last_post'] - $topic['start_date']) / 86400)), 1);
		}

		if ($avg_posts < 0)
		{
			$avg_posts = 1;
		}

		if ($avg_posts > ( $topic['posts'] + 1) )
		{
			$avg_posts = $topic['posts'] + 1;
		}

		$data = array(
					   'th_topic'      => $topic['title'],
					   'th_desc'       => $topic['description'],
					   'th_start_date' => $std->get_date($topic['start_date'], 'LONG'),
					   'th_start_name' => $std->make_profile_link($topic['starter_name'], $topic['starter_id'] ),
					   'th_last_date'  => $std->get_date($topic['last_post'], 'LONG'),
	    		 	   'th_last_name'  => $std->make_profile_link($topic['last_poster_name'], $topic['last_poster_id'] ),
					   'th_avg_post'   => $avg_posts,
					 );

		$this->output .= $this->html->topic_history($data);

		$this->output .= $this->html->mod_log_start();

		// Do we have any logs in the mod-logs DB about this topic? eh? well?

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'moderator_logs',
									  'where'  => 'topic_id='.intval($tid),
									  'order'  => 'ctime DESC' ) );
		$DB->simple_exec();


		if ( ! $DB->get_num_rows() )
		{
			$this->output .= $this->html->mod_log_none();
		}
		else
		{
			while ($row = $DB->fetch_row())
			{
				$row['member'] = $std->make_profile_link($row['member_name'], $row['member_id'] );
				$row['date']   = $std->get_date($row['ctime'], 'LONG');
				$this->output .= $this->html->mod_log_row($row);
			}
		}

		$this->output .= $this->html->mod_log_end();

		$this->page_title = $this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// MERGE TOPICS:
	/*-------------------------------------------------------------------------*/

	function merge_start()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1) {
			$passed = 1;
		}

		else if ($this->moderator['split_merge'] == 1) {
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}


		$this->output = $this->html_start_form( array( 1 => array( 'CODE', '61' ),
												       2 => array( 't' , $this->topic['tid'] ),
												       3 => array( 'f' , $this->forum['id']  ),
		 								      )      );

		$this->output .= $this->html->table_top( $ibforums->lang['mt_top']." ".$this->forum['name']." &gt; ".$this->topic['title'] );

		$this->output .= $this->html->mod_exp( $ibforums->lang['mt_explain'] );

		$this->output .= $this->html->merge_body( $this->topic['title'], $this->topic['description'] );

		$this->output .= $this->html->end_form( $ibforums->lang['mt_submit'] );

		$this->page_title = $ibforums->lang['mt_top']." ".$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// Merge complete
	/*-------------------------------------------------------------------------*/

	function merge_complete()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['split_merge'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		//-----------------------------------------
		// Check the input
		//-----------------------------------------

		if ($ibforums->input['topic_url'] == "" or $ibforums->input['title'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
		}

		//-----------------------------------------
		// Get the topic ID of the entered URL
		//-----------------------------------------

		preg_match( "/(\?|&amp;)(t|showtopic)=(\d+)($|&amp;)/", $ibforums->input['topic_url'], $match );

		$old_id = intval(trim($match[3]));

		if ($old_id == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'mt_no_topic' ) );
		}

		//-----------------------------------------
		// Get the topic from the DB
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'tid, title, forum_id, last_post, last_poster_id, last_poster_name, posts, views', 'from' => 'topics', 'where' => 'tid='.intval($old_id) ) );
		$DB->simple_exec();

		if ( ! $old_topic = $DB->fetch_row() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'mt_no_topic' ) );
		}

		//-----------------------------------------
		// Did we try and merge the same topic?
		//-----------------------------------------

		if ($old_id == $this->topic['tid'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'mt_same_topic' ) );
		}

		//-----------------------------------------
		// Do we have moderator permissions for this
		// topic (ie: in the forum the topic is in)
		//-----------------------------------------

		$pass = FALSE;

		if ( $this->topic['forum_id'] == $old_topic['forum_id'] )
		{
			$pass = TRUE;
		}
		else
		{
			if ( $ibforums->member['g_is_supmod'] == 1 )
			{
				$pass = TRUE;
			}
			else
			{
				$DB->simple_construct( array( 'select' => 'mid',
											  'from'   => 'moderators',
											  'where'  => "forum_id=".$old_topic['forum_id']." AND (member_id='".$ibforums->member['id']."' OR (is_group=1 AND group_id='".$ibforums->member['mgroup']."'))" ) );

				$DB->simple_exec();

				if ( $DB->get_num_rows() )
				{
					$pass = TRUE;
				}
			}
		}

		if ( $pass == FALSE )
		{
			// No, we don't have permission

			$this->moderate_error();
		}

		//-----------------------------------------
		// Update the posts, remove old polls, subs and topic
		//-----------------------------------------

		$DB->do_update( 'posts', array( 'topic_id' => $this->topic['tid'] ), 'topic_id='.$old_topic['tid'] );

		$DB->simple_exec_query( array( 'delete' => 'polls', 'where' => "tid=".$old_topic['tid'] ) );

		$DB->simple_exec_query( array( 'delete' => 'voters', 'where' => "tid=".$old_topic['tid'] ) );

		$DB->simple_exec_query( array( 'delete' => 'tracker', 'where' => "topic_id=".$old_topic['tid'] ) );

		$DB->simple_exec_query( array( 'delete' => 'topics', 'where' => "tid=".$old_topic['tid'] ) );

		//-----------------------------------------
		// Update the newly merged topic
		//-----------------------------------------

		$updater = array(  'title'       => $ibforums->input['title'],
						   'description' => $ibforums->input['desc']
						);

		if ($old_topic['last_post'] > $this->topic['last_post'])
		{
			$updater['last_post']        = $old_topic['last_post'];
			$updater['last_poster_name'] = $old_topic['last_poster_name'];
			$updater['last_poster_id']   = $old_topic['last_poster_id'];
		}

		// We need to now count the original post, which isn't in the "posts" field 'cos it was a new topic

		$old_topic['posts']++;

		$str = $DB->compile_db_update_string($updater);

		$DB->simple_exec_query( array( 'update' => 'topics', 'set' => "$str,views=views+{$old_topic['views']}", 'where' => 'tid='.$this->topic['tid'] ) );

		//-----------------------------------------
		// Fix up the "new_topic" attribute.
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'pid, author_name, author_id, post_date',
									  'from'   => 'posts',
									  'where'  => "topic_id=".$this->topic['tid'],
									  'order'  => 'post_date ASC',
									  'limit'  => array( 0,1 ) ) );

		$DB->simple_exec();

		if ( $first_post = $DB->fetch_row() )
		{
			$DB->do_update( 'posts', array( 'new_topic' => 0 ), "topic_id={$this->topic['tid']}" );
			$DB->do_update( 'posts', array( 'new_topic' => 1 ), "pid={$first_post['pid']}" );
		}

		//-----------------------------------------
		// Reset the post count for this topic
		//-----------------------------------------

		$amode = $first_post['author_id'] ? 1 : 0;

		$DB->simple_construct( array( 'select' => 'COUNT(*) as posts',
									  'from'   => 'posts',
									  'where'  => "queued <> 1 AND topic_id=".$this->topic['tid'] ) );

		$DB->simple_exec();

		if ( $post_count = $DB->fetch_row() )
		{
			$post_count['posts']--; //Remove first post

			$DB->do_update( 'topics', array( 'posts'         => $post_count['posts'],
											 'starter_name' => $first_post['author_name'],
					   						 'starter_id'   => $first_post['author_id'],
					   						 'start_date'   => $first_post['post_date'],
					   						 'author_mode'   => $amode
					   	  ) , 'tid='.$this->topic['tid'] );
		}

		//-----------------------------------------
		// Update the forum(s)
		//-----------------------------------------

		$this->recount($this->topic['forum_id']);

		if ($this->topic['forum_id'] != $old_topic['forum_id'])
		{
			$this->recount($old_topic['forum_id']);
		}

		$this->moderate_log("Merged topic '{$old_topic['title']}' with '{$this->topic['title']}'");

		$print->redirect_screen( $ibforums->lang['mt_redirect'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid'] );
	}

	/*-------------------------------------------------------------------------*/
	// UNSUBSCRIBE ALL FORM:
	/*-------------------------------------------------------------------------*/

	function unsubscribe_all_form()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$DB->simple_construct( array( 'select' => 'COUNT(trid) as subbed', 'from' => 'tracker', 'where' => "topic_id=".$this->topic['tid'] ) );
		$DB->simple_exec();

		$tracker = $DB->fetch_row();

        if ( $tracker['subbed'] < 1 )
        {
        	$text = $ibforums->lang['ts_none'];
        }
        else
        {
        	$text = sprintf($ibforums->lang['ts_count'], $tracker['subbed']);
        }

		$this->output = $this->html_start_form( array( 1 => array( 'CODE', '31' ),
												       2 => array( 't' , $this->topic['tid'] ),
												       3 => array( 'f' , $this->forum['id']  ),
		 								      )      );

		$this->output .= $this->html->table_top( $ibforums->lang['ts_title']." &gt; ".$this->forum['name']." &gt; ".$this->topic['title'] );

		$this->output .= $this->html->mod_exp( $text );

		$this->output .= $this->html->end_form( $ibforums->lang['ts_submit'] );

		$this->page_title = $ibforums->lang['ts_title']." &gt; ".$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// Unsub all
	/*-------------------------------------------------------------------------*/

	function unsubscribe_all()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		// Delete the subbies based on this topic ID

		$DB->simple_exec_query( array( 'delete' => 'tracker', 'where' => "topic_id=".$this->topic['tid'] ) );

		$print->redirect_screen( $ibforums->lang['ts_redirect'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid']."&st=".$ibforums->input['st'] );
	}

	/*-------------------------------------------------------------------------*/
	// EDIT POLL FORM:
	/*-------------------------------------------------------------------------*/

	function poll_delete_form()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['delete_topic'] == 1)
		{
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'polls', 'where' => "tid=".$this->topic['tid'] ) );
		$DB->simple_exec();

        $poll_data = $DB->fetch_row();

        if (! $poll_data['pid'])
        {
        	$this->moderate_error();
        }

		$this->output = $this->html_start_form( array( 1 => array( 'CODE', '23' ),
												       2 => array( 't' , $this->topic['tid'] ),
												       3 => array( 'f' , $this->forum['id']  ),
		 								      )      );

		$this->output .= $this->html->table_top( $ibforums->lang['pd_top']." ".$this->forum['name']." &gt; ".$this->topic['title'] );

		$this->output .= $this->html->mod_exp( $ibforums->lang['pd_text'] );

		$this->output .= $this->html->end_form( $ibforums->lang['pd_submit'] );

		$this->page_title = $ibforums->lang['pd_top'].$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// Do delete poll
	/*-------------------------------------------------------------------------*/

	function poll_delete_do()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1) {
			$passed = 1;
		}

		else if ($this->moderator['delete_topic'] == 1) {
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$DB->simple_exec_query( array( 'delete' => 'polls', 'where' => "tid=".$this->topic['tid'] ) );

		$DB->simple_exec_query( array( 'delete' => 'voters', 'where' => "tid=".$this->topic['tid'] ) );

		$DB->do_update( 'topics', array( 'poll_state' => '', 'last_vote' => '', 'total_votes' => '' ), 'tid='.$this->topic['tid'] );

		// Boing!

		$print->redirect_screen( $ibforums->lang['pd_redirect'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid']."&st=".$ibforums->input['st'] );
	}

	/*-------------------------------------------------------------------------*/
	// Poll edit
	/*-------------------------------------------------------------------------*/

	function poll_edit_do()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['edit_post'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'polls', 'where' => "tid=".$this->topic['tid'] ) );
		$DB->simple_exec();

        $poll_data = $DB->fetch_row();

        if (! $poll_data['pid'])
        {
        	$this->moderate_error();
        }

        $poll_answers = unserialize(stripslashes($poll_data['choices']));

		reset($poll_answers);

		$new_poll_array = array();
		$ids            = array();
		$rearranged     = array();

		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^POLL_(\d+)$/", $key, $match ) )
 			{
 				if (isset($ibforums->input[$match[0]]))
 				{
 					$ids[] = $match[1];
 				}
 			}
 		}

 		//-----------------------------------------

 		foreach ($poll_answers as $entry)
		{
			$rearranged[ $entry[0] ] = array( $entry[0], $entry[1], $entry[2]);
		}

 		//-----------------------------------------

 		$total_votes = 0;

 		foreach( $ids as $nid )
 		{
 			//-----------------------------------------
 			// Is it a current poll thingy?
 			//-----------------------------------------

 			if ( strlen($rearranged[ $nid ][1]) > 0 )
 			{
 				$new_poll_array[] = array( $rearranged[ $nid ][0], $ibforums->input['POLL_'.$nid], intval($ibforums->input['VOTES_'.$nid]) );
 				$total_votes += intval($ibforums->input['VOTES_'.$nid]);
 			}
 			else
 			{
 				if ( strlen($ibforums->input['POLL_'.$nid]) > 0 )
 				{
 					$new_poll_array[] = array( $nid, $ibforums->input['POLL_'.$nid], intval($ibforums->input['VOTES_'.$nid]) );
 					$total_votes += intval($ibforums->input['VOTES_'.$nid]);
 				}
 			}
		}

		//-----------------------------------------
		// Take care of any new ones...
		//-----------------------------------------

		$poll_data['choices'] = addslashes(serialize($new_poll_array));

		$DB->do_update( 'polls', array( 'votes'         => $total_votes,
										'choices'       => addslashes(serialize($new_poll_array)),
										'poll_question' => $ibforums->input['poll_question'],
									  ), 'tid='.$this->topic['tid']  );

		// Update the topic table to change the poll_only value.

		$poll_state = $ibforums->input['pollonly'] == 1 ? 'closed' : 'open';

		$DB->do_update( 'topics', array( 'poll_state' => $poll_state ), 'tid='.$this->topic['tid'] );

		$this->moderate_log("Edited a Poll: Set total votes to $total_votes used to be {$poll_data['votes']}");

		$print->redirect_screen( $ibforums->lang['pe_done'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid']."&st=".$ibforums->input['st'] );
    }


    /*-------------------------------------------------------------------------*/
	// Poll edit form
	/*-------------------------------------------------------------------------*/

	function poll_edit_form()
	{
		global $std, $ibforums, $DB, $print;

		$ibforums->vars['max_poll_choices'] = $ibforums->vars['max_poll_choices'] ? $ibforums->vars['max_poll_choices'] : 10;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['edit_post'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'polls', 'where' => "tid=".$this->topic['tid'] ) );
		$DB->simple_exec();

        $poll_data = $DB->fetch_row();

        if (! $poll_data['pid'])
        {
        	$this->moderate_error();
        }

		$this->output = $this->html_start_form( array( 1 => array( 'CODE', '21' ),
												       2 => array( 't' , $this->topic['tid'] ),
												       3 => array( 'f' , $this->forum['id']  ),
		 								      )      );

		$this->output .= $this->html->table_top( $ibforums->lang['pe_top']." ".$this->forum['name']." &gt; ".$this->topic['title'] );

		$this->output .= $this->html->poll_edit_top();

		$poll_answers = unserialize(stripslashes($poll_data['choices']));

		reset($poll_answers);

		foreach ($poll_answers as $entry)
		{
			$id     = $entry[0];
			$choice = $entry[1];
			$votes  = $entry[2];

			$this->output .= $this->html->poll_entry($id, $choice, $votes);

		}

		//-----------------------------------------
		// Not got a silly number here?
		//-----------------------------------------

		if ( $ibforums->vars['max_poll_choices'] > 50 )
		{
			$ibforums->vars['max_poll_choices'] = 50;
		}

		if ( count($poll_answers) < $ibforums->vars['max_poll_choices'] )
		{
			for ( $i = count($poll_answers) ; $i <= $ibforums->vars['max_poll_choices'] ; $i++ )
			{
				$this->output .= $this->html->poll_edit_new_entry($i);
			}
		}


        $this->output .= $this->html->poll_select_form($poll_data['poll_question']);

		$this->output .= $this->html->end_form( $ibforums->lang['pe_submit'] );

		$this->page_title = $ibforums->lang['pe_top'].$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// MOVE FORM:
	/*-------------------------------------------------------------------------*/

	function move_form()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1) {
			$passed = 1;
		}

		else if ($this->moderator['move_topic'] == 1) {
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$this->output = $this->html_start_form( array( 1 => array( 'CODE', '14' ),
												       2 => array( 'tid' , $this->topic['tid'] ),
												       3 => array( 'sf'  , $this->forum['id']  ),
		 								      )      );

		$jump_html = $std->build_forum_jump(0,0,0);

		$this->output .= $this->html->table_top( $ibforums->lang['top_move']." ".$this->forum['name']." &gt; ".$this->topic['title'] );
		$this->output .= $this->html->mod_exp( $ibforums->lang['move_exp'] );
		$this->output .= $this->html->move_form( $jump_html , $this->forum['name']);
		$this->output .= $this->html->end_form( $ibforums->lang['submit_move'] );

		$this->page_title = $ibforums->lang['t_move'].": ".$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// Complete move
	/*-------------------------------------------------------------------------*/

	function do_move()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1) {
			$passed = 1;
		}

		else if ($this->moderator['move_topic'] == 1) {
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		//-----------------------------------------
		// Check for input..
		//-----------------------------------------

		if ($ibforums->input['sf'] == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_no_source' ) );
		}

		//-----------------------------------------

		if ($ibforums->input['move_id'] == "" or $ibforums->input['move_id'] == -1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_no_forum' ) );
		}

		//-----------------------------------------

		if ($ibforums->input['move_id'] == $ibforums->input['sf'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_same_forum' ) );
		}

		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, sub_can_post, name, redirect_on', 'from' => 'forums', 'where' => "id IN(".$ibforums->input['sf'].",".$ibforums->input['move_id'].")" ) );
		$DB->simple_exec();

		if ($DB->get_num_rows() != 2)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_no_forum' ) );
		}

		$source = intval($ibforums->input['sf']);
		$moveto = intval($ibforums->input['move_id']);

		$source_name = "";
		$dest_name   = "";

		//-----------------------------------------
		// Check for an attempt to move into a subwrap forum
		//-----------------------------------------

		while ( $f = $DB->fetch_row() )
		{
			if ($f['id'] == $ibforums->input['sf'])
			{
				$source_name = $f['name'];
			}
			else
			{
				$dest_name = $f['name'];
			}

			if ( ($f['sub_can_post'] != 1) OR $f['redirect_on'] == 1 )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'forum_no_post_allowed' ) );
			}
		}

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.intval($ibforums->input['tid']) ) );
		$DB->simple_exec();

		if ( ! $this->topic = $DB->fetch_row() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_no_forum' ) );
		}

		$ibforums->input['leave'] = $ibforums->input['leave'] == 'y' ? 1 : 0;

		$this->modfunc->topic_move($this->topic['tid'], $ibforums->input['sf'], $ibforums->input['move_id'], $ibforums->input['leave']);

		$ibforums->input['t'] = $this->topic['tid'];

		$this->moderate_log("Moved a topic from $source_name to $dest_name");

		// Resync the forums..

		$this->modfunc->forum_recount($source);

		$this->modfunc->forum_recount($moveto);

		$print->redirect_screen( $ibforums->lang['p_moved'], "act=SF&f=".$this->forum['id'] );
	}

	/*-------------------------------------------------------------------------*/
	// Delete post
	/*-------------------------------------------------------------------------*/

	function delete_post()
	{
		global $std, $ibforums, $DB, $print;

		// Get this post id.

		$ibforums->input['p'] = intval($ibforums->input['p']);

		$DB->simple_construct( array( 'select' => 'pid, author_id, post_date, new_topic', 'from' => 'posts', 'where' => "topic_id={$this->topic['tid']} and pid={$ibforums->input['p']}" ) );
		$DB->simple_exec();

		if ( ! $post = $DB->fetch_row() )
		{
			$this->moderate_error();
		}

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}
		else if ($this->moderator['delete_post'] == 1)
		{
			$passed = 1;
		}
		else if ( ($ibforums->member['g_delete_own_posts'] == 1) and ( $ibforums->member['id'] == $post['author_id'] ) )
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		//-----------------------------------------
		// Check to make sure that this isn't the first post in the topic..
		//-----------------------------------------

		if ($post['new_topic'] == 1)
		{
			$this->moderate_error('no_delete_post');
		}

		if ( $this->trash_forum and $this->trash_forum != $this->forum['id'] )
		{
			//-----------------------------------------
			// Set up and pass to split topic handler
			//-----------------------------------------

			$ibforums->input['checked'] = 1;
			$ibforums->input['fid']     = $this->trash_forum;
			$ibforums->input['title']   = "From: ".$this->topic['title'];
			$ibforums->input['desc']    = "From Topic ID: ".$this->topic['tid'];
			$ibforums->input[ 'post_'.$ibforums->input['p'] ] = 1;

			$this->trash_inuse = 1;

			$this->multi_split_topic();

			$this->trash_inuse = 0;
		}
		else
		{
			$this->modfunc->post_delete( $ibforums->input['p'] );
			$this->modfunc->forum_recount( $this->forum['id'] );
		}

		$print->redirect_screen( $ibforums->lang['post_deleted'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid']."&st=".$ibforums->input['st'] );
	}

	/*-------------------------------------------------------------------------*/
	// DELETE TOPIC:
	/*-------------------------------------------------------------------------*/

	function delete_form()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['delete_topic'] == 1)
		{
			$passed = 1;
		}

		else if ($this->topic['starter_id'] == $ibforums->member['id'])
		{
			if ($ibforums->member['g_delete_own_topics'] == 1)
			{
				$passed = 1;
			}
		}

		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$this->output = $this->html->delete_js();

		$this->output .= $this->html_start_form( array( 1 => array( 'CODE', '08' ),
												        2 => array( 't', $this->topic['tid'] )
		 								       )      );

		$this->output .= $this->html->table_top( $ibforums->lang['top_delete']." ".$this->forum['name']." &gt; ".$this->topic['title'] );
		$this->output .= $this->html->mod_exp( $ibforums->lang['delete_topic'] );
		$this->output .= $this->html->end_form( $ibforums->lang['submit_delete'] );

		$this->page_title = $ibforums->lang['t_delete'].": ".$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// Do delete topic
	/*-------------------------------------------------------------------------*/

	function delete_topic()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}
		else if ($this->moderator['delete_topic'] == 1)
		{
			$passed = 1;
		}
		else if ($this->topic['starter_id'] == $ibforums->member['id'])
		{
			if ($ibforums->member['g_delete_own_topics'] == 1)
			{
				$passed = 1;
			}
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		// Do we have a linked topic to remove?

		$DB->simple_construct( array( 'select' => 'tid, forum_id', 'from' => 'topics', 'where' => "state='link' AND moved_to='".$this->topic['tid'].'&'.$this->forum['id']."'" ) );
		$DB->simple_exec();

		if ( $linked_topic = $DB->fetch_row() )
		{
			$DB->simple_exec_query( array( 'delete' => 'topics', 'where' => "tid=".$linked_topic['tid'] ) );

			$this->modfunc->forum_recount($linked_topic['forum_id']);
		}

		if ( $this->trash_forum and $this->trash_forum != $this->forum['id'] )
		{
			//-----------------------------------------
			// Move, don't delete
			//-----------------------------------------

			$this->modfunc->topic_move($this->topic['tid'], $this->forum['id'], $this->trash_forum);
			$this->modfunc->forum_recount($this->forum['id']);
			$this->modfunc->forum_recount($this->trash_forum);

			$this->moderate_log("Applied trash can to delete topic id:".$this->topic['tid']);
		}
		else
		{
			$this->modfunc->topic_delete($this->topic['tid']);
			$this->moderate_log("Deleted a topic");
		}

		$print->redirect_screen( $ibforums->lang['p_deleted'], "act=SF&f=".$this->forum['id'] );
	}

	/*-------------------------------------------------------------------------*/
	// EDIT TOPIC:
	/*-------------------------------------------------------------------------*/

	function edit_form()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['edit_topic'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		$this->output = $this->html_start_form( array( 1 => array( 'CODE', '12' ),
												       2 => array( 't', $this->topic['tid'] )
		 								      )      );

		$this->output .= $this->html->table_top( $ibforums->lang['top_edit']." ".$this->forum['name']." &gt; ".$this->topic['title'] );
		$this->output .= $this->html->mod_exp( $ibforums->lang['edit_topic'] );
		$this->output .= $this->html->topictitle_fields( $this->topic['title'], $this->topic['description'] );
		$this->output .= $this->html->end_form( $ibforums->lang['submit_edit'] );

		$this->page_title = $ibforums->lang['t_edit'].": ".$this->topic['title'];

		$this->nav = array ( "<a href='{$this->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 "<a href='{$this->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						   );
	}

	/*-------------------------------------------------------------------------*/
	// DO edit
	/*-------------------------------------------------------------------------*/

	function do_edit()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['edit_topic'] == 1)
		{
			$passed = 1;
		}
		else
		{
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		if (empty($this->topic['tid']))
		{
			$this->moderate_error();
		}

		if ( trim($ibforums->input['TopicTitle']) == "")
		{
			$std->Error( array( 'LEVEL' => 2, 'MSG' => 'no_topic_title' ) );
		}

		$topic_title = preg_replace( "/'/", "/\\'/", $ibforums->input['TopicTitle'] );
		$topic_desc  = preg_replace( "/'/", "/\\'/", $ibforums->input['TopicDesc']  );

		$DB->do_update( 'topics', array( 'title' => $topic_title, 'description' => $topic_desc ), 'tid='.$this->topic['tid'] );

		if ($this->topic['tid'] == $this->forum['last_id'])
		{
			$DB->do_update( 'forums', array( 'last_title' => $topic_title ), 'id='.$this->forum['id'] );
		}

		$std->update_forum_cache();

		$this->moderate_log("Moderator edited a topic title: (ID: {$this->topic['tid']}) From '{$this->topic['title']}' to '$topic_title'");

		$print->redirect_screen( $ibforums->lang['p_edited'], "act=SF&f=".$this->forum['id'] );
	}

	/*-------------------------------------------------------------------------*/
	// OPEN TOPIC:
	/*-------------------------------------------------------------------------*/

	function open_topic()
	{
		global $std, $ibforums, $DB, $print;

		if ($this->topic['state'] == 'open')
		{
			$this->moderate_error();
		}

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->topic['starter_id'] == $ibforums->member['id'])
		{
			if ($ibforums->member['g_open_close_posts'] == 1)
			{
				$passed = 1;
			}
		}
		else
		{
			$passed = 0;
		}

		if ($this->moderator['open_topic'] == 1)
		{
			$passed = 1;
		}


		if ($passed != 1) $this->moderate_error();

		$this->modfunc->topic_open($this->topic['tid']);

		$this->moderate_log("Opened Topic");

		$print->redirect_screen( $ibforums->lang['p_opened'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid']."&st=".$ibforums->input['st'] );
	}

	/*-------------------------------------------------------------------------*/
	// CLOSE TOPIC:
	/*-------------------------------------------------------------------------*/

	function close_topic()
	{
		global $std, $ibforums, $DB, $print;

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->topic['starter_id'] == $ibforums->member['id'])
		{
			if ($ibforums->member['g_open_close_posts'] == 1)
			{
				$passed = 1;
			}
		}
		else
		{
			$passed = 0;
		}

		if ($this->moderator['close_topic'] == 1)
		{
			$passed = 1;
		}


		if ($passed != 1) $this->moderate_error();

		$this->modfunc->topic_close($this->topic['tid']);

		$this->moderate_log("Locked Topic");

		$print->redirect_screen( $ibforums->lang['p_closed'], "act=SF&f=".$this->forum['id'] );
	}

	/*-------------------------------------------------------------------------*/
	// PIN TOPIC:
	/*-------------------------------------------------------------------------*/

	function pin_topic()
	{
		global $std, $ibforums, $DB, $print;

		if ($this->topic['PIN_STATE'] == 1)
		{
			$this->moderate_error();
		}

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['pin_topic'] == 1)
		{
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		$this->modfunc->topic_pin($this->topic['tid']);

		$this->moderate_log("Pinned Topic");

		$print->redirect_screen( $ibforums->lang['p_pinned'], "showtopic=".$this->topic['tid']."&st=".$ibforums->input['st'] );

	}

	/*-------------------------------------------------------------------------*/
	// UNPIN TOPIC:
	/*-------------------------------------------------------------------------*/

	function unpin_topic()
	{
		global $std, $ibforums, $DB, $print;

		if ($this->topic['pinned'] == 0)
		{
			$this->moderate_error();
		}

		$passed = 0;

		if ($ibforums->member['g_is_supmod'] == 1)
		{
			$passed = 1;
		}

		else if ($this->moderator['unpin_topic'] == 1)
		{
			$passed = 1;
		}
		else {
			$passed = 0;
		}

		if ($passed != 1) $this->moderate_error();

		$this->modfunc->topic_unpin($this->topic['tid']);

		$this->moderate_log("Unpinned Topic");

		$print->redirect_screen( $ibforums->lang['p_unpinned'], "act=ST&f=".$this->forum['id']."&t=".$this->topic['tid']."&st=".$ibforums->input['st'] );
	}

	/*-------------------------------------------------------------------------*/
	// GET TOPIC IDS
	/*-------------------------------------------------------------------------*/

	function get_tids()
	{
		global $std, $ibforums, $DB;

		$ids = array();

 		$ids = explode( ',', $ibforums->input['selectedtids'] );

 		if ( count($ids) < 1 )
 		{
 			$this->moderate_error('cp_err_no_topics');
 			return;
 		}

 		return $ids;
	}

	/*-------------------------------------------------------------------------*/
	// Get Pids
	/*-------------------------------------------------------------------------*/

	function get_pids()
	{
		global $std, $ibforums, $DB;

		$ids = array();

 		$ids = explode( ',', $ibforums->input['selectedpids'] );

 		if ( count($ids) < 1 )
 		{
 			$this->moderate_error('cp_err_no_topics');
 			return;
 		}

 		return $ids;
	}

	/*-------------------------------------------------------------------------*/
	// MODERATE ERROR:
	/*-------------------------------------------------------------------------*/

	function moderate_error($msg = 'moderate_no_permission')
	{
		global $std;

		$std->Error( array( 'LEVEL' => 2, 'MSG' => $msg ) );

		// Make sure we exit..

		exit();
	}

	/*-------------------------------------------------------------------------*/
	// MODERATE LOG:
	/*-------------------------------------------------------------------------*/

	function moderate_log($title = 'unknown')
	{
		global $std, $ibforums, $DB, $HTTP_REFERER, $QUERY_STRING;

		$this->modfunc->add_moderate_log( $ibforums->input['f'], $ibforums->input['t'], $ibforums->input['p'], $this->topic['title'], $title );
	}


	/*-------------------------------------------------------------------------*/
	// Re Count topics for the forums:
	/*-------------------------------------------------------------------------*/

	function recount($fid="")
	{
		global $ibforums, $DB, $std;

		if ($fid == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'move_no_source' ) );
		}

		$this->modfunc->forum_recount( $fid );
	}

	/*-------------------------------------------------------------------------*/
	// HTML: start form.
	/*-------------------------------------------------------------------------*/

	function html_start_form($additional_tags=array())
	{
		global $ibforums, $std;

		$form = "<form action='{$this->base_url}' method='POST' name='REPLIER'>".
				"<input type='hidden' name='st' value='".$ibforums->input['st']."' />".
				"<input type='hidden' name='act' value='mod' />".
				"<input type='hidden' name='s' value='".$ibforums->session_id."' />".
				"<input type='hidden' name='f' value='".$this->forum['id']."' />".
				"<input type='hidden' name='selectedpids' value='".$ibforums->input['selectedpids']."' />".
				"<input type='hidden' name='auth_key' value='".$std->return_md5_check()."' />";

		// Any other tags to add?

		if ( count( $additional_tags ) )
		{
			foreach( $additional_tags as $k => $v )
			{
				$form .= "\n<input type='hidden' name='{$v[0]}' value='{$v[1]}'>";
			}
		}

		return $form;
    }

    /*-------------------------------------------------------------------------*/
	// Faster Pussycat, Kill, Kill!
	/*-------------------------------------------------------------------------*/

	function bash_uploaded_photos($id)
	{
		global $ibforums, $DB, $std, $print;

		foreach( array( 'swf', 'jpg', 'jpeg', 'gif', 'png' ) as $ext )
		{
			if ( @file_exists( $ibforums->vars['upload_dir']."/photo-".$id.".".$ext ) )
			{
				@unlink( $ibforums->vars['upload_dir']."/photo-".$id.".".$ext );
			}
		}
	}

	function bash_uploaded_avatars($id)
	{
		global $ibforums, $DB, $std, $print;

		foreach( array( 'swf', 'jpg', 'jpeg', 'gif', 'png' ) as $ext )
		{
			if ( @file_exists( $ibforums->vars['upload_dir']."/av-".$id.".".$ext ) )
			{
				@unlink( $ibforums->vars['upload_dir']."/av-".$id.".".$ext );
			}
		}
	}
}

?>