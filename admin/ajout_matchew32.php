<? include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$date666 = date("U");

function DirTri($rep,$tri)
{
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
    if($File != "." && $File != ".." && $File != "index.htm")
    {
      $Array[] = "$File";
    }
    $i++;
  }
  closedir($dir);

  if($tri == 'DESC'){
    rsort($Array);
  }else{
    sort($Array);
  }
  $Max = count($Array);
  $texto = "Fichiers associés:";
  for($i = 0; $i != $Max; $i++){
  echo "<td valign='top' nowrap><font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$texto&nbsp;</font></td><td width='80%' colspan='-1' valign='top'><font size='2' face='Verdana, Arial, Helvetica, sans-serif'><a href=\"$rep"."$Array[$i]\">$Array[$i]</a></font></td></tr><tr>";
  $texto = "";
  }
  //echo "<br><br>".$Max." fichier(s)" ;
}

if($type != 'LAN Arena')
{
$loc = $paslan;
}
else
{
$loc = $lan;
}

$mois2 = $mois;

if ($style == "1vs1")
{
$gentil1 = $gentil1_1
}

//require "allthematch.php";

if ($mois =="Janvier")
	{
	$mois = '01';
	}
if ($mois =="Février")
	{
	$mois = '02';
	}
if ($mois =="Mars")
	{
	$mois = '03';
	}
if ($mois =="Avril")
	{
	$mois = '04';
	}
if ($mois =="Mai")
	{
	$mois = '05';
	}
if ($mois =="Juin")
	{
	$mois = '06';
	}
if ($mois =="Juillet")
	{
	$mois = '07';
	}
if ($mois =="Août")
	{
	$mois = '08';
	}
if ($mois =="Septembre")
	{
	$mois = '09';
	}
if ($mois =="Octobre")
	{
	$mois = '10';
	}
if ($mois =="Novembre")
	{
	$mois = '11';
	}
if ($mois =="Décembre")
	{
	$mois = '12';
	}
	
/*
$req5 = MYSQL_QUERY("SELECT * FROM ib_members WHERE name='$auteur'");
$disp5 = mysql_fetch_array($req5);
*/

$orderdate = "$annee$mois$jour";
$requete  = "INSERT INTO matches_war3 VALUES('','$gentil1','$gentil2','$gentil3','$mechant1','$mechant2','$mechant3','$style','$jour','$mois2','$annee','$manche','$win1','$k_m1_j1','$k_m1_j2','$k_m1_j3','$m_m1_j1','$m_m1_j2','$m_m1_j3','$map1','$win2','$k_m2_j1','$k_m2_j2','$k_m2_j3','$m_m2_j1','$m_m2_j2','$m_m2_j3','$map2','$win3','$k_m3_j1','$k_m3_j2','$k_m3_j3','$m_m3_j1','$m_m3_j2','$m_m3_j3','$map3','$type','$loc','$occ','$orderdate')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

$requete  = "SELECT * FROM matches_war3 ORDER BY id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
mkdir("../matches_war3/fichiers/$disp[id]", 0777);

/*

$cucu = "_".$disp[id]."_";

$requete3  = "INSERT INTO ib_topics VALUES ('', '-=K1der=- vs $mechants', '', 'open','0', '$disp5[id]','$date666', '0','$date666', 0, '$auteur', '$auteur','0','0','0','6','1','0','$cucu', '', '','0');";
$req3 = mysql_query($requete3) or die('Erreur SQL !<br>'.$requete3.'<br>'.mysql_error());
$req = MYSQL_QUERY("SELECT * FROM ib_topics ORDER BY tid DESC LIMIT 0, 1");
$disp = mysql_fetch_array($req);

$requete2  = "INSERT INTO ib_posts VALUES('0','','','$disp5[id]','$auteur','0','1','127.0.0.1','$date666','','$allthematch','0','$disp[tid]','6','','$cucu','','','','1','')";

$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='6'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]+1;

mysql_query("UPDATE ib_forums SET last_title='-=K1der=- vs $mechants' ,topics='$topics' ,last_post='$date666' , last_poster_name='$auteur' WHERE id='6'");

*/
?>
<script language="Javascript">
alert('Matche ajouté avec Succès');
         window.location='../index.php?page=admin';
</script>
