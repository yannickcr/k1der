<?
include "secu.php";
?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE server SET url='$furl', descr='$descr' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le fichier n\'a pas �t� mis � jour !";
}
else
{
 $ALERT = "Le fichier a �t� mis � jour avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=ger_server_list_fichier&action=modif';
</script>