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
|   > Email Logs Stuff
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


class ad_emaillogs
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

		if ($ibforums->member['mgroup'] != $ibforums->vars['admin_group'])
		{
			$ibforums->admin->error("Sorry, these functions are for the root admin group only");
		}

		switch($ibforums->input['code'])
		{
			case 'list':
				$this->list_current();
				break;

			case 'remove':
				$this->remove_entries();
				break;

		    case 'viewemail':
		    	$this->view_email();
		    	break;

			//-----------------------------------------
			default:
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// View a single email.
	//-----------------------------------------

	function view_email()
	{
		global $ibforums, $DB,  $std;

		if ($ibforums->input['id'] == "")
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again");
		}

		$id = intval($ibforums->input['id']);

		$DB->cache_add_query( 'emaillogs_view_email', array( 'id' => $id ) );
		$DB->cache_exec_query();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again ($id)");
		}

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( $row['email_subject'] );



		$row['email_date'] = $ibforums->admin->get_date( $row['email_date'], 'LONG' );

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
													"<strong>From:</strong> {$row['name']} &lt;{$row['from_email_address']}&gt;
													<br /><strong>To:</strong> {$row['to_name']} &lt;{$row['to_email_address']}&gt;
													<br /><strong>Sent:</strong> {$row['email_date']}
													<br /><strong>From IP:</strong> {$row['from_ip_address']}
													<br /><strong>Subject:</strong> {$row['email_subject']}
													<hr>
													<br />{$row['email_content']}
												    "
										 )      );

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->print_popup();
	}



	//-----------------------------------------
	// Remove row(s)
	//-----------------------------------------

	function remove_entries()
	{
		global $ibforums, $DB,  $std;

		if ( $ibforums->input['type'] == 'all' )
		{
			$DB->simple_exec_query( array( 'delete' => 'email_logs' ) );
		}
		else
		{
			$ids = array();

			foreach ($ibforums->input as $k => $v)
			{
				if ( preg_match( "/^id_(\d+)$/", $k, $match ) )
				{
					if ($ibforums->input[ $match[0] ])
					{
						$ids[] = $match[1];
					}
				}
			}

			//-----------------------------------------

			if ( count($ids) < 1 )
			{
				$ibforums->admin->error("You did not select any email log entries to approve or delete");
			}

			$DB->simple_exec_query( array( 'delete' => 'email_logs', 'where' => " email_id IN (".implode(',', $ids ).")" ) );
		}

		$ibforums->admin->save_log("Removed email log entries");

		$std->boink_it($ibforums->base_url."&act=emaillog");
		exit();


	}





	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$ibforums->html .= $ibforums->adskin->js_pop_win();

		$form_array = array();

		$start = intval($ibforums->input['st']);

		$ibforums->admin->page_detail = "Stored email logs";
		$ibforums->admin->page_title  = "Email Logs Manager";

		//-----------------------------------------
		// Check URL parameters
		//-----------------------------------------

		$url_query = array();
		$db_query  = array();

		if ( $ibforums->input['type'] != "" )
		{
			$ibforums->admin->page_title .= " (Search Results)";

			switch( $ibforums->input['type'] )
			{
				case 'fromid':
					$url_query[] = 'type=fromid';
					$url_query[] = 'id='.intval($ibforums->input['id']);
					$db_query[]  = 'email.from_member_id='.intval($ibforums->input['id']);
					break;
				case 'toid':
					$url_query[] = 'type=toid';
					$url_query[] = 'id='.intval($ibforums->input['id']);
					$db_query[]  = 'email.to_member_id='.intval($ibforums->input['id']);
					break;
				case 'subject':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "email.email_subject LIKE '%{$string}%'" : "email.email_subject='{$string}'";
					break;
				case 'content':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "email.email_content LIKE '%{$string}%'" : "email.email_content='{$string}'";
					break;
				case 'email_from':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "email.from_email_address LIKE '%{$string}%'" : "email.from_email_address='{$string}'";
					break;
				case 'email_to':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "email.to_email_address LIKE '%{$string}%'" : "email.to_email_address='{$string}'";
					break;
				case 'name_from':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}

					if ( $ibforums->input['match'] == 'loose' )
					{
						$DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name LIKE '%{$string}%'" ) );
						$DB->simple_exec();

						if ( ! $DB->get_num_rows() )
						{
							$ibforums->admin->error("No matches found in the email logs");
						}

						$ids = array();

						while ( $r = $DB->fetch_row() )
						{
							$ids[] = $r['id'];
						}

						$db_query[] = 'email.from_member_id IN('.implode( ',', $ids ).')';
					}
					else
					{
						$DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name='{$string}'" ) );
						$DB->simple_exec();

						if ( ! $DB->get_num_rows() )
						{
							$ibforums->admin->error("No matches found in the email logs");
						}

						$r = $DB->fetch_row();

						$db_query[] = 'email.from_member_id IN('.$r['id'].')';
					}

					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					break;
				case 'name_to':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}

					if ( $ibforums->input['match'] == 'loose' )
					{
						$DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name LIKE '%{$string}%'" ) );
						$DB->simple_exec();

						if ( ! $DB->get_num_rows() )
						{
							$ibforums->admin->error("No matches found in the email logs");
						}

						$ids = array();

						while ( $r = $DB->fetch_row() )
						{
							$ids[] = $r['id'];
						}

						$db_query[] = 'email.to_member_id IN('.implode( ',', $ids ).')';
					}
					else
					{
						$DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name='{$string}'" ) );
						$DB->simple_exec();

						if ( ! $DB->get_num_rows() )
						{
							$ibforums->admin->error("No matches found in the email logs");
						}

						$r = $DB->fetch_row();

						$db_query[] = 'email.to_member_id IN('.$r['id'].')';
					}

					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					break;
				default:
					//
					break;
			}
		}

		$url_query[] = 'match='.$ibforums->input['match'];

		//-----------------------------------------
		// LIST 'EM
		//-----------------------------------------

		$dbe = "";
		$url = "";

		if ( count($db_query) > 0 )
		{
			$dbe = implode(' AND ', $db_query );
		}

		if ( count($url_query) > 0 )
		{
			$url = '&'.implode( '&', $url_query);
		}

		$DB->simple_construct( array( 'select' => 'count(email.email_id) as cnt',
									  'from'   => 'email_logs email',
									  'where'  => $dbe ) );
		$DB->simple_exec();

		$count = $DB->fetch_row();

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $count['cnt'],
											   'PER_PAGE'    => 25,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Pages: ",
											   'BASE_URL'    => $ibforums->base_url.'&act=emaillog'.$url,
											 )
									  );
		if ( $dbe )
		{
			$dbe = 'WHERE '.$dbe;
		}

		$DB->cache_add_query( 'emaillogs_list_current', array( 'dbe' => $dbe, 'limit_a' => $start ) );
		$DB->cache_exec_query();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'remove'     ),
												                 2 => array( 'act'   , 'emaillog'       ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"         , "5%" );
		$ibforums->adskin->td_header[] = array( "From Member"    , "20%" );
		$ibforums->adskin->td_header[] = array( "Subject"        , "30%" );
		$ibforums->adskin->td_header[] = array( "To Member"      , "20%" );
		$ibforums->adskin->td_header[] = array( "Sent Time"      , "25%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Logged Emails" );

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{

				$row['email_date'] = $ibforums->admin->get_date( $row['email_date'], 'SHORT' );

				$ibforums->html .= $ibforums->adskin->add_td_row( array(
														  "<center><input type='checkbox' class='checkbox' name='id_{$row['email_id']}' value='1' /></center>",
														  "<a href='{$ibforums->base_url}&act=emaillog&code=list&type=fromid&id={$row['id']}' title='Show all from this member'><img src='{$ibforums->adskin->img_url}/acp_search.gif' border='0' alt='..by id'></a>&nbsp;<b><a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?act=Profile&MID={$row['id']}' title='Members profile (new window)' target='blank'>{$row['name']}</a></b>",
														  "<a href='javascript:pop_win(\"&act=emaillog&code=viewemail&id={$row['email_id']}\",400,400)' title='Read email'>{$row['email_subject']}</a>",
														  "<a href='{$ibforums->base_url}&act=emaillog&code=list&type=toid&id={$row['to_id']}' title='Show all sent to this member'><img src='{$ibforums->adskin->img_url}/acp_search.gif' border='0' alt='..by id'></a>&nbsp;<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?act=Profile&MID={$row['to_id']}'  title='Members profile (new window)' target='blank'>{$row['to_name']}</a>",
														  "{$row['email_date']}",
												 )      );


			}
		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic("<center>No results</center>");
		}

		$ibforums->html .= $ibforums->adskin->add_td_basic('<div style="float:left;width:auto"><input type="submit" value="Remove Checked" id="button" />&nbsp;<input type="checkbox" id="checkbox" name="type" value="all" />&nbsp;Remove all?</div><div align="right">'.$links.'</div></form>', 'left', 'pformstrip');

		$ibforums->html .= $ibforums->adskin->end_table();




		//-----------------------------------------
		//-------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'list'     ),
												  2 => array( 'act'   , 'emaillog'       ),
									     )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Email Logs" );

		$form_array = array(
							  0 => array( 'subject'    , 'Email Subject'    ),
							  1 => array( 'content'    , 'Email Body' ),
							  2 => array( 'email_from' , 'From Email Address' ),
							  3 => array( 'email_to'   , 'To Email Address'   ),
							  4 => array( 'name_from'  , 'From Member Name'),
							  5 => array( 'name_to'    , 'To Member Name' ),
						   );

		$type_array = array(
							  0 => array( 'exact'      , 'is exactly' ),
							  1 => array( 'loose'      , 'contains'   ),
						   );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search where</b> &nbsp;"
												  . $ibforums->adskin->form_dropdown( "type", $form_array) ." "
												  . $ibforums->adskin->form_dropdown( "match", $type_array) ." "
												  . $ibforums->adskin->form_input( "string"),

								 )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->nav[] = array( 'act=emaillog', 'Email Logs (Show all)' );

		$ibforums->admin->output();

	}



}


?>