<?
include "secu.php";?><?

require("config.php");

$source = "../";

$src = "$r1
$r2
$r3
$r4
$r5";

// ID du message, nom de fichier.
//$index = ($nbmes + 1);
$nomf = "data.dat";

// Creer le fichier.
$nfichier = fopen($source."$nomf", "w");
fclose($nfichier);

// Enregistrer la newz.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);

?>
