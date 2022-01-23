<?php
// PROBABILMENTE LO STR_REPLACE SI POTRA' ELIMINARE NELLA DEF ALTRIMENTI DOPO USEREMO pathinfo
if (!defined('__PHP_STATS_PATH__')) {
  $path_include=(!isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['PATH_TRANSLATED']) : $_SERVER['DOCUMENT_ROOT'].str_replace('\\','/',dirname($_SERVER['PHP_SELF']))).'/';
  $path_include=str_replace(Array('//','\\\\'),Array('/','/'),$path_include);
  if(strpos($path_include,':')===1) $path_include=substr($path_include,2);
  define('__PHP_STATS_PATH__',$path_include);
  }
$GLOBALS['php_stats_appendVarJs']=$GLOBALS['php_stats_sendVarJs']=$GLOBALS['php_stats_script_url']=$GLOBALS['php_stats_full_recn']='';
@require(__PHP_STATS_PATH__.'php-stats.recphp.php');
if((($GLOBALS['php_stats_appendVarJs']!='' && $GLOBALS['php_stats_sendVarJs']==1) || $GLOBALS['php_stats_full_recn']) && $php_stats_ok==1) echo '<script type="text/javascript" src="'.$GLOBALS['php_stats_script_url'].'/php-stats.phpjs.php?'.$GLOBALS['php_stats_appendVarJs'].'"></script>';
?>