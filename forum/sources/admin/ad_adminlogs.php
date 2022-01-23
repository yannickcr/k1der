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
|   > Admin Logs Stuff
|   > Module written by Matt Mecham
|   > Date started: 11nd September 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class ad_adminlogs
{

	var $base_url;
	var $colours = array();

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
		// Make sure we're a root admin, or else!
		//-----------------------------------------

		if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
		{
			$ibforums->admin->error("Sorry, these functions are for the root admin group only");
		}

		$this->colours  = array(
								"cat"      => "green",
								"forum"    => "darkgreen",
								"mem"      => "red",
								'group'    => "purple",
								'mod'      => 'orange',
								'op'       => 'darkred',
								'help'     => 'darkorange',
								'modlog'   => 'steelblue',
				   			   );


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

		$ibforums->admin->page_detail = "Viewing all actions by a administrator";
		$ibforums->admin->page_title  = "Administration Logs Manager";

		if ($ibforums->input['search_string'] == "")
		{
			$DB->simple_construct( array( 'select' => 'COUNT(id) as count', 'from' => 'admin_logs', 'where' => "member_id=".intval($ibforums->input['mid']) ) );
			$DB->simple_exec();

			$row = $DB->fetch_row();

			$row_count = $row['count'];

			$query = "&act=adminlog&mid={$ibforums->input['mid']}&code=view";

			$DB->cache_add_query( 'adminlogs_view_one', array( 'mid' => $ibforums->input['mid'], 'limit_a' => $start ) );
			$DB->cache_exec_query();

		}
		else
		{
			$ibforums->input['search_string'] = urldecode($ibforums->input['search_string']);

			$dbq = "m.".$ibforums->input['search_type']." LIKE '%".$ibforums->input['search_string']."%'";

			$DB->simple_construct( array( 'select' => 'COUNT(m.id) as count', 'from' => 'admin_logs m', 'where' => $dbq ) );
			$DB->simple_exec();

			$row_count = $row['count'];

			$query = "&act=adminlog&code=view&search_type={$ibforums->input['search_type']}&search_string=".urlencode($ibforums->input['search_string']);

			$DB->cache_add_query( 'adminlogs_view_two', array( 'dbq' => $dbq, 'limit_a' => $start ) );
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

		$ibforums->admin->page_detail = "You may view and remove actions performed by your administrators";
		$ibforums->admin->page_title  = "Administrator Logs Manager";

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Member Name"            , "20%" );
		$ibforums->adskin->td_header[] = array( "Action Perfomed"        , "40%" );
		$ibforums->adskin->td_header[] = array( "Time of action"         , "20%" );
		$ibforums->adskin->td_header[] = array( "IP address"             , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Admin Logs" );
		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'center', 'pformstrip');

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$row['ctime'] = $ibforums->admin->get_date( $row['ctime'], 'LONG' );

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$row['name']}</b>",
														  "<span style='color:{$this->colours[$row['act']]}'>{$row['note']}</span>",
														  "{$row['ctime']}",
														  "{$row['ip_address']}",
												 )      );


			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("<center>No results</center>");
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic($links, 'center', 'pformstrip');

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

		$DB->simple_exec_query( array( 'delete' => 'admin_logs', 'where' => "member_id=".$ibforums->input['mid'] ) );

		$std->boink_it($ibforums->base_url."&act=adminlog");
	}


	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$form_array = array();

		$ibforums->admin->page_detail = "You may view and remove actions performed by your administrators in mission critical areas of the administration CP (such as forum control, member control, group control, help files and moderator log management).";
		$ibforums->admin->page_title  = "Administration Logs Manager";

		//-----------------------------------------
		// LAST FIVE ACTIONS
		//-----------------------------------------

		$DB->cache_add_query( 'adminlogs_view_list_current', array() );
		$DB->cache_exec_query();

		$ibforums->adskin->td_header[] = array( "Member Name"            , "20%" );
		$ibforums->adskin->td_header[] = array( "Action Perfomed"        , "40%" );
		$ibforums->adskin->td_header[] = array( "Time of action"         , "20%" );
		$ibforums->adskin->td_header[] = array( "IP address"             , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Last 5 Admin Actions" );

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$row['ctime'] = $ibforums->admin->get_date( $row['ctime'], 'LONG' );

				$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$row['name']}</b>",
														  "<span style='color:{$this->colours[$row['act']]}'>{$row['note']}</span>",
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

		$ibforums->html .= $ibforums->adskin->start_table( "Saved Admininstration Logs" );

		$DB->cache_add_query( 'adminlogs_view_list_current_two', array() );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>{$r['name']}</b>",
													  "<center>{$r['act_count']}</center>",
													  "<center><a href='".$ibforums->adskin->base_url."&act=adminlog&code=view&mid={$r['member_id']}'>View</a></center>",
													  "<center><a href='".$ibforums->adskin->base_url."&act=adminlog&code=remove&mid={$r['member_id']}'>Remove</a></center>",
											 )      );
		}



		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
												  2 => array( 'act'   , 'adminlog'       ),
									     )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Admin Logs" );

		$form_array = array(
							  0 => array( 'note'      , 'Action Performed' ),
							  1 => array( 'ip_address',  'IP Address'  ),
							  2 => array( 'member_id' , 'Member ID' ),
							  3 => array( 'act'        , 'ACT Setting'  ),
							  4 => array( 'code'       , 'CODE Setting'  ),
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