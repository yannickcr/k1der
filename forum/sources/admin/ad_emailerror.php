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
|   > Email Error Logs Stuff
|   > Module written by Matt Mecham
|   > Date started: 7th April 2004
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


class ad_emailerror
{


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

		$DB->simple_construct( array( 'select' => '*', 'from' => 'mail_error_logs', 'where' => "mlog_id=$id" ) );
		$DB->simple_exec();

		if ( ! $row = $DB->fetch_row() )
		{
			$ibforums->admin->error("Could not resolve the email ID, please try again ($id)");
		}

		$ibforums->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( $row['mlog_subject'] );

		$row['mlog_date']    = $ibforums->admin->get_date( $row['mlog_date'], 'LONG' );
		$row['mlog_content'] = nl2br($row['mlog_content']);

		$row['mlog_msg']        = $row['mlog_msg']        ? $row['mlog_msg']        : '<em>No Info</em>';
		$row['mlog_code']       = $row['mlog_code']       ? $row['mlog_code']       : '<em>No Info</em>';
		$row['mlog_smtp_error'] = $row['mlog_smtp_error'] ? $row['mlog_smtp_error'] : '<em>No Info</em>';

		$ibforums->html .= $ibforums->adskin->add_td_row( array(
																  "<strong>From:</strong> &lt;{$row['mlog_from']}&gt;
																  <br /><strong>To:</strong> &lt;{$row['mlog_to']}&gt;
																  <br /><strong>Sent:</strong> {$row['mlog_date']}
																  <br /><strong>Subject:</strong> {$row['mlog_subject']}
																  <hr />
																  <br />{$row['mlog_content']}....
																  <hr />
																  <br />IPB ERROR: {$row['mlog_msg']}
																  <br />SMTP CODE: {$row['mlog_code']}
																  <br />SMTP ERROR: {$row['mlog_smtp_error']}
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
			$DB->simple_exec_query( array( 'delete' => 'mail_error_logs' ) );
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

			$DB->simple_exec_query( array( 'delete' => 'mail_error_logs', 'where' => "mlog_id IN (".implode(',', $ids ).")" ) );
		}

		$ibforums->admin->save_log("Removed email error log entries");

		$std->boink_it($ibforums->base_url."&act=emailerror");
	}

	//-----------------------------------------
	// SHOW EMAIL ERROR LOGS
	//-----------------------------------------

	function list_current()
	{
		global $ibforums, $DB,  $std;

		$ibforums->html .= $ibforums->adskin->js_pop_win();

		$form_array = array();

		$start = intval($ibforums->input['st']);

		$ibforums->admin->page_detail = "Stored email error logs";
		$ibforums->admin->page_title  = "Email Error Logs Manager";

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
				case 'subject':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "mlog_subject LIKE '%{$string}%'" : "mlog_subject='{$string}'";
					break;
				case 'email_from':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "mlog_from LIKE '%{$string}%'" : "mlog_from='{$string}'";
					break;
				case 'email_to':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "mlog_to LIKE '%{$string}%'" : "mlog_to='{$string}'";
					break;
				case 'error':
					$string = urldecode($ibforums->input['string']);
					if ( $string == "" )
					{
						$ibforums->admin->error("You must enter something to search by");
					}
					$url_query[] = 'type='.$ibforums->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $ibforums->input['match'] == 'loose' ? "mlog_msg LIKE '%{$string}%' or mlog_smtp_msg LIKE '%{$string}%'" : "mlog_msg='{$string} or mlog_smtp_msg='{$string}'";
					break;

				default:
					//
					break;
			}
		}

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

		$DB->simple_construct( array( 'select' => 'count(*) as cnt', 'from' => 'mail_error_logs', 'where' => $dbe ) );
		$DB->simple_exec();

		$count = $DB->fetch_row();

		$links = $std->build_pagelinks( array( 'TOTAL_POSS'  => $count['cnt'],
											   'PER_PAGE'    => 25,
											   'CUR_ST_VAL'  => $start,
											   'L_SINGLE'    => "Single Page",
											   'L_MULTI'     => "Pages: ",
											   'BASE_URL'    => $ibforums->base_url.'&act=emailerror'.$url,
											 )
									  );

		$DB->simple_construct( array( 'select' => '*', 'from' => 'mail_error_logs', 'where' => $dbe, 'order' => 'mlog_date DESC', 'limit' => array( $start, 25 ) ) );
		$DB->simple_exec();

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'remove'     ),
												                 2 => array( 'act'   , 'emailerror' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"         , "1%" );
		$ibforums->adskin->td_header[] = array( "To"             , "20%" );
		$ibforums->adskin->td_header[] = array( "Subject"        , "20%" );
		$ibforums->adskin->td_header[] = array( "Error MSG"      , "30%" );
		$ibforums->adskin->td_header[] = array( "Date"           , "20%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Logged Emails Errors" );

		if ( $DB->get_num_rows() )
		{
			while ( $row = $DB->fetch_row() )
			{
				$row['mlog_date'] = $ibforums->admin->get_date( $row['mlog_date'], 'SHORT' );

				$ibforums->html .= $ibforums->adskin->add_td_row( array(
																		"<center><input type='checkbox' class='checkbox' name='id_{$row['mlog_id']}' value='1' /></center>",
																		'<b>'.$row['mlog_to'].'</b>',
																		"<a href='javascript:pop_win(\"&act=emailerror&code=viewemail&id={$row['mlog_id']}\", \"{$row['mlog_id']}\",400,350)' title='Read email'>".$row['mlog_subject']."</a>",
																		$row['mlog_msg'].'<br />'.$row['mlog_code']. '&nbsp;'.$row['mlog_smtp_msg'],
																		$row['mlog_date'],
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

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , 'list'       ),
												                 2 => array( 'act'   , 'emailerror' ),
									                    )      );

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "100%" );

		$ibforums->html .= $ibforums->adskin->start_table( "Search Email Error Logs" );

		$form_array = array(
							  0 => array( 'subject'    , 'Email Subject'      ),
							  2 => array( 'email_from' , 'From Email Address' ),
							  3 => array( 'email_to'   , 'To Email Address'   ),
							  4 => array( 'error'      , 'Error Message'      ),
						   );

		$type_array = array(
							  0 => array( 'exact'      , 'is exactly' ),
							  1 => array( 'loose'      , 'contains'   ),
						   );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Search where</b> &nbsp;"
																 . $ibforums->adskin->form_dropdown( "type" , $form_array, $_POST['type'])  ." "
																 . $ibforums->adskin->form_dropdown( "match", $type_array, $_POST['match']) ." "
																 . $ibforums->adskin->form_input( "string", $_POST['string']),
													   )      );

		$ibforums->html .= $ibforums->adskin->end_form("Search");

		$ibforums->html .= $ibforums->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$ibforums->admin->nav[] = array( 'act=emailerror', 'Email Error Logs (Show all)' );

		$ibforums->admin->output();

	}



}


?>