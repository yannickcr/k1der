<?php
header('content-type: image/png'); 
$corps = 18;
/* Le chemin du fichier de police True Type */
$font = 'yikes.ttf';
$texte = $_GET['texte'];
if (get_magic_quotes_gpc()) $texte = stripslashes($texte); 
/* Imagettfbbox retourne les dimensions du texte */
$size =imagettfbbox($corps,0,$font,$texte);
$dx = abs($size[2]-$size[0]);
$dy = abs($size[5]-$size[3]);
$xpad=10;
$ypad=11-5;
if(!isset($_GET['x']) || !isset($_GET['y'])) $im = imagecreate($dx+$xpad,$dy+$ypad);
else $im = imagecreate($_GET['x'],$_GET['y']);
$fond = imagecolorallocate($im,204,0,0);
$couleur = imagecolorallocate($im,255,255,255);
$couleur2 = imagecolorallocate($im,0,0,0);
/* Imagettftext dessine le texte en partant de la ligne de base du premier caractère */
imagettftext($im,$corps,0,(int)(($xpad+5)/2),($dy+3)-(int)($ypad/2),$couleur2,$font,$texte);
imagettftext($im,$corps,0,(int)($xpad/2),$dy+1-(int)($ypad/2),$couleur,$font,$texte);
imagepng($im);
imagedestroy($im);
?>