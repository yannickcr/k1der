<?
include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM cats_down WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la cat�gorie n\'a pas �t� effac�e !";
}
else
{
 $ALERT = "La cat�gorie a �t� effac�e avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=cat_liste&action=suppr';
</script>