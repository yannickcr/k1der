<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE events SET titre='$titre',text='$text', date='$annee$mois$jour' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l\'�v�nement n\'a pas �t� mis � jour !";
}
else
{
 $ALERT = "L\'�v�nement a �t� mis � jour avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
         window.location='../index.php?page=admin';
</script>