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
|   > Import functions
|   > Module written by Matt Mecham
|   > Date started: 22nd April 2002
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

class ad_modlogs {

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

		//-----------------------------------------

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

		$ibforums->admin->page_detail = "Viewing all actions by a moderator";
		$ibforums->admin->page_title  = "Moderator Logs Manager";

		if ($ibforums->input['search_string'] == "")
		{
			$DB->simple_construct( array( 'select' => 'COUNT(id) as count', 'from' => 'moderator_logs', 'where' => "member_id=".intval($ibforums->input['mid']) ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=modlog&mid={$ibforums->input['mid']}&code=view";

			$DB->cache_add_query( 'modlogs_view_one', array( 'mid' => $ibforums->input['mid'], 'start' => $start ) );
			$DB->cache_exec_query();
		}
		else
		{
			$ibforums->input['search_string'] = urldecode($ibforums->input['search_string']);

			if ( ($ibforums->input['search_type'] == 'topic_id') or ($ibforums->input['search_type'] == 'forum_id') )
			{
				$dbq = "m.".$ibforums->input['search_type']."='".$ibforums->input['search_string']."'";
			}
			else
			{
				$dbq = "m.".$ibforums->input['search_type']." LIKE '%".$ibforums->input['search_string']."%'";
			}

			$DB->simple_construct( array( 'select' => 'COUNT(m.id) as count', 'from' => 'moderator_logs m', 'where' => $dbq ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=modlog&code=view&search_type={$ibforums->input['search_type']}&search_string=".urlencode($ibforums->input['search_string']);

			$DB->cache_add_query( 'modlogs_view_two', array( 'dbq' => $dbq, 'start' => $start ) );
			$DB->cache_exec_query();
		}

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $row_count,
											   'PER_PAGE'    => 20,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Pages: ",
											   'BASE_URL'    => $ibforums->base_url.$query,
											 )
									  );

		$ibforums->admin->page_detail = "You may view and remove actions performed by your moderators";
		$ibforums->admin->page_title  = "Moderator Logs Manager";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Member Name"            , "15%" );
		$ibforums->adskin->td_header[] = array( "Action Perfomed"        , "15%" );
		$ibforums->adskin->td_header[] = array( "Forum"                  , "15%" );
		$ibforums->adskin->td_header[] = array( "Topic Title"            , "25%" );
		$ibforums->adskin->td_header[] = array( "Time of action"         , "20%" );
		$ibforums->adskin->td_header[] = array( "IP address"             , "10%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Moderator Logs" );
		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'center', 'catrow');

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{
				$row['ctime'] = $ibforums->admin->get_date( $row['ctime'], 'LONG' );

				if ( $row['topic_id'] )
				{
					$topicid = "<br />Topic ID: ".$row['topic_id'];
				}
				else
				{
					$topicid = "&nbsp;";
				}

				$sess_id             = preg_replace( "/^.+?s=(\w{32}).+?$/" , "\\1", $row['http_referer'] );
				$row['http_referer'] = preg_replace( "/s=(\w){32}/" , ""  , $row['http_referer'] );

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$row['member_name']}</b>",
																		 "<span style='font-weight:bold;color:red'>{$row['action']}</span>",
																		 "<b>{$row['name']}</b>",
																		 "{$row['topic_title']}".$topicid,
																		 "{$row['ctime']}",
																		 "{$row['ip_address']}",
																)      );
			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("<center>No results</center>");
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'center', 'tdtop');

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

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You did not select a member ID to remove by!");
		}

		$DB->simple_exec_query( array( 'delete' => 'moderator_logs', 'where' => "member_id=".intval($ibforums->input['mid']) ) );

		$ibforums->admin->save_log("Removed Moderator Logs");

		$std->boink_it($ibforums->base_url."&act=modlog");
		exit();


	}





	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$form_array = array();

		$ibforums->admin->page_detail = "You may view and remove actions performed by your moderators";
		$ibforums->admin->page_title  = "Moderator Logs Manager";


		//-----------------------------------------
		// VIEW LAST 5
		//-----------------------------------------

		$DB->cache_add_query( 'modlogs_list_current_last_five', array() );
		$DB->cache_exec_query();

		$ibforums->adskin->td_header[] = array( "Member Name"            , "15%" );
		$ibforums->adskin->td_header[] = array( "Action Perfomed"        , "15%" );
		$ibforums->adskin->td_header[] = array( "Forum"                  , "15%" );
		$ibforums->adskin->td_header[] = array( "Topic Title"            , "25%" );
		$ibforums->adskin->td_header[] = array( "Time of action"         , "20%" );
		$ibforums->adskin->td_header[] = array( "IP address"             , "10%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Last 5 Moderation Actions" );

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$row['ctime'] = $ibforums->admin->get_date( $row['ctime'], 'LONG' );

				$topicid = "";

				if ( $row['topic_id'] )
				{
					$topicid = "<br />Topic ID: ".$row['topic_id'];
				}

				$sess_id             = preg_replace( "/^.+?s=(\w{32}).+?$/" , "\\1", $row['http_referer'] );
				$row['http_referer'] = preg_replace( "/s=(\w){32}/" , ""  , $row['http_referer'] );

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$row['member_name']}</b>",
																		 "<span style='font-weight:bold;color:red'>{$row['action']}</span>",
																		 "<b>{$row['name']}</b>",
																		 "{$row['topic_title']}".$topicid,
																		 "{$row['ctime']}",
																		 "{$row['ip_address']}",
																)      );
			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("<center>No results</center>");
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Member Name"            , "30%" );
		$ibforums->adskin->td_header[] = array( "Actions Perfomed"       , "20%" );
		$ibforums->adskin->td_header[] = array( "View all by member"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Remove all by member"   , "30%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Moderator Logs" );

		$DB->cache_add_query( 'modlogs_list_current_show_all', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$r['member_name']}</b>",
																	 "<center>{$r['act_count']}</center>",
																	 "<center><a href='".$ibforums->adskin->base_url."&act=modlog&code=view&mid={$r['member_id']}'>View</a></center>",
																	 "<center><a href='".$ibforums->adskin->base_url."&act=modlog&code=remove&mid={$r['member_id']}'>Remove</a></center>",
															)      );
		}



		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
																 2 => array( 'act'   , 'modlog'       ),
														)      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Moderator Logs" );

		$form_array = array(
							  0 => array( 'topic_title', 'Topic Title' ),
							  1 => array( 'ip_address',  'IP Address'  ),
							  2 => array( 'member_name', 'Member Name' ),
							  3 => array( 'topic_id'   , 'Topic ID'    ),
							  4 => array( 'forum_id'   , 'Forum ID'    )
						   );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search for...</b>" ,
										  		  $ibforums->adskin->form_input( "search_string")
								 )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search in...</b>" ,
										  		  $ibforums->adskin->form_dropdown( "search_type", $form_array)
								 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->output();

	}



}


?>