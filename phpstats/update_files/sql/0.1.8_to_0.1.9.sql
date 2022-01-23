###########################################

CREATE TABLE php_stats_cache_back (user_id varchar(15) NOT NULL default '0', data int(11) NOT NULL default '0', lastpage varchar(255) NOT NULL default '0', visitor_id varchar(30) NOT NULL default '', hits tinyint(3) unsigned NOT NULL default '0', visits smallint(5) unsigned NOT NULL default '0', reso varchar(10) NOT NULL default '', colo varchar(10) NOT NULL default '', agent varchar(255) NOT NULL default '', host varchar(50) NOT NULL default '', lang varchar(8) NOT NULL default '', giorno varchar(10) NOT NULL default '', level tinyint(3) unsigned NOT NULL default '0') TYPE=MyISAM;
INSERT INTO php_stats_cache_back (user_id,data,lastpage,visitor_id,hits,visits,reso,colo,agent,host,lang,giorno,level) SELECT DISTINCT(user_id),data,lastpage,visitor_id,hits,visits,reso,colo,agent,host,lang,giorno,level FROM php_stats_cache;
DELETE FROM php_stats_cache;
INSERT INTO php_stats_cache (user_id,data,lastpage,visitor_id,hits,visits,reso,colo,agent,host,lang,giorno,level) SELECT user_id,data,lastpage,visitor_id,hits,visits,reso,colo,agent,host,lang,giorno,level FROM php_stats_cache_back;
DROP TABLE IF EXISTS php_stats_cache_back;
ALTER TABLE php_stats_cache ADD UNIQUE (user_id);
ALTER TABLE php_stats_cache CHANGE visitor_id visitor_id VARCHAR( 32 ) NOT NULL; 
ALTER TABLE php_stats_cache ADD COLUMN os varchar(20) NOT NULL AFTER agent;
ALTER TABLE php_stats_cache ADD COLUMN bw varchar(20) NOT NULL AFTER os;
UPDATE php_stats_config SET name='instat_report_w' WHERE name='report_w';
UPDATE php_stats_config SET name='instat_max_online' WHERE name='max_online';
UPDATE php_stats_config SET name='inadm_last_update' WHERE name='last_update';
UPDATE php_stats_config SET name='inadm_lastcache_time' WHERE name='last_cache_time';
UPDATE php_stats_config SET name='inadm_upd_available' WHERE name='upd_available';
ALTER TABLE php_stats_config ADD PRIMARY KEY (name);
DELETE FROM php_stats_config WHERE name='exc_fol';
DELETE FROM php_stats_config WHERE name='exc_ip';
DELETE FROM php_stats_config WHERE name='exc_sip';
DELETE FROM php_stats_config WHERE name='exc_dip';
INSERT INTO php_stats_config VALUES ('unlock_pages','0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|');
INSERT INTO php_stats_config VALUES ('exc_fol', '');
INSERT INTO php_stats_config VALUES ('exc_sip', '');
INSERT INTO php_stats_config VALUES ('exc_dip', '');
ALTER TABLE php_stats_query ADD COLUMN domain varchar(8) NOT NULL DEFAULT '?' AFTER engine;
ALTER TABLE php_stats_query ADD COLUMN page smallint(6) NOT NULL DEFAULT 0 AFTER domain;
UPDATE php_stats_systems SET bw='Explorer 6.0' WHERE bw='Explorer 6';
UPDATE php_stats_systems SET bw='Explorer 5.0' WHERE bw='Explorer 5';
ALTER TABLE php_stats_daily ADD COLUMN no_count_hits int(11) DEFAULT 0 NOT NULL AFTER visits;
ALTER TABLE php_stats_daily ADD COLUMN no_count_visits int(11) DEFAULT 0 NOT NULL AFTER no_count_hits;
ALTER TABLE php_stats_counters ADD COLUMN no_count_hits int(11) unsigned NOT NULL DEFAULT 0;
ALTER TABLE php_stats_counters ADD COLUMN no_count_visits int(11) unsigned NOT NULL DEFAULT 0;
ALTER TABLE php_stats_pages ADD COLUMN no_count_hits int(11) NOT NULL DEFAULT 0 AFTER visits;
ALTER TABLE php_stats_pages ADD COLUMN no_count_visits int(11) NOT NULL DEFAULT 0 AFTER no_count_hits;
ALTER TABLE php_stats_hourly ADD COLUMN no_count_hits int(11) unsigned NOT NULL DEFAULT 0 AFTER visits;
ALTER TABLE php_stats_hourly ADD COLUMN no_count_visits int(11) unsigned NOT NULL DEFAULT 0 AFTER no_count_hits;
ALTER TABLE php_stats_details CHANGE COLUMN referer referer longtext;
ALTER TABLE php_stats_details ADD os VARCHAR(20) NOT NULL AFTER agent, ADD bw VARCHAR(20) NOT NULL AFTER os;
ALTER TABLE php_stats_downloads CHANGE COLUMN nome nome varchar(255) NOT NULL;
ALTER TABLE php_stats_downloads ADD COLUMN descrizione varchar(255) NOT NULL default '' AFTER nome;
ALTER TABLE php_stats_downloads ADD COLUMN type varchar(20) NOT NULL default '' AFTER descrizione;
ALTER TABLE php_stats_downloads ADD COLUMN home varchar(255) NOT NULL default '' AFTER type;
ALTER TABLE php_stats_downloads ADD COLUMN size varchar(20) NOT NULL default '' AFTER home;
ALTER TABLE php_stats_downloads ADD COLUMN withinterface enum('YES','NO') NOT NULL DEFAULT 'NO' AFTER downloads;
ALTER TABLE php_stats_query CHANGE COLUMN data data varchar(255) binary NOT NULL DEFAULT '';

# SCRIVO LA NUOVA VERSIONE

UPDATE php_stats_config SET value='0.1.9' WHERE name='phpstats_ver';
