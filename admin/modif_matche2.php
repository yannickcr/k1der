<?
include "secu.php";
?><?
require("../config.inc.php3");

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

include "allthematch2.php";

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
$orderdate = "$annee$mois$jour";


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

$comm = addslashes($comm);
$type = addslashes($type);
$loc = addslashes($loc);
$occ = addslashes($occ);

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE matches SET
mechants='$mechants',
site='$site',
irc='$irc',
score_k1='$score_k1',
score_me='$score_me',
jour='$jour',
mois='$mois2',
annee='$annee',
type='$type',
loc='$loc',
occ='$occ',
map='$map',
map2='$map2',
jou_k1='$jou_k1',
jou_k2='$jou_k2',
jou_k3='$jou_k3',
jou_k4='$jou_k4',
jou_k5='$jou_k5',
jou_m1='$jou_m1',
jou_m2='$jou_m2',
jou_m3='$jou_m3',
jou_m4='$jou_m4',
jou_m5='$jou_m5', 
orderdate='$orderdate',
hltv='$hltv',
score_map_k1_ct='$score_map_k1_ct',
score_map_k1_t='$score_map_k1_t',
score_map2_k1_ct='$score_map2_k1_ct',
score_map2_k1_t='$score_map2_k1_t',
score_map_me_ct='$score_map_me_ct',
score_map_me_t='$score_map_me_t',
score_map2_me_ct='$score_map2_me_ct',
score_map2_me_t='$score_map2_me_t',
det = '$det',
comm = '$comm'
WHERE id='$id'");

$caca = "_".$id."_";

$allthematch = addslashes($allthematch);

$requete  = "UPDATE ib_topics SET title='-=K1der=- vs $mechants' WHERE pinned='$caca' && forum_id='6'";
mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$requete  = "UPDATE ib_posts SET post='$allthematch' WHERE attach_hits='$caca' && forum_id='6'";
mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le matche n\'a pas été mis à jour !";
}
else
{
 $ALERT = "Le matche a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>