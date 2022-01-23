<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE phrases SET phrase='$phrase' WHERE phrase like '%$old_phrase%'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la phrase à la con n\'a pas été mis à jour !";
}
else
{
 $ALERT = "La phrase à la con a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>