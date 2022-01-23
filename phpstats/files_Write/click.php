<?php   ////////////////////////////////////////////////
        //   ___ _  _ ___     ___ _____ _ _____ ___   //
        //  | _ \ || | _ \___/ __|_   _/_\_   _/ __|  //
        //  |  _/ __ |  _/___\__ \ | |/ _ \| | \__ \  //
        //  |_| |_||_|_|0.1.9|___/ |_/_/ \_\_| |___/  //
        //                                            //
   /////////////////////////////////////////////////////////
   //       Author: Roberto Valsania (Webmaster76)        //
   //   Staff: Matrix, Viewsource, PaoDJ, Fabry, theCAS   //
   //          Homepage: www.php-stats.com,               //
   //                    www.php-stats.it,                //
   //                    www.php-stat.com                 //
   /////////////////////////////////////////////////////////

                  if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
             if(isset($_GET['id'])) $id=$_GET['id']; else $id="";
if(isset($_SERVER['HTTP_REFERER'])) $HTTP_REFERER=$_SERVER['HTTP_REFERER'];
 if(isset($_SERVER['REMOTE_ADDR'])) $ip=(isset($_SERVER['HTTP_PC_REMOTE_ADDR']) ? $_SERVER['HTTP_PC_REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR']);

define('IN_PHPSTATS', true);
define('__OPTIONS_FILE__','option/php-stats-options.php');
define('__LOCK_FILE__','option/options_lock.php');

// inclusione delle principali funzioni esterne
if (file_exists(__LOCK_FILE__)) sleep(2);
if (!@include(__OPTIONS_FILE__)) die("<b>ERRORE</b>: File di config non accessibile.");
if(!@include('inc/main_func.inc.php')) die('<b>ERRORE</b>: File main_func.inc.php non accessibile.');

$get='';
if($option['prefix']=='') $option['prefix']='php_stats';
// Connessione a MySQL e selezione database
db_connect();

if(!@include("lang/$option[language]/main_lang.inc")) die("<b>ERRORE</b>: File $option[language]/main_lang.inc non accessibile."); // Language file

// Statistiche attive?
if($option['stats_disabled']) die($string['click_stats_disabled']);

// Controllo la validità dell'id (Per evitare SQL injection!)
if(!ereg('(^[0-9]*[0-9]$)',$id)) die("$error[click_errs_id]");
if($id!='')
  {
  $result=sql_query("SELECT url FROM $option[prefix]_clicks WHERE id='$id' LIMIT 1");
  if(mysql_affected_rows()>0)
    list($get)=@mysql_fetch_row($result);
    else
    echo"<br><br><center>$error[click_noid]</center>";
  }
if($get!="")
  {
  $get=str_replace(" ","%20",$get);
  $check=@fopen($get,"r");
  if($check!=false)
    {
    header("location: $get");
    sql_query("UPDATE $option[prefix]_clicks SET clicks=clicks+1 WHERE id='$id'");
    }
    else
    {
    $tmp=str_replace("%filename%",$get,$error['click_down']);
    echo"<br><br><center>$tmp</center>";
    }
  if($modulo[0]):
  // INSERISCO NEI DETTAGLI IL CLICK
  $result=sql_query("SELECT visitor_id FROM $option[prefix]_cache WHERE user_id='$ip' LIMIT 1");
  if(@mysql_num_rows($result)>0)
    {
    list($visitor_id)=@mysql_fetch_row($result);
    $date=time()-$option['timezone']*3600;
    $loaded="clk|$id";
    sql_query("INSERT INTO $option[prefix]_details VALUES ('$visitor_id','$ip','','','','','$date','','$loaded','','','')");
    }
  endif;
  }
?>