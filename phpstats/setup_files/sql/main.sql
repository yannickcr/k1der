#       ////////////////////////////////////////////////
#       //   ___ _  _ ___     ___ _____ _ _____ ___   //
#       //  | _ \ || | _ \___/ __|_   _/_\_   _/ __|  //
#       //  |  _/ __ |  _/___\__ \ | |/ _ \| | \__ \  //
#       //  |_| |_||_|_|0.1.9|___/ |_/_/ \_\_| |___/  //
#       //                                            //
#  /////////////////////////////////////////////////////////
#  //       Author: Roberto Valsania (Webmaster76)        //
#  //   Staff: Matrix, Viewsource, PaoDJ, Fabry, theCAS   //
#  //          Homepage: www.php-stats.com,               //
#  //                    www.php-stats.it,                //
#  //                    www.php-stat.com                 //
#  /////////////////////////////////////////////////////////
#
#
# Struttura della tabella `php_stats_cache`
#

DROP TABLE IF EXISTS php_stats_cache;
CREATE TABLE php_stats_cache (
  user_id varchar(15) NOT NULL default '0',
  data int(11) NOT NULL default '0',
  lastpage varchar(255) NOT NULL default '0',
  visitor_id varchar(32) NOT NULL default '',
  hits tinyint(3) unsigned NOT NULL default '0',
  visits smallint(5) unsigned NOT NULL default '0',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  host varchar(50) NOT NULL default '',
  lang varchar(8) NOT NULL default '',
  giorno varchar(10) NOT NULL default '',
  level tinyint(3) unsigned NOT NULL default '0',
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_cache`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_clicks`
#

DROP TABLE IF EXISTS php_stats_clicks;
CREATE TABLE php_stats_clicks (
  id int(11) NOT NULL auto_increment,
  nome varchar(20) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  creazione int(11) NOT NULL default '0',
  clicks int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dump dei dati per la tabella `php_stats_clicks`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_config`
#

DROP TABLE IF EXISTS php_stats_config;
CREATE TABLE php_stats_config (
  name varchar(20) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (name)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_config`
#

INSERT INTO php_stats_config VALUES ('stats_disabled', '0');
INSERT INTO php_stats_config VALUES ('language', 'it');
INSERT INTO php_stats_config VALUES ('server_url', 'http://www.tuosito.it');
INSERT INTO php_stats_config VALUES ('admin_pass', '123456');
INSERT INTO php_stats_config VALUES ('use_pass', '0');
INSERT INTO php_stats_config VALUES ('unlock_pages','0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|');
INSERT INTO php_stats_config VALUES ('cifre', '8');
INSERT INTO php_stats_config VALUES ('stile', '1');
INSERT INTO php_stats_config VALUES ('timezone', '0');
INSERT INTO php_stats_config VALUES ('template', 'default');
INSERT INTO php_stats_config VALUES ('startvisits', '0');
INSERT INTO php_stats_config VALUES ('starthits', '0');
INSERT INTO php_stats_config VALUES ('nomesito', 'Tuo Sito o link');
INSERT INTO php_stats_config VALUES ('moduli', '1|2|1|1|2|0|1|0|0|0|0|1|');
INSERT INTO php_stats_config VALUES ('user_mail', 'tuonome@tuoserver.it');
INSERT INTO php_stats_config VALUES ('user_pass_new', '');
INSERT INTO php_stats_config VALUES ('user_pass_key', '');
INSERT INTO php_stats_config VALUES ('prune_0_on', '1');
INSERT INTO php_stats_config VALUES ('prune_0_value', '24');
INSERT INTO php_stats_config VALUES ('prune_1_on', '0');
INSERT INTO php_stats_config VALUES ('prune_1_value', '100');
INSERT INTO php_stats_config VALUES ('prune_2_on', '0');
INSERT INTO php_stats_config VALUES ('prune_2_value', '1000');
INSERT INTO php_stats_config VALUES ('prune_3_on', '0');
INSERT INTO php_stats_config VALUES ('prune_3_value', '1000');
INSERT INTO php_stats_config VALUES ('prune_4_on', '0');
INSERT INTO php_stats_config VALUES ('prune_4_value', '1000');
INSERT INTO php_stats_config VALUES ('prune_5_on', '0');
INSERT INTO php_stats_config VALUES ('prune_5_value', '1000');
INSERT INTO php_stats_config VALUES ('phpstats_ver', '0.1.9');
INSERT INTO php_stats_config VALUES ('inadm_last_update', '1099607534');
INSERT INTO php_stats_config VALUES ('inadm_lastcache_time', '0');
INSERT INTO php_stats_config VALUES ('inadm_upd_available', '0');
INSERT INTO php_stats_config VALUES ('ip_timeout', '1');
INSERT INTO php_stats_config VALUES ('page_timeout', '1200');
INSERT INTO php_stats_config VALUES ('report_w_on', '1');
INSERT INTO php_stats_config VALUES ('report_w_day', '1');
INSERT INTO php_stats_config VALUES ('instat_report_w', '0');
INSERT INTO php_stats_config VALUES ('instat_max_online', '0|0');
INSERT INTO php_stats_config VALUES ('auto_optimize', '1');
INSERT INTO php_stats_config VALUES ('auto_opt_every', '100');
INSERT INTO php_stats_config VALUES ('exc_fol','');
INSERT INTO php_stats_config VALUES ('exc_sip','');
INSERT INTO php_stats_config VALUES ('exc_dip','');

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_counters`
#

DROP TABLE IF EXISTS php_stats_counters;
CREATE TABLE php_stats_counters (
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  no_count_hits int(11) unsigned NOT NULL default '0',
  no_count_visits int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_counters`
#

INSERT INTO php_stats_counters VALUES (0, 0, 0, 0);

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_daily`
#

DROP TABLE IF EXISTS php_stats_daily;
CREATE TABLE php_stats_daily (
  data date NOT NULL default '0000-00-00',
  hits int(11) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  no_count_hits int(11) NOT NULL default '0',
  no_count_visits int(11) NOT NULL default '0',
  PRIMARY KEY  (data)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_daily`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_details`
#

DROP TABLE IF EXISTS php_stats_details;
CREATE TABLE php_stats_details (
  visitor_id varchar(50) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  host varchar(50) NOT NULL default '',
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  lang varchar(10) NOT NULL default '',
  date int(10) unsigned NOT NULL default '0',
  referer longtext NOT NULL default '',
  currentPage varchar(255) NOT NULL default '',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  titlePage varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_details`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_domains`
#

DROP TABLE IF EXISTS php_stats_domains;
CREATE TABLE php_stats_domains (
  visits int(11) NOT NULL default '0',
  hits int(11) NOT NULL default '0',
  tld varchar(8) NOT NULL default '',
  area varchar(4) NOT NULL default '',
  PRIMARY KEY  (tld)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_domains`
#

INSERT INTO php_stats_domains VALUES (0, 0, 'ac', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ad', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ae', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'af', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ag', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'ai', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'al', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'am', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'an', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'ao', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'aq', 'AN');
INSERT INTO php_stats_domains VALUES (0, 0, 'ar', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'as', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'au', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'aw', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'az', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ba', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'bb', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'bd', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'be', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'bf', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'bg', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'bh', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'bi', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'bj', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'bm', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'bn', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'bo', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'br', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'bs', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'bt', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'bv', 'AN');
INSERT INTO php_stats_domains VALUES (0, 0, 'bw', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'by', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'bz', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'ca', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'cc', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'cd', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'cf', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'cg', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ch', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ci', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ck', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'cl', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'cm', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'cn', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'co', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'cr', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'cu', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'cv', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'cx', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'cy', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'cz', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'de', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'dj', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'dk', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'dm', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'do', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'dz', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ec', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'ee', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'eg', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'eh', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'er', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'es', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'et', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'fi', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'fj', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'fk', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'fm', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'fo', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'fr', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ga', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'gd', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'ge', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'gf', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'gg', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'gh', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'gi', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'gl', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'gm', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'gn', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'gp', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'gq', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'gr', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'gs', 'AN');
INSERT INTO php_stats_domains VALUES (0, 0, 'gt', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'gu', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'gw', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'gy', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'hk', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'hm', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'hn', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'hr', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ht', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'hu', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'id', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ie', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'il', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'im', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'in', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'io', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'iq', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ir', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'is', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'it', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'je', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'jm', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'jo', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'jp', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ke', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'kg', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'kh', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ki', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'km', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'kn', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'kp', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'kr', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'kw', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ky', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'kz', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'la', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'lb', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'lc', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'li', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'lk', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'lr', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ls', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'lt', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'lu', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'lv', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ly', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ma', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'mc', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'md', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'mg', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'mh', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'mk', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ml', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'mm', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'mn', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'mo', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'mp', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'mq', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'mr', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ms', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'mt', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'mu', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'mv', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'mw', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'mx', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'my', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'mz', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'na', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'nc', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'ne', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'nf', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'ng', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ni', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'nl', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'no', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'np', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'nr', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'nu', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'nz', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'om', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'pa', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'pe', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'pf', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'pg', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'ph', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'pk', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'pl', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'pm', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'pn', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'pr', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'pt', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'pw', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'py', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'qa', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 're', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ro', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'ru', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'rw', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'sa', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'sb', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'sc', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'sd', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'se', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'sg', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'sh', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'si', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'sj', '');
INSERT INTO php_stats_domains VALUES (0, 0, 'sk', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'sl', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'sm', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'sn', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'so', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'sr', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'st', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'sv', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'sy', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'sz', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'tc', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'td', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'tf', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tg', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'th', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tj', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tk', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'tm', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tn', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'to', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'tp', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tr', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tt', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'tv', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'tw', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'tz', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'ua', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'ug', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'uk', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'gb', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'um', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'us', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'uy', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'uz', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'va', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'vc', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 've', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'vg', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'vi', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'vn', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'vu', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'wf', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'ws', 'OZ');
INSERT INTO php_stats_domains VALUES (0, 0, 'ye', 'AS');
INSERT INTO php_stats_domains VALUES (0, 0, 'yt', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'yu', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'za', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'zm', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'zr', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'zw', 'AF');
INSERT INTO php_stats_domains VALUES (0, 0, 'com', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'net', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'org', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'edu', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'int', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'arpa', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'at', 'EU');
INSERT INTO php_stats_domains VALUES (0, 0, 'gov', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'mil', 'AM');
INSERT INTO php_stats_domains VALUES (0, 0, 'su', 'GUS');
INSERT INTO php_stats_domains VALUES (0, 0, 'unknown', '');
INSERT INTO php_stats_domains VALUES (0, 0, 'arts', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'firm', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'info', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'nom', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'rec', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'shop', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'web', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'biz', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'pro', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'coop', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'museum', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'aero', 'UN');
INSERT INTO php_stats_domains VALUES (0, 0, 'eu', 'EU');

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_downloads`
#

DROP TABLE IF EXISTS php_stats_downloads;
CREATE TABLE php_stats_downloads (
  id int(11) NOT NULL auto_increment,
  nome varchar(255) NOT NULL default '',
  descrizione varchar(255) NOT NULL default '',
  type varchar(20) NOT NULL default '',
  home varchar(255) NOT NULL default '',
  size varchar(20) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  creazione int(11) NOT NULL default '0',
  downloads int(11) NOT NULL default '0',
  withinterface enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (id)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dump dei dati per la tabella `php_stats_downloads`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_hourly`
#

DROP TABLE IF EXISTS php_stats_hourly;
CREATE TABLE php_stats_hourly (
  data tinyint(4) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  no_count_hits int(11) unsigned NOT NULL default '0',
  no_count_visits int(11) unsigned NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_hourly`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_ip`
#

DROP TABLE IF EXISTS php_stats_ip;
CREATE TABLE php_stats_ip (
  ip varchar(15) NOT NULL default '',
  date int(11) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ip)
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_ip`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_langs`
#

DROP TABLE IF EXISTS php_stats_langs;
CREATE TABLE php_stats_langs (
  lang varchar(8) NOT NULL default '',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_langs`
#

INSERT INTO php_stats_langs VALUES ('unknown', 0, 0);
INSERT INTO php_stats_langs VALUES ('af', 0, 0);
INSERT INTO php_stats_langs VALUES ('sq', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-dz', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-bh', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-eg', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-iq', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-jo', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-kw', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-lb', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-ly', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-ma', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-om', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-qa', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-sa', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-sy', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-tn', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-ae', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar-ye', 0, 0);
INSERT INTO php_stats_langs VALUES ('ar', 0, 0);
INSERT INTO php_stats_langs VALUES ('hy', 0, 0);
INSERT INTO php_stats_langs VALUES ('as', 0, 0);
INSERT INTO php_stats_langs VALUES ('az', 0, 0);
INSERT INTO php_stats_langs VALUES ('az', 0, 0);
INSERT INTO php_stats_langs VALUES ('eu', 0, 0);
INSERT INTO php_stats_langs VALUES ('be', 0, 0);
INSERT INTO php_stats_langs VALUES ('bn', 0, 0);
INSERT INTO php_stats_langs VALUES ('bg', 0, 0);
INSERT INTO php_stats_langs VALUES ('ca', 0, 0);
INSERT INTO php_stats_langs VALUES ('zh-cn', 0, 0);
INSERT INTO php_stats_langs VALUES ('zh-hk', 0, 0);
INSERT INTO php_stats_langs VALUES ('zh-mo', 0, 0);
INSERT INTO php_stats_langs VALUES ('zh-sg', 0, 0);
INSERT INTO php_stats_langs VALUES ('zh-tw', 0, 0);
INSERT INTO php_stats_langs VALUES ('zh', 0, 0);
INSERT INTO php_stats_langs VALUES ('hr', 0, 0);
INSERT INTO php_stats_langs VALUES ('cs', 0, 0);
INSERT INTO php_stats_langs VALUES ('da', 0, 0);
INSERT INTO php_stats_langs VALUES ('div', 0, 0);
INSERT INTO php_stats_langs VALUES ('nl-be', 0, 0);
INSERT INTO php_stats_langs VALUES ('nl', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-au', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-bz', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-ca', 0, 0);
INSERT INTO php_stats_langs VALUES ('en', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-ie', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-jm', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-nz', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-ph', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-za', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-tt', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-gb', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-us', 0, 0);
INSERT INTO php_stats_langs VALUES ('en-zw', 0, 0);
INSERT INTO php_stats_langs VALUES ('en', 0, 0);
INSERT INTO php_stats_langs VALUES ('et', 0, 0);
INSERT INTO php_stats_langs VALUES ('fo', 0, 0);
INSERT INTO php_stats_langs VALUES ('fa', 0, 0);
INSERT INTO php_stats_langs VALUES ('fi', 0, 0);
INSERT INTO php_stats_langs VALUES ('fr-be', 0, 0);
INSERT INTO php_stats_langs VALUES ('fr-ca', 0, 0);
INSERT INTO php_stats_langs VALUES ('fr', 0, 0);
INSERT INTO php_stats_langs VALUES ('fr-lu', 0, 0);
INSERT INTO php_stats_langs VALUES ('fr-mc', 0, 0);
INSERT INTO php_stats_langs VALUES ('fr-ch', 0, 0);
INSERT INTO php_stats_langs VALUES ('mk', 0, 0);
INSERT INTO php_stats_langs VALUES ('gd', 0, 0);
INSERT INTO php_stats_langs VALUES ('ka', 0, 0);
INSERT INTO php_stats_langs VALUES ('de-at', 0, 0);
INSERT INTO php_stats_langs VALUES ('de', 0, 0);
INSERT INTO php_stats_langs VALUES ('de-li', 0, 0);
INSERT INTO php_stats_langs VALUES ('de-lu', 0, 0);
INSERT INTO php_stats_langs VALUES ('de-ch', 0, 0);
INSERT INTO php_stats_langs VALUES ('el', 0, 0);
INSERT INTO php_stats_langs VALUES ('gu', 0, 0);
INSERT INTO php_stats_langs VALUES ('he', 0, 0);
INSERT INTO php_stats_langs VALUES ('hi', 0, 0);
INSERT INTO php_stats_langs VALUES ('hu', 0, 0);
INSERT INTO php_stats_langs VALUES ('is', 0, 0);
INSERT INTO php_stats_langs VALUES ('id', 0, 0);
INSERT INTO php_stats_langs VALUES ('it', 0, 0);
INSERT INTO php_stats_langs VALUES ('it-ch', 0, 0);
INSERT INTO php_stats_langs VALUES ('ja', 0, 0);
INSERT INTO php_stats_langs VALUES ('kn', 0, 0);
INSERT INTO php_stats_langs VALUES ('kk', 0, 0);
INSERT INTO php_stats_langs VALUES ('kok', 0, 0);
INSERT INTO php_stats_langs VALUES ('ko', 0, 0);
INSERT INTO php_stats_langs VALUES ('kz', 0, 0);
INSERT INTO php_stats_langs VALUES ('lv', 0, 0);
INSERT INTO php_stats_langs VALUES ('lt', 0, 0);
INSERT INTO php_stats_langs VALUES ('ms', 0, 0);
INSERT INTO php_stats_langs VALUES ('ms', 0, 0);
INSERT INTO php_stats_langs VALUES ('ml', 0, 0);
INSERT INTO php_stats_langs VALUES ('mt', 0, 0);
INSERT INTO php_stats_langs VALUES ('mr', 0, 0);
INSERT INTO php_stats_langs VALUES ('mn', 0, 0);
INSERT INTO php_stats_langs VALUES ('ne', 0, 0);
INSERT INTO php_stats_langs VALUES ('nb-no', 0, 0);
INSERT INTO php_stats_langs VALUES ('no', 0, 0);
INSERT INTO php_stats_langs VALUES ('nn-no', 0, 0);
INSERT INTO php_stats_langs VALUES ('or', 0, 0);
INSERT INTO php_stats_langs VALUES ('pl', 0, 0);
INSERT INTO php_stats_langs VALUES ('pt-br', 0, 0);
INSERT INTO php_stats_langs VALUES ('pt', 0, 0);
INSERT INTO php_stats_langs VALUES ('pa', 0, 0);
INSERT INTO php_stats_langs VALUES ('rm', 0, 0);
INSERT INTO php_stats_langs VALUES ('ro-md', 0, 0);
INSERT INTO php_stats_langs VALUES ('ro', 0, 0);
INSERT INTO php_stats_langs VALUES ('ru-md', 0, 0);
INSERT INTO php_stats_langs VALUES ('ru', 0, 0);
INSERT INTO php_stats_langs VALUES ('sa', 0, 0);
INSERT INTO php_stats_langs VALUES ('sr', 0, 0);
INSERT INTO php_stats_langs VALUES ('sr', 0, 0);
INSERT INTO php_stats_langs VALUES ('sk', 0, 0);
INSERT INTO php_stats_langs VALUES ('ls', 0, 0);
INSERT INTO php_stats_langs VALUES ('sb', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-ar', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-bo', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-cl', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-co', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-cr', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-do', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-ec', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-sv', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-gt', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-hn', 0, 0);
INSERT INTO php_stats_langs VALUES ('es', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-mx', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-ni', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-pa', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-py', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-pe', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-pr', 0, 0);
INSERT INTO php_stats_langs VALUES ('es', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-us', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-uy', 0, 0);
INSERT INTO php_stats_langs VALUES ('es-ve', 0, 0);
INSERT INTO php_stats_langs VALUES ('sx', 0, 0);
INSERT INTO php_stats_langs VALUES ('sw', 0, 0);
INSERT INTO php_stats_langs VALUES ('sv-fi', 0, 0);
INSERT INTO php_stats_langs VALUES ('sv', 0, 0);
INSERT INTO php_stats_langs VALUES ('syr', 0, 0);
INSERT INTO php_stats_langs VALUES ('ta', 0, 0);
INSERT INTO php_stats_langs VALUES ('tt', 0, 0);
INSERT INTO php_stats_langs VALUES ('te', 0, 0);
INSERT INTO php_stats_langs VALUES ('th', 0, 0);
INSERT INTO php_stats_langs VALUES ('ts', 0, 0);
INSERT INTO php_stats_langs VALUES ('tn', 0, 0);
INSERT INTO php_stats_langs VALUES ('tr', 0, 0);
INSERT INTO php_stats_langs VALUES ('uk', 0, 0);
INSERT INTO php_stats_langs VALUES ('ur', 0, 0);
INSERT INTO php_stats_langs VALUES ('uz', 0, 0);
INSERT INTO php_stats_langs VALUES ('uz', 0, 0);
INSERT INTO php_stats_langs VALUES ('vi', 0, 0);
INSERT INTO php_stats_langs VALUES ('xh', 0, 0);
INSERT INTO php_stats_langs VALUES ('yi', 0, 0);
INSERT INTO php_stats_langs VALUES ('zu', 0, 0);

# --------------------------------------------------------

#
# Struttura della tabella `php_stats_pages`
#

DROP TABLE IF EXISTS php_stats_pages;
CREATE TABLE php_stats_pages (
  data varchar(255) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0',
  no_count_hits int(11) unsigned NOT NULL default '0',
  no_count_visits int(11) unsigned NOT NULL default '0',
  presence bigint(20) unsigned default '0',
  tocount int(10) unsigned NOT NULL default '0',
  date int(11) unsigned NOT NULL default '0',
  lev_1 int(10) NOT NULL default '0',
  lev_2 int(10) NOT NULL default '0',
  lev_3 int(10) NOT NULL default '0',
  lev_4 int(10) NOT NULL default '0',
  lev_5 int(10) NOT NULL default '0',
  lev_6 int(10) NOT NULL default '0',
  outs int(10) NOT NULL default '0',
  titlePage varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_pages`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_query`
#

DROP TABLE IF EXISTS php_stats_query;
CREATE TABLE php_stats_query (
  data varchar(255) binary NOT NULL default '',
  engine varchar(30) NOT NULL default '',
  domain varchar(8) NOT NULL default '?',
  page smallint(6) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  date int(10) unsigned NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_query`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_referer`
#

DROP TABLE IF EXISTS php_stats_referer;
CREATE TABLE php_stats_referer (
  data varchar(255) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_referer`
#


# --------------------------------------------------------

#
# Struttura della tabella `php_stats_systems`
#

DROP TABLE IF EXISTS php_stats_systems;
CREATE TABLE php_stats_systems (
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  hits int(11) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

#
# Dump dei dati per la tabella `php_stats_systems`
#