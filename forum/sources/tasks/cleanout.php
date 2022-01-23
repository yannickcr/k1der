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
// Clean out 'dead' sessions, validations, registration image entires, etc
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
		// Delete reg_anti_spam
		//-----------------------------------------

		$date = time() - (60*60*6);

		$DB->simple_exec_query( array( 'delete' => 'reg_antispam', 'where' => 'ctime < '.$date ) );

		//-----------------------------------------
		// Delete old sessions
		//-----------------------------------------

		$date = $ibforums->vars['session_expiration'] ? (time() - $ibforums->vars['session_expiration']) : (time() - 3600);

		$DB->simple_exec_query( array( 'delete' => 'sessions',  'where'  => "running_time < {$date}" ) );

		//-----------------------------------------
		// Delete old searches
		//-----------------------------------------

		$date = time() - (60*60*24);

		$DB->simple_exec_query( array( 'delete' => 'search_results',  'where'  => "search_date < {$date}" ) );

		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------

		$this->class->append_task_log( $this->task, 'Old reg_images, sessions and search results removed' );
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