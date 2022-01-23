<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM phrases WHERE phrase like '%$phrase%'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la phrase à la con n\'a pas été effacée !";
}
else
{
 $ALERT = "La phrase à la con a été effacée avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=phrases_list&action=suppr';
</script>