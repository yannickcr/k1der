<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM liens WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le lien n\'a pas été effacé !";
}
else
{
 $ALERT = "Le lien a été effacé avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=liens_list&action=suppr';
</script>