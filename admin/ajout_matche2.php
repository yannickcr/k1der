<? include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$date666 = date("U");

if ($k1 != 1) { $jou_k1 = $jou_k1_k; } else { $jou_k1 = $jou_k1_t; }
if ($k2 != 1) { $jou_k2 = $jou_k2_k; } else { $jou_k2 = $jou_k2_t; }
if ($k3 != 1) { $jou_k3 = $jou_k3_k; } else { $jou_k3 = $jou_k3_t; }
if ($k4 != 1) { $jou_k4 = $jou_k4_k; } else { $jou_k4 = $jou_k4_t; }
if ($k5 != 1) { $jou_k5 = $jou_k5_k; } else { $jou_k5 = $jou_k5_t; }

if (!ereg("http://",$site))
{
$site = "http://".$site;
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

if ($section == "cs")
{
$score_k1 = $score_map_k1_ct+$score_map_k1_t+$score_map2_k1_ct+$score_map2_k1_t;
$score_me = $score_map_me_ct+$score_map_me_t+$score_map2_me_ct+$score_map2_me_t;
$det = 1;
}
else
{
$score_map_k1_ct = 0;
$score_map_k1_t = 0;
$score_map2_k1_ct = 0;
$score_map2_k1_t = 0;
$score_map_me_ct = 0;
$score_map_me_t = 0;
$score_map2_me_ct = 0;
$score_map2_me_t = 0;
$det = 0;
}

require "allthematch.php";

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
	
$req5 = MYSQL_QUERY("SELECT * FROM ib_members WHERE name='$auteur'");
$disp5 = mysql_fetch_array($req5);

$jou_k1 = addslashes($jou_k1);
$jou_k2 = addslashes($jou_k2);
$jou_k3 = addslashes($jou_k3);
$jou_k4 = addslashes($jou_k4);
$jou_k5 = addslashes($jou_k5);

$jou_m1 = addslashes($jou_m1);
$jou_m2 = addslashes($jou_m2);
$jou_m3 = addslashes($jou_m3);
$jou_m4 = addslashes($jou_m4);
$jou_m5 = addslashes($jou_m5);

$mechants = addslashes($mechants);
$comm = addslashes($comm);
$type = addslashes($type);
$loc = addslashes($loc);
$occ = addslashes($occ);

$orderdate = "$annee$mois$jour";
$requete  = "INSERT INTO matches VALUES('','$mechants','$site','$irc','$score_k1','$score_me','$jour','$mois2','$annee','$type','$loc','$occ','$map','$map2','$score_map_k1_ct','$score_map_k1_t ','$score_map_me_ct','$score_map_me_t','$score_map2_k1_ct','$score_map2_k1_t','$score_map2_me_ct','$score_map2_me_t','$jou_k1','$jou_k2','$jou_k3','$jou_k4','$jou_k5','$jou_m1','$jou_m2','$jou_m3','$jou_m4','$jou_m5','$orderdate','$hltv','$det','$comm')";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());

$requete  = "SELECT * FROM matches ORDER BY id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$disp = mysql_fetch_array($req);
mkdir("../matches/fichiers/$disp[id]", 0777);

$cucu = "_".$disp[id]."_";

$requete3  = "INSERT INTO ib_topics VALUES ('', '-=K1der=- vs $mechants', '', 'open','0', '$disp5[id]','$date666', '0','$date666', 0, '$auteur', '$auteur','0','0','0','6','1','0','$cucu', '', '','0');";
$req3 = mysql_query($requete3) or die('Erreur SQL !<br/>'.$requete3.'<br/>'.mysql_error());
$req = MYSQL_QUERY("SELECT * FROM ib_topics ORDER BY tid DESC LIMIT 0, 1");
$disp = mysql_fetch_array($req);

$allthematch = addslashes($allthematch);

$requete2  = "INSERT INTO ib_posts VALUES('0','','','$disp5[id]','$auteur','0','1','127.0.0.1','$date666','','$allthematch','0','$disp[tid]','6','','$cucu','','','','1','')";

$req2 = mysql_query($requete2) or die('Erreur SQL !<br/>'.$requete2.'<br/>'.mysql_error());

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='6'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]+1;

mysql_query("UPDATE ib_forums SET last_title='-=K1der=- vs $mechants' ,topics='$topics' ,last_post='$date666' , last_poster_name='$auteur' WHERE id='6'");


?>
<script language="Javascript">
alert('Matche ajouté avec Succès');
         window.location='../index.php?page=admin';
</script>
