ALTER TABLE ibf_forums RENAME ibf_forums_bak;
DROP TABLE if exists ibf_attachments;
DROP TABLE if exists ibf_announcements;
CREATE TABLE ibf_attachments (
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
);
CREATE TABLE ibf_message_text (
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
);
CREATE TABLE ibf_message_topics (
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
);
CREATE TABLE ibf_skin_sets (
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
);
CREATE TABLE ibf_skin_templates_cache (
template_id varchar(32) NOT NULL default '',
template_group_name varchar(255) NOT NULL default '',
template_group_content mediumtext NOT NULL default '',
template_set_id int(10) NOT NULL default '0',
primary key (template_id),
KEY template_set_id (template_set_id),
KEY template_group_name (template_group_name)
);
CREATE TABLE ibf_mail_queue(
mail_id int(10) auto_increment NOT NULL,
mail_date int(10) NOT NULL default '0',
mail_to varchar(255) NOT NULL default '',
mail_from varchar(255) NOT NULL default '',
mail_subject text NOT NULL default '',
mail_content text NOT NULL default '',
mail_type varchar(200) NOT NULL default '',
PRIMARY KEY (mail_id)
);
CREATE TABLE ibf_task_manager (
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
);
CREATE TABLE ibf_task_logs (
log_id int(10) auto_increment NOT NULL,
log_title varchar(255) NOT NULL default '',
log_date int(10) NOT NULL default '0',
log_ip varchar(16) NOT NULL default '0',
log_desc text NOT NULL default '',
PRIMARY KEY(log_id)
);
CREATE TABLE ibf_custom_bbcode (
bbcode_id int(10) NOT NULL auto_increment,
bbcode_title varchar(255) NOT NULL default '',
bbcode_desc text NOT NULL default '',
bbcode_tag varchar(255) NOT NULL default '',
bbcode_replace text NOT NULL default '',
bbcode_useoption tinyint(1) NOT NULL default '',
bbcode_example text NOT NULL default '',
PRIMARY KEY (bbcode_id)
);
CREATE TABLE ibf_conf_settings (
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
);
CREATE TABLE ibf_conf_settings_titles (
conf_title_id smallint(3) NOT NULL auto_increment,
conf_title_title varchar(255) NOT NULL default '',
conf_title_desc text NOT NULL default '',
conf_title_count smallint(3) NOT NULL default '0',
conf_title_noshow tinyint(1) NOT NULL default '0',
conf_title_keyword varchar(200) NOT NULL default '',
PRIMARY KEY(conf_title_id)
);
CREATE TABLE ibf_topics_read (
read_tid int(10) NOT NULL default '0',
read_mid mediumint(8) NOT NULL default '0',
read_date int(10) NOT NULL default '0',
UNIQUE KEY read_tid_mid( read_tid, read_mid )
);
CREATE TABLE ibf_banfilters (
ban_id int(10) NOT NULL auto_increment,
ban_type varchar(10) NOT NULL default 'ip',
ban_content text NOT NULL default '',
ban_date int(10) NOT NULL default '0',
PRIMARY KEY (ban_id)
);
CREATE TABLE ibf_attachments_type (
atype_id int(10) NOT NULL auto_increment,
atype_extension varchar(18) NOT NULL default '',
atype_mimetype varchar(255) NOT NULL default '',
atype_post tinyint(1) NOT NULL default '1',
atype_photo tinyint(1) NOT NULL default '0',
atype_img text NOT NULL default '',
PRIMARY KEY (atype_id)
);
CREATE TABLE ibf_members_converge (
converge_id int(10) auto_increment NOT NULL,
converge_email varchar(250) NOT NULL default '',
converge_joined int(10) NOT NULL default '',
converge_pass_hash varchar(32) NOT NULL default '',
converge_pass_salt varchar(5) NOT NULL default '',
PRIMARY KEY( converge_id )
);
CREATE TABLE ibf_announcements (
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
);
CREATE TABLE ibf_mail_error_logs (
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
);
CREATE TABLE ibf_bulk_mail (
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
);
CREATE TABLE ibf_upgrade_history (
upgrade_id int(10) NOT NULL auto_increment,
upgrade_version_id int(10) NOT NULL default '',
upgrade_version_human varchar(200) NOT NULL default '',
upgrade_date int(10) NOT NULL default '0',
upgrade_mid int(10) NOT NULL default '0',
upgrade_notes text NOT NULL default '',
PRIMARY KEY (upgrade_id)
);
DROP TABLE if exists ibf_forums;
CREATE TABLE ibf_forums (
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
);
alter table ibf_posts add post_parent int(10) NOT NULL default '0';
alter table ibf_posts ADD post_key varchar(32) NOT NULL default '0';
alter table ibf_posts add post_htmlstate smallint(1) NOT NULL default '0';
alter table ibf_topics ADD topic_hasattach smallint(5) NOT NULL default '0';
alter table ibf_members add login_anonymous varchar(3) NOT NULL default '0&0';
alter table ibf_members add ignored_users text NOT NULL default '';
alter table ibf_members add mgroup_others varchar(255) NOT NULL default '';
alter table ibf_member_extra
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
ADD avatar_type varchar(15) NOT NULL default 'local';
alter table ibf_members add member_login_key varchar(32) NOT NULL default '';
alter table ibf_members change password legacy_password varchar(32) NOT NULL default '';
alter table ibf_macro rename ibf_skin_macro;
alter table ibf_skin_macro change can_remove macro_can_remove tinyint(1) default '0';
alter table ibf_groups add g_bypass_badwords tinyint(1) NOT NULL default '0';
alter table ibf_cache_store change cs_value cs_value mediumtext NOT NULL default '';
alter table ibf_cache_store add cs_array tinyint(1) NOT NULL default '0';
alter table ibf_sessions add in_error tinyint(1) NOT NULL default '';
alter table ibf_topic_mmod add mm_forums text NOT NULL default '';
alter table ibf_groups change g_icon g_icon text NOT NULL default '';
alter table ibf_emoticons add emo_set varchar(64) NOT NULL default 'default';
alter table ibf_admin_sessions change ID session_id varchar(32) NOT NULL default '';
alter table ibf_admin_sessions change IP_ADDRESS session_ip_address varchar(32) NOT NULL default '';
alter table ibf_admin_sessions change MEMBER_NAME session_member_name varchar(250) NOT NULL default '';
alter table ibf_admin_sessions change MEMBER_ID session_member_id mediumint(8) NOT NULL default '';
alter table ibf_admin_sessions change SESSION_KEY session_member_login_key varchar(32) NOT NULL default '';
alter table ibf_admin_sessions change LOCATION session_location varchar(64) NOT NULL default '';
alter table ibf_admin_sessions change LOG_IN_TIME session_log_in_time int(10) NOT NULL default '';
alter table ibf_admin_sessions change RUNNING_TIME session_running_time int(10) NOT NULL default '';
alter table ibf_forum_tracker add forum_track_type varchar(100) NOT NULL default 'delayed';
alter table ibf_tracker add topic_track_type varchar(100) NOT NULL default 'delayed';
delete from ibf_members where id=0 LIMIT 1;
delete from ibf_member_extra where id=0 limit 1;
alter table ibf_groups add g_attach_per_post int(10) NOT NULL default '0';
		
	