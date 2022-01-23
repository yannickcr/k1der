<? $level = "10"; include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM equipe ORDER by kinder");
$i=0;
while($disp = mysql_fetch_array($req))
{
$i++;
mysql_query("UPDATE equipe SET level='$nivo[$i]' WHERE id='$id[$i]'");
}

$ALERT = "Les Levels ont été mis à jour avec succès !";
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>