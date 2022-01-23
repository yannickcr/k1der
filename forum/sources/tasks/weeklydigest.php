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
		// Send daily digests...
		//-----------------------------------------

		require_once( $this->root_path.'sources/lib/digest_functions.php' );
		$digest = new digest_functions(  $this->root_path );

		$digest->digest_time = 'weekly';
		$digest->digest_type = 'topic';
		$digest->run_digest();

		$digest->digest_time = 'weekly';
		$digest->digest_type = 'forum';
		$digest->run_digest();

		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------

		$this->class->append_task_log( $this->task, 'Weekly Topic & Forum Digest Sent' );
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