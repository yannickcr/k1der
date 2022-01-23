<? include "secu.php"; ?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("DELETE FROM dossiers_p WHERE id_dossier='$id' && page='$idp'");

$ALERT = "La page a été effacé avec succès !";

$req = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' ORDER BY page DESC");
$nbr = MYSQL_NUM_ROWS($req);
while ($disp = mysql_fetch_array($req))
{
mysql_query("UPDATE dossiers_p SET page='$nbr' WHERE id='$disp[id]'");
$nbr--;
}

?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=ger_dossier&id=<? echo $id; ?>';
</script>