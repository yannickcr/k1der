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
|   > Admin Member Tool functions
|   > Module written by Matt Mecham
|   > Date started: 17th September 2003
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

$root_path = "";

class ad_member_tools
{

	var $base_url;
	var $modules = "";

	function auto_run()
	{
		global $ibforums, $DB,  $std, $ibforums;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		$ibforums->admin->nav[] = array( 'act=mtools', 'Member Tools Home' );

		//-----------------------------------------

		$ibforums->admin->page_title  = "Member Tool Box";
		$ibforums->admin->page_detail = 'You can use the tools below to search for IP address.';

		switch($ibforums->input['code'])
		{

			case 'showallips':
				$this->show_ips();
				break;

			case 'learnip':
				$this->learn_ip();
				break;

			//-----------------------------------------
			default:
				$this->show_index();
				break;
		}

	}


	//-----------------------------------------
	//
	// LEARN ABOUT THE IP. It's very good.
	//
	//-----------------------------------------


	function learn_ip()
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;

		if ( $ibforums->input['ip'] == "" )
		{
			$this->show_index("You did not enter an IP address to search by");
		}

		$ip = trim($ibforums->input['ip']);

		$resolved = 'N/A - Partial IP Address';
		$exact    = 0;

		if ( substr_count( $ip, '.' ) == 3 )
		{
			$exact = 1;
		}

		if ( strstr( $ip, '*' ) )
		{
			$exact = 0;
			$ip    = str_replace( "*", "", $ip );
		}

		if ( $exact != 0 )
		{
			$resolved = @gethostbyaddr($ip);
			$query    = "='".$ip."'";
		}
		else
		{
			$query    = " LIKE '".$ip."%'";
		}

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Host Address for {$ibforums->input['ip']}" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>IP address resolves to</b>" ,
																 $resolved
													    )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Find registered members
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Name"       , "30%" );
		$ibforums->adskin->td_header[] = array( "Email"      , "20%" );
		$ibforums->adskin->td_header[] = array( "Posts"      , "10%" );
		$ibforums->adskin->td_header[] = array( "IP"         , "20%" );
		$ibforums->adskin->td_header[] = array( "Registered" , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Members using that IP when REGISTERING" );

		$DB->simple_construct( array( 'select' => 'id, name, email, posts, ip_address, joined',
									  'from'   => 'members',
									  'where'  => "ip_address{$query}",
									  'order'  => 'joined DESC',
									  'limit'  => array( 0,250) ) );
		$DB->simple_exec();

		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No Matches Found", "center");
		}
		else
		{
			while ( $m = $DB->fetch_row() )
			{

				$ibforums->html .= $ibforums->adskin->add_td_row( array( $m['name'] ,
																		 $m['email'],
																		 $m['posts'],
																		 $m['ip_address'],
																		 $std->get_date( $m['joined'], 'SHORT' )
																)      );
			}
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Find Names posted under
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Name"       , "20%" );
		$ibforums->adskin->td_header[] = array( "Email"      , "20%" );
		$ibforums->adskin->td_header[] = array( "IP"         , "15%" );
		$ibforums->adskin->td_header[] = array( "First Used"  , "20%" );
		$ibforums->adskin->td_header[] = array( "View Post"  , "15%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Members using that IP when POSTING" );

		$DB->cache_add_query( 'member_tools_learn_ip_one', array( 'query' => $query) );
		$DB->cache_exec_query();

		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No Matches Found", "center");
		}
		else
		{
			while ( $m = $DB->fetch_row() )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( $m['name'] ,
																		 $m['email'],
																		 $m['ip_address'],
																		 $std->get_date( $m['post_date'], 'SHORT' ),
																		 "<center><a href='index.php?showtopic={$m['topic_id']}&view=findpost&p={$m['pid']}' target='_blank'>View Post</a></center>",
																)      );
			}
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Find Names VOTED under
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Name"       , "20%" );
		$ibforums->adskin->td_header[] = array( "Email"      , "20%" );
		$ibforums->adskin->td_header[] = array( "IP"         , "15%" );
		$ibforums->adskin->td_header[] = array( "First Used" , "20%" );
		$ibforums->adskin->td_header[] = array( "View Poll" , "15%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Members using that IP when VOTING" );

		$DB->cache_add_query( 'member_tools_learn_ip_two', array( 'query' => $query) );
		$DB->cache_exec_query();

		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No Matches Found", "center");
		}
		else
		{
			while ( $m = $DB->fetch_row() )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( $m['name'] ,
																		 $m['email'],
																		 $m['ip_address'],
																		 $std->get_date( $m['vote_date'], 'SHORT' ),
																		 "<center><a href='index.php?showtopic={$m['tid']}' target='_blank'>View Poll</a></center>",
																)      );
			}
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		// Find Names EMAILING under
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Name"       , "20%" );
		$ibforums->adskin->td_header[] = array( "Email"      , "20%" );
		$ibforums->adskin->td_header[] = array( "IP"         , "15%" );
		$ibforums->adskin->td_header[] = array( "First Used"    , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Members using that IP when EMAILING other members" );

		$DB->cache_add_query( 'member_tools_learn_ip_three', array( 'query' => $query) );
		$DB->cache_exec_query();

		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No Matches Found", "center");
		}
		else
		{
			while ( $m = $DB->fetch_row() )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( $m['name'] ,
																		 $m['email'],
																		 $m['from_ip_address'],
																		 $std->get_date( $m['email_date'], 'SHORT' ),
																)      );
			}
		}

		$ibforums->html .= $ibforums->adskin->end_table();


		//-----------------------------------------
		// Find Names VALIDATING under
		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "Name"       , "20%" );
		$ibforums->adskin->td_header[] = array( "Email"      , "20%" );
		$ibforums->adskin->td_header[] = array( "IP"         , "15%" );
		$ibforums->adskin->td_header[] = array( "First Used" , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Members using that IP while VALIDATING their accounts" );

		$DB->cache_add_query( 'member_tools_learn_ip_four', array( 'query' => $query) );
		$DB->cache_exec_query();

		if ( ! $DB->get_num_rows() )
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No Matches Found", "center");
		}
		else
		{
			while ( $m = $DB->fetch_row() )
			{
				$ibforums->html .= $ibforums->adskin->add_td_row( array( $m['name'] ,
														  $m['email'],
														  $m['ip_address'],
														  $std->get_date( $m['entry_date'], 'SHORT' ),
												 )      );
			}
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	//-----------------------------------------
	//
	// SHOW ALL IPs
	//
	//-----------------------------------------


	function show_ips()
	{
		global $ibforums, $DB,  $std;

		if ( $ibforums->input['name'] == "" and $ibforums->input['member_id'] == "" )
		{
			$this->show_index("You did not enter a name to search by");
		}

		if ( $ibforums->input['member_id'] )
		{
			$id = intval($ibforums->input['member_id']);

			$DB->simple_construct( array( 'select' => 'id, name, email, ip_address', 'from' => 'members', 'where' => "id=$id" ) );
			$DB->simple_exec();

			if ( ! $member = $DB->fetch_row() )
			{
				$this->show_index("Could not locate a member with the id of '$id'");
			}
		}
		else
		{
			$name = addslashes($ibforums->input['name']);

			$DB->simple_construct( array( 'select' => 'id, name, email, ip_address', 'from' => 'members', 'where' => "name='$name'" ) );
			$DB->simple_exec();

			if ( ! $member = $DB->fetch_row() )
			{
				$this->show_index( "We could not find an exact match for that member name, some choices will be shown below", $name );
			}
		}

		$DB->simple_construct( array( 'select' => 'count(distinct(ip_address)) as cnt', 'from' => 'posts', 'where' => "author_id={$ibforums->member['id']}" ) );
		$DB->simple_exec();

		$count = $DB->fetch_row();

		$st  = intval($ibforums->input['st']);
		$end = 50;

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $count['cnt'],
											   'PER_PAGE'    => $end,
											   'CUR_ST_VAL'  => $st,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Multiple Pages",
											   'BASE_URL'    => $ibforums->adskin->base_url."&act=mtools&code=showallips&member_id={$member['id']}",
									  )      );

		$master = array();
		$ips    = array();

		$DB->cache_add_query( 'member_tools_show_ips', array( 'mid' => $member['id'], 'st' => $st, 'end' => $end ) );
		$DB->cache_exec_query();

		while ( $r = $DB->fetch_row() )
		{
			$master[] = $r;
			$ips[]    = "'".$r['ip_address']."'";
		}

		$reg = array();

		if ( count($ips) > 0 )
		{
			$DB->simple_construct( array( 'select' => 'id, name, ip_address', 'from' => 'members', 'where' => "ip_address IN (".implode(",",$ips).") AND id != {$member['id']}" ) );
			$DB->simple_exec();

			while ( $i = $DB->fetch_row() )
			{
				$reg[ $i['ip_address'] ][] = $i;
			}
		}

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "IP Address"          , "20%" );
		$ibforums->adskin->td_header[] = array( "Times Used"          , "10%" );
		$ibforums->adskin->td_header[] = array( "Date Used"           , "25%" );
		$ibforums->adskin->td_header[] = array( "Used for other Reg." , "20%" );
		$ibforums->adskin->td_header[] = array( "IP Tool"             , "25%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "{$member['name']}'s IP addresses ({$count['cnt']}) matches" );

		foreach( $master as $idx => $r )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( $r['ip_address'] ,
																	 $r['ip'] ,
																	 $std->get_date( $r['post_date'], 'SHORT' ),
																	 "<center>". intval( count($reg[ $r['ip_address'] ]) ). "</center>",
																	 "<center><a href='{$ibforums->base_url}&act=mtools&code=learnip&ip={$r['ip_address']}'>Learn about this IP</a></center>"
															)      );
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic( "$links", "center", "catrow2");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}

	//-----------------------------------------
	//
	// Default Screen
	//
	//-----------------------------------------


	function show_index($msg="", $membername="")
	{
		global $ibforums, $DB,  $std, $HTTP_POST_VARS;


		if ($msg != "")
		{
			$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );

			$ibforums->html .= $ibforums->adskin->start_table( "Message" );

			$ibforums->html .= $ibforums->adskin->add_td_row( array( $msg ) );

			$ibforums->html .= $ibforums->adskin->end_table();
		}


		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'showallips'  ),
																 2 => array( 'act'   , 'mtools'     ),
														)      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Show all IP Addresses a member has posted with" );

		if ( $membername == "" )
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Enter the member's name</b>" ,
													  $ibforums->adskin->form_input( "name", $std->txt_stripslashes($_POST['name']) )
											 )      );
		}
		else
		{
			$DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "lower(name) LIKE '{$membername}%'" ) );
			$DB->simple_exec();

			if ( ! $DB->get_num_rows() )
			{
				$this->show_index("There are no members with names that start with '$membername'");
			}

			$mem_array = array();

			while ( $m = $DB->fetch_row() )
			{
				$mem_array[] = array( $m['id'], $m['name'] );
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Choose the member from the selection</b>" ,
													  $ibforums->adskin->form_dropdown( "member_id", $mem_array )
											 )      );
		}

		$ibforums->html .= $ibforums->adskin->end_form("Get IP Addresses");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'learnip'  ),
												  2 => array( 'act'   , 'mtools'     ),
									     )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "IP Multi-Tool" );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Show me everything you know about this IP...</b>" ,
												   $ibforums->adskin->form_input( "ip", $std->txt_stripslashes($_POST['ip']) )
										  )      );

		$ibforums->html .= $ibforums->adskin->end_form("Show me!");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();
	}







}

?>