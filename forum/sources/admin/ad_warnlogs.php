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
|   > Warn Log functions
|   > Module written by Matt Mecham
|   > Date started: 4th June 2003
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

class ad_warnlogs {

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

			case 'viewcontact':
				$this->view_contact();
				break;

			case 'viewnote':
				$this->view_note();
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
	// View NOTE in da pop up innit
	//-----------------------------------------

	function view_note()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again");
		}

		require( ROOT_PATH.'sources/lib/post_parser.php');

        $this->parser  = new post_parser(1);

		$id = intval($ibforums->input['id']);

		$DB->cache_add_query( 'warnlogs_view_note', array( 'id' => $id ) );
		$DB->cache_exec_query();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again ($id)");
		}

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$content = preg_match( "#<content>(.+?)</content>#is", $row['wlog_notes'], $cont );

		$ibforums->html .= $ibforums->adskin->start_table( "Warn Notes" );

		$row['date']  = $ibforums->admin->get_date( $row['wlog_date'], 'LONG' );

		$text   = $this->parser->convert( array(
													'TEXT'    => $cont[1],
													'SMILIES' => 1,
													'CODE'    => 1,
													'HTML'    => 0
										   )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<strong>From:</strong> {$row['p_name']}
													<br /><strong>To:</strong> {$row['a_name']}
													<br /><strong>Sent:</strong> {$row['date']}
													<hr>
													<br />$text
												    "
										 )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();


	}


	//-----------------------------------------
	// View contact in da pop up innit
	//-----------------------------------------

	function view_contact()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again");
		}

		$id = intval($ibforums->input['id']);

		$DB->cache_add_query( 'warnlogs_view_note', array( 'id' => $id ) );
		$DB->cache_exec_query();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again ($id)");
		}

		$type = $row['wlog_contact'] == 'pm' ? "PM" : "EMAIL";

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$subject = preg_match( "#<subject>(.+?)</subject>#is", $row['wlog_contact_content'], $subj );
		$content = preg_match( "#<content>(.+?)</content>#is", $row['wlog_contact_content'], $cont );

		$ibforums->html .= $ibforums->adskin->start_table( $type.": ".$subj[1] );



		$row['date'] = $ibforums->admin->get_date( $row['wlog_date'], 'LONG' );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<strong>From:</strong> {$row['p_name']}
													<br /><strong>To:</strong> {$row['a_name']}
													<br /><strong>Sent:</strong> {$row['date']}
													<br /><strong>Subject:</strong> $subj[1]
													<hr>
													<br />$cont[1]
												    "
										 )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();


	}




	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function view()
	{
		global $ibforums, $DB,  $std;

		$start = $ibforums->input['st'] ? $ibforums->input['st'] : 0;

		$ibforums->html .= $ibforums->adskin->js_pop_win();

		$ibforums->admin->page_detail = "Viewing all warn entries on a member";
		$ibforums->admin->page_title  = "Warn Logs Manager";

		if ($ibforums->input['search_string'] == "" and $ibforums->input['mid'])
		{
			$DB->simple_construct( array( 'select' => 'COUNT(wlog_id) as count', 'from' => 'warn_logs', 'where' => "wlog_mid='".$ibforums->input['mid']."'" ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=warnlog&mid={$ibforums->input['mid']}&code=view";

			$DB->cache_add_query( 'warnlogs_view', array( 'mid' => $ibforums->input['mid'], 'start' => $start ) );
			$DB->cache_exec_query();
		}
		else
		{
			$ibforums->input['search_string'] = urldecode($ibforums->input['search_string']);

			if ( ($ibforums->input['search_type'] == 'notes') )
			{
				$dbq = "l.wlog_notes LIKE '%".$ibforums->input['search_string']."%'";
			}
			else
			{
				$dbq = "l.wlog_contact_content LIKE '%".$ibforums->input['search_string']."%'";
			}

			$DB->simple_construct( array( 'select' => 'COUNT(l.wlog_id) as count', 'from' => 'warn_logs l', 'where' => $dbq ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=warnlog&code=view&search_type={$ibforums->input['search_type']}&search_string=".urlencode($ibforums->input['search_string']);

			$DB->cache_add_query( 'warnlogs_view_two', array( 'dbq' => $dbq, 'start' => $start ) );
			$DB->cache_exec_query();
		}

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $row_count,
											   'PER_PAGE'    => 30,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Pages: ",
											   'BASE_URL'    => $ibforums->base_url.$query,
											 )
									  );

		$ibforums->admin->page_detail = "You may view warn entries added by your moderators";
		$ibforums->admin->page_title  = "Warn Logs Manager";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Type"        , "5%" );
		$ibforums->adskin->td_header[] = array( "Member Name" , "15%" );
		$ibforums->adskin->td_header[] = array( "Contacted"   , "5%" );
		$ibforums->adskin->td_header[] = array( "MOD Q"       , "10%" );
		$ibforums->adskin->td_header[] = array( "SUSP"        , "10%" );
		$ibforums->adskin->td_header[] = array( "NO POST"     , "10%" );
		$ibforums->adskin->td_header[] = array( "Date"        , "15%" );
		$ibforums->adskin->td_header[] = array( "Warned By"   , "15%" );
		$ibforums->adskin->td_header[] = array( "View Note"   , "10%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Warn Logs" );
		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'right', 'pformstrip');

		$days = array( 'd' => "Days", 'h' => "Hours" );

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$row['wlog_date'] = $ibforums->admin->get_date( $row['wlog_date'], 'LONG' );

				$type = ( $row['wlog_type'] == 'pos' )      ? '<span style="color:green;font-weight:bold">-</span>' : '<span style="color:red;font-weight:bold">+</span>';
				$cont = ( $row['wlog_contact'] !=  'none' ) ? "<center><a href='javascript:pop_win(\"&act=warnlog&code=viewcontact&id={$row['wlog_id']}\",400,400)'>View</a></center>" : '&nbsp;';

				$mod     = preg_match( "#<mod>(.+?)</mod>#is"        , $row['wlog_notes'], $mm );
				$post    = preg_match( "#<post>(.+?)</post>#is"      , $row['wlog_notes'], $pm );
				$susp    = preg_match( "#<susp>(.+?)</susp>#is"      , $row['wlog_notes'], $sm );
				$content = preg_match( "#<content>(.+?)</content>#is", $row['wlog_notes'], $cm );

				$content = $cm[1];

				$mod  = trim($mm[1]);
				$post = trim($pm[1]);
				$susp = trim($sm[1]);

				list($v, $u, $i) = explode(',', $mod);

				if ( $i == 1 )
				{
					$mod = 'INDEF';
				}
				else if ( $v == "" )
				{
					$mod = 'None';
				}
				else
				{
					$mod = $v.' '.$days[$u];
				}

				//-----------------------------------------

				list($v, $u, $i) = explode(',', $post);

				if ( $i == 1 )
				{
					$post = 'INDEF';
				}
				else if ( $v == "" )
				{
					$post = 'None';
				}
				else
				{
					$post = $v.' '.$days[$u];
				}

				list($v, $u) = explode(',', $susp);

				if ( $v == "" )
				{
					$susp = 'None';
				}
				else
				{
					$susp = $v.' '.$days[$u];
				}

				//-----------------------------------------

				$ibforums->html .= $ibforums->adskin->add_td_row( array(
														  "<center>$type</center>",
														  "<b>{$row['a_name']}</b>",
														  $cont,
														  $mod,
														  $susp,
														  $post,
														  "{$row['wlog_date']}",
														  "<b>{$row['p_name']}</b>",
														  "<center><a href='javascript:pop_win(\"&act=warnlog&code=viewnote&id={$row['wlog_id']}\",400,400)'>View</a></center>"
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

		if ($ibforums->input['mid'] == "")
		{
			$ibforums->admin->error("You did not select a member ID to remove by!");
		}

		$DB->simple_exec_query( array( 'delete' => 'warn_logs', 'where' => "wlog_mid={$ibforums->input['mid']}" ) );

		$ibforums->admin->save_log("Removed Warn Logs");

		$std->boink_it($ibforums->base_url."&act=warnlog");
	}





	//-----------------------------------------
	// SHOW LOGS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$form_array = array();

		$ibforums->admin->page_detail = "You may view and remove warn actions performed by your staff.<br />Note: Removing the logs does not decrease the member's warn level";
		$ibforums->admin->page_title  = "Warn Logs Manager";

		$ibforums->html .= $ibforums->adskin->js_pop_win();

		//-----------------------------------------
		// VIEW LAST 5
		//-----------------------------------------

		$DB->cache_add_query( 'warnlogs_list_current', array() );
		$DB->cache_exec_query();

		$ibforums->adskin->td_header[] = array( "Type"            , "5%" );
		$ibforums->adskin->td_header[] = array( "Warned Member"   , "25%" );
		$ibforums->adskin->td_header[] = array( "Contacted?"      , "5%" );
		$ibforums->adskin->td_header[] = array( "Date"            , "25%" );
		$ibforums->adskin->td_header[] = array( "Warned By"       , "25%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Last 10 Warn Entries" );

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$row['wlog_date'] = $ibforums->admin->get_date( $row['wlog_date'], 'LONG' );

				$type = ( $row['wlog_type'] == 'pos' ) ? '<span style="color:green;font-weight:bold">-</span>' : '<span style="color:red;font-weight:bold">+</span>';
				$cont = ( $row['wlog_contact'] !=  'none' ) ? "<center><a title='Show message' href='javascript:pop_win(\"&act=warnlog&code=viewcontact&id={$row['wlog_id']}\",400,400)'><img src='{$ibforums->adskin->img_url}/acp_check.gif' border='0' alt='X'></a></center>" : '&nbsp;';

				$ibforums->html .= $ibforums->adskin->add_td_row( array(
														  "<center>$type</center>",
														  "<b>{$row['a_name']}</b>",
														  $cont,
														  "{$row['wlog_date']}",
														  "<b>{$row['p_name']}</b>",
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
		$ibforums->adskin->td_header[] = array( "Times Warned"           , "20%" );
		$ibforums->adskin->td_header[] = array( "View all by member"     , "20%" );
		$ibforums->adskin->td_header[] = array( "Remove all by member"   , "30%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Warn Logs" );

		$DB->cache_add_query( 'warnlogs_list_current_two', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$r['name']}</b>",
													  "<center>{$r['act_count']}</center>",
													  "<center><a href='".$ibforums->adskin->base_url."&act=warnlog&code=view&mid={$r['wlog_mid']}'>View</a></center>",
													  "<center><a href='".$ibforums->adskin->base_url."&act=warnlog&code=remove&mid={$r['wlog_mid']}'>Remove</a></center>",
											 )      );
		}



		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
												  2 => array( 'act'   , 'warnlog'       ),
									     )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Warn Logs" );

		$form_array = array(
							  0 => array( 'notes'  , 'Entry Notes' ),
							  1 => array( 'contact', 'Email/PM Sent'  ),
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