<? include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("UPDATE equipe SET next_match='non' WHERE kinder='$HTTP_COOKIE_VARS[gen]'");
?>
<script language="JavaScript">
alert("Tu as été supprimé dans la liste des joueurs disponibles pour le match");
window.location='../index.php?page=news'
</script>
