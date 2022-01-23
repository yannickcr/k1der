###########################################
ALTER TABLE php_stats_query CHANGE domain domain VARCHAR( 8 ) DEFAULT 'unknown' NOT NULL;
UPDATE php_stats_query set domain=substring_index(engine,'.',-1), engine=substring_index(engine,'.',1) where instr(engine,'.')!=0;
UPDATE php_stats_query set domain='unknown' where domain='com' or domain='org' or domain='net';
UPDATE php_stats_query set domain='unknown' where domain='?';

# SCRIVO LA NUOVA VERSIONE

UPDATE php_stats_config SET value='0.1.9.1' WHERE name='phpstats_ver';
