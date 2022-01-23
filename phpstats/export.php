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
// Vars declaration
                  if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
                 if(!isset($_POST)) $_POST=$HTTP_POST_VARS;
               if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
           if(isset($_GET['what'])) $what=addslashes($_GET['what']); else $what='';
      if(isset($_POST['compress'])) $compress=addslashes($_POST['compress']); else $compress=0;
 if(isset($_POST['export_choice'])) $export_choice=addslashes($_POST['export_choice']); else $export_choice=0;
 if(isset($_POST['mode'])) $mode=addslashes($_POST['mode']); else $mode=0;
 if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie='';

$date=date("Y-m-d");
switch ($export_choice)
    {
    case 0:  break;
    case 1:  $exp_type='Details'; break;
    case 12: $exp_type='IP'; break;
    default: break;
    }

@set_time_limit(2400);
$memory_limit=trim(@ini_get('memory_limit'));
// Setto 3 MB di base
if (empty($memory_limit)) $memory_limit=3*1024*1024;

    if (strtolower(substr($memory_limit,-1))=='m') $memory_limit=(int)substr($memory_limit,0,-1)*1024*1024;
    elseif (strtolower(substr($memory_limit,-1))=='k') $memory_limit=(int)substr($memory_limit,0,-1)*1024;
    elseif (strtolower(substr($memory_limit,-1))=='g') $memory_limit=(int)substr($memory_limit,0,-1)*1024*1024*1024;
    else $memory_limit=(int)$memory_limit;
    if ($memory_limit>1500000) $memory_limit-=1500000;
    $memory_limit*=2/3;

// Imposto il limite di guardia di 1/2 MB sotto il buffer
 $limit_memory_guard=$memory_limit-floor((1*1024*1024)/2);

// inclusioni principali delle funzioni esterne
require('config.php');
require('inc/main_func.inc.php');
require('inc/admin_func.inc.php');
if(isset($option['out_compress'])) if($option['out_compress']==1) if(phpversion()>'4.0.4') ob_start('ob_gzhandler');
if(!isset($option['prefix']) || $option['prefix']=='') $option['prefix']='php_stats';
// Connessione a MySQL e selezione database
db_connect();
// Leggo le variabili
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_array($result)) $option[$row[0]]=$row[1];
$modulo=explode("|",$option['moduli']);
// Controllo che l'utente abbia i permessi necessari altrimenti LOGIN
if($pass_cookie!=md5($option['admin_pass'])) { header("Location: $option[script_url]/admin.php?action=login"); die(); }
if ($compress==1) {
// @ob_start();
// @ob_implicit_flush(0);
header("Content-Type: application/x-gzip; name=\"Export-$exp_type-[$date].xls.gz\"");
header("Content-disposition: attachment; filename=Export-$exp_type-[$date].xls.gz");
}
else {
header("Content-Type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: attachment; filename=Export-$exp_type-[$date].xls");
}

// Inclusioni
if($modulo[7]) if($option['ip-zone']) include("lang/$option[language]/domains_lang.php");
include("./lang/$option[language]/main_lang.inc");
$data=date("Y-m-d",mktime(date("G")-$option['timezone'],date("i"),0,date("m"),date("d"),date("Y")));
$data=explode("-",$data);
$today=str_replace("%mount%", formatmount($data[1]),$varie['date_format']);
$today=str_replace("%day%",$data[2],$today);
$today=str_replace("%year%",$data[0],$today);
$gen_on=str_replace("%php-stats-ver%",$option['phpstats_ver'],$string['gen_on']);
$gen_on=str_replace("%data%",$today,$gen_on);

$data_write='';
$data_buffer='';
$data_buffer_lenght=0;

switch ($export_choice) {
    case 0:
           break;
    case 1:   // Details
include("lang/$option[language]/domains_lang.php");
include("lang/$option[language]/bw_lang.php");
//Array_Connessioni
$arrayTipeConn=Array(
'0'=>'',
'1'=>'Dial-Up',
'2'=>'ADSL',
'3'=>'Lan',
'4'=>'Wireline',
'5'=>'GPRS',
'6'=>'UMTS',
'7'=>'WI-FI');

$elenco0='';
$location='isp_db/';
$hook=opendir($location);
while(($file=readdir($hook)) !== false){
        if($file != '.' && $file != '..'){
      $path=$location . '/' . $file;
          if(is_dir($path)) $elenco0[]=$file;
        }
}
closedir($hook);
// Fine lettura directory isp-db
$range_isp='|';
if(is_array($elenco0)){
  @natsort($elenco0);
  while(list($key, $val)=each($elenco0)){
     $val=chop($val);
     $range_isp.=$val.'|';
 }
}
if(isset($_POST['data_1'])) $data_1=$_POST['data_1'];
if(isset($_POST['data_2'])) $data_2=$_POST['data_2'];
$data_test_1=$data_test_2=true;

for ($a=0;$a<=2;++$a) {
if(trim($data_1[$a])=='') $data_test_1=false;
if(trim($data_2[$a])=='') $data_test_2=false;
}
$data_start=$data_end='';
if($data_test_1 && $data_test_2)
  {
  $data_start=mktime(0,0,0,$data_1[1],$data_1[0],$data_1[2]);
  $data_end=(mktime(0,0,0,$data_2[1],$data_2[0],$data_2[2]))+86399;
  }
$query_tot=sql_query("SELECT count(DISTINCT visitor_id) FROM $option[prefix]_details");
list($num_totale)=@mysql_fetch_row($query_tot);
if($num_totale>0) {
  $result=sql_query("SELECT visitor_id FROM $option[prefix]_details GROUP BY visitor_id ORDER BY date DESC");
  $closed=1;
  while($row=@mysql_fetch_array($result))
    {
    $count=0;
    list($details_id)=$row;
    if (($data_start!='')&&($data_end!='')) $result2=sql_query("SELECT ip,host,os,bw,lang,date,referer,currentPage,reso,colo,titlePage FROM $option[prefix]_details WHERE visitor_id='$details_id' AND date BETWEEN $data_start AND $data_end ORDER BY date ASC");
    else $result2=sql_query("SELECT ip,host,os,bw,lang,date,referer,currentPage,reso,colo,titlePage FROM $option[prefix]_details WHERE visitor_id='$details_id' ORDER BY date ASC");
    $visitor_pages=@mysql_num_rows($result2);
    while($row2=@mysql_fetch_array($result2))
      {
      list($details_ip,$details_host,$details_os,$details_bw,$details_language,$details_date,$details_referer,$details_currentPage,$details_reso,$details_colo,$details_titlePage)=$row2;
      if($count==0)
        {
        if($closed==0)
          {
          $data_write.="</table><table><tr></tr></table>";
          $closed=1;
          }
        $data_write.=
        "<table border=\"1\" bordercolor=\"#000000\">".
        "<tr><td align=\"center\" colspan=\"4\" bgcolor=\"#99CCFF\"><b>".formatdate($details_date)." - ".formattime($details_date)."</b></td>".
        "<tr><td bgcolor=\"#CCCCCC\"><center>$string[details_os]</center></td>".
        "<td bgcolor=\"#999999\"><center>$string[details_browser]</center></td>".
        "<td bgcolor=\"#CCCCCC\"><center>$string[details_reso]</center></td>".
        "<td bgcolor=\"#999999\"><center>$string[details_ip]</center></td>".
        "</tr>";

        // Corpo della tabella
        $viewReso=1;
        $rangeMacro='-Spider,Grabber-';
        $nome_bw=$details_bw;
        if(@strpos($rangeMacro,$details_os))
          {
          $nome_os=$details_os;
          $viewReso=0;
          }
        else $nome_os=chop($details_os);

        $data_write.=
        "<tr><td bgcolor=\"#CCCCCC\"><center>".$nome_os."</center></td>".
        "<td bgcolor=\"#999999\"><center>".$nome_bw."</center></td>";
        $reso='';
        if($viewReso)
          {
          $reso=($details_reso=='' ? '?' : $details_reso);
          if($details_colo!='' && $details_colo!='?') $reso.=" $details_colo bit";
          }
        else $reso='N/A';
                $data_write.=
                "<td bgcolor=\"#CCCCCC\"><center>$reso</center></td>".
                "<td bgcolor=\"#999999\"><center>$details_ip</center></td>".
                "</tr>";
        write_file_dump();
                $count=0;
                $table='';
                // LINGUA DEL BROWSER
        if($bw_lang!='' && isset($bw_lang[$details_language]))  // Non visualizzo se non è settata la lingua
                  {
                  $table.="<tr><td bgcolor=\"#999999\"><center>$string[details_lang]</center></td><td colspan=\"3\" bgcolor=\"#CCCCCC\">".$lang=$bw_lang[$details_language]."</td></tr>";
                  ++$count;
                  }
        // PAESE
        if($option['ip-zone'] && $modulo[7]){
              $ip_number=sprintf('%u',ip2long($details_ip))-0;
              if(
                ($ip_number>=3232235520 && $ip_number<=3232301055) || //192.168.0.0 ... 192.168.255.255
                ($ip_number>=167772160 && $ip_number<=184549375) || //10.0.0.0 ... 10.255.255.255
                ($ip_number>=2886729728 && $ip_number<=2887778303) || //172.16.0.0 ... 172.31.255.255
                ($ip_number>=0 && $ip_number<=16777215) || //0.0.0.0 ... 0.255.255.255
                ($ip_number>=4026531840 && $ip_number<=4294967295) //240.0.0.0 ... 255.255.255.255
                ) $domain='lan';
                else if($option['ip-zone']==1)
                    {
                    $result3=sql_query("SELECT tld FROM $option[prefix]_ip_zone WHERE $ip_number BETWEEN ip_from AND ip_to");
                    if(@mysql_affected_rows()>0) list($domain)=@mysql_fetch_row($result3); else $domain='unknown';
                }
            else if($option['ip-zone']==2) $domain=getIP($ip_number,23,'ip-to-country.db','ip-to-country.idx',2);
          $domain2=$domain;
          $domain=$domain_name[$domain];
              $table.="<tr>";
                  $table.="<td bgcolor=\"#999999\"><center>$string[details_country]</center></td><td colspan=\"3\" bgcolor=\"#CCCCCC\">".$domain."</td>";
          $table.="</tr>";
          if(strpos($range_isp,$domain2)) {
          $isp_recognize=getIP($ip_number,148,$location.$domain2."/$domain2.db",$location.$domain2."/$domain2.idx",127);
          if($isp_recognize!='unknown') {
            $isp_recognize=rtrim($isp_recognize);
            list($conn_type,$isp_string,$isp_descr)=explode('|',$isp_recognize);
          $table.=
          "<tr>".
          "<td bgcolor=\"#999999\"><center>$string[details_isp]</center></span></td><td colspan=\"3\" bgcolor=\"#CCCCCC\">$isp_string</span></td>".
          "</tr>";
          if($conn_type!=0) $table.="<tr><td bgcolor=\"#999999\"><center>$string[details_connection]</center></span></td><td colspan=\"3\" bgcolor=\"#CCCCCC\">$arrayTipeConn[$conn_type]</td></tr>";
          if($isp_descr!='') $table.="<tr><td bgcolor=\"#999999\"><center>$string[details_descr]</center></span></td><td colspan=\"3\" bgcolor=\"#CCCCCC\">$isp_descr</td></tr>";
          } }
          }
                // HOST
                 if($details_host!='' && $details_host!=$details_ip) // Non visualizzo se HOST è vuoto
                  {
                  $table.=
                  "<tr>".
                  "<td bgcolor=\"#CCCCCC\"><center>$string[details_server]</center></td><td colspan=\"3\" bgcolor=\"#999999\">$details_host</td>".
                  "</tr>";
                  ++$count;
                  }
                // REFERER o MOTORE DI RICERCA
        if($details_referer!='')
          {
          $table.="<tr>";
          if(substr($details_referer,0,4)==='http')
                {
            $table.="<td bgcolor=\"#999999\"><center>$string[details_referer]</center></td><td colspan=\"3\" bgcolor=\"#CCCCCC\">";
                        $details_referer=htmlspecialchars($details_referer);
                    $table.=formaturl($details_referer,'',70,35,-30)."</td></tr>";
            }
                    else
                    {
            list($nome_motore,$domain,$query,$resultPage)=explode('|',$details_referer);
            $query=stripslashes($query); // \' -> '
            $motore_string=$nome_motore.' ('.$domain_name[$domain].')';
            if($resultPage>0) $motore_string.=', '.$string['se_page'].': '.$resultPage;
                        $table.=
                        "<td bgcolor=\"#999999\"><center>$string[details_referer]</center></td>".
                        "<td bgcolor=\"#CCCCCC\">$motore_string</td>".
                        "<td bgcolor=\"#999999\"><center>$string[details_query]</center></td>".
                        "<td bgcolor=\"#CCCCCC\">$query</td>".
                        "</tr>";
                        }
                  ++$count;
                  }
        if($count)
                  {
          $data_write.=$table;
          }
        $data_write.="<tr><td bgcolor=\"#BCBCD6\"><center>$string[details_ora]</center></td><td colspan=\"3\" bgcolor=\"#33CCFF\"><center>".str_replace("%VISITEDPAGES%",$visitor_pages,$string['details_pageviewed'])."</center></td></tr>";
                if(substr($details_currentPage,0,3)=="dwn") { $data_write.="DOWN!<br>";}
        $data_write.="<tr bgcolor=\"#BCBCD6\"><td bgcolor=\"#BCBCD6\"><center>".formattime($details_date)."</center></td><td colspan=\"3\" bgcolor=\"#33CCFF\">$details_currentPage</td></tr>";
        $closed=0;
        }
        else
        {
////////////////////////////////////////////
     $data_write.=check_details($details_date,$details_currentPage,$details_titlePage,$mode);
        $closed=0;
        }
      ++$count;
      }
    }
  if($closed==0) { $data_write.="</table><table><tr></tr></table>"; $closed=1; }
   }
write_file_dump();
empty_buffer();
break;
    case 12:
$query_tot=sql_query("SELECT * FROM $option[prefix]_ip");
$num_totale=@mysql_num_rows($query_tot);
if ($num_totale>0)
{
$data_write="<table border=\"1\" bordercolor=\"#000000\">";
$data_write.="<tr><td align=\"center\" bgcolor=\"#99CCFF\"><b>$string[ip]</b></td><td align=\"center\" bgcolor=\"#99CCFF\"><b>$string[ip_last_visit]</b></td><td align=\"center\" bgcolor=\"#99CCFF\"><b>$string[ip_hits]</b></td><td align=\"center\" bgcolor=\"#99CCFF\"><b>$string[ip_visite]</b></td></tr>";
  $result=sql_query("SELECT * FROM $option[prefix]_ip ORDER BY hits DESC");
  $counter=0;
  while($row=@mysql_fetch_array($result))
   {
   $data_write.="<tr><td align=\"center\" bgcolor=\"#CCCCCC\">".long2ip($row[0])."</td><td align=\"right\" bgcolor=\"#999999\">".formatdate($row[1])." - ".formattime($row[1])."</td><td align=\"right\" bgcolor=\"#CCCCCC\">$row[2]</td><td align=\"right\" bgcolor=\"#999999\">$row[3]</td></tr>";
   if ($counter==1000) { write_file_dump(); $counter=0; } else ++$counter;
   }
$data_write.="</table>";
write_file_dump();
empty_buffer();
}
break;
}

function check_details($time,$page,$title,$mode) {
global $option,$db,$string;
$return="<tr><td bgcolor=\"#BCBCD6\"><center>".formattime($time)."</center></td><td colspan=\"3\" bgcolor=\"#33CCFF\">";
                if(substr($page,0,3)==='dwn')
                  {
                  list($dummy,$id)=explode("|",$page);
                  $res_name=sql_query("SELECT nome,url FROM $option[prefix]_downloads WHERE id='$id' LIMIT 1");
                  if(@mysql_num_rows($res_name)>0)
                    {
                        list($name,$url)=@mysql_fetch_row($res_name);
                        $return.=str_replace("%NAME%",$name,$string['details_down']);
                        }
                        else
                        $return.=str_replace("%ID%",$id,$string['details_down']);;
                  }
                  elseif(substr($page,0,3)=='clk')
                  {
                  list($dummy,$id)=explode("|",$page);
                  $res_name=sql_query("SELECT nome,url FROM $option[prefix]_clicks WHERE id='$id' LIMIT 1");
                  if(@mysql_num_rows($res_name)>0)
                    {
                        list($name,$url)=@mysql_fetch_row($res_name);
                        $return.=str_replace("%NAME%",$name,$string['details_click']);
                        }
                        else
                        $return.=str_replace("%ID%",$id,$string['details_click']);;
                  }
                  else
         // $return.=formaturl($page,"",70,35,-30);
          $return.=$page;
          $return.="</td></tr>";
          return($return);
                  }

function write_file_dump()
{
 global $data_buffer,$limit_memory_guard,$compress,$data_write;
 $data_buffer.=$data_write;
 $data_write='';
 $data_buffer_lenght=strlen($data_buffer);
 if ($data_buffer_lenght>$limit_memory_guard) {
   if ($compress) $data_buffer=gzencode($data_buffer);
   echo $data_buffer;
   flush();
 $data_buffer='';
 $data_buffer_lenght=0;
 }
 return TRUE;
}

function empty_buffer()
{
 global $data_buffer,$compress;
  if ($compress) $data_buffer=gzencode($data_buffer);
  echo $data_buffer;
 return TRUE;
}

// Chiusura connessione a MySQL se necessario
if($option['persistent_conn']!=1) mysql_close();
?>