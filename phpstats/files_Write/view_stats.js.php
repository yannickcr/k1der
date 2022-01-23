<?php
	    ////////////////////////////////////////////////
        //   ___ _  _ ___     ___ _____ _ _____ ___   //
        //  | _ \ || | _ \___/ __|_   _/_\_   _/ __|  //
        //  |  _/ __ |  _/___\__ \ | |/ _ \| | \__ \  //
        //  |_| |_||_|_|0.1.9|___/ |_/_/ \_\_| |___/  //
        //                                            //
   /////////////////////////////////////////////////////////
   //       Author: Roberto Valsania (Webmaster76)        //
   //   Staff:                                            //
   //         Matrix - Massimiliano Coppola               //
   //         Viewsource                                  //
   //         PaoDJ - Paolo Antonio Tremadio              //
   //         Fabry - Fabrizio Tomasoni                   //
   //         theCAS - Carlo Alberto Siti                 //
   //                                                     //
   //          Homepage: www.php-stats.com,               //
   //                    www.php-stats.it,                //
   //                    www.php-stat.com                 //
   /////////////////////////////////////////////////////////

      //////////////////////////////////////////////////////////////
      // MODE 0 -> Utenti on-line (default)                       //
      // MODE 1 -> Visitatori di OGGI                             //
      // MODE 2 -> Pagine visitate di OGGI                        //
      // MODE 3 -> Visitatori TOTALI                              //
      // MODE 4 -> Pagine visitate TOTALI                         //
      // MODE 8 -> Downloads (bisogna specificare l'ID)           //
      // MODE 9 -> Clicks (bisogna specificare l'ID)              //
      //////////////////////////////////////////////////////////////

define ('__OPTIONS_FILE__','option/php-stats-options.php');
define ('__LOCK_FILE__','option/options_lock.php');

         if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
      if(!isset($_SERVER)) $_SERVER=$HTTP_SERVER_VARS;
  if(isset($_GET['mode'])) $mode=addslashes($_GET['mode']); else $mode=0;
 if(isset($_GET['style'])) $style=addslashes($_GET['style']); else $style="";
if(isset($_GET['digits'])) $digits=addslashes($_GET['digits']); else $digits="";
    if(isset($_GET['id'])) $id=addslashes($_GET['id']);

define('IN_PHPSTATS',true);
// Verifica presenza del file di opzioni libero altrimenti aspetto ed inclusione funzioni principali
if (file_exists(__LOCK_FILE__)) sleep(2);
@require(__OPTIONS_FILE__);
@require('inc/main_func_stats.inc.php');
@require('inc/admin_func.inc.php');

// Connessione a MySQL e selezione database
db_connect();

if($option['stats_disabled']) die();

if($digits=="") $digits=$option['cifre'];
 if($style=="") $style=$option['stile'];

switch ($mode)
  {
    // UTENTI ON-LINE
    case '0':
    $ip=$_SERVER['REMOTE_ADDR'];
	if($option['online_timeout']>0)
	  $tmp=$option['online_timeout']*60;
	  else
	  {
      $result=sql_query("SELECT SUM(tocount),SUM(presence) FROM $option[prefix]_pages");
      list($tocount_pages,$presence_pages)=@mysql_fetch_row($result);
      $tempo_pagina=round(($presence_pages/$tocount_pages),0);
      $tmp=$tempo_pagina*1.3;
	  }
	$date=(time()-($option['timezone']*3600)-($tmp));
    $result=sql_query("SELECT data FROM $option[prefix]_cache WHERE data>$date AND os NOT REGEXP 'Spider|Grabber'");
    $online=@mysql_num_rows($result);
    $result=sql_query("SELECT * FROM $option[prefix]_cache WHERE user_id='$ip' AND data>$date AND os NOT REGEXP 'Spider|Grabber'");
    $ip_presente=@mysql_num_rows($result);
    if($ip_presente!=1) ++$online;
    $toshow=$online;
    break;

    // VISITATORI DI OGGI (0.1.9)
    case '1':
	$tmp1=0;
	$tmp2=0;
    $data_oggi=date("Y-m-d",mktime(date("G")-$option['timezone'],date("i"),0,date("m"),date("d"),date("Y")));
    $result=sql_query("SELECT visits,no_count_visits FROM $option[prefix]_daily WHERE data='$data_oggi' LIMIT 1");
    list($tmp1,$tmp2)=@mysql_fetch_row($result);
	$result=sql_query("SELECT SUM(visits) FROM $option[prefix]_cache WHERE giorno='$data_oggi' AND os NOT REGEXP 'Spider|Grabber' LIMIT 1");
	list($tmp3)=@mysql_fetch_row($result);
	$toshow=$tmp1-$tmp2+$tmp3;
    break;

    // PAGINE OGGI (0.1.9)
    case '2':
	$tmp1=0;
	$tmp2=0;
    $data_oggi=date("Y-m-d",mktime(date("G")-$option['timezone'],date("i"),0,date("m"),date("d"),date("Y")));
    $result=sql_query("SELECT hits,no_count_hits FROM $option[prefix]_daily WHERE data='$data_oggi' LIMIT 1");
    list($tmp1,$tmp2)=@mysql_fetch_row($result);
	$result=sql_query("SELECT SUM(hits) FROM $option[prefix]_cache WHERE giorno='$data_oggi' AND os NOT REGEXP 'Spider|Grabber' LIMIT 1");
	list($tmp3)=@mysql_fetch_row($result);
	$toshow=$tmp1-$tmp2+$tmp3;
    break;

	// Visitatori totali
	case '3':
	list($visite,$no_count_visite)=@mysql_fetch_row(sql_query("SELECT visits,no_count_visits FROM $option[prefix]_counters LIMIT 1"));
    $toshow=$visite-$no_count_visite+$option['startvisits'];
	break;

	// Pagine totali
	case '4':
	list($hits,$no_count_hits)=@mysql_fetch_row(sql_query("SELECT hits,no_count_hits FROM $option[prefix]_counters LIMIT 1"));
	$toshow=$hits-$no_count_hits+$option['starthits'];
	break;

	// NUMERO DI DOWNLOADS DELL'ID SPECIFICATO
    case '8':
	$toshow=0;
	if(!ereg('(^[0-9]*[0-9]$)',$id)) die("<B>ERRORE:</B> Specificare un id numerico.");
    $result=sql_query("SELECT downloads FROM $option[prefix]_downloads WHERE id='$id' LIMIT 1");
    list($toshow)=@mysql_fetch_row($result);
    break;

	// NUMERO DI CLICKS DELL'ID SPECIFICATO
    case '9':
	$toshow=0;
	if(!ereg('(^[0-9]*[0-9]$)',$id)) die("<B>ERRORE:</B> Specificare un id numerico.");
    $result=sql_query("SELECT clicks FROM $option[prefix]_clicks WHERE id='$id' LIMIT 1");
    list($toshow)=@mysql_fetch_row($result);
    break;

  }
// VISUALIZZO
  $line="document.write('";
  if($style==0)
    $line.=$toshow;
    else
    {
    chop($toshow);
	$nb_digits=max(strlen($toshow),$digits);
    $toshow=substr("0000000000".$toshow,-$nb_digits);
    $digits=preg_split("//",$toshow);
    for($i=0;$i<=$nb_digits;++$i)
      if($digits[$i]!="") $line.="<IMG SRC=\"$option[script_url]/stili/$style/$digits[$i].gif\">";
    }
  $line.="');";
  echo"$line";

// Chiusura connessione a MySQL se necessario.
if($option['persistent_conn']!=1) mysql_close();
unset($option);
?>