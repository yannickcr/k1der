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
|   > TASK SCRIPT: Send Bulk Mail
|   > Script written by Matt Mecham
|   > Date started: 28th January 2004
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// THIS TASKS OPERATIONS:
// Sends out bulk mail
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

		define( 'IN_ACP', 1 );

		//-----------------------------------------
		// Load Bulk Mailer thing
		//-----------------------------------------

		require_once( $this->root_path.'sources/admin/ad_postoffice.php' );
		$po = new ad_postoffice();

		$po->mail_send_process( $this->root_path );
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