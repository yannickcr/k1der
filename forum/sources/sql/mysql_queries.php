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
|   > MySQL DB Queries abstraction module
|   > Module written by Matt Mecham
|   > Date started: 26th November 2003
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/



class sql_queries extends db_driver
{

     var $db  = "";
     var $tbl = "";

    /*========================================================================*/
    // Set up...
    /*========================================================================*/

    function sql_queries( $obj )
    {
    	$this->db = &$obj;

    	if ( ! $this->db->obj['sql_tbl_prefix'] )
    	{
    		$this->db->obj['sql_tbl_prefix'] = 'ibf_';
    	}

    	$this->tbl = $this->db->obj['sql_tbl_prefix'];
    }

    /*========================================================================*/

    function forums_get_active_users( $a )
    {
    	# forums.php

    	return "SELECT s.member_id, s.member_name, s.member_group, s.id, s.login_type, s.location, s.running_time, t.forum_id
				FROM ".SQL_PREFIX."sessions s
				 LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=s.in_topic)
				WHERE (s.in_forum={$a['fid']} OR t.forum_id={$a['fid']})
				AND s.running_time > {$a['time']}
				AND s.in_error != 1";
    }

    function forums_get_replied_topics( $a )
    {
    	return  "SELECT COUNT(DISTINCT(p.topic_id)) as max FROM ".SQL_PREFIX."topics t
				  LEFT JOIN ".SQL_PREFIX."posts p ON (p.topic_id=t.tid)
				 WHERE t.forum_id={$a['fid']} AND p.author_id={$a['mid']} AND p.new_topic=0
				 {$a['approved']} {$a['prune_filter']}";
    }

    function forums_get_replied_topics_actual( $a )
    {
    	return  "SELECT DISTINCT(p.author_id), t.* FROM ".SQL_PREFIX."topics t
				  LEFT JOIN ".SQL_PREFIX."posts p ON (p.topic_id=t.tid AND p.author_id={$a['mid']})
				 WHERE t.forum_id={$a['fid']}
				 AND {$a['query']}  AND p.new_topic=0
				 ORDER BY pinned desc,{$a['topic_sort']} {$a['sort_key']} {$a['r_sort_by']}
				 LIMIT {$a['limit_a']}, {$a['limit_b']}";
    }

    function topics_check_for_mod( $a )
    {
    	# topics.php

    	return "SELECT * FROM ".SQL_PREFIX."moderators WHERE forum_id={$a['fid']} AND (member_id={$a['mid']} OR (is_group=1 AND group_id={$a['gid']}))";

    }


    function topics_get_posts( $a )
    {
    	# topics.php

    	return "SELECT p.*,
				m.id,m.name,m.mgroup,m.email,m.joined,m.posts, m.last_visit, m.last_activity,m.login_anonymous,m.title,m.hide_email, m.warn_level, m.warn_lastwarn,
				me.msnname,me.aim_name,me.icq_number,me.signature, me.website,me.yahoo,me.location, me.avatar_location, me.avatar_type, me.avatar_size
				FROM ".SQL_PREFIX."posts p
				  LEFT JOIN ".SQL_PREFIX."members m ON (p.author_id=m.id)
				  LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
				WHERE p.pid IN(".implode(',', $a['pids']).") ORDER BY {$a['scol']} {$a['sord']}";

    }

    function topics_get_posts_with_join( $a )
    {
    	# topics.php

    	return "SELECT p.*,
				m.id,m.name,m.mgroup,m.email,m.joined,m.posts, m.last_visit, m.last_activity,m.login_anonymous,m.title,m.hide_email, m.warn_level, m.warn_lastwarn,
				me.msnname,me.aim_name,me.icq_number,me.signature, me.website,me.yahoo,me.location, me.avatar_location, me.avatar_type, me.avatar_size,
				pc.*
				FROM ".SQL_PREFIX."posts p
				  LEFT JOIN ".SQL_PREFIX."members m ON (p.author_id=m.id)
				  LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
				  LEFT JOIN ".SQL_PREFIX."pfields_content pc ON (pc.member_id=p.author_id)
				WHERE p.pid IN(".implode(',', $a['pids']).") ORDER BY {$a['scol']} {$a['sord']}";

    }

    function topics_get_active_users( $a )
    {
    	# topics.php

    	return "SELECT s.member_id, s.member_name, s.member_group, s.id, s.login_type, s.location, s.running_time
				FROM ".SQL_PREFIX."sessions s
				WHERE s.in_topic={$a['tid']}
				AND s.running_time > {$a['time']}
				AND s.in_error != 1";
	}

	function topics_replace_topic_read( $a )
	{
		# topics.php
		# Not got REPLACE INTO? Use delete from .. where, then insert into ... set...

		return "REPLACE INTO ".SQL_PREFIX."topics_read SET read_tid={$a['tid']},read_mid={$a['mid']},read_date={$a['date']}";
	}



	function session_load_member( $a )
	{
		return "SELECT m.id, m.name, m.mgroup, m.member_login_key, m.email, m.restrict_post, m.view_sigs, m.view_avs, m.view_pop, m.view_img, m.auto_track,
				m.mod_posts, m.language, m.skin, m.new_msg, m.show_popup, m.msg_total, m.time_offset, m.posts, m.joined, m.last_post, m.subs_pkg_chosen,
				m.ignored_users, m.login_anonymous, m.last_visit, m.last_activity, m.dst_in_use, m.view_prefs, m.org_perm_id, m.mgroup_others, m.temp_ban, m.sub_end,
				m.has_blog
				FROM ".SQL_PREFIX."members m
				WHERE m.id={$a['mid']}";
	}

	function post_topic_tracker( $a )
	{
		#post

		return "SELECT tr.trid, tr.topic_id, m.name, m.email, m.id, m.email_full, m.language, m.org_perm_id, m.mgroup, m.last_activity, t.title, t.forum_id
				FROM ".SQL_PREFIX."tracker tr, ".SQL_PREFIX."topics t,".SQL_PREFIX."members m
				WHERE tr.topic_id='{$a['tid']}'
				AND tr.member_id=m.id
				AND m.id <> {$a['mid']}
				AND t.tid=tr.topic_id
				AND ( ( tr.topic_track_type='delayed' AND m.last_activity > {$a['last_post']} ) OR tr.topic_track_type='immediate' )";

	}

	function post_forum_tracker( $a )
	{
		#post

		return "SELECT tr.frid, m.name, m.email, m.id, m.language, m.last_activity, m.org_perm_id, g.g_perm_id
				FROM ".SQL_PREFIX."forum_tracker tr,".SQL_PREFIX."members m, ".SQL_PREFIX."groups g
				WHERE tr.forum_id={$a['fid']}
				AND tr.member_id=m.id
				AND m.mgroup=g.g_id
				AND m.id <> {$a['mid']}
				AND ( ( tr.forum_track_type='delayed' AND m.last_activity < {$a['last_post']} ) OR tr.forum_track_type='immediate' )";
	}


	function post_get_quoted( $a )
	{
		return "select p.*,t.forum_id FROM ".SQL_PREFIX."posts p LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=p.topic_id)
				WHERE pid IN (".implode(",", $a['quoted_pids']).")";

	}

	function msg_get_msg_poster( $a )
	{
		return "SELECT m.*, g.* FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."groups g WHERE m.id={$a['mid']} AND g.g_id=m.mgroup";
	}

	function msg_get_msg_archive( $a )
	{
		return "SELECT m.id,m.name,m.mgroup,m.email,m.joined,m.posts, m.last_visit, m.last_activity,m.login_anonymous,m.title,m.hide_email, m.warn_level, m.warn_lastwarn,
				g.g_id, g.g_title, g.g_icon, g.g_dohtml,
				me.msnname,me.aim_name,me.icq_number,me.signature, me.website,me.yahoo,me.location, me.avatar_location, me.avatar_type, me.avatar_size,
				mt.*, msg.*
				FROM ".SQL_PREFIX."message_topics mt
				 LEFT JOIN ".SQL_PREFIX."message_text msg ON (mt.mt_msg_id=msg.msg_id)
				 LEFT JOIN ".SQL_PREFIX."members m ON (mt.mt_from_id=m.id)
				 LEFT JOIN ".SQL_PREFIX."groups g ON (g.g_id=m.mgroup)
				 LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
				WHERE mt.mt_owner_id={$a['mid']} AND mt.mt_date {$a['older_newer']} {$a['time_cut']} {$a['folder_query']}
				LIMIT 0, {$a['limit_b']}";
	}

	function msg_get_msg_to_show( $a )
	{
		return "SELECT m.id,m.name,m.mgroup,m.email,m.joined,m.posts, m.last_visit, m.last_activity,m.login_anonymous,m.title,m.hide_email, m.warn_level, m.warn_lastwarn,
				g.g_id, g.g_title, g.g_icon, g.g_dohtml,
				me.msnname,me.aim_name,me.icq_number,me.signature, me.website,me.yahoo,me.location, me.avatar_location, me.avatar_type, me.avatar_size,
				mt.*, msg.*
				FROM ".SQL_PREFIX."message_topics mt
				 LEFT JOIN ".SQL_PREFIX."message_text msg ON (mt.mt_msg_id=msg.msg_id)
				 LEFT JOIN ".SQL_PREFIX."members m ON (mt.mt_from_id=m.id)
				 LEFT JOIN ".SQL_PREFIX."groups g ON (g.g_id=m.mgroup)
				 LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
				WHERE mt.mt_id={$a['msgid']} AND mt.mt_owner_id={$a['mid']}";
	}

	function msg_get_saved_msg( $a )
	{
		return "SELECT m.id,m.name, mt.*, msg.*
				FROM ".SQL_PREFIX."message_topics mt
				 LEFT JOIN ".SQL_PREFIX."message_text msg ON (mt.mt_msg_id=msg.msg_id)
				 LEFT JOIN ".SQL_PREFIX."members m ON (mt.mt_to_id=m.id)
				WHERE mt.mt_id={$a['msgid']} AND mt.mt_owner_id={$a['mid']}";
	}

	function msg_get_cc_users( $a )
	{

		return "SELECT m.mgroup_others, m.id, m.name, m.msg_total, m.view_pop, m.email_pm, m.language, m.email, me.vdirs, g.g_max_messages, g.g_use_pm FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."groups g
		        LEFT JOIN ".SQL_PREFIX."member_extra me ON (m.id=me.id)
			   WHERE LOWER(m.name) IN (".implode(",",$a['name_array']).")
			   AND m.mgroup=g.g_id";


	}

	function msg_get_cc_blocked( $a )
	{

		return "SELECT m.name, c.allow_msg
				FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."contacts c
				WHERE contact_id={$a['mid']}
				AND member_id IN (".implode(",",$a['cc_array']).") AND m.id=c.member_id";

	}

	function msg_get_sent_list( $a )
	{
		return "SELECT mem.name as from_name, mem.id as from_id, mt.*
				 FROM ".SQL_PREFIX."message_topics mt
				 LEFT JOIN ".SQL_PREFIX."members mem ON ( mt.mt_to_id=mem.id )
				WHERE mt.mt_owner_id={$a['mid']} AND mt.mt_from_id={$a['mid']} AND mt.mt_vid_folder='{$a['vid']}'
				ORDER BY {$a['sort']} LIMIT {$a['limita']}, {$a['limitb']}";
	}

	function msg_get_folder_list( $a )
	{

		return "SELECT mt.*,mem.name as from_name, mem.id as from_id
				 FROM ".SQL_PREFIX."message_topics mt
				 LEFT JOIN ".SQL_PREFIX."members mem ON ( mt.mt_from_id=mem.id )
				WHERE mt.mt_owner_id={$a['mid']} AND mt.mt_to_id={$a['mid']}  AND mt.mt_vid_folder='{$a['vid']}'
				ORDER BY mt.{$a['sort']} LIMIT {$a['limita']}, {$a['limitb']}";
	}

	function msg_get_tracking( $a )
	{
		return "SELECT msg.*, mt.*, mp.name as to_name, mp.id as memid
				 FROM ".SQL_PREFIX."message_topics mt
				  LEFT JOIN ".SQL_PREFIX."message_text msg ON ( mt.mt_msg_id=msg.msg_id )
				  LEFT JOIN ".SQL_PREFIX."members mp ON (mt.mt_to_id=mp.id)
				WHERE mt.mt_from_id={$a['mid']} AND mt.mt_tracking=1";

	}

	function msg_get_new_pm_notification( $a )
	{
		return "SELECT m.id,m.name,m.mgroup,m.email,m.joined,m.posts, m.last_visit, m.last_activity,
				 m.warn_level, m.warn_lastwarn,
				 me.*,
				g.g_id, g.g_title, g.g_icon, g.g_dohtml, mt.*, msg.*
				FROM ".SQL_PREFIX."message_topics mt
				 LEFT JOIN ".SQL_PREFIX."message_text msg ON (mt.mt_msg_id=msg.msg_id)
				 LEFT JOIN ".SQL_PREFIX."members m ON (mt.mt_from_id=m.id)
				 LEFT JOIN ".SQL_PREFIX."member_extra me ON (mt.mt_from_id=me.id)
				 LEFT JOIN ".SQL_PREFIX."groups g ON (g.g_id=m.mgroup)
				WHERE mt.mt_owner_id={$a['mid']} AND mt.mt_vid_folder='in' ORDER BY mt_date DESC LIMIT 1";
	}

	function ucp_tracker_prune( $a )
	{
		return "SELECT tr.trid FROM ".SQL_PREFIX."tracker tr, ".SQL_PREFIX."topics t WHERE t.tid=tr.topic_id AND t.last_post < {$a['time']}";
	}

	function profile_get_all( $a )
	{
		return "SELECT m.*, me.*, s.running_time, s.location as sesslocation, s.in_forum, s.in_topic FROM ".SQL_PREFIX."members m
					LEFT JOIN ".SQL_PREFIX."sessions s ON (s.member_id=m.id)
					LEFT JOIN ".SQL_PREFIX."member_extra me ON ( me.id=m.id )
				WHERE m.id={$a['mid']}";
	}

	function profile_get_favourite( $a )
	{
		return "SELECT t.forum_id, COUNT(p.author_id) as f_posts
    				FROM ".SQL_PREFIX."posts p
    				  LEFT JOIN ".SQL_PREFIX."topics t ON ( p.topic_id=t.tid AND t.forum_id IN (".implode(",",$a['fid_array']).") )
    			    WHERE p.author_id={$a['mid']} AND t.tid IS NOT NULL
    			    GROUP BY t.forum_id
    			    ORDER BY f_posts DESC";
	}

	function attach_get_perms( $a )
	{
		return "SELECT p.pid, p.topic_id, t.forum_id
    				FROM ".SQL_PREFIX."posts p
    				  LEFT JOIN ".SQL_PREFIX."topics t ON ( p.topic_id=t.tid )
    			    WHERE p.pid={$a['apid']}";
	}


	function usercp_get_attachments( $a )
	{
		return "SELECT a.*, t.*, p.topic_id
				 FROM ".SQL_PREFIX."attachments a
				  LEFT JOIN ".SQL_PREFIX."posts p ON ( a.attach_pid=p.pid )
				  LEFT JOIN ".SQL_PREFIX."topics t ON ( t.tid=p.topic_id )
				 WHERE a.attach_member_id={$a['mid']}
				 ORDER BY {$a['order']}
				 LIMIT {$a['limit_a']}, {$a['limit_b']}";

	}

	function usercp_get_to_delete( $a )
	{
		return "SELECT a.*, p.topic_id, p.pid
				 FROM ".SQL_PREFIX."attachments a
				  LEFT JOIN ".SQL_PREFIX."posts p ON ( a.attach_pid=p.pid )
				 WHERE a.attach_id IN (".implode(",",$a['aid_array']).")
				 AND attach_member_id={$a['mid']}";

	}



	function stats_get_all_members( $a )
	{
		return "SELECT m.*, me.*
    			 FROM ".SQL_PREFIX."members m
    				LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
    			 WHERE m.id IN(".implode(',', $a['member_ids']).")
    			 ORDER BY m.name";
	}

	function stats_get_all_members_groups( $a )
	{
		return "SELECT m.*, me.*
    			 FROM ".SQL_PREFIX."members m
    				LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
    			 WHERE mgroup IN (".implode( ',', $a['group_ids'] ).")
    			 ORDER BY m.name";
	}

	function stats_get_todays_posters( $a )
	{
		return "SELECT COUNT(*) as tpost, m.id, m.name, m.joined, m.posts
				 FROM ".SQL_PREFIX."posts p
					LEFT JOIN ".SQL_PREFIX."members m ON (m.id=p.author_id )
					LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=p.topic_id)
				WHERE t.forum_id in(".implode(",", $a['ids']).") and post_date > {$a['time_low']}
				GROUP BY p.author_id
				ORDER BY tpost DESC LIMIT 0,10";
	}

	function ucp_mod_ip_tool_one( $a )
	{
		return "SELECT pid, t.forum_id
				FROM ".SQL_PREFIX."posts p, ".SQL_PREFIX."topics t
				WHERE t.tid=p.topic_id and p.queued=0 AND t.forum_id IN({$a['the_forums']}) AND {$a['sql']}";
	}

	function ucp_get_all_announcements( $a )
	{
		return "SELECT a.*, m.id, m.name
				 FROM ".SQL_PREFIX."announcements a
				  LEFT JOIN ".SQL_PREFIX."members m on (a.announce_member_id=m.id)
				 ORDER BY announce_end DESC";
	}

	function ucp_get_all_announcements_byid( $a )
	{
		return "SELECT a.*, m.*, me.*
				 FROM ".SQL_PREFIX."announcements a
				  LEFT JOIN ".SQL_PREFIX."members m on (a.announce_member_id=m.id)
				  LEFT JOIN ".SQL_PREFIX."member_extra me on (me.id=m.id)
				 WHERE a.announce_id={$a['id']}";
	}

	function ucp_get_forum_tracker( $a )
	{
		return "SELECT t.*, f.*
				FROM ".SQL_PREFIX."forum_tracker t
				 LEFT JOIN ".SQL_PREFIX."forums f ON (t.forum_id=f.id)
				WHERE t.member_id={$a['mid']}
				ORDER BY f.position";
	}

	function ucp_get_topic_tracker( $a )
	{
		return "SELECT s.topic_track_type, s.trid, s.member_id, s.topic_id, s.last_sent, s.start_date as track_started, t.*, f.id as forum_id, f.name as forum_name
				FROM ".SQL_PREFIX."tracker s, ".SQL_PREFIX."topics t, ".SQL_PREFIX."forums f
				WHERE s.member_id={$a['mid']} AND t.tid=s.topic_id AND f.id=t.forum_id {$a['date_query']}
				ORDER BY f.id, t.last_post DESC";
	}

	function mlist_count( $a )
	{
		return "SELECT COUNT(*) as total_members FROM ".SQL_PREFIX."members m
				LEFT JOIN ".SQL_PREFIX."member_extra me ON me.id=m.id
				LEFT JOIN ".SQL_PREFIX."pfields_content p ON (p.member_id=m.id)
				LEFT JOIN ".SQL_PREFIX."groups g ON (g.g_id=m.mgroup)
				WHERE g.g_hide_from_list <> 1 {$a['query']}";
	}

	function mlist_get_members( $a )
	{
		return "SELECT m.*,me.*,p.*,g.g_hide_from_list,g.g_id FROM ".SQL_PREFIX."members m
				LEFT JOIN ".SQL_PREFIX."member_extra me ON me.id=m.id
				LEFT JOIN ".SQL_PREFIX."pfields_content p ON (p.member_id=m.id)
				LEFT JOIN ".SQL_PREFIX."groups g ON (g.g_id=m.mgroup)
				WHERE g.g_hide_from_list <> 1 {$a['query']}
				ORDER BY m.{$a['sort']} {$a['order']}
				LIMIT {$a['limit_a']}, {$a['limit_b']}";
	}

	function forum_get_attachments( $a )
	{
		return "SELECT a.*, t.*, p.topic_id, p.pid
				 FROM ".SQL_PREFIX."attachments a
				  LEFT JOIN ".SQL_PREFIX."posts p ON ( a.attach_pid=p.pid )
				  LEFT JOIN ".SQL_PREFIX."topics t ON ( t.tid=p.topic_id )
				 WHERE a.attach_pid != 0 AND p.topic_id={$a['tid']}
				 ORDER BY a.attach_date";

	}

	function buddy_posts_last_visit( $a )
	{
		return "SELECT COUNT(*) as posts
				FROM ".SQL_PREFIX."posts p
				 LEFT JOIN ".SQL_PREFIX."topics t ON (p.topic_id=t.tid)
				 WHERE t.forum_id IN({$a['forum_string']})
				AND p.queued=0 AND p.post_date > {$a['last_visit']}";
 	}

	function generic_get_all_member( $a )
	{
		return "SELECT g.*, m.*, me.*
				FROM ".SQL_PREFIX."members m
				 LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
				 LEFT JOIN ".SQL_PREFIX."groups g ON (g.g_id=m.mgroup)
				WHERE m.id={$a['mid']}";
	}

	function moderate_get_topics( $a )
	{
		return "SELECT p.*,t.forum_id FROM ".SQL_PREFIX."posts p LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=p.topic_id) WHERE pid IN (".implode(",", $a['pids']).")";
	}

	function moderate_concat_title( $a )
	{
		return "UPDATE ".SQL_PREFIX."topics SET title=CONCAT('{$a['pre']}', title, '{$a['end']}') WHERE tid IN(".implode( ",", $a['tids'] ).")";
	}

	function mod_func_get_last_post( $a )
	{
		return "SELECT p.post_date, p.topic_id, p.author_id, p.author_name, p.pid, t.forum_id
				FROM ".SQL_PREFIX."posts p
				 LEFT JOIN ".SQL_PREFIX."topics t ON (p.topic_id=t.tid)
				WHERE topic_id={$a['tid']} and queued=0
				ORDER BY pid DESC LIMIT 0,1";
	}

	function mod_func_get_attach_count( $a )
	{
		return "SELECT COUNT(*) as count FROM ".SQL_PREFIX."attachments a
			     LEFT JOIN ".SQL_PREFIX."posts p on (a.attach_pid=p.pid)
			    WHERE p.topic_id={$a['tid']}";

	}

	function mod_func_get_topic_tracker( $a )
	{
		return "SELECT tr.*, m.id, m.mgroup, m.org_perm_id, t.tid, t.forum_id, g.g_id, g.g_perm_id
				 FROM ".SQL_PREFIX."tracker tr
				 LEFT JOIN ".SQL_PREFIX."topics t ON (tr.topic_id=t.tid)
				 LEFT JOIN ".SQL_PREFIX."members m on (m.id=tr.member_id)
				 LEFT JOIN ".SQL_PREFIX."groups g on (g.g_id=m.mgroup)
				WHERE tr.topic_id".$a['tid'];
	}

	function register_get_dead_validating( $a )
	{
		return "SELECT v.vid, v.member_id, m.posts
				   FROM ".SQL_PREFIX."validating v
				 LEFT JOIN ".SQL_PREFIX."members m ON (v.member_id=m.id)
				WHERE v.new_reg=1
				AND v.coppa_user <> 1
				AND v.entry_date < {$a['less_than']}
				AND lost_pass <> 1";
	}

	function search_get_all_user_count( $a )
	{
		return "SELECT count(*) as count
				FROM ".SQL_PREFIX."posts p
				 LEFT JOIN ".SQL_PREFIX."topics t ON (p.topic_id=t.tid)
				WHERE p.queued=0 AND t.forum_id IN({$a['forums']}) AND p.author_id={$a['mid']}";
	}

	function search_get_all_user_query( $a )
	{
		return "SELECT p.*, t.*, t.posts as topic_posts, t.title as topic_title, m.*, me.*
				FROM ".SQL_PREFIX."posts p
				 LEFT JOIN ".SQL_PREFIX."topics t ON (p.topic_id=t.tid)
				 LEFT JOIN ".SQL_PREFIX."members m ON (m.id=p.author_id)
				 LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=p.author_id)
				WHERE p.queued=0 AND t.forum_id IN({$a['forums']}) AND p.author_id={$a['mid']}
				ORDER BY post_date DESC";
	}

	function search_get_last_ten( $a )
	{
		return "SELECT p.*, t.*, t.posts as topic_posts, t.title as topic_title, m.*, me.*
				FROM ".SQL_PREFIX."posts p
				 LEFT JOIN ".SQL_PREFIX."topics t ON (p.topic_id=t.tid)
				 LEFT JOIN ".SQL_PREFIX."members m ON (m.id=p.author_id)
				 LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=p.author_id)
				WHERE p.queued=0 AND t.forum_id IN({$a['forums']}) AND p.author_id={$a['mid']}
				ORDER BY post_date DESC
				LIMIT 0,10";
	}

	function search_main_in_titles( $a )
	{
		return "SELECT t.*, t.posts as topic_posts, t.title as topic_title, p.pid, p.author_id, p.author_name, p.post_date, p.post, m.*, me.*
				FROM ".SQL_PREFIX."topics t
				  LEFT JOIN ".SQL_PREFIX."posts p ON (t.tid=p.topic_id AND p.new_topic=1)
				  LEFT JOIN ".SQL_PREFIX."members m ON (m.id=p.author_id)
				  LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=p.author_id)
				WHERE t.tid IN({$a['topics']})
				ORDER BY p.post_date DESC
				LIMIT {$a['limit_a']},25";
	}

	function search_main_in_posts( $a )
	{
		return "SELECT t.*, t.posts as topic_posts, t.title as topic_title, p.pid, p.author_id, p.author_name, p.post_date, p.post, p.post_htmlstate, m.*, me.*
				FROM ".SQL_PREFIX."posts p
				  LEFT JOIN ".SQL_PREFIX."topics t ON (t.tid=p.topic_id)
				  LEFT JOIN ".SQL_PREFIX."members m ON (m.id=p.author_id)
				  LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=p.author_id)
				WHERE p.pid IN({$a['posts']})
				ORDER BY p.post_date DESC
				LIMIT {$a['limit_a']},25";
	}

	function poll_get_poll_with_topic( $a )
	{
		return "SELECT f.allow_pollbump, t.*, p.pid as poll_id,p.choices,p.starter_id,p.votes
				FROM ".SQL_PREFIX."polls p, ".SQL_PREFIX."topics t, ".SQL_PREFIX."forums f
				WHERE t.tid={$a['tid']} and p.tid=t.tid and t.forum_id=f.id";
	}

	function contact_member_report_get_mods( $a )
	{
		return "SELECT m.id, m.name, m.email, m.mgroup, moderator.member_id, moderator.group_id
				FROM ".SQL_PREFIX."moderators moderator, ".SQL_PREFIX."members m
				WHERE moderator.forum_id={$a['fid']}
				AND (moderator.member_id=m.id OR moderator.group_id=m.mgroup)";
	}

	function contact_member_report_get_cpaccess( $a )
	{
		return "SELECT m.id, m.name, m.email FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."groups g WHERE g.g_access_cp=1 AND m.mgroup=g.g_id";
	}

	function print_page_get_members( $a )
	{
		return "SELECT g.*, m.* FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."groups g WHERE m.id in ({$a['mem_ids']}) AND m.mgroup=g.g_id";
	}

	function stats_who_posted( $a )
	{
		return "SELECT COUNT(p.pid) as pcount, p.author_id, p.author_name FROM ".SQL_PREFIX."posts p
 				WHERE p.topic_id={$a['tid']} AND queued=0 GROUP BY p.author_name ORDER BY pcount DESC";
	}

	function warn_get_data( $a )
	{
		return "SELECT l.*,  p.id as punisher_id, p.name as punisher_name
				 FROM ".SQL_PREFIX."warn_logs l
				  LEFT JOIN ".SQL_PREFIX."members p ON ( p.id=l.wlog_addedby )
				WHERE l.wlog_mid={$a['mid']} ORDER BY l.wlog_date DESC LIMIT {$a['limit_a']}, {$a['limit_b']}";
	}

	function warn_get_forum( $a )
	{
		return "SELECT t.tid, t.title, f.id, f.name FROM ".SQL_PREFIX."topics t, ".SQL_PREFIX."forums f WHERE tid={$a['tid']} AND t.forum_id=f.id";
	}

	function portal_get_poll_join( $a )
	{
		return "SELECT t.tid, t.title, t.state, t.last_vote, p.*, v.member_id as member_voted
				FROM ".SQL_PREFIX."topics t, ".SQL_PREFIX."polls p
				LEFT JOIN ".SQL_PREFIX."voters v ON (v.member_id={$a['mid']} and v.tid=t.tid)
				WHERE t.tid={$a['tid']} AND p.tid=t.tid";
	}

	function portal_get_monster_bitch( $a )
	{
		return "SELECT t.*, p.*, me.avatar_location, m.view_avs, me.avatar_size, me.avatar_type,
				m.id as member_id, m.name as member_name, m.mgroup
				FROM ".SQL_PREFIX."topics t
				 LEFT JOIN ".SQL_PREFIX."members m ON (t.starter_id=m.id)
				 LEFT JOIN ".SQL_PREFIX."member_extra me on (m.id=me.id)
				 LEFT JOIN ".SQL_PREFIX."posts p ON (t.topic_firstpost=p.pid)
				WHERE t.forum_id IN (-1{$a['csite_article_forum']}) {$a['qe']}
				AND t.approved=1 AND (t.moved_to IS NULL or t.moved_to='')
				ORDER BY t.pinned DESC, t.start_date DESC
				LIMIT 0,{$a['limit']}";
	}

	function help_search( $a )
	{
		return "SELECT id, title, description
				 FROM ".SQL_PREFIX."faq
				WHERE LOWER(title) LIKE '%{$a['search_string']}%' or LOWER(text) LIKE '%{$a['search_string']}%'
				ORDER BY title";
	}

	#-- NEW FOR RC1 --#

	function login_getmember( $a )
	{
		return "select id, name, email, mgroup, member_login_key, ip_address, login_anonymous
				from ".SQL_PREFIX."members
				where LOWER(name)='{$a['username']}'";
	}

	function contact_member_report_get_supmod( $a )
	{
		return "SELECT m.id, m.name, m.email FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."groups g WHERE g.g_is_supmod=1 AND m.mgroup=g.g_id";
	}

	function post_get_topic_review( $a )
	{
		return "SELECT p.*, m.mgroup
				 FROM ".SQL_PREFIX."posts p
				 LEFT JOIN ".SQL_PREFIX."members m ON (m.id=p.author_id)
				WHERE topic_id={$a['tid']} and queued=0
				ORDER BY pid DESC
				LIMIT 0,10";
	}

	function post_forum_tracker_all( $a )
	{
		#post

		return "SELECT m.name, m.email, m.id, m.language, m.last_activity, m.org_perm_id, g.g_perm_id
				FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."groups g
				WHERE m.mgroup IN ({$a['groups']})
				AND m.mgroup=g.g_id
				AND m.id <> {$a['mid']}
				AND m.allow_admin_mails=1
				AND m.last_activity < {$a['last_post']}";
	}


} // end class


?>