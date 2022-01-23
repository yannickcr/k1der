<?
include "secu.php";?><? // VARIABLES.

// POUR BART, LE GESTIONNAIRE DE FICHIERS
$titre_site = "K1der"; // Titre du site.
$rbase = "c:/easyphp/www/"; // Repertoire reel sur le disque dur (avec slash de fin !!!).
$vbase = "http://localhost"; // URL de base virtuelle (sans slash de fin).
// FIN DE BART

$ext[0][0] = "Gestionnaire de fichiers";
$ext[0][1] = "admin\bart.php";

$ext[1][0] = "MyNewz 1.1";
$ext[1][1] = "mynewz.php";

sort($ext);

$mdp = "q3a"; // Mot de passe
$password = $HTTP_COOKIE_VARS["NitroAdminCook182"]; // Contenu du cookie

if (substr($dir, -2) == "//") {
	$dir = substr($dir, 0, -1);}
elseif (substr($dir, -1) == "/") {
	$dir = $dir;}
else {
	$dir = $dir."/";}
$dir = stripslashes($dir);
$avant = dirname($dir);
$avant = stripslashes($avant);

$dossiers = array(); // Tableau des dossiers.
$fichiers = array(); // Tableau des fichiers.
$d = 0;
$f = 0;
?>