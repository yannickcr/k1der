<table width="465" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr> 
          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
        </tr>
        <tr> 
          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Dessins=-</font></b></font></td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
      </table>
<?
function faire_piti($rep_pitis, $nom_image_photo) {
  include "config.photos_lan.php";
  @set_time_limit(86400);							// pour eviter que sa mette une erreur de temps
  $chemin_image = $rep_pitis."/".$nom_image_photo;
  $chemin_piti = $rep_pitis."/piti/".$nom_image_photo;
  if(is_file($chemin_image)) {
    $gd_image = ImageCreateFromJPEG($chemin_image);				// on cree une image GD 
    $data = GetImageSize($chemin_image);					// on recupere la taille de l'image
    $largeur_photo = $data[0];
    $hauteur_photo = $data[1];
	if ($largeur_photo > $hauteur_photo)
	{
	$coeff_reduc = $largeur_photo / $hauteur_max;				// cacul du coefficient de reduction
    $largeur_piti = $largeur_photo / $coeff_reduc;				// calcul de la largeur du piti
    $hauteur_piti = $hauteur_photo / $coeff_reduc;				// calcul de la hauteur du piti
	}
	else
	{
    $coeff_reduc = $largeur_max / $hauteur_photo;				// cacul du coefficient de reduction
    $largeur_piti = $largeur_photo * $coeff_reduc;				// calcul de la largeur du piti
    $hauteur_piti = $hauteur_photo * $coeff_reduc;				// calcul de la hauteur du piti
	}
    $gd_piti = ImageCreateTrueColor($largeur_piti, $hauteur_piti);			// on cree une image vide
    imagecopyresampled($gd_piti, $gd_image, 0,0,0,0, $largeur_piti,$hauteur_piti, $largeur_photo,$hauteur_photo);
    ImageJPEG($gd_piti,$chemin_piti, $qualite_piti);				//ecriture de l'image
    ImageDestroy($gd_image);							// destruction de l'image cree par GD
    Imagedestroy($gd_piti);
  }
}
?>
<?
$dir = opendir("images/dessins");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
if (($fichier != ".") and ($fichier != ".."))
{
$j = 0;
?>
<table width="200" border="0" cellspacing="0" cellpadding="0" align="center">

                <tr> 
                  <td width="7" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20"></td>
                  <td width="186" background="images/fond.gif"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b><font size="2"><? echo $fichier; ?></font></b></font></div></td>
                  <td width="7" background="images/fond.gif"><img src="images/courbe2.gif" width="7" height="20"></td>
                </tr>
              </table><div align="center">
  <table width="400" border="0" cellspacing="20" cellpadding="0">
    <tr> 
      <?
//	echo "$fichier<BR>";
	$dir2 = opendir("images/dessins/$fichier");
	while($fichier2 = readdir($dir2)) {
	if (($fichier2 != ".") and ($fichier2 != "..") and ($fichier2 != "piti") and ($fichier2 != "desc"))
{
  if(!is_dir("images/dessins/$fichier/piti")) {							// IS y'a pas de repertoire piti on le cree
    mkdir("images/dessins/$fichier/piti", 0777);
  }
  if(!file_exists("images/dessins/$fichier/piti/".$fichier2)){// si n'existe pas le piti
  faire_piti("images/dessins/$fichier", $fichier2);
// echo "images/dessins/$fichier/".$fichier2;
}
      $extensions = explode(".",$fichier2);
      $nombre_ext = count($extensions);
      $extension = $extensions[$nombre_ext-1];
	  $image_desc = str_replace(".jpg", ".txt", $fichier2);
	  if(!file_exists("images/dessins/$fichier/desc/$image_desc"))
	  {
	  $desc = "";
	  }
	  else
	  {
	  include "images/dessins/$fichier/desc/$image_desc";
	  }
?>
      <td width="50%"> <div align="center"> 
          <table border="0" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
            <tr> 
              <td bordercolor="#000000"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="dessins.php?rep=images/dessins/<? echo $fichier; ?>&image=<? echo $fichier2; ?>" target="_blank"><img onload=this.style.filter='progid:DXImageTransform.Microsoft.Shadow(color=#000000,direction=135,strength=3)' src="images/dessins/<? echo $fichier; ?>/piti/<? echo $fichier2; ?>" border="0"></a></font></td>
            </tr>
          </table>
          <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		  <?
		  $desc = stripslashes($desc);
		  echo $desc;
		  ?> 
          </font></div></td>
      <?
$j++;
if ($j == "2")
{
?>
    </tr>
    <tr> 
      <?
$j = 0;
}
}}
?>
    
  </table>
</div>
<?
}
}
// ferme le répertoire
closedir($dir);
?>
                
