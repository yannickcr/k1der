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

// DEFINIZIONE VARIABILI PRINCIPALI
define('IN_PHPSTATS',true);
define ('__OPTIONS_FILE__','option/php-stats-options.php');
define ('__LOCK_FILE__','option/options_lock.php');

// VARIABILI ESTERNE
                        if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
  if(isset($_COOKIE['php_stats_esclusion'])) $php_stats_esclusion=$_COOKIE['php_stats_esclusion']; else $php_stats_esclusion='';
                        if(!isset($_SERVER)) $_SERVER=$HTTP_SERVER_VARS;
          if(isset($_SERVER['REMOTE_ADDR'])) $ip=(isset($_SERVER['HTTP_PC_REMOTE_ADDR']) ? $_SERVER['HTTP_PC_REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR']);
if(isset($_SERVER['HTTP_REFERER']) && !$_GET['flashmode']=='ok') $HTTP_REFERER=$_SERVER['HTTP_REFERER'];
                           if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
                        $NowritableServer=0;  // Permesso di scrittura sul server

// Verifica presenza del file di opzioni libero altrimenti aspetto ed inclusione funzioni principali
if (file_exists(__LOCK_FILE__)) sleep(2);
@require(__OPTIONS_FILE__);

// Compressione buffer
if($option['out_compress']) ob_start('ob_gzhandler');

// Se necessario invio subito l'immagine così posso effettuare gli exit() in qualunque momento
if($option['callviaimg'])
  {
  // Immagine fittizia 1 pixel x 1 pixel trasparente
  header('Content-Type: image/gif');
  echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
  @flush();
  }
// Controllo esclusione tramite cookie prima per evitare operazioni inutili
if(strpos($php_stats_esclusion,"|$option[script_url]|")!==FALSE) exit();

@require('inc/main_func_stats.inc.php');

if($option['stats_disabled']) exit(); // Statistiche attive?

// DETERMINO L'IP DI UTENTI DIETRO PROXY
if(isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']))
  {
  $real_ip=$HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
  if(ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$',$real_ip)) $ip=$real_ip; // Se l'IP è valido è l'IP reale dell'utente
  }
$s=urldecode(urlencode('§§')); //Codifica e decodifica del sepatarore

$title='?';
if($option['page_title'] && isset($_GET['t']))
  {
  $tmpTitle=htmlspecialchars(addslashes(urldecode($_GET['t'])));
  if($tmpTitle!='\\\\\\&quot; t \\\\\\&quot;') $title=$tmpTitle;
  }

//EXCLUSION MoD By aVaTaR feature theCAS
// ESCLUSIONE SIP By aVaTaR feature theCAS
$nip=sprintf('%u',ip2long($ip))-0;

for($i=0;$i<$countExcSip;++$i)
  {
  $from=substr($excsips[$i],0,10)-0;
  $to=substr($excsips[$i],10)-0;
  if($from<=$nip && $nip<=$to) exit(); //esclusione
  }

// ESCLUSIONE DIP By aVaTaR feature theCAS
for($i=0;$i<$countExcDip;++$i)
  {
  $exdip=substr($excdips[$i],2)-0;
  if($exdip===$nip) exit();
  }

// PREPARARO VARIABILI
$loaded='?';
if(isset($HTTP_REFERER)) $loaded=htmlspecialchars(addslashes($HTTP_REFERER));
else {
     if(isset($_GET['NS_url']))
       {
       $tmp=htmlspecialchars(addslashes(urldecode($_GET['NS_url'])));
       if($tmp!='' && strpos($tmp,'NS_url')===FALSE) $loaded=str_replace($s,'&',$tmp); // Pagina visualizzata
       }
     }

if($loaded!='?')
  {
  $loadedLC=strtolower($loaded);
  // BLOCCO URL NON VALIDI
  if($option['lock_not_valid_url'])
    {
    $lock_url=true;
    $loadedLCTemp=($option['www_trunc'] ? str_replace('://www.','://',$loadedLC) : $loadedLC);
    for($i=0;$i<$countServerUrl;++$i){
      if(strpos($loadedLCTemp,$serverUrl[$i])===0) { $lock_url=false; break; }
      }
    if($lock_url===true) exit();
    }
  // ESCLUSIONE CARTELLE e URL By aVaTaR feature theCAS
  for($i=0;$i<$countExcFol;++$i)
    {
    if(@strpos($loadedLC,$excf[$i])!==FALSE) exit(); //strpos in stripos
    }
  if($option['www_trunc']) if(strtolower(substr($loaded,0,11))==='http://www.') $loaded='http://'.substr($loaded,11);
  $tmp='/'.basename($loadedLC);
  if (in_array($tmp, $default_pages)) $loaded=substr($loaded,0,-strlen($tmp));
  $loaded=filter_urlvar($loaded,'sid'); // ELIMINO VARIABILI SPECIFICHE NELLE PAGINE VISITATE (esempio il session-id)
  }

if($loaded!='?' && !ereg('^http://[[:alnum:]._-]{2,}',$loaded)) $loaded='?';

$date=time()-$option['timezone']*3600;
list($date_Y,$date_m,$date_d,$date_G)=explode('-',date('Y-m-d-G',$date));
$mese_oggi=$date_Y.'-'.$date_m; // Y-m
$data_oggi=$mese_oggi.'-'.$date_d; // Y-m-d
$ora=$date_G;

$secondi=$date-3600*$option['ip_timeout']; // CALCOLO LA SCADENZA DELLA CACHE
/////////////////////////////////////////////////////////////////////////////////////////////
// VERIFICO SE L'IP E' PRESENTE NELLA CACHE: SE NECESSARIO LO INSERISCO OPPURE LO AGGIORNO //
/////////////////////////////////////////////////////////////////////////////////////////////
$cache_cleared=0; // Flag -> La cache ha subito una pulizia
    $do_update=0; // Flag -> Devo eseguire l'update della cache
    $do_insert=0; // Flag -> Devo eseguire l'inserimento nella cache

// MI PREPARO A CONNETTERMI A MYSQL
if(!isset($option['prefix'])) $option['prefix']='php_stats';
$append=($option['php_stats_safe'] ? '' : 'LIMIT 1'); // MySQL 3.22 dont' have LIMIT in UPDATE select!!!!

db_connect();   // CONNESSIONE MYSQL NEL CASO DI OPTIONS DA FILE

// Riconoscimento immediato agent per evitare operazioni inutili con spider e per poterli raggruppare
$HTTP_USER_AGENT=$nome_os=$nome_bw='?';
if(isset($_SERVER['HTTP_USER_AGENT'])){
  $tmp=htmlspecialchars(addslashes($_SERVER['HTTP_USER_AGENT']));
  $HTTP_USER_AGENT=str_replace(' ','',$tmp);
  }
$spider_agent=$ip_agent_cached=false;

$result=sql_query("SELECT data,lastpage,user_id,visitor_id,reso,colo,os,bw,giorno,level FROM $option[prefix]_cache WHERE user_id='$ip' LIMIT 1");
if(mysql_affected_rows()>0) $ip_agent_cached=true;
else{
    if($HTTP_USER_AGENT!='?'){
      $nome_bw=getbrowser($HTTP_USER_AGENT);
      if(strpos(__RANGE_MACRO__,$GLOBALS['cat_macro']))
        {
        $nome_os=$GLOBALS['cat_macro'];
        $spider_agent=true;
        $result=sql_query("SELECT data,lastpage,user_id,visitor_id,reso,colo,os,bw,giorno,level FROM $option[prefix]_cache WHERE os='$nome_os' AND bw='$nome_bw' LIMIT 1");
        if(mysql_affected_rows()>0) $ip_agent_cached=true;
        }
        else $nome_os=getos($HTTP_USER_AGENT);
      }
    }
if($ip_agent_cached)
  {
  list($last_page_time,$last_page_url,$user_id,$visitor_id,$reso,$c,$nome_os,$nome_bw,$giorno,$level)=@mysql_fetch_row($result);
  $ip=$user_id;
  if($spider_agent===false)  { if(strpos(__RANGE_MACRO__,$nome_os)) $spider_agent=true; }
  // Aggiornamento tempo di permanenza dell'ultima pagina
  if($modulo[3] && ($spider_agent===false))
    {
    $tmp=$date-$last_page_time;
    if($tmp<$option['page_timeout']) sql_query("UPDATE $option[prefix]_pages SET presence=presence+$tmp,tocount=tocount+1,date=$date WHERE data='$last_page_url' $append");
    }
  // VERIFICO SCADENZA PAGINA IN CASO DI IP IDENTICI
  if($last_page_time<$secondi)
    { // SCADUTO
    $cache_cleared=do_clear($visitor_id,1); // PULIZIA TOTALE
    $do_insert=1; // DEVO INSERIRE IL NUOVO VISITATORE
    }
  else
    { // NON SCADUTO
    if($data_oggi!=$giorno) // Controllo visite a cavallo di 2 giorni
      $cache_cleared=do_clear($visitor_id,0); // PULIZIA PARZIALE, NON CANCELLO
    $do_update=1; // Ma aggiorno sempre un dato non scaduto
    }
  }
  else $do_insert=1; // Se non trovo l'IP nella cache inserisco.

if($do_update) // AGGIORNAMENTO CACHE
    {
    sql_query("UPDATE $option[prefix]_cache SET data='$date',lastpage='$loaded',giorno='$data_oggi',hits=hits+1".($spider_agent ? '' : ',level=level+1')." WHERE user_id='$ip' $append");
    $is_uniqe=0;
    ++$level;
    $update_hv='hits=hits+1'.($spider_agent ? ',no_count_hits=no_count_hits+1' : '');
    }

if($do_insert) // INSERIMENTO DATI IN CACHE
  {
  $c=$reso=$lang='?';
  if(isset($_GET['c'])){
    $tmp=htmlspecialchars(addslashes(urldecode($_GET['c'])));
    if(strpos($tmp,'c')===FALSE) $c=$tmp;
    }
  if(isset($_GET['w']) && isset($_GET['h'])){
    $w=htmlspecialchars(addslashes(urldecode($_GET['w'])));
    $h=htmlspecialchars(addslashes(urldecode($_GET['h'])));
    if(strpos($w,'w')===FALSE && strpos($h,'h')===FALSE) $reso=$w.'x'.$h;
    }
  if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ($spider_agent===false)){
    $tmp=htmlspecialchars(addslashes($_SERVER['HTTP_ACCEPT_LANGUAGE']));
    $tmp=explode(',',$tmp);
    $lang=strtolower($tmp[0]);
    }
  if(($modulo[7] && $option['ip-zone']==0) || $option['log_host']) $host=gethostbyaddr($ip); else $host='';
  $visitor_id=md5(uniqid(rand(), true));
  sql_query("INSERT INTO $option[prefix]_cache (user_id,data,lastpage,visitor_id,hits,visits,reso,colo,os,bw,host,lang,giorno,level) VALUES('$ip','$date','$loaded','$visitor_id','1','1','$reso','$c','$nome_os','$nome_bw','$host','$lang','$data_oggi','1')");
  $is_uniqe=$level=1;
  $update_hv='hits=hits+1,visits=visits+1'.($spider_agent ? ',no_count_hits=no_count_hits+1,no_count_visits=no_count_visits+1' : '');
  }
//////////////////////////////////////////////////////////
// DATI NON SALVATI IN CACHE E CONTINUAMENTE AGGIORNATI //
//////////////////////////////////////////////////////////
// CONTATORI PRINCIPALI
sql_query("UPDATE $option[prefix]_counters SET $update_hv $append");
// SCRIVO LA PAGINA VISUALIZZATA
if($modulo[3]):
  $what='hits=hits+1'.($spider_agent ? ',no_count_hits=no_count_hits+1' : '');
  if($level<7 && ($spider_agent===false)) $what.=', lev_'.$level.'=lev_'.$level.'+1';
  sql_query("UPDATE $option[prefix]_pages SET $what,date='$date' WHERE data='$loaded' $append");
  if(@mysql_affected_rows()<1)
    {
    $lev_1=$lev_2=$lev_3=$lev_4=$lev_5=$lev_6=0;
    if($level<7 && ($spider_agent===false)) eval('$lev_'.$level.'=1;');
       sql_query("INSERT INTO $option[prefix]_pages (data,hits,visits,no_count_hits,no_count_visits,presence,tocount,date,lev_1,lev_2,lev_3,lev_4,lev_5,lev_6,outs,titlePage) VALUES('$loaded','1','$is_uniqe',".($spider_agent ? "'1','$is_uniqe'" : "'0','0'").",'0','0','$date','$lev_1','$lev_2','$lev_3','$lev_4','$lev_5','$lev_6','0','$title')");
    }
  if($option['prune_4_on']) prune("$option[prefix]_pages",$option['prune_4_value']);
endif;
// PREPARO REFERER
$reffer=$details_referer='';
if(isset($_GET['f']) && $_GET['f']!='')
  {
  $tmpreffer=trim(htmlspecialchars(addslashes($_GET['f'])));
  $internal_url=FALSE;
  if(is_internal($tmpreffer)) { $internal_url=TRUE; $option['full_recn']=0; }
  if($internal_url===FALSE && ($is_uniqe || $option['full_recn']))
    {
    $tmpreffer=str_replace($s,'&',$tmpreffer);
    if(strpos($tmpreffer,'reffer')===FALSE) $reffer=filter_urlvar($tmpreffer,'sid'); // ELIMINO VARIABILI SPECIFICHE NEI REFERER (esempio il session-id)
    }
  }

if($reffer!='' && !ereg('^http://[[:alnum:]._-]{2,}',$reffer)) $reffer='';

// SCRIVO I MOTORI DI RICERCA, QUERY e REFERER
if($modulo[4])
  {
  if($reffer!=''){
  if($do_insert || $option['full_recn'])
    {
    if(substr($reffer,-1)==='/') $reffer=substr($reffer,0,-1);
    $engineResult=getengine($reffer);
    if($engineResult!==FALSE)
      {
      list($nome_motore,$domain,$query,$resultPage)=$engineResult;
      $details_referer=implode('|',$engineResult).'|'.addslashes(urldecode($reffer));

      // MOTORI DI RICERCA E QUERY
      $clause="data='$query' AND engine='$nome_motore' AND domain='$domain' AND page='$resultPage'"; if($modulo[4]==2) $clause.=" AND mese='$mese_oggi'";
      sql_query("UPDATE $option[prefix]_query SET visits=visits+1, date='$date' WHERE $clause $append");
      if(@mysql_affected_rows()<1)
        {
        $insert="(data,engine,domain,page,visits,date,mese) VALUES('$query','$nome_motore','$domain','$resultPage','1','$date','".($modulo[4]==2 ? "$mese_oggi" : '')."')";
        sql_query("INSERT INTO $option[prefix]_query $insert");
        if($option['prune_3_on']) prune("$option[prefix]_query",$option['prune_3_value']);
        }
      }
    else// REFERERS
      {
      $reffer_dec=addslashes(urldecode($reffer));
      $details_referer=$reffer_dec;
      $clause="data='$reffer_dec'"; if($modulo[4]==2) $clause.=" AND mese='$mese_oggi'";
      sql_query("UPDATE $option[prefix]_referer SET visits=visits+1,date='$date' WHERE $clause $append");
      if(@mysql_affected_rows()<1)
        {
        $insert="(data,visits,date,mese) VALUES('$reffer_dec','1','$date','".($modulo[4]==2 ? "$mese_oggi" : '')."')";
        sql_query("INSERT INTO $option[prefix]_referer $insert");
        }
      if($option['prune_5_on']) prune("$option[prefix]_referer",$option['prune_5_value']);
      }
    }
  }
  }
// SCRIVO I DETTAGLI
if($modulo[0]):
  if($is_uniqe) $what="'$visitor_id','$ip','$host','$nome_bw','$nome_os','$lang','$date','$details_referer','$loaded','$reso','$c','$title'";
           else $what="'$visitor_id','$ip','','','','','$date','','$loaded','','','$title'";
  sql_query("INSERT INTO $option[prefix]_details (visitor_id,ip,host,bw,os,lang,date,referer,currentPage,reso,colo,titlePage) VALUES ($what)");
  if($option['prune_0_on']) { $limit=$option['prune_0_value']*3600; $secondi2=$date-$limit; sql_query("DELETE FROM $option[prefix]_details WHERE date<$secondi2 LIMIT 2"); }
  if($option['prune_1_on']) prune_details("$option[prefix]_details",$option['prune_1_value']);
endif;
// ACCESSI ORARI
if($modulo[5]){
  $clause="data='$ora'"; if($modulo[5]==2) $clause.=" AND mese='$mese_oggi'";
  sql_query("UPDATE $option[prefix]_hourly SET $update_hv WHERE $clause $append");
  if(@mysql_affected_rows()<1)
    {
    $insert="(data,hits,visits,no_count_hits,no_count_visits,mese) VALUES('$ora','1','$is_uniqe',".($spider_agent ? "'1','$is_uniqe'" : "'0','0'").",'".($modulo[5]==2 ? "$mese_oggi" : '')."')";
    sql_query("INSERT INTO $option[prefix]_hourly $insert");
    }
}
if($modulo[3]==2 || $option['report_w_on'])
  {
  $result=sql_query("SELECT name,value FROM $option[prefix]_config WHERE name LIKE 'instat_%'");
  while($row=@mysql_fetch_row($result)) $option2[$row[0]]=$row[1];

  // Mi assicuro che il dato sia un integer
  $option2['instat_report_w']=intval($option2['instat_report_w']);
  }
// MAX UTENTI ON-LINE
if($modulo[3]==2){
  list($max_ol,$time_ol)=explode('|',$option2['instat_max_online']);
  $max_ol=@intval($max_ol); // IMPONGO LA CONVERSIONE AD INTERO
  if($option['online_timeout']==0) $tmp=$date-300; else $tmp=$date-$option['online_timeout']*60;
  $online=0;
  $result=sql_query("SELECT data FROM $option[prefix]_cache WHERE data>$tmp AND os NOT REGEXP 'Spider|Grabber'");
  if(mysql_affected_rows()>0) $online=@mysql_num_rows($result);
  if($online>$max_ol) sql_query("UPDATE $option[prefix]_config SET value='$online|$date' WHERE name='instat_max_online'");
}

// Se non l'ho fatto prima, se necessario pulisco, un dato in cache
if(!$cache_cleared) { do_clear(); $cache_cleared=1; }

// VERIFICO SE DEVO SPEDIRE L' E-MAIL CON IL PROMEMORIA DEGLI ACCESSI
if($option['report_w_on'] && $date>$option2['instat_report_w']) { @include('inc/report.inc.php'); report(); }

// OPTIMIZE TABLES
if($option['auto_optimize'])
  {
  if(!isset($hits)) list($hits)=@mysql_fetch_row(sql_query("SELECT hits FROM $option[prefix]_counters LIMIT 1"));
  if(($hits % $option['auto_opt_every'])==0):
    $query="OPTIMIZE TABLES $option[prefix]_cache";
    if($option['prune_1_on'] || $option['prune_0_on']) $query.=",$option[prefix]_details";
    if($option['prune_2_on']) $query.=",$option[prefix]_ip";
    if($option['prune_4_on']) $query.=",$option[prefix]_pages";
    if($option['prune_3_on']) $query.=",$option[prefix]_query";
    if($option['prune_5_on']) $query.=",$option[prefix]_referer";
    sql_query($query);
  endif;
  }

// Chiusura connessione a MySQL se necessario
if(!$option['persistent_conn']) mysql_close();
unset($option);
?>