<?php
// appel de la classe
require_once('pclzip.lib.php');
@ini_set("memory_limit",'100M');
@ini_set("max_execution_time",'6000');

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
mysql_close();

$nom = str_replace(" ","_",$disp[nom]);
$nom = str_replace("-","_",$nom);
$nom = str_replace("[","",$nom);
$nom = str_replace("]","",$nom);
$nom = str_replace("'","",$nom);
$nom = str_replace("#","",$nom);
$nom = strtr(strtolower($nom), $Caracs);

$archive = "photos_".$nom.".zip";

$dir = opendir("lan/");
while($fichier = readdir($dir)) {
if (ereg(".zip",$fichier)) @unlink("lan/".$fichier);
	if ($fichier == "infos_lan.txt") @unlink("lan/".$fichier);
}
closedir($dir);

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


// création d'un objet 'zipfile'
$zip= new PclZip("lan/".$archive);

// On l'ajoute dans l'archive
//$zip->add("lan/infos_lan.txt");

//$taille = sizeof($zip->listContent()) -1;
//$zip->deleteByIndex(0-$taille);

// ajout du fichier dans cet objet

//$zip->add($filename,"","");

//$dir = "lan/".$_GET['id']."/photos/";
$dir = "lan/".$_GET["id"]."/photos/";

/*while($fichier = readdir($odir)) {
	if (ereg(".jpg",$fichier)) {
	echo $dir.$fichier."<br>";*/
		$zip->add($dir.",lan/infos_lan.txt","");
		//$zip->add("cache.txt",$dir,"");

		//$i++;
	//}
//}

//header('Content-Type: application/x-zip');
//header('Content-Length: '.filesize("lan/archive.zip"));
//header('Content-Disposition: attachment; filename=archive.zip');
/*$fp = fopen ("lan/archive.zip", "r");
$content = fread($fp, filesize("lan/archive.zip"));
fclose ($fp);
echo $content;*/
header("Location:lan/".$archive);
?> 

