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
	var $this_version = '20004';
	var $upgrade_from = '20003';
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

		$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (22,'IPB Portal', 'These settings enable you to enable or disable IPB Portal and control the options IPB Portal offers.', 20, 0, 'ipbportal');";

		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('ENABLE IPB Portal?', 'If \'yes\', IPB Portal can be accessed via \'index.php?act=home\' or via the special \'index.php\' script (see documentation for more info).', '22', 'yes_no', 'csite_on', '', '1', '', '', 1, 1, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('IPB Portal Page Title?', 'This will appear inbetween the &lt;title&gt; elements on the page', '22', 'input', 'csite_title', '', 'IPB Portal', '', '', 1, 2, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Forums to export articles from', 'Separate <b>forum ids</b> with a comma for more than one', '22', 'input', 'csite_article_forum', '1,2,3,4,5,6', '', '', '', 1, 3, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Number of Articles to display in the main section', '', '22', 'input', 'csite_article_max', '', '15', '', '', 1, 4, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Enable Recent Articles?', 'This will show a list of recent topic titles on the IPB Portal page', '22', 'yes_no', 'csite_article_recent_on', '', '1', '', '', 1, 5, 'IPB Portal Recent Articles', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Max. no recent articles to show', '', '22', 'input', 'csite_article_recent_max', '', '5', '', '', 1, 6, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Max. length of topic titles', '', '22', 'input', 'csite_article_len', '', '30', '', '', 1, 7, '', 1, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Date format for articles', '<a href=\'http://www.php.net/date\'>Same as PHP\'s date function', '22', 'input', 'csite_article_date', '', 'm-j-y H:i', '', '', 1, 3, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Enable Recent Discussions', '', '22', 'yes_no', 'csite_discuss_on', '', '1', '', '', 1, 9, 'IPB Portal Recent Discussions', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Max. no recent discussions to show', '', '22', 'input', 'csite_discuss_max', '', '10', '', '', 1, 10, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Max. length of topic titles', '', '22', 'input', 'csite_discuss_len', '', '30', '', '', 1, 11, '', 1, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show User / Guest Info box?', '', '22', 'yes_no', 'csite_pm_show', '', '1', '', '', 1, 12, 'IPB Portal Components', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show online users?', '', '22', 'yes_no', 'csite_online_show', '', '1', '', '', 1, 13, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show search box?', '', '22', 'yes_no', 'csite_search_show', '', '1', '', '', 1, 14, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Enable skin selection choice dropdown?', '', '22', 'yes_no', 'csite_skinchange_show', '', '1', '', '', 1, 15, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Enter URL to poll topic for inclusion', 'Leave blank to not show a poll or the poll box', '22', 'input', 'csite_poll_url', '', '', '', '', 1, 17, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show Site Navigation Menu?', '', '22', 'yes_no', 'csite_nav_show', '', '1', '', '', 1, 18, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Site Navigation Menu Links', 'One per line in this format<br>http://www.apple.com [Apple\'s Website]<br><br>{board_url} will convert into your board', '22', 'textarea', 'csite_nav_contents', '', '{board_url} [Forums]\r\n{board_url}act=Search&CODE=getactive [Today\'s Active Topics]\r\n{board_url}act=Stats [Today\'s Top 10 Posters]\r\n{board_url}act=Stats&CODE=leaders [Contact Staff]', '', 'if ( $show == 1)\r\n{\r\n    $value = preg_replace( \"/&(middot|quot|copy|amp)/\", \"&\\\\1\", $value );\r\n}', 1, 19, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show Affiliates / Favoured Sites box?', '', '22', 'yes_no', 'csite_fav_show', '', '0', '', '', 1, 20, '', 0, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show Affiliates / Favoured Sites box content', 'Raw HTML enabled', '22', 'textarea', 'csite_fav_contents', '', '', '', 'if ( $show == 1)\r\n{\r\n $value = preg_replace( \"/&(middot|quot|copy|amp)/\", \"&\\\\1\", $value );\r\n}', 1, 21, '', 1, '', 0);";


		return $SQL;
	}

}


?>