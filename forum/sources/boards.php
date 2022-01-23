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
|   > Board index module
|   > Module written by Matt Mecham
|   > Date started: 17th February 2002
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

class boards {

    var $output   = "";
    var $base_url = "";
    var $html     = "";
    var $forums   = array();
    var $mods     = array();
    var $cats     = array();
    var $children = array();
    var $nav;

    var $news_topic_id = "";
    var $news_forum_id = "";
    var $news_title    = "";
    var $sep_char      = "";
    var $statfunc      = "";

    /*-------------------------------------------------------------------------*/
    // INIT
    /*-------------------------------------------------------------------------*/

    function init()
    {
		global $ibforums, $DB, $std, $forums, $print, $skin_universal;

    	$this->base_url = $ibforums->base_url;

        // Get more words for this invocation!

        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_boards', $ibforums->lang_id);

        $this->html = $std->load_template('skin_boards');
    }

    /*-------------------------------------------------------------------------*/
    // Auto run function
    /*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $forums, $print, $skin_universal;

        $this->init();

        require ROOT_PATH.'sources/lib/boardstats_functions.php';

        $this->statfunc = new boardstats_functions();

       	$this->statfunc->register_class( $this );

        if (! $ibforums->member['id'] )
        {
        	$ibforums->input['last_visit'] = time();
        }

        if ( $ibforums->vars['converge_login_method'] != 'username' )
        {
        	$ibforums->lang['qli_name'] = $ibforums->lang['email_address'];
        }

        $this->output .= $this->html->PageTop( $std->get_date( $ibforums->input['last_visit'], 'LONG' ) );


        //-----------------------------------------
        // What are we doing?
        //-----------------------------------------

        $this->process_all_cats();

        //-----------------------------------------
		// Add in show online users
		//-----------------------------------------

		$stats_html .= $this->statfunc->active_users();

		//-----------------------------------------
		// Are we viewing the calendar?
		//-----------------------------------------

		$stats_html .= $this->statfunc->show_calendar_events();

		//-----------------------------------------
		// Add in show stats
		//-----------------------------------------

		$stats_html .= $this->statfunc->show_totals();

		if ($stats_html != "")
		{
			$collapsed_ids = ','.$std->my_getcookie('collapseprefs').',';

			$show['div_fo'] = 'show';
			$show['div_fc'] = 'none';

			if ( strstr( $collapsed_ids, ',stat,' ) )
			{
				$show['div_fo'] = 'none';
				$show['div_fc'] = 'show';
			}

			$this->output .= $this->html->stats_header($this->statfunc->users_online, $this->statfunc->total_posts, $this->statfunc->total_members, $show);
			$this->output .= $stats_html;
			$this->output .= $this->html->stats_footer();
		}

		//-----------------------------------------
		// Add in board info footer
		//-----------------------------------------

		$this->output .= $this->html->bottom_links();

		//-----------------------------------------
		// Check for news forum.
		//-----------------------------------------

		if ( $forums->forum_by_id[ $ibforums->vars['news_forum_id'] ]['last_id'] and $ibforums->vars['index_news_link'] )
		{
			$t_html = $this->html->newslink( $this->news_forum_id, stripslashes($forums->forum_by_id[ $ibforums->vars['news_forum_id'] ]['last_title']) ,
											 $forums->forum_by_id[ $ibforums->vars['news_forum_id'] ]['last_id']);

			$this->output = str_replace( "<!-- IBF.NEWSLINK -->" , "$t_html" , $this->output );
		}

		//-----------------------------------------
		// Showing who's chatting OLD?
		//-----------------------------------------

		if ( $ibforums->vars['chat_account_no'] and $ibforums->vars['chat_who_on'] )
		{
			require_once( ROOT_PATH.'sources/lib/chat_functions.php' );

			$chat = new chat_functions();

			$chat->register_class( $this );

			$chat_html = $chat->get_online_list();

			$this->output = str_replace( "<!--IBF.WHOSCHATTING-->", $chat_html, $this->output );
		}

		//-----------------------------------------
		// Showing who's chatting NEW?
		//-----------------------------------------

		if ( $ibforums->vars['chat04_account_no'] and $ibforums->vars['chat04_who_on'] )
		{
			require_once( ROOT_PATH.'sources/lib/chat04_functions.php' );

			$chat = new chat_functions();

			$chat->register_class( $this );

			$chat_html = $chat->get_online_list();

			$this->output = str_replace( "<!--IBF.WHOSCHATTING-->", $chat_html, $this->output );
		}

		//-----------------------------------------
		// Print as normal
		//-----------------------------------------

        $print->add_output("$this->output");

        $cp = "";

        if ($ibforums->vars['ips_cp_purchase'])
        {
        	$cp = "";
        }

        $print->do_output( array( 'TITLE' => $ibforums->vars['board_name'].$cp, 'JS' => 0, 'NAV' => $this->nav ) );

	}

    /*-------------------------------------------------------------------------*/
	//
	// Display sub forums
	//
	/*-------------------------------------------------------------------------*/

	function show_subforums($fid)
	{
		global $std, $DB, $ibforums, $forums;

		$this->init();

		//-----------------------------------------
		// Get show / hide cookah
		//-----------------------------------------

		$collapsed_ids = ','.$std->my_getcookie('collapseprefs').',';

        $forums->register_class( $this );

		if ( is_array( $forums->forum_cache[ $fid ] ) )
		{
			$cat_data = $forums->forum_by_id[ $fid ];

			$cat_data['div_fo'] = 'show';
			$cat_data['div_fc'] = 'none';

			if ( strstr( $collapsed_ids, ','.$fid.',' ) and ( $cat_data['sub_can_post'] == 1 ) )
			{
				$cat_data['div_fo'] = 'none';
				$cat_data['div_fc'] = 'show';
			}

			foreach( $forums->forum_cache[ $fid ] as $id => $forum_data )
			{
				//-----------------------------------------
				// Get all subforum stats
				// and calculate
				//-----------------------------------------

				if ( $ibforums->vars['forum_cache_minimum'] )
				{
					$forum_data['description'] = "<!--DESCRIPTION:{$forum_data['id']}-->";
					$need_desc[] = $forum_data['id'];
				}

				if ( $forum_data['redirect_on'] )
				{
					$temp_html .= $this->html->forum_redirect_row( $forum_data );
				}
				else
				{
					$temp_html .= $this->html->ForumRow( $forums->forums_format_lastinfo( $forums->forums_calc_children( $forum_data['id'], $forum_data ) ) );
				}
			}
		}

		if ( $temp_html )
		{
			$sub_output .= $this->html->subheader($cat_data);
			$sub_output .= $temp_html;
			$sub_output .= $this->html->end_this_cat();
		}
		else
		{
			return $sub_output;
		}

		unset($temp_html);

		$sub_output .= $this->html->end_all_cats();

		//-----------------------------------------
        // Get descriptions?
        //-----------------------------------------

        if ( $ibforums->vars['forum_cache_minimum'] and count($need_desc) )
        {
        	$DB->simple_construct( array( 'select' => 'id,description', 'from' => 'forums', 'where' => 'id IN('.implode( ',', $need_desc ) .')' ) );
        	$DB->simple_exec();

        	while( $r = $DB->fetch_row() )
        	{
        		$sub_output = str_replace( "<!--DESCRIPTION:{$r['id']}-->", $r['description'], $sub_output );
        	}
        }

		return $sub_output;
    }

    /*-------------------------------------------------------------------------*/
	//
	// PROCESS ALL CATEGORIES
	//
	/*-------------------------------------------------------------------------*/

	function process_all_cats()
	{
		global $std, $DB, $ibforums, $forums;

		$need_desc = array();
		$root      = array();
		$parent    = array();

		//-----------------------------------------
		// Want to view categories?
		//-----------------------------------------

		if ( $ibforums->input['c'] )
		{
			foreach( explode( ",", $ibforums->input['c'] ) as $c )
			{
				$c = intval( $c );
				$i = $forums->forum_by_id[ $c ]['parent_id'];

				$root[ $i ]   = $i;
				$parent[ $c ] = $c;
			}
		}

		if ( ! count( $root ) )
		{
			$root[] = 'root';
		}

		//-----------------------------------------
		// Get show / hide cookah
		//-----------------------------------------

		$collapsed_ids = ','.$std->my_getcookie('collapseprefs').',';

		$forums->register_class( $this );

		foreach( $root as $root_id )
		{
			if ( is_array( $forums->forum_cache[ $root_id ] ) and count( $forums->forum_cache[ $root_id ] ) )
			{
				foreach( $forums->forum_cache[ $root_id ] as $id => $forum_data )
				{
					//-----------------------------------------
					// Only showing certain root forums?
					//-----------------------------------------

					if ( count( $parent ) )
					{
						if ( ! in_array( $id, $parent ) )
						{
							continue;
						}
					}

					$cat_data = $forum_data;

					$cat_data['div_fo'] = 'show';
					$cat_data['div_fc'] = 'none';

					if ( strstr( $collapsed_ids, ','.$cat_data['id'].',' ) )
					{
						$cat_data['div_fo'] = 'none';
						$cat_data['div_fc'] = 'show';
					}

					if ( is_array( $forums->forum_cache[ $forum_data['id'] ] ) )
					{
						foreach( $forums->forum_cache[ $forum_data['id'] ] as $id => $forum_data )
						{
							//-----------------------------------------
							// Get all subforum stats
							// and calculate
							//-----------------------------------------

							if ( $ibforums->vars['forum_cache_minimum'] )
							{
								$forum_data['description'] = "<!--DESCRIPTION:{$forum_data['id']}-->";
								$need_desc[] = $forum_data['id'];
							}

							if ( $forum_data['redirect_on'] )
							{
								$temp_html .= $this->html->forum_redirect_row( $forum_data );
							}
							else
							{
								$temp_html .= $this->html->ForumRow( $forums->forums_format_lastinfo( $forums->forums_calc_children( $forum_data['id'], $forum_data ) ) );
							}
						}
					}

					if ( $temp_html )
					{
						$this->output .= $this->html->CatHeader_Expanded($cat_data);
						$this->output .= $temp_html;
						$this->output .= $this->html->end_this_cat();
					}

					unset($temp_html);
				}
			}
		}

        $this->output .= $this->html->end_all_cats();

        //-----------------------------------------
        // Get descriptions?
        //-----------------------------------------

        if ( $ibforums->vars['forum_cache_minimum'] and count($need_desc) )
        {
        	$DB->simple_construct( array( 'select' => 'id,description', 'from' => 'forums', 'where' => 'id IN('.implode( ',', $need_desc ) .')' ) );
        	$DB->simple_exec();

        	while( $r = $DB->fetch_row() )
        	{
        		$this->output = str_replace( "<!--DESCRIPTION:{$r['id']}-->", $r['description'], $this->output );
        	}
        }
    }


}

?>