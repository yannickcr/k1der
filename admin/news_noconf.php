<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM mynewsinfos WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la news n\'a pas �t� rejet�e !";
}
else
{
 $ALERT = "La news a �t� rejet�e !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>