<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM phrases WHERE phrase like '%$phrase%'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la phrase � la con n\'a pas �t� effac�e !";
}
else
{
 $ALERT = "La phrase � la con a �t� effac�e avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=phrases_list&action=suppr';
</script>