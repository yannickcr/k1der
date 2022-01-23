<?php

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
  else $nb_day = $nb_day_list[$month]; 
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
  if ($month=="") $month = date("n"); 
  
  $current_day = date("d"); 
  $first_day = date('w', mktime(0, 0, 0, $month, 1, $year))-1; 
  if ($first_day == -1) $first_day = 6; 
  $nb_day = getNbDay(date("n"), $year); 
  // affichage
  $moa = date("m");
  $moa2 = date("m");
  
$moa = str_replace("10","Octobre",$moa);
$moa = str_replace("11","Novembre",$moa);
$moa = str_replace("12","Décembre",$moa);
$moa = str_replace("01","Janvier",$moa);
$moa = str_replace("02","Février",$moa);
$moa = str_replace("03","Mars",$moa);
$moa = str_replace("04","Avril",$moa);
$moa = str_replace("05","Mai",$moa);
$moa = str_replace("06","Juin",$moa);
$moa = str_replace("07","Juillet",$moa);
$moa = str_replace("08","Août",$moa);
$moa = str_replace("09","Septembre",$moa);

$prevmonth = $month-1;
$prevyear = $year;

if ($prevmonth == 0)
{
$prevmonth = 12;
$prevyear = $year-1;
}

$nextmonth = $month+1;
$nextyear = $year;

if ($nextmonth == 13)
{
$nextmonth = 1;
$nextyear = $year+1;
}

$envir = "index.php?".getenv("QUERY_STRING");

if ($envir != "index.php?")
{
$envir = $envir."&";
}
$toovir = "&moa=".$month."&ane=".$year;
$envir = str_replace($toovir,'',$envir);
if(strlen($nextmonth)==1) $nextmonth='0'.$nextmonth; 
if(strlen($prevmonth)==1) $prevmonth='0'.$prevmonth; 
?>
<div class="caltxt">
<a style="float:left; " href="<?php echo $envir; ?>moa=<?php echo $prevmonth; ?>&amp;ane=<?php echo $prevyear; ?>">&lt;&lt;</a>
<a style="float:right; " href="<?php echo $envir; ?>moa=<?php echo $nextmonth; ?>&amp;ane=<?php echo $nextyear; ?>">&gt;&gt;</a>
<?php echo $moa; ?><br /><?php echo $year; ?>
</div>
<table>
	<tr>
		<th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th><th>Di</th>
	</tr>
	<tr>
<?php  
  // case vide avant 
  for ($i=0;$i<$first_day;$i++) { 
  print("<td> </td>"); 
  } 
  //cases renseignées 
  for ($i=0;$i<$nb_day;$i++) { 
  $first_day++; 
  $j = $i+1; 
  if ($first_day%7 == 1) { 
  print("<tr>"); 
  }
  
  $long = strlen($moa2); 
   
  if ($long < 2)
  {
  $moa2 = "0".$moa2;
  }
  
  $long2 = strlen($j); 
  if ($long2 < 2)
  {
  $j2 = "0".$j;
  }
  else
  {
  $j2 = $j;
  }
    
  $lan = '';
  $match = '';
  $event = '';
  $annif = '';
  $prev = '';
    
  $cetteannee = date("Y");
  $cemois = date("m");
  $cejour = date("d");
  
  require("config.inc.php3");
  $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
  mysql_select_db("$dbbase",$db) or Die("Base Down !");
  
  $requete1  = "SELECT * FROM lan_party WHERE orderdate ='$year$moa2$j2'";
  $req1 = mysql_query($requete1) or die('Erreur SQL !<br />'.$requete1.'<br />'.mysql_error());  
  $nbre1 =mysql_num_rows($req1);
  
  $thei = 1;
  $bar = "&lt;hr /&gt;";
  
  if ($nbre1 != 0)
  {
  $lan .= "&lt;b&gt;LAN Party :&lt;/b&gt;&lt;br /&gt;";
  while($disp1 = mysql_fetch_array($req1))
  {
  if ($thei != 1)
  {
  $lan .= "$bar$disp1[nom]&lt;br /&gt;";
  }
  else
  {
  $lan .= "$disp1[nom]&lt;br /&gt;";
  }
  $thei++;
  }
  }


  $requete2  = "SELECT * FROM next_matches WHERE orderdate ='$year$moa2$j2' ORDER BY date ASC";
  $req2 = mysql_query($requete2) or die('Erreur SQL !<br />'.$requete2.'<br />'.mysql_error());  
  $nbre2 =mysql_num_rows($req2);

  $thei = 1;
  $bar = "&lt;hr /&gt;";

  if ($nbre2 != 0)
  {
  $match .= "&lt;b&gt;Match :&lt;/b&gt;&lt;br /&gt;";
  while($disp2 = mysql_fetch_array($req2))
  {
  if ($thei != 1)
  {
  $match .= $bar."Contre les $disp2[mechants] à $disp2[heure]&lt;br /&gt;";
  }
  else
  {
  $match .= "Contre les $disp2[mechants] à $disp2[heure]&lt;br /&gt;";
  }
  $thei++;
  }
  }
  
  $requete6  = "SELECT * FROM matches WHERE orderdate ='$year$moa2$j2'";
  $req6 = mysql_query($requete6) or die('Erreur SQL !<br />'.$requete6.'<br />'.mysql_error());  
  $nbre6 =mysql_num_rows($req6);

  $thei = 1;
  $bar = "&lt;hr /&gt;";

  if ($nbre6 != 0)
  {
  $prev .= "&lt;b&gt;Match :&lt;/b&gt;&lt;br/&gt;";
  while($disp6 = mysql_fetch_array($req6))
  {
  
  if ($disp6['score_k1'] > $disp6['score_me'])
  {
  $txt = "Win";
  $back = "#009900";
  }
  else if ($disp6['score_k1'] < $disp6['score_me'])
  {
  $txt = "Lose";
  $back = "#FF0000";
  }
  else
  {
  $txt = "Draw";
  $back = "#0000FF";
  }  
  if ($thei != 1)
  {
  $prev .= $bar.addslashes($disp6['mechants'])." | &lt;span class=\'bulle".strtolower($txt)."\'&gt;".$txt."&lt;/span&gt;&nbsp;&nbsp;$disp6[score_k1]/$disp6[score_me]&lt;br /&gt;";
  }
  else
  {
  $prev .= addslashes($disp6['mechants'])." | &lt;span class=\'bulle".strtolower($txt)."\'&gt;".$txt."&lt;/span&gt;&nbsp;&nbsp;$disp6[score_k1]/$disp6[score_me]&lt;br /&gt;";
  }
  $thei++;
  }
  }
  
  $requete3  = "SELECT * FROM events WHERE date ='$year$moa2$j2'";
  $req3 = mysql_query($requete3) or die('Erreur SQL !<br />'.$requete3.'<br />'.mysql_error());  
  $nbre3 =mysql_num_rows($req3);

  $thei = 1;
  $bar = "&lt;hr /&gt;";
  
  if ($nbre3 != 0)
  {
  $event = '';
  $oldtitre = '';
  $disp3['titre'] = '';

  while($disp3 = mysql_fetch_array($req3))
  {
  if ($disp3['titre'] == '') { $disp3['titre'] = "Evènement"; }
  if ($disp3['titre'] != $oldtitre)
  {
  $disp3['titre'] =str_replace("'","\'",$disp3['titre']);
  $event .= "&lt;b&gt;".$disp3['titre']." :&lt;/b&gt;&lt;br /&gt;";
  $oldtitre = $disp3['titre'];
  }
  else
  {
  $event .= "$bar";
  }
  $disp3['text'] =str_replace("'","\'",$disp3['text']);
  $event .= $disp3['text']."&lt;br /&gt;";
  $thei++;
  }
  }

  $requete4  = "SELECT * FROM equipe WHERE age2 ='$moa2$j2'";
  $req4 = mysql_query($requete4) or die('Erreur SQL !<br />'.$requete4.'<br />'.mysql_error());  
  $nbre4 =mysql_num_rows($req4);
  
  $thei = 1;
  $bar = "&lt;hr /&gt;";

  if ($nbre4 != 0)
  {  
  $annif .= "&lt;b&gt;Anniversaire :&lt;/b&gt;&lt;br/&gt;";
  while($disp4 = mysql_fetch_array($req4))
  {
  
  $ziday = "$year$moa2$j2";
  $sonage = $ziday-$disp4['age'];
  $sonage = $sonage/10000;
  $sonage = (int)$sonage;  
  
  if ($thei != 1)
  {
  if ("$year$moa2$j2" < "$cetteannee$cemois$cejour")
  {
  $annif .= $bar."$disp4[kinder] à ".$sonage." ans !&lt;br/&gt;";
  }
  else
  {
  $annif .= $bar."$disp4[kinder] à ".$sonage." ans !&lt;br /&gt;";
  }
  }
  else
  {
  if ("$year$moa2$j2" < "$cetteannee$cemois$cejour")
  {
  $annif .= "$disp4[kinder] a ".$sonage." ans !&lt;br/&gt;";
  }
  else
  {
  $annif .= "$disp4[kinder] à ".$sonage." ans !&lt;br/&gt;";
  }
  }
  $thei++;
  }
  }  

  $requete5  = "SELECT * FROM anniv WHERE date ='$moa2$j2'";
  $req5 = mysql_query($requete5) or die('Erreur SQL !<br />'.$requete5.'<br />'.mysql_error());  
  $nbre5 =mysql_num_rows($req5);
 // echo "$nbre5";
  $thei = 1;
  $bar = "&lt;hr /&gt;";

  if ($nbre5 != 0)
  {  
  $annif .= "&lt;b&gt;Anniversaire :&lt;/b&gt;&lt;br/&gt;";

  while($disp5 = mysql_fetch_array($req5))
  {
  
  $nes = $disp5['an'].$disp5['date'];
  $ziday = "$year$moa2$j2";
  $sonage = $ziday-$nes;
  $sonage = $sonage/10000;
  $sonage = (int)$sonage;  
  $disp5['nom'] =str_replace("'","\'",$disp5['nom']);
  if ($thei != 1)
  {
  $annif .= $bar.$disp5['nom']." à ".$sonage." ans !&lt;br /&gt;";
  }
  else
  {
  $annif .= $disp5['nom']." à ".$sonage." ans !&lt;br /&gt;";
  }
  $thei++;
  }
  }  


  if ("$year$moa2$j2" == "$cetteannee$cemois$cejour")
  {
  $lejour = "<span class=\"aujourdhui\">$j</span>";
  }
  else
  {
  $lejour = $j;
  }

  if (($nbre1 == 0) && ($nbre2 == 0) && ($nbre3 == 0) && ($nbre4 == 0) && ($nbre5 == 0) && ($nbre6 == 0)) print("<td class=\"jourvide\">$lejour</td>\n"); 
  else print("<td class=\"jourplein\"><a onmouseover=\"affiche('','$lan$prev$match$event$annif')\" onmouseout=\"affiche('cache')\">$lejour</a></td>\n"); 
  
  if ($first_day%7 == 0) { 
  print("</tr>"); 
  } 
  } 
  while (($first_day%7) != 0){ 
  $first_day++; 
  print("<td> </td>"); 
  } 
  print("</tr></table>"); 
}
if (!isset($_GET['moa'])) $moa = date("n");
else $moa = $_GET['moa'];
if (!isset($_GET['ane'])) $ane = date("Y");
else $ane = $_GET['ane'];
calendriercalendrier($moa , $ane);
?>
