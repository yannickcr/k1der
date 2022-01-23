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
|   > Spider (MAN) Logs
|   > Module written by Matt Mecham
|   > Date started: 28th May 2003
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class ad_spiderlogs {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		$ibforums->admin->nav[] = array( 'act=spiderlog', 'Search Engine Spider Logs' );

		//-----------------------------------------
		// Get bot names
		//-----------------------------------------

		foreach( explode( "\n", $ibforums->vars['search_engine_bots'] ) as $bot )
		{
			list($ua, $n) = explode( "=", $bot );

			$this->bot_map[ strtolower($ua) ] = $n;
		}

		switch($ibforums->input['code'])
		{
			case 'view':
				$this->view();
				break;

			case 'remove':
				$this->remove();
				break;

			//-----------------------------------------
			default:
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function view()
	{
		global $ibforums, $DB,  $std;

		$start = $ibforums->input['st'] ? $ibforums->input['st'] : 0;

		$ibforums->admin->page_detail = "Viewing all actions by a search engine spider";
		$ibforums->admin->page_title  = "Search Engine Logs Manager";

		$botty = urldecode($ibforums->input['bid']);

		if ($ibforums->input['search_string'] == "")
		{
			$DB->simple_construct( array( 'select' => 'COUNT(sid) as count', 'from' => 'spider_logs', 'where' => "bot='$botty'" ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=spiderlog&bid={$ibforums->input['bid']}&code=view";

			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'spider_logs',
										  'where'  => "bot='$botty'",
										  'order'  => 'entry_date DESC',
										  'limit'  => array( $start, 20 ) ) );
			$DB->simple_exec();
		}
		else
		{
			$ibforums->input['search_string'] = urldecode($ibforums->input['search_string']);

			$DB->simple_construct( array( 'select' => 'COUNT(sid) as count', 'from' => 'spider_logs', 'where' => "query_string LIKE '%{$ibforums->input['search_string']}%'" ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=spiderlog&code=view&search_string=".urlencode($ibforums->input['search_string']);

			$DB->simple_construct( array( 'select' => '*',
										  'from'   => 'spider_logs',
										  'where'  => "query_string LIKE '%{$ibforums->input['search_string']}%'",
										  'order'  => 'entry_date DESC',
										  'limit'  => array( $start, 20 ) ) );
			$DB->simple_exec();
		}

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $row_count,
											   'PER_PAGE'    => 20,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Pages: ",
											   'BASE_URL'    => $ibforums->base_url.$query,
											 )
									  );

		$ibforums->admin->page_detail = "You may view and remove actions performed by a search engine bot";
		$ibforums->admin->page_title  = "Search Engine Logs Manager";

        //-----------------------------------------
		// Show form!
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Bot Name"            , "15%" );
		$ibforums->adskin->td_header[] = array( "Query String"        , "15%" );
		$ibforums->adskin->td_header[] = array( "Time of action"      , "20%" );
		$ibforums->adskin->td_header[] = array( "IP address"          , "10%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Search Engine Logs" );
		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'right', 'pformstrip');

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{
				$extra = "";

				if ( preg_match( '#lo-fi#i', $row['query_string'] ) )
				{
					$extra = '(Lo-Fi)';
					$row['query_string'] = 'showtopic='.preg_replace( "#Lo-Fi\: t(.+?)\.html#", "\\1", $row['query_string'] );
				}

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>".$this->bot_map[ strtolower($row['bot']) ]."</b>",
																		 "<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?{$row['query_string']}' target='_blank'>$extra {$row['query_string']}</a>",
																		 $ibforums->admin->get_date( $row['entry_date'], 'LONG' ),
																		 "{$row['ip_address']}",
																)      );

			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("<center>No results</center>");
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'right', 'pformstrip');

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}

	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function remove()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['bid'] == "")
		{
			$ibforums->admin->error("You did not select a bot to remove by!");
		}

		$botty = urldecode($ibforums->input['bid']);

		$DB->simple_exec_query( array( 'delete' => 'spider_logs', 'where' => "bot='$botty'" ) );

		$ibforums->admin->save_log("Removed Search Engine Logs");

		$std->boink_it($ibforums->base_url."&act=spiderlog");
		exit();


	}





	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$form_array = array();

		$ibforums->admin->page_detail = "You may view and remove entries in your spider engine logs";
		$ibforums->admin->page_title  = "Search Engine Logs Manager";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Bot Name"            , "20%" );
		$ibforums->adskin->td_header[] = array( "Hits"                , "20%" );
		$ibforums->adskin->td_header[] = array( "Last Hit"            , "20%" );
		$ibforums->adskin->td_header[] = array( "View all by bot"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Remove all by bot"   , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Search Engine Spider Logs" );


		$DB->cache_add_query( 'spiderlogs_list_current', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{

			$url_butt = urlencode($r['bot']);

			$ibforums->html .= $ibforums->adskin->add_td_row( array( $this->bot_map[ strtolower($r['bot']) ],
																	 "<center>{$r['cnt']}</center>",
																	  $ibforums->admin->get_date( $r['entry_date'], 'SHORT' ),
																	 "<center><a href='".$ibforums->adskin->base_url."&act=spiderlog&code=view&bid={$url_butt}'>View</a></center>",
																	 "<center><a href='".$ibforums->adskin->base_url."&act=spiderlog&code=remove&bid={$url_butt}'>Remove</a></center>",
															)      );
		}



		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
																 2 => array( 'act'   , 'spiderlog'       ),
														)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Search Engine Logs" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search for...</b>" ,
										  		  $ibforums->adskin->form_input( "search_string").'... in the query string'
								 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}



}


?>