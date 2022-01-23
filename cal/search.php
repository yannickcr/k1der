<?php ob_start("ob_gzhandler");?>
<html>
<head>

<style type="text/css">
<!--
a:visited {
	text-decoration: none;
}
-->
</style>
<STYLE type=text/css><!--
.drop { position: absolute; width: 3;  filter: flipV(), flipH(); font-size: 40; color: blue }

a:link {
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
-->
</STYLE>

<script language="javascript" src="http://www.k1der.net/phpstats/php-stats.js.php"></script>
<noscript><img src="http://www.k1der.net/phpstats/php-stats.php" border="0" alt=""></noscript>

<title>-=K1der=- The Chocolat Effect</title>
<body bgcolor="#ffffff" text="#000000" link="#DE0200" vlink="#DE0200" alink="#DE0200" LEFTMARGIN=0 TOPMARGIN=4 MARGINWIDTH=0 MARGINHEIGHT=4>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?
//$nom_page = "Calendrier - Recherche"; // optionnel, c'est pour spécifier le nom de la page
//require "../stat/visiteur.php";

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

function diff_date($jour , $mois , $an , $jour2 , $mois2 , $an2){ 
 $timestamp = mktime(0, 0, 0, $mois, $jour, $an); 
 if ($mois != $mois2)
 {
 $kk = 1;
 }
 else
 {
 $kk = 0;
 }
 $timestamp2 = mktime(0, 0, 0, $mois2-$kk, $jour2, $an2); 
  
  $diff = floor(($timestamp - $timestamp2) / (3600 * 24)); 
  return $diff; 
}

require("../config.inc.php3");
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

?>
<center>
  <table width="950" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="217">&nbsp;</td>
      <td width="366"> <img src="../images/titre3_normal.gif" width="122" height="58" border="0" usemap="#Map2"></td>
      <td valign="bottom" width="116"><img src="../images/titre4_normal.gif" width="78" height="27"></td>
      <td valign="bottom" width="140"> <img src="../images/titre5_normal.gif" width="93" height="52"></td>
      <td valign="bottom" width="111"> <img src="../images/titre6_normal.gif" width="72" height="28"></td>
    </tr>
    <tr> 
      <td colspan="5"><a href="../index.php"><img src="../images/titre2_normal.gif" width="950" height="95" border="0"></a></td>
    </tr>
    <tr > 
      <td colspan="5"><table width="950" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="20" valign="bottom" background="../images/fond.gif"><img src="../images/coingbasgauche.gif" width="15" height="15"></td>
            <td width="910" background="../images/fond.gif">&nbsp;</td>
            <td width="20" align="right" valign="bottom" background="../images/fond.gif"><img src="../images/coingbasdroite.gif" width="15" height="15"></td>
          </tr>
        </table></td>
    </tr>
  </table>
  <table width="950" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="870" height="30" colspan="3" valign="top"> <table width="225" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="50"> &nbsp;&nbsp;&nbsp;<font face="Geneva, Arial, Helvetica, sans-serif"><strong><a href="index.php">&lt;&lt;</a></strong></font></td>
            <td width="175"> 
              <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="../index.php?page=ajouter_lan">Ajouter 
                une LAN </a></strong></font></div></td>
          </tr>
        </table>
        <?
			  if ($region != '')
			  {
			  if ($all != '1')
			  {
			  include 'dep.php';
			  }
			  else
			  {
			  include 'dep_all.php';
			  }
			  $nbre =mysql_num_rows($req);
			  }
			  ?>
        <font size="5" face="Verdana, Arial, Helvetica, sans-serif"><b> </b></font> 
        <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Clique 
          sur la r&eacute;gion qui t'interresse</strong></font><br>
          <br>
          <img src="../images/regions/map.gif" alt="Languedoc-Roussillon" name="image13" width="195" height="186" border="0" usemap="#image13MapMap" id="image13"><br>
          <br>
        </div></td>
    </tr>
    <tr> 
      <td colspan="3" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?
			  if ($region != '')
			  {
			if (($nbre == '0') or ($nbre == ''))
			{
			$region = stripslashes("$region");
			echo "<b>Désolé, Aucune LAN n'a été trouv&eacute;es en $region</b>";
			}
			else
			{
			$region = stripslashes("$region");
			echo "<b>$nbre LAN trouv&eacute;es en $region :</b>";
			}
			?>
        <br>
        <?
			  if (($nbre != '0') or ($nbre != ''))
			  {
			  while($disp = mysql_fetch_array($req))
			  {
			  $date1 = date2timestamp("$disp[debut]","Ymd");
			  $date1 = date("d/m/Y", $date1);
			  echo "- <a href=lan.php?id=$disp[id]><b>$disp[nom]</b></a>, le <b>$date1</b> à <b>$disp[ville]</b> ($disp[dep])<br>";
			  }
			  }
			  ?>
			  <br>
              <br>
			  <?
			  if ($all != '1')
			  {
			  ?>
              <a href="search.php?region=<? echo $region; ?>&all=1">Rechercher toutes les LANs qui ont eut lieues en <? echo $region; ?> depuis F&eacute;vrier 2003</a>
			  <?
			  }
			  else
			  {
			  ?>
              <a href="search.php?region=<? echo $region; ?>">Rechercher seulement les LANs à venir en <? echo $region; ?></a>
			  <?
			  }
			  }
			  ?>
</font></td>
    </tr>
  </table>
  <map name="image13MapMap">
    <area shape="poly" coords="53,55,43,52,33,51,23,46,16,49,2,49,4,62,20,69,30,75,49,70" href="search.php?region=Bretagne" alt="Bretagne">
    <area shape="poly" coords="30,78,46,99,57,100,54,85,63,85,76,69,70,58,55,55,50,71" href="search.php?region=Pays%20de%20la%20Loire" alt="Pays de la Loire">
    <area shape="poly" coords="50,106,48,112,57,120,65,125,76,113,75,101,82,96,74,88,66,86,57,87,59,100,47,101" href="search.php?region=Poitou-Charentes" alt="Poitou-Charentes">
    <area shape="poly" coords="49,117,47,130,38,158,59,171,64,161,62,149,77,145,84,127,77,114,66,127" href="search.php?region=Aquitaine" alt="Aquitaine">
    <area shape="poly" coords="61,171,78,173,91,175,93,169,89,162,96,159,114,149,108,131,97,133,94,127,86,128,79,146,64,150" href="search.php?region=Midi-Pyr%E9n%E9es" alt="Midi-Pyr&eacute;n&eacute;es">
    <area shape="poly" coords="109,178,93,180,91,163,114,151,108,130,119,128,126,141,131,143,134,150,129,157,118,157,108,165" href="search.php?region=Languedoc-Roussillon" alt="Languedoc-Roussillon">
    <area shape="poly" coords="131,160,141,159,154,167,178,145,166,136,168,129,161,121,152,126,145,136,146,144,131,138,137,148" href="search.php?region=Provence-Alpes-C%F4te%20dAzur" alt="Provence-Alpes-C&ocirc;te d'Azur">
    <area shape="poly" coords="188,150,185,159,179,164,181,177,190,184,193,169,193,159" href="search.php?region=Corse" alt="Corse">
    <area shape="poly" coords="160,95,167,105,162,108,167,116,162,121,150,126,143,136,143,142,135,138,129,141,122,132,130,123,120,114,119,102,128,101,140,97,149,100" href="search.php?region=Rhone-Alpes" alt="Rhone-Alpes">
    <area shape="poly" coords="96,98,102,105,101,115,96,127,86,126,81,117,77,102,84,97" href="search.php?region=Limousin" alt="Limousin">
    <area shape="poly" coords="99,97,107,90,116,93,121,97,118,108,126,122,123,129,109,129,98,130,104,113,103,104" href="search.php?region=Auvergne" alt="Auvergne">
    <area shape="poly" coords="42,28,50,29,53,37,66,38,73,50,78,55,75,58,46,51" href="search.php?region=Basse-Normandie" alt="Basse-Normandie">
    <area shape="poly" coords="79,70,76,60,83,49,92,58,101,62,106,66,109,88,97,96,83,96,66,85" href="search.php?region=Centre" alt="Centre">
    <area shape="poly" coords="112,57,118,67,130,65,138,72,139,83,140,97,125,101,117,93,110,89,107,61" href="search.php?region=Bourgogne" alt="Bourgogne">
    <area shape="poly" coords="142,97,151,97,152,90,163,78,163,70,151,63,141,71" href="search.php?region=Franche-Comt%E9" alt="Franche-Comt&eacute;">
    <area shape="poly" coords="161,66,169,73,173,55,177,45,169,40,162,44" href="search.php?region=Alsace" alt="Alsace">
    <area shape="poly" coords="68,31,85,23,89,38,85,48,77,52,70,41" href="search.php?region=Haute-Normandie" alt="Haute-Normandie">
    <area shape="poly" coords="121,24,86,17,88,6,99,3,108,10,116,16" href="search.php?region=Nord-Pas-De-Calais" alt="Nord-Pas-De-Calais">
    <area shape="poly" coords="138,32,151,32,157,40,168,38,159,44,158,64,151,62,146,66,131,49,131,40" href="search.php?region=Lorraine" alt="Lorraine">
    <area shape="poly" coords="112,41,121,26,86,19,89,29,90,39" href="search.php?region=Picardie" alt="Picardie">
    <area shape="poly" coords="110,42,89,41,86,49,94,58,104,60,113,54" href="search.php?region=Ile-de-France" alt="Ile-de-France">
    <area shape="poly" coords="128,23,136,31,127,42,143,66,140,71,131,63,120,65,115,56,114,44,120,33" href="search.php?region=Champagne-Ardenne" alt="Champagne-Ardenne">
  </map>
</center>
<map name="image13Map">
  <area shape="poly" coords="53,55,43,52,33,51,23,46,16,49,2,49,4,62,20,69,30,75,49,70" href="search.php?region=Bretagne" alt="Bretagne">
  <area shape="poly" coords="30,78,46,99,57,100,54,85,63,85,76,69,70,58,55,55,50,71" href="search.php?region=Pays%20de%20la%20Loire" alt="Pays de la Loire">
  <area shape="poly" coords="50,106,48,112,57,120,65,125,76,113,75,101,82,96,74,88,66,86,57,87,59,100,47,101" href="search.php?region=Poitou-Charentes" alt="Poitou-Charentes">
  <area shape="poly" coords="49,117,47,130,38,158,59,171,64,161,62,149,77,145,84,127,77,114,66,127" href="search.php?region=Aquitaine" alt="Aquitaine">
  <area shape="poly" coords="61,171,78,173,91,175,93,169,89,162,96,159,114,149,108,131,97,133,94,127,86,128,79,146,64,150" href="search.php?region=Midi-Pyr%E9n%E9es" alt="Midi-Pyr&eacute;n&eacute;es">
  <area shape="poly" coords="109,178,93,180,91,163,114,151,108,130,119,128,126,141,131,143,134,150,129,157,118,157,108,165" href="search.php?region=Languedoc-Roussillon" alt="Languedoc-Roussillon">
  <area shape="poly" coords="131,160,141,159,154,167,178,145,166,136,168,129,161,121,152,126,145,136,146,144,131,138,137,148" href="search.php?region=Provence-Alpes-C%F4te%20d%27Azur" alt="Provence-Alpes-C&ocirc;te d'Azur">
  <area shape="poly" coords="188,150,185,159,179,164,181,177,190,184,193,169,193,159" href="search.php?region=Corse" alt="Corse">
  <area shape="poly" coords="160,95,167,105,162,108,167,116,162,121,150,126,143,136,143,142,135,138,129,141,122,132,130,123,120,114,119,102,128,101,140,97,149,100" href="search.php?region=Rhone-Alpes" alt="Rhone-Alpes">
  <area shape="poly" coords="96,98,102,105,101,115,96,127,86,126,81,117,77,102,84,97" href="search.php?region=Limousin" alt="Limousin">
  <area shape="poly" coords="99,97,107,90,116,93,121,97,118,108,126,122,123,129,109,129,98,130,104,113,103,104" href="search.php?region=Auvergne" alt="Auvergne">
  <area shape="poly" coords="42,28,50,29,53,37,66,38,73,50,78,55,75,58,46,51" href="search.php?region=Basse-Normandie" alt="Basse-Normandie">
  <area shape="poly" coords="79,70,76,60,83,49,92,58,101,62,106,66,109,88,97,96,83,96,66,85" href="search.php?region=Centre" alt="Centre">
  <area shape="poly" coords="112,57,118,67,130,65,138,72,139,83,140,97,125,101,117,93,110,89,107,61" href="search.php?region=Bourgogne" alt="Bourgogne">
  <area shape="poly" coords="142,97,151,97,152,90,163,78,163,70,151,63,141,71" href="search.php?region=Franche-Comt%E9" alt="Franche-Comt&eacute;">
  <area shape="poly" coords="161,66,169,73,173,55,177,45,169,40,162,44" href="search.php?region=Alsace" alt="Alsace">
  <area shape="poly" coords="68,31,85,23,89,38,85,48,77,52,70,41" href="search.php?region=Haute-Normandie" alt="Haute-Normandie">
  <area shape="poly" coords="121,24,86,17,88,6,99,3,108,10,116,16" href="search.php?region=Nord-Pas-De-Calais" alt="Nord-Pas-De-Calais">
  <area shape="poly" coords="138,32,151,32,157,40,168,38,159,44,158,64,151,62,146,66,131,49,131,40" href="search.php?region=Lorraine" alt="Lorraine">
  <area shape="poly" coords="112,41,121,26,86,19,89,29,90,39" href="search.php?region=Picardie" alt="Picardie">
  <area shape="poly" coords="110,42,89,41,86,49,94,58,104,60,113,54" href="search.php?region=Ile-de-France" alt="Ile-de-France">
  <area shape="poly" coords="128,23,136,31,127,42,143,66,140,71,131,63,120,65,115,56,114,44,120,33" href="search.php?region=Champagne-Ardenne" alt="Champagne-Ardenne">
</map>
</body>
</html>
<XML style=display:none> 
<?php ob_end_flush(); ?>
