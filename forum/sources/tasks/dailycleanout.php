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
// Prunes back subscribed topics...
//+--------------------------------------------------------------------------

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class task_item
{
	var $class     = "";
	var $root_path = "";
	var $task      = "";

	/*-------------------------------------------------------------------------*/
	// Our 'auto_run' function
	// ADD CODE HERE
	/*-------------------------------------------------------------------------*/

	function run_task()
	{
		global $DB, $ibforums, $std;

		//-----------------------------------------
		// Delete old subscriptions
		//-----------------------------------------

		$deleted = 0;
		$trids   = array();

		if ($ibforums->vars['subs_autoprune'] > 0)
 		{
			$time = time() - ($ibforums->vars['subs_autoprune'] * 86400);

			$DB->cache_add_query( 'ucp_tracker_prune', array( 'time' => $time ) );
			$DB->cache_exec_query();

			while ( $r = $DB->fetch_row() )
			{
				$trids[] = $r['trid'];
			}

			if (count($trids) > 0)
			{
				$DB->simple_exec_query( array( 'delete', 'tracker', 'where' => "trid IN (".implode(",",$trids).")" ) );
			}

			$deleted = intval( count($trids) );
 		}

 		//-----------------------------------------
 		// Remove read topics
 		//-----------------------------------------

 		$ibforums->vars['db_topic_read_cutoff'] = intval($ibforums->vars['db_topic_read_cutoff']);

 		if ( $ibforums->vars['db_topic_read_cutoff'] > 0 )
 		{
 			$time = time() - ( $ibforums->vars['db_topic_read_cutoff'] * 86400 );

 			$DB->simple_exec_query( array( 'delete' => 'topics_read', 'where' => "read_date < $time" ) );

 			$topics_deleted = $DB->get_affected_rows();
 		}

		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------

		$this->class->append_task_log( $this->task, "$deleted subscriptions pruned and {$topics_deleted} db read topic entries deleted" );
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