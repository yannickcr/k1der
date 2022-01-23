<?
include "secu.php";?><?

require("admin/config.php");

$source = "";

$src = "<?

\$root_dir = \"$dossier\";

?>

";

// ID du message, nom de fichier.
//$index = ($nbmes + 1);
$nomf = "root.txt";

// Creer le fichier.
$nfichier = fopen($source."$nomf", "w");
fclose($nfichier);

// Enregistrer la newz.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);

?>
