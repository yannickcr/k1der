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
|   > Statistical functions
|   > Module written by Matt Mecham
|   > Date started: 4th July 2002
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

class ad_statistics {

	var $base_url;
	var $month_names = array();

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

		$this->month_names = array( 1 => 'January', 'February', 'March'     , 'April'  , 'May'     , 'June',
										 'July'   , 'August'  , 'September' , 'October', 'November', 'December'
								  );

		switch($ibforums->input['code'])
		{
			case 'show_reg':
				$this->result_screen('reg');
				break;

			case 'show_topic':
				$this->result_screen('topic');
				break;

			case 'topic':
				$this->main_screen('topic');
				break;

			//-----------------------------------------

			case 'show_post':
				$this->result_screen('post');
				break;

			case 'post':
				$this->main_screen('post');
				break;

			//-----------------------------------------

			case 'show_msg':
				$this->result_screen('msg');
				break;

			case 'msg':
				$this->main_screen('msg');
				break;

				//-----------------------------------------

			case 'show_views':
				$this->show_views();
				break;

			case 'views':
				$this->main_screen('views');
				break;

			//-----------------------------------------

			default:
				$this->main_screen('reg');
				break;
		}

	}

	//select forum_id, SUM(views)from ibf_topics group by forum_id order by forum_id

	//-----------------------------------------
	//| Results screen
	//-----------------------------------------

	function show_views()
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Statistic Center Results";

		$ibforums->admin->page_detail = "Showing topic view statistics";

		//-----------------------------------------

		if ( ! checkdate($ibforums->input['to_month']   ,$ibforums->input['to_day']   ,$ibforums->input['to_year']) )
		{
			$ibforums->admin->error("The 'Date To:' time is incorrect, please check the input and try again");
		}

		if ( ! checkdate($ibforums->input['from_month'] ,$ibforums->input['from_day'] ,$ibforums->input['from_year']) )
		{
			$ibforums->admin->error("The 'Date From:' time is incorrect, please check the input and try again");
		}

		//-----------------------------------------

		$to_time   = mktime(12 ,0 ,0 ,$ibforums->input['to_month']   ,$ibforums->input['to_day']   ,$ibforums->input['to_year']  );
		$from_time = mktime(12 ,0 ,0 ,$ibforums->input['from_month'] ,$ibforums->input['from_day'] ,$ibforums->input['from_year']);


		$human_to_date   = getdate($to_time);
		$human_from_date = getdate($from_time);

		$DB->cache_add_query( 'statistics_show_views', array( 'from_time' => $from_time, 'to_time' => $to_time, 'sortby' => $ibforums->input['sortby'] ) );
		$DB->cache_exec_query();

		$running_total = 0;
		$max_result    = 0;

		$results       = array();

		$ibforums->adskin->td_header[] = array( "Forum"   , "40%" );
		$ibforums->adskin->td_header[] = array( "Result"  , "50%" );
		$ibforums->adskin->td_header[] = array( "Views"   , "10%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( "Topic Views"
										    ." ({$human_from_date['mday']} {$this->month_names[$human_from_date['mon']]} {$human_from_date['year']} to"
										    ." {$human_to_date['mday']} {$this->month_names[$human_to_date['mon']]} {$human_to_date['year']})"
										  );

		if ( $DB->get_num_rows() )
		{

			while ($row = $DB->fetch_row() )
			{

				if ( $row['result_count'] >  $max_result )
				{
					$max_result = $row['result_count'];
				}

				$running_total += $row['result_count'];

				$results[] = array(
									 'result_name'     => $row['result_name'],
									 'result_count'    => $row['result_count'],
								  );

			}

			foreach( $results as $pOOp => $data )
			{

    			$img_width = intval( ($data['result_count'] / $max_result) * 100 - 8);

    			if ($img_width < 1)
    			{
    				$img_width = 1;
    			}

    			$img_width .= '%';

    			$ibforums->html .= $ibforums->adskin->add_td_row( array( $data['result_name'],
    													  "<img src='{$ibforums->adskin->img_url}/bar_left.gif' border='0' width='4' height='11' align='middle' alt=''><img src='{$ibforums->adskin->img_url}/bar.gif' border='0' width='$img_width' height='11' align='middle' alt=''><img src='{$ibforums->adskin->img_url}/bar_right.gif' border='0' width='4' height='11' align='middle' alt=''>",
												  		  "<center>".$data['result_count']."</center>",
									             )      );
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( '&nbsp;',
													 "<div align='right'><b>Total</b></div>",
													 "<center><b>".$running_total."</b></center>",
											)      );

		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No results found", "center" );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	//| Results screen
	//-----------------------------------------

	function result_screen($mode='reg')
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Statistic Center Results";

		$ibforums->admin->page_detail = "&nbsp;";

		//-----------------------------------------

		if ( ! checkdate($ibforums->input['to_month']   ,$ibforums->input['to_day']   ,$ibforums->input['to_year']) )
		{
			$ibforums->admin->error("The 'Date To:' time is incorrect, please check the input and try again");
		}

		if ( ! checkdate($ibforums->input['from_month'] ,$ibforums->input['from_day'] ,$ibforums->input['from_year']) )
		{
			$ibforums->admin->error("The 'Date From:' time is incorrect, please check the input and try again");
		}

		//-----------------------------------------

		$to_time   = mktime(12 ,0 ,0 ,$ibforums->input['to_month']   ,$ibforums->input['to_day']   ,$ibforums->input['to_year']  );
		$from_time = mktime(12 ,0 ,0 ,$ibforums->input['from_month'] ,$ibforums->input['from_day'] ,$ibforums->input['from_year']);


		$human_to_date   = getdate($to_time);
		$human_from_date = getdate($from_time);

		//-----------------------------------------

		if ($mode == 'reg')
		{
			$table     = 'Registration Statistics';

			$sql_table = 'members';
			$sql_field = 'joined';

			$ibforums->admin->page_detail = "Showing the number of users registered. (Note: All times based on GMT)";
		}
		else if ($mode == 'topic')
		{
			$table     = 'New Topic Statistics';

			$sql_table = 'topics';
			$sql_field = 'start_date';

			$ibforums->admin->page_detail = "Showing the number of topics started. (Note: All times based on GMT)";
		}
		else if ($mode == 'post')
		{
			$table     = 'Post Statistics';

			$sql_table = 'posts';
			$sql_field = 'post_date';

			$ibforums->admin->page_detail = "Showing the number of posts. (Note: All times based on GMT)";
		}
		else if ($mode == 'msg')
		{
			$table     = 'PM Sent Statistics';

			$sql_table = 'message_topics';
			$sql_field = 'mt_date';

			$ibforums->admin->page_detail = "Showing the number of sent messages. (Note: All times based on GMT)";
		}


	  	switch ($ibforums->input['timescale'])
	  	{
	  		case 'daily':
	  			$sql_date = "%w %U %m %Y";
		  		$php_date = "F jS - Y";
		  		break;

		  	case 'monthly':
		  		$sql_date = "%m %Y";
		  	    $php_date = "F Y";
		  	    break;

		  	default:
		  		// weekly
		  		$sql_date = "%U %Y";
		  		$php_date = " [F Y]";
		  		break;
		}

		$DB->cache_add_query( 'statistics_result_screen', array( 'from_time' => $from_time,
																 'to_time'   => $to_time,
																 'sortby'    => $ibforums->input['sortby'],
																 'sql_field' => $sql_field,
																 'sql_table' => $sql_table,
																 'sql_date'  => $sql_date ) );
		$DB->cache_exec_query();

		$running_total = 0;
		$max_result    = 0;

		$results       = array();

		$ibforums->adskin->td_header[] = array( "Date"    , "20%" );
		$ibforums->adskin->td_header[] = array( "Result"  , "70%" );
		$ibforums->adskin->td_header[] = array( "Count"   , "10%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( ucfirst($ibforums->input['timescale'])
										    ." ".$table
										    ." ({$human_from_date['mday']} {$this->month_names[$human_from_date['mon']]} {$human_from_date['year']} to"
										    ." {$human_to_date['mday']} {$this->month_names[$human_to_date['mon']]} {$human_to_date['year']})"
										  );

		if ( $DB->get_num_rows() )
		{

			while ($row = $DB->fetch_row() )
			{

				if ( $row['result_count'] >  $max_result )
				{
					$max_result = $row['result_count'];
				}

				$running_total += $row['result_count'];

				$results[] = array(
									 'result_maxdate'  => $row['result_maxdate'],
									 'result_count'    => $row['result_count'],
									 'result_time'     => $row['result_time'],
								  );

			}

			foreach( $results as $pOOp => $data )
			{

    			$img_width = intval( ($data['result_count'] / $max_result) * 100 - 8);

    			if ($img_width < 1)
    			{
    				$img_width = 1;
    			}

    			$img_width .= '%';

    			if ($ibforums->input['timescale'] == 'weekly')
    			{
    				$date = "Week #".strftime("%W", $data['result_maxdate']) . date( $php_date, $data['result_maxdate'] );
    			}
    			else
    			{
    				$date = date( $php_date, $data['result_maxdate'] );
    			}

    			$ibforums->html .= $ibforums->adskin->add_td_row( array( $date,
    													  "<img src='{$ibforums->adskin->img_url}/bar_left.gif' border='0' width='4' height='11' align='middle' alt=''><img src='{$ibforums->adskin->img_url}/bar.gif' border='0' width='$img_width' height='11' align='middle' alt=''><img src='{$ibforums->adskin->img_url}/bar_right.gif' border='0' width='4' height='11' align='middle' alt=''>",
												  		  "<center>".$data['result_count']."</center>",
									             )      );
			}

			$ibforums->html .= $ibforums->adskin->add_td_row( array( '&nbsp;',
													 "<div align='right'><b>Total</b></div>",
													 "<center><b>".$running_total."</b></center>",
											)      );

		}
		else
		{
			$ibforums->html .= $ibforums->adskin->add_td_basic( "No results found", "center" );
		}

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();

	}

	//-----------------------------------------
	//| Date selection screen
	//-----------------------------------------

	function main_screen($mode='reg')
	{
		global $ibforums, $DB,  $std;

		$ibforums->admin->page_title = "Statistic Center";

		$ibforums->admin->page_detail = "Please define the date ranges and other options below.<br>Note: The statistics generated are based on the information currently held in the database, they do not take into account pruned forums or delete posts, etc.";

		if ($mode == 'reg')
		{
			$form_code = 'show_reg';

			$table     = 'Registration Statistics';
		}
		else if ($mode == 'topic')
		{
			$form_code = 'show_topic';

			$table     = 'New Topic Statistics';
		}
		else if ($mode == 'post')
		{
			$form_code = 'show_post';

			$table     = 'Post Statistics';
		}
		else if ($mode == 'msg')
		{
			$form_code = 'show_msg';

			$table     = 'PM Statistics';
		}
		else if ($mode == 'views')
		{
			$form_code = 'show_views';

			$table     = 'Topic View Statistics';
		}


		$old_date = getdate(time() - (3600 * 24 * 90));
		$new_date = getdate(time() + (3600 * 24));


		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_form( array( 1 => array( 'code'  , $form_code  ),
												  2 => array( 'act'   , 'stats'     ),
									     )      );

		//-----------------------------------------

		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$ibforums->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		//-----------------------------------------

		$ibforums->html .= $ibforums->adskin->start_table( $table );


		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Date From</b>" ,
												  $ibforums->adskin->form_dropdown( "from_month" , $this->make_month(), $old_date['mon']  ).'&nbsp;&nbsp;'.
												  $ibforums->adskin->form_dropdown( "from_day"   , $this->make_day()  , $old_date['mday'] ).'&nbsp;&nbsp;'.
												  $ibforums->adskin->form_dropdown( "from_year"  , $this->make_year() , $old_date['year'] )
									     )      );

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Date To</b>" ,
												  $ibforums->adskin->form_dropdown( "to_month" , $this->make_month(), $new_date['mon']  ).'&nbsp;&nbsp;'.
												  $ibforums->adskin->form_dropdown( "to_day"   , $this->make_day()  , $new_date['mday'] ).'&nbsp;&nbsp;'.
												  $ibforums->adskin->form_dropdown( "to_year"  , $this->make_year() , $new_date['year'] )
									     )      );

		if ($mode != 'views')
		{
			$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Time scale</b>" ,
													  $ibforums->adskin->form_dropdown( "timescale" , array( 0 => array( 'daily', 'Daily'), 1 => array( 'weekly', 'Weekly' ), 2 => array( 'monthly', 'Monthly' ) ) )
											 )      );
		}

		$ibforums->html .= $ibforums->adskin->add_td_row( array( "<b>Result Sorting</b>" ,
												  $ibforums->adskin->form_dropdown( "sortby" , array( 0 => array( 'asc', 'Ascending - Oldest dates first'), 1 => array( 'desc', 'Descending - Newest dates first' ) ), 'desc' )
									     )      );

		$ibforums->html .= $ibforums->adskin->end_form("Show");

		$ibforums->html .= $ibforums->adskin->end_table();

		$ibforums->admin->output();


	}

	//-----------------------------------------

	function make_year()
	{
		$time_now = getdate();

		$return = array();

		$start_year = 2002;

		$latest_year = intval($time_now['year']);

		if ($latest_year == $start_year)
		{
			$start_year -= 1;
		}

		for ( $y = $start_year; $y <= $latest_year; $y++ )
		{
			$return[] = array( $y, $y);
		}

		return $return;
	}

	//-----------------------------------------

	function make_month()
	{
		$return = array();

		for ( $m = 1 ; $m <= 12; $m++ )
		{
			$return[] = array( $m, $this->month_names[$m] );
		}

		return $return;
	}

	//-----------------------------------------

	function make_day()
	{
		$return = array();

		for ( $d = 1 ; $d <= 31; $d++ )
		{
			$return[] = array( $d, $d );
		}

		return $return;
	}



}


?>