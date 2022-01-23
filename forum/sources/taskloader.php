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
|   > Task loader module
|   > Module written by Matt Mecham
|   > Date started: 28th January 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Thu 20 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class taskloader
{

    function auto_run()
    {
		global $ibforums, $DB, $std, $print;

    	@set_time_limit(1200);

    	//-----------------------------------------
		// Require and run
		//-----------------------------------------

		chdir( ROOT_PATH );
		$ROOT_PATH = getcwd() .'/';

		require_once( ROOT_PATH.'sources/lib/task_functions.php' );

		$functions = new task_functions();
    	$functions->root_path = $ROOT_PATH;

    	//-----------------------------------------
		// Check shutdown functions
		//-----------------------------------------

    	if ( USE_SHUTDOWN )
		{
			register_shutdown_function( array( &$functions, 'run_task') );
		}
    	else
    	{
    		$functions->run_task();
    	}

    	if ( $functions->type != 'cron' )
    	{
    		//-----------------------------------------
    		// Print out the 'blank' gif
    		//-----------------------------------------

    		@header( "Content-Type: image/gif" );
    		print base64_decode( "R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" );
    	}
 	}
}

?>