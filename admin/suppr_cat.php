<?
include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM cats_down WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la catégorie n\'a pas été effacée !";
}
else
{
 $ALERT = "La catégorie a été effacée avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=cat_liste&action=suppr';
</script>