<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE admin SET texte='$texte', lien='$lien', level='$level' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la page n\'a pas été mis à jour !";
}
else
{
 $ALERT = "La page a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin_pages';
</script>