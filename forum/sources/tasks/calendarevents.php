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
|   > TASK SCRIPT: Test
|   > Script written by Matt Mecham
|   > Date started: 28th January 2004
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// THIS TASKS OPERATIONS:
// Gathers birthday and calendar events for a few days and caches them
//+--------------------------------------------------------------------------

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class task_item
{
	var $class        = "";
	var $root_path    = "";
	var $task         = "";
	var $restrict_log = 0;

	/*-------------------------------------------------------------------------*/
	// Our 'auto_run' function
	// ADD CODE HERE
	/*-------------------------------------------------------------------------*/

	function run_task()
	{
		global $DB, $ibforums, $std;

		//-----------------------------------------
		// Cache calendar info
		//-----------------------------------------

		$calendar  = array();
		$birthdays = array();

		$a = explode( ',', gmdate( 'Y,n,j,G,i,s', time() ) );

		$day         = $a[2];
		$month       = $a[1];
		$year        = $a[0];
		$daysinmonth = date( 't', time() );

		//-----------------------------------------
		// Get 24hr before and 24hr after to make
		// sure we don't break any timezones
		//-----------------------------------------

		$last_day   = $day - 1;
		$last_month = $month;
		$last_year  = $year;
		$next_day   = $day + 1;
		$next_month = $month;
		$next_year  = $year;

		//-----------------------------------------
		// Calculate dates..
		//-----------------------------------------

		if ( $last_day == 0 )
		{
			$last_month -= 1;
			$last_day   = date( 't', time() - 86400 );
		}

		if ( $last_month == 0 )
		{
			$last_month = 12;
			$last_year  -= 1;
		}

		if ( $next_day > date( 't', time() ) )
		{
			$next_month += 1;
			$next_day   = 1;
		}

		if ( $next_month == 13 )
		{
			$next_month = 1;
			$next_year += 1;
		}

		//-----------------------------------------
		// Grab birthdays
		//-----------------------------------------

		$DB->simple_construct( array( 'select' => 'id, name, mgroup, bday_day, bday_month, bday_year',
									  'from'   => 'members',
									  'where'  => "( bday_day=$last_day AND bday_month=$last_month )
									  			   or ( bday_day=$day AND bday_month=$month )
									  			   or ( bday_day=$next_day AND bday_month=$next_month )"
							 )      );

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$birthdays[ $r['id'] ] = $r;
		}

		//-----------------------------------------
		// Grab Calendar (-24hr + 24hr)
		//-----------------------------------------

		if ($ibforums->vars['calendar_limit'] < 2)
		{
			$ibforums->vars['calendar_limit'] = 2;
		}

		$our_unix = mktime( 0, 0, 1, $month, $day, $year) - 86400;
		$max_date = $our_unix + ( ($ibforums->vars['calendar_limit'] + 1 ) * 86400);

		$DB->simple_construct( array( 'select' => 'eventid, title, read_perms, priv_event, userid, unix_stamp',
									  'from'   => 'calendar_events',
									  'where'  => "unix_stamp > $our_unix and unix_stamp < $max_date",
									  'order'  => "unix_stamp"
							 )      );

		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$calendar[ $r['eventid'] ] = $r;
		}

		//-----------------------------------------
        // Get recurring events
        //-----------------------------------------

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'calendar_events',
									  'where'  => "event_repeat=1 AND ( repeat_unit IN ('w','m') OR (repeat_unit='y' AND ( month={$last_month} OR month={$month} OR month={$next_month} ) ) )" ) );
		$DB->simple_exec();

        while ( $rc = $DB->fetch_row() )
        {
        	$recur_start_unix = mktime( 0, 0, 1, $rc['month'], $rc['mday'], $rc['year'] );
			$recur_end_unix   = mktime( 0, 0, 1, $rc['end_month'], $rc['end_day'], $rc['end_year'] );

			//-----------------------------------------
			// Out of range END?
			//-----------------------------------------

			if ( $recur_end_unix < $max_date )
			{
				continue;
			}

			//-----------------------------------------
			// Out of range START?
			//-----------------------------------------

			if ( $recur_start_unix > $our_unix )
			{
				continue;
			}

			$rc['_recurring'] = 1;
			$rc['unix_stamp'] = $max_date - 3600;

			$calendar[ $rc['eventid'] ] = $rc;
		}

		//-----------------------------------------
		// Save...
		//-----------------------------------------

		$DB->simple_exec_query( array( 'delete' => 'cache_store', 'where' => "cs_key IN ('birthdays', 'calendar')" ) );

		$DB->do_insert( 'cache_store', array( 'cs_array' => 1, 'cs_key' => 'birthdays', 'cs_value' => addslashes(serialize($birthdays)) ) );
		$DB->do_insert( 'cache_store', array( 'cs_array' => 1, 'cs_key' => 'calendar' , 'cs_value' => addslashes(serialize($calendar)) ) );

		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------

		if ( ! $this->restrict_log )
		{
			$this->class->append_task_log( $this->task, intval(count($birthdays))." birthdays cached, ".intval(count($calendar))." events cached" );
		}
	}

	/*-------------------------------------------------------------------------*/
	// register_class
	// LEAVE ALONE
	/*-------------------------------------------------------------------------*/

	function register_class(&$class)
	{
		$this->class = $class;

		$this->root_path = $this->class->root_path;
	}

	/*-------------------------------------------------------------------------*/
	// pass_task
	// LEAVE ALONE
	/*-------------------------------------------------------------------------*/

	function pass_task( $this_task )
	{
		$this->task = $this_task;
	}


}
?>