<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$jour          = addslashes(stripslashes(trim($jour)));
$mois       = addslashes(stripslashes(trim($mois)));
$annee       = addslashes(stripslashes(trim($annee)));
$nom       = addslashes(stripslashes(trim($nom)));
$lanlien       = addslashes(stripslashes(trim($lanlien)));

$rqt = Mysql_Query("UPDATE lan SET jour='$jour', mois='$mois', annee='$annee', nom='$nom', lanlien='$lanlien', duree='$duree'");
$rqt = Mysql_Query("UPDATE equipe SET statut='pas'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la LAN Arena n'a pas �t� mis � jour !";
 $ALERT2 = "Statuts des joueurs conserv�s";
}
else
{
 $ALERT = "LAN Arena mise � jour avec succ�s !";
 $ALERT2 = "Statuts des joueurs remis � \'Sais Pas\'";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
alert('<? echo $ALERT2 ?>');
window.location='../index.php?page=admin';
</script>
