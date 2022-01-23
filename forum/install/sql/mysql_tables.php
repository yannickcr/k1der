<?php

$TABLE[] = "CREATE TABLE ibf_admin_logs (
  id bigint(20) NOT NULL auto_increment,
  act varchar(255) default NULL,
  code varchar(255) default NULL,
  member_id int(10) default NULL,
  ctime int(10) default NULL,
  note text,
  ip_address varchar(255) default NULL,
  PRIMARY KEY  (id)
)";

$TABLE[] = "CREATE TABLE ibf_admin_sessions (
  session_id varchar(32) NOT NULL default '',
  session_ip_address varchar(32) NOT NULL default '',
  session_member_name varchar(250) NOT NULL default '',
  session_member_id mediumint(8) NOT NULL default '0',
  session_member_login_key varchar(32) NOT NULL default '',
  session_location varchar(64) NOT NULL default '',
  session_log_in_time int(10) NOT NULL default '0',
  session_running_time int(10) NOT NULL default '0',
  PRIMARY KEY  (session_id)
)";


$TABLE[] = "CREATE TABLE ibf_announcements (
  announce_id int(10) unsigned NOT NULL auto_increment,
  announce_title varchar(255) NOT NULL default '',
  announce_post text NOT NULL,
  announce_forum text NOT NULL,
  announce_member_id mediumint(8) unsigned NOT NULL default '0',
  announce_html_enabled tinyint(1) NOT NULL default '0',
  announce_views int(10) unsigned NOT NULL default '0',
  announce_start int(10) unsigned NOT NULL default '0',
  announce_end int(10) unsigned NOT NULL default '0',
  announce_active tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (announce_id)
)";


$TABLE[] = "CREATE TABLE ibf_attachments (
  attach_id int(10) NOT NULL auto_increment,
  attach_file varchar(250) NOT NULL default '',
  attach_location varchar(250) NOT NULL default '',
  attach_thumb_location varchar(250) NOT NULL default '',
  attach_hits int(10) NOT NULL default '0',
  attach_date int(10) NOT NULL default '0',
  attach_temp tinyint(1) NOT NULL default '0',
  attach_pid int(10) NOT NULL default '0',
  attach_post_key varchar(32) NOT NULL default '0',
  attach_msg int(10) NOT NULL default '0',
  attach_member_id mediumint(8) NOT NULL default '0',
  attach_approved int(10) NOT NULL default '1',
  attach_filesize int(10) NOT NULL default '0',
  attach_thumb_width smallint(5) NOT NULL default '0',
  attach_thumb_height smallint(5) NOT NULL default '0',
  attach_is_image tinyint(1) NOT NULL default '0',
  attach_ext varchar(10) NOT NULL default '',
  PRIMARY KEY  (attach_id),
  KEY attach_pid (attach_pid),
  KEY attach_msg (attach_msg),
  KEY attach_post_key (attach_post_key),
  KEY attach_mid_size (attach_member_id,attach_filesize)
)";

$TABLE[] = "CREATE TABLE ibf_attachments_type (
  atype_id int(10) NOT NULL auto_increment,
  atype_extension varchar(18) NOT NULL default '',
  atype_mimetype varchar(255) NOT NULL default '',
  atype_post tinyint(1) NOT NULL default '1',
  atype_photo tinyint(1) NOT NULL default '0',
  atype_img text NOT NULL,
  PRIMARY KEY  (atype_id)
)";


$TABLE[] = "CREATE TABLE ibf_badwords (
  wid int(3) NOT NULL auto_increment,
  type varchar(250) NOT NULL default '',
  swop varchar(250) default NULL,
  m_exact tinyint(1) default '0',
  PRIMARY KEY  (wid)
)";

$TABLE[] = "CREATE TABLE ibf_banfilters (
  ban_id int(10) NOT NULL auto_increment,
  ban_type varchar(10) NOT NULL default 'ip',
  ban_content text NOT NULL,
  ban_date int(10) NOT NULL default '0',
  PRIMARY KEY  (ban_id)
)";


$TABLE[] = "CREATE TABLE ibf_bulk_mail (
  mail_id int(10) NOT NULL auto_increment,
  mail_subject varchar(255) NOT NULL default '',
  mail_content mediumtext NOT NULL,
  mail_groups mediumtext NOT NULL,
  mail_honor tinyint(1) NOT NULL default '1',
  mail_opts mediumtext NOT NULL,
  mail_start int(10) NOT NULL default '0',
  mail_updated int(10) NOT NULL default '0',
  mail_sentto int(10) NOT NULL default '0',
  mail_active tinyint(1) NOT NULL default '0',
  mail_pergo smallint(5) NOT NULL default '0',
  PRIMARY KEY  (mail_id)
)";


$TABLE[] = "CREATE TABLE ibf_cache_store (
  cs_key varchar(255) NOT NULL default '',
  cs_value mediumtext NOT NULL,
  cs_extra varchar(255) NOT NULL default '',
  cs_array tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (cs_key)
)";

$TABLE[] = "CREATE TABLE ibf_calendar_events (
  eventid mediumint(8) NOT NULL auto_increment,
  userid mediumint(8) NOT NULL default '0',
  year int(4) NOT NULL default '2002',
  month int(2) NOT NULL default '1',
  mday int(2) NOT NULL default '1',
  title varchar(254) NOT NULL default 'no title',
  event_text text NOT NULL,
  read_perms varchar(254) NOT NULL default '*',
  unix_stamp int(10) NOT NULL default '0',
  priv_event tinyint(1) NOT NULL default '0',
  show_emoticons tinyint(1) NOT NULL default '1',
  rating smallint(2) NOT NULL default '1',
  event_ranged tinyint(1) NOT NULL default '0',
  event_repeat tinyint(1) NOT NULL default '0',
  repeat_unit char(2) NOT NULL default '',
  end_day int(2) default NULL,
  end_month int(2) default NULL,
  end_year int(4) default NULL,
  end_unix_stamp int(10) default NULL,
  event_bgcolor varchar(32) NOT NULL default '',
  event_color varchar(32) NOT NULL default '',
  PRIMARY KEY  (eventid),
  KEY unix_stamp (unix_stamp)
)";

$TABLE[] = "CREATE TABLE ibf_conf_settings (
  conf_id int(10) NOT NULL auto_increment,
  conf_title varchar(255) NOT NULL default '',
  conf_description text NOT NULL,
  conf_group varchar(255) NOT NULL default '',
  conf_type varchar(255) NOT NULL default '',
  conf_key varchar(255) NOT NULL default '',
  conf_value text NOT NULL,
  conf_default text NOT NULL,
  conf_extra text NOT NULL,
  conf_evalphp text NOT NULL,
  conf_protected tinyint(1) NOT NULL default '0',
  conf_position smallint(3) NOT NULL default '0',
  conf_start_group varchar(255) NOT NULL default '',
  conf_end_group tinyint(1) NOT NULL default '0',
  conf_help_key varchar(255) NOT NULL default '0',
  conf_add_cache tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (conf_id)
)";


$TABLE[] = "CREATE TABLE ibf_conf_settings_titles (
  conf_title_id smallint(3) NOT NULL auto_increment,
  conf_title_title varchar(255) NOT NULL default '',
  conf_title_desc text NOT NULL,
  conf_title_count smallint(3) NOT NULL default '0',
  conf_title_noshow tinyint(1) NOT NULL default '0',
  conf_title_keyword varchar(200) NOT NULL default '0',
  PRIMARY KEY  (conf_title_id)
)";


$TABLE[] = "CREATE TABLE ibf_contacts (
  id mediumint(8) NOT NULL auto_increment,
  contact_id mediumint(8) NOT NULL default '0',
  member_id mediumint(8) NOT NULL default '0',
  contact_name varchar(32) NOT NULL default '',
  allow_msg tinyint(1) default NULL,
  contact_desc varchar(50) default NULL,
  PRIMARY KEY  (id)
)";


$TABLE[] = "CREATE TABLE ibf_custom_bbcode (
  bbcode_id int(10) NOT NULL auto_increment,
  bbcode_title varchar(255) NOT NULL default '',
  bbcode_desc text NOT NULL,
  bbcode_tag varchar(255) NOT NULL default '',
  bbcode_replace text NOT NULL,
  bbcode_useoption tinyint(1) NOT NULL default '0',
  bbcode_example text NOT NULL,
  PRIMARY KEY  (bbcode_id)
)";


$TABLE[] = "CREATE TABLE ibf_email_logs (
  email_id int(10) NOT NULL auto_increment,
  email_subject varchar(255) NOT NULL default '',
  email_content text NOT NULL,
  email_date int(10) NOT NULL default '0',
  from_member_id mediumint(8) NOT NULL default '0',
  from_email_address varchar(250) NOT NULL default '',
  from_ip_address varchar(16) NOT NULL default '127.0.0.1',
  to_member_id mediumint(8) NOT NULL default '0',
  to_email_address varchar(250) NOT NULL default '',
  topic_id int(10) NOT NULL default '0',
  PRIMARY KEY  (email_id),
  KEY from_member_id (from_member_id),
  KEY email_date (email_date)
)";

$TABLE[] = "CREATE TABLE ibf_emoticons (
  id smallint(3) NOT NULL auto_increment,
  typed varchar(32) NOT NULL default '',
  image varchar(128) NOT NULL default '',
  clickable smallint(2) NOT NULL default '1',
  emo_set varchar(64) NOT NULL default 'default',
  PRIMARY KEY  (id)
)";


$TABLE[] = "CREATE TABLE ibf_faq (
  id mediumint(8) NOT NULL auto_increment,
  title varchar(128) NOT NULL default '',
  text text,
  description text NOT NULL,
  PRIMARY KEY  (id)
)";


$TABLE[] = "CREATE TABLE ibf_forum_perms (
  perm_id int(10) NOT NULL auto_increment,
  perm_name varchar(250) NOT NULL default '',
  PRIMARY KEY  (perm_id)
)";

$TABLE[] = "CREATE TABLE ibf_forum_tracker (
  frid mediumint(8) NOT NULL auto_increment,
  member_id varchar(32) NOT NULL default '',
  forum_id smallint(5) NOT NULL default '0',
  start_date int(10) default NULL,
  last_sent int(10) NOT NULL default '0',
  forum_track_type varchar(100) NOT NULL default 'delayed',
  PRIMARY KEY  (frid)
)";

$TABLE[] = "CREATE TABLE ibf_forums (
  id smallint(5) NOT NULL default '0',
  topics mediumint(6) default NULL,
  posts mediumint(6) default NULL,
  last_post int(10) default NULL,
  last_poster_id mediumint(8) NOT NULL default '0',
  last_poster_name varchar(32) default NULL,
  name varchar(128) NOT NULL default '',
  description text,
  position smallint(5) default NULL,
  use_ibc tinyint(1) default NULL,
  use_html tinyint(1) default NULL,
  status tinyint(1) default '1',
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
  quick_reply tinyint(1) default '0',
  redirect_url varchar(250) default '',
  redirect_on tinyint(1) NOT NULL default '0',
  redirect_hits int(10) NOT NULL default '0',
  redirect_loc varchar(250) default '',
  rules_title varchar(255) NOT NULL default '',
  rules_text text NOT NULL,
  topic_mm_id varchar(250) NOT NULL default '',
  notify_modq_emails text,
  sub_can_post tinyint(1) default '1',
  permission_custom_error text NOT NULL,
  permission_array mediumtext NOT NULL,
  permission_showtopic tinyint(1) NOT NULL default '0',
  queued_topics mediumint(6) NOT NULL default '0',
  queued_posts mediumint(6) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY id (id),
  KEY position (position,parent_id)
)";

$TABLE[] = "CREATE TABLE ibf_groups (
  g_id int(3) unsigned NOT NULL auto_increment,
  g_view_board tinyint(1) default NULL,
  g_mem_info tinyint(1) default NULL,
  g_other_topics tinyint(1) default NULL,
  g_use_search tinyint(1) default NULL,
  g_email_friend tinyint(1) default NULL,
  g_invite_friend tinyint(1) default NULL,
  g_edit_profile tinyint(1) default NULL,
  g_post_new_topics tinyint(1) default NULL,
  g_reply_own_topics tinyint(1) default NULL,
  g_reply_other_topics tinyint(1) default NULL,
  g_edit_posts tinyint(1) default NULL,
  g_delete_own_posts tinyint(1) default NULL,
  g_open_close_posts tinyint(1) default NULL,
  g_delete_own_topics tinyint(1) default NULL,
  g_post_polls tinyint(1) default NULL,
  g_vote_polls tinyint(1) default NULL,
  g_use_pm tinyint(1) default NULL,
  g_is_supmod tinyint(1) default NULL,
  g_access_cp tinyint(1) default NULL,
  g_title varchar(32) NOT NULL default '',
  g_can_remove tinyint(1) default NULL,
  g_append_edit tinyint(1) default NULL,
  g_access_offline tinyint(1) default NULL,
  g_avoid_q tinyint(1) default NULL,
  g_avoid_flood tinyint(1) default NULL,
  g_icon text NOT NULL,
  g_attach_max bigint(20) default NULL,
  g_avatar_upload tinyint(1) default '0',
  g_calendar_post tinyint(1) default '0',
  prefix varchar(250) default NULL,
  suffix varchar(250) default NULL,
  g_max_messages int(5) default '50',
  g_max_mass_pm int(5) default '0',
  g_search_flood mediumint(6) default '20',
  g_edit_cutoff int(10) default '0',
  g_promotion varchar(10) default '-1&-1',
  g_hide_from_list tinyint(1) default '0',
  g_post_closed tinyint(1) default '0',
  g_perm_id varchar(255) NOT NULL default '',
  g_photo_max_vars varchar(200) default '',
  g_dohtml tinyint(1) NOT NULL default '0',
  g_edit_topic tinyint(1) NOT NULL default '0',
  g_email_limit varchar(15) NOT NULL default '10:15',
  g_bypass_badwords tinyint(1) NOT NULL default '0',
  g_can_msg_attach tinyint(1) NOT NULL default '0',
  g_attach_per_post int(10) NOT NULL default '0',
  PRIMARY KEY  (g_id)
)";


$TABLE[] = "CREATE TABLE ibf_languages (
  lid mediumint(8) NOT NULL auto_increment,
  ldir varchar(64) NOT NULL default '',
  lname varchar(250) NOT NULL default '',
  lauthor varchar(250) default NULL,
  lemail varchar(250) default NULL,
  PRIMARY KEY  (lid)
)";


$TABLE[] = "CREATE TABLE ibf_mail_error_logs (
  mlog_id int(10) NOT NULL auto_increment,
  mlog_date int(10) NOT NULL default '0',
  mlog_to varchar(250) NOT NULL default '',
  mlog_from varchar(250) NOT NULL default '',
  mlog_subject varchar(250) NOT NULL default '',
  mlog_content varchar(250) NOT NULL default '',
  mlog_msg text NOT NULL,
  mlog_code varchar(200) NOT NULL default '',
  mlog_smtp_msg text NOT NULL,
  PRIMARY KEY  (mlog_id)
)";

$TABLE[] = "CREATE TABLE ibf_mail_queue (
  mail_id int(10) NOT NULL auto_increment,
  mail_date int(10) NOT NULL default '0',
  mail_to varchar(255) NOT NULL default '',
  mail_from varchar(255) NOT NULL default '',
  mail_subject text NOT NULL,
  mail_content text NOT NULL,
  mail_type varchar(200) NOT NULL default '',
  PRIMARY KEY  (mail_id)
)";


$TABLE[] = "CREATE TABLE ibf_member_extra (
  id mediumint(8) NOT NULL default '0',
  notes text,
  links text,
  bio text,
  ta_size char(3) default NULL,
  photo_type varchar(10) default '',
  photo_location varchar(255) default '',
  photo_dimensions varchar(200) default '',
  aim_name varchar(40) NOT NULL default '',
  icq_number int(15) NOT NULL default '0',
  website varchar(250) NOT NULL default '',
  yahoo varchar(40) NOT NULL default '',
  interests text NOT NULL,
  msnname varchar(200) NOT NULL default '',
  vdirs text NOT NULL,
  location varchar(250) NOT NULL default '',
  signature text NOT NULL default '',
  avatar_location varchar(128) NOT NULL default '',
  avatar_size varchar(9) NOT NULL default '',
  avatar_type varchar(15) NOT NULL default 'local',
  PRIMARY KEY  (id)
)";

$TABLE[] = "CREATE TABLE ibf_members (
  id mediumint(8) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  mgroup smallint(3) NOT NULL default '0',
  legacy_password varchar(32) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  joined int(10) NOT NULL default '0',
  ip_address varchar(16) NOT NULL default '',
  posts mediumint(7) default '0',
  title varchar(64) default NULL,
  allow_admin_mails tinyint(1) default NULL,
  time_offset varchar(10) default NULL,
  hide_email varchar(8) default NULL,
  email_pm tinyint(1) default NULL,
  email_full tinyint(1) default NULL,
  skin smallint(5) default NULL,
  warn_level int(10) default NULL,
  warn_lastwarn int(10) NOT NULL default '0',
  language varchar(32) default NULL,
  last_post int(10) default NULL,
  restrict_post varchar(100) NOT NULL default '0',
  view_sigs tinyint(1) default '1',
  view_img tinyint(1) default '1',
  view_avs tinyint(1) default '1',
  view_pop tinyint(1) default '1',
  bday_day int(2) default NULL,
  bday_month int(2) default NULL,
  bday_year int(4) default NULL,
  new_msg tinyint(2) default '0',
  msg_total smallint(5) default '0',
  show_popup tinyint(1) default NULL,
  misc varchar(128) default NULL,
  last_visit int(10) default '0',
  last_activity int(10) default '0',
  dst_in_use tinyint(1) default '0',
  view_prefs varchar(64) default '-1&-1',
  coppa_user tinyint(1) default '0',
  mod_posts varchar(100) NOT NULL default '0',
  auto_track varchar(50) default '0',
  temp_ban varchar(100) default '0',
  sub_end int(10) NOT NULL default '0',
  login_anonymous char(3) NOT NULL default '0&0',
  ignored_users text NOT NULL,
  mgroup_others varchar(255) NOT NULL default '',
  org_perm_id varchar(255) NOT NULL default '',
  member_login_key varchar(32) NOT NULL default '',
  subs_pkg_chosen smallint(3) NOT NULL default '0',
  has_blog TINYINT(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY name (name),
  KEY mgroup (mgroup),
  KEY bday_day (bday_day),
  KEY bday_month (bday_month)
)";


$TABLE[] = "CREATE TABLE ibf_members_converge (
  converge_id int(10) NOT NULL auto_increment,
  converge_email varchar(250) NOT NULL default '',
  converge_joined int(10) NOT NULL default '0',
  converge_pass_hash varchar(32) NOT NULL default '',
  converge_pass_salt varchar(5) NOT NULL default '',
  PRIMARY KEY  (converge_id),
  KEY converge_email (converge_email)
)";

$TABLE[] = "CREATE TABLE ibf_message_text (
  msg_id int(10) NOT NULL auto_increment,
  msg_date int(10) default NULL,
  msg_post text,
  msg_cc_users text,
  msg_sent_to_count smallint(5) NOT NULL default '0',
  msg_deleted_count smallint(5) NOT NULL default '0',
  msg_post_key varchar(32) NOT NULL default '0',
  msg_author_id mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (msg_id),
  KEY msg_date (msg_date),
  KEY msg_sent_to_count (msg_sent_to_count),
  KEY msg_deleted_count (msg_deleted_count)
)";


$TABLE[] = "CREATE TABLE ibf_message_topics (
  mt_id int(10) NOT NULL auto_increment,
  mt_msg_id int(10) NOT NULL default '0',
  mt_date int(10) NOT NULL default '0',
  mt_title varchar(255) NOT NULL default '',
  mt_from_id mediumint(8) NOT NULL default '0',
  mt_to_id mediumint(8) NOT NULL default '0',
  mt_vid_folder varchar(32) NOT NULL default '',
  mt_read tinyint(1) NOT NULL default '0',
  mt_hasattach smallint(5) NOT NULL default '0',
  mt_hide_cc tinyint(1) default '0',
  mt_tracking tinyint(1) default '0',
  mt_owner_id mediumint(8) NOT NULL default '0',
  mt_user_read int(10) default '0',
  PRIMARY KEY  (mt_id),
  KEY mt_from_id (mt_from_id),
  KEY mt_owner_id (mt_owner_id,mt_to_id,mt_vid_folder,mt_date)
)";


$TABLE[] = "CREATE TABLE ibf_moderator_logs (
  id int(10) NOT NULL auto_increment,
  forum_id int(5) default '0',
  topic_id int(10) NOT NULL default '0',
  post_id int(10) default NULL,
  member_id mediumint(8) NOT NULL default '0',
  member_name varchar(32) NOT NULL default '',
  ip_address varchar(16) NOT NULL default '0',
  http_referer varchar(255) default NULL,
  ctime int(10) default NULL,
  topic_title varchar(128) default NULL,
  action varchar(128) default NULL,
  query_string varchar(128) default NULL,
  PRIMARY KEY  (id)
)";


$TABLE[] = "CREATE TABLE ibf_moderators (
  mid mediumint(8) NOT NULL auto_increment,
  forum_id int(5) NOT NULL default '0',
  member_name varchar(32) NOT NULL default '',
  member_id mediumint(8) NOT NULL default '0',
  edit_post tinyint(1) default NULL,
  edit_topic tinyint(1) default NULL,
  delete_post tinyint(1) default NULL,
  delete_topic tinyint(1) default NULL,
  view_ip tinyint(1) default NULL,
  open_topic tinyint(1) default NULL,
  close_topic tinyint(1) default NULL,
  mass_move tinyint(1) default NULL,
  mass_prune tinyint(1) default NULL,
  move_topic tinyint(1) default NULL,
  pin_topic tinyint(1) default NULL,
  unpin_topic tinyint(1) default NULL,
  post_q tinyint(1) default NULL,
  topic_q tinyint(1) default NULL,
  allow_warn tinyint(1) default NULL,
  edit_user tinyint(1) NOT NULL default '0',
  is_group tinyint(1) default '0',
  group_id smallint(3) default NULL,
  group_name varchar(200) default NULL,
  split_merge tinyint(1) default '0',
  can_mm tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (mid),
  KEY forum_id (forum_id),
  KEY group_id (group_id),
  KEY member_id (member_id)
)";

$TABLE[] = "CREATE TABLE ibf_pfields_content (
  member_id mediumint(8) NOT NULL default '0',
  updated int(10) default '0',
  PRIMARY KEY  (member_id)
)";

$TABLE[] = "CREATE TABLE ibf_pfields_data (
  pf_id smallint(5) NOT NULL auto_increment,
  pf_title varchar(250) NOT NULL default '',
  pf_desc varchar(250) NOT NULL default '',
  pf_content text NOT NULL,
  pf_type varchar(250) NOT NULL default '',
  pf_not_null tinyint(1) NOT NULL default '0',
  pf_member_hide tinyint(1) NOT NULL default '0',
  pf_max_input smallint(6) NOT NULL default '0',
  pf_member_edit tinyint(1) NOT NULL default '0',
  pf_position smallint(6) NOT NULL default '0',
  pf_show_on_reg tinyint(1) NOT NULL default '0',
  pf_input_format text NOT NULL,
  pf_admin_only tinyint(1) NOT NULL default '0',
  pf_topic_format text NOT NULL,
  PRIMARY KEY  (pf_id)
)";

$TABLE[] = "CREATE TABLE ibf_polls (
  pid mediumint(8) NOT NULL auto_increment,
  tid int(10) NOT NULL default '0',
  start_date int(10) default NULL,
  choices text,
  starter_id mediumint(8) NOT NULL default '0',
  votes smallint(5) NOT NULL default '0',
  forum_id smallint(5) NOT NULL default '0',
  poll_question varchar(255) default NULL,
  PRIMARY KEY  (pid),
  KEY tid (tid)
)";

$TABLE[] = "CREATE TABLE ibf_posts (
  pid int(10) NOT NULL auto_increment,
  append_edit tinyint(1) default '0',
  edit_time int(10) default NULL,
  author_id mediumint(8) NOT NULL default '0',
  author_name varchar(32) default NULL,
  use_sig tinyint(1) NOT NULL default '0',
  use_emo tinyint(1) NOT NULL default '0',
  ip_address varchar(16) NOT NULL default '',
  post_date int(10) default NULL,
  icon_id smallint(5) default NULL,
  post text,
  queued tinyint(1) NOT NULL default '0',
  topic_id int(10) NOT NULL default '0',
  post_title varchar(255) default NULL,
  new_topic tinyint(1) default '0',
  edit_name varchar(255) default NULL,
  post_key varchar(32) NOT NULL default '0',
  post_parent int(10) NOT NULL default '0',
  post_htmlstate smallint(1) NOT NULL default '0',
  PRIMARY KEY  (pid),
  KEY topic_id (topic_id,queued,pid),
  KEY author_id (author_id,topic_id),
  KEY post_date (post_date)
) TYPE=MyISAM";

$TABLE[] = "CREATE TABLE ibf_reg_antispam (
  regid varchar(32) NOT NULL default '',
  regcode varchar(8) NOT NULL default '',
  ip_address varchar(32) default NULL,
  ctime int(10) default NULL,
  PRIMARY KEY  (regid)
)";


$TABLE[] = "CREATE TABLE ibf_search_results (
  id varchar(32) NOT NULL default '',
  topic_id text NOT NULL,
  search_date int(12) NOT NULL default '0',
  topic_max int(3) NOT NULL default '0',
  sort_key varchar(32) NOT NULL default 'last_post',
  sort_order varchar(4) NOT NULL default 'desc',
  member_id mediumint(10) default '0',
  ip_address varchar(64) default NULL,
  post_id text,
  post_max int(10) NOT NULL default '0',
  query_cache text
)";

$TABLE[] = "CREATE TABLE ibf_sessions (
  id varchar(32) NOT NULL default '0',
  member_name varchar(64) default NULL,
  member_id mediumint(8) NOT NULL default '0',
  ip_address varchar(16) default NULL,
  browser varchar(64) default NULL,
  running_time int(10) default NULL,
  login_type char(3) default '',
  location varchar(40) default NULL,
  member_group smallint(3) default NULL,
  in_forum smallint(5) NOT NULL default '0',
  in_topic int(10) default NULL,
  in_error tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY in_topic (in_topic),
  KEY in_forum (in_forum)
)";

$TABLE[] = "CREATE TABLE ibf_skin_macro (
  macro_id smallint(3) NOT NULL auto_increment,
  macro_value varchar(200) default NULL,
  macro_replace text,
  macro_can_remove tinyint(1) default '0',
  macro_set smallint(3) NOT NULL default '0',
  PRIMARY KEY  (macro_id),
  KEY macro_set (macro_set)
)";


$TABLE[] = "CREATE TABLE ibf_skin_sets (
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
  set_css mediumtext NOT NULL,
  set_cache_macro mediumtext NOT NULL,
  set_wrapper mediumtext NOT NULL,
  set_css_updated int(10) NOT NULL default '0',
  set_cache_css mediumtext NOT NULL,
  set_cache_wrapper mediumtext NOT NULL,
  set_emoticon_folder varchar(60) NOT NULL default 'default',
  PRIMARY KEY  (set_skin_set_id)
)";

$TABLE[] = "CREATE TABLE ibf_skin_templates (
  suid int(10) NOT NULL auto_increment,
  set_id int(10) NOT NULL default '0',
  group_name varchar(255) NOT NULL default '',
  section_content mediumtext,
  func_name varchar(255) default NULL,
  func_data text,
  updated int(10) default NULL,
  can_remove tinyint(4) default '0',
  PRIMARY KEY  (suid)
)";


$TABLE[] = "CREATE TABLE ibf_skin_templates_cache (
  template_id varchar(32) NOT NULL default '',
  template_group_name varchar(255) NOT NULL default '',
  template_group_content mediumtext NOT NULL,
  template_set_id int(10) NOT NULL default '0',
  PRIMARY KEY  (template_id),
  KEY template_set_id (template_set_id),
  KEY template_group_name (template_group_name)
)";


$TABLE[] = "CREATE TABLE ibf_spider_logs (
  sid int(10) NOT NULL auto_increment,
  bot varchar(255) NOT NULL default '',
  query_string text NOT NULL,
  entry_date int(10) NOT NULL default '0',
  ip_address varchar(16) NOT NULL default '',
  PRIMARY KEY  (sid)
)";


$TABLE[] = "CREATE TABLE ibf_subscription_currency (
  subcurrency_code varchar(10) NOT NULL default '',
  subcurrency_desc varchar(250) NOT NULL default '',
  subcurrency_exchange decimal(10,8) NOT NULL default '0.00000000',
  subcurrency_default tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (subcurrency_code)
)";

$TABLE[] = "CREATE TABLE ibf_subscription_extra (
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
  PRIMARY KEY  (subextra_id)
)";


$TABLE[] = "CREATE TABLE ibf_subscription_logs (
  sublog_id int(10) NOT NULL auto_increment,
  sublog_date int(10) NOT NULL default '0',
  sublog_member_id mediumint(8) NOT NULL default '0',
  sublog_transid int(10) NOT NULL default '0',
  sublog_ipaddress varchar(16) NOT NULL default '',
  sublog_data text,
  sublog_postdata text,
  PRIMARY KEY  (sublog_id)
)";


$TABLE[] = "CREATE TABLE ibf_subscription_methods (
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
  submethod_active tinyint(1) NOT NULL default '0',
  submethod_use_currency varchar(10) NOT NULL default 'USD',
  PRIMARY KEY  (submethod_id)
)";


$TABLE[] = "CREATE TABLE ibf_subscription_trans (
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
  PRIMARY KEY  (subtrans_id)
)";

$TABLE[] = "CREATE TABLE ibf_subscriptions (
  sub_id smallint(5) NOT NULL auto_increment,
  sub_title varchar(250) NOT NULL default '',
  sub_desc text,
  sub_new_group mediumint(8) NOT NULL default '0',
  sub_length smallint(5) NOT NULL default '1',
  sub_unit char(2) NOT NULL default 'm',
  sub_cost decimal(10,2) NOT NULL default '0.00',
  sub_run_module varchar(250) NOT NULL default '',
  PRIMARY KEY  (sub_id)
)";


$TABLE[] = "CREATE TABLE ibf_task_logs (
  log_id int(10) NOT NULL auto_increment,
  log_title varchar(255) NOT NULL default '',
  log_date int(10) NOT NULL default '0',
  log_ip varchar(16) NOT NULL default '0',
  log_desc text NOT NULL,
  PRIMARY KEY  (log_id)
)";

$TABLE[] = "CREATE TABLE ibf_task_manager (
  task_id int(10) NOT NULL auto_increment,
  task_title varchar(255) NOT NULL default '',
  task_file varchar(255) NOT NULL default '',
  task_next_run int(10) NOT NULL default '0',
  task_week_day tinyint(1) NOT NULL default '-1',
  task_month_day smallint(2) NOT NULL default '-1',
  task_hour smallint(2) NOT NULL default '-1',
  task_minute smallint(2) NOT NULL default '-1',
  task_cronkey varchar(32) NOT NULL default '',
  task_log tinyint(1) NOT NULL default '0',
  task_description text NOT NULL,
  task_enabled tinyint(1) NOT NULL default '1',
  task_key varchar(30) NOT NULL default '',
  task_safemode tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (task_id),
  KEY task_next_run (task_next_run)
)";

$TABLE[] = "CREATE TABLE ibf_titles (
  id smallint(5) NOT NULL auto_increment,
  posts int(10) default NULL,
  title varchar(128) default NULL,
  pips varchar(128) default NULL,
  PRIMARY KEY  (id),
  KEY posts (posts)
)";

$TABLE[] = "CREATE TABLE ibf_topic_mmod (
  mm_id smallint(5) NOT NULL auto_increment,
  mm_title varchar(250) NOT NULL default '',
  mm_enabled tinyint(1) NOT NULL default '0',
  topic_state varchar(10) NOT NULL default 'leave',
  topic_pin varchar(10) NOT NULL default 'leave',
  topic_move smallint(5) NOT NULL default '0',
  topic_move_link tinyint(1) NOT NULL default '0',
  topic_title_st varchar(250) NOT NULL default '',
  topic_title_end varchar(250) NOT NULL default '',
  topic_reply tinyint(1) NOT NULL default '0',
  topic_reply_content text NOT NULL,
  topic_reply_postcount tinyint(1) NOT NULL default '0',
  mm_forums text NOT NULL,
  topic_approve tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (mm_id)
)";


$TABLE[] = "CREATE TABLE ibf_topics (
  tid int(10) NOT NULL auto_increment,
  title varchar(250) NOT NULL default '',
  description varchar(70) default NULL,
  state varchar(8) default NULL,
  posts int(10) default NULL,
  starter_id mediumint(8) NOT NULL default '0',
  start_date int(10) default NULL,
  last_poster_id mediumint(8) NOT NULL default '0',
  last_post int(10) default NULL,
  icon_id tinyint(2) default NULL,
  starter_name varchar(32) default NULL,
  last_poster_name varchar(32) default NULL,
  poll_state varchar(8) default NULL,
  last_vote int(10) default NULL,
  views int(10) default NULL,
  forum_id smallint(5) NOT NULL default '0',
  approved tinyint(1) default NULL,
  author_mode tinyint(1) default NULL,
  pinned tinyint(1) default NULL,
  moved_to varchar(64) default NULL,
  rating text,
  total_votes int(5) NOT NULL default '0',
  topic_hasattach smallint(5) NOT NULL default '0',
  topic_firstpost int(10) NOT NULL default '0',
  topic_queuedposts int(10) NOT NULL default '0',
  PRIMARY KEY  (tid),
  KEY last_post (last_post),
  KEY forum_id (forum_id,approved,pinned),
  KEY topic_firstpost (topic_firstpost)
) TYPE=MyISAM";


$TABLE[] = "CREATE TABLE ibf_topics_read (
  read_tid int(10) NOT NULL default '0',
  read_mid mediumint(8) NOT NULL default '0',
  read_date int(10) NOT NULL default '0',
  UNIQUE KEY read_tid_mid (read_tid,read_mid)
)";


$TABLE[] = "CREATE TABLE ibf_tracker (
  trid mediumint(8) NOT NULL auto_increment,
  member_id mediumint(8) NOT NULL default '0',
  topic_id int(10) NOT NULL default '0',
  start_date int(10) default NULL,
  last_sent int(10) NOT NULL default '0',
  topic_track_type varchar(100) NOT NULL default 'delayed',
  PRIMARY KEY  (trid),
  KEY topic_id (topic_id)
)";


$TABLE[] = "CREATE TABLE ibf_upgrade_history (
  upgrade_id int(10) NOT NULL auto_increment,
  upgrade_version_id int(10) NOT NULL default '0',
  upgrade_version_human varchar(200) NOT NULL default '',
  upgrade_date int(10) NOT NULL default '0',
  upgrade_mid int(10) NOT NULL default '0',
  upgrade_notes text NOT NULL,
  PRIMARY KEY  (upgrade_id)
)";

$TABLE[] = "CREATE TABLE ibf_validating (
  vid varchar(32) NOT NULL default '',
  member_id mediumint(8) NOT NULL default '0',
  real_group smallint(3) NOT NULL default '0',
  temp_group smallint(3) NOT NULL default '0',
  entry_date int(10) NOT NULL default '0',
  coppa_user tinyint(1) NOT NULL default '0',
  lost_pass tinyint(1) NOT NULL default '0',
  new_reg tinyint(1) NOT NULL default '0',
  email_chg tinyint(1) NOT NULL default '0',
  ip_address varchar(16) NOT NULL default '0',
  PRIMARY KEY  (vid),
  KEY new_reg (new_reg)
)";


$TABLE[] = "CREATE TABLE ibf_voters (
  vid int(10) NOT NULL auto_increment,
  ip_address varchar(16) NOT NULL default '',
  vote_date int(10) NOT NULL default '0',
  tid int(10) NOT NULL default '0',
  member_id varchar(32) default NULL,
  forum_id smallint(5) NOT NULL default '0',
  PRIMARY KEY  (vid)
)";


$TABLE[] = "CREATE TABLE ibf_warn_logs (
  wlog_id int(10) NOT NULL auto_increment,
  wlog_mid mediumint(8) NOT NULL default '0',
  wlog_notes text NOT NULL,
  wlog_contact varchar(250) NOT NULL default 'none',
  wlog_contact_content text NOT NULL,
  wlog_date int(10) NOT NULL default '0',
  wlog_type varchar(6) NOT NULL default 'pos',
  wlog_addedby mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (wlog_id)
)";

    

?>