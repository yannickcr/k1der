<?
if ($_GET["key"]==1) header("Location:http://whatpulse.bounceme.net/sig.php?account=Country&ext=.png");
else {
	$nomRepertoire = "country_sign";
	$dossier = opendir($nomRepertoire);
	for($i=0;$Fichier = readdir($dossier);$i++) {
	  if ($Fichier != "." && $Fichier != "..") $fichiers[$i]=$Fichier;
	  else $i--;
	} 
	closedir($dossier);
	
	$i = rand(0,(count($fichiers)-1));
	$fich = fopen($nomRepertoire."/".$fichiers[$i],"r");
	while ($plop=fgets($fich)) $image.=$plop;
	header("Content-Type: image/jpg");
	echo $image;
}
?>