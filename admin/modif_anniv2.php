<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

if (strlen($jour) == '1')
{
$jour = "0".$jour;
}

$rqt = Mysql_Query("UPDATE anniv SET nom='$nom',date='$mois$jour', an='$annee' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l\'anniversaire n\'a pas été mis à jour !";
}
else
{
 $ALERT = "L\'anniversaire a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
         window.location='../index.php?page=admin';
</script>