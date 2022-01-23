###########################################

# modifiche alla tabella php_stats_cache

ALTER TABLE php_stats_cache ADD level TINYINT UNSIGNED NOT NULL ;

###########################################

# modifiche alla tabella php_stats_config

INSERT INTO php_stats_config (name,value) VALUES ('last_cache_time', '0');
INSERT INTO php_stats_config (name,value) VALUES ('upd_available', '0');
INSERT INTO php_stats_config (name,value) VALUES ('stats_disabled', '0');
DELETE FROM php_stats_config WHERE name='visualizza';


###########################################

# modifiche alla tabella php_stats_details

ALTER TABLE php_stats_details ADD titlePage VARCHAR(255) NOT NULL;

###########################################

# modifiche alla tabella php_stats_ip

DELETE FROM php_stats_ip;
ALTER TABLE php_stats_ip CHANGE ip ip DOUBLE NOT NULL;

###########################################

# modifiche alla tabella php_stats_pages

ALTER TABLE php_stats_pages CHANGE ins lev_1 INT(10) DEFAULT '0' NOT NULL;
ALTER TABLE php_stats_pages ADD lev_2 INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER lev_1,ADD lev_3 INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER lev_2, ADD lev_4 INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER lev_3, ADD lev_5 INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER lev_4, ADD lev_6 INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER lev_5;
ALTER TABLE php_stats_pages CHANGE outs outs INT(10) DEFAULT '0' NOT NULL;
ALTER TABLE php_stats_pages ADD titlePage  VARCHAR( 255 ) NOT NULL ;

###########################################

# SCRIVO LA NUOVA VERSIONE

UPDATE php_stats_config SET value='0.1.8' WHERE name='phpstats_ver';