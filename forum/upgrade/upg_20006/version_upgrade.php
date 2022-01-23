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
	var $this_version = '20006';
	var $upgrade_from = '20005';
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
		global $ibforums, $DB, $std;

		$SQL = array();

		if ( ! $DB->field_exists( 'subs_pkg_chosen', SQL_PREFIX.'members' ) )
		{
			$SQL[] = "alter table ibf_members add subs_pkg_chosen smallint(3) NOT NULL default '0';";
		}

		$SQL[] = "alter table ibf_topic_mmod add topic_approve tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ibf_groups add g_attach_per_post int(10) NOT NULL default '0';";
		$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_id,conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (23,'Subscriptions Manager', 'These settings control various subscription manager features.', 3, 0, 'subsmanager');";
		$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_id,conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (24,'IPB Registration', 'This section will allow you to edit your IPB registered licence settings.', 3, 1, 'ipbreg');";
		$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_id,conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (25,'IPB Copyright Removal', 'This section allows you to manage your copyright removal key.', 2, 1, 'ipbcopyright');";
		$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_id,conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (20,'Invision Chat Settings (Legacy Version)', 'This will allow you to customize your Invision Chat integration settings for the legacy edition.', 14, 1, 'chat');";
		$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_id,conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (21,'Invision Chat Settings', 'This will allow you to customize your Invision Chat integration settings for the new 2004 edition', 10, 1, 'chat04');";

		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Add &#39;&lt;&#39; and &#39;&gt;&#39; to &#39;to&#39; and &#39;from&#39; addresses?', 'Some SMTP mailers require that email addresses are in the following format \'<\' address \'>\' (no quotes). If you are getting errors in the mail error log, enabled this option', '12', 'yes_no', 'mail_wrap_brackets', '', '0', '', '', 1, 6, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Account Number?', 'Removing this number will remove all links / chat functionality within IPB.\r\n', '20', 'input', 'chat_account_no', '', '', '', '', 1, 1, 'Account Specifics', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Server?', '<!--SERVER-->', '20', 'input', 'chat_server_addr', '', '', '', '', 1, 2, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Enable &#39;Who&#39;s Chatting?&#39;', 'If enabled, this will show who\'s chatting in the chat room on your forums home page underneath the active users list.', '20', 'yes_no', 'chat_who_on', '', '1', '', '', 1, 3, 'Who&#39;s Chatting', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Hide Who&#39;s Chatting when no members are logged into chat?', '', '20', 'yes_no', 'chat_hide_whoschatting', '', '1', '', '', 1, 4, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Update local list no less than every:', 'The local list is updated from the master chat server by the frequency you set and cached locally.', '20', 'dropdown', '', '', '15', '5=5\r\n10=10\r\n15=15\r\n30=30', '', 1, 5, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Changed your IPChat control panel password?', 'If you have changed your IPChat control panel password, please add the new password here. Leave blank if it\'s not changed.', '20', 'input', 'chat_pass_md5', '', '', '', '', 1, 6, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('ENTER YOUR IPCHAT CONTROL PANEL PASSWORD', 'You must enter your IPChat control panel password to allow retrieval of the Who\'s Chatting list.', '20', 'input', 'chat_pass_md5_new', '', '', '', '', 1, 7, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Dimensions (WIDTH)?', '', '20', 'input', 'chat_width', '', '600', '', '', 1, 8, 'Showing the chat room', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Dimensions (HEIGHT)?', '', '20', 'input', 'chat_height', '', '350', '', '', 1, 9, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Pop-up Window Padding? (in px)', 'Allows window to open without scrollbars', '20', 'input', 'chat_poppad', '', '50', '', '', 1, 10, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Default Chat Room Interface Language?', '', '20', 'dropdown', 'chat_language', '', 'en', 'en=English\r\nar=Arabic\r\nde=German\r\nes=Spanish\r\nfr=French\r\nhr=Croation\r\nit=Italian\r\niw=Hebrew\r\nnl=Dutch\r\npl=Polish\r\npt=Portuguese', '', 1, 11, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Load the chat room in...?', '', '20', 'dropdown', 'chat_display', '', 'self', 'self=Normal IPB Page\r\nnew=New Pop Up Window', '', 1, 9, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Allow chat access to these groups', 'You may select more than one', '20', 'multi', 'chat_access_groups', '', '', '#show_groups#', 'if ( \$save == 1)\r\n{\r\n	if ( is_array(\$POST[\'chat_access_groups\']) )\r\n	{\r\n		\$POST[\'chat_access_groups\'] = implode(\",\",\$POST[\'chat_access_groups\']);\r\n	}\r\n	else\r\n	{\r\n		\$POST[\'chat_access_groups\'] = '';\r\n	}\r\n	\r\n	\$key = \'chat_access_groups\';\r\n}if ( \$show == 1 ) { \$key=\'chat_access_groups[]\'; }', 1, 13, 'Access Permissions', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Allow ADMIN access to these groups', 'You may select more than one - please choose carefully!', '20', 'multi', 'chat_admin_groups', '', '', '#show_groups#', 'if ( \$save == 1)\r\n{\r\n	if ( is_array(\$POST[\'chat_admin_groups\']) )\r\n	{\r\n		\$POST[\'chat_admin_groups\'] = implode(\",\",\$POST[\'chat_admin_groups\']);\r\n	}\r\n	else\r\n	{\r\n		\$POST[\'chat_admin_groups\'] = '';\r\n	}\r\n	\r\n	\$key = \'chat_admin_groups\';\r\n}if ( \$show == 1 ) { \$key=\'chat_admin_groups[]\'; }', 1, 14, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Account Number?', 'Removing this number will remove all links / chat functionality within IPB.\r\n', '21', 'input', 'chat04_account_no', '', '', '', '', 1, 1, 'Account Specifics', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room URL?', '<!--SERVER-->', '21', 'input', 'chat04_whodat_server_addr', '', 'http://invision.parachat.com/cgi-bin/userlist/test', '', '', 1, 2, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Enable &#39;Who&#39;s Chatting?&#39;', 'If enabled, this will show who\'s chatting in the chat room on your forums home page underneath the active users list.', '21', 'yes_no', 'chat04_who_on', '', '1', '', '', 1, 3, 'Who&#39;s Chatting', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Hide Who&#39;s Chatting when no members are logged into chat?', '', '21', 'yes_no', 'chat04_hide_whoschatting', '', '1', '', '', 1, 4, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Update local list no less than every:', 'The local list is updated from the master chat server by the frequency you set and cached locally.', '21', 'dropdown', 'chat04_who_save', '', '1', '1=1\r\n5=5\r\n10=10\r\n15=15\r\n30=30', '', 1, 5, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Dimensions (WIDTH)?', '', '21', 'input', 'chat04_width', '', '600', '', '', 1, 8, 'Showing the chat room', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Dimensions (HEIGHT)?', '', '21', 'input', 'chat04_height', '', '350', '', '', 1, 9, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Chat Room Pop-up Window Padding? (in px)', 'Allows window to open without scrollbars', '21', 'input', 'chat04_poppad', '', '50', '', '', 1, 10, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Load the chat room in...?', '', '21', 'dropdown', 'chat04_display', '', 'self', 'self=Normal IPB Page\r\nnew=New Pop Up Window', '', 1, 9, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Allow chat access to these groups', 'You may select more than one', '21', 'multi', 'chat04_access_groups', '', '', '#show_groups#', 'if ( \$save == 1)\r\n{\r\n	if ( is_array(\$POST[\'chat04_access_groups\']) )\r\n	{\r\n		\$POST[\'chat04_access_groups\'] = implode(\",\",\$POST[\'chat04_access_groups\']);\r\n	}\r\n	else\r\n	{\r\n		\$POST[\'chat04_access_groups\'] = '';\r\n	}\r\n	\r\n	\$key = \'chat04_access_groups\';\r\n}', 1, 13, 'Access Permissions', 1, '', 1);";

		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Allow poll creator to vote in own poll?', 'If set to \"yes\", the poll creator will have the option of voting in their own poll.', '5', 'yes_no', 'allow_creator_vote', '', '1', '', '', 1, 31, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Registration Terms &amp; Rules', 'The content of this section will be displayed before a member can register a new account.\r\n<b>HTML ENABLED</b>', '17', 'textarea', 'reg_rules', '', '<b>Forum Terms & Rules</b>\r\n\r\nPlease take a moment to review these rules detailed below. If you agree with them and wish to proceed with the registration, simply click the \"Register\" button below. To cancel this registration, simply hit the \'back\' button on your browser.\r\n\r\nPlease remember that we are not responsible for any messages posted. We do not vouch for or warrant the accuracy, completeness or usefulness of any message, and are not responsible for the contents of any message.\r\n\r\nThe messages express the views of the author of the message, not necessarily the views of this bulletin board. Any user who feels that a posted message is objectionable is encouraged to contact us immediately by email. We have the ability to remove objectionable messages and we will make every effort to do so, within a reasonable time frame, if we determine that removal is necessary.\r\n\r\nYou agree, through your use of this service, that you will not use this bulletin board to post any material which is knowingly false and/or defamatory, inaccurate, abusive, vulgar, hateful, harassing, obscene, profane, sexually oriented, threatening, invasive of a person\'s privacy, or otherwise violative of any law.\r\n\r\nYou agree not to post any copyrighted material unless the copyright is owned by you or by this bulletin board.\r\n', '', '', 1, 5, 'Registration Terms &amp; Rules', 1, '', 0);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show subscription packages when registering?', 'If enabled, the subscription packages will be shown in the registration page and the registering member will be able to select one.', '23', 'yes_no', 'subsm_show_reg', '', '1', '', '', 1, 1, 'Optional Purchase Upon Registration', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Force every member to require a subscriptions package', 'If enabled, the member must choose a package upon registration to proceed past the validating group and current members must purchase a subscription to view the board.', '23', 'yes_no', 'subsm_enforce', '', '0', '', '', 1, 2, 'Force Package Purchase', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Member group to use after registration but before package purchase?', 'If you require that every member purchases a subscription, choose a group to move the member into once they have validated their registration prior to purchasing a subscriptions manager package.\r\nMembers in this group will see the \'Purchase Subscriptions\' page when logged in and if they don\'t already have a package.', '23', 'dropdown', 'subsm_nopkg_group', '', '1', '#show_groups#', '', 1, 3, '', 1, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Reported Post Notifications sent to', 'Where do you want reported post notifications to go?', '6', 'dropdown', 'reportpost_method', '', 'pm', 'email=Email\r\npm=Personal Message', '', 1, 19, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Display registered to line?', 'If enabled, you can optionally add a \"registered to\" line at the bottom of your board.', '24', 'yes_no', 'ipb_reg_show', '', '0', '', '', 1, 2, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Show as registered to...', 'Examples: <em>Matthew Mecham</em>, <em>IPS, Inc.', '24', 'input', 'ipb_reg_name', '', '', '', '', 1, 3, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('IPB Registered Licence Key', 'This is the IPB registraton key NOT your customer code or client center password. To get your key, log into your client center and click on \'Invision Power Board\' under \'Purchased Services\'.\r\n<br />DO NOT REMOVE OR EDIT THIS KEY UNLESS YOU\'RE SURE OF WHAT YOU\'RE DOING!', '24', 'input', 'ipb_reg_number', '', '', '', '', 1, 1, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('IPB Copyright Removal Key', 'Your copyright removal key obtained from IPS.', '25', 'input', 'ipb_copy_number', '', '', '', '', 1, 1, '', 0, '', 1);";
		$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Copyright Purchased?', '', '25', 'yes_no', 'ips_cp_purchase', '', '0', '', '', 1, 2, '', 0, '', 1);";

		return $SQL;
	}

}


?>