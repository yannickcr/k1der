<?php
if(!defined('IN_PHPSTATS')) die("Php-Stats internal file.");
ignore_user_abort(true);

$option=Array(
'host'=>'localhost',
'database'=>'k1der1',
'user_db'=>'root',
'pass_db'=>'',
'script_url'=>'http://www.k1der.net/phpstats',
'exc_pass'=>'',
'prefix'=>'php_stats',
'callviaimg'=>1,
'php_stats_safe'=>0,
'out_compress'=>1,
'persistent_conn'=>0,
'autorefresh'=>3,
'show_server_details'=>1,
'show_average_user'=>0,
'short_url'=>1,
'lock_not_valid_url'=>0,
'ext_whois'=>'',
'online_timeout'=>5,
'page_title'=>1,
'refresh_page_title'=>0,
'log_host'=>0,
'clear_cache'=>0,
'full_recn'=>0,
'logerrors'=>0,
'check_new_version'=>1,
'www_trunc'=>0,
'accept_ssi'=>0,
'compatibility_mode'=>0,
'ip-zone'=>0,
'down_mode'=>0,
'check_links'=>1,
'stats_disabled'=>0,
'language'=>'fr',
'server_url'=>'http://www.k1der.net',
'admin_pass'=>'',
'use_pass'=>0,
'cifre'=>8,
'stile'=>1,
'timezone'=>0,
'template'=>'default',
'startvisits'=>0,
'starthits'=>0,
'nomesito'=>'-=K1der=- The Chocolat Effect || Clan Counter-Strike de Bretagne',
'user_mail'=>'no@mail.com',
'user_pass_new'=>'',
'user_pass_key'=>'',
'prune_0_on'=>1,
'prune_0_value'=>72,
'prune_1_on'=>1,
'prune_1_value'=>1000,
'prune_2_on'=>1,
'prune_2_value'=>1000,
'prune_3_on'=>1,
'prune_3_value'=>1000,
'prune_4_on'=>1,
'prune_4_value'=>1000,
'prune_5_on'=>1,
'prune_5_value'=>1000,
'phpstats_ver'=>'0.1.9.1',
'inadm_lastcache_time'=>1113501275,
'ip_timeout'=>1,
'page_timeout'=>1200,
'report_w_on'=>0,
'report_w_day'=>1,
'instat_report_w'=>1110754800,
'auto_optimize'=>1,
'auto_opt_every'=>100,
'exc_fol'=>'',
'exc_sip'=>'',
'exc_dip'=>''
);

$modulo=Array(1,2,1,2,2,2,1,1,1,1,1,1);

$unlockedPages=Array(
''
);

$serverUrl=Array(
'http://www.k1der.net'
);
$countServerUrl=1;

$countExcFol=0;

$countExcSip=0;

$countExcDip=0;

$default_pages=Array(
'/',
'/index.htm',
'/index.html',
'/default.htm',
'/index.php',
'/index.asp',
'/default.asp');
?>