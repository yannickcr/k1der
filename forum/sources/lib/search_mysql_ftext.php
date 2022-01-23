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
|   > MySQL FULL TEXT Search Library
|   > Module written by Matt Mecham
|   > Date started: 31st March 2003
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/


class search_lib extends Search
{

    var $parser      = "";
    var $is          = "";
    var $resultlimit = "";

    //-----------------------------------------
	// Constructor
	//-----------------------------------------

    function search_lib($that)
    {
		global $ibforums, $DB, $std, $print;

    	$this->is          = &$that; // hahaha!
    	$this->resultlimit = $this->is->resultlimit;
 	}

 	//-----------------------------------------
	// Simple search
	//-----------------------------------------

	function do_simple_search()
	{
		global $ibforums, $DB, $std, $HTTP_POST_VARS, $print, $forums;

		if ( ! $ibforums->input['sid'] )
		{
			$boolean = "";

			//-----------------------------------------
			// NEW SEARCH.. Check Keywords..
			//-----------------------------------------

			if ( $this->is->mysql_version >= 40010 )
			{
				$boolean  = 'IN BOOLEAN MODE';
				$keywords = $this->is->filter_ftext_keywords($ibforums->input['keywords']);
			}
			else
			{
				$keywords = $this->is->filter_keywords($ibforums->input['keywords']);
			}

			$check_keywords = trim($keywords);

			if ( (! $check_keywords) or ($check_keywords == "") or (! isset($check_keywords) ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_words') );
			}

			if (strlen(trim($keywords)) < $ibforums->vars['min_search_word'])
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_word_short', 'EXTRA' => 4) );
			}

			//-----------------------------------------
			// Check for filter abuse..
			//-----------------------------------------

			$tmp = explode( ' ', $keywords );

			foreach( $tmp as $t )
			{
				if ( ! $t )
				{
					continue;
				}

				$t = preg_replace( "#[\+\-\*\.]#", "", $t );

				//-----------------------------------------
				// Allow abc* but not a***
				//-----------------------------------------

				if ( strlen( $t ) < 3 )
				{
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_word_short', 'EXTRA' => 4) );
				}
			}

			//print $check_keywords; exit();

			//-----------------------------------------
			// Get forums...
			//-----------------------------------------

			$myforums = $this->is->get_searchable_forums();

			if ($myforums == "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
			}

			//-----------------------------------------
			// How many results?
			//-----------------------------------------

			$DB->query("SELECT COUNT(*) as dracula
						FROM ".SQL_PREFIX."posts p
						 LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=p.topic_id)
						WHERE t.forum_id IN ($myforums)
						AND MATCH(p.post) AGAINST ('$check_keywords' $boolean)");

			$count = $DB->fetch_row();

			if ( $count['dracula'] < 1 ) // Tee-hee!
			{
				$this->output .= $this->is->html->search_error_page($ibforums->input['keywords']);
				$print->add_output( $this->output );
				$print->do_output( array( 'TITLE' => $ibforums->lang['g_simple_title'], 'JS' => 0, NAV => array( $ibforums->lang['g_simple_title'] ) ) );
			}

			//-----------------------------------------
			// Store it daddy-o!
			//-----------------------------------------

			$cache = "SELECT MATCH(post) AGAINST ('$check_keywords' $boolean) as score, t.tid, t.posts as topic_posts, t.title as topic_title, t.views, t.forum_id,
			                 p.post, p.author_id, p.author_name, p.post_date, p.pid, p.post_htmlstate,m.*, me.*
					  FROM ".SQL_PREFIX."posts p
					   LEFT JOIN ".SQL_PREFIX."topics t on (p.topic_id=t.tid)
					   LEFT JOIN ".SQL_PREFIX."members m on (m.id=p.author_id)
					   LEFT JOIN ".SQL_PREFIX."member_extra me on (me.id=p.author_id)
					  WHERE t.forum_id IN ($myforums) AND t.title IS NOT NULL
					  AND MATCH(post) AGAINST ('$check_keywords' $boolean)";

			if ( $ibforums->input['sortby'] != "relevant" )
			{
				$cache .= " ORDER BY p.post_date DESC";
			}

			$unique_id = md5(uniqid(microtime(),1));

			$str = $DB->compile_db_insert_string( array (
															'id'         => $unique_id,
															'search_date'=> time(),
															'topic_id'   => '00',
															'topic_max'  => $count['dracula'],
															'member_id'  => $ibforums->member['id'],
															'ip_address' => $ibforums->input['IP_ADDRESS'],
															'post_id'    => '00',
															'query_cache'=> $cache,

												)        );

			$DB->query("INSERT INTO ibf_search_results ({$str['FIELD_NAMES']}) VALUES ({$str['FIELD_VALUES']})");

			$hilight = str_replace( '&quot;', '', $ibforums->input['keywords'] );
			$hilight = urlencode(trim(str_replace( '&amp;', '&', $hilight)));

			$print->redirect_screen( $ibforums->lang['search_redirect'] , "act=Search&CODE=simpleresults&sid=$unique_id&highlite=".$hilight );

		}
		else
		{
			//-----------------------------------------
			// Load up the topic stuff
			//-----------------------------------------

			require_once( ROOT_PATH.'sources/topics.php' );
			$this->topics = new topics();
			$this->topics->topic_init();

			//-----------------------------------------
			// Get SQL schtuff
			//-----------------------------------------

			$this->unique_id = $ibforums->input['sid'];

			if ($this->unique_id == "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
			}

			$DB->query("SELECT * FROM ibf_search_results WHERE id='{$this->unique_id}'");

			if ( ! $sr = $DB->fetch_row() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_results' ) );
			}

			$query = stripslashes($sr['query_cache']);

			$check_keywords = preg_replace( '/&amp;(lt|gt|quot);/', "&\\1;", trim(urldecode($ibforums->input['highlite'])) );

			//-----------------------------------------
			// Display
			//-----------------------------------------

			$this->links = $std->build_pagelinks(
						     array(
						      		'TOTAL_POSS'  => $sr['topic_max'],
						      		'leave_out'   => 10,
									'PER_PAGE'    => 25,
									'CUR_ST_VAL'  => $this->is->first,
									'L_SINGLE'    => $ibforums->lang['sp_single'],
									'L_MULTI'     => "",
									'BASE_URL'    => $ibforums->base_url."&amp;act=Search&amp;CODE=simpleresults&amp;sid=".$this->unique_id."&amp;highlite=".urlencode(str_replace('"', '', $check_keywords)),
								  )
						   					    );

			//-----------------------------------------
			// oh look, a query!
			//-----------------------------------------

			$last_tid = 0;

			$SQLtime = new Debug();

			$SQLtime->startTimer();

			$outer = $DB->query($query." LIMIT {$this->is->first},25");

			$ex_time = sprintf( "%.4f",$SQLtime->endTimer() );

			$show_end = 25;

			if ( $sr['topic_max'] < 25 )
			{
				$show_end = $sr['topic_max'];
			}

			$this->output .= $this->is->html->result_simple_header(array(
																		 'links'   => $this->links,
																		 'start'   => $this->is->first,
																		 'end'     => $show_end + $this->is->first,
																		 'matches' => $sr['topic_max'],
																		 'ex_time' => $ex_time,
																		 'keyword' => $check_keywords,
																  )     );

			$attach_pids = array();

			while ( $row = $DB->fetch_row($outer) )
			{
				//-----------------------------------------
				// Listen up, this is relevant.
				// MySQL's relevance is a bit of a mystery. It's
				// based on many hazy variables such as placing, occurance
				// and such. The result is a floating point number, like 1.239848556
				// No one can really disect what this means in human terms, so I'm
				// going to simply assume that anything over 1.0 is 100%, and *100 any
				// other relevance result.
				//-----------------------------------------

				$member = $this->topics->parse_member( $row );

				$row['relevance'] = sprintf( "%3d", ( $row['score'] > 1.0 ) ? 100 : $row['score'] * 100 );

				$row['post_date'] = $std->get_date( $row['post_date'], 'LONG' );

				// Link member's name

				if ($row['author_id'])
				{
					$row['author_name'] = "<a href='{$ibforums->base_url}act=Profile&amp;MID={$row['author_id']}'>{$row['author_name']}</a>";
				}

				//-----------------------------------------
				// Attachments?
				//-----------------------------------------

				if ( strstr( $row['post'], '[attachmentid=' ) )
				{
					$attach_pids[] = $row['pid'];
				}

				//-----------------------------------------
				// Fix up quotes..
				//-----------------------------------------

				$row['post'] = $this->topics->parser->unconvert( $row['post'] );

				$row['post'] = $this->topics->parser->convert( array(
																	  'TEXT'    => $row['post'],
																	  'CODE'    => $forums->forum_by_id[$row['forum_id']]['use_ibc'],
																	  'SMILIES' => 1,
																	  'HTML'    => 0
															 )      );

				$this->topics->parser->pp_do_html  = ( $forums->forum_by_id[$row['forum_id']]['use_html'] and $ibforums->cache['group_cache'][ $row['mgroup'] ]['g_dohtml'] and $row['post_htmlstate'] ) ? 1 : 0;
				$this->topics->parser->pp_wordwrap = $ibforums->vars['post_wordwrap'];
				$this->topics->parser->pp_nl2br    = $row['post_htmlstate'] == 2 ? 1 : 0;

				$row['post'] = $this->topics->parser->post_db_parse( $row['post'] );

				$keywords_array = explode( " ", str_replace( "+", " ", $ibforums->input['highlite'] ) );

				if ( count($keywords_array) )
				{
					foreach( $keywords_array as $keys )
					{
						if ( $keys == "" )
						{
							continue;
						}

						while( preg_match( "/(^|\s|'|\"|>)(".preg_quote($keys, '/').")(\s|'|\"|,|\.|!|<br|$)/is", $row['post'] ) )
						{
							$row['post'] = preg_replace( "/(^|\s|'|\"|>)(".preg_quote($keys, '/').")(\s|'|\"|,|\.|!|<br|$)/is", "\\1<span class='searchlite'>\\2</span>\\3", $row['post'] );
						}
					}
				}

				$row['forum_name'] = $forums->forum_by_id[ $row['forum_id'] ]['name'];


				$this->output .= $this->is->html->RenderPostRow($row, $member);
			}

			//-----------------------------------------
			// Add in attachments?
			//-----------------------------------------

			if ( count( $attach_pids ) )
			{
				$this->output = $this->topics->parse_attachments( $this->output, $attach_pids );
			}

			$this->output .= $this->is->html->end_results_table(array( 'SHOW_PAGES'   => $this->links ) , 1 );

			$print->add_output("$this->output");
			$print->do_output( array( 'TITLE' => $ibforums->lang['g_simple_title'], 'JS' => 0, NAV => array( $ibforums->lang['g_simple_title'] ) ) );

    	}

 	}




 	//-----------------------------------------
	// Main Board Search-e-me-doo-daa
	//-----------------------------------------


	function do_main_search()
	{
		global $ibforums, $DB, $std, $HTTP_POST_VARS, $print;

		//-----------------------------------------
		// Do we have any input?
		//-----------------------------------------

		//-----------------------------------------
		// USING FULL TEXT - Wooohoo!!
		//-----------------------------------------


		if ($ibforums->input['namesearch'] != "")
		{
			$name_filter = $this->is->filter_keywords($ibforums->input['namesearch'], 1);
		}

		if ($ibforums->input['useridsearch'] != "")
		{
			$keywords = $this->is->filter_keywords($ibforums->input['useridsearch']);
			$this->is->search_type = 'userid';
		}
		else
		{
			$keywords = $this->is->filter_keywords($ibforums->input['keywords']);
			$this->is->search_type = 'posts';
		}

		if ( $name_filter != "" AND $ibforums->input['keywords'] != "" )
		{
			$type = 'joined';
		}
		else if ( $name_filter == "" AND $ibforums->input['keywords'] != "" )
		{
			$type= 'postonly';
		}
		else if ( $name_filter != "" AND $ibforums->input['keywords'] == "" )
		{
			$type='nameonly';
		}

		//-----------------------------------------

		if ( $this->is->mysql_version >= 40010 )
		{
			$boolean  = 'IN BOOLEAN MODE';
			$keywords = $this->is->filter_ftext_keywords($ibforums->input['keywords']);
		}
		else
		{
			$keywords = $this->is->filter_keywords($ibforums->input['keywords']);
		}

		$check_keywords = trim($keywords);

		if ( (! $check_keywords) or ($check_keywords == "") or (! isset($check_keywords) ) )
		{
			if ($ibforums->input['namesearch'] == "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_words') );
			}
		}
		else
		{
			if ( strlen(trim($keywords)) < $ibforums->vars['min_search_word'] and $type != 'nameonly' )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_word_short', 'EXTRA' => 4) );
			}
		}

		//-----------------------------------------
		// Check for filter abuse..
		//-----------------------------------------

		$tmp = explode( ' ', $keywords );

		foreach( $tmp as $t )
		{
			if ( ! $t )
			{
				continue;
			}

			$t = preg_replace( "#[\+\-\*\.]#", "", $t );

			//-----------------------------------------
			// Allow abc* but not a***
			//-----------------------------------------

			if ( ( strlen( $t ) < $ibforums->vars['min_search_word'] )  and $type != 'nameonly' )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_word_short', 'EXTRA' => 4) );
			}
		}

		//-----------------------------------------

		if ($ibforums->input['search_in'] == 'titles')
		{
			$this->is->search_in = 'titles';
		}

		//-----------------------------------------

		$forums = $this->is->get_searchable_forums();

		//-----------------------------------------
		// Do we have any forums to search in?
		//-----------------------------------------

		if ($forums == "")
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_forum') );
		}

		//-----------------------------------------

		foreach( array( 'last_post', 'posts', 'starter_name', 'forum_id' ) as $v )
		{
			if ($ibforums->input['sort_key'] == $v)
			{
				$this->is->sort_key = $v;
			}
		}

		//-----------------------------------------

		foreach ( array( 1, 7, 30, 60, 90, 180, 365, 0 ) as $v )
		{
			if ($ibforums->input['prune'] == $v)
			{
				$this->is->prune = $v;
			}
		}

		//-----------------------------------------

		if ($ibforums->input['sort_order'] == 'asc')
		{
			$this->is->sort_order = 'asc';
		}

		//-----------------------------------------

		if ($ibforums->input['result_type'] == 'posts')
		{
			$this->is->result_type = 'posts';
		}

		if ( $ibforums->vars['min_search_word'] < 1 )
		{
			$ibforums->vars['min_search_word'] = 4;
		}

		//-----------------------------------------
		// Add on the prune days
		//-----------------------------------------

		if ($this->is->prune > 0)
		{
			$gt_lt = $ibforums->input['prune_type'] == 'older' ? "<" : ">";
			$time = time() - ($ibforums->input['prune'] * 86400);

			if ( $this->is->result_type == 'posts' )
			{
				$topics_datecut = "t.start_date $gt_lt $time AND";
			}
			else
			{
				$topics_datecut = "t.last_post $gt_lt $time AND";
			}

			$posts_datecut  = "p.post_date $gt_lt $time AND";
		}

		 // Is this a membername search?

		 $name_filter = trim( $name_filter );
		 $member_string = "";

		 if ( $name_filter != "" )
		 {
			//-----------------------------------------
			// Get all the possible matches for the supplied name from the DB
			//-----------------------------------------

			$name_filter = str_replace( '|', "&#124;", $name_filter );

			if ($ibforums->input['exactname'] == 1)
			{
				$sql_query = "SELECT id from ibf_members WHERE lower(name)='".$name_filter."'";
			}
			else
			{
				$sql_query = "SELECT id from ibf_members WHERE name like '%".$name_filter."%'";
			}


			$DB->query( $sql_query );


			while ($row = $DB->fetch_row())
			{
				$member_string .= "'".$row['id']."',";
			}

			$member_string = preg_replace( "/,$/", "", $member_string );

			// Error out of we matched no members

			if ($member_string == "")
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_name_search_results') );
			}

			$posts_name  = " AND p.author_id IN ($member_string)";
			$topics_name = " AND t.starter_id IN ($member_string)";

		}

		if ( $type != 'nameonly' )
		{
			if (strlen(trim($keywords)) < $ibforums->vars['min_search_word'])
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'search_word_short', 'EXTRA' => $ibforums->vars['min_search_word']) );
			}

		}

		$unique_id = md5(uniqid(microtime(),1));

		if ($type != 'nameonly')
		{
			if ( ! $this->is->topic_search_only )
			{
				$topics_query = "SELECT t.tid
								FROM ibf_topics t
								WHERE $topics_datecut t.forum_id IN ($forums)
								$topics_name AND t.approved=1 AND MATCH(title) AGAINST ('".trim($keywords)."' $boolean)";


				$posts_query = "SELECT p.pid
								FROM ibf_posts p
								 LEFT JOIN ibf_topics t ON ( p.topic_id=t.tid )
								WHERE $posts_datecut  t.forum_id IN ($forums)
								 AND p.queued <> 1
								 $posts_name AND MATCH(post) AGAINST ('".trim($keywords)."' $boolean)";
			}
			else
			{
				//-----------------------------------------
				// Search in topic only
				//-----------------------------------------

				$posts_query = "SELECT p.pid
								FROM ibf_posts p
								 LEFT JOIN ibf_topics t ON ( p.topic_id=t.tid )
								WHERE
								 p.topic_id={$this->is->topic_id}
								 AND $posts_datecut  t.forum_id IN ($forums)
								 AND p.queued <> 1
								 $posts_name AND MATCH(post) AGAINST ('".trim($keywords)."' $boolean)";
			}
		}
		else
		{

			$topics_query = "SELECT t.tid
							FROM ibf_topics t
							WHERE $topics_datecut t.forum_id IN ($forums)
							$topics_name";

			$posts_query = "SELECT p.pid
						    FROM ibf_posts p
						     LEFT JOIN ibf_topics t ON ( p.topic_id=t.tid )
						    WHERE $posts_datecut t.forum_id IN ($forums)
						     AND p.queued <> 1
						     $posts_name";
		}

		//-----------------------------------------
		// Get the topic ID's to serialize and store into
		// the database
		//-----------------------------------------

		$topics = "";
		$posts  = "";
		$topic_array = array();
		$post_array  = array();
		$t_cnt       = 0;
		$p_cnt       = 0;
		$more        = "";

		//-----------------------------------------

		if ( ! $this->is->topic_search_only )
		{
			$DB->query($topics_query);

			while ($row = $DB->fetch_row() )
			{
				$t_cnt++;

				if ( $t_cnt > $this->resultlimit )
				{
					$more = 1;
					break;
				}

				$topic_array[ $row['tid'] ] = $row['tid'];
			}

			$DB->free_result();
		}

		//-----------------------------------------

		$DB->query($posts_query);

		while ($row = $DB->fetch_row() )
		{
			$p_cnt++;

			if ( $p_cnt > $this->resultlimit )
			{
				$more = 1;
				break;
			}

			$post_array[ $row['pid'] ] = $row['pid'];
		}

		$DB->free_result();

		//-----------------------------------------

		$topics = implode( ",", $topic_array );
		$posts  = implode( ",", $post_array );

		//-----------------------------------------
		// Do we have any results?
		//-----------------------------------------

		if ($topics == "" and $posts == "")
		{
			$this->output .= $this->is->html->search_error_page($ibforums->input['keywords']);
			$print->add_output( $this->output );
			$print->do_output( array( 'TITLE' => $ibforums->lang['g_simple_title'], 'JS' => 0, NAV => array( $ibforums->lang['g_simple_title'] ) ) );
		}

		//-----------------------------------------
		// If we are still here, return data like a good
		// boy (or girl). Yes Reg; or girl.
		// What have the Romans ever done for us?
		//-----------------------------------------

		return array(
					  'topic_id'    => $topics,
					  'post_id'     => $posts,
					  'topic_max'   => intval( count( $topic_array ) ),
					  'post_max'    => intval( count( $post_array  ) ),
					  'keywords'    => str_replace( '"', "", $keywords ),
					  'query_cache' => $more,
					);

	}


}

?>