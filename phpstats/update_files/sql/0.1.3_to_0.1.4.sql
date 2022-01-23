# MODIFICHE AL DATABASE DALLA 0.1.3 RC2 ALLA 0.1.4

# APPORTO LE MODIFICHE
INSERT INTO php_stats_config values('ip_timeout','1');
INSERT INTO php_stats_config values('page_timeout','600');

# SCRIVO LA NUOVA VERSIONE
UPDATE php_stats_config SET value='0.1.4' WHERE name='phpstats_ver';