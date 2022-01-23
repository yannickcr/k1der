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

<title>-=K1der=- The Chocolat Effect || Calendrier des Lan Party</title><body bgcolor="#ffffff" text="#000000" link="#DE0200" vlink="#DE0200" alink="#DE0200" LEFTMARGIN=0 TOPMARGIN=4 MARGINWIDTH=0 MARGINHEIGHT=4>
<?
require("../config.inc.php3");
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

//$nom_page = "Calendrier"; // optionnel, c'est pour spécifier le nom de la page
//require "../stat/visiteur.php";

$titrepage = "-=K1der=- The Chocolat Effect";

echo "<title>$titrepage</title>";

$mois2["1"] = "Janvier";
$mois2["2"] = "Février";
$mois2["3"] = "Mars";
$mois2["4"] = "Avril";
$mois2["5"] = "Mai";
$mois2["6"] = "Juin";
$mois2["7"] = "Juillet";
$mois2["8"] = "Août";
$mois2["9"] = "Septembre";
$mois2["10"] = "Octobre";
$mois2["11"] = "Novembre";
$mois2["12"] = "Décembre";

function getMois($month){
return $mois2[$month];
}

function jour_sem_cal_greg($jour,$mois,$annee){
# Fonction facteur à télécharger séparément
    $jours_sem = array("Samedi","Dimanche", "Lundi", "Mardi", "Mercredi","Jeudi", "Vendredi");
    $f=facteur($jour, $mois,$annee);
    $j= $f-intval($f/7)*7;
    return $jours_sem[$j];  
}
function facteur($jour,$mois,$annee){
$b=365*$annee;
        $c=31*($mois-1);
        if (($mois==1) || ($mois==2))
        { $d= 0;
            $e = intval(($annee -1)/4);
            $h = intval(0.75*(intval(($annee-1)/100)+1));
        }
     else
        { $d= intval(0.4*$mois+2.3);
            $e = intval($annee/4);
            $h = intval(0.75*(intval($annee/100)+1));
        }                                
     $result = $jour + $b+ $c - $d +$e -$h; 
             
    return $result; 
}
/* utilisation de $j $m et $a */
if($m >12)
{
   // print("Vous avez déja vi un mois 13 ?");
}
elseif($j > 31)
{
   // print("Arf, le 32 existe ?");
}
elseif($a < 1582)
{
  //  print("Le calendrier grégorien n'est établi qu'a partir de 1583");
}
else
{
    //print("<center><b>Le $j/$m/$a est un :<br></b>");
    //print("<br><br><br><a href=\"index.php\">Retour</a>");
}

function oeuf_date ($Year) {
   
       /*
       G is the Golden Number-1 
       H is 23-Epact (modulo 30) 
       I is the number of days from 21 March to the Paschal full moon 
       J is the weekday for the Paschal full moon (0=Sunday,
         1=Monday, etc.) 
       L is the number of days from 21 March to the Sunday on or before
         the Paschal full moon (a number between -6 and 28) 
       */
       

         $G = $Year % 19;
        $C = (int)($Year / 100);
         $H = (int)($C - ($C / 4) - ((8*$C+13) / 25) + 19*$G + 15) % 30;
         $I = (int)$H - (int)($H / 28)*(1 - (int)($H / 28)*(int)(29 / ($H + 1))*((int)(21 - $G) / 11));
        $J = ($Year + (int)($Year/4) + $I + 2 - $C + (int)($C/4)) % 7;
         $L = $I - $J;
         $m = 3 + (int)(($L + 40) / 44);
         $d = $L + 28 - 31 * ((int)($m / 4));
         $y = $Year;
         $E = mktime(0,0,0, $m, $d, $y);

        return $E;

   }

function GetFeastday($date)
{
    $d=@getdate($date);
    if($d['mday']==1 && $d['mon']==1) return 'Jour de l\'An';
    else if($d['mday']==1 && $d['mon']==5) return 'Fête du travail';
    else if($d['mday']==8 && $d['mon']==5) return 'Victoire 1945';
    else if($d['mday']==14 && $d['mon']==7) return 'Fête Nationale';
    else if($d['mday']==15 && $d['mon']==8) return 'Assomption';
    else if($d['mday']==1 && $d['mon']==1) return 'Toussaint';
    else if($d['mday']==11 && $d['mon']==11) return 'Armistice 1918';
    else if($d['mday']==25 && $d['mon']==12) return 'Noël';
    else 
    {
        //Autres cas
        //Paques
        $paques=@getdate(oeuf_date($d['year']));
        
        //Lundi de paques
        $Lpaques=$paques;
        for($i=0; $Lpaques['wday']!=1 && $i<7; $i++)
        $Lpaques=@getdate(@mktime(0,0,0,$Lpaques['mon'],$Lpaques['mday']+$i,$Lpaques['year']));
        if($d['mday']==$Lpaques['mday'] && $d['mon']==$Lpaques['mon']) 
        return 'Lundi de Pâques';
        else
        {

            //Pentecote=septième dimanche après Pâques
            $pentecote=@getdate(@mktime(0,0,0,$paques['mon'],$paques['mday']+49,$paques['year']));
            for($i=0; $pentecote['wday']!=0 &&$i<7; $i++)
            $pentecote=@getdate(@mktime(0,0,0,$pentecote['mon'],$pentecote['mday']+$i,$pentecote['year']));
            
            //Lundi de Pentecote
            $Lpentecote=@getdate(@mktime(0,0,0,$pentecote['mon'],$pentecote['mday']+1,$pentecote['year']));
            if($d['mday']==$Lpentecote['mday'] && $d['mon']==$Lpentecote['mon']) 
            return 'Pentecôte';
            else
            {

                //Ascension = pentecote -10j
                $ascension=@getdate(@mktime(0,0,0,$pentecote['mon'],$pentecote['mday']-10,$pentecote['year']));
                if($d['mday']==$ascension['mday'] && $d['mon']==$ascension['mon']) 
                return 'Ascension';
            }
        }
    }

    return '';
}

function getNbDay($month , $year){ 
 /** function retournant le nombre de jour pour un mois et une année donnés 
  /** author : renaud racouchot 
  /** email : renotm@caramail.com 
  /** vous pouvez utiliser ce script comme bon vous semble **/ 
  
  $nb_day_list = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
  
  // cas des années bisextile 
  if ($month==2) { 
  if ((($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0)) { 
  $nb_day = 29; 
  } 
  else { 
  $nb_day = 28; 
  } 
  } 
  else { 
  $nb_day = $nb_day_list[$month]; 
  } 
  return $nb_day; 
}

function calendriercalendrier($month , $year){ 
 /** function affichant un calendrier pour le mois et l'année choisie 
  /** author : renaud racouchot 
  /** email : renotm@caramail.com 
  /** ----------------------------- 
  /** utilise la function getNbDay(); 
  /** vous pouvez utiliser ce script comme bon vous semble **/ 
  
  // initialisation des variables utiles 
  
  $month_list = array("", "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin",
"Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"); 
  //recuperation de la date du jour 
  if ($year=="") { 
  $year = date("Y"); 
  } 
  if ($month==""){ 
  $month = date("n"); 
  } 
  
  $current_day = date("d"); 
  $first_day = date(w, mktime(0, 0, 0, $month, 1, $year))-1; 
  if ($first_day == -1) $first_day = 6; 
  $nb_day = getNbDay($month, $year); 
  // affichage 
	if ($month =="1")
	{
	$moa = "Janvier";
	}
	if ($month =="2")
	{
	$moa = "Février";
	}
	if ($month =="3")
	{
	$moa = "Mars";
	}
	if ($month =="4")
	{
	$moa = "Avril";
	}
	if ($month =="5")
	{
	$moa = "Mai";
	}
	if ($month =="6")
	{
	$moa = "Juin";
	}
	if ($month =="7")
	{
	$moa = "Juillet";
	}
	if ($month =="8")
	{
	$moa = "Août";
	}
	if ($month =="9")
	{
	$moa = "Septembre";
	}
	if ($month =="10")
	{
	$moa = "Octobre";
	}
	if ($month =="11")
	{
	$moa = "Novembre";
	}
	if ($month =="12")
	{
	$moa = "Décembre";
	}
	?><center>
<table width="950" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="3"><table width="950" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="217">&nbsp;</td>
          <td width="366"> <img src="../images/titre3_normal.gif" width="122" height="58"></td>
          <td valign="bottom" width="116"><img src="../images/titre4_normal.gif" width="78" height="27"></td>
          <td valign="bottom" width="140"> <img src="../images/titre5_normal.gif" width="93" height="52"></td>
          <td valign="bottom" width="111"> <img src="../images/titre6_normal.gif" width="72" height="28"></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan="3"><a href="../index.php"><img src="../images/titre2_normal.gif" width="950" height="95" border="0"></a></td>
  </tr>
  <tr> 
    <td width="20" valign="bottom" background="../images/fond.gif"><img src="../images/coingbasgauche.gif" width="15" height="15"></td>
    <td width="910" background="../images/fond.gif">&nbsp;</td>
    <td width="20" align="right" valign="bottom" background="../images/fond.gif"><img src="../images/coingbasdroite.gif" width="15" height="15"></td>
  </tr>
</table>
<table width="950" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td> <table width="400" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="50">&nbsp;</td>
          <td width="175"> 
            <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="../index.php?page=ajouter_lan">Ajouter 
              une LAN </a></strong></font></div></td>
          <td width="175">
<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="search.php">Rechercher 
              une LAN </a></strong></font></div></td>
        </tr>
      </table>
      <br> <table width="847" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td><div align="center"><font face="Geneva, Arial, Helvetica, sans-serif"><strong><a href="index.php?mois=
				<?
				if ($month-1 == '0')
				{
				$yearp = $year-1;
				$moap = '12';
				}
				else
				{
				$moap = $month-1;
				$yearp = $year;
				}
				echo $moap; ?>&annee=<? echo $yearp; ?>">&lt;&lt;</a></strong></font></div></td>
          <td width="100%"> <div align="center"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><? echo "$moa $year"; ?></font></strong></div></td>
          <td><div align="center"><font face="Geneva, Arial, Helvetica, sans-serif"><strong> 
              <a href="index.php?mois=
				<?
				if ($month+1 == '13')
				{
				$yearp = $year+1;
				$moap = '1';
				}
				else
				{
				$moap = $month+1;
				$yearp = $year;
				}
				echo $moap; ?>&annee=<? echo $yearp; ?>">&gt;&gt;</a></strong></font></div></td>
        </tr>
      </table>
      <strong></strong> <font size="4" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
      <table width="847" border="1" align="center" cellpadding="1" cellspacing="2" bordercolor="#FFFFFF">
        <tr> 
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lundi</font></div></td>
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mardi</font></div></td>
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mercredi</font></div></td>
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Jeudi</font></div></td>
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Vendredi</font></div></td>
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Samedi</font></div></td>
          <td width="121"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Dimanche</font></div></td>
        </tr>
        <tr> 
          <?
  
  // case vide avant 
  for ($i=0;$i<$first_day;$i++) { 
  ?>
          <td width="121"> </td>
          <?
  } 
  //cases renseignées 
  for ($i=0;$i<$nb_day;$i++) { 
  if ($month < '10')
  {
  $mois2 = "0$month";
  } 
  $first_day++; 
  $j = $i+1; 
  if ($first_day%7 == 1) { 
  ?>
        <tr> 
          <?
  }
  	if ($j < 10)
	{
	$j2 = "0$j";
    }
	else
	{
	$j2 = $j;
	}
  $requete  = "SELECT nom FROM calendrier WHERE debut='$year$mois2$j2' && conf=1";
  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
  $nbre =mysql_num_rows($req);
  ?>
          <td width="121" height="40" valign="top" bordercolor="#000000" 
	<? 
	$m = $month;
	$a = $year;
	$zour = jour_sem_cal_greg($j,$m,$a);
	$jour= mktime(0,0,0,$month,$j,$year);
    $fete= GetFeastday($jour);
	
	if ($nbre != '0')
	{
	echo "bgcolor=#FFE0E0";
	}
	else if (($zour == 'Dimanche') or ($zour == 'Samedi'))
	{
	echo "bgcolor=#E0E0E0";
	}
	else if ($fete != '')
	{
	echo "bgcolor=#E0E0E0";
	} 
	?>> <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?
						$ce_mois = date(m);
						$cette_annee = date(Y);
						$ce_jour = date(j);
						if (($ce_mois == $month) && ($cette_annee == $year) && ($ce_jour == $j))
						{
						echo "<left><font color=red><b>$j</b></font><br></left>";
						}
						else
						{
						echo "<left>$j<br></left>";
						}
						?>
                  </font> <div align="center"> </div>
                  <div align="center"> </div>
                  <div align="center"> </div></td>
                <td width="100%"></td>
              </tr>
              <tr valign="top"> 
                <td height="100%" colspan="2"
					  
	<?
	$a = '';
	$b = '';
	$c = '';
	if ($mois2 == '')
	{
	$mois2 = $month;
	}
	$requete  = "SELECT * FROM vacances ORDER BY id";
	$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	$nbre =mysql_num_rows($req);
	while($disp = mysql_fetch_array($req))
	{

	if ($j < '10')
	{
	$j2 = "0$j";
	}
	if ("$year$mois2$j2" >= "$disp[debut_zonea]")
	{
	if ("$year$mois2$j2" <= "$disp[fin_zonea]")
	{
	$a = 'r';
	}
	}
	if ("$year$mois2$j2" >= "$disp[debut_zoneb]")
	{
	if ("$year$mois2$j2" <= "$disp[fin_zoneb]")
	{
	$b = 'v';
	}
	}
	if ("$year$mois2$j2" >= "$disp[debut_zonec]")
	{
	if ("$year$mois2$j2" <= "$disp[fin_zonec]")
	{
	$c = 'b';
	}
	}
	$j2 = $j;
	}
	echo "background='../images/fond_cal_$a$b$c.gif'";
	?>><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?
	if ($month < 10)
	{
	$mois2 = "0$month";
	}
	if ($j < 10)
	{
	$j2 = "0$j";
	}
	else
	{
	$j2 = "$j";
	}
	
  $requete  = "SELECT * FROM calendrier WHERE debut='$year$mois2$j2' && conf=1";
  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
  $nbre =mysql_num_rows($req);
	  
	while($disp = mysql_fetch_array($req))
	{
	if ($disp[dep] == "0")
	{
	echo "<center><a href=lan.php?id=$disp[id]><font size=1 color=#CC0000>$disp[nom]</font></a><br><font size=1>&nbsp;<br></font></center>";
	}
	else
	{
	echo "<center><a href=lan.php?id=$disp[id]><font size=1 color=#CC0000>$disp[nom]</font></a> <font size=1 color=#000000>($disp[dep])</font><br><font size=1>&nbsp;<br></font></center>";
	}
	}
	?>
                  </font></td>
              </tr>
            </table></td>
          <?
  if ($first_day%7 == 0) { 
  ?>
        </tr>
        <?
  } 
  } 
  while (($first_day%7) != 0){ 
  $first_day++;
  ?>
        <td> </td>
        <?
  } 
  ?>
        </tr>
      </table>
      <?
  }
  if (($mois == '') && ($annee == ''))
  {
  $mois = date(n);
  $annee = date(Y);
  }
  calendriercalendrier($mois , $annee);

?>
    </td>
  </tr>
  <tr> 
    <td align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td align="center"><form name="form1" method="post" action="index.php">
        <font face="Verdana, Arial, Helvetica, sans-serif" size="2">Aller au mois 
        de 
        <select name="mois" id="select">
          <? $ce_mois = date(m); ?>
          <option value="1"<?	if ($ce_mois == '1') { echo "selected"; } ?>><font color="#FFFFFF">Janvier</font></option>
          <option value="2"<?	if ($ce_mois == '2') { echo "selected"; } ?>><font color="#FFFFFF">F&eacute;vrier</font></option>
          <option value="3"<?	if ($ce_mois == '3') { echo "selected"; } ?>><font color="#FFFFFF">Mars</font></option>
          <option value="4"<?	if ($ce_mois == '4') { echo "selected"; } ?>><font color="#FFFFFF">Avril</font></option>
          <option value="5"<?	if ($ce_mois == '5') { echo "selected"; } ?>><font color="#FFFFFF">Mai</font></option>
          <option value="6"<?	if ($ce_mois == '6') { echo "selected"; } ?>><font color="#FFFFFF">Juin</font></option>
          <option value="7"<?	if ($ce_mois == '7') { echo "selected"; } ?>><font color="#FFFFFF">Juillet</font></option>
          <option value="8"<?	if ($ce_mois == '8') { echo "selected"; } ?>><font color="#FFFFFF">Ao&ucirc;t</font></option>
          <option value="9"<?	if ($ce_mois == '9') { echo "selected"; } ?>><font color="#FFFFFF">Septembre</font></option>
          <option value="10"<? if ($ce_mois == '10') { echo "selected"; } ?>><font color="#FFFFFF">Octobre</font></option>
          <option value="11"<? if ($ce_mois == '11') { echo "selected"; } ?>><font color="#FFFFFF">Novembre</font></option>
          <option value="12"<? if ($ce_mois == '12') { echo "selected"; } ?>><font color="#FFFFFF">D&eacute;cembre</font></option>
        </select>
        de l'ann&eacute;e 
        <select name="annee" id="annee">
          <?
			  $v = '0';
			  $cette_annee = date(Y);
			  $sel_annee1 = $cette_annee-2;
			  while ($v < 9)
			  {
			  $sel_annee = $sel_annee1+$v;
			  ?>
          <option value="<? echo $sel_annee; ?>"
				<? 
				if ($cette_annee == $sel_annee)
				{
				echo "selected";
				}
				 ?>
				><? echo $sel_annee; ?></option>
          <?
				$v = $v+1;
				}
				?>
        </select>
        </font><br>
        <br>
        <input type="submit" name="Submit" value="Go Go Go !">
      </form></td>
  </tr>
  <tr> 
    <td valign="top"> &nbsp;&nbsp;<img src="../images/za.gif" width="15" height="3" align="absmiddle"> 
      <font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Zone 
      A</strong> (Caen, Clermont-Ferrand, Grenoble, Lyon, Montpellier, Nancy-Metz, 
      Nantes, Rennes, Toulouse)</font><br> &nbsp;&nbsp;<img src="../images/zb.gif" width="15" height="3" align="absmiddle"> 
      <font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Zone 
      B</strong> (Aix-Marseille, Amiens, Besan&ccedil;on, Dijon, Lille, Limoges, 
      Nice, Orl&eacute;ans-Tours, Poitiers, Reims, Rouen, Strasbourg)</font><br> 
      &nbsp; <img src="../images/zc.gif" width="15" height="3" align="absmiddle"> 
      <font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Zone 
      C</strong> (Bordeaux, Cr&eacute;teil, Paris, Versailles)<br>
      </font> <table width="250" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
        <tr> 
          <td width="1"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          <td width="25" bordercolor="#000000" bgcolor="#E0E0E0"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<strong>Week-End</strong> 
            / <strong>Jour F&eacute;ri&eacute;</strong></font></td>
        </tr>
      </table></td>
  </tr>
</table>
<p align="center"><font face="Verdana" size="1">Webmaster 
      :&nbsp;
	  <script language="JavaScript">
	  var un="country";
	  var deux = "k1der.net";
	  document.write("<a href="+"ma"+"ilto:"+un+"[AT]"+deux+">-=K1der=- Country</a>");
	  </script>
	  </font><font face="Verdana" size="1"> Dessins 
      :&nbsp; 
      <script language="JavaScript">
	  var un="maxi";
	  var deux = "k1der.net";
	  document.write("<a href="+"ma"+"ilto:"+un+"[AT]"+deux+">-=K1der=- Maxi</a>");
	  </script> </font><br>
      <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="1">Copyright 
      ©2003-2004 -=K1der=- Team (Articles, News, Images &amp; Commentaires du 
      Webmaster)<br>
      Reproduction interdite sans autorisation.<br>
      Ce site est pr&eacute;vu pour une r&eacute;solution de 1024x768 sur un navigateur 
      de type 5 ou sup&eacute;rieur<br>
      Il est recommand&eacute; d'ouvrir les yeux et d'allumer l'&eacute;cran pour 
      b&eacute;n&eacute;ficier du contenu de ce site<br>
<a href="../changelog.txt" target="_blank"><font color="#CCCCCC">changelog</font></a></font></p>
<XML style=display:none> 
<?php ob_end_flush(); ?>
