<?
include "secu.php";?><?
require("../config.inc.php3");

$user = ucfirst($HTTP_COOKIE_VARS[gen]);

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE equipe SET statut='$statut' WHERE kinder='$user'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l\'inscription � �chou�e !";
}
else
{
 $ALERT = "Inscription r�alis�e avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>
