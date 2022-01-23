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
|   > New Post module
|   > Module written by Matt Mecham
|   > Date started: 17th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/


class post_functions extends Post
{
	var $nav = array();
	var $title     = "";
	var $post      = array();
	var $topic     = array();
	var $upload    = array();
	var $mod_topic = array();
	var $class     = "";
	var $m_group   = "";
	var $post_key  = "";

	function post_functions($class)
	{
		global $ibforums, $std, $DB;

		//-----------------------------------------
		// Check permissions
		//-----------------------------------------

		$this->post_key = $ibforums->input['post_key'] ? $ibforums->input['post_key'] : md5(microtime());

		$this->class = $class;

		$this->class->check_for_new_topic();

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
		//-----------------------------------------

		$this->post = $this->class->compile_post();

		//-----------------------------------------
		// check to make sure we have a valid topic title
		//-----------------------------------------

		$ibforums->input['TopicTitle'] = str_replace( "<br />", "", $ibforums->input['TopicTitle'] );

		$ibforums->input['TopicTitle'] = trim($ibforums->input['TopicTitle']);

		//-----------------------------------------
		// More unicode..
		//-----------------------------------------

		$temp = $std->txt_stripslashes($_POST['TopicTitle']);

		$temp = preg_replace("/&#([0-9]+);/", "-", $temp );

		if ( strlen($temp) > 64 or strlen($ibforums->input['TopicTitle']) > 250 )
		{
			$this->class->obj['post_errors'] = 'topic_title_long';
		}

		if ( (strlen($temp) < 2) or (!$ibforums->input['TopicTitle'])  )
		{
			$this->class->obj['post_errors'] = 'no_topic_title';
		}

		//-----------------------------------------
		// If we don't have any errors yet, parse the upload
		//-----------------------------------------

		if ($this->class->obj['post_errors'] == "")
		{
			$this->upload = $this->class->process_upload();
		}


		if ( ($this->class->obj['post_errors'] != "") or ($this->class->obj['preview_post'] != "") )
		{
			//-----------------------------------------
			// Show the form again
			//-----------------------------------------

			$this->show_form();
		}
		else
		{
			$this->add_new_topic();
		}
	}

	/*-------------------------------------------------------------------------*/
	// ADD TOPIC FUNCTION
	/*-------------------------------------------------------------------------*/

	function add_new_topic()
	{
		global $ibforums, $std, $DB, $print;

		//-----------------------------------------
		// Fix up the topic title
		//-----------------------------------------

		$ibforums->input['TopicTitle'] = $this->class->pf_clean_topic_title( $ibforums->input['TopicTitle'] );

		$ibforums->input['TopicTitle'] = $this->class->parser->bad_words( $ibforums->input['TopicTitle'] );
		$ibforums->input['TopicDesc']  = $this->class->parser->bad_words( $ibforums->input['TopicDesc']  );

		$pinned = 0;
		$state  = 'open';

		if ( ($ibforums->input['mod_options'] != "") or ($ibforums->input['mod_options'] != 'nowt') )
		{
			if ($ibforums->input['mod_options'] == 'pin')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or $this->class->moderator['pin_topic'] == 1)
				{
					$pinned = 1;

					$this->class->moderate_log('Pinned topic from post form', $ibforums->input['TopicTitle']);
				}
			}
			else if ($ibforums->input['mod_options'] == 'close')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or $this->class->moderator['close_topic'] == 1)
				{
					$state = 'closed';

					$this->class->moderate_log('Closed topic from post form', $ibforums->input['TopicTitle']);
				}
			}
			else if ($ibforums->input['mod_options'] == 'pinclose')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or ( $this->class->moderator['pin_topic'] == 1 AND $this->class->moderator['close_topic'] == 1 ) )
				{
					$pinned = 1;
					$state = 'closed';

					$this->class->moderate_log('Pinned & closed topic from post form', $ibforums->input['TopicTitle']);
				}
			}
		}

		//-----------------------------------------
		// Build the master array
		//-----------------------------------------

		$this->topic = array(
							  'title'            => $ibforums->input['TopicTitle'],
							  'description'      => $ibforums->input['TopicDesc'] ,
							  'state'            => $state,
							  'posts'            => 0,
							  'starter_id'       => $ibforums->member['id'],
							  'starter_name'     => $ibforums->member['id'] ?  $ibforums->member['name'] : $ibforums->input['UserName'],
							  'start_date'       => time(),
							  'last_poster_id'   => $ibforums->member['id'],
							  'last_poster_name' => $ibforums->member['id'] ?  $ibforums->member['name'] : $ibforums->input['UserName'],
							  'last_post'        => time(),
							  'icon_id'          => $ibforums->input['iconid'],
							  'author_mode'      => $ibforums->member['id'] ? 1 : 0,
							  'poll_state'       => 0,
							  'last_vote'        => 0,
							  'views'            => 0,
							  'forum_id'         => $this->class->forum['id'],
							  'approved'         => ( $this->class->obj['moderate'] == 1 || $this->class->obj['moderate'] == 2 ) ? 0 : 1,
							  'pinned'           => $pinned,
							 );


		//-----------------------------------------
		// Insert the topic into the database to get the
		// last inserted value of the auto_increment field
		// follow suit with the post
		//-----------------------------------------

		$DB->do_insert( 'topics', $this->topic );

		$this->post['topic_id']  = $DB->get_insert_id();
		$this->topic['tid']      = $this->post['topic_id'];

		//-----------------------------------------
		// Update the post info with the upload array info
		//-----------------------------------------

		$this->post['post_key']  = $this->post_key;
		$this->post['new_topic'] = 1;

		//-----------------------------------------
		// Unqueue the post if we're starting a new topic
		//-----------------------------------------

		$this->post['queued'] = 0;

		//-----------------------------------------
		// Add post to DB
		//-----------------------------------------

		$DB->do_insert( 'posts', $this->post );

		$this->post['pid'] = $DB->get_insert_id();

		//-----------------------------------------
		// Update topic with firstpost ID
		//-----------------------------------------

		$DB->simple_construct( array( 'update' => 'topics',
									  'set'    => "topic_firstpost=".$this->post['pid'],
									  'where'  => "tid=".$this->topic['tid']
							 )      );

		$DB->simple_exec();

		//-----------------------------------------
		// If we are still here, lets update the
		// board/forum stats
		//-----------------------------------------

		$this->class->pf_update_forum_and_stats($this->topic['tid'], $this->topic['title'], 'new');

		//-----------------------------------------
		// Make attachments "permanent"
		//-----------------------------------------

		$this->class->pf_make_attachments_permanent($this->post_key, $this->topic['tid'], $this->post['pid']);

		//-----------------------------------------
		// If we are a member, lets update thier last post
		// date and increment their post count.
		//-----------------------------------------

		$this->class->pf_increment_user_post_count();

		//-----------------------------------------
		// Moderating?
		//-----------------------------------------

		if ( $this->class->obj['moderate'] == 1 OR $this->class->obj['moderate'] == 2 )
		{
			//-----------------------------------------
			// Redirect them with a message telling them the
			// post has to be previewed first
			//-----------------------------------------

			$this->class->notify_new_topic_approval( $this->topic['tid'], $this->topic['title'], $this->topic['starter_name'], $this->post['pid'] );

			$print->redirect_screen( $ibforums->lang['moderate_topic'], "act=SF&f={$this->class->forum['id']}" );
		}

		//-----------------------------------------
		// Are we tracking new topics we start 'auto_track'?
		//-----------------------------------------

		$this->class->pf_add_tracked_topic($this->topic['tid']);

		//-----------------------------------------
		// Are we tracking this forum? If so generate some mailies - yay!
		//-----------------------------------------

		$this->class->forum_tracker($this->class->forum['id'], $this->topic['tid'], $this->topic['title'], $this->class->forum['name'], $this->post['post'] );

		//-----------------------------------------
		// Redirect them back to the topic
		//-----------------------------------------

		$std->boink_it($this->class->base_url."showtopic={$this->topic['tid']}");

	}

	/*-------------------------------------------------------------------------*/
	// SHOW FORM
	/*-------------------------------------------------------------------------*/

	function show_form()
	{
		global $ibforums, $std, $DB, $print;

		//-----------------------------------------
		// Are we quoting posts?
		//-----------------------------------------

		$raw_post = $this->class->check_multi_quote();

		//-----------------------------------------
		// Sort out the "raw" textarea input and make it safe incase
		// we have a <textarea> tag in the raw post var.
		//-----------------------------------------

		$topic_title = isset($_POST['TopicTitle']) ? $ibforums->input['TopicTitle'] : "";
		$topic_desc  = isset($_POST['TopicDesc'])  ? $ibforums->input['TopicDesc']  : "";

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

		$this->class->output .= $this->class->html_start_form( array( 1 => array( 'CODE', '01' ),
														  			  2 => array( 'post_key', $this->post_key ),
											     			 )      );

		//-----------------------------------------
		// START TABLE
		//-----------------------------------------

		$this->class->output .= $this->class->html->table_structure();

		//-----------------------------------------

		$topic_title = $this->class->html->topictitle_fields( array( 'TITLE' => $topic_title, 'DESC' => $topic_desc ) );

		$start_table = $this->class->html->table_top( "{$ibforums->lang['top_txt_new']} {$this->class->forum['name']}");

		$name_fields = $this->class->html_name_field();

		$post_box    = $this->class->html_post_body( $raw_post );

		$mod_options = $this->class->mod_options();

		$end_form    = $this->class->html->EndForm( $ibforums->lang['submit_new'] );

		$post_icons  = $this->class->html_post_icons();

		if ($this->class->can_upload)
		{
			$upload_field = $this->class->html_build_uploads($this->post_key,'new');
		}

		//-----------------------------------------

		$this->class->output = str_replace( "<!--START TABLE-->" , $start_table  , $this->class->output );
		$this->class->output = str_replace( "<!--NAME FIELDS-->" , $name_fields  , $this->class->output );
		$this->class->output = str_replace( "<!--POST BOX-->"    , $post_box     , $this->class->output );
		$this->class->output = str_replace( "<!--POST ICONS-->"  , $post_icons   , $this->class->output );
		$this->class->output = str_replace( "<!--UPLOAD FIELD-->", $upload_field , $this->class->output );
		$this->class->output = str_replace( "<!--MOD OPTIONS-->" , $mod_options  , $this->class->output );
		$this->class->output = str_replace( "<!--END TABLE-->"   , $end_form     , $this->class->output );
		$this->class->output = str_replace( "<!--TOPIC TITLE-->" , $topic_title  , $this->class->output );
		$this->class->output = str_replace( "<!--FORUM RULES-->" , $std->print_forum_rules($this->class->forum), $this->class->output );

		//-----------------------------------------

		$this->class->output = $this->class->html_add_smilie_box( $this->class->output );

		//-----------------------------------------
		// Add in siggy buttons and such
		//-----------------------------------------

		$this->class->html_checkboxes('new', 0, $this->class->forum['id']);

		//-----------------------------------------

		$this->title = $ibforums->lang['posting_new_topic'];

		$print->add_output( $this->class->output );
        $print->do_output( array( 'TITLE'    => $ibforums->vars['board_name']." -> ".$this->title,
        					 	  'JS'       => 1,
        					 	  'NAV'      => $this->class->nav,
        				 )     );

	}


}

?>