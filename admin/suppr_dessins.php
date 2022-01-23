<?
include "secu.php";
//$index = ($nbmes + 1);
$nomf = "../images/dessins/$fichier/desc/$image";

if (file_exists("../images/dessins/$fichier/desc/$image"))
{
unlink("../images/dessins/$fichier/desc/$image");
}

$image = str_replace(".txt", ".jpg", $image);

if (file_exists("../images/dessins/$fichier/piti/$image"))
{
unlink("../images/dessins/$fichier/piti/$image");
}

unlink("../images/dessins/$fichier/$image");

?>
<script language="Javascript">
alert('Dessin supprimé avec succès !');
window.location='../index.php?page=admin';
</script>