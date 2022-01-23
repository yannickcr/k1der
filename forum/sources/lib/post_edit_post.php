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
|   > Edit post library
|   > Module written by Matt Mecham
|   > Date started: 19th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/



class post_functions extends Post {

	var $nav               = array();
	var $title             = "";
	var $post              = array();
	var $topic             = array();
	var $upload            = array();
	var $moderator         = array( 'member_id' => 0, 'member_name' => "", 'edit_post' => 0 );
	var $orig_post         = array();
	var $edit_title        = 0;
	var $post_key		   = "";
	var $class             = "";

	function post_functions($class)
	{
		global $ibforums, $std, $DB;

		$this->class = $class;

		//-----------------------------------------
		// Lets load the topic from the database before we do anything else.
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => "tid=".intval($ibforums->input['t']) ) );
		$DB->simple_exec();

		$this->topic = $DB->fetch_row();

		//-----------------------------------------
		// Is it legitimate?
		//-----------------------------------------

		if (! $this->topic['tid'])
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

		//-----------------------------------------
		// Load the old post
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'posts', 'where' => "pid=".intval($ibforums->input['p']) ) );
		$DB->simple_exec();

		$this->orig_post = $DB->fetch_row();

		if (! $this->orig_post['pid'])
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

		//-----------------------------------------
		// Generate post key (do we have one?)
		//-----------------------------------------

		if ( ! $this->orig_post['post_key'] )
		{
			//-----------------------------------------
			// Generate one and save back to post and attachment
			// to ensure 1.3 < compatibility
			//-----------------------------------------

			$this->post_key = md5(microtime());

			$DB->do_update( 'posts', array( 'post_key' => $this->post_key ), 'pid='.$this->orig_post['pid'] );

			$DB->do_update( 'attachments', array( 'attach_post_key' => $this->post_key ), 'attach_pid='.$this->orig_post['pid'] );
		}
		else
		{
			$this->post_key = $this->orig_post['post_key'];
		}

		//-----------------------------------------
		// Load the moderator
		//-----------------------------------------

		if ($ibforums->member['id'])
		{
			$DB->simple_construct( array( 'select' => 'member_id, member_name, mid, edit_post, edit_topic',
										  'from'   => 'moderators',
										  'where'  => "forum_id=".$this->class->forum['id']." AND (member_id='".$ibforums->member['id']."' OR (is_group=1 AND group_id='".$ibforums->member['mgroup']."'))" ) );

			$DB->simple_exec();

			$this->moderator = $DB->fetch_row();
		}

		//-----------------------------------------
		// Lets do some tests to make sure that we are
		// allowed to edit this topic
		//-----------------------------------------

		$can_edit = 0;

		if ($ibforums->member['g_is_supmod'])
		{
			$can_edit = 1;
		}
		if ($this->moderator['edit_post'])
		{
			$can_edit = 1;
		}
		if ( ($this->orig_post['author_id'] == $ibforums->member['id']) and ($ibforums->member['g_edit_posts']) )
		{
			// Have we set a time limit?

			if ($ibforums->member['g_edit_cutoff'] > 0)
			{
				if ( $this->orig_post['post_date'] > ( time() - ( intval($ibforums->member['g_edit_cutoff']) * 60 ) ) )
				{
					$can_edit = 1;
				}
			}
			else
			{
				$can_edit = 1;
			}
		}

		if ($can_edit != 1)
		{
			$std->Error( array( LEVEL => 1, MSG => 'not_op') );
		}

		//-----------------------------------------
		// Check access
		//-----------------------------------------

		$this->class->check_for_edit($this->topic);

		//-----------------------------------------
		// // Do we have edit topic abilities?
		//-----------------------------------------

		if ( $this->orig_post['new_topic'] == 1 )
		{
			if ($ibforums->member['g_is_supmod'] == 1)
			{
				$this->edit_title = 1;
			}
			else if ($this->moderator['edit_topic'] == 1)
			{
				$this->edit_title = 1;
			}
			else if ($ibforums->member['g_edit_topic'] == 1 )
			{
				$this->edit_title = 1;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// MAIN PROCESS FUNCTION
	/*-------------------------------------------------------------------------*/

	function process()
	{
		global $ibforums, $std, $DB, $print;

		//-----------------------------------------
		// Did we remove an attachment?
		//-----------------------------------------

		if ( $ibforums->input['removeattachid'] )
		{
			if ( $ibforums->input[ 'removeattach_'. $ibforums->input['removeattachid'] ] )
			{
				$this->class->pf_remove_attachment( intval($ibforums->input['removeattachid']), $this->post_key );
				$this->show_form();
			}
		}

		//-----------------------------------------
		// Did we add an attachment?
		//-----------------------------------------

		if ( $ibforums->input['attachgo'] )
		{
			$this->class->obj['post_errors'] = "";
			$this->upload_id = $this->class->process_upload();
			$this->show_form();
		}

		//-----------------------------------------
		// Parse the post, and check for any errors.
		// overwrites saved post intentionally
		//-----------------------------------------

		$this->post = $this->class->compile_post();

		if ($this->class->obj['post_errors'] == "")
		{
			$this->upload = $this->class->process_upload();
		}

		if ( ($this->class->obj['post_errors'] != "") or ($this->class->obj['preview_post'] != "") )
		{
			//-----------------------------------------
			// Show the form again
			//-----------------------------------------

			$this->show_form($class);
		}
		else
		{
			$this->complete_edit($class);
		}
	}


	/*-------------------------------------------------------------------------*/
	// COMPLETE EDIT THINGY
	/*-------------------------------------------------------------------------*/

	function complete_edit($class)
	{
		global $ibforums, $std, $DB, $print;

		$time = $std->get_date( time(), 'LONG' );

		//-----------------------------------------
		// Reset some data
		//-----------------------------------------

		$this->post['ip_address']  = $this->orig_post['ip_address'];
		$this->post['topic_id']    = $this->orig_post['topic_id'];
		$this->post['author_id']   = $this->orig_post['author_id'];
		$this->post['pid']         = $this->orig_post['pid'];
		$this->post['post_date']   = $this->orig_post['post_date'];
		$this->post['author_name'] = $this->orig_post['author_name'];
		$this->post['queued']      = $this->orig_post['queued'];
		$this->post['edit_time']   = time();
		$this->post['edit_name']   = $ibforums->member['name'];

		//-----------------------------------------
		// If the post icon has changed, update the topic post icon
		//-----------------------------------------

		if ($this->orig_post['new_topic'] == 1)
		{
			if ($this->post['icon_id'] != $this->orig_post['icon_id'])
			{
				$DB->do_update( 'topics', array( 'icon_id' => $this->post['icon_id'] ), 'tid='.$this->topic['tid'] );
			}
		}

		//-----------------------------------------
		// Update topic title?
		//-----------------------------------------

		if ( $this->edit_title == 1 )
		{
			$ibforums->input['TopicTitle'] = $this->class->pf_clean_topic_title( $ibforums->input['TopicTitle'] );

			$ibforums->input['TopicTitle'] = trim( $this->class->parser->bad_words( $ibforums->input['TopicTitle'] ) );
			$ibforums->input['TopicDesc']  = trim( $this->class->parser->bad_words( $ibforums->input['TopicDesc']  ) );

			if ( $ibforums->input['TopicTitle'] != "" )
			{
				if ( ($ibforums->input['TopicTitle'] != $this->topic['title']) or ($ibforums->input['TopicDesc'] != $this->topic['description'])  )
				{
					$DB->do_update( 'topics', array( 'title'       => $ibforums->input['TopicTitle'],
													 'description' => $ibforums->input['TopicDesc']
												   ) , "tid=".$this->topic['tid']
								  );

					if ($this->topic['tid'] == $this->class->forum['last_id'])
					{
						$DB->do_update( 'forums', array( 'last_title' => $ibforums->input['TopicTitle'] ), 'id='.$this->class->forum['id'] );
						$std->update_forum_cache();
					}

					if ( ($this->moderator['edit_topic'] == 1) OR ( $ibforums->member['g_is_supmod'] == 1 ) )
					{
						$DB->do_insert( 'moderator_logs', array (
																'forum_id'    => $this->class->forum['id'],
																'topic_id'    => $this->topic['tid'],
																'post_id'     => $this->post['pid'],
																'member_id'   => $ibforums->member['id'],
																'member_name' => $ibforums->member['name'],
																'ip_address'  => $ibforums->input['IP_ADDRESS'],
																'http_referer'=> $_SERVER['HTTP_REFERER'],
																'ctime'       => time(),
																'topic_title' => $this->topic['title'],
																'action'      => "Edited topic title or description '{$this->topic['title']}' to '{$ibforums->input['TopicTitle']}' via post form",
																'query_string'=> $_SERVER['QUERY_STRING'],
															)    );
					}
				}
			}
		}

		//-----------------------------------------
		// Update the database (ib_forum_post)
		//-----------------------------------------

		$this->post['append_edit'] = 1;

		if ($ibforums->member['g_append_edit'])
		{
			if ($ibforums->input['add_edit'] != 1)
			{
				$this->post['append_edit'] = 0;
			}
		}

		$db_string = $DB->compile_db_update_string( $this->post );

		$DB->do_update( 'posts', $this->post, 'pid='.$this->post['pid'] );

		//-----------------------------------------
		// Make attachments "permanent"
		//-----------------------------------------

		$this->class->pf_make_attachments_permanent($this->post_key, $this->topic['tid'], $this->post['pid']);

		//-----------------------------------------
		// Make sure paperclip symbol is OK
		//-----------------------------------------

		$this->class->pf_recount_topic_attachments($this->topic['tid']);

		//-----------------------------------------
		// Redirect them back to the topic
		//-----------------------------------------

		$print->redirect_screen( $ibforums->lang['post_edited'], "act=ST&f={$this->class->forum['id']}&t={$this->topic['tid']}&st={$ibforums->input['st']}#entry{$this->post['pid']}");

	}

	/*-------------------------------------------------------------------------*/
	// SHOW FORM
	/*-------------------------------------------------------------------------*/

	function show_form()
	{
		global $ibforums, $std, $DB, $print;

		//-----------------------------------------
		// Sort out the "raw" textarea input and make it safe incase
		// we have a <textarea> tag in the raw post var.
		//-----------------------------------------

		$raw_post = isset($_POST['Post']) ? $std->txt_htmlspecialchars($_POST['Post']) : $this->class->parser->unconvert($this->orig_post['post'], $this->class->forum['use_ibc'], $this->class->forum['use_html']);

		if (isset($raw_post))
		{
			$raw_post = $std->txt_raw2form($raw_post);
		}

		//-----------------------------------------
		// Is this the first post in the topic?
		//-----------------------------------------

		if ( $this->edit_title == 1 )
		{
			$topic_title = isset($_POST['TopicTitle']) ? $ibforums->input['TopicTitle'] : $this->topic['title'];
			$topic_desc  = isset($_POST['TopicDesc'])  ? $ibforums->input['TopicDesc']  : $this->topic['description'];

			$topic_title = $this->class->html->topictitle_fields( array( 'TITLE' => $topic_title, 'DESC' => $topic_desc ) );
		}

		//-----------------------------------------
		// Do we have any posting errors?
		//-----------------------------------------

		if ($this->class->obj['post_errors'])
		{
			$this->class->output .= $this->class->html->errors( $ibforums->lang[ $this->class->obj['post_errors'] ]);
		}

		if ($this->class->obj['preview_post'])
		{
			$this->class->parser->pp_do_html = intval($ibforums->input['post_htmlstatus']) AND $this->class->forum['use_html'] AND $ibforums->member['g_dohtml'] ? 1 : 0;
			$this->class->parser->pp_nl2br   = $ibforums->input['post_htmlstatus'] == 2 ? 1 : 0;

			$this->post['post'] = $this->class->parser->post_db_parse(
															     $this->class->parser->convert( array(
															     								 'TEXT'    => $this->post['post'],
															     								 'CODE'    => $this->class->forum['use_ibc'],
															     								 'SMILIES' => $ibforums->input['enableemo'],
															     								 'HTML'    => $this->class->forum['use_html']
															     						)      )
															        );

			$this->class->output .= $this->class->html->preview( $this->post['post'] );
		}


		$this->class->output .= $this->class->html_start_form( array( 1 => array( 'CODE', '09' ),
																	  2 => array( 't'   , $this->topic['tid']),
																	  3 => array( 'p'   , $ibforums->input['p'] ),
																	  4 => array( 'st'  , $ibforums->input['st'] ),
																	  5 => array( 'post_key', $this->post_key )
															 )       );

		//-----------------------------------------
		// START TABLE
		//-----------------------------------------

		$this->class->output .= $this->class->html->table_structure();

		//-----------------------------------------

		$start_table = $this->class->html->table_top( "{$ibforums->lang['top_txt_edit']} {$this->topic['title']}");

		$name_fields = $this->class->html_name_field();

		$post_box    = $this->class->html_post_body( $raw_post );

		$end_form    = $this->class->html->EndForm( $ibforums->lang['submit_edit'] );

		$post_icons  = $this->class->html_post_icons($this->orig_post['icon_id']);

		if ($this->class->can_upload)
		{
			$upload_field = $this->class->html_build_uploads($this->post_key,'edit',$this->orig_post['pid']);
		}

		if ($ibforums->member['g_append_edit'])
		{
			$checked = "";

			if ($this->orig_post['append_edit'])
			{
				$checked = "checked";
			}

			$edit_option = $this->class->html->add_edit_box($checked);
		}

		//-----------------------------------------

		$this->class->output = str_replace( "<!--START TABLE-->" , $start_table  , $this->class->output );
		$this->class->output = str_replace( "<!--NAME FIELDS-->" , $name_fields  , $this->class->output );
		$this->class->output = str_replace( "<!--POST BOX-->"    , $post_box     , $this->class->output );
		$this->class->output = str_replace( "<!--POST ICONS-->"  , $post_icons   , $this->class->output );
		$this->class->output = str_replace( "<!--END TABLE-->"   , $end_form     , $this->class->output );
		$this->class->output = str_replace( "<!--UPLOAD FIELD-->", $upload_field , $this->class->output );
		$this->class->output = str_replace( "<!--MOD OPTIONS-->" , $edit_option  , $this->class->output );
		$this->class->output = str_replace( "<!--FORUM RULES-->" , $std->print_forum_rules($this->class->forum), $this->class->output );
		$this->class->output = str_replace( "<!--TOPIC TITLE-->" , $topic_title  , $this->class->output );

		//-----------------------------------------

		$this->class->output = $this->class->html_add_smilie_box( $this->class->output );

		//-----------------------------------------
		// Add in siggy buttons and such
		//-----------------------------------------

		$ibforums->input['post_htmlstatus'] = $this->orig_post['post_htmlstate'];

		$this->class->html_checkboxes('edit', $this->topic['tid'], $this->class->forum['id']);

		//-----------------------------------------

		$this->class->html_topic_summary($this->topic['tid']);

		$this->nav = array( "<a href='{$this->class->base_url}&act=SC&c={$this->class->forum['cat_id']}'>{$this->class->forum['cat_name']}</a>",
							"<a href='{$this->class->base_url}&act=SF&f={$this->class->forum['id']}'>{$this->class->forum['name']}</a>",
							"<a href='{$this->class->base_url}&act=ST&f={$this->class->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>",
						  );

		$this->title = $ibforums->lang['editing_post'].' '.$this->topic['title'];

		$print->add_output( $this->class->output );

        $print->do_output( array( 'TITLE'    => $ibforums->vars['board_name']." -> ".$this->title,
        					 	  'JS'       => 1,
        					 	  'NAV'      => $this->class->nav,
        					  ) );

	}


}

?>