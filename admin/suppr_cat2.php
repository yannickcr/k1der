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

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la cat�gorie n\'a pas �t� effac�e !";
}
else
{
 $ALERT = "La cat�gorie a �t� effac�e avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=cat_list&action=suppr';
</script>