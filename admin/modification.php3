<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$titre      = addslashes(stripslashes(trim($titre)));
$signature  = addslashes(stripslashes(trim($signature)));
$email_sign = addslashes(stripslashes(strtolower(trim($email_sign))));
$nom_source = addslashes(stripslashes(trim($nom_source)));
$url_source = addslashes(stripslashes(trim($url_source)));
$path_image = addslashes(stripslashes(trim($path_image)));
$url_image  = addslashes(stripslashes(trim($url_image)));
$news       = nl2br($news);
$news       = addslashes(stripslashes(trim($news)));

$rqt = Mysql_Query("UPDATE $TBL_NEWS SET titre='$titre', signature='$signature', email_sign='$email_sign', source='$source', nom_source='$nom_source', url_source='$url_source', path_image='$path_image', url_image='$url_image', news='$news' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la news n° $id n\'a pas été mise à jour !";
}
else
{
 $ALERT = "La news n° $id a été mise à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>