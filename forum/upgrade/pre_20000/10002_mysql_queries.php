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
|   > IPB UPGRADE 1.2 -> 2.0 SQL STUFF!
|   > Script written by Matt Mecham
|   > Date started: 21st April 2004
|   > Interesting fact: Turin Brakes are also good
+--------------------------------------------------------------------------
*/

class upgrade_sql
{
	var $errors   = array();
	var $sqlcount = 0;
	var $sqlbit   = array();

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function upgrade_sql()
	{
		global $DB, $ibforums, $std;

		define( 'THIS_HUMAN_VERSION', '2.0.2' );
		define( 'THIS_LONG_VERSION' , '20012' );

		$this->sqlbit['attach'] = "attach_file != ''";
	}

	/*-------------------------------------------------------------------------*/
	// STEP 24: RECACHE & REBUILD
	/*-------------------------------------------------------------------------*/

	function step_24()
	{
		global $DB, $std, $ibforums;

		//-----------------------------------
		// Get ACP library
		//-----------------------------------

		require_once( ROOT_PATH.'sources/admin/admin_cache_functions.php' );
		$acp = new admin_cache_functions();

		//-----------------------------------
		// Cache skins and shit
		//-----------------------------------

		$acp->_rebuild_all_caches( array(2,3) );

		//-------------------------------------------------------------
		// Forum cache
		//-------------------------------------------------------------

		$ibforums->cache['forum_cache'] = array();

		$DB->simple_construct( array( 'select' => '*',
									  'from'   => 'forums',
									  'order'  => 'parent_id, position'
							 )      );
		$DB->simple_exec();

		while( $fr = $DB->fetch_row() )
		{
			$perms = unserialize(stripslashes($fr['permission_array']));

			$fr['read_perms']   = $perms['read_perms'];
			$fr['reply_perms']  = $perms['reply_perms'];
			$fr['start_perms']  = $perms['start_perms'];
			$fr['upload_perms'] = $perms['upload_perms'];
			$fr['show_perms']   = $perms['show_perms'];

			unset($fr['permission_array']);

			$ibforums->cache['forum_cache'][ $fr['id'] ] = $fr;
		}

		$std->update_cache( array( 'name' => 'forum_cache', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Group Cache
		//-------------------------------------------------------------

		$ibforums->cache['group_cache'] = array();

		$DB->simple_construct( array( 'select' => "*",
									  'from'   => 'groups'
							 )      );

		$DB->simple_exec();

		while ( $i = $DB->fetch_row() )
		{
			$ibforums->cache['group_cache'][ $i['g_id'] ] = $i;
		}

		$std->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Systemvars
		//-------------------------------------------------------------

		$ibforums->cache['systemvars'] = array();

		$result = $DB->simple_exec_query( array( 'select' => 'count(*) as cnt', 'from' => 'mail_queue' ) );

		$ibforums->cache['systemvars']['mail_queue'] = intval( $result['cnt'] );
		$ibforums->cache['systemvars']['task_next_run'] = time() + 3600;

		$std->update_cache( array( 'name' => 'systemvars', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Stats
		//-------------------------------------------------------------

		$ibforums->cache['stats'] = array();

		$DB->simple_construct( array( 'select' => 'count(pid) as posts', 'from' => 'posts', 'where' => "queued <> 1" ) );
		$DB->simple_exec();

		$r = $DB->fetch_row();
		$stats['total_replies'] = intval($r['posts']);

		$DB->simple_construct( array( 'select' => 'count(tid) as topics', 'from' => 'topics', 'where' => "approved = 1" ) );
		$DB->simple_exec();

		$r = $DB->fetch_row();
		$stats['total_topics']   = intval($r['topics']);
		$stats['total_replies'] -= $stats['total_topics'];

		$DB->simple_construct( array( 'select' => 'count(id) as members', 'from' => 'members', 'where' => "mgroup <> '".$ibforums->vars['auth_group']."'" ) );
		$DB->simple_exec();

		$r = $DB->fetch_row();
		$stats['mem_count'] = intval($r['members']);

		$ibforums->cache['stats']['total_replies'] = $stats['total_replies'];
		$ibforums->cache['stats']['total_topics']  = $stats['total_topics'];
		$ibforums->cache['stats']['mem_count']     = $stats['mem_count'];

		$r = $DB->simple_exec_query( array( 'select' => 'id, name',
											'from'   => 'members',
											'order'  => 'id DESC',
											'limit'  => '0,1'
								   )      );

		$ibforums->cache['stats']['last_mem_name'] = $r['name'];
		$ibforums->cache['stats']['last_mem_id']   = $r['id'];

		$std->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Ranks
		//-------------------------------------------------------------

		$ibforums->cache['ranks'] = array();

		$DB->simple_construct( array( 'select' => 'id, title, pips, posts',
									  'from'   => 'titles',
									  'order'  => "posts DESC",
							)      );

		$DB->simple_exec();

		while ($i = $DB->fetch_row())
		{
			$ibforums->cache['ranks'][ $i['id'] ] = array(
														  'TITLE' => $i['title'],
														  'PIPS'  => $i['pips'],
														  'POSTS' => $i['posts'],
														);
		}

		$std->update_cache( array( 'name' => 'ranks', 'array' => 1, 'deletefirst' => 1 ) );


		//-------------------------------------------------------------
		// SETTINGS
		//-------------------------------------------------------------

		$ibforums->cache['settings'] = array();

		$DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_add_cache=1' ) );
		$info = $DB->simple_exec();

		while ( $r = $DB->fetch_row($info) )
		{
			$ibforums->cache['settings'][ $r['conf_key'] ] = $r['conf_value'] != "" ? $r['conf_value'] : $r['conf_default'];
		}

		$std->update_cache( array( 'name' => 'settings', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// EMOTICONS
		//-------------------------------------------------------------

		$ibforums->cache['emoticons'] = array();

		$DB->simple_construct( array( 'select' => 'typed,image,clickable,emo_set', 'from' => 'emoticons' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['emoticons'][] = $r;
		}

		$std->update_cache( array( 'name' => 'emoticons', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// LANGUAGES
		//-------------------------------------------------------------

		$ibforums->cache['languages'] = array();

		$DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['languages'][] = $r;
		}

		$std->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// ATTACHMENT TYPES
		//-------------------------------------------------------------

		$ibforums->cache['attachtypes'] = array();

		$DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$DB->simple_exec();

		while ( $r = $DB->fetch_row() )
		{
			$ibforums->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		$std->update_cache( array( 'name' => 'attachtypes', 'array' => 1, 'deletefirst' => 1 ) );
	}

	/*-------------------------------------------------------------------------*/
	// STEP 21: OPTIMIZATION II
	/*-------------------------------------------------------------------------*/

	function step_21()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."posts drop index topic_id;";
		$SQL[] = "alter table ".SQL_PREFIX."posts drop index author_id;";
		$SQL[] = "alter table ".SQL_PREFIX."posts add index topic_id (topic_id, queued, pid);";
		$SQL[] = "alter table ".SQL_PREFIX."posts add index author_id( author_id, topic_id);";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."posts DROP INDEX forum_id, ADD INDEX(post_date);";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 20: OPTIMIZATION
	/*-------------------------------------------------------------------------*/

	function step_20()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."tracker change topic_id topic_id int(10) NOT NULL default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."tracker ADD INDEX(topic_id);";

		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics CHANGE pinned pinned TINYINT( 1 ) DEFAULT '0' NOT NULL;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics CHANGE approved approved TINYINT( 1 ) DEFAULT '0' NOT NULL;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics ADD INDEX(topic_firstpost);";

		$SQL[] = "UPDATE ".SQL_PREFIX."members SET language=''";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 19: DROPPING TABLES
	/*-------------------------------------------------------------------------*/

	function step_19()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "DROP TABLE ".SQL_PREFIX."tmpl_names;";
		$SQL[] = "DROP TABLE ".SQL_PREFIX."forums_bak;";
		$SQL[] = "DROP TABLE ".SQL_PREFIX."categories;";
		$SQL[] = "DROP TABLE ".SQL_PREFIX."messages;";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 18: SAFE INSERTS
	/*-------------------------------------------------------------------------*/

	function step_18()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (1, 'Hourly Clean Out', 'cleanout.php', 1076074920, -1, -1, -1, 59, '2a7d083832daa123b73a68f9c51fdb29', 1, 'Kill old sessions, reg images, searches',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (2, 'Daily Stats Rebuild', 'rebuildstats.php', 1076112000, -1, -1, 0, 0, '640b9a6c373ff207bc1b1100a98121af', 1, 'Rebuilds board statistics',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (3, 'Daily Clean Out', 'dailycleanout.php', 1076122800, -1, -1, 3, 0, 'e71b52f3ff9419abecedd14b54e692c4', 1, 'Prunes topic subscriptions',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (4, 'Birthday and Events Cache', 'calendarevents.php', 1076100960, -1, -1, 12, -1, '2c148c9bd754d023a7a19dd9b1535796', 1, 'Caches calendar events &amp; birthdays',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_id, task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled) VALUES (9, 'Announcements Update', 'announcements.php', 1080747660, -1, -1, 4, -1, 'e82f2c19ab1ed57c140fccf8aea8b9fe', 1, 'Rebuilds cache and expires out of date announcements', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('Send Bulk Mail', 'bulkmail.php', 1086706080, -1, -1, -1, -1, '61359ac93eb93ebbd935a4e275ade2db', 0, 'Dynamically assigned, no need to edit or change', 0, 'bulkmail', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('Daily Topic &amp; Forum Digest', 'dailydigest.php', 1086912600, -1, -1, 0, 10, '723cab2aae32dd5d04898b1151038846', 1, 'Emails out daily topic &amp; forum digest emails', 1, 'dailydigest', 0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('Weekly Topic &amp; Forum Digest', 'weeklydigest.php', 1087096200, 0, -1, 3, 10, '7e7fccd07f781bdb24ac108d26612931', 1, 'Emails weekly topic &amp; forum digest emails', 1, 'weeklydigest', 0);";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Post Snap Back', 'This tag displays a little linked image which links back to a post - used when quoting posts from the board. Opens in same window by default.', 'snapback', '<a href=\"index.php?act=findpost&amp;pid={content}\"><{POST_SNAPBACK}></a>', 0, '[snapback]100[/snapback]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Right', 'Aligns content to the right of the posting area', 'right', '<div align=\'right\'>{content}</div>', 0, '[right]Some text here[/right]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Left', 'Aligns content to the left of the post', 'left', '<div align=\'left\'>{content}</div>', 0, '[left]Left aligned text[/left]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Center', 'Aligns content to the center of the posting area.', 'center', '<div align=\'center\'>{content}</div>', 0, '[center]Centered Text[/center]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Topic Link', 'This tag provides an easy way to link to a topic', 'topic', '<a href=\'index.php?showtopic={option}\'>{content}</a>', 1, '[topic=100]Click me![/topic]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('Post Link', 'This tag provides an easy way to link to a post.', 'post', '<a href=\'index.php?act=findpost&pid={option}\'>{content}</a>', 1, '[post=100]Click me![/post]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('CODEBOX', 'Use this BBCode tag to show a scrolling codebox. Useful for long sections of code.', 'codebox', '<div class=\'codetop\'>CODE</div><div class=\'codemain\' style=\'height:200px;white-space:pre;overflow:auto\'>{content}</div>', 0, '[codebox]long_code_here = '';[/codebox]');";

		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='USD', subcurrency_desc='United States Dollars', subcurrency_exchange='1.00', subcurrency_default=1;";
		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='GBP', subcurrency_desc='United Kingdom Pounds', subcurrency_exchange=' 0.630776', subcurrency_default=0;";
		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='CAD', subcurrency_desc='Canada Dollars', subcurrency_exchange='1.37080', subcurrency_default=0;";
		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='EUR', subcurrency_desc='Euro', subcurrency_exchange='0.901517', subcurrency_default=0;";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('PayPal', 'paypal', '', '', '', '', '', '', '', 0, 1, 'All major credit cards accepted. See <a href=\"https://www.paypal.com/affil/pal=9DJEWQQKVB6WL\" target=\"_blank\">PayPal</a> for more information.', '', 1, 'USD');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('NOCHEX', 'nochex', '', '', '', '', '', '', '', 0, 1, 'UK debit and credit cards, such as Switch, Solo and VISA Delta. All prices will be convereted into GBP (UK Pounds) upon ordering.', NULL, 1, 'GBP');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('Post Service', 'manual', '', '', '', '', '', '', '', 0, 0, 'You can use this method if you wish to send us a check, postal order or international money order.', NULL, 1, 'USD');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('2CheckOut', '2checkout', '', '', '', '', '', '', '', 1, 1, 'All major credit cards accepted. See <a href=\'http://www.2checkout.com/cgi-bin/aff.2c?affid=28376\' target=\'_blank\'>2CheckOut</a> for more information.', NULL, 1, 'USD');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('Authorize.net', 'authorizenet', '', '', '', '', '', '', '', '1', '1', NULL, NULL, '1', 'USD');";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (1, 'pdf', 'application/pdf', 1, 0, 'folder_mime_types/pdf.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (2, 'png', 'image/png', 1, 1, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (3, 'viv', 'video/vivo', 1, 0, 'folder_mime_types/win_player.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (4, 'wmv', 'video/x-msvideo', 1, 0, 'folder_mime_types/win_player.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (5, 'html', 'application/octet-stream', 1, 0, 'folder_mime_types/html.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (6, 'ram', 'audio/x-pn-realaudio', 1, 0, 'folder_mime_types/real_audio.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (7, 'gif', 'image/gif', 1, 1, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (8, 'mpg', 'video/mpeg', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (9, 'ico', 'image/ico', 1, 0, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (10, 'tar', 'application/x-tar', 1, 0, 'folder_mime_types/zip.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (11, 'bmp', 'image/x-MS-bmp', 1, 0, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (12, 'tiff', 'image/tiff', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (13, 'rtf', 'text/richtext', 1, 0, 'folder_mime_types/rtf.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (14, 'hqx', 'application/mac-binhex40', 1, 0, 'folder_mime_types/stuffit.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (15, 'aiff', 'audio/x-aiff', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (31, 'zip', 'application/zip', 1, 0, 'folder_mime_types/zip.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (17, 'ps', 'application/postscript', 1, 0, 'folder_mime_types/eps.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (18, 'doc', 'application/msword', 1, 0, 'folder_mime_types/doc.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (19, 'mov', 'video/quicktime', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (20, 'ppt', 'application/powerpoint', 1, 0, 'folder_mime_types/ppt.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (21, 'wav', 'audio/x-wav', 1, 0, 'folder_mime_types/music.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (22, 'mp3', 'audio/x-mpeg', 1, 0, 'folder_mime_types/music.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (23, 'jpg', 'image/jpeg', 1, 1, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (24, 'txt', 'text/plain', 1, 0, 'folder_mime_types/txt.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (25, 'xml', 'text/xml', 1, 0, 'folder_mime_types/script.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (26, 'css', 'text/css', 1, 0, 'folder_mime_types/script.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (27, 'swf', 'application/x-shockwave-flash', 0, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (32, 'php', 'application/octet-stream', 1, 0, 'folder_mime_types/php.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (28, 'htm', 'application/octet-stream', 1, 0, 'folder_mime_types/html.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (29, 'jpeg', 'image/jpeg', 1, 1, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (33, 'gz', 'application/x-gzip', 1, 0, 'folder_mime_types/zip.gif');";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('skin_id_cache', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('bbcode', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('moderators', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('multimod', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('banfilters', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('attachtypes', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('emoticons', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('forum_cache', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('badwords', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('systemvars', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('ranks', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('group_cache', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('stats', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('profilefields', 'a:0:{}', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('settings','', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('languages', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('birthdays', 'a:0:{}', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('calendar', 'a:0:{}', '', 1);";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."skin_sets (set_skin_set_id, set_name, set_image_dir, set_hidden, set_default, set_css_method, set_skin_set_parent, set_author_email, set_author_name, set_author_url, set_css, set_cache_macro, set_wrapper, set_css_updated, set_cache_css, set_cache_wrapper, set_emoticon_folder) VALUES (1, 'IPB Master Skin Set', '1', 0, 0, '0', -1, '', '', '', '', '', '', 1079109298, '', '', 'default');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."skin_sets (set_skin_set_id, set_name, set_image_dir, set_hidden, set_default, set_css_method, set_skin_set_parent, set_author_email, set_author_name, set_author_url, set_css, set_cache_macro, set_wrapper, set_css_updated, set_cache_css, set_cache_wrapper, set_emoticon_folder) VALUES (2, 'IPB Default Skin', '1', 0, 1, '0', -1, 'ipbauto@invisionboard.com', 'Invision Power Services', 'www.invisionboard.com', '', '', '', 1074679074, '', '', 'default');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."skin_sets (set_skin_set_id, set_name, set_image_dir, set_hidden, set_default, set_css_method, set_skin_set_parent, set_author_email, set_author_name, set_author_url, set_css, set_cache_macro, set_wrapper, set_css_updated, set_cache_css, set_cache_wrapper, set_emoticon_folder) VALUES (3, 'IPB Pre-2.0 Skins', '1', 0, 0, '0', -1, 'ipbauto@invisionboard.com', 'Invision Power Services', 'www.invisionboard.com', '', '', '', 1074679074, '', '', 'default');";


		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (1, 'General Configuration', 'These settings control the basics of the board such as URLs and paths.', 17);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (2, 'CPU Saving &amp; Optimization', 'This section allows certain features to be limited or removed to get more performance out of your board.', 16);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (3, 'Date &amp; Time Formats', 'This section contains the date and time formats used throughout the board.', 7);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (4, 'User Profiles', 'This section allows you to adjust your member\'s global permissions and other options.', 22);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (5, 'Topics, Posts and Polls', 'These options control various elements when posting, reading topics and reading polls.', 32);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (6, 'Security and Privacy', 'These options allow you to adjust the security and privacy options for your board.', 18);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (7, 'Cookies', 'This section allows you to set the default cookie options.', 3);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (8, 'COPPA Set-up', 'This section allows you to comply with <a href=\'http://www.ftc.gov/ogc/coppa1.htm\'>COPPA</a>.', 3);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (9, 'Calendar &amp; Birthdays', 'This section will allow you to set up the board calendar and its related options.', 8);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (10, 'News Set-up', 'This section will allow you to specify the forum you wish to export news topics from to be used with ssi.php and IPDynamic Lite.', 2);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (11, 'Personal Message Set-up', 'This section allows you to control the global PM options.', 3);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (12, 'Email Set-up', 'This section will allow you to change the incoming and outgoing email addresses as well as the email method.', 7);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (13, 'Warn Set-up', 'This section will allow you to set up the warning system.', 15);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (14, 'Trash Can Set-up', 'The trashcan is a special forum in which topics are moved into instead of being deleted.', 6);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (15, 'Board Offline / Online', 'Use this setting to turn switch your board online or offline and leave a message for your visitors.', 2);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (16, 'Search Engine Spiders', 'This section will allow you to set up and maintain your search engine bot spider recognition settings.', 7);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (17, 'Board Guidelines', 'This section allows you to maintain your board guidelines. If enabled, a link will be added to the board header linking to the board guidelines.', 4);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (18, 'Converge Set Up', 'Converge is Invision Power Services central authentication method for all IPS applications. This allows you to have a single log-in for all your IPS products.', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (19, 'Full Text Search Set-Up', 'Full text searching is a very fast and very efficient way of searching large amounts of posts without maintaining a manual index. This may not be available for your system.', 2);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (20,'Invision Chat Settings (Legacy Version)', 'This will allow you to customize your Invision Chat integration settings for the legacy edition.', 14, 1, 'chat');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (21,'Invision Chat Settings', 'This will allow you to customize your Invision Chat integration settings for the new 2004 edition', 10, 1, 'chat04');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (22,'IPB Portal', 'These settings enable you to enable or disable IPB Portal and control the options IPB Portal offers.', 20, 0, 'ipbportal');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (23,'Subscriptions Manager', 'These settings control various subscription manager features.', 3, 0, 'subsmanager');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (24,'IPB Registration', 'This section will allow you to edit your IPB registered licence settings.', 3, 1, 'ipbreg');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (25,'IPB Copyright Removal', 'This section allows you to manage your copyright removal key.', 2, 1, 'ipbcopyright');";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."upgrade_history (upgrade_id, upgrade_version_id, upgrade_version_human, upgrade_date, upgrade_mid, upgrade_notes) VALUES ('', '".THIS_LONG_VERSION."', '".THIS_HUMAN_VERSION."', '0', '0', '');";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 17: ALTER OTHERS TABLE II
	/*-------------------------------------------------------------------------*/

	function step_17()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."groups add g_attach_per_post int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topic_mmod add topic_approve tinyint(1) NOT NULL default '0';";

		$SQL[] = "alter table ".SQL_PREFIX."groups add g_can_msg_attach tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."pfields_data
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

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 16: ALTER MEMBERS TABLE II
	/*-------------------------------------------------------------------------*/

	function step_16()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members add has_blog TINYINT(1) NOT NULL default '0'";
		$SQL[] = "alter table ".SQL_PREFIX."members add sub_end int(10) NOT NULL default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members DROP msg_from_id, DROP msg_msg_id;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members DROP org_supmod, DROP integ_msg;";
		$SQL[] = "alter table ".SQL_PREFIX."members DROP aim_name, DROP icq_number, DROP website, DROP yahoo, DROP interests,
				  DROP msnname, DROP vdirs, DROP signature, DROP location, DROP avatar, DROP avatar_size;";
		$SQL[] = "alter table ".SQL_PREFIX."members change auto_track auto_track varchar(50) default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members change temp_ban temp_ban varchar(100) default '0'";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members change msg_total msg_total smallint(5) default '0'";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 15: ALTER TOPIC TABLE II
	/*-------------------------------------------------------------------------*/

	function step_15()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics add topic_firstpost int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topics add topic_queuedposts int(10) NOT NULL default '0';";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 14: ALTER POST TABLE II
	/*-------------------------------------------------------------------------*/

	function step_14()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "ALTER TABLE ".SQL_PREFIX."posts DROP attach_id, DROP attach_hits, DROP attach_type, DROP attach_file;";
		$SQL[] = "alter table ".SQL_PREFIX."posts change queued queued tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts drop forum_id;";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 7: IMPORT FORUMS
	/*-------------------------------------------------------------------------*/

	function step_7()
	{
		global $DB, $std, $ibforums;

		//-----------------------------------------
		// Convert existing forums
		//-----------------------------------------

		$o = $DB->query("SELECT * FROM ".SQL_PREFIX."forums_bak ORDER BY id");

		while( $r = $DB->fetch_row( $o ) )
		{
			$perm_array = addslashes(serialize(array(
													  'start_perms'  => $r['start_perms'],
													  'reply_perms'  => $r['reply_perms'],
													  'read_perms'   => $r['read_perms'],
													  'upload_perms' => $r['upload_perms'],
													  'show_perms'   => $r['read_perms']
									)		  )     );

			$DB->do_insert( 'forums', array (
											  'id'                      => $r['id'],
											  'position'                => $r['position'],
											  'topics'                  => $r['topics'],
											  'posts'                   => $r['posts'],
											  'last_post'               => $r['last_post'],
											  'last_poster_id'          => $r['last_poster_id'],
											  'last_poster_name'        => $r['last_poster_name'],
											  'name'                    => $r['name'],
											  'description'             => $r['description'],
											  'use_ibc'                 => $r['use_ibc'],
											  'use_html'                => $r['use_html'],
											  'status'                  => $r['status'],
											  'password'                => $r['password'],
											  'last_id'                 => $r['last_id'],
											  'last_title'              => $r['last_title'],
											  'sort_key'                => $r['sort_key'],
											  'sort_order'              => $r['sort_order'],
											  'prune'                   => $r['prune'],
											  'show_rules'              => $r['show_rules'],
											  'preview_posts'           => $r['preview_posts'],
											  'allow_poll'              => $r['allow_poll'],
											  'allow_pollbump'          => $r['allow_pollbump'],
											  'inc_postcount'           => $r['inc_postcount'],
											  'parent_id'               => $r['parent_id'],
											  'sub_can_post'            => $r['sub_can_post'],
											  'quick_reply'             => $r['quick_reply'],
											  'redirect_on'             => $r['redirect_on'],
											  'redirect_hits'           => $r['redirect_hits'],
											  'redirect_url'            => $r['redirect_url'],
											  'redirect_loc'		    => $r['redirect_loc'],
											  'rules_title'			    => $r['rules_title'],
  											  'rules_text'			    => $r['rules_text'],
											  'notify_modq_emails'      => $r['notify_modq_emails'],
											  'permission_array'        => $perm_array,
											  'permission_showtopic'    => '',
											  'permission_custom_error' => '',
									)       );
		}

		//-----------------------------------------
		// Convert categories
		//-----------------------------------------

		$DB->query("SELECT MAX(id) as max FROM ".SQL_PREFIX."forums");
		$max = $DB->fetch_row();

		$fid = $max['max'];

		$o = $DB->query("SELECT * FROM ".SQL_PREFIX."categories WHERE id > 0");

		while( $r = $DB->fetch_row( $o ) )
		{
			$fid++;

			$perm_array = addslashes(serialize(array(
													  'start_perms'  => '*',
													  'reply_perms'  => '*',
													  'read_perms'   => '*',
													  'upload_perms' => '*',
													  'show_perms'   => '*',
									)		  )     );

			$DB->do_insert( 'forums', array(
											 'id'               => $fid,
											 'position'         => $r['position'],
											 'name'             => $r['name'],
											 'sub_can_post'     => 0,
											 'permission_array' => $perm_array,
											 'parent_id'        => -1,
						  )                );

			//-----------------------------------------
			// Update old categories
			//-----------------------------------------

			$n = $DB->query("SELECT id FROM ".SQL_PREFIX."forums_bak WHERE category={$r['id']} AND parent_id = -1");

			$ids = array();

			while( $c = $DB->fetch_row($n) )
			{
				$ids[] = $c['id'];
			}

			if ( count($ids) )
			{
				$DB->do_update( 'forums', array( 'parent_id' => $fid ), 'id IN ('.implode(',',$ids).')' );
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 6: ALTER OTHER TABLES
	/*-------------------------------------------------------------------------*/

	function step_6()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."macro rename ibf_skin_macro;";
		$SQL[] = "alter table ".SQL_PREFIX."skin_macro change can_remove macro_can_remove tinyint(1) default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."groups add g_bypass_badwords tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."cache_store change cs_value cs_value mediumtext NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."cache_store add cs_array tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."sessions add in_error tinyint(1) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."topic_mmod add mm_forums text NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."groups change g_icon g_icon text NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."emoticons add emo_set varchar(64) NOT NULL default 'default';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change ID session_id varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change IP_ADDRESS session_ip_address varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change MEMBER_NAME session_member_name varchar(250) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change MEMBER_ID session_member_id mediumint(8) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change SESSION_KEY session_member_login_key varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change LOCATION session_location varchar(64) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change LOG_IN_TIME session_log_in_time int(10) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change RUNNING_TIME session_running_time int(10) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."forum_tracker add forum_track_type varchar(100) NOT NULL default 'delayed';";
		$SQL[] = "alter table ".SQL_PREFIX."tracker add topic_track_type varchar(100) NOT NULL default 'delayed';";

		$SQL[] = "delete from ".SQL_PREFIX."members where id=0 LIMIT 1;";
		$SQL[] = "delete from ".SQL_PREFIX."member_extra where id=0 limit 1;";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 5: ALTER MEMBERS TABLE
	/*-------------------------------------------------------------------------*/

	function step_5()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."members add login_anonymous varchar(3) NOT NULL default '0&0';";
		$SQL[] = "alter table ".SQL_PREFIX."members add ignored_users text NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."members add mgroup_others varchar(255) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."member_extra
		ADD aim_name varchar(40) NOT NULL default '',
		ADD icq_number int(15) NOT NULL default '',
		ADD website varchar(250) NOT NULL default '',
		ADD yahoo varchar(40) NOT NULL default '',
		ADD interests text NOT NULL default '',
		ADD msnname varchar(200) NOT NULL default '',
		ADD vdirs text NOT NULL default '',
		ADD location varchar(250) NOT NULL default '',
		ADD signature text NOT NULL default '',
		ADD avatar_location varchar(128) NOT NULL default '',
		ADD avatar_size varchar(9) NOT NULL default '',
		ADD avatar_type varchar(15) NOT NULL default 'local';";
		$SQL[] = "alter table ".SQL_PREFIX."members add member_login_key varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."members change password legacy_password varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."members add subs_pkg_chosen smallint(3) NOT NULL default '0';";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 4: ALTER POST TABLE
	/*-------------------------------------------------------------------------*/

	function step_4()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."topics ADD topic_hasattach smallint(5) NOT NULL default '0';";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 3: ALTER POST TABLE
	/*-------------------------------------------------------------------------*/

	function step_3()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "alter table ".SQL_PREFIX."posts add post_parent int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts ADD post_key varchar(32) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts add post_htmlstate smallint(1) NOT NULL default '0';";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 2: DROP FORUMS TABLE, CREATE NEW TABLES
	/*-------------------------------------------------------------------------*/

	function step_2()
	{
		global $DB, $std, $ibforums;

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."attachments (
		   attach_id int(10) NOT NULL auto_increment,
		   attach_ext varchar(10) NOT NULL default '',
		   attach_file varchar(250) NOT NULL default '',
		   attach_location varchar(250) NOT NULL default '',
		   attach_thumb_location varchar(250) NOT NULL default '',
		   attach_thumb_width smallint(5) NOT NULL default '0',
		   attach_thumb_height smallint(5) NOT NULL default '0',
		   attach_is_image tinyint(1) NOT NULL default '0',
		   attach_hits int(10) NOT NULL default '0',
		   attach_date int(10) NOT NULL default '0',
		   attach_temp tinyint(1) NOT NULL default '0',
		   attach_pid int(10) NOT NULL default '0',
		   attach_post_key varchar(32) NOT NULL default '0',
		   attach_msg int(10) NOT NULL default '0',
		   attach_member_id mediumint(8) NOT NULL default '0',
		   attach_approved int(10) NOT NULL default '1',
		   attach_filesize int(10) NOT NULL default '0',
		PRIMARY KEY (attach_id),
		KEY attach_pid (attach_pid),
		KEY attach_msg (attach_msg),
		KEY attach_post_key (attach_post_key),
		KEY attach_mid_size (attach_member_id, attach_filesize)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."message_text (
		 msg_id int(10) NOT NULL auto_increment,
		 msg_date int(10) default NULL,
		 msg_post text default '',
		 msg_cc_users text default '',
		 msg_sent_to_count smallint(5) NOT NULL default '0',
		 msg_deleted_count smallint(5) NOT NULL default '0',
		 msg_post_key varchar(32) NOT NULL default '0',
		 msg_author_id mediumint(8) NOT NULL default '0',
		PRIMARY KEY (msg_id),
		KEY msg_date (msg_date),
		KEY msg_sent_to_count (msg_sent_to_count),
		KEY msg_deleted_count (msg_deleted_count)
		);";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."message_topics (
		   mt_id int(10) NOT NULL auto_increment,
		   mt_msg_id int(10) NOT NULL default '0',
		   mt_date int(10) NOT NULL default '0',
		   mt_title varchar(255) NOT NULL default '',
		   mt_from_id mediumint(8) NOT NULL default '0',
		   mt_to_id mediumint(8) NOT NULL default '0',
		   mt_owner_id mediumint(8) NOT NULL default '0',
		   mt_vid_folder varchar(32) NOT NULL default '',
		   mt_read tinyint(1) NOT NULL default '0',
		   mt_hasattach smallint(5) NOT NULL default '0',
		   mt_hide_cc tinyint(1) default '0',
		   mt_tracking tinyint(1) default '0',
		   mt_user_read int(10) default '0',
		PRIMARY KEY (mt_id),
		KEY mt_from_id (mt_from_id),
		KEY mt_owner_id (mt_owner_id, mt_to_id, mt_vid_folder)
		);";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."skin_sets (
		   set_skin_set_id int(10) NOT NULL auto_increment,
		   set_name varchar(150) NOT NULL default '',
		   set_image_dir varchar(200) NOT NULL default '',
		   set_hidden tinyint(1) NOT NULL default '0',
		   set_default tinyint(1) NOT NULL default '0',
		   set_css_method varchar(100) NOT NULL default 'inline',
		   set_skin_set_parent smallint(5) NOT NULL default '-1',
		   set_author_email varchar(255) NOT NULL default '',
		   set_author_name varchar(255) NOT NULL default '',
		   set_author_url varchar(255) NOT NULL default '',
		   set_css mediumtext NOT NULL default '',
		   set_wrapper mediumtext NOT NULL default '',
		   set_css_updated int(10) NOT NULL default '0',
		   set_cache_css mediumtext NOT NULL default '',
		   set_cache_macro mediumtext NOT NULL default '',
		   set_cache_wrapper mediumtext NOT NULL default '',
		   set_emoticon_folder varchar(60) NOT NULL default 'default',
		   PRIMARY KEY(set_skin_set_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."skin_templates_cache (
		   template_id varchar(32) NOT NULL default '',
		   template_group_name varchar(255) NOT NULL default '',
		   template_group_content mediumtext NOT NULL default '',
		   template_set_id int(10) NOT NULL default '0',
		   primary key (template_id),
		   KEY template_set_id (template_set_id),
		   KEY template_group_name (template_group_name)
	   );";


	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."mail_queue(
		   mail_id int(10) auto_increment NOT NULL,
		   mail_date int(10) NOT NULL default '0',
		   mail_to varchar(255) NOT NULL default '',
		   mail_from varchar(255) NOT NULL default '',
		   mail_subject text NOT NULL default '',
		   mail_content text NOT NULL default '',
		   mail_type varchar(200) NOT NULL default '',
		   PRIMARY KEY (mail_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."task_manager (
		   task_id int(10) auto_increment NOT NULL,
		   task_title varchar(255) NOT NULL default '',
		   task_file varchar(255) NOT NULL default '',
		   task_next_run int(10) NOT NULL default '',
		   task_week_day tinyint(1) NOT NULL default '-1',
		   task_month_day smallint(2) NOT NULL default '-1',
		   task_hour smallint(2) NOT NULL default '-1',
		   task_minute smallint(2) NOT NULL default '-1',
		   task_cronkey varchar(32) NOT NULL default '',
		   task_log tinyint(1) NOT NULL default '0',
		   task_description text NOT NULL default '',
		   task_enabled tinyint(1) NOT NULL default '1',
		   task_key varchar(30) NOT NULL default '',
		   task_safemode tinyint(1) NOT NULL default '',
		   PRIMARY KEY(task_id),
		   KEY task_next_run (task_next_run)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."task_logs (
		   log_id int(10) auto_increment NOT NULL,
		   log_title varchar(255) NOT NULL default '',
		   log_date int(10) NOT NULL default '0',
		   log_ip varchar(16) NOT NULL default '0',
		   log_desc text NOT NULL default '',
		   PRIMARY KEY(log_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."custom_bbcode (
		   bbcode_id int(10) NOT NULL auto_increment,
		   bbcode_title varchar(255) NOT NULL default '',
		   bbcode_desc text NOT NULL default '',
		   bbcode_tag varchar(255) NOT NULL default '',
		   bbcode_replace text NOT NULL default '',
		   bbcode_useoption tinyint(1) NOT NULL default '',
		   bbcode_example text NOT NULL default '',
		   PRIMARY KEY (bbcode_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."conf_settings (
		   conf_id int(10) NOT NULL auto_increment,
		   conf_title varchar(255) NOT NULL default '',
		   conf_description text NOT NULL default '',
		   conf_group smallint(3) NOT NULL default '',
		   conf_type varchar(255) NOT NULL default '',
		   conf_key varchar(255) NOT NULL default '',
		   conf_value text NOT NULL default '',
		   conf_default text NOT NULL default '',
		   conf_extra text NOT NULL default '',
		   conf_evalphp text NOT NULL default '',
		   conf_protected tinyint(1) NOT NULL default '',
		   conf_position smallint(3) NOT NULL default '0',
		   conf_start_group varchar(255) NOT NULL default '',
		   conf_end_group tinyint(1) NOT NULL default '0',
		   conf_help_key varchar(255) NOT NULL default '0',
		   conf_add_cache tinyint(1) NOT NULL default '1',
		   PRIMARY KEY (conf_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."conf_settings_titles (
		   conf_title_id smallint(3) NOT NULL auto_increment,
		   conf_title_title varchar(255) NOT NULL default '',
		   conf_title_desc text NOT NULL default '',
		   conf_title_count smallint(3) NOT NULL default '0',
		   conf_title_noshow tinyint(1) NOT NULL default '0',
		   conf_title_keyword varchar(200) NOT NULL default '',
		   PRIMARY KEY(conf_title_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."topics_read (
		 read_tid int(10) NOT NULL default '0',
		 read_mid mediumint(8) NOT NULL default '0',
		 read_date int(10) NOT NULL default '0',
		 UNIQUE KEY read_tid_mid( read_tid, read_mid )
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."banfilters (
		   ban_id int(10) NOT NULL auto_increment,
		   ban_type varchar(10) NOT NULL default 'ip',
		   ban_content text NOT NULL default '',
		   ban_date int(10) NOT NULL default '0',
		   PRIMARY KEY (ban_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."attachments_type (
		   atype_id int(10) NOT NULL auto_increment,
		   atype_extension varchar(18) NOT NULL default '',
		   atype_mimetype varchar(255) NOT NULL default '',
		   atype_post tinyint(1) NOT NULL default '1',
		   atype_photo tinyint(1) NOT NULL default '0',
		   atype_img text NOT NULL default '',
		   PRIMARY KEY (atype_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."members_converge (
		   converge_id int(10) auto_increment NOT NULL,
		   converge_email varchar(250) NOT NULL default '',
		   converge_joined int(10) NOT NULL default '',
		   converge_pass_hash varchar(32) NOT NULL default '',
		   converge_pass_salt varchar(5) NOT NULL default '',
		   PRIMARY KEY( converge_id )
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."announcements (
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

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."mail_error_logs (
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

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."bulk_mail (
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

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."upgrade_history (
		   upgrade_id int(10) NOT NULL auto_increment,
		   upgrade_version_id int(10) NOT NULL default '',
		   upgrade_version_human varchar(200) NOT NULL default '',
		   upgrade_date int(10) NOT NULL default '0',
		   upgrade_mid int(10) NOT NULL default '0',
		   upgrade_notes text NOT NULL default '',
		   PRIMARY KEY (upgrade_id)
	   );";

	   $SQL[] = "DROP TABLE ".SQL_PREFIX."forums";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."forums (
				 id smallint(5) NOT NULL default '0',
				 topics mediumint(6) default '0',
				 posts mediumint(6) default '0',
				 last_post int(10) default NULL,
				 last_poster_id mediumint(8) NOT NULL default '0',
				 last_poster_name varchar(32) default NULL,
				 name varchar(128) NOT NULL default '',
				 description text,
				 position tinyint(2) default NULL,
				 use_ibc tinyint(1) default NULL,
				 use_html tinyint(1) default NULL,
				 status varchar(10) default NULL,
				 password varchar(32) default NULL,
				 last_title varchar(128) default NULL,
				 last_id int(10) default NULL,
				 sort_key varchar(32) default NULL,
				 sort_order varchar(32) default NULL,
				 prune tinyint(3) default NULL,
				 show_rules tinyint(1) default NULL,
				 preview_posts tinyint(1) default NULL,
				 allow_poll tinyint(1) NOT NULL default '1',
				 allow_pollbump tinyint(1) NOT NULL default '0',
				 inc_postcount tinyint(1) NOT NULL default '1',
				 skin_id int(10) default NULL,
				 parent_id mediumint(5) default '-1',
				 sub_can_post tinyint(1) default '1',
				 quick_reply tinyint(1) default '0',
				 redirect_url varchar(250) default '',
				 redirect_on tinyint(1) NOT NULL default '0',
				 redirect_hits int(10) NOT NULL default '0',
				 redirect_loc varchar(250) default '',
				 rules_title varchar(255) NOT NULL default '',
				 rules_text text NOT NULL,
				 topic_mm_id varchar(250) NOT NULL default '',
				 notify_modq_emails text default '',
				 permission_custom_error text NOT NULL default '',
				 permission_array mediumtext NOT NULL default '',
				 permission_showtopic tinyint(1) NOT NULL default '0',
				 queued_topics mediumint(6) NOT NULL default '0',
				 queued_posts  mediumint(6) NOT NULL default '0',
				 PRIMARY KEY  (id),
				 KEY position (position, parent_id)
			   );";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscriptions (
		 sub_id smallint(5) NOT NULL auto_increment,
		 sub_title varchar(250) NOT NULL default '',
		 sub_desc text,
		 sub_new_group mediumint(8) NOT NULL default 0,
		 sub_length smallint(5) NOT NULL default '1',
		 sub_unit varchar(2) NOT NULL default 'm',
		 sub_cost decimal(10,2) NOT NULL default '0.00',
		 sub_run_module varchar(250) NOT NULL default '',
		 PRIMARY KEY (sub_id)
		) TYPE=MyISAM;";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_extra (
		 subextra_id smallint(5) NOT NULL auto_increment,
		 subextra_sub_id smallint(5) NOT NULL default '0',
		 subextra_method_id smallint(5) NOT NULL default '0',
		 subextra_product_id varchar(250) NOT NULL default '0',
		 subextra_can_upgrade tinyint(1) NOT NULL default '0',
		 subextra_recurring tinyint(1) NOT NULL default '0',
		 subextra_custom_1 text,
		 subextra_custom_2 text,
		 subextra_custom_3 text,
		 subextra_custom_4 text,
		 subextra_custom_5 text,
		 PRIMARY KEY(subextra_id)
		) TYPE=MyISAM;";


		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_trans (
		 subtrans_id int(10) NOT NULL auto_increment,
		 subtrans_sub_id smallint(5) NOT NULL default '0',
		 subtrans_member_id mediumint(8) NOT NULL default '0',
		 subtrans_old_group smallint(5) NOT NULL default '0',
		 subtrans_paid decimal(10,2) NOT NULL default '0.00',
		 subtrans_cumulative decimal(10,2) NOT NULL default '0.00',
		 subtrans_method varchar(20) NOT NULL default '',
		 subtrans_start_date int(11) NOT NULL default '0',
		 subtrans_end_date int(11) NOT NULL default '0',
		 subtrans_state varchar(200) NOT NULL default '',
		 subtrans_trxid varchar(200) NOT NULL default '',
		 subtrans_subscrid varchar(200) NOT NULL default '',
		 subtrans_currency varchar(10) NOT NULL default 'USD',
		 PRIMARY KEY (subtrans_id)
		) TYPE=MyISAM;";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_logs (
		 sublog_id int(10) NOT NULL auto_increment,
		 sublog_date int(10) NOT NULL default '',
		 sublog_member_id mediumint(8) NOT NULL default '0',
		 sublog_transid int(10) NOT NULL default '',
		 sublog_ipaddress varchar(16) NOT NULL default '',
		 sublog_data text,
		 sublog_postdata text,
		 PRIMARY KEY (sublog_id)
		) TYPE=MyISAM;";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_methods (
		 submethod_id smallint(5) NOT NULL auto_increment,
		 submethod_title varchar(250) NOT NULL default '',
		 submethod_name varchar(20) NOT NULL default '',
		 submethod_email varchar(250) NOT NULL default '',
		 submethod_sid text,
		 submethod_custom_1 text,
		 submethod_custom_2 text,
		 submethod_custom_3 text,
		 submethod_custom_4 text,
		 submethod_custom_5 text,
		 submethod_is_cc tinyint(1) NOT NULL default '0',
		 submethod_is_auto tinyint(1) NOT NULL default '0',
		 submethod_desc text,
		 submethod_logo text,
		 submethod_active tinyint(1) NOT NULL default '',
		 submethod_use_currency varchar(10) NOT NULL default 'USD',
		 PRIMARY KEY (submethod_id)
		) TYPE=MyISAM;";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_currency (
		 subcurrency_code varchar(10) NOT NULL,
		 subcurrency_desc varchar(250) NOT NULL default '',
		 subcurrency_exchange decimal(10, 8) NOT NULL,
		 subcurrency_default tinyint(1) NOT NULL default '0',
		 PRIMARY KEY(subcurrency_code)
		) TYPE=MyISAM;";


		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 1: COPY AND POPULATE BACK UP FORUMS TABLE
	/*-------------------------------------------------------------------------*/

	function step_1()
	{
		global $DB, $std, $ibforums;

		$table = $DB->sql_get_table_schematic( 'forums' );

		$SQL[] = str_replace( SQL_PREFIX."forums", SQL_PREFIX."forums_bak", $table['Create Table'] );

		$SQL[] = "INSERT INTO ".SQL_PREFIX."forums_bak SELECT * FROM ".SQL_PREFIX."forums";

		$this->error   = array();
		$this->sqlcount = 0;

		$DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$DB->query( $query );

			if ( $DB->error )
			{
				$this->error[] = $DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}

		//-----------------------------------------
		// Check...
		//-----------------------------------------

		$DB->query( "SELECT COUNT(*) as count FROM ".SQL_PREFIX."forums_bak" );
		$count = $DB->fetch_row();

		if ( intval( $count['count'] ) < 1 )
		{
			print "The back-up forums table has not been populated successfully. Continuing this convert WILL delete all forums permanently. Contact technical support immediately.";
			exit();
		}
	}

	//#------------------------------------------------------------------------
	// OTHER SQL WORK
	//#------------------------------------------------------------------------

	function sql_members($a, $b)
	{
		return "SELECT m.*, me.id as mextra FROM ibf_members m LEFT JOIN ibf_member_extra me ON ( me.id=m.id ) LIMIT $a, $b";
	}

	function sql_members_email( $a )
	{
		return "select id, name, email, count(email) as count from ibf_members group by email order by count desc LIMIT 0, $a";
	}

	function sql_members_email_update( $push_auth )
	{
		return "UPDATE ibf_members SET email=concat( id, '-', email ) where id IN(".implode(",", $push_auth).")";
	}

	function sql_members_converge( $start, $end )
	{
		return "SELECT m.*, c.converge_id as cid FROM ibf_members m LEFT JOIN ibf_members_converge c ON ( c.converge_id=m.id ) WHERE id >= $start and id < $end";
	}

}













?>