<?
include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("UPDATE config SET valeur='$charte_gen' WHERE nom='charte_gen'");
mysql_query("UPDATE config SET valeur='$charte_cs' WHERE nom='charte_cs'");
mysql_query("UPDATE config SET valeur='$charte_war3' WHERE nom='charte_war3'");
mysql_query("UPDATE config SET valeur='$nb_cs' WHERE nom='nb_cs'");
mysql_query("UPDATE config SET valeur='$nb_war3' WHERE nom='nb_war3'");
mysql_query("UPDATE config SET valeur='$type_cs' WHERE nom='type_cs'");
mysql_query("UPDATE config SET valeur='$type_war3' WHERE nom='type_war3'");

$ALERT = "La page de recrutement a été mise à jour avec succès !";
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>