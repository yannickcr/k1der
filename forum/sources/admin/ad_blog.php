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
+--------------------------------------------------------------------------
|
|   > Blog AdminCP script wrapper
|   > Script written by Remco Wilting
|   > Date started: 27st August 2004
|   > Module version: 1.0.003
|
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class ad_blog {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $forums, $DB,  $std;

		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------
		// Do some set up
		//-----------------------------------------

		if ( ! @is_dir( ROOT_PATH.'/modules/blog' ) )
		{
			//$std->boink_it("http://www.invisionblog.com");
		}
		else
		{
			require ROOT_PATH.'modules/blog/admin/ad_blog.php';

			$adblog = new ad_blog_plugin();
            $adblog->run_me();
		}

	}

}

?>