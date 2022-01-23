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
|   > Topic display in printable format module
|   > Module written by Matt Mecham
|   > Date started: 25th March 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class printpage {

    var $output    = "";
    var $base_url  = "";
    var $html      = "";
    var $moderator = array();
    var $forum     = array();
    var $topic     = array();
    var $category  = array();
    var $mem_groups = array();
    var $mem_titles = array();
    var $mod_action = array();
    var $poll_html  = "";
    var $parser     = "";

    /*-------------------------------------------------------------------------*/
	//
	// Our constructor, load words, load skin, print the topic listing
	//
	/*-------------------------------------------------------------------------*/

    function auto_run()
    {
		global $ibforums, $DB, $std, $print, $forums;

        //-----------------------------------------
		// Compile the language file
		//-----------------------------------------

        $ibforums->lang      = $std->load_words($ibforums->lang, 'lang_printpage', $ibforums->lang_id);

        $this->html          = $std->load_template('skin_printpage');

        require ROOT_PATH."sources/lib/post_parser.php";

        $this->parser = new post_parser();

        //-----------------------------------------
        // Check the input
        //-----------------------------------------

        $ibforums->input['t'] = intval($ibforums->input['t']);
        $ibforums->input['f'] = intval($ibforums->input['f']);

        if ( ! $ibforums->input['t'] or ! $ibforums->input['f'] )
        {
            $std->Error( array( LEVEL => 1, MSG => 'missing_files') );
        }

        //-----------------------------------------
        // Get the forum info based on the
        // forum ID, get the category name, ID,
        // and get the topic details
        //-----------------------------------------

        $DB->simple_construct( array( 'select' => '*', 'from' => 'topics', 'where' => "tid=".$ibforums->input['t'] ) );
		$DB->simple_exec();

        $this->topic = $DB->fetch_row();

        $this->forum = $forums->forum_by_id[ $this->topic['forum_id'] ];

        //-----------------------------------------
        // Error out if we can not find the forum
        //-----------------------------------------

        if ( ! $this->forum['id'])
        {
        	$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
        }

        //-----------------------------------------
        // Error out if we can not find the topic
        //-----------------------------------------

        if (!$this->topic['tid'])
        {
        	$std->Error( array( LEVEL => 1, MSG => 'missing_files') );
        }

        //-----------------------------------------
        // Check viewing permissions, private forums,
        // password forums, etc
        //-----------------------------------------

        if ( (!$this->topic['pin_state']) and (!$ibforums->member['g_other_topics']) )
        {
        	$std->Error( array( LEVEL => 1, MSG => 'no_view_topic') );
        }

        //-----------------------------------------
        // Check access
        //-----------------------------------------

        $forums->forums_check_access( $this->forum['id'], 1, 'topic' );

        //-----------------------------------------
        //
        // Main logic engine
        //
        //-----------------------------------------

        if ($ibforums->input['client'] == 'choose')
        {
        	// Show the "choose page"

        	$this->page_title = $this->topic['title'];

			$this->nav = array ( "<a href='{$ibforums->base_url}&act=SF&f={$this->forum['id']}'>{$this->forum['name']}</a>",
							 	 "<a href='{$ibforums->base_url}&act=ST&f={$this->forum['id']}&t={$this->topic['tid']}'>{$this->topic['title']}</a>"
						       );


			$this->output = $this->html->choose_form($this->forum['id'], $this->topic['tid'], $this->topic['title']);

			$print->add_output("$this->output");

        	$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, NAV => $this->nav ) );

        	exit(); // Incase we haven't already done so :p
        }
        else
        {
        	$header = 'text/html';
        	$ext    = '.html';

        	switch ($ibforums->input['client'])
        	{
        		case 'printer':
        			$header = 'text/html';
        			$ext    = '.html';
        			break;
        		case 'html':
        			$header = 'unknown/unknown';
        			$ext    = '.html';
        			break;
        		default:
        			$header = 'application/msword';
        			$ext    = '.doc';
        	}
        }

        $title = substr( str_replace( " ", "_" , preg_replace( "/&(lt|gt|quot|#124|#036|#33|#39);/", "", $this->topic['title'] ) ), 0, 12);

		//$this->output .= "<br><br><font size='1'><center>Powered by Invision Power Board<br>&copy; 2002 Invision PS</center></font></body></html>";

		@header("Content-type: $header");

		if ($ibforums->input['client'] != 'printer')
		{
			@header("Content-Disposition: attachment; filename=$title".$ext);
		}

		print $this->get_posts();

		exit;


	}

	/*-------------------------------------------------------------------------*/
	// GET POSTS
	/*-------------------------------------------------------------------------*/

	function get_posts()
	{
		global $ibforums, $DB, $std, $print;

		//-----------------------------------------
		// Render the page top
		//-----------------------------------------

		$posts_html = $this->html->pp_header( $this->forum['name'], $this->topic['title'], $this->topic['starter_name'] , $this->forum['id'], $this->topic['tid']);

		$max_posts   = 300;
		$attach_pids = array();

		$DB->simple_construct( array ( 'select' => '*',
									   'from'   => 'posts',
									   'where'  => "topic_id={$this->topic['tid']} and queued=0",
									   'order'  => 'pid',
									   'limit'  => array(0, $max_posts)
								   )   );
		$DB->simple_exec();

		//-----------------------------------------
		// Loop through to pick out the correct member IDs.
		// and push the post info into an array - maybe in the future
		// we can add page spans, or maybe save to a PDF file?
		//-----------------------------------------

		$the_posts      = array();
		$mem_ids        = "";
		$member_array   = array();
		$cached_members = array();

		while ( $i = $DB->fetch_row() )
		{
			$the_posts[] = $i;

			if ($i['author_id'] != 0)
			{
				if (preg_match( "/'".$i['author_id']."',/", $mem_ids) )
				{
					continue;
				}
				else
				{
					$mem_ids .= "'".$i['author_id']."',";
				}
			}
		}

		//-----------------------------------------
		// Fix up the member_id string
		//-----------------------------------------

		$mem_ids = preg_replace( "/,$/", "", $mem_ids);

		//-----------------------------------------
		// Get the member profiles needed for this topic
		//-----------------------------------------

		if ($mem_ids != "")
		{
			$DB->cache_add_query( 'print_page_get_members', array( 'mem_ids' => $mem_ids ) );
			$DB->cache_exec_query();

			while ( $m = $DB->fetch_row() )
			{
				if ($m['id'] and $m['name'])
				{
					if (isset($member_array[ $m['id'] ]))
					{
						continue;
					}
					else
					{
						$member_array[ $m['id'] ] = $m;
					}
				}
			}
		}

		//-----------------------------------------
		// Format and print out the topic list
		//-----------------------------------------

		$td_col_cnt = 0;

		foreach ($the_posts as $row) {

			$poster = array();

			//-----------------------------------------
			// Get the member info. We parse the data and cache it.
			// It's likely that the same member posts several times in
			// one page, so it's not efficient to keep parsing the same
			// data
			//-----------------------------------------

			if ($row['author_id'] != 0)
			{
				//-----------------------------------------
				// Is it in the hash?
				//-----------------------------------------

				if ( isset($cached_members[ $row['author_id'] ]) )
				{
					//-----------------------------------------
					// Ok, it's already cached, read from it
					//-----------------------------------------

					$poster = $cached_members[ $row['author_id'] ];
					$row['name_css'] = 'normalname';
				}
				else
				{
					//-----------------------------------------
					// Ok, it's NOT in the cache, is it a member thats
					// not been deleted?
					//-----------------------------------------

					if ($member_array[ $row['author_id'] ])
					{
						$row['name_css'] = 'normalname';
						$poster = $member_array[ $row['author_id'] ];

						//-----------------------------------------
						// Add it to the cached list
						//-----------------------------------------

						$cached_members[ $row['author_id'] ] = $poster;
					}
					else
					{
						//-----------------------------------------
						// It's probably a deleted member, so treat them as a guest
						//-----------------------------------------

						$poster = $std->set_up_guest( $row['author_id'] );
						$row['name_css'] = 'unreg';
					}
				}
			}
			else
			{
				//-----------------------------------------
				// It's definately a guest...
				//-----------------------------------------

				$poster = $std->set_up_guest( $row['author_name'] );
				$row['name_css'] = 'unreg';
			}

			//-----------------------------------------

			$row['post_css'] = $td_col_count % 2 ? 'post1' : 'post2';

			++$td_col_count;

			//-----------------------------------------

			$row['post'] = preg_replace( "/<!--EDIT\|(.+?)\|(.+?)-->/", "", $row['post'] );

			//-----------------------------------------

			$row['post_date']   = $std->get_date( $row['post_date'], 'LONG' );

			//-----------------------------------------
 			// Quoted attachments?
 			//-----------------------------------------

 			preg_match( "#\[attachmentid=(\d+)\]i#", $row['post'], $match );

 			if ( $match[1] )
 			{
 				$attach_pids[ $match[1] ] = $match[1];
 			}

 			$attach_pids[ $row['pid'] ] = $row['pid'];

			$row['post'] = $this->parse_message($row['post']);

			//-----------------------------------------
			// Siggie stuff
			//-----------------------------------------

			$row['signature'] = "";

			if ($poster['signature'] and $ibforums->member['view_sigs'])
			{
				if ($row['use_sig'] == 1)
				{
					$this->parser->pp_do_html  = intval($ibforums->vars['sig_allow_html']);
					$this->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
					$this->pp_nl2br            = 1;

					$row['signature'] = $ibforums->skin_global->signature_separator( $this->parser->post_db_parse($poster['signature']) );
				}
			}

			//-----------------------------------------
			// Parse HTML tag on the fly
			//-----------------------------------------

			$this->parser->pp_do_html  = ( $this->forum['use_html'] and $ibforums->cache['group_cache'][ $ibforums->member['mgroup'] ]['g_dohtml'] and $row['post_htmlstate'] ) ? 1 : 0;
			$this->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
			$this->parser->pp_nl2br    = $row['post_htmlstate'] == 2 ? 1 : 0;

			$row['post'] = $this->parser->post_db_parse( $row['post'] );

			$posts_html .= $this->html->pp_postentry( $poster, $row );

		}

		if ( count( $attach_pids ) )
 		{
 			//-----------------------------------------
			// ATTACHMENTS!!!
			//-----------------------------------------

			require_once( ROOT_PATH.'sources/topics.php' );
			$this->topic = new topics();
 			$this->topic->topic_init();

			$posts_html = $this->topic->parse_attachments( $posts_html, $attach_pids );
 		}

		//-----------------------------------------
		// Print the footer
		//-----------------------------------------

		$posts_html .= $this->html->pp_end();

		//-----------------------------------------
		// Macros
		//-----------------------------------------

		$print->_unpack_macros();

		if ( is_array( $print->macros ) )
      	{
			foreach( $print->macros as $i => $row )
			{
				if ( $row['macro_value'] != "" )
				{
					$posts_html = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $posts_html );
				}
			}
		}

		$posts_html = str_replace( "<#IMG_DIR#>", $ibforums->skin['_imagedir'], $posts_html );
		$posts_html = str_replace( "<#EMO_DIR#>", $ibforums->skin['_emodir']  , $posts_html );

		//-----------------------------------------
        // CSS
        //-----------------------------------------

        $ibforums->skin['_usecsscache'] = 0;

        $css = $print->_get_css();

        $posts_html = str_replace( '<!--IPB.CSS-->', $css, $posts_html );

		return $posts_html;
	}


	function parse_message($message="")
	{
		$message = preg_replace( "#<!--Flash (.+?)-->.+?<!--End Flash-->#e"                            , "(FLASH MOVIE)" , $message );
		$message = preg_replace( "#<a href=[\"'](http|https|ftp|news)://(\S+?)['\"].+?".">(.+?)</a>#"  , "\\1://\\2"     , $message );

		return $message;

	}

}

?>