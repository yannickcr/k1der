<?
//---------------------------------------------------------------------------------------------------
//							
//	WebJeff-Photos v1.2
//	
//	Auteur : Jean-François GAZET
//	Site web : http://www.webjeff.org
//	Email : webmaster@webjeff.org	
//
//---------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------
//	OPTIONS
//---------------------------------------------------------------------------------------------------

// Barre de navigation en haut de page
// (Navigation links on top)
$nav_haut="oui";  // ("oui" ou "non")

// Barre de navigation en bas de page
// (Navigation links at bottom)
$nav_bas="oui";  // ("oui" ou "non")

// Afficher le nom du fichier image
// (display file name)
$aff_filename="non";  // ("oui" ou "non")

// Afficher le poids du fichier
// (display file size in Mb)
$aff_filesize="non";	// ("oui" ou "non")
$size_unit="o"; 	// ("o" ou "b" pour octets ou bytes)

// Afficher les dimensions de l'image
// (display file size, width and height)
$aff_dimensions="non";  // ("oui" ou "non")

// Afficher la date de modification de l'image
// (display mod time of picture)
$aff_modtime="non";  // ("oui" ou "non")

// Classer les images par (sort pictures with) : 
// Nom de fichiers (filename) : "nom" 
// Poids du fichier (file size) : "taille"
// Date de modification (modification date) : "date"
// Type de fichier (type of file, gif, jpg...) : "type"
$ordre="nom";

// Inverser le sens de l'ordre 
$sens=0;  // (1 ou 0, sens normal : 0)


//---------------------------------------------------------------------------------------------------
//	FIN DES OPTIONS
//---------------------------------------------------------------------------------------------------

function mimetype($fichier)
	{
	if(eregi("\.gif$",$fichier)){$nom_type="GIF";}
	else if(eregi("\.jpg$",$fichier)){$nom_type="JPG";}
	else if(eregi("\.png$",$fichier)){$nom_type="PNG";}
	else if(eregi("\.bmp$",$fichier)){$nom_type="BMP";}
	return $nom_type;
	}
?>
<html>
<head>
<title>Screenshot Viewer</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<SCRIPT LANGUAGE="JavaScript">
window.moveTo(0,0);
if (document.all) 
{
window.resizeTo(screen.availWidth,screen.availHeight);
}
else if (document.layers) 
{
if (window.outerHeight<screen.availHeight||window.outerWidth<screen.availWidth)
{
window.outerHeight = screen.availHeight;
window.outerWidth = screen.availWidth;
}
}
</SCRIPT>
</head>
<body>
<div align="center"><font face="Verdana" size="4" color="#987898"><img src="images/k1der2.gif" align="middle" width="288" height="94"></font><br>
  <br>
<?
// LISTING DE TOUTES LES IMAGES
$tableau=""; $i=0;
$handle=opendir("$rep");
while ($fichier = readdir($handle))
	{
	$extension=substr($fichier,-3);
	if($fichier!="." && $fichier!=".." && (eregi("gif",$extension)||eregi("jpg",$extension))) 
		{
		if($ordre=="nom") {$listing[$fichier]=$i;}
		else if($ordre=="taille") {$listing[$fichier]=filesize("$rep/$fichier");}
		else if($ordre=="date") {$listing[$fichier]=filemtime("$rep/$fichier");}
		else if($ordre=="type") {$listing[$fichier]=mimetype("$rep/$fichier");}
		else {$listing[$fichier]=mimetype("$rep/$fichier","image");}		
		}
	$i++;
	}
closedir($handle);

if(!is_array($listing)) 
	{
	echo "<br><br><font face=\"Verdana\" size=\"2\">\n";
	echo "Exemple d'appel de ce script : <br>\n";
	echo "screen.php?rep=totoimages</font>\n";
	}
else
	{
	// TRI DE LA LISTE DES FICHIER
	if($ordre=="nom") {if($sens==0){ksort($listing);}else{krsort($listing);}}
	else if($ordre=="date") {if($sens==0){arsort($listing);}else{asort($listing);}}
	else if($ordre=="taille"||$ordre=="type") {if($sens==0){asort($listing);}else{arsort($listing);}}
	else {if($sens==0){ksort($listing);}else{krsort($listing);}}
	
	// INDICES DES IMAGES DEBUT, PRECEDENTE, SUIVANTE ET FIN
	$i=0;
	while (list($cle,$val) = each($listing))
		{
		if($image=="") {$image=$cle;} 
		if($i==0) {$debut=$cle;}
		if($savesuiv==1) {$suivant=$cle; $savesuiv=0;}
		if($image==$cle) 
			{
			$savesuiv=1;
			$stopprev=1;
			$numphoto=$i+1;
			}
		if($stopprev=="") {$precedent=$cle;}
		$fin=$cle;
		//echo "cle=$cle , val=$val , debut=$debut , precedent=$precedent , suivant=$suivant , fin=$fin<br>";
		$i++;
		}
	
	// BARRE DE NAVIGATION
	$tableau.="<table align=\"center\" width=\"80%\">\n";
	$tableau.="<tr><td width=\"10%\"><font face=\"Verdana\" size=\"2\">";
	// Liens début et précédent
	if($precedent!="")
		{
		$tableau.="<a href=\"screen.php?rep=$rep&image=$debut\"><img STYLE='filter:fliph' src='images/fleche.jpg' border=0 align='absmiddle'><img STYLE='filter:fliph' src='images/fleche.jpg' border=0 align='absmiddle'></a>&nbsp;&nbsp;&nbsp;\n";
		$tableau.="<a href=\"screen.php?rep=$rep&image=$precedent\"><img STYLE='filter:fliph' src='images/fleche.jpg' border=0 align='absmiddle'></a>\n";
		}
	$tableau.="</font></td><td align=\"center\"><font face=\"Verdana\" size=\"2\">ScreenShot <b>$numphoto</b> sur <b>$i</b></font></td>
	<td align=\"right\" width=\"10%\"><font face=\"Verdana\" size=\"2\">";
	// Liens suivant et fin
	if($suivant!="")
		{
		$tableau.="<a href=\"screen.php?rep=$rep&image=$suivant\"><img src='images/fleche.jpg' border=0 align='absmiddle'></a>&nbsp;&nbsp;&nbsp;";
		$tableau.="<a href=\"screen.php?rep=$rep&image=$fin\"><img src='images/fleche.jpg' border=0 align='absmiddle'><img src='images/fleche.jpg' border=0 align='absmiddle'></a>";
		}
	$tableau.="</font></td></tr>\n";
	$tableau.="</table>";  
	
	// AFFICHAGE
	if(file_exists("$rep/$image"))
		{
		if($nav_haut=="oui") {echo "$tableau\n";}
		$taillefic = GetImageSize("$rep/$image");
		echo "<IMG SRC=\"$rep/$image\" ".$taillefic[3]."><br>\n";
		echo "<font face=\"Verdana\" size=\"2\">\n";
		if($aff_filename=="oui") 
			{
			echo "Nom du fichier : <b>$image</b><br>\n";
			}
		if($aff_modtime=="oui") 
			{
			echo "Date de modification : <b>".date("d/m/Y H:i",filemtime("$rep/$image"))."</b><br>\n";
			}
		if($aff_filesize=="oui") 
			{
			$taille=filesize("$rep/$image");
			if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . " G".$size_unit;}
			elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . " M".$size_unit;}
			elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . " K".$size_unit;}
			else {$taille = $taille . " ".$size_unit;} 
			if($taille==0) {$taille="";}
			echo "Poids : <b>$taille</b><br>\n";
			}
		if($aff_dimensions=="oui") 
			{
			echo "Largeur - Hauteur : <b>$taillefic[0] x $taillefic[1]</b><br>\n";
			}
		echo "</font>\n";
		if($nav_bas=="oui") {echo "$tableau\n";}
		}
	else
		{
		echo "<font face=\"Verdana\" size=\"2\">L'image pass&eacute;e en param&egrave;tre n'existe pas.</font>";
		}
	}
?>
        </div>
  <p></p>
</body>
</html>
