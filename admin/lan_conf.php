<?
include("secu.php"); ?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE calendrier SET conf='1' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la LAN n\'a pas �t� valid�e !";
}
else
{
 $ALERT = "La LAN a �t� valid�e !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>