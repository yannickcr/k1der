<?php
$level="2";
require "../admin/secu.php";
// appel de la classe
require_once('../pclzip.lib.php');
@ini_set("memory_limit",'100M');
@ini_set("max_execution_time",'6000');

$archive = "strats_backup.zip";

$dir = opendir(".");
while($fichier = readdir($dir)) {
	if (ereg(".zip",$fichier)) @unlink("".$fichier);
}
closedir($dir);

// création d'un objet 'zipfile'
$zip= new PclZip($archive);

// On l'ajoute dans l'archive
//$zip->add("lan/infos_lan.txt");

//$taille = sizeof($zip->listContent()) -1;
//$zip->deleteByIndex(0-$taille);

// ajout du fichier dans cet objet

//$zip->add($filename,"","");

//$dir = "lan/".$_GET['id']."/photos/";
$dir = "./strats/";

/*while($fichier = readdir($odir)) {
	if (ereg(".jpg",$fichier)) {
	echo $dir.$fichier."<br>";*/
		$v_list = $zip->add($dir);
		//$zip->add("cache.txt",$dir,"");
//$v_list = $zip->add($dir,"","lan/".$_GET['id']."/photos/piti");
if ($v_list == 0) {
    die("Error : ".$zip->errorInfo(true));
  }

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
header("location:".$archive);
?> 

