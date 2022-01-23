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

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la catégorie n\'a pas été effacée !";
}
else
{
 $ALERT = "La catégorie a été effacée avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=cat_list&action=suppr';
</script>