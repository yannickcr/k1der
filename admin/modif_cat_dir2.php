<?
include "secu.php";
require("../config.php");

$dir = opendir("../images/gallerie");
while($fichier = readdir($dir)) {
	if ($fichier == $new_nom)
	{
	?>
	<script language="Javascript">
	alert('Cette cat�gorie existe d�j�');
	window.location='modif_cat_dir.php';
	</script>
	<?
	}
}
chdir("..");
chdir("images");
chdir("gallerie");
mkdir($new_nom);

if ($nbre != '0')
{
?>
<script language="Javascript">
alert('Une autre cat�gorie porte d�j� ce nom');
window.location='modif_cat.php?nom=nom_bak';
</script>
<?
}
else
{
$rqt = Mysql_Query("UPDATE cats_down SET nom='$nom' WHERE id='$id'");
}
if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la cat�gorie n\'a pas �t� mise � jour !";
}
else
{
 $ALERT = "La cat�gorie a �t� mise � jour avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>