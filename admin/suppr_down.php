<?
include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM liens_down WHERE id = '$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
$catcat = $disp[cat];
$requete  = "SELECT * FROM liens_down WHERE cat = '$disp[cat]'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
if ($nbre == '1')
{
?>
<script language="Javascript">
alert('Vous allez supprimer le dernier download de cette catégorie,\nla catégorie va donc être supprimée.');
</script>
<?
$requete  = "DELETE FROM cats_down WHERE id = '$catcat'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
}
$rqt = Mysql_Query("DELETE FROM liens_down WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le download n\'a pas été effacé !";
}
else
{
 $ALERT = "Le download a été effacé avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=down_liste&action=suppr';
</script>