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
	var $this_version = '20002';
	var $upgrade_from = '20001';
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

		$SQL[]="create table ibf_announcements (
		announce_id int(10) UNSIGNED NOT NULL auto_increment,
		announce_title varchar(255) NOT NULL default '',
		announce_post text NOT NULL default '',
		announce_forum text NOT NULL default '',
		announce_member_id mediumint(8) UNSIGNED NOT NULL default '0',
		announce_html_enabled tinyint(1) NOT NULL default '0',
		announce_views int(10) UNSIGNED NOT NULL default '0',
		announce_start int(10) UNSIGNED NOT NULL default '0',
		announce_end int(10) UNSIGNED NOT NULL default '0',
		announce_active tinyint(1) NOT NULL default '1',
		PRIMARY KEY (announce_id)
		);";

		$SQL[]="INSERT INTO ibf_task_manager (task_title, task_file, task_next_run, task_week_day,
				task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled)
				VALUES ('Announcements Update', 'announcements.php', 1080747660, -1, -1, 4, -1, 'e82f2c19ab1ed57c140fccf8aea8b9fe', 1,
				'Rebuilds cache and expires out of date announcements', 1);";

		$SQL[]="CREATE TABLE ibf_bulk_mail (
		  mail_id int(10) NOT NULL auto_increment,
		  mail_subject varchar(255) NOT NULL default '',
		  mail_content mediumtext NOT NULL default '',
		  mail_groups mediumtext NOT NULL default '',
		  mail_honor tinyint(1) NOT NULL default '1',
		  mail_opts mediumtext NOT NULL default '',
		  mail_start int(10) NOT NULL default '0',
		  mail_updated int(10) NOT NULL default '0',
		  mail_sentto int(10) NOT NULL default '0',
		  mail_active tinyint(1) NOT NULL default '0',
		  mail_pergo smallint(5) NOT NULL default '0',
		  PRIMARY KEY (mail_id)
		);";


		$SQL[]="alter table ibf_task_manager add task_key varchar(30) NOT NULL default '', add task_safemode tinyint(1) NOT NULL default '';";

		$SQL[]="INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value,
				conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group,
				conf_end_group, conf_help_key, conf_add_cache) VALUES ('Add &#39;&lt;&#39; and &#39;&gt;&#39; to &#39;to&#39; and &#39;from&#39; addresses?',
				'Some SMTP mailers require that email addresses are in the following format \'<\' address \'>\' (no quotes).
				If you are getting errors in the mail error log, enabled this option', '12', 'yes_no', 'mail_wrap_brackets', '', '0', '', '', 1, 6, '', 0, '', 1);";

		$SQL[]="create table ibf_mail_error_logs (
		  mlog_id int(10) auto_increment NOT NULL,
		  mlog_date int(10) NOT NULL default '0',
		  mlog_to varchar(250) NOT NULL default '',
		  mlog_from varchar(250) NOT NULL default '',
		  mlog_subject varchar(250) NOT NULL default '',
		  mlog_content varchar(250) NOT NULL default '',
		  mlog_msg text NOT NULL default '',
		  mlog_code varchar(200) NOT NULL default '',
		  mlog_smtp_msg text NOT NULL default '',
		  PRIMARY KEY (mlog_id)
		);";


		$SQL[]="alter table ibf_pfields_data
		change fid pf_id smallint(5) NOT NULL auto_increment,
		change ftitle pf_title varchar(250) NOT NULL default '',
		change fdesc pf_desc varchar(250) NOT NULL default '',
		change fcontent pf_content text NOT NULL default '',
		change ftype pf_type varchar(250) NOT NULL default '',
		change freq pf_not_null tinyint(1) NOT NULL default '0',
		change fhide pf_member_hide tinyint(1) NOT NULL default '0',
		change fmaxinput pf_max_input smallint(6) NOT NULL default '0',
		change fedit pf_member_edit tinyint(1) NOT NULL default '0',
		change forder pf_position smallint(6) NOT NULL default '0',
		change fshowreg pf_show_on_reg tinyint(1) NOT NULL default '0',
		add pf_input_format text NOT NULL default '',
		add pf_admin_only tinyint(1) NOT NULL default '0',
		add pf_topic_format text NOT NULL default '';";

		$SQL[]="INSERT INTO ibf_skin_macro (macro_value, macro_replace, macro_set) VALUES ( 'POST_SNAPBACK', \"<img src='style_images/<#IMG_DIR#>/post_snapback.gif' alt='*' border='0' />\", 1);";

		$SQL[]="alter table ibf_forum_tracker add forum_track_type varchar(100) NOT NULL default 'delayed';";
		$SQL[]="alter table ibf_tracker add topic_track_type varchar(100) NOT NULL default 'delayed';";

		$SQL[]="INSERT INTO ibf_custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Post Snap Back', 'This tag displays a little linked image which links back to a post - used when quoting posts from the board. Opens in same window by default.', 'snapback', '<a href=\"index.php?act=findpost&amp;pid={content}\"><{POST_SNAPBACK}></a>', 0, '[snapback]100[/snapback]');";
		$SQL[]="INSERT INTO ibf_custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Right', 'Aligns content to the right of the posting area', 'right', '<div align=\'right\'>{content}</div>', 0, '[right]Some text here[/right]');";
		$SQL[]="INSERT INTO ibf_custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Left', 'Aligns content to the left of the post', 'left', '<div align=\'left\'>{content}</div>', 0, '[left]Left aligned text[/left]');";
		$SQL[]="INSERT INTO ibf_custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Center', 'Aligns content to the center of the posting area.', 'center', '<div align=\'center\'>{content}</div>', 0, '[center]Centered Text[/center]');";
		$SQL[]="INSERT INTO ibf_custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Topic Link', 'This tag provides an easy way to link to a topic', 'topic', '<a href=\'index.php?showtopic={option}\'>{content}</a>', 1, '[topic=100]Click me![/topic]');";
		$SQL[]="INSERT INTO ibf_custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Post Link', 'This tag provides an easy way to link to a post.', 'post', '<a href=\'index.php?act=findpost&pid={option}\'>{content}</a>', 1, '[post=100]Click me![/post]');";

		$SQL[]="alter table ibf_members change auto_track auto_track varchar(50) default '0';";


		$SQL[]="alter table ibf_conf_settings_titles
		add conf_title_noshow tinyint(1) NOT NULL default '0',
		add conf_title_keyword varchar(200) NOT NULL default '0'";

		return $SQL;
	}

}


?>