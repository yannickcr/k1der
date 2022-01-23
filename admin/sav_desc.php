<?
include "secu.php";?><?

$source = "../images/dessins/titre/";

$src = "<?
\$desc = \"$desc\";
?>";

// ID du message, nom de fichier.
//$index = ($nbmes + 1);
$nomf = $nom;

// Creer le fichier.
$nfichier = fopen($source."$nomf", "w");
fclose($nfichier);

// Enregistrer la newz.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);
?>
<script language="Javascript">
alert('Description mise à jour avec succès');
window.location='../index.php?page=dessins_list';
</script>

