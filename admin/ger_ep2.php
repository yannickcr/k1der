<?
include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

function date2timestamp($date,$format){
// Paramètres : 
   //    $date : date formattée comme renvoie date()
   //    $format : format de la date similire au paramètre de date()
/* exemple : date2timestamp("2001-07-11 16:00:00","Y-m-d h:i:s");
retourne 994860000
*/


   //jour
   $d = "([0-3][0-9])";
   $j = "([1-3]?[0-9])";
   // mois
   $m = "(0[0-9]|1[0-2])";
   $n = "([0-9]|1[0-2])";
   $F = "(January|February|March|April|May|June|July|August|September|October|November|December)";
   $M = "(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)";
   //annee
   $Y = "([0-9]{4,4})";
   $y = "([0-9]{2,2})";
   //heures
   $g = "([1]?[0-9])";
   $G = "([0-2]?[0-9])";
   $h = "([01][0-9])";
   $H = "([0-2][0-9])";
   //minutes
   $i = "([0-5][0-9])";
   //secondes
   $s = "([0-5][0-9])";
   
   $z = "([0-3]?[0-9]?[0-9])";
   $I = "[01]" ;
   
   $lesmois = array('January'=>1,'February'=>2,'March'=>3,'April'=>4,'May'=>5,'June'=>6,
   'July'=>7,'August'=>8,'September'=>9,'October'=>10,'November'=>11,'December'=>12,
   'Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,'Jul'=>7,'Aug'=>8,'Sep'=>9,
   'Oct'=>10,'Nov'=>11,'Dec'=>12);
   
   $ok = array('M','F','I','d','j','m','n','y','Y','g','G','h','H','i','s','z');
   $nok = array('a','A','L','B','D','S','t','T','w','Z');

   $form_m = preg_replace("/([\(\)\[\]\{\}\?\.\*\?\$\^\/\\\\])/","\\\\$1",$format);
   $len = strlen($form_m);
   $form="";
   for($count=0;$count<$len;$count++)
      {
      $chr = substr($form_m,$count,1);
      if ($chr == '\\' || substr($form,-1,1) == '\\')
         {
         $form .= substr($form_m,$count,2);
         $count++;
         continue;
         }
      if (in_array($chr,$ok))
         $form .= $$chr; 
      else      
         if (in_array($chr,$nok))
            $form .= ".+"; 
         else
            if ($chr == 'r')
               $form .= ", $d $M $Y $H:$i:$s [-+][0-9]{4,4}";
            else
               $form .= $chr;
      }
   
   $format = preg_replace("/(^|[^\\\\])(r)/","$1, d M Y H:i:s",$format);
   $form = preg_replace("/\\\\\\\\([a-zA-Z])/","$1",$form);
   preg_match("/$form/",$date,$reg);
  
   $len = strlen($format);
   $pos = 1;
   $annee = $mois = $jour = 0;

   for($count=0;$count<$len;$count++)
      {
      $chr = substr($format,$count,1);
      if ($chr == '\\')
         {
         $count++;
         continue;
         }
      if ($chr == 'd' || $chr == 'j')
         $jour = $reg[$pos++];
      if ($chr == 'm' || $chr == 'n')
         $mois = $reg[$pos++];
      if ($chr == 'M' || $chr == 'F')
         $mois = $lesmois[$reg[$pos++]];
      if ($chr == 'y'|| $chr == 'Y')
         $annee = $reg[$pos++];
      if ($chr == 'g' || $chr == 'h'||$chr == 'G' || $chr == 'H')
         $heure = $reg[$pos++];
      if ($chr == 'i')
         $min = $reg[$pos++];
      if ($chr == 's' || $chr == 'z')
         $sec = $reg[$pos++];
      if ($chr == 'I')
         $dst = $reg[$pos++];
      }

   if ($jour == 0)
      return "Pas de jour specifie";
   if ($mois == 0)
      return "Pas de mois specifie";
   if ($annee == 0)
      return "Pas d'annee specifiee";
   if (!isset($heure))
      $heure=0;
   if (!isset($min))
      $min=0;
   if (!isset($sec))
      $sec=0;
   if (!isset($dst))
      $dst=-1;
   $timestamp = mktime($heure, $min, $sec, $mois, $jour, $annee, $dst);
   return $timestamp;
}

if ($inconnue == 1)
{
$date = "Non prévue";
}
else
{
$date = date2timestamp("$annee$mois$jour", "Ymd");
}

mysql_query("UPDATE config SET valeur='$episode' WHERE nom='episode'");
mysql_query("UPDATE config SET valeur='$ep_scen' WHERE nom='ep_scen'");
mysql_query("UPDATE config SET valeur='$ep_story' WHERE nom='ep_story'");
mysql_query("UPDATE config SET valeur='$ep_flash' WHERE nom='ep_flash'");
mysql_query("UPDATE config SET valeur='$date' WHERE nom='ep_date'");

$ALERT = "Avancement de l\'épisode mis à jour avec succès !";
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>