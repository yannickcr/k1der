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

/// DEFINIZIONE VARIABILI PRINCIPALI
define('IN_PHPSTATS',true);

// VARIABILI ESTERNE
                      if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
if(isset($_COOKIE['php_stats_esclusion'])) $php_stats_esclusion=$_COOKIE['php_stats_esclusion']; else $php_stats_esclusion=0;
                      if(!isset($_SERVER)) $_SERVER=$HTTP_SERVER_VARS;
                         if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
       if(isset($_GET['forcenoimg'])=='1') $forcenoimg=true; else $forcenoimg=false;
                      $NowritableServer=1;  // Nessun permesso di scrittura sul server

// Inclusioni necessarie
@require('config.php');

if($option['callviaimg'] && ($forcenoimg===false))
  {
  // Immagine fittizia 1 pixel x 1 pixel trasparente
  header('Content-Type: image/gif');
  echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
  @flush();
  }
// Controllo esclusione tramite cookie prima per evitare operazioni inutili
if($php_stats_esclusion=='1') exit();

do{
// Verifico subito se lo script � stato richiamato da un date corretto o da una pagina memorizzata
$date=0;
$dateTest=time()-$option['timezone']*3600;
if(isset($_GET['date'])) $date=$_GET['date'];
if($date>$dateTest && $date<($dateTest-300)) exit(); // Verifica validit� data e richiamo entro 5 min per evitare le pag. salvate

@require('inc/main_func_stats.inc.php');

ignore_user_abort(true);
if($option['stats_disabled']) exit(); // Statistiche attive?

// VERIFICO CHE SIANO ARRIVATE IN GET LE VAR IP E VISITOR_ID ALTRIMENTI BLOCCO L'ESECUZIONE
if(isset($_GET['ip'])) $ip=urldecode($_GET['ip']); else exit();
if(!ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$',$ip)) exit();
// SI PUO' AGGIUNGERE UN CONTROLLO PER VEDERE SE L'IP CHIAMATO E' LO STESSO DEL CLIENT MA FORSE E' SUPERFLUO

if(isset($_GET['visitor_id'])) $visitor_id=strtolower(urldecode($_GET['visitor_id'])); else exit();
if((!eregi('^[0-9,a-z]{32}',$visitor_id)) && (strlen($visitor_id)<>32)) exit();

$s=urldecode(urlencode('��')); //Codifica e decodifica del sepatarore

$title='?';
if($option['page_title'] && isset($_GET['t']))
  {
  $tmpTitle=htmlspecialchars(addslashes(urldecode($_GET['t'])));
  if($tmpTitle!='\\\\\\&quot; t \\\\\\&quot;') $title=$tmpTitle;
  }

if(!isset($option['prefix'])) $option['prefix']='php_stats';
$append=$append_2='';
if(!$option['php_stats_safe']) {
  $append='LIMIT 1'; // MySQL 3.22 dont' have LIMIT in UPDATE select!!!!
  $append_2='LIMIT 2'; // append per safe mode
}

// Compressione buffer
if($option['out_compress']) ob_start('ob_gzhandler');
// Connessione a MySQL e selezione database
db_connect();
// Lettura variabili
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_row($result)) $option[$row[0]]=$row[1];
if($option['stats_disabled']) break 2; // Statistiche attive?
$modulo=explode('|',$option['moduli']); // Leggo i moduli da attivare

// PREPARARO VARIABILI
// 1STEP QUESTA PORZIONE HA POCA POSSIBILITA' DI ESSERE ESEGUITA QUINDI INFICIA POCO LE PERFORMANCE
$loaded='?';
if(isset($_GET['NS_url'])) {
  $tmp=htmlspecialchars(addslashes(urldecode($_GET['NS_url'])));
  if($tmp!='' && !strpos($tmp,'NS_url')) $loaded=str_replace($s,'&',$tmp); // Pagina visualizzata

  if($loaded!='?') {
    $loadedLC=strtolower($loaded);
    if($option['lock_not_valid_url']) {
       $serverUrl=explode("\n",$option['server_url']);
       for($i=0,$tot=count($option['server_url']);$i<$tot;++$i) {
          if(strpos($loadedLC,$serverUrl[$i])===TRUE) break 2;
      }
    }
    // ESCLUSIONE CARTELLE e URL By aVaTaR feature theCAS
    if($option['exc_fol']!=='') {
      $excf=explode("\n",$option['exc_fol']);
      for($i=0,$countExcFol=count($excf);$i<$countExcFol;++$i) {
         if(strpos($loadedLC,$excf[$i])!==FALSE) break 2; //strpos in stripos
      }
    }
    if($option['www_trunc']) if(strtolower(substr($loaded,0,11))=='http://www.') $loaded='http://'.substr($loaded,11);
    $tmp='/'.strtolower(basename($loaded));
    if(in_array($tmp,$default_pages)) $loaded=substr($loaded,0,-strlen($tmp));
    $loaded=filter_urlvar($loaded,'sid'); // ELIMINO VARIABILI SPECIFICHE NELLE PAGINE VISITATE (esempio il session-id)
  }
}
// FINE 1STEP

if($loaded!='?' && !ereg('^http://[[:alnum:]._-]{2,}',$loaded)) $loaded='?';

list($date_Y,$date_m,$date_d,$date_G)=explode('-',date('Y-m-d-G',$date));
$mese_oggi=$date_Y.'-'.$date_m; // Y-m
$data_oggi=$mese_oggi.'-'.$date_d; // Y-m-d
$ora=$date_G;

  // Ricavo tutte le variabili che in precedenza il php non ha ricavato
    $c=$reso='?';
        if(isset($_GET['c'])) {
          $tmp=htmlspecialchars(addslashes(urldecode($_GET['c'])));
          if(!strpos($tmp,'c')) $c=$tmp;
        }
        if(isset($_GET['w']) && isset($_GET['h'])) {
          $w=htmlspecialchars(addslashes(urldecode($_GET['w'])));
          $h=htmlspecialchars(addslashes(urldecode($_GET['h'])));
          if(!strpos($w,'w') && !strpos($h,'h')) $reso=$w.'x'.$h;
        }
   $TmpSqlCache=$TmpSqlDetails=$SqlCache='';
   if ($loaded!='?') $TmpSqlCache[]="lastpage='$loaded'";
   if (($c!='?') || ($reso!='?')) $TmpSqlCache[]=$TmpSqlDetails[]="reso='$reso',colo='$c'";
   if(is_array($TmpSqlCache)){
     $SqlCache=implode(',',$TmpSqlCache);
     if($SqlCache!='')
       sql_query("UPDATE $option[prefix]_cache SET $SqlCache WHERE user_id='$ip' AND visitor_id='$visitor_id' $append");
   }

//////////////////////////////////////////////////////////
// DATI NON SALVATI IN CACHE E CONTINUAMENTE AGGIORNATI //
//////////////////////////////////////////////////////////

// VERIFICO SE DEBBO PRELEVARE L'URL DEL TITOLO DI PAGINA
  if (($loaded=='?') && ($title!='?'))
    {
    $appendDetails='';
    $result=sql_query("SELECT lastpage FROM $option[prefix]_cache WHERE user_id='$ip' AND visitor_id='$visitor_id' LIMIT 1");
    if(mysql_affected_rows()>0)
      {
      list($loaded)=@mysql_fetch_row($result);
      $appendDetails="AND currentPage='$loaded'";
      }
    }

// SCRIVO LA PAGINA VISUALIZZATA
if(($modulo[3]) && ($title!=''))
  sql_query("UPDATE $option[prefix]_pages SET titlePage='$title' WHERE data='$loaded' $append");

// PREPARO REFERER
$reffer=$details_referer='';
if(isset($_GET['f']) && $_GET['f']!='') {
  $tmpreffer=htmlspecialchars(addslashes($_GET['f']));
  if(!is_internal($tmpreffer)) { // && ($is_uniqe || $option['full_recn']))
    $tmpreffer=str_replace($s,'&',$tmpreffer);
        if(!strpos($tmpreffer,'reffer')) $reffer=filter_urlvar($tmpreffer,'sid'); // ELIMINO VARIABILI SPECIFICHE NEI REFERER (esempio il session-id)
  }

if($reffer!='' && !ereg('^http://[[:alnum:]._-]{2,}',$reffer)) $reffer='';

  // SCRIVO I MOTORI DI RICERCA, QUERY e REFERER
  if($modulo[4]) {
    if($reffer!=''){
          if(substr($reffer,-1)==='/') $reffer=substr($reffer,0,-1);
      $engineResult=getengine($reffer);
          if($engineResult!==FALSE) {
            list($nome_motore,$domain,$query,$resultPage)=$engineResult;
        $details_referer=implode('|',$engineResult).'|'.urldecode($reffer);
            // MOTORI DI RICERCA E QUERY
            $clause="data='$query' AND engine='$nome_motore' AND domain='$domain' AND page='$resultPage'"; if($modulo[4]==2) $clause.=" AND mese='$mese_oggi'";
            sql_query("UPDATE $option[prefix]_query SET visits=visits+1, date='$date' WHERE $clause $append");

        if(mysql_affected_rows()<1) {
              $insert="VALUES('$query','$nome_motore','$domain','$resultPage','1','$date','"; if($modulo[4]==2) $insert.="$mese_oggi"; $insert.="')";
                  sql_query("INSERT INTO $option[prefix]_query $insert");
                  if($option['prune_3_on']) prune("$option[prefix]_query",$option['prune_3_value']);
            }
          }
    else { // REFERERS
         $reffer_dec=urldecode($reffer);
         $details_referer=$reffer_dec;
                 $clause="data='$reffer_dec'"; if($modulo[4]==2) $clause.=" AND mese='$mese_oggi'";
                 sql_query("UPDATE $option[prefix]_referer SET visits=visits+1,date='$date' WHERE $clause $append");
                 if(mysql_affected_rows()<1) {
                   $insert="VALUES('$reffer_dec','1','$date','";        if($modulo[4]==2) $insert.="$mese_oggi"; $insert.="')";
                   sql_query("INSERT INTO $option[prefix]_referer $insert");
                 }
                 if($option['prune_5_on']) prune("$option[prefix]_referer",$option['prune_5_value']);
                 }
    }
  }
}
// SCRIVO I DETTAGLI
if($modulo[0]) {
   if($reffer!='') $TmpSqlDetails[]="referer='$details_referer'";
   if($loaded!='?') { $TmpSqlDetails[]="currentPage='$loaded'"; $append_2=($appendDetails==='' ? "AND currentPage='?'" : ''); }
   if($title!='?') $TmpSqlDetails[]="titlePage='$title'";

   $SqlDetails=implode(',',$TmpSqlDetails);
   if($SqlDetails!='')
      sql_query("UPDATE $option[prefix]_details SET $SqlDetails WHERE visitor_id='$visitor_id' AND ip='$ip' $appendDetails $append_2");
}

}while(0);//FINE OPERAZIONI OPZIONALI
// Chiusura connessione a MySQL se necessario
if(!$option['persistent_conn']) mysql_close();
unset($option);
?>