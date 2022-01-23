<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$nb = 0;

if ($r1 != '') { $nb = $nb+1; }
if ($r2 != '') { $nb = $nb+1; }
if ($r3 != '') { $nb = $nb+1; }
if ($r4 != '') { $nb = $nb+1; }
if ($r5 != '') { $nb = $nb+1; }
if ($r6 != '') { $nb = $nb+1; }
if ($r7 != '') { $nb = $nb+1; }
if ($r8 != '') { $nb = $nb+1; }
if ($r9 != '') { $nb = $nb+1; }
if ($r10 != '') { $nb = $nb+1; }


$titre = addslashes($titre);
$r1 = addslashes($r1);
$r2 = addslashes($r2);
$r3 = addslashes($r3);
$r4 = addslashes($r4);
$r5 = addslashes($r5);
$r6 = addslashes($r6);
$r7 = addslashes($r7);
$r8 = addslashes($r8);
$r9 = addslashes($r9);
$r10 = addslashes($r10);



$requete  = "INSERT INTO sondages VALUES('','$titre','$nb','$r1','$r2','$r3','$r4','$r5','$r6','$r7','$r8','$r9','$r10','v1','v2','v3','v4','v5','v6','v7','v8','v9','v10')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('Sondage ajouté avec succès');
         window.location='../index.php?page=admin';
</script>