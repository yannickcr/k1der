<?
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die("Php-Stats internal file.");

// Troncatura URL //
function formaturl($url, $title='', $maxwidth=60, $width1=15, $width2=-20, $link_title='', $mode=0){
  global $option,$short_url,$style;
  $iconType='None';

  if(trim($title)==='') $title='?'; //titolo pagina
  $longurl=(preg_match("/[a-z]:\/\//si", $url) ? $url : "http://$url"); //url lunga (per i link)

  $tmp=explode("\n",$option['server_url']);
  for($i=0,$tot=count($tmp);$i<$tot;++$i)
    {
    $server=trim($tmp[$i]);
    if($server=='') continue;
    if(strpos($url,$server)!==0) continue;
    if($option['short_url']) $url=str_replace($server,'',$url);//troncatura url
    if(strpos($url,'.swf?page=')>0) $iconType='Flash';
    else $iconType='Home';
    break;
    }

  if($iconType=='None' && ((strpos($longurl,'http://www.google')===0) || (strpos($longurl,'http://google')===0))) $iconType='Google';

  if($url=='') $url='/';

  switch($iconType){
    default:
    case 'None': $icon=''; break;
    case 'Home': $icon="<img src=\"templates/$option[template]/images/icon_home.gif\" border=\"0\">"; break;
    case 'Flash': $icon="<img src=\"templates/$option[template]/images/icon_flash.gif\" border=\"0\">"; break;
    case 'Google': $icon="<img src=\"templates/$option[template]/images/icon_google.gif\" border=\"0\">"; break;
  }

  switch($mode){
    default:
    case 0://visualizza url
      $linktext=(strlen($url)>$maxwidth ? substr($url,0,$width1).'...'.substr($url,$width2) : $url);
      break;
    case 1://visualizza titolo
      $linktext=(strlen($title)>$maxwidth ? substr($title,0,$width1).'...'.substr($title,$width2) : $title);
      break;
    case 2://visualizza titolo (url)
      $maxwidth-=3;//considero lo spazio e le parentesi

      $pos=strpos($url,'?');//cerco la query string
      if($pos!==FALSE) $url=substr($url,0,$pos);//taglio la query string

      $titlelength=strlen($title);
      $urllength=strlen($url);
      if($titlelength+$urllength>$maxwidth){//controllo se titolo e url sono più lunghe di maxwidth
        $tmp=floor($maxwidth/3);
        if($titlelength<$tmp*2){//uso lo spazio risparmiato per l'url
          $tmp=$maxwidth-$titlelength;//spazio disponibile per url
          $width1=floor(($tmp-3)/2);
          $width2=-$width1;
          $url=substr($url,0,$width1).'...'.substr($url,$width2);
        }
        else if($urllength<$tmp){//uso lo spazio risparmiato per il title
          $tmp=$maxwidth-$urllength;//spazio disponibile per title
          $width1=floor(($tmp-3)/2);
          $width2=-$width1;
          $title=substr($title,0,$width1).'...'.substr($title,$width2);
        }
        else{//title 2/3 di spazio, url 1/3 di spazio
          $width1=floor(($tmp-3)/2);
          $width2=-$width1;
          $url=substr($url,0,$width1).'...'.substr($url,$width2);
          $width1=floor($tmp-3);//($tmp-3)*2/2
          $width2=-$width1;
          $title=substr($title,0,$width1).'...'.substr($title,$width2);
        }
      }
      $linktext="$title ($url)";
  }
  return "<a href=\"$longurl\" title=\"$link_title\" target=\"_blank\">$icon ".str_replace('\\"', '"', $linktext)."</a>";
}

// Formattazione mese //
function formatmount($mount,$mode=0){ // 0 -> MESE NORMALE 1 -> MESE ABBREVIATO
global $varie;
return($mode==0 ? $varie['mounts'][$mount-1] : $varie['mounts_1'][$mount-1]);
}

// Formattazione ora //
function formattime($time){
return($time!=0 ? date("H:i:s",$time) : "");
}

function formatdate($date,$mode=0){
   global $varie;
   $mode=$mode-0;
   if($date==0) return '';
   switch($mode)
   {
    case 0:
       list($date_n,$date_j,$date_Y)=explode('-',date('n-j-Y',$date));
       return str_replace(Array('%mount%','%day%','%year%'),Array(formatmount($date_n),$date_j,$date_Y),$varie['date_format']);
    case 1:
       list($anno,$mese)=explode('-',$date);
       return str_replace(Array('%mount%','%year%'),Array(formatmount($mese),$anno),$varie['date_format_2']);
    default:
    case 2:
       list($date_m,$date_d,$date_y)=explode('-',date('m-d-y',$date));
       return str_replace(Array('%mount%','%day%','%year%'),Array($date_m,$date_d,$date_y),$varie['date_format_3']);
   }
}

function formatperm($value,$mode=1){
global $varie;
$value=round($value,0);
if($mode==1)
  {
  $minuti=floor($value/60);
  $secondi=$value-($minuti*60);
  if($secondi<10) $secondi='0'.$secondi;
  if($minuti<10) $minuti='0'.$minuti;
  return str_replace(Array('%minutes%','%seconds%'),Array($minuti,$secondi),$varie['perm_format']);
  }

$ore=floor($value/3600);
$value=$value-($ore*3600);
$minuti=floor($value/60);
$secondi=$value-($minuti*60);
if($ore<10) $ore='0'.$ore;
if($secondi<10) $secondi='0'.$secondi;
if($minuti<10) $minuti='0'.$minuti;
return str_replace(Array('%hours%','%minutes%','%seconds%'),Array($ore,$minuti,$secondi),$varie['perm_format_2']);
}


// Verifica l'esistenza di un file sul server
function checkfile($url) {
global $string,$option;
$url=chop($url);
$url=str_replace(' ','%20',$url);
$check=false;
$check=@fopen($url,'r');
if($check==false) return("<img src=\"templates/$option[template]/images/icon_bullet_red.gif\" title=\"$string[link_broken]\">");
             else return("<img src=\"templates/$option[template]/images/icon_bullet_green.gif\" title=\"$string[link_ok]\">");
}

// Funzione per check dei campi (0=numerico,1=alfanumerico)
function checktext($campo,$mode=0)
{
$ok=0;
if($mode==0) $car_permessi="_1234567890"; else $car_permessi="_abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789_";
$str_lenght=strlen($campo);
for ($i=0;$i<=$str_lenght;++$i)
  {
  $str_temp=substr($campo,$i-1,1);
  $chk=(strpos($car_permessi,$str_temp) ? strpos($car_permessi,$str_temp)+1 : 0);
  if($chk==0) $ok=1;
  }
return($ok);
}

// Prepara l'HTML dal template
function gettemplate($template) {
$file=file($template);
$template=implode('',$file);
$template=str_replace('"','',$template);
return $template;
}

// CREA INFOBOX
function info_box($title,$body,$width=200,$cellspacing=10) {
global $style;
$return =
"<br><br><table border=\"0\" $style[table_header] width=\"$width\">".
"<tr><td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\">$title</span></td>".
"<tr><td align=\"center\" valign=\"middle\" bgcolor=$style[table_bgcolor] nowrap>".
"<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"$cellspacing\" border=\"0\"><tr><td align=\"center\" valign=\"middle\"  nowrap>".
"<span class=\"tabletextA\">$body</span></td></tr>".
"</td></tr></table>".
"<tr><td height=\"1\"bgcolor=$style[table_title_bgcolor] nowrap></td></tr>".
"</table>";
return($return);
}

function draw_table_title($titolo,$pagina='',$base_url='',$tables='',$q_sort='',$q_order='') {
global $option,$style;
$return="<td bgcolor=$style[table_title_bgcolor] nowrap>";
if($pagina==='')
  {
  $return.="<center><span class=\"tabletitle\">$titolo</span></center></td>";
  }
else
  {
  $return.="<a href=\"$base_url&sort=$pagina";
  if($q_sort==$tables["$pagina"]) $return.='&order='.($q_order==='ASC' ? '0' : '1');
  $return.=
  '">'.
  '<center><span class="tabletitle">';
  if($q_sort==$tables["$pagina"]) $return.="<img src=\"templates/$option[template]/images/".($q_order==='ASC' ? 'asc' : 'dsc').'_order.gif" border=0 align="middle"> ';
  $return.=$titolo.'</span></center></a></td>';
}
return($return);
}

// VISUALIZZA LA BARRA DELLA PAGINAZIONE
function pag_bar($base_url,$pagina_corrente,$numero_pagine,$rec_pag){
global $varie,$style;
  $return="\n\n<center><span class=\"tabletextA\">";
  if($pagina_corrente>1) $return.="\n<a href=\"$base_url&start=".(($pagina_corrente-2)*$rec_pag)."\">$varie[prev]</a>&nbsp&nbsp;";
  if($pagina_corrente>5 && $numero_pagine>6) $pi=$pagina_corrente-2; else $pi=1;
  if($pagina_corrente<($numero_pagine-3)) $pf=($numero_pagine>6 ? max(($pagina_corrente+2),6) : $numero_pagine);
  else $pf=$numero_pagine;
  if($pi>1) $return.="<a href=\"$base_url&start=0\">1</a>&nbsp;... ";
  for($pagina=$pi; $pagina<=$pf; ++$pagina)
    {
    if($pagina==$pagina_corrente) $return.="<b>$pagina</b> ";
                             else $return.="<a href=\"$base_url&start=".(($pagina-1)*$rec_pag)."\">".$pagina."</a>&nbsp;";
    }
  if(($numero_pagine-$pf)>0) $return.="... <a href=\"$base_url&start=".(($numero_pagine-1)*$rec_pag)."\">$numero_pagine</a>&nbsp;";
  if($pagina_corrente<$numero_pagine) $return.= "<a href=\"$base_url&start=".(($pagina_corrente)*$rec_pag)."\">&nbsp$varie[next]</a>";
  $return.='</span></center>';
return($return);
}

function create_option_file($create_staticJS='')
{
global $page_list;
if (!defined('__OPTIONS_FILE__')) define ('__OPTIONS_FILE__','option/php-stats-options.php');
if (!defined('__LOCK_FILE__')) define ('__LOCK_FILE__','option/options_lock.php');
if (!defined('__STATICJS_FILE__')) define ('__STATICJS_FILE__','php-stats.js');
unset($option);
@include('config.php');

// ARRAY ORDINATA DEI VALORI CHE NON DEVONO ESSERE SCRITTI
$noWrite=Array('inadm_last_update','instat_report_w','instat_max_online','inadm_upd_available');

// Valori da memorizzare in formato stringa
$stringValue=Array('host','database','user_db','pass_db','script_url','exc_pass','prefix','ext_whois','language','server_url','admin_pass','template','nomesito','user_mail','user_pass_new','user_pass_key','phpstats_ver','exc_fol','exc_sip','exc_dip');

$options_text='<?php
if(!defined(\'IN_PHPSTATS\')) die("Php-Stats internal file.");
ignore_user_abort(true);

$option=Array(
';

// Scrivo le variabili presenti in config.php
while (list ($key, $value) = each ($option))
   {
   switch ($key)
     {
     case 'script_url':
             if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' && substr($value,0,5)==='http:') $value='https:'.substr($value,5);
            if(substr($value,-1)==='/') $value=substr($value,0,-1);
        $options_text.="'$key'=>'$value',\n";
        break;
     default:
        $options_text.=(in_array($key,$stringValue) ? "'$key'=>'".addslashes($value)."',\n" : "'$key'=>$value,\n");
        break;
     }
    }
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_row($result))
  {
  if (!(in_array($row[0],$noWrite)))
    {
    switch ($row[0])
     {
     case 'server_url':
        $tmpServerUrl=explode("\n",$row[1]);
        $options_text.="'$row[0]'=>'$row[1]',\n";
        break;
     case 'unlock_pages':
        $tmpUnlockPages=explode('|',$row[1]);
        break;
     case 'moduli':
        $tmpModuli=explode('|',$row[1]);
        break;
     case 'exc_fol':
        $options_text.="'$row[0]'=>'$row[1]',\n";
        $tmpExc_fol=explode("\n",$row[1]);
        break;
     case 'exc_sip':
        $options_text.="'$row[0]'=>'$row[1]',\n";
        $tmpExc_sip=explode("\n",$row[1]);
        break;
     case 'exc_dip':
        $options_text.="'$row[0]'=>'$row[1]',\n";
        $tmpExc_dip=explode("\n",$row[1]);
        break;
     default:
        $options_text.=(in_array($row[0],$stringValue) ? "'$row[0]'=>'$row[1]',\n" : "'$row[0]'=>$row[1],\n");
        break;
     }
    }
  else array_shift($noWrite); // CON ARRAY_SHIFT AUMENTO LA VELOCITA' DI CREAZIONE DEL FILE
  }
$options_text=substr($options_text, 0, -2)."\n);\n\n";

$options_text.="\$modulo=Array(";
for($i=0,$tot=count($tmpModuli);$i<$tot-1;++$i) $options_text.="$tmpModuli[$i],";
$options_text=substr($options_text, 0, -1).");\n\n";

$options_text.="\$unlockedPages=Array(\n";
  if(in_array(1,$tmpUnlockPages)){
  for($i=0,$tot=count($tmpUnlockPages);$i<$tot-1;++$i) $options_text.=($tmpUnlockPages[$i]==1 ? "'$page_list[$i]',\n" : '');
  $options_text=substr($options_text, 0, -2)."\n);\n\n";
  }
  else $options_text.="''\n);\n\n";

$tot=count($tmpServerUrl);
if (($tot===1) && ($tmpServerUrl[0]=='')) $options_text.="\$countServerUrl=0;\n\n";
else {
     $options_text.="\$serverUrl=Array(\n";
     for($i=0;$i<$tot;++$i) $options_text.="'".($option['www_trunc'] ? str_replace('://www.','://',$tmpServerUrl[$i]) : $tmpServerUrl[$i])."',\n";
     $options_text=substr($options_text, 0, -2)."\n);\n\$countServerUrl=$tot;\n\n";
     }

$tot=count($tmpExc_fol);
if (($tot===1) && ($tmpExc_fol[0]=='')) $options_text.="\$countExcFol=0;\n\n";
else {
     $options_text.="\$excf=Array(\n";
     for($i=0;$i<$tot;++$i) $options_text.="'$tmpExc_fol[$i]',\n";
     $options_text=substr($options_text, 0, -1)."\n);\n\$countExcFol=$tot;\n\n";
     }

$tot=count($tmpExc_sip);
if (($tot===1) && ($tmpExc_sip[0]=='')) $options_text.="\$countExcSip=0;\n\n";
else {
     $options_text.="\$excsips=Array(\n";
     for($i=0,$tot=count($tmpExc_sip);$i<$tot;++$i) $options_text.="'$tmpExc_sip[$i]',\n";
     $options_text=substr($options_text, 0, -1)."\n);\n\$countExcSip=$tot;\n\n";
     }

$tot=count($tmpExc_dip);
if (($tot===1) && ($tmpExc_dip[0]=='')) $options_text.="\$countExcDip=0;\n\n";
else {
     $options_text.="\$excdips=Array(\n";
     for($i=0,$tot=count($tmpExc_dip);$i<$tot;++$i) $options_text.="'$tmpExc_dip[$i]',\n";
     $options_text=substr($options_text, 0, -1)."\n);\n\$countExcDip=$tot;\n\n";
     }

unset($tmpModuli,$tmpServerUrl,$tmpExc_fol,$tmpExc_sip,$tmpExc_dip);

// SCRIVO L'ARRAY DEFAULT PAGE
$options_text.="\$default_pages=Array(\n";
while (list ($key, $value) = each ($default_pages)) $options_text.="'$value',\n";
$options_text=substr($options_text, 0, -2).");\n?>";

// CREO IL FILE DI LOCK E FACCIO UNO SLEEP DI 1 SEC ALTRIMENTI NON VIENE MAI RILEVATO
$ok=@touch(__LOCK_FILE__);
if (!$ok) return($ok);
sleep(1);

// CREAZIONE FILE OPTIONS.PHP
$optionsFile=@fopen(__OPTIONS_FILE__, 'w+');
$ok=@fwrite($optionsFile,$options_text);
if (!$ok) return($ok);
fclose($optionsFile);

$ok=@unlink(__LOCK_FILE__);

if($create_staticJS!=''){
$createFile=TRUE;

if(file_exists(__STATICJS_FILE__))
  {
  $staticJSFile=@fopen(__STATICJS_FILE__,'r');
  if($staticJSFile)
    {
    $tmp=@fread($staticJSFile,5);
    fclose($staticJSFile);
    if(($tmp=='//cvi' && $option['callviaimg']) || ($tmp!='//cvi' && !$option['callviaimg'])) $createFile=FALSE;
    }
  }

if($createFile){
$jsstatic_text='
if(document.referrer) var f=document.referrer;
else var f=top.document.referrer;
f=escape(f);
f=f.replace(/&/g,"%A7%A7");
if((f=="null") || (f=="unknown") || (f=="undefined")) f="";
var w=screen.width;
var h=screen.height;
var rand=Math.round(100000*Math.random());
var browser=navigator.appName;
var t=escape(document.title);
var NS_url="";
if(browser!="Netscape") c=screen.colorDepth; else c=screen.pixelDepth;
NS_url=document.URL;
NS_url=escape(NS_url);
NS_url=NS_url.replace(/&/g,"%A7%A7");';

if($option['callviaimg'])
{
$jsstatic_text=
'//cvi
'.$jsstatic_text.'
var sc1="<img src=\''.$option['script_url'].'/php-stats.php?w="+w+"&amp;h="+h+"&amp;c="+c+"&amp;f="+f+"&amp;NS_url="+NS_url+"&amp;t="+t+"\' border=\'0\' alt=\'\' width=\'1\' height=\'1\'>";';
}
else
{
$jsstatic_text.=
'
sc1="<scr"+"ipt type=\'text/javascript\' src=\''.$option['script_url'].'/php-stats.php?w="+w+"&amp;h="+h+"&amp;c="+c+"&amp;f="+f+"&amp;NS_url="+NS_url+"&amp;t="+t+"\'></scr"+"ipt>";';
}

$jsstatic_text.='
document.write(sc1);';
$STATICJS_FILE='php-stats.js';
$staticJSFile=@fopen($STATICJS_FILE, 'w+');
if($staticJSFile){
  fwrite($staticJSFile,$jsstatic_text);
  fclose($staticJSFile);
  }
}
}
return($ok);
}

// PULIZIA DELLA CACHE E TRASFERIMENTO DATI DALLA CACHE AL DATABASE //
function clear_cache() {
global $option,$modulo;
$range_macro='-Spider,Grabber-';
$result=sql_query("SELECT user_id,visitor_id,hits,visits,reso,colo,os,bw,host,lang,giorno FROM $option[prefix]_cache");

if(@mysql_affected_rows()<1) return false; //nessun dato in cache

$option['ip-zone']-=0; //mi assicuro che l'opzione sia in formato numerico.

$date=time()-$option['timezone']*3600;

if($option['php_stats_safe']!=1) $append='LIMIT 1'; // MySQL 3.22 dont' have LIMIT in UPDATE select!!!!

while($row=@mysql_fetch_row($result))
  {
  list($Cuser_id,$Cvisitor_id,$Chits,$Cvisits,$Creso,$Ccolo,$Cos,$Cbw,$Chost,$Clang,$Cgiorno)=$row;

  if(($Chits==0) && ($Cvisits==0)) continue; //nessuna visita o hit rilevato
  $spider_agent=(strpos($range_macro,$Cos)==TRUE);

  // DEPURO DEI DATI IMMESSI NEL DATABASE PRINCIPALE
  sql_query("UPDATE $option[prefix]_cache SET hits='0',visits='0' WHERE visitor_id='$Cvisitor_id' $append");

  // determino il mese in formato AAAA-MM
  list($y,$m,$d)=explode('-',$Cgiorno);
  $mese_oggi=$y.'-'.$m;

  // SISTEMI (OS,BW,RESO,COLORS)
  if($modulo[1])
    {
    $clause="os='$Cos' AND bw='$Cbw' AND reso='$Creso' AND colo='$Ccolo'".(($modulo[1]==2) ? " AND mese='$mese_oggi'" : '');
    sql_query("UPDATE $option[prefix]_systems SET visits=visits+$Cvisits,hits=hits+$Chits WHERE $clause $append");
    if(@mysql_affected_rows()<1)
      {
      $insert="VALUES('$Cos','$Cbw','$Creso','$Ccolo','$Chits','$Cvisits','".(($modulo[1]==2) ? $mese_oggi : '')."')";
      sql_query("INSERT INTO $option[prefix]_systems $insert");
      }
    }

  // LINGUE (impostate dal browser)
  if(($spider_agent===false) && $modulo[2]){
    sql_query("UPDATE $option[prefix]_langs SET hits=hits+$Chits,visits=visits+$Cvisits WHERE lang='$Clang' $append");
    if(@mysql_affected_rows()<1) sql_query("UPDATE $option[prefix]_langs SET hits=hits+$Chits,visits=visits+$Cvisits WHERE lang='unknown' $append");
  }

  // ACCESSI GIORNALIERI
  if($modulo[6]){
    sql_query("UPDATE $option[prefix]_daily SET hits=hits+$Chits,visits=visits+$Cvisits".(($modulo[11] && $spider_agent) ? ",no_count_hits=no_count_hits+$Chits,no_count_visits=no_count_visits+$Cvisits" : '')." WHERE data='$Cgiorno' ".$append);
    if(@mysql_affected_rows()<1) sql_query("INSERT INTO $option[prefix]_daily VALUES('$Cgiorno','$Chits','$Cvisits'".(($modulo[11] && $spider_agent) ? ",'$Chits','$Cvisits'" : ",'0','0'").")");
  }

  // INIZIALIZZO L'IP UNA SOLA VOLTA PER TUTTE
  $ip_number=sprintf('%u',ip2long($Cuser_id));

  // COUNTRY
  if(($spider_agent===false) && $modulo[7])
    {
    if(
      ($ip_number>=3232235520 && $ip_number<=3232301055) || //192.168.0.0 ... 192.168.255.255
      ($ip_number>=167772160 && $ip_number<=184549375) || //10.0.0.0 ... 10.255.255.255
      ($ip_number>=2886729728 && $ip_number<=2887778303) || //172.16.0.0 ... 172.31.255.255
      ($ip_number>=0 && $ip_number<=16777215) || //0.0.0.0 ... 0.255.255.255
      ($ip_number>=4026531840 && $ip_number<=4294967295) //240.0.0.0 ... 255.255.255.255
      ) $domain='lan';
    else switch($option['ip-zone'])
      {
      case 0: //tramite host
        $domain='';
        $tmp=explode('.',$Chost);
        for($i=count($tmp)-1;$i>=0;--$i)
          {
          if(@!$tmp[$i]) continue; //esistono domini come 'google.com.'
          $domain=$tmp[$i];
          break;
          }
      break;
      case 1: //tramite ip2c MySQL
        $result2=sql_query("SELECT tld FROM $option[prefix]_ip_zone WHERE $ip_number BETWEEN ip_from AND ip_to");
        if(@mysql_affected_rows()>0) list($domain)=@mysql_fetch_row($result2);
        else $domain='unknown';
      break;
      case 2: //tramite ip2c file
        $domain=getIP($ip_number,23,'ip-to-country.db','ip-to-country.idx',2);
      break;
      }
    sql_query("UPDATE $option[prefix]_domains SET visits=visits+$Cvisits,hits=hits+$Chits WHERE tld='$domain' $append");
    if(@mysql_affected_rows()<1) sql_query("UPDATE $option[prefix]_domains SET visits=visits+$Cvisits,hits=hits+$Chits WHERE tld='unknown' $append");
    }

  // INDIRIZZI IP
  if($modulo[10])
    {
    sql_query("UPDATE $option[prefix]_ip SET visits=visits+$Cvisits,hits=hits+$Chits,date='$date' WHERE ip='$ip_number' $append");
    if(@mysql_affected_rows()<1) sql_query("INSERT INTO $option[prefix]_ip VALUES('$ip_number','$date','$Chits','$Cvisits')");
    }
  }
  return true;
}

function size($file) {
  if(@!file_exists($file)) return 'N/A';
  $size = filesize($file);
  $sizes = Array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
  $ext = $sizes[0];
  for ($i=1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
   $size = $size / 1024;
   $ext  = $sizes[$i];
  }
  return round($size, 2).$ext;
}

function relative_path($absolute,$curpath){
   $file=basename($absolute);
   $absolute=dirname($absolute);
   if(substr($absolute,-1)!=='/') $absolute.='/';
   if(substr($curpath,-1)!=='/') $curpath.='/';
   if($absolute===$curpath) return $file; //sono la stessa directory
   if(strlen($absolute)>strlen($curpath)) return substr($absolute,strlen($curpath)).$file;
   else{
      $tmp=substr($curpath,strlen($absolute));
      $backdirs=substr_count($tmp,'/');
      $result='';
      for($i=0;$i<$backdirs;++$i) $result.='../';
      return $result.$file;
   }
}
?>