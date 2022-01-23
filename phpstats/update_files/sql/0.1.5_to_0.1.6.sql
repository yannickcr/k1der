# MODIFICHE AL DATABASE DALLA 0.1.5 RC 1 ALLA 0.1.6

INSERT INTO php_stats_config VALUES ('auto_opt_every', '100');
UPDATE php_stats_config SET value='0.1.6' WHERE name='phpstats_ver';