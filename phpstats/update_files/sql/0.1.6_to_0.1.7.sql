# MODIFICHE AL DATABASE DALLA 0.1.6 ALLA 0.1.7

# APPORTO LE MODIFICHE
ALTER TABLE php_stats_cache CHANGE visitor_id visitor_id VARCHAR(30) NOT NULL;
ALTER TABLE php_stats_cache CHANGE hits hits TINYINT UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE php_stats_cache CHANGE visits visits SMALLINT UNSIGNED DEFAULT '0' NOT NULL;
INSERT INTO php_stats_config VALUES ('max_online', '0|1');

# SCRIVO LA NUOVA VERSIONE
UPDATE php_stats_config SET value='0.1.7' WHERE name='phpstats_ver';