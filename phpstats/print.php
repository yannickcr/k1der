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

$is_loged_in=0;  // Non loggato
define('IN_PHPSTATS', true);
$body="";
$style=""; // In caso di register globals=on
// Vars declaration
                 if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
              if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
          if(isset($_GET['what'])) $what=addslashes($_GET['what']); else $what="";
if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie="";

// inclusioni principali delle funzioni esterne
require('config.php');
require('inc/main_func.inc.php');
require('inc/admin_func.inc.php');
if(isset($option['out_compress'])) if($option['out_compress']==1) if(phpversion()>"4.0.4") ob_start("ob_gzhandler");
if(!isset($option['prefix']) || $option['prefix']=='') $option['prefix']="php_stats";
// Connessione a MySQL e selezione database
db_connect();
//Leggo le variabili
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_array($result)) $option[$row[0]]=$row[1];
include("lang/$option[language]/main_lang.inc");
$data=date("Y-m-d",mktime(date("G")-$option['timezone'],date("i"),0,date("m"),date("d"),date("Y")));
$data=explode("-",$data);
$today=str_replace("%mount%", formatmount($data[1]),$varie['date_format']);
$today=str_replace("%day%",$data[2],$today);
$today=str_replace("%year%",$data[0],$today);
$gen_on=str_replace("%php-stats-ver%",$option['phpstats_ver'],$string['gen_on']);
$gen_on=str_replace("%data%",$today,$gen_on);
// Controllo password
if($pass_cookie==md5($option['admin_pass'])) $is_loged_in=1;
if($option['use_pass'] == '1') if($is_loged_in!=1) { header("Location: $option[script_url]/admin.php"); }

switch ($what) {

  case 'referer':
  $count=1;
   $body.=
   "<center><font size=\"5\">$string[se_title]</font><br>".
   "<font size=\"1\">$gen_on</font><br><br>".
   "</center>".
   "<center><table width=\"500\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
   $result=sql_query("SELECT * FROM $option[prefix]_engines ORDER BY visits DESC");
   while($row=@mysql_fetch_array($result))
     {
         if($count%2>0) $bgcolor="#FAFAFA"; else $bgcolor="#FFFFFF";
         $body.="<tr><td width=\"400\" bgcolor=\"$bgcolor\">$row[0]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[1]</td></tr>";
     ++$count;
         }
   $body.="</table></center>";
   break;

  case 'query':
  $count=1;
  $body.=
  "<center><font size=\"5\">$string[query_title]</font><br>".
  "<font size=\"1\">$gen_on</font><br><br>".
  "</center>".
  "<center><table width=\"500\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  $result=sql_query("SELECT * FROM $option[prefix]_query ORDER BY visits DESC");
  while($row=@mysql_fetch_array($result))
    {
        $row[0]=htmlspecialchars($row[0]);
        if($count%2>0) $bgcolor="#FAFAFA"; else $bgcolor="#FFFFFF";
        $body.="<tr><td width=\"500\" bgcolor=\"$bgcolor\">$row[0]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[1]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[2]</td></tr>";
    ++$count;
        }
  $body.="</table></center>";
  break;

  case 'query-mens':
  if(isset($_GET['mese'])) $mese=addslashes($_GET['mese']); else $mese=date("Y-m",time()-$option['timezone']*3600);
  $count=1;
  $body.=
  "<center><font size=\"5\">$string[query_title]</font><br>".
  "<font size=\"1\">$gen_on</font><br><br>".
  "</center>".
  "<center><table width=\"500\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  $result=sql_query("SELECT * FROM $option[prefix]_query WHERE mese='$mese' ORDER BY visits DESC");
  while($row=@mysql_fetch_array($result))
    {
        $row[0]=htmlspecialchars($row[0]);
        if($count%2>0) $bgcolor="#FAFAFA"; else $bgcolor="#FFFFFF";
        $body.="<tr><td width=\"500\" bgcolor=\"$bgcolor\">$row[0]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[1]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[2]</td></tr>";
    ++$count;
        }
  $body.="</table></center>";
  break;

  case 'query-tot':
  $count=1;
  $body.=
  "<center><font size=\"5\">$string[query_title]</font><br>".
  "<font size=\"1\">$gen_on</font><br><br>".
  "</center>".
  "<center><table width=\"500\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  $result=sql_query("SELECT data,SUM(visits) AS test ,date FROM $option[prefix]_query GROUP BY data ORDER BY test DESC");
  while($row=@mysql_fetch_array($result))
    {
        if($count%2>0) $bgcolor="#FAFAFA"; else $bgcolor="#FFFFFF";
        $row[0]=htmlspecialchars($row[0]);
        $body.="<tr><td width=\"500\" bgcolor=\"$bgcolor\">$row[0]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[1]</td></tr>";
    ++$count;
        }
  $body.="</table></center>";
  break;

  case 'query-tot-mens':
  if(isset($_GET['mese'])) $mese=addslashes($_GET['mese']); else $mese=date("Y-m",time()-$option['timezone']*3600);
  $count=1;
  $body.=
  "<center><font size=\"5\">$string[query_title]</font><br>".
  "<font size=\"1\">$gen_on</font><br><br>".
  "</center>".
  "<center><table width=\"500\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  $result=sql_query("SELECT data,SUM(visits) AS test ,date FROM $option[prefix]_query WHERE mese='$mese' GROUP BY data ORDER BY test DESC");
  while($row=@mysql_fetch_array($result))
    {
        if($count%2>0) $bgcolor="#FAFAFA"; else $bgcolor="#FFFFFF";
        $row[0]=htmlspecialchars($row[0]);
        $body.="<tr><td width=\"500\" bgcolor=\"$bgcolor\">$row[0]</td><td width=\"100\" align=\"right\" bgcolor=\"$bgcolor\">$row[1]</td></tr>";
    ++$count;
        }
  $body.="</table></center>";
  break;

  default:
  header("Location: $option[script_url]/admin.php");
  break;
}

$page=
"<html>\n".
"<head>\n".
"<title>Php-Stat PRO v.$option[phpstats_ver]</title>\n".
"<META NAME=\"ROBOTS\" CONTENT=\"NONE\">\n".
"<style type=\"text/css\">".
"a:visited,a:active {text-decoration:none; color:#000000; font-weight:plain;}".
"a:hover {text-decoration:none; color:#AA0000; font-weight: plain;}".
"a:link { text-decoration:none; color:#000000; font-weight:plain; }".
"</style>".
"</head>\n".
"<body>\n".
$body."\n".
"<br><CENTER><FONT face=verdana size=1>Php-Stats $option[phpstats_ver] © <a href=\"http://www.php-stats.com\">Webmaster76</a></FONT></CENTER>\n".
"</body>";
//Output the html
echo"$page";
// Chiusura connessione a MySQL se necessario
if($option['persistent_conn']!=1) mysql_close();
?>