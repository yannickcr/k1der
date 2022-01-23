<?
function get_meteo ($sortie)
{
if (eregi("National Ranking(.*)International Ranking", $sortie, $meteo))

$tablo = "<table width='100%' cellspacing='10' cellpadding='0' border='0'><tr><td width='50%' valign='top'><table width='100%' cellspacing='0' cellpadding='0' border='0' class='bigtable_box'><tr><td class='bigtable_header' colspan='2'><a class='bigtable_link' href=\"main.php?act=comp_classement2&amp;ligID=4&amp;pays=&amp;mois=6&amp;annee=2003&amp;start=1\">$meteo[0]";
$tablo = str_replace("src=\"img/","src=\"http://www.cyberleagues.org/img/",$tablo);
$tablo = str_replace("<td width=\"50%\" valign=\"top\">","",$tablo);
$tablo = str_replace("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"bigtable_box\">","",$tablo);
$tablo = str_replace("<tr>","",$tablo);
$tablo = str_replace("<td class=\"bigtable_header\" colspan=\"2\">","",$tablo);
$tablo = str_replace("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"bigtable_box\">","",$tablo);
$tablo = str_replace("<a class=\"bigtable_link\" href=\"main.php?act=comp_classement2&amp;ligID=4&amp;pays=INT&amp;mois=&amp;annee=&amp;start=1\">International Ranking","</Tr></TABLE>",$tablo);
$tablo = "<head>
<LINK href='cyberleague.css' type=text/css rel=stylesheet>
<LINK href='http://www.cyberleagues.org/css/fr_menu.css' type=text/css rel=stylesheet>
<LINK href='http://www.cyberleagues.org/css/fr_images5.css' type=text/css rel=stylesheet>
<LINK href='http://www.cyberleagues.org/css/chapitre5.css' type=text/css rel=stylesheet>
</head>
$tablo";
$tablo = str_replace("href=\"main.php?act=","target='_blank' href=\"http://www.cyberleagues.org/main.php?act=",$tablo);
$tablo = str_replace("National Ranking","&nbsp;Classement National",$tablo);

$tablo = str_replace("January","Janvier",$tablo);
$tablo = str_replace("February","Février",$tablo);
$tablo = str_replace("March","Mars",$tablo);
$tablo = str_replace("April","Avril",$tablo);
$tablo = str_replace("May","Mai",$tablo);
$tablo = str_replace("June","Juin",$tablo);
$tablo = str_replace("July","Juillet",$tablo);
$tablo = str_replace("August","Août",$tablo);
$tablo = str_replace("September","Septembre",$tablo);
$tablo = str_replace("October","Octobre",$tablo);
$tablo = str_replace("November","Novembre",$tablo);
$tablo = str_replace("December","Décembre",$tablo);

$tablo = str_replace("Â","",$tablo);

//$tabl = explode("<td class=\"bigtable_content3\" width=\"15\">",$tablo);
$tabl = explode("<td class=\"bigtable_content4\" width=\"15\">",$tablo);
$tabl = explode("</td>",$tabl[1]);
$clas = nl2br($tabl[0]);
$clas = str_replace("	","",$clas);
$clas = str_replace(" ","",$clas);
$clas = str_replace(" ","",$clas);
$clas = str_replace(".","",$clas);
$clas = str_replace("&nbsp;","",$clas);
$clas = str_replace("<br/>","",$clas);
$clas = str_replace(" ","",$clas);
$clas = intval($clas);

include "config.inc.php3";
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$date = date("d/m/Y");
 
$req = mysql_query("SELECT * FROM cl");
$disp = mysql_fetch_array($req);
$best = $disp[best_cl];
if ($clas <= $best && $clas != 0) { 
mysql_query("UPDATE cl SET best_cl='$clas'");
mysql_query("UPDATE cl SET best_cl_date='$date'");
}

$req = mysql_query("SELECT * FROM cl");
$disp = mysql_fetch_array($req);
$bad = $disp[bad_cl];
if ($clas >= $bad && $clas != 0) {
mysql_query("UPDATE cl SET bad_cl='$clas'");
mysql_query("UPDATE cl SET bad_cl_date='$date'");
}
//mysql_close();
$req = mysql_query("SELECT * FROM cl");
$disp = mysql_fetch_array($req);

return $tablo."<br><br><center>Meilleur classement: <b>".$disp[best_cl]."</b>èmes (le ".$disp[best_cl_date].")<br>Plus mauvais classement: <b>".$disp[bad_cl]."</b>èmes (le ".$disp[bad_cl_date].")</center>";
}

ob_start("get_meteo");
readfile("http://www.cyberleagues.org/main.php?act=team_info&ID_participant=19720&mstep=3");
ob_end_flush();
?>