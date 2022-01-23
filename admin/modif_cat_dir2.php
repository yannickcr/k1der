<?
include "secu.php";
require("../config.php");

$dir = opendir("../images/gallerie");
while($fichier = readdir($dir)) {
	if ($fichier == $new_nom)
	{
	?>
	<script language="Javascript">
	alert('Cette catégorie existe déjà');
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
alert('Une autre catégorie porte déjà ce nom');
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
 $ALERT = "Une erreur s\'est produite, la catégorie n\'a pas été mise à jour !";
}
else
{
 $ALERT = "La catégorie a été mise à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>