<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE phrases SET phrase='$phrase' WHERE phrase like '%$old_phrase%'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la phrase � la con n\'a pas �t� mis � jour !";
}
else
{
 $ALERT = "La phrase � la con a �t� mis � jour avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>