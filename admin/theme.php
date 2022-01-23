<?
include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE config SET valeur='$theme' WHERE nom='theme'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le Theme n\'a pas été changé à jour !";
}
else
{
 $ALERT = "Le Theme a été changé !";
}
?>
<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>
