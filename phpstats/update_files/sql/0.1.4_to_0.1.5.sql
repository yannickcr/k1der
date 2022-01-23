# MODIFICHE AL DATABASE DALLA 0.1.4 ALLA 0.1.5

# APPORTO LE MODIFICHE

INSERT INTO php_stats_config VALUES ('report_w_on', '0');
INSERT INTO php_stats_config VALUES ('report_w_day', '6');
INSERT INTO php_stats_config VALUES ('report_w', '1044658800');
INSERT INTO php_stats_config VALUES ('auto_optimize', '0');

ALTER TABLE php_stats_counters CHANGE hits hits INT(11) UNSIGNED DEFAULT '0';  
ALTER TABLE php_stats_counters CHANGE visits visits INT(11) UNSIGNED DEFAULT '0';  
ALTER TABLE php_stats_referer ADD mese VARCHAR(8) NOT NULL;
ALTER TABLE php_stats_query ADD mese VARCHAR(8) NOT NULL;
ALTER TABLE php_stats_hourly ADD mese VARCHAR(8) NOT NULL;  

CREATE TABLE php_stats_systems (
  os varchar(20) NOT NULL default '',
  bw varchar(20) NOT NULL default '',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  hits int(11) NOT NULL default '0',
  visits int(11) NOT NULL default '0',
  mese varchar(8) NOT NULL default ''
) TYPE=MyISAM;

CREATE TABLE php_stats_cache (
  user_id varchar(15) NOT NULL default '',
  data int(11) NOT NULL default '0',
  lastpage varchar(255) NOT NULL default '0',
  visitor_id varchar(50) NOT NULL default '',
  hits int(10) unsigned NOT NULL default '0',
  visits int(10) unsigned NOT NULL default '0',
  reso varchar(10) NOT NULL default '',
  colo varchar(10) NOT NULL default '',
  agent varchar(255) NOT NULL default '',
  host varchar(50) NOT NULL default '',
  lang varchar(8) NOT NULL default '',
  giorno varchar(10) NOT NULL default ''
) TYPE=MyISAM;

ALTER TABLE php_stats_domains
  DROP desc_tw,
  DROP desc_se,
  DROP desc_ru,
  DROP desc_no,
  DROP desc_it,
  DROP desc_bh,
  DROP desc_cnb,
  DROP desc_pl,
  DROP desc_nl,
  DROP desc_fr,
  DROP desc_es,
  DROP desc_cn,
  DROP desc_de,
  DROP desc_en;

CREATE TABLE php_stats_langs (
  lang varchar(8) NOT NULL default '',
  hits int(11) unsigned NOT NULL default '0',
  visits int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM;
  
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

DROP TABLE php_stats_engines;

UPDATE php_stats_config SET value='0.1.5' WHERE name='phpstats_ver';