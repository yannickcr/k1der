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
|   > Topic display module
|   > Module written by Matt Mecham
|   > Date started: 18th February 2002
|
|	> Module Version Number: 1.1.0
|   > DBA Checked: Fri 21 May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class topic_display {

    var $output    = "";
    var $base_url  = "";
    var $html      = "";
    var $moderator = array();
    var $forum     = array();
    var $topic     = array();
    var $mem_titles = array();
    var $mod_action = array();
    var $poll_html  = "";
    var $parser     = "";
    var $mimetypes  = "";
    var $nav_extra  = "";
    var $read_array = array();
    var $mod_panel_html = "";
    var $warn_range = 0;
    var $warn_done  = 0;
    var $pfields    = array();
    var $pfields_dd = array();
    var $md5_check  = "";
    var $post_count  = 0;
    var $cached_members = array();
    var $first_printed  = 0;
    var $pids           = array();

    /*-------------------------------------------------------------------------*/
	// Register class
	/*-------------------------------------------------------------------------*/

	function register_class($class="")
	{
		$this->lib = &$class;

		$this->topic = $this->lib->topic;
        $this->forum = $this->lib->forum;
    }

    /*-------------------------------------------------------------------------*/
	//
	// Our constructor, load words, load skin, print the topic listing
	//
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $forums, $DB, $std, $print, $skin_universal;

        require_once( ROOT_PATH.'sources/topics.php' );

        $this->lib = new topics();

        $this->lib->init();
        $this->lib->topic_set_up();

        $this->topic = &$this->lib->topic;
        $this->forum = &$this->lib->forum;

        //-----------------------------------------
        // Checky checky
        //-----------------------------------------

        if ( ! $this->topic['topic_firstpost'] )
        {
        	$std->boink_it($ibforums->base_url."showtopic=".$this->topic['tid'].'&amp;mode=standard');
        }

        //-----------------------------------------
		// Print it
		//-----------------------------------------

		$this->output = str_replace( "<!--IBF.MOD_PANEL-->", $this->lib->moderation_panel(), $this->output );

		// Enable quick reply box?

		if (   ( $this->topic['quick_reply'] == 1 )
		   and ( $std->check_perms( $this->topic['reply_perms']) == TRUE )
		   and ( $this->topic['state'] != 'closed' ) )
		{
			$show = "none";

			$sqr = $std->my_getcookie("open_qr");

			if ( $sqr == 1 )
			{
				$show = "show";
			}
			$this->output = str_replace( "<!--IBF.QUICK_REPLY_CLOSED-->", $this->lib->html->quick_reply_box_closed(), $this->output );
			$this->output = str_replace( "<!--IBF.QUICK_REPLY_OPEN-->"  , $this->lib->html->quick_reply_box_open($this->topic['forum_id'], $this->topic['tid'], $show, $this->md5_check), $this->output );
		}

		$this->output = str_replace( "<!--IBF.TOPIC_OPTIONS_CLOSED-->", $this->lib->html->topic_opts_closed(), $this->output );
		$this->output = str_replace( "<!--IBF.TOPIC_OPTIONS_OPEN-->"  , $this->lib->html->topic_opts_open($this->topic['forum_id'], $this->topic['tid']), $this->output );

		$this->topic['id'] = $this->topic['forum_id'];

		$this->output = str_replace( "<!--IBF.FORUM_RULES-->", $std->print_forum_rules($this->topic), $this->output );

		//-----------------------------------------
		// Topic multi-moderation - yay!
		//-----------------------------------------

		$this->output = str_replace( "<!--IBF.MULTIMOD-->", $this->lib->multi_moderation(), $this->output );

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
		global $ibforums, $forums, $DB, $std, $print;

		//-----------------------------------------
		// Grab the posts we'll need
		//-----------------------------------------

		$first = intval($ibforums->input['st']);

		$query_type = 'topics_get_posts';

		if ( $ibforums->vars['post_order_column'] != 'post_date' )
		{
			$ibforums->vars['post_order_column'] = 'pid';
		}

		if ( $ibforums->vars['post_order_sort'] != 'desc' )
		{
			$ibforums->vars['post_order_sort'] = 'asc';
		}

		if ($ibforums->vars['au_cutoff'] == "")
		{
			$ibforums->vars['au_cutoff'] = 15;
		}

		if ( $ibforums->vars['custom_profile_topic'] == 1 )
		{
			$query_type = 'topics_get_posts_with_join';
		}

		//-----------------------------------------
		// Moderator?
		//-----------------------------------------

		$queued_query_bit = ' and queued=0';

		if ( $std->can_queue_posts($this->topic['forum_id']) )
		{
			$queued_query_bit = '';

			if ( $ibforums->input['modfilter'] == 'invisible_posts' )
			{
				$queued_query_bit = ' and queued=1';
			}
		}

		//-----------------------------------------
		// Using "new" mode?
		//-----------------------------------------

		if ( $this->lib->topic_view_mode == 'linearplus' and $this->topic['topic_firstpost'] )
		{
			$this->topic['new_mode_start'] = $first + 1;

			if ( $first )
			{
				$this->topic['new_mode_start']--;
			}

			if ( $first + $ibforums->vars['display_max_posts'] > ( $this->topic['posts'] + 1 ) )
			{
				$this->topic['new_mode_end'] = $this->topic['posts'];
			}
			else
			{
				$this->topic['new_mode_end'] = $first + ($ibforums->vars['display_max_posts'] - 1);
			}

			if ( $first )
			{
				$this->pids = array( 0 => $this->topic['topic_firstpost'] );
			}

			//-----------------------------------------
			// Get PIDS of this page/topic
			//-----------------------------------------

			$DB->simple_construct( array (
										   'select' => 'pid',
										   'from'   => 'posts',
										   'where'  => 'topic_id='.$this->topic['tid']. $queued_query_bit,
										   'order'  => 'pid',
										   'limit'  => array( $first, $ibforums->vars['display_max_posts'] )
								)        );

			$DB->simple_exec();

			while( $p = $DB->fetch_row() )
			{
				$this->pids[] = $p['pid'];
			}
		}
		else
		{
			//-----------------------------------------
			// Run query
			//-----------------------------------------

			$this->lib->topic_view_mode = 'linear';

			$DB->simple_construct( array (
										   'select' => 'pid',
										   'from'   => 'posts',
										   'where'  => 'topic_id='.$this->topic['tid']. $queued_query_bit,
										   'order'  => $ibforums->vars['post_order_column'].' '.$ibforums->vars['post_order_sort'],
										   'limit'  => array( $first, $ibforums->vars['display_max_posts'] )
								)        );

			$DB->simple_exec();

			while( $p = $DB->fetch_row() )
			{
				$this->pids[] = $p['pid'];
			}
		}

		//-----------------------------------------
		// Do we have any PIDS?
		//-----------------------------------------

		if ( ! count( $this->pids ) )
		{
			if ( $first )
			{
				//-----------------------------------------
				// Add dummy PID, AUTO FIX
				// will catch this below...
				//-----------------------------------------

				$this->pids[] = 0;
			}

			if ( $ibforums->input['modfilter'] == 'invisible_posts' )
			{
				$this->pids[] = 0;
			}
		}

		//-----------------------------------------
		// Attachment PIDS
		//-----------------------------------------

		$this->lib->attach_pids = $this->pids;

		//-----------------------------------------
		// Fail safe
		//-----------------------------------------

		if ( ! is_array( $this->pids ) or ! count( $this->pids ) )
		{
			$this->pids = array( 0 => 0 );
		}

		//-----------------------------------------
		// Get posts
		//-----------------------------------------

		$DB->cache_add_query( $query_type, array( 'pids' => $this->pids, 'scol' => $ibforums->vars['post_order_column'], 'sord' => $ibforums->vars['post_order_sort'] ) );

		$oq = $DB->simple_exec();

		if ( ! $DB->get_num_rows() )
		{
			if ($first >= $ibforums->vars['display_max_posts'])
			{
				//-----------------------------------------
				// AUTO FIX: Get the correct number of replies...
				//-----------------------------------------

				$DB->simple_construct( array(
											 'select' => 'COUNT(*) as pcount',
											 'from'   => 'posts',
											 'where'  => "topic_id=".$this->topic['tid']." and queued !=1"
									 )      );

				$newq   = $DB->simple_exec();

				$pcount = $DB->fetch_row($newq);

				$pcount['pcount'] = $pcount['pcount'] > 0 ? $pcount['pcount'] - 1 : 0;

				//-----------------------------------------
				// Update the post table...
				//-----------------------------------------

				if ($pcount['pcount'] > 1)
				{
					$DB->simple_construct( array(
											 'update' => 'topics',
											 'set'    => "posts=".$pcount['pcount'],
											 'where'  => "tid=".$this->topic['tid']
									 )      );

					$DB->simple_exec();

				}

				$std->boink_it($ibforums->base_url."act=ST&f={$this->forum['id']}&t={$this->topic['tid']}&view=getlastpost");
			}
		}

		//-----------------------------------------
		// Render the page top
		//-----------------------------------------

		if ( $this->lib->topic_view_mode == 'linearplus' and $this->topic['posts'] > 0 )
		{
			$this->output .= $this->lib->html->topic_page_top_new_mode( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ) );
		}
		else
		{
			$this->output .= $this->lib->html->topic_page_top_classic( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ) );
		}

		//-----------------------------------------
		// Format and print out the topic list
		//-----------------------------------------

		while ( $row = $DB->fetch_row( $oq ) )
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

			//-----------------------------------------
			// Show end first post
			//-----------------------------------------

			if ( $this->lib->topic_view_mode == 'linearplus' and $this->first_printed == 0 and $row['pid'] == $this->topic['topic_firstpost'] and $this->topic['posts'] > 0)
			{
				$this->output .= $this->lib->html->topic_end_first_post( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ) );
			}

		}

		//-----------------------------------------
		// Print the footer
		//-----------------------------------------

		$this->output .= $this->lib->html->TableFooter( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum ), 0, $std->return_md5_check() );

	}

}

?>