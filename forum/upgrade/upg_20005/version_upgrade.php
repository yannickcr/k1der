<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > IPB UPGRADE MODULE:: IPB 2.0.0 PDR1 -> PDR 2
|   > Script written by Matt Mecham
|   > Date started: 23rd April 2004
|   > "So what, pop is dead - it's no great loss.
	   So many facelifts, it's face flew off"
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class version_upgrade
{
	var $this_version = '20005';
	var $upgrade_from = '20004';
	var $first_step   = 'update your database to include the new schematic modifications.';
	var $md5_check    = '';
	var $base_url     = '';
	var $mod_to_run   = '';

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function version_upgrade()
	{
		global $ibforums, $std, $DB;

		$this->md5_check = $std->return_md5_check();

		$this->base_url  = "index.php?act=work&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}";

		if ( is_array( $ibforums->modules_to_run ) and count( $ibforums->modules_to_run ) )
		{
			$tmp = array_shift( $ibforums->modules_to_run );

			$this->mod_to_run = implode( ', ', $ibforums->modules_to_run );
		}

		if ( ! $this->mod_to_run )
		{
			$this->mod_to_run = 'None';
		}
	}

	/*-------------------------------------------------------------------------*/
	// Auto run..
	/*-------------------------------------------------------------------------*/

	function auto_run()
	{
		global $ibforums, $std, $DB;

		//--------------------------------
		// What are we doing?
		//--------------------------------

		switch( $ibforums->input['workact'] )
		{
			case 'sql':
				$this->upgrade_sql();
				break;
			default:
				$this->upgrade_intro();
				break;
		}
	}


	/*-------------------------------------------------------------------------*/
	// SQL
	/*-------------------------------------------------------------------------*/

	function upgrade_sql()
	{
		global $ibforums, $std, $DB;

		$cnt = 0;
		$sql = $this->_get_sql();

		foreach( $sql as $q )
		{
			$DB->query( $q );

			$cnt++;
		}

		//--------------------------------
		// Next page...
		//--------------------------------

		$ibforums->core->redirect( "index.php?act=done&loginkey={$ibforums->input['loginkey']}&securekey={$ibforums->input['securekey']}&mid={$ibforums->input['mid']}", "$cnt queries run...." );

	}

	/*-------------------------------------------------------------------------*/
	// INTRO
	/*-------------------------------------------------------------------------*/

	function upgrade_intro()
	{
		global $ibforums, $std, $DB;

		$ibforums->template->content .= "
			<div class='tableborder'>
			 <div class='maintitle'>Welcome to the IPB Upgrade System</div>
			 <div class='tdrow1' style='padding:6px'>This upgrade module will upgrade you from <b>{$ibforums->versions[$this->upgrade_from]}</b> to <b>{$ibforums->versions[$this->this_version]}</b>
			 <br /><br />This first step will {$this->first_step}
			 <br /><br />
			 <div align='center'><span style='font-weight:bold;font-size:14px'>&raquo; <a href='{$this->base_url}&workact=sql'>Proceed...</a></span></div>
			 </div>
			</div>
			<br />
			<div align='center'>Modules to run after this module: {$this->mod_to_run}</div>
			";

		$ibforums->template->output();

	}

	/*-------------------------------------------------------------------------*/
	// SQL (ARRAY)
	/*-------------------------------------------------------------------------*/

	function _get_sql()
	{

		$SQL = array();

		$SQL[] = "INSERT INTO ibf_task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('Send Bulk Mail', 'bulkmail.php', 1086706080, -1, -1, -1, -1, '61359ac93eb93ebbd935a4e275ade2db', 0, 'Dynamically assigned, no need to edit or change', 0, 'bulkmail', 1);";
		$SQL[] = "INSERT INTO ibf_task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('Daily Topic &amp; Forum Digest', 'dailydigest.php', 1086912600, -1, -1, 0, 10, '723cab2aae32dd5d04898b1151038846', 1, 'Emails out daily topic &amp; forum digest emails', 1, 'dailydigest', 0);";
		$SQL[] = "INSERT INTO ibf_task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('Weekly Topic &amp; Forum Digest', 'weeklydigest.php', 1087096200, 0, -1, 3, 10, '7e7fccd07f781bdb24ac108d26612931', 1, 'Emails weekly topic &amp; forum digest emails', 1, 'weeklydigest', 0);";


		return $SQL;
	}

}


?>