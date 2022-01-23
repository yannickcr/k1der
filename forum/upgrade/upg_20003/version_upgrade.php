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
	var $this_version = '20003';
	var $upgrade_from = '20002';
	var $first_step   = 'update your templates.';
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
			default:
				$this->upgrade_intro();
				break;
		}
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
			 <div align='center'><span style='font-weight:bold;font-size:14px'>&raquo; <a href='{$this->base_url}&act=done'>Proceed...</a></span></div>
			 </div>
			</div>
			<br />
			<div align='center'>Modules to run after this module: {$this->mod_to_run}</div>
			";

		$ibforums->template->output();

	}


}


?>