<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM events WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l\'�v�nement n\'a pas �t� effac� !";
}
else
{
 $ALERT = "L\'�v�nement a �t� effac� avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=event_liste&action=suppr';
</script>