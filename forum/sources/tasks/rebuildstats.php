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
// Rebuilds topics, posts, forum, members, last reg. member counts
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
		// Get current stats...
		//-----------------------------------------

		$stats = array();

		$r = $DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='stats'" ) );

		$tmp = unserialize( $std->txt_safeslashes($r['cs_value']) );

		if ( is_array( $tmp ) and count( $tmp ) )
		{
			foreach( $tmp as $k => $v )
			{
				$stats[ $k ] = stripslashes($v);
			}
		}

		unset( $tmp );

		//-----------------------------------------
		// Rebuild stats
		//-----------------------------------------

		$r = $DB->simple_exec_query( array( 'select' => 'count(*) as posts', 'from' => 'posts', 'where' => 'queued <> 1' ) );
		$stats['total_replies'] = intval($r['posts']);

		$r = $DB->simple_exec_query( array( 'select' => 'count(*) as topics', 'from' => 'topics', 'where' => 'approved = 1' ) );
		$stats['total_topics'] = intval($r['topics']);

		$stats['total_replies'] -= $stats['total_topics'];

		$r = $DB->simple_exec_query( array( 'select' => 'count(*) as members', 'from' => 'members', 'where' => "mgroup <> ".$ibforums->vars['auth_group'] ) );
		$stats['mem_count'] = intval( $r['members'] );

		$r = $DB->simple_exec_query( array( 'select' => 'id, name',
										    'from'   => 'members',
										    'where'  => "mgroup <> ".$ibforums->vars['auth_group'],
										    'order'  => 'id DESC',
										    'limit'  => array(0,1)
								   )      );

		$stats['last_mem_name'] = $r['name'];
		$stats['last_mem_id']   = $r['id'];

		if ( count($stats) > 0 )
		{
			$DB->obj['use_shutdown']  = 0;
			$ibforums->cache['stats'] = $stats;
			$std->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 1 ) );
		}

		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------

		$this->class->append_task_log( $this->task, 'Statistics rebuilt' );
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