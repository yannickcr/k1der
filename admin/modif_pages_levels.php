<? include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

while($i >= '0')
{
mysql_query("UPDATE admin SET level='$nivo[$i]' WHERE id='$id[$i]'");
$i--;
}

$ALERT = "Les Levels des pages ont été mis à jour avec succès !";
?>
<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>