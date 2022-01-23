<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die("Php-Stats internal file.");
//
function restore_hits_visits() {
global $db,$option,$string,$error,$varie,$style,$phpstats_title;
// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['calendar_title'];
// Titolo
$return="<span class=\"pagetitle\">Aggiornamento Pagine Visitate e Visitatori<br><br></span>";
$result=sql_query("SELECT value FROM $option[prefix]_config WHERE name='phpstats_ver'");
$row=mysql_fetch_array($result);
if ($row[0]!="0.1.8") $return.="<br><br><center><span class=\"testo\"><b>La versione attuale di Php-Stats non è la 0.1.8</b></span>";
else {
sql_query("UPDATE $option[prefix]_config SET value=1 WHERE name='stats_disabled'");
$result=sql_query("SELECT SUM(hits),SUM(visits) FROM $option[prefix]_daily");
list($hits_tot,$visite_tot)=@mysql_fetch_array($result);
if(!isset($hits_tot)) $hits_tot=0;
if(!isset($visite_tot)) $visite_tot=0;
sql_query("UPDATE $option[prefix]_counters SET hits=$hits_tot, visits=$visite_tot");
sql_query("UPDATE $option[prefix]_config SET value=0 WHERE name='stats_disabled'");
$return.="<br><br><center><span class=\"testo\"><b>Aggiornamento Effettuato</b></span>"; }
return($return);
}
?>