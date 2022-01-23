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
|   > Reply post module
|   > Module written by Matt Mecham
|   > DBA Checked: Fri 21 May 2004
|
+--------------------------------------------------------------------------
*/


class post_functions extends Post {

	var $nav        = array();
	var $title      = "";
	var $post       = array();
	var $topic      = array();
	var $upload     = array();
	var $mod_topic  = array();
	var $class      = "";
	var $m_group    = "";
	var $post_key   = "";
	var $quote_pids = array();
	var $quote_posts = array();

	function post_functions($class)
	{
		global $ibforums, $std, $DB, $forums;

		$this->class = $class;

		$this->post_key = $ibforums->input['post_key'] ? $ibforums->input['post_key'] : md5(microtime());

		//-----------------------------------------
		// Lets load the topic from the database before we do anything else.
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => "forum_id=".intval($this->class->forum['id'])." AND tid=".intval($ibforums->input['t']) ) );
		$DB->simple_exec();

		$this->topic = $DB->fetch_row();

		//-----------------------------------------
		// Check permissions, etc
		//-----------------------------------------

		if (! $this->topic['tid'])
		{
			$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
		}

		//-----------------------------------------
		// Lets do some tests to make sure that we are
		// allowed to reply to this topic
		//-----------------------------------------

		$this->class->check_for_reply($this->topic);

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
			$this->add_reply($class);
		}
	}


	/*-------------------------------------------------------------------------*/
	// ADD THE REPLY
	/*-------------------------------------------------------------------------*/


	function add_reply()
	{
		global $ibforums, $std, $DB, $print;

		//-----------------------------------------
		// Insert the post into the database to get the
		// last inserted value of the auto_increment field
		//-----------------------------------------

		$this->post['topic_id'] = $this->topic['tid'];

		//-----------------------------------------
		// Get the last post time of this topic not counting
		// this new reply
		//-----------------------------------------

		$this->last_post = $this->topic['last_post'];

		//die( $this->last_post .' - '.$ibforums->member['last_activity'] . ' l');

		//-----------------------------------------
		// Are we a mod, and can we change the topic state?
		//-----------------------------------------

		$return_to_move = 0;

		if ( ($ibforums->input['mod_options'] != "") or ($ibforums->input['mod_options'] != 'nowt') )
		{
			if ($ibforums->input['mod_options'] == 'pin')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or $this->class->moderator['pin_topic'] == 1)
				{
					$this->topic['pinned'] = 1;

					$this->class->moderate_log('Pinned topic from post form', $this->topic['title']);
				}
			}
			else if ($ibforums->input['mod_options'] == 'close')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or $this->class->moderator['close_topic'] == 1)
				{
					$this->topic['state'] = 'closed';

					$this->class->moderate_log('Closed topic from post form', $this->topic['title']);
				}
			}
			else if ($ibforums->input['mod_options'] == 'move')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or $this->class->moderator['move_topic'] == 1)
				{
					$return_to_move = 1;
				}
			}
			else if ($ibforums->input['mod_options'] == 'pinclose')
			{
				if ($ibforums->member['g_is_supmod'] == 1 or ( $this->class->moderator['pin_topic'] == 1 AND $this->class->moderator['close_topic'] == 1 ) )
				{
					$this->topic['pinned'] = 1;
					$this->topic['state']  = 'closed';

					$this->class->moderate_log('Pinned & closed topic from post form', $this->topic['title']);
				}
			}
		}

		//-----------------------------------------
		// Add post to DB
		//-----------------------------------------

		$this->post['post_key']    = $this->post_key;
		$this->post['post_parent'] = intval($ibforums->input['parent_id']);

		//-----------------------------------------
		// Typecast
		//-----------------------------------------

		$DB->force_data_type = array( 'pid'  => 'int',
									  'post' => 'string' );

		$DB->do_insert( 'posts', $this->post );

		$this->post['pid'] = $DB->get_insert_id();

		//-----------------------------------------
		// If we are still here, lets update the
		// board/forum/topic stats
		//-----------------------------------------

		$this->class->pf_update_forum_and_stats($this->topic['tid'], $this->topic['title'], 'reply');

		//-----------------------------------------
		// Get the correct number of replies
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'COUNT(*) as posts', 'from' => 'posts', 'where' => "topic_id={$this->topic['tid']} and queued != 1" ) );
		$DB->simple_exec();

		$posts = $DB->fetch_row();

		$pcount = intval( $posts['posts'] - 1 );

		//-----------------------------------------
		// Get the correct number of queued replies
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'COUNT(*) as posts', 'from' => 'posts', 'where' => "topic_id={$this->topic['tid']} and queued=1" ) );
		$DB->simple_exec();

		$qposts = $DB->fetch_row();

		$qpcount = intval( $qposts['posts'] );

		//-----------------------------------------
		// UPDATE TOPIC
		//-----------------------------------------

		$poster_name = $ibforums->member['id'] ? $ibforums->member['name'] : $ibforums->input['UserName'];

		$update_array = array(
							  'posts'			 => $pcount,
							  'topic_queuedposts'=> $qpcount
							 );

		if ( $this->class->obj['moderate'] != 1 and $this->class->obj['moderate'] != 3 )
		{
			$update_array['last_poster_id']   = $ibforums->member['id'];
			$update_array['last_poster_name'] = $poster_name;
			$update_array['last_post']        = time();
			$update_array['pinned']           = $this->topic['pinned'];
			$update_array['state']            = $this->topic['state'];
		}

		$DB->do_update( 'topics', $update_array, "tid={$this->topic['tid']}"  );

		//-----------------------------------------
		// If we are a member, lets update thier last post
		// date and increment their post count.
		//-----------------------------------------

		$this->class->pf_increment_user_post_count();

		//-----------------------------------------
		// Make attachments "permanent"
		//-----------------------------------------

		$this->class->pf_make_attachments_permanent($this->post_key, $this->topic['tid'], $this->post['pid']);

		//-----------------------------------------
		// Moderating?
		//-----------------------------------------

		if ( $this->class->obj['moderate'] == 1 or $this->class->obj['moderate'] == 3 )
		{
			//-----------------------------------------
			// Boing!!!
			//-----------------------------------------

			$this->class->notify_new_topic_approval( $this->topic['tid'], $this->topic['title'], $this->topic['starter_name'], $this->post['pid'], 'reply' );

			$page = floor( ($this->topic['posts'] + 1) / $ibforums->vars['display_max_posts']);
			$page = $page * $ibforums->vars['display_max_posts'];

			$print->redirect_screen( $ibforums->lang['moderate_post'], "showtopic={$this->topic['tid']}&st=$page" );
		}

		//-----------------------------------------
		// Are we tracking topics we reply in 'auto_track'?
		//-----------------------------------------

		$this->class->pf_add_tracked_topic($this->topic['tid'], 1);

		//-----------------------------------------
		// Check for subscribed topics
		// Pass on the previous last post time of the topic
		// to see if we need to send emails out
		//-----------------------------------------

		$this->class->topic_tracker( $this->topic['tid'], $this->post['post'], $poster_name, $this->last_post );

		//-----------------------------------------
		// Redirect them back to the topic
		//-----------------------------------------

		if ($return_to_move == 1)
		{
			$std->boink_it($this->class->base_url."act=Mod&CODE=02&f={$this->class->forum['id']}&t={$this->topic['tid']}");
		}
		else
		{
			$page = floor( ($this->topic['posts'] + 1) / $ibforums->vars['display_max_posts']);
			$page = $page * $ibforums->vars['display_max_posts'];
			$std->boink_it($ibforums->base_url."showtopic={$this->topic['tid']}&st=$page&p={$this->post['pid']}&#entry{$this->post['pid']}");
		}

	}


	/*-------------------------------------------------------------------------*/
	// SHOW FORM
	/*-------------------------------------------------------------------------*/


	function show_form()
	{
		global $ibforums, $std, $DB, $print, $forums;

		//-----------------------------------------
		// Are we quoting posts?
		//-----------------------------------------

		$raw_post = $this->class->check_multi_quote();

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



		$this->class->output .= $this->class->html_start_form( array( 1 => array( 'CODE'     , '03' ),
														              2 => array( 't'        , $this->topic['tid'] ),
														              3 => array( 'post_key' , $this->post_key     ),
														              4 => array( 'parent_id', $ibforums->input['parent_id'] ),
														     )      );

		//-----------------------------------------
		// START TABLE
		//-----------------------------------------

		$this->class->output .= $this->class->html->table_structure();

		//-----------------------------------------

		$start_table = $this->class->html->table_top( "{$ibforums->lang['top_txt_reply']} {$this->topic['title']}");

		$name_fields = $this->class->html_name_field();

		$post_box    = $this->class->html_post_body( $raw_post );

		$mod_options = $this->class->mod_options(1);

		$end_form    = $this->class->html->EndForm( $ibforums->lang['submit_reply'] );

		$post_icons  = $this->class->html_post_icons();

		if ($this->class->can_upload)
		{
			$upload_field = $this->class->html_build_uploads($this->post_key,'reply');
		}

		//-----------------------------------------

		$this->class->output = str_replace( "<!--START TABLE-->" , $start_table  , $this->class->output );
		$this->class->output = str_replace( "<!--NAME FIELDS-->" , $name_fields  , $this->class->output );
		$this->class->output = str_replace( "<!--POST BOX-->"    , $post_box     , $this->class->output );
		$this->class->output = str_replace( "<!--POST ICONS-->"  , $post_icons   , $this->class->output );
		$this->class->output = str_replace( "<!--UPLOAD FIELD-->", $upload_field , $this->class->output );
		$this->class->output = str_replace( "<!--MOD OPTIONS-->" , $mod_options  , $this->class->output );
		$this->class->output = str_replace( "<!--END TABLE-->"   , $end_form     , $this->class->output );
		$this->class->output = str_replace( "<!--FORUM RULES-->" , $std->print_forum_rules($this->class->forum), $this->class->output );

		//-----------------------------------------

		$this->class->output = $this->class->html_add_smilie_box( $this->class->output );

		//-----------------------------------------
		// Add in siggy buttons and such
		//-----------------------------------------

		$this->class->html_checkboxes('reply', $this->topic['tid'], $this->class->forum['id']);

		//-----------------------------------------

		$this->class->html_topic_summary($this->topic['tid']);

		$this->nav = array( "<a href='{$this->class->base_url}act=SC&amp;c={$this->class->forum[cat_id]}'>{$this->class->forum['cat_name']}</a>",
							"<a href='{$this->class->base_url}showforum={$this->class->forum['id']}'>{$this->class->forum['name']}</a>",
							"<a href='{$this->class->base_url}showtopic={$this->topic['tid']}'>{$this->topic['title']}</a>",
						  );

		$this->title = $ibforums->lang['replying_in'].' '.$this->topic['title'];

		$print->add_output( $this->class->output );

        $print->do_output( array( 'TITLE'    => $ibforums->vars['board_name']." -> ".$this->title,
        					 	  'JS'       => 1,
        					 	  'NAV'      => $this->class->nav,
        			     )      );

	}


}

?>