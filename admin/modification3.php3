<?
include "secu.php";?><?
$user = ucfirst($HTTP_COOKIE_VARS[gen]);
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$nom          = addslashes(stripslashes(trim($nom)));
$pass       = addslashes(stripslashes(trim($pass)));

$rqt = Mysql_Query("UPDATE equipe SET pass='$pass' WHERE kinder='$user'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le mot de passe de $user n'a pas été mis à jour !";
}
else
{
 $ALERT = "Le Mot de Passe du joueur $user a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>
