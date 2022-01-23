<?php ob_start("ob_gzhandler");?>
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

<title>-=K1der=- The Chocolat Effect</title><body bgcolor="#ffffff" text="#000000" link="#DE0200" vlink="#DE0200" alink="#DE0200" LEFTMARGIN=0 TOPMARGIN=4 MARGINWIDTH=0 MARGINHEIGHT=4>
<?
//$nom_page = "Calendrier - LAN"; // optionnel, c'est pour spécifier le nom de la page
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
      <td width="870" height="30" colspan="3" valign="top"> <table width="400" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="50">&nbsp;&nbsp;&nbsp;<font face="Geneva, Arial, Helvetica, sans-serif"><strong><a href="index.php">&lt;&lt;</a></strong></font></td>
            <td width="175"> 
              <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="../index.php?page=ajouter_lan">Ajouter 
                une LAN </a></strong></font></div></td>
            <td width="175">
<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="search.php">Rechercher 
                une LAN </a></strong></font></div></td>
          </tr>
        </table>
        <br> 
        <?
			$requete  = "SELECT * FROM calendrier WHERE id='$id'";
			$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
			$nbre =mysql_num_rows($req);
			$disp = mysql_fetch_array($req);
			?>
        <font size="5" face="Verdana, Arial, Helvetica, sans-serif"><b> 
        <?
			  include "region.php";
			  echo $disp[nom];
			?>
        </b></font> </td>
    </tr>
    <tr> 
      <td colspan="3" valign="top"><table width="950" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td valign="top">&nbsp;</td>
            <td rowspan="7" valign="top"><div align="center"><img src="../images/regions/<? echo $img; ?>.gif" width="201" height="186"><br>
                <strong><font color="#CC0000" size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
                <font size="2"> <? if ($region != "inconnue") echo $region; ?></font></font></strong></div></td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td valign="top">&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="75%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Date 
              :</strong></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?

					setlocale(LC_ALL, "fr");
					$debut = date2timestamp($disp[debut], "Ymd");
					$debut = strftime("%d %B %Y",$debut);
					
					$debut = str_replace("January","Janvier",$debut);
					$debut = str_replace("February","Février",$debut);
					$debut = str_replace("March","Mars",$debut);
					$debut = str_replace("April","Avril",$debut);
					$debut = str_replace("May","Mai",$debut);
					$debut = str_replace("June","Juin",$debut);
					$debut = str_replace("July","Juillet",$debut);
					$debut = str_replace("August","Août",$debut);
					$debut = str_replace("September","Septembre",$debut);
					$debut = str_replace("October","Octobre",$debut);
					$debut = str_replace("November","Novembre",$debut);
					$debut = str_replace("December","Decembre",$debut);
					
					$fin = date2timestamp($disp[fin], "Ymd");
					$fin = strftime("%d %B %Y",$fin);
					
					$fin = str_replace("January","Janvier",$fin);
					$fin = str_replace("February","Février",$fin);
					$fin = str_replace("March","Mars",$fin);
					$fin = str_replace("April","Avril",$fin);
					$fin = str_replace("May","Mai",$fin);
					$fin = str_replace("June","Juin",$fin);
					$fin = str_replace("July","Juillet",$fin);
					$fin = str_replace("August","Août",$fin);
					$fin = str_replace("September","Septembre",$fin);
					$fin = str_replace("October","Octobre",$fin);
					$fin = str_replace("November","Novembre",$fin);
					$fin = str_replace("December","Decembre",$fin);

					echo "du <b>$debut</b> au <b>$fin</b> ( $disp[dur] jours )";
			?>
              </font><font size="5" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            <td width="25%" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="75%">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="45" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Lieu 
                    :</strong></font></td>
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    <?
					if (($disp[adresse] != '') && ($disp[adresse] != '?'))
					{
					if ($disp[dep] == "0")
					{
					echo "$disp[adresse], $disp[ville]";
					}
					else
					{
					echo "$disp[adresse], $disp[ville] (<i>$region</i>, $disp[dep])";
					}
					}
					else if ($disp[dep] == "0")
					{
					echo "$disp[ville]";
					}
					else
					{
					echo "$disp[ville] (<i>$region</i>, $disp[dep])";
					}
					?></td>
                </tr>
              </table></td>
            <td width="25%" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="75%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Site 
              Internet :</strong></font> <a target=_blank href="<? if (!ereg("http://",$disp[site])) { echo "http://"; } echo $disp[site]; ?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
              <?
			  if (!ereg("http://",$disp[site])) echo "http://";
			echo $disp[site];
			?>
              </b></font></a><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
              </b></font></td>
            <td width="25%" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="75%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>E-Mail 
              :</strong></font> <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <a href="mailto:<? echo $disp[mail]; ?>"> <? echo $disp[mail]; ?></a> 
              </font></strong></td>
            <td width="25%" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="75%" height="100" valign="top">&nbsp;</td>
            <td width="25%" height="100" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="3" valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Nombre 
              de Places :</strong></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?
			echo "$disp[places]";
			?>
              </font> </td>
            <td rowspan="5" valign="top"> 
              <?
				  if ($disp[k1der] == 'oui')
				  {
				  ?>
              <img src="../images/lan.jpg" alt="Y ora des -=K1der=- &agrave; cette LAN !!!" width="300" height="197"> 
              <?
				  }
				  ?>
            </td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Prix 
              :</strong></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?
			echo $disp[prix];
			?></font></td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="60" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Tournois 
                    :</strong></font> </td>
                  <td width="350" valign="bottom"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <?
						  if (($disp[tournois1] == '') && ($disp[tournois2] == '') && ($disp[tournois3] == '') && ($disp[tournois4] == '') && ($disp[tournois5] == '') && ($disp[tournois6] == '') && ($disp[tournois7] == '') && ($disp[tournois8] == ''))
						  {
						  echo "Non";
						  }
						  else
						  {
					if ($disp[tournois1] != '')
					{
					echo "<li>$disp[tournois1]<br>";
					}
					if ($disp[tournois2] != '')
					{
					echo "<li>$disp[tournois2]<br>";
					}
					if ($disp[tournois3] != '')
					{
					echo "<li>$disp[tournois3]<br>";
					}
					if ($disp[tournois4] != '')
					{
					echo "<li>$disp[tournois4]<br>";
					}
					if ($disp[tournois5] != '')
					{
					echo "<li>$disp[tournois5]<br>";
					}
					if ($disp[tournois6] != '')
					{
					echo "<li>$disp[tournois6]<br>";
					}
					if ($disp[tournois7] != '')
					{
					echo "<li>$disp[tournois7]<br>";
					}
					if ($disp[tournois8] != '')
					{
					echo "<li>$disp[tournois8]<br>";
					}
					}
					?>
                    </font></td>
                </tr>
                <tr> 
                  <td width="60">&nbsp;</td>
                  <td width="350">&nbsp;</td>
                </tr>
              </table></td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td valign="top"> 
              <?
				  if (($disp[tournois1] == '') && ($disp[tournois2] == '') && ($disp[tournois3] == '') && ($disp[tournois4] == '') && ($disp[tournois5] == '') && ($disp[tournois6] == '') && ($disp[tournois7] == '') && ($disp[tournois8] == ''))
				  {
				  }
				  else
				  {
				  ?>
              <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Lots 
              :</strong></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <br>
              <?
			echo $disp[lots];
			}
			?>
              </font></td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td valign="top">&nbsp;</td>
            <td valign="top">&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="3" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <?
				  if ($disp[infos] == '')
				  {
				  }
				  else
				  {
				  ?>
              Infos compl&eacute;mentaires :</strong></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <br>
              <?
			echo $disp[infos];
			}
			?>
              </font></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="3" valign="top">&nbsp;</td>
    </tr>
  </table>
</center>
<XML style=display:none> 
<?php ob_end_flush(); ?>
