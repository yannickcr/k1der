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
|   > Subscription Manager For IPB
|   > Module written by Matt Mecham
|   > Date started: 19th August 2003
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

class ad_subsmanager {

	var $base_url;

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

		$ibforums->admin->page_title = "IPB Subscriptions Manager";

		$ibforums->admin->page_detail = "You can set up and manage your member's paid subscriptions in this section.";

		$ibforums->admin->nav[] = array( 'act=msubs'              , 'IPB Subscription Manager Home' );
		$ibforums->admin->nav[] = array( 'act=msubs&code=dosearch', 'Show all subscribed members' );

		//-----------------------------------------
		// Do some set up
		//-----------------------------------------

		if ( ! @is_dir( ROOT_PATH.'/modules/subsmanager' ) )
		{
			//$std->boink_it("http://customer.invisionpower.com/ipb/subs/redirect_acp.php");
		}
		else
		{
			define( 'IPB_CALLED', 1 );

			require ROOT_PATH.'/modules/subsmanager/ad_plugin_subsm.php';

			$PLUGIN = new ad_plugin_subsm();
		}

		//-----------------------------------------

		switch($ibforums->input['code'])
		{
			case 'editpkginfo':
				$PLUGIN->edit_pkg_gateway_info();
				break;
			case 'doeditpkg':
				$PLUGIN->doedit_pkg_gateway_info();
				break;
			//-----------------------------------------
			case 'removepackage':
				$PLUGIN->remove_package();
				break;
			case 'doremovepackage':
				$PLUGIN->do_remove_package();
				break;
			//-----------------------------------------
			case 'removemembers':
				$PLUGIN->remove_members();
				break;
			case 'doremovemembers':
				$PLUGIN->do_remove_members();
				break;
			//-----------------------------------------
			case 'addpackage':
				$PLUGIN->alter_package_form('add');
				break;

			case 'doaddpackage':
				$PLUGIN->do_add_package();
				break;
			//-----------------------------------------
			case 'editpackage':
				$PLUGIN->alter_package_form('edit');
				break;

			case 'doeditpackage':
				$PLUGIN->do_edit_package();
				break;
			//-----------------------------------------
			case 'editmethod':
				$PLUGIN->edit_method();
				break;

			case 'doeditmethod':
				$PLUGIN->do_edit_method();
				break;
			//-----------------------------------------
			case 'dosearch':
				$PLUGIN->do_search();
				break;
			case 'searchlog':
				$PLUGIN->do_search_log();
				break;
			case 'searchlogview':
				$PLUGIN->do_search_log_view();
				break;
			//-----------------------------------------
			case 'domodifytrans':
				$PLUGIN->do_modify_trans();
				break;

			case 'dotransdelete':
				$PLUGIN->do_delete_trans();
				break;

			//-----------------------------------------
			case 'edittransaction':
				$PLUGIN->edit_transaction('edit');
				break;

			case 'addtransaction':
				$PLUGIN->edit_transaction('add');
				break;

			case 'doedittransaction':
				$PLUGIN->save_transaction('edit');
				break;

			case 'doaddtransaction':
				$PLUGIN->save_transaction('add');
				break;

			case 'overview':
				$PLUGIN->do_overview();
				break;

			//-----------------------------------------

			case 'currency':
				$PLUGIN->currency_index();
				break;
			case 'editcurrency':
				$PLUGIN->currency_edit();
				break;
			case 'deletecurrency':
				$PLUGIN->currency_delete();
				break;

			default:
				$PLUGIN->index_screen();
				break;
		}

	}

}


?>