<? include "secu.php";

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

$requete  = "SELECT * FROM defi WHERE id=$id";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);

$message = "Salut<br><b>$disp[pseudo]</b> a demandé que votre team (<b>$disp[clan]</b>) fasse un matche contre nous.<br>Nous avons le plaisir de vous informer que nous acceptons le matche :)<br><br>Voici les informations que <b>$disp[pseudo]</b> à rentré concernant le matche:<br>Date : le <b>$disp[jour]/$disp[mois]/$disp[annee]</b> à <b>$disp[heure]:$disp[minute]</b><br>Votre Carte : <b>$disp[map]</b><br>Server : <b>$disp[server]</b><br><br>Voila, donc pour toute questions/modifications envoyer un mail à country@k1der.net ou passez sur irc ( #k1der sur Quakenet )<br><br>Team K1der";

$date = date("d/m/Y"); 
$heure = date("H:i");
$from="From:k1der_bot@k1der.net\n"; 
$from.="MIME-version: 1.0\n"; 
$from.="Content-type: text/html; charset= iso-8859-1\n"; 
$sujet = 'Matche vs K1der'; 
$message = str_replace("
","<br>",$message);
$message = "<font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$message<br><br>---------------------------------------<br>@+ sur <a target=_blank href=http://www.k1der.net>-=K1der=- The Chocolat Effect</a><br></font>"; 
$message = stripslashes($message);
mail ($disp[mail],$sujet,$message,$from);

$ladate = date2timestamp("$disp[annee]$disp[mois]$disp[jour]$disp[heure]$disp[minute]","YmdHi");

$requete  = "INSERT INTO next_matches VALUES('','$disp[clan]','$disp[leader]','$disp[pseudo]','$disp[mail]','$disp[irc]','$disp[msn]','$disp[server]','$ladate','$disp[heure]:$disp[minute]','Matche','','','','','','$disp[map]','Sais pas','$disp[annee]$disp[mois]$disp[jour]')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_query("DELETE FROM defi WHERE id='$id'");
?>
<script language="Javascript">
alert('Matche accepté, il à été ajouté dans les prochains matches');
         window.location='../index.php?page=admin';
</script>