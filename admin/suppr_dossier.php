<? include "secu.php"; ?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("DELETE FROM dossiers WHERE id='$id'");
mysql_query("DELETE FROM dossiers_p WHERE id_dossier='$id'");

$ALERT = "Le dossier a �t� effac� avec succ�s !";

?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=ger_dossiers';
</script>