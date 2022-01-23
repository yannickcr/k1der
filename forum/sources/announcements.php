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
|   > Announcements module
|   > Module written by Matt Mecham
|   > Date started: 29th March 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class announcements
{
    /*-------------------------------------------------------------------------*/
    // CONSTRUCTOR
    /*-------------------------------------------------------------------------*/

    function announcements()
    {

    }

    /*-------------------------------------------------------------------------*/
    // AUTO RUN
    /*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $forums, $print;

        $ibforums->input['id'] = intval($ibforums->input['id']);
        $ibforums->input['f']  = intval($ibforums->input['f']);

        if ( ! $ibforums->input['id'] and ! $ibforums->input['f'] )
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
		// Get the announcement
		//-----------------------------------------

		$DB->cache_add_query( 'ucp_get_all_announcements_byid', array( 'id' => $ibforums->input['id'] ) );
		$DB->cache_exec_query();

		$announce = $DB->fetch_row();

		if ( ! $announce['announce_id'] or ! $announce['announce_forum'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
		// Permission to see it?
		//-----------------------------------------

		$pass = 0;

		if ( $announce['announce_forum'] == '*' )
		{
			$pass = 1;
		}
		else
		{
			$tmp = explode( ",", $announce['announce_forum'] );

			if ( ! is_array( $tmp ) and ! ( count( $tmp ) ) )
			{
				$pass = 0;
			}
			else
			{
				foreach( $tmp as $id )
				{
					if ( $forums->forum_by_id[ $id ]['id'] )
					{
						$pass = 1;
						break;
					}
				}
			}
		}

		if ( $pass != 1 )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }

        //-----------------------------------------
        // Mkay, get the require libraries
        //-----------------------------------------

        require_once( ROOT_PATH.'sources/lib/post_parser.php' );
        $parser = new post_parser();

        require_once( ROOT_PATH.'sources/topics.php' );
        $topic = new topics();

        $topic->topic_init();

    	//-----------------------------------------
    	// Parsey parsey!
    	//-----------------------------------------

        $member = $topic->parse_member( $announce );

        //-----------------------------------------
		// Parse HTML tag on the fly
		//-----------------------------------------

		$announce['announce_post'] = $parser->convert( array( 'TEXT'    => $announce['announce_post'],
															  'SMILIES' => 1,
															  'CODE'    => 1,
													 )      );


		$parser->pp_do_html  = $announce['announce_html_enabled'];
		$parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
		$parser->pp_nl2br    = 1;

		$announce['announce_post'] = $parser->post_db_parse( $announce['announce_post'] );

		if ( $announce['announce_start'] and $announce['announce_end'] )
		{
			$announce['running_date'] = sprintf( $ibforums->lang['announce_both'], gmdate( 'jS F Y', $announce['announce_start'] ), gmdate( 'jS F Y', $announce['announce_end'] ) );
		}
		else if ( $announce['announce_start'] and ! $announce['announce_end'] )
		{
			$announce['running_date'] = sprintf( $ibforums->lang['announce_start'], gmdate( 'jS F Y', $announce['announce_start'] ) );
		}
		else if ( ! $announce['announce_start'] and $announce['announce_end'] )
		{
			$announce['running_date'] = sprintf( $ibforums->lang['announce_end'], gmdate( 'jS F Y', $announce['announce_end'] ) );
		}
		else
		{
			$announce['running_date'] = '';
		}

		$this->output = $topic->html->announcement_show($announce, $member);

		//-----------------------------------------
		// Show
		//-----------------------------------------

		$this->nav = $forums->forums_breadcrumb_nav( $ibforums->input['f'] );

		//-----------------------------------------
		// Update hits
		//-----------------------------------------

		$DB->simple_construct( array( 'update' => 'announcements', 'set' => 'announce_views=announce_views+1', 'where' => "announce_id=".$ibforums->input['id'] ) );
		$DB->simple_shutdown_exec();

		$print->add_output( $this->output );
        $print->do_output( array( 'TITLE'    => $ibforums->vars['board_name']." -> ".$forums->forum_by_id[ $ibforums->input['f'] ]['name'],
        					 	  'JS'       => 0,
        					 	  'NAV'      => $this->nav,
        				 )      );

    }

    /*-------------------------------------------------------------------------*/
    // REBUILD
    /*-------------------------------------------------------------------------*/

    function announce_retire_expired()
    {
		global $ibforums, $DB, $std;

    	//-----------------------------------------
    	// Update all out of date 'uns
    	//-----------------------------------------

    	$DB->do_update( 'announcements', array( 'announce_active' => 0 ), 'announce_end != 0 AND announce_end < '.time() );

    	$this->announce_recache();
    }

    /*-------------------------------------------------------------------------*/
    // REBUILD
    /*-------------------------------------------------------------------------*/

    function announce_recache()
    {
		global $ibforums, $DB, $std;

    	$ibforums->cache['announcements'] = array();

    	$DB->cache_add_query( 'ucp_get_all_announcements', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$start_ok = 0;
			$end_ok   = 0;

			if ( ! $r['announce_active'] )
			{
				continue;
			}

			if ( ! $r['announce_start'] )
			{
				$start_ok = 1;
			}
			else if ( $r['announce_start'] < time() )
			{
				$start_ok = 1;
			}

			if ( ! $r['announce_end'] )
			{
				$end_ok = 1;
			}
			else if ( $r['announce_end'] > time() )
			{
				$end_ok = 1;
			}

			if ( $start_ok and $end_ok )
			{
				$ibforums->cache['announcements'][ $r['announce_id'] ] = array( 'announce_id'    => $r['announce_id'],
																				'announce_title' => $r['announce_title'],
																				'announce_start' => $r['announce_start'],
																				'announce_end'   => $r['announce_end'],
																				'announce_forum' => $r['announce_forum'],
																				'announce_views' => $r['announce_views'],
																				'member_id'      => $r['id'],
																				'member_name'    => $r['name']
																			  );
			}
		}

		$DB->obj['use_shutdown'] = 0;
		$std->update_cache( array( 'name' => 'announcements', 'array' => 1, 'deletefirst' => 1 ) );
    }


}

?>