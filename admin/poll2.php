<?
include "secu.php";?><?

require("config.php");

$source = "../";

$src = "0|0|0|0|0|";

// ID du message, nom de fichier.
//$index = ($nbmes + 1);
$nomf = "votes.dat";

// Creer le fichier.
$nfichier = fopen($source."$nomf", "w");
fclose($nfichier);

// Enregistrer la newz.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);

?>
<script language="Javascript">
alert('Résultats ré-initialisés');
window.location='../index.php?page=admin';
</script>