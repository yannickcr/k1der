<?
include "secu.php";
?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE liens SET nom='$nom_lien', lien='$lien_lien', image='$image_lien' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le lien n\'a pas été mis à jour !";
}
else
{
 $ALERT = "Le lien a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>