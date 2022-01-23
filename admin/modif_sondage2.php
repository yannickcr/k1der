<?
include "secu.php";
?>
<?
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
mysql_query("UPDATE config SET valeur='' WHERE nom='badip'");

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


$rqt = Mysql_Query("UPDATE sondages SET titre='$titre', nb='$nb', r1='$r1', r2='$r2', r3='$r3', r4='$r4', r5='$r5', r6='$r6', r7='$r7', r8='$r8', r9='$r9', r10='$r10' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le sondage n\'a pas été mis à jour !";
}
else
{
 $ALERT = "Le sondage a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=sondage_liste&action=modif';
</script>