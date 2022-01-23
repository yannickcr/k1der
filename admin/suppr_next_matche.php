<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = mysql_query("DELETE FROM next_matches WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le prochain match n\'a pas été effacé !";
}
else
{
 $ALERT = "Le prochain match a été effacé avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>