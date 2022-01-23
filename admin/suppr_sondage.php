<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM sondages WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le sondage n\'a pas été effacé !";
}
else
{
 $ALERT = "Le sondage a été effacé avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=sondage_liste&action=suppr';
</script>