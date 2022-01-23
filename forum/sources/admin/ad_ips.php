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
|   > IPS Remote Call thingy
|   > Module written by Matt Mecham
|   > Date started: 17th October 2002
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


class ad_ips {

	var $base_url;

	var $colours = array();

	var $url = "http://www.invisionboard.com/acp/";

	var $version = "1.1";

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

		switch($ibforums->input['code'])
		{

			case 'news':
				$this->news();
				break;

			case 'updates':
				$this->updates();
				break;

			case 'docs':
				$this->docs();
				break;

			case 'support':
				$this->support();
				break;

			case 'host':
				$this->host();
				break;

			case 'purchase':
				$this->purchase();
				break;

			//-----------------------------------------
			default:
				exit();
				break;
		}

	}




	function news()
	{
		global $ibforums, $DB,  $std;

		@header("Location: ".$this->url."?news");
		exit();
	}

	function updates()
	{
		global $ibforums, $DB,  $std;

		//@header("Location: ".$this->url."?updates&version=".$this->version);
		@header("Location: ".$this->url."?updates");
		exit();
	}

	function docs()
	{
		global $ibforums, $DB,  $std;

		@header("Location: http://www.invisionpower.com/documentation/showdoc.php");
		exit();
	}

	function support()
	{
		global $ibforums, $DB,  $std;

		@header("Location: ".$this->url."?support");
		exit();
	}

	function host()
	{
		global $ibforums, $DB,  $std;

		@header("Location: ".$this->url."?host");
		exit();
	}

	function purchase()
	{
		global $ibforums, $DB,  $std;

		@header("Location: ".$this->url."?purchase");
		exit();
	}









}


?>