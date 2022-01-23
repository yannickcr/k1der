<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM server WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le fichier n\'a pas été effacé de la liste !";
}
else
{
 $ALERT = "Le fichier a été effacé avec succès de la liste !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=ger_server_list_fichier&action=suppr';
</script>