<?

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
  $moa = $month;
  $moa2 = $month;
  
$moa = str_replace("10","Octobre",$moa);
$moa = str_replace("11","Novembre",$moa);
$moa = str_replace("12","Décembre",$moa);
$moa = str_replace("1","Janvier",$moa);
$moa = str_replace("2","Février",$moa);
$moa = str_replace("3","Mars",$moa);
$moa = str_replace("4","Avril",$moa);
$moa = str_replace("5","Mai",$moa);
$moa = str_replace("6","Juin",$moa);
$moa = str_replace("7","Juillet",$moa);
$moa = str_replace("8","Août",$moa);
$moa = str_replace("9","Septembre",$moa);

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
$toovir = "moa=$month&amp;ane=$year&";
$envir = str_replace($toovir,'',$envir);

  print("<center><table width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">");
  print("<tr>");
  print("<td><div align='center'><strong><font size='2' face='Verdana, Arial, Helvetica, sans-serif'><a href=\"".$envir."moa=$prevmonth&amp;ane=$prevyear\">&lt;&lt;</a>&nbsp;&nbsp;</font></strong></div></td>");
  print("<td width='100'><div align='center'><strong><font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$moa<br/>$year</font></strong></div></td>");
  print("<td><div align='center'><strong><font size='2' face='Verdana, Arial, Helvetica, sans-serif'>&nbsp;&nbsp;<a href=\"".$envir."moa=$nextmonth&amp;ane=$nextyear\">&gt;&gt;</a></font></strong></div></td>");
  print("</tr>");
  print("</table>");
  print("<table width='150' style=\"border-width:0px\" >"); 
  print("<tr><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Lu</font></td><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Ma</font></td><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Me</font></td><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Je</font></td><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Ve</font></td><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Sa</font></td><td><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">Di</font></td></tr>"); 
  
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
  $req1 = mysql_query($requete1) or die('Erreur SQL !<br/>'.$requete1.'<br/>'.mysql_error());  
  $nbre1 =mysql_num_rows($req1);
  
  $thei = 1;
  $bar = "<center><hr size=\'1\' noshade color=#000000 width=50></center>";
  
  if ($nbre1 != 0)
  {
  $lan .= "<b>LAN Party :</b><br/>";
  while($disp1 = mysql_fetch_array($req1))
  {
  if ($thei != 1)
  {
  $lan .= "$bar$disp1[nom]<br/>";
  }
  else
  {
  $lan .= "$disp1[nom]<br/>";
  }
  $thei++;
  }
  }


  $requete2  = "SELECT * FROM next_matches WHERE orderdate ='$year$moa2$j2' ORDER BY date ASC";
  $req2 = mysql_query($requete2) or die('Erreur SQL !<br/>'.$requete2.'<br/>'.mysql_error());  
  $nbre2 =mysql_num_rows($req2);

  $thei = 1;
  $bar = "<center><hr size=\'1\' noshade=\'noshade\' color=\'#000000\' width=\'50\'></center>";

  if ($nbre2 != 0)
  {
  $match .= "<b>Match :</b><br/>";
  while($disp2 = mysql_fetch_array($req2))
  {
  if ($thei != 1)
  {
  $match .= $bar."Contre les $disp2[mechants] à $disp2[heure]<br/>";
  }
  else
  {
  $match .= "Contre les $disp2[mechants] à $disp2[heure]<br/>";
  }
  $thei++;
  }
  }
  
  $requete6  = "SELECT * FROM matches WHERE orderdate ='$year$moa2$j2'";
  $req6 = mysql_query($requete6) or die('Erreur SQL !<br/>'.$requete6.'<br/>'.mysql_error());  
  $nbre6 =mysql_num_rows($req6);

  $thei = 1;
  $bar = "<center><hr size=\'1\' noshade=\'noshade\' color=\'#000000\' width=\'50\'></center>";

  if ($nbre6 != 0)
  {
  $prev .= "&lt;b&gt;Match :&lt;/b&gt;&lt;br/&gt;";
  while($disp6 = mysql_fetch_array($req6))
  {
  
  if ($disp6[score_k1] > $disp6[score_me])
  {
  $txt = "Win";
  $back = "#009900";
  }
  else if ($disp6[score_k1] < $disp6[score_me])
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
  $prev .= $bar.addslashes($disp6[mechants])." | &lt;font color=\'$back\'&gt;&lt;b&gt;$txt&lt;/b&gt;&lt;/font&gt;&nbsp;&nbsp;$disp6[score_k1]/$disp6[score_me]&lt;br/&gt;";
  }
  else
  {
  $prev .= addslashes($disp6[mechants])." | &lt;font color=\'$back\'&gt;&lt;b&gt;$txt&lt;/b&gt;&lt;/font&gt;&nbsp;&nbsp;$disp6[score_k1]/$disp6[score_me]&lt;br/&gt;";
  }
  $thei++;
  }
  }
  
  $requete3  = "SELECT * FROM events WHERE date ='$year$moa2$j2'";
  $req3 = mysql_query($requete3) or die('Erreur SQL !<br/>'.$requete3.'<br/>'.mysql_error());  
  $nbre3 =mysql_num_rows($req3);

  $thei = 1;
  $bar = "<center><hr size=\'1\' noshade=\'noshade\' color=\'#000000\' width=\'50\'></center>";
  
  if ($nbre3 != 0)
  {
  $event = '';
  $oldtitre = '';
  $disp3[titre] = '';

  while($disp3 = mysql_fetch_array($req3))
  {
  if ($disp3[titre] == '') { $disp3[titre] = "Evènement"; }
  if ($disp3[titre] != $oldtitre)
  {
  $disp3[titre] =str_replace("'","\'",$disp3[titre]);
  $event .= "<b>$disp3[titre] :</b><br/>";
  $oldtitre = $disp3[titre];
  }
  else
  {
  $event .= "$bar";
  }
  $disp3[text] =str_replace("'","\'",$disp3[text]);
  $event .= "$disp3[text]<br/>";
  $thei++;
  }
  }

  $requete4  = "SELECT * FROM equipe WHERE age2 ='$moa2$j2'";
  $req4 = mysql_query($requete4) or die('Erreur SQL !<br/>'.$requete4.'<br/>'.mysql_error());  
  $nbre4 =mysql_num_rows($req4);
  
  $thei = 1;
  $bar = "<center><hr size=\'1\' noshade=\'noshade\' color=\'#000000\' width=\'50\'></center>";

  if ($nbre4 != 0)
  {  
  $annif .= "&lt;b&gt;Anniversaire :&lt;/b&gt;&lt;br/&gt;";
  while($disp4 = mysql_fetch_array($req4))
  {
  
  $ziday = "$year$moa2$j2";
  $sonage = $ziday-$disp4[age];
  $sonage = $sonage/10000;
  $sonage = (int)$sonage;  
  
  if ($thei != 1)
  {
  if ("$year$moa2$j2" < "$cetteannee$cemois$cejour")
  {
  $annif .= $bar."Oua c bien $disp4[kinder] est grand ".$sonage." ans now !&lt;br/&gt;";
  }
  else
  {
  $annif .= $bar."$disp4[kinder] à ".$sonage." ans !<br/>";
  }
  }
  else
  {
  if ("$year$moa2$j2" < "$cetteannee$cemois$cejour")
  {
  $annif .= "Oua c bien $disp4[kinder] est grand ".$sonage." ans now !&lt;br/&gt;";
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
  $req5 = mysql_query($requete5) or die('Erreur SQL !<br/>'.$requete5.'<br/>'.mysql_error());  
  $nbre5 =mysql_num_rows($req5);
 // echo "$nbre5";
  $thei = 1;
  $bar = "<center><hr size=\'1\' noshade=\'noshade\' color=\'#000000\' width=\'50\'></center>";

  if ($nbre5 != 0)
  {  
  $annif .= "&lt;b&gt;Anniversaire :&lt;/b&gt;&lt;br/&gt;";

  while($disp5 = mysql_fetch_array($req5))
  {
  
  $nes = "$disp5[an]$disp5[date]";
  $ziday = "$year$moa2$j2";
  $sonage = $ziday-$nes;
  $sonage = $sonage/10000;
  $sonage = (int)$sonage;  
  $disp5[nom] =str_replace("'","\'",$disp5[nom]);
  if ($thei != 1)
  {
  $annif .= $bar."$disp5[nom] à ".$sonage." ans !<br/>";
  }
  else
  {
  $annif .= "$disp5[nom] à ".$sonage." ans !<br/>";
  }
  $thei++;
  }
  }  


  if ("$year$moa2$j2" == "$cetteannee$cemois$cejour")
  {
  $lejour = "<font color=\"#CC0000\"><b>$j</b></font>";
  }
  else
  {
  $lejour = $j;
  }

  if (($nbre1 == 0) && ($nbre2 == 0) && ($nbre3 == 0) && ($nbre4 == 0) && ($nbre5 == 0) && ($nbre6 == 0))
  {
  $back = "#FFFFFF";
  print("<td bgcolor='$back' style=\"border:1px solid #000000\" align=\"center\"><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\">$lejour</font></td>"); 
  }
  else 
  {
  $back = "#FFE0E0";
  print("<td bgcolor='$back' style=\"border:1px solid #000000\" align=\"center\"><font face='Verdana, Arial, Helvetica, sans-serif' size=\"1\"><a onmouseover=\"affiche('','&lt;font face=Verdana, Arial, Helvetica, sans-serif size=\'1\'&gt;$lan$prev$match$event$annif&lt;/font&gt;')\" onmouseout=\"affiche('cache')\">$lejour</a></font></td>\n"); 
  }
  
  if ($first_day%7 == 0) { 
  print("</tr>"); 
  } 
  } 
  while (($first_day%7) != 0){ 
  $first_day++; 
  print("<td> </td>"); 
  } 
  print("</tr></table></center>"); 
}
if ($moa == '')
{
$moa = date("n");
}
if ($ane == '')
{
$ane = date("Y");
}
calendriercalendrier($moa , $ane);
?>
