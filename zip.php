<?php
// appel de la classe
require_once('pclzip.lib.php');
@ini_set("memory_limit",'100M');
@ini_set("max_execution_time",'6000');
	
// Suppression de l'ancienne archive
$dir = opendir("/home/.sites/65/site113/web/lan");
while($fichier = readdir($dir)) {
if (ereg(".zip",$fichier)) @unlink($fichier);
	if ($fichier == "lan/infos_lan.txt") @unlink($fichier);
}
closedir($dir);

// On détermine le nom du fichier par rapport à celui de la Lan

include("config.inc.php3");

$Caracs = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
                "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
                "Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
                "Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
                "Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
                "Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
                "Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
                "Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
                "à" => "a", "á" => "a", "â" => "a", "ã" => "a",
                "ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
                "è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
                "ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
                "ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
                "ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
                "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
                "ý" => "y", "ÿ" => "y", "@" => "a");
				
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
$requete  = "SELECT * FROM lan_party WHERE id='".$_GET['id']."'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
$nom = str_replace(" ","_",$disp[nom]);
$nom = str_replace("-","_",$nom);
$nom = str_replace("[","",$nom);
$nom = str_replace("]","",$nom);
$nom = str_replace("'","",$nom);
$nom = strtr(strtolower($nom), $Caracs);

$archive = "photos_".$nom.".zip";

// Tan qu'a faire on fait le txt à mettre dans l'archive
$txt = 
"Infos sur la Lan Party".chr(13)."
-----------------------".chr(13)."
".chr(13)."
Nom : ".$disp[nom].chr(13)."
".chr(13)."
Date : ".$disp[jour]." ".$disp[mois]." ".$disp[annee].chr(13)."
Ville : ".$disp[loc].chr(13)."
Site Internet : ".$disp[url].chr(13)."
".chr(13)."
-----------------------------------------------".chr(13)."
Ce fichier à été downloadé sur www.k1der.net ;)".chr(13)."
-----------------------------------------------";

// On l'enregistre
$fp = fopen("lan/infos_lan.txt", "w");
fputs($fp,$txt);
fclose($fp);

mysql_close();

// création d'un objet 'zipfile'
$zip= new PclZip("lan/".$archive);

// On l'ajoute dans l'archive
$v_list = $zip->add("infos_lan.txt");

$taille = sizeof($zip->listContent()) -1;
$zip->deleteByIndex(0-$taille);

$dir = "lan/".$_GET['id']."/photos/";

//$v_list = $zip->add($dir,"","lan/".$_GET['id']."/photos/piti");
if ($v_list == 0) {
    die("Error : ".$zip->errorInfo(true));
  }
header('Content-Type: application/x-zip');
header('Content-Disposition: attachment; filename='.$archive);
$fp = fopen ("lan/".$archive, "r");
$content = fread($fp, filesize("lan/".$archive));
fclose ($fp);
echo $content;
?> 