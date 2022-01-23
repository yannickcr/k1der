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
|   > Topic Outline Display Module wodule wookie, nookie shut up
|   > Module written by Matt Mecham
|   > Date started: 1st December 2003 (Pinch and a punch!)
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class topic_display {

    var $output         = "";
    var $html           = "";
    var $forum          = array();
    var $topic          = array();
    var $mem_titles     = array();
    var $mod_action     = array();
    var $poll_html      = "";
    var $parser         = "";
    var $mimetypes      = "";
    var $nav_extra      = "";
    var $mod_panel_html = "";
    var $warn_range     = 0;
    var $warn_done      = 0;
    var $pfields        = array();
    var $pfields_dd     = array();
    var $md5_check      = "";
    var $post_count     = 0;
    var $cached_members = array();
    var $pids           = array();
    var $lib            = "";
    var $structured_pids = array();
    var $post_cache      = array();


	/*-------------------------------------------------------------------------*/
	// Register class
	/*-------------------------------------------------------------------------*/

	function register_class($class="")
	{
		$this->lib = &$class;

		$this->topic = $this->lib->topic;
        $this->forum = $this->lib->forum;

        $this->topic['SHOW_PAGES'] = "";
    }

	/*-------------------------------------------------------------------------*/
	//
	// Our constructor, load words, load skin, print the topic listing
	//
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $forums, $DB, $std, $print, $skin_universal;

        //-----------------------------------------
        // Require and init topics.php
        //-----------------------------------------

        require_once( ROOT_PATH.'sources/topics.php' );

        $this->lib = new topics();

        $this->lib->init();
        $this->lib->topic_set_up();

        $this->topic = &$this->lib->topic;
        $this->forum = &$this->lib->forum;

        $this->topic['SHOW_PAGES'] = "";

        //-----------------------------------------
        // Checky checky
        //-----------------------------------------

        if ( ! $this->topic['topic_firstpost'] )
        {
        	$std->boink_it($ibforums->base_url."showtopic=".$this->topic['tid'].'&amp;mode=standard');
        }

        $this->display_topic();

        //-----------------------------------------
		// Print it
		//-----------------------------------------

		$this->topic['id'] = $this->topic['forum_id'];

		$this->output = str_replace( "<!--IBF.FORUM_RULES-->", $std->print_forum_rules($this->topic), $this->output );

		// Pass it to our print routine

		$print->add_output("$this->output");
        $print->do_output( array( 'TITLE'    => $ibforums->vars['board_name']." -> {$this->topic['title']}",
        					 	  'JS'       => 1,
        					 	  'NAV'      => $this->lib->nav,
        				 )      );
	}

	/*-------------------------------------------------------------------------*/
	//
	// Show the damned topic batman
	//
	/*-------------------------------------------------------------------------*/

    function display_topic()
    {
		global $ibforums, $forums, $DB, $std, $print, $skin_universal;

		//-----------------------------------------
		// Grab the posts we'll need
		//-----------------------------------------

		$query_type = 'topics_get_posts';

		$first = intval($ibforums->input['start']);
		$last  = $ibforums->vars['threaded_per_page'] ? $ibforums->vars['threaded_per_page'] : 250;

		if ( $ibforums->vars['custom_profile_topic'] == 1 )
		{
			$query_type = 'topics_get_posts_with_join';
		}

		//-----------------------------------------
		// GET meh pids
		//-----------------------------------------

		if ( $first > 0 )
		{
			// we're on a page, make sure init val is there

			$this->pids[0]                = $this->topic['topic_firstpost'];
			$this->structured_pids[ 0 ][] = $this->topic['topic_firstpost'];
		}

		$DB->simple_construct( array (
									   'select' => 'pid, post_parent',
									   'from'   => 'posts',
									   'where'  => 'topic_id='.$this->topic['tid']. ' and queued != 1',
									   'order'  => 'pid',
									   'limit'  => array( $first, $last )
							)        );

		$DB->simple_exec();

		while( $p = $DB->fetch_row() )
		{
			$this->pids[] = $p['pid'];

			// Force to be children of 'root' post

			if ( ! $p['post_parent'] and $p['pid'] != $this->topic['topic_firstpost'] )
			{
				$p['post_parent'] = $this->topic['topic_firstpost'];
			}

			$this->structured_pids[ $p['post_parent'] ][] = $p['pid'];
		}

		//-----------------------------------------
		// Get post bodah
		//-----------------------------------------

		if ( count( $this->pids ) )
		{
			$DB->simple_construct( array (
											'select' => 'pid, post, author_id, author_name, post_date, post_title, post_parent, topic_id, icon_id',
											'from'   => 'posts',
											'where'  => 'pid IN('.implode(',',$this->pids).')',
											'order'  => 'pid',
								 )        );

			$DB->simple_exec();

			while( $p = $DB->fetch_row() )
			{
				if ( ! $p['post_parent'] and $p['pid'] != $this->topic['topic_firstpost'] )
				{
					$p['post_parent'] = $this->topic['topic_firstpost'];
				}

				$this->post_cache[ $p['pid'] ] = $p;

				$this->last_id = $p['pid'];
			}
		}

		//-----------------------------------------
		// Force root in cache
		//-----------------------------------------

		$this->post_cache[0] = array( 'id' => 1 );

		$this->post_cache[$this->topic['topic_firstpost']]['post_title']  = $this->topic['title'];

		//-----------------------------------------
		// Are we viewing Posts?
		//-----------------------------------------

		$post_id = intval($ibforums->input['pid']);

		$postid_array = array( 1 => $post_id );

		if ( $post_id and $post_id != $this->topic['topic_firstpost'] )
		{
			$parents = $this->post_get_parents( $post_id );

			if ( count($parents) )
			{
				foreach( $parents as $p => $pid )
				{
					if ( $pid != $this->topic['topic_firstpost'] )
					{
						$postid_array[] = $pid;
					}
				}
			}
		}

		if ( count($postid_array) )
		{
			//-----------------------------------------
			// Get root post and children of clicked
			//-----------------------------------------

			$this->used_post_ids = ','.implode( ",", $postid_array ).',';

			$postid_array[0] = $this->topic['topic_firstpost'];

			$DB->cache_add_query( $query_type, array( 'pids' => $postid_array, 'scol' => 'pid', 'sord' => 'asc') );
		}
		else
		{
			//-----------------------------------------
			// Just get root
			//-----------------------------------------

			$DB->cache_add_query( $query_type, array( 'pids' => array( 0 => $this->topic['topic_firstpost'] ) ) );
		}

		//-----------------------------------------
		// Attachment PIDS
		//-----------------------------------------

		$this->lib->attach_pids = $postid_array;

		//-----------------------------------------
		// Render the original post
		//-----------------------------------------

		$this->output .= $this->lib->html->topic_page_top_new_mode( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ) );

		$DB->simple_exec();

		//-----------------------------------------
		// Format and print out the topic list
		//-----------------------------------------

		$num_rows = $DB->get_num_rows();

		while ( $row = $DB->fetch_row() )
		{
			$return = $this->lib->parse_row( $row );

			$poster = $return['poster'];
			$row    = $return['row'];

			//-----------------------------------------
			// Are we giving this bloke a good ignoring?
			//-----------------------------------------

			if ( $ibforums->member['ignored_users'] )
			{
				if ( strstr( $ibforums->member['ignored_users'], ','.$poster['id'].',' ) and $ibforums->input['p'] != $row['pid'] )
				{
					if ( ! strstr( $ibforums->vars['cannot_ignore_groups'], ','.$poster['mgroup'].',' ) )
					{
						$this->output .= $this->lib->html->render_row_hidden( $row, $poster );
						continue;
					}
				}
			}

			$this->output .= $this->lib->html->RenderRow( $row, $poster );

			if ( ! $this->printed and $num_rows > 1 )
			{
				$this->output .= $this->lib->html->topic_end_first_post( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ), 1 );
				$this->printed = 1;
			}
		}

		$this->output .= $this->lib->html->topic_end_outline( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ) );

		//-----------------------------------------
		// Sort out pagination
		//-----------------------------------------

		$total_replies = $this->topic['posts'];
		$show_replies  = count( $this->structured_pids) - 1;

		$this->topic['threaded_pages'] = $std->build_pagelinks( array( 'TOTAL_POSS'  => $total_replies,
																	   'PER_PAGE'    => $last,
																	   'CUR_ST_VAL'  => $ibforums->input['start'],
																	   'L_SINGLE'    => "",
																	   'BASE_URL'    => $ibforums->base_url."showtopic=".$this->topic['tid'],
																	   'USE_ST'      => 'start'
																	 )  );

		//-----------------------------------------
		// START GETTING THE OUTLINE LIST
		//-----------------------------------------

		$this->output .= $this->lib->html->toutline_start_list();

		$this->output .= $this->loop_get_children();

		$this->output .= $this->lib->html->toutline_end_list($this->topic['threaded_pages']);

		$this->output .= $this->lib->html->TableFooter( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ), 1, $std->return_md5_check() );

	}

	//-----------------------------------------
	// Render kiddies
	//-----------------------------------------

	function loop_get_children($root_id=0, $html="" ,$dguide=-1)
	{
		global $DB, $ibforums, $std, $forums;

		$dguide++;

		if ( is_array( $this->structured_pids[ $root_id ] ) )
		{
			if ( count( $this->structured_pids[ $root_id ] ) )
			{
				$html .= $this->lib->html->toutline_start_new_parent();

				foreach( $this->structured_pids[ $root_id ] as $idx => $pid )
				{
					$html .= $this->render_list_row( $this->post_cache[ $pid ], $dguide );

					$html = $this->loop_get_children( $pid, $html, $dguide );
				}

				$html .= $this->lib->html->toutline_end_new_parent();
			}
		}

		return $html;

	}

	//-----------------------------------------
	// Parse row
	//-----------------------------------------

	function render_list_row( $post, $depth=0 )
	{
		global $DB, $ibforums, $std, $forums;

		$post['depthguide'] = "";

		$ibforums->vars['post_showtext_notitle'] = 1;

		for( $i = 1 ; $i < $depth; $i++ )
		{
			$post['depthguide'] .= $this->depth_guide[ $i ];
		}

		// Last child?

		if ( $depth > 0 )
		{
			$last_id = count($this->structured_pids[ $post['post_parent'] ]) - 1;

			if ( $this->structured_pids[ $post['post_parent'] ][$last_id] == $post['pid'] )
			{
				$this->depth_guide[ $depth ] = '<img src="style_images/<#IMG_DIR#>/spacer.gif" width="20" height="16">';
				$post['depthguide'] .= '<img src="style_images/<#IMG_DIR#>/to_post_no_children.gif" />';
			}
			else
			{
				$this->depth_guide[ $depth ] = '<img src="style_images/<#IMG_DIR#>/to_down_pipe.gif">';
				$post['depthguide'] .= '<img src="style_images/<#IMG_DIR#>/to_post_with_children.gif" />';
			}
		}

		if ( ! $post['post_title'] )
		{
			if ( $ibforums->vars['post_showtext_notitle'] )
			{
				$post_text = $this->lib->parser->strip_all_tags( $post['post'] );

				if ( strlen($post_text) > 50 )
				{
					$post['post_title'] = substr( $post_text, 0, 50 ).'...';
					$post['post_title'] = preg_replace( "/&#?(\w+)?;?\.\.\.$/", '...', $post['post_title'] );
				}
				else
				{
					$post['post_title'] = $post_text;
				}

				if ( ! trim($post['post_title']) )
				{
					$post['post_title'] = 'RE: '.$this->topic['title'];
				}
			}
			else
			{
				$post['post_title'] = 'RE: '.$this->topic['title'];
			}
		}


		$post['linked_name'] = $std->make_profile_link( $post['author_name'], $post['author_id'] );

		$post['formatted_date'] = $std->get_date( $post['post_date'], 'LONG' );

		$post['new_post'] = '<img src="style_images/<#IMG_DIR#>/to_post_off.gif" />';

		if ( $post['post_date'] > $this->lib->last_read_tid )
		{
			$post['new_post'] = '<img src="style_images/<#IMG_DIR#>/to_post.gif" />';
		}

		//$post['post_debug'] = "{ID: {$post['pid']}} Last Array Index: $last_id - Last id in tree{$this->structured_pids[ $post['post_parent'] ][$last_id]}, DEPTH: $depth, [parent: {$post['post_parent']}]";

		if ( strstr( $this->used_post_ids, ','.$post['pid'].',' ) )
		{
			return $this->lib->html->toutline_show_row_highlight( $post );
		}
		else
		{
			return $this->lib->html->toutline_show_row( $post );
		}

	}

	//-----------------------------------------
	// Get parents
	//-----------------------------------------

	function post_get_parents($root_id, $ids=array())
	{
		if ( $this->post_cache[ $root_id ]['post_parent'] )
		{
			$ids[] = $this->post_cache[ $root_id ]['post_parent'];

			$ids = $this->post_get_parents( $this->post_cache[ $root_id ]['post_parent'], $ids );
		}

		return $ids;
	}

	//-----------------------------------------
	// Get children
	//-----------------------------------------

	function post_get_children($root_id, $ids=array())
	{
		if ( is_array($this->structured_pids[ $root_id ]) )
		{
			foreach( $this->structured_pids[ $root_id ] as $id => $pid )
			{
				$ids[] = $pid;

				$ids = $this->post_get_children( $pdaid, $ids );
			}
		}

		return $ids;
	}



}

?>