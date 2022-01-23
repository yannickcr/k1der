<?
include "secu.php";
?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("UPDATE sondages SET titre='$titre', v1='', v2='', v3='', v4='', v5='', v6='', v7='', v8='', v9='', v10='' WHERE id='$id'");

?>

<script language="Javascript">
alert('Résultats ré-initialisés');
window.location='../index.php?page=admin';
</script>