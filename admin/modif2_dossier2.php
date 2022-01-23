<? include "secu.php"; ?>
<?
if (($old_conf == 0) && ($conf == 1))
{
$date = date("d/m/Y");
}

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("UPDATE dossiers SET titre='$titre', resume='$resume', date='$date', conf='$conf', image='$image' WHERE id='$id'");

?>
<script language="Javascript">
alert('Dossier modifié avec succès');
window.location='../index.php?page=ger_dossier&id=<? echo $id; ?>';
</script>