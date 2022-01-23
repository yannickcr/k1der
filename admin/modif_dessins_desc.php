<?
include "secu.php";
//$index = ($nbmes + 1);
$nomf = "../images/dessins/$fichier/desc/$image";
$src = "<?
\$desc = \"$desc\";
?>";

// Creer le fichier.
$nfichier = fopen("$nomf", "w");
fclose($nfichier);

// Enregistrer la newz.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);

?>
<script language="Javascript">
alert('Description mise à jour avec succès !');
window.location='../index.php?page=admin';
</script>