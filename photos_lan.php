<?

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

function mettre_global($vars_global) {
  global $vars_global;
}


function faire_piti($rep_pitis, $nom_image_photo) {
  include "config.photos_lan.php";
  @set_time_limit(86400);							// pour eviter que sa mette une erreur de temps
	@ini_set("max_execution_time",86400);
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
    $gd_piti = ImageCreatetruecolor($largeur_piti, $hauteur_piti);			// on cree une image vide
    //imagecopyresized($gd_piti, $gd_image, 0,0,0,0, $largeur_piti,$hauteur_piti, $largeur_photo,$hauteur_photo);
	imagecopyresampled($gd_piti, $gd_image, 0,0,0,0, $largeur_piti,$hauteur_piti, $largeur_photo,$hauteur_photo);
    imagejpeg($gd_piti,$chemin_piti, $qualite_piti);				//ecriture de l'image
    imagedestroy($gd_image);							// destruction de l'image cree par GD
    imagedestroy($gd_piti);
  }
}

function verif_piti($nom_lan) {
  @set_time_limit(86400);
  ini_set("max_execution_time", "999999");
  $rep_lan = "./lan";
  $rep_photo = $rep_lan."/".$nom_lan."/photos";
  $rep_piti = $rep_lan."/".$nom_lan."/photos/piti";
  $open_le_rep_photo = opendir($rep_photo);
  if(!is_dir($rep_piti)) {							// IS y'a pas de repertoire piti on le cree
    mkdir($rep_piti, 0777);
  }
  while ($image_photo = readdir($open_le_rep_photo)) {
    $extensions = explode(".",$image_photo);					// on rcupere l'extension
    $nombre_ext = count($extensions);
    $extension = $extensions[$nombre_ext-1];
    if( (($image_photo != ".") && ($image_photo != "..")) && ($extension == "jpg")) {
      if(  (!file_exists($rep_piti."/".$image_photo)) || (!is_dir($rep_piti)) ){// si n'existe pas le piti
        faire_piti($rep_photo, $image_photo);					// 
      }
    }
  }
}

function getmicrotime(){
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
}
function Compte_Fichiers($dossier, $ext)
{
  if (is_dir($dossier))
  {
  $rep=dir($dossier);
  while($fichier = $rep->read())
  {
    if(ereg(".php", $fichier)==FALSE)
    {
      if($fichier!="." && $fichier!="..")
      {
        ++$cpt;
      }
    }
  }
  }
  return "$cpt";
}


// fin de decalrations des fonctions

if(empty($la_lan)) {
?><br/>
<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Lan 
      Party=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<br/><br/>
<center>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="2"> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lan<br/>
          </font></strong></div></td>
      <td> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></strong></div></td>
      <td><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ville</font></strong></div>
        <div align="center"></div>
        </td>
    </tr>
    <tr> 
      <td colspan="7"><hr width="550" size="1" noshade color="#000000"></td>
    </tr>
    <?
	$requete  = "SELECT * FROM lan_party ORDER BY orderdate";
	$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
	while($disp = mysql_fetch_array($req))
	{
/*	echo "<tr>";
	if ($first != "1")
	{
	echo "<td>";
	}
	else
	{
	echo "<td>";
	}*/
$nbr_photos = Compte_Fichiers("lan/$disp[id]/photos","jpg");
if (!$nbr_photos) $nbr_photos = 0;

	echo "</td><td><p align=\"center\">";					// affichage d'une ligne de tableau
	//if ($nbr_photos != "0") {						// si il y a des photos :
  	  echo "<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">$disp[nom]</font>";
	  if ($nbr_photos != '0')
	  {
	  echo "</td><td></td><td><p align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">$disp[jour] $disp[mois] $disp[annee]</font></p></td><td><p align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$disp[loc]</font></p></td><td><p align=\"center\"><a href=\"index.php?page=lan_details&la_lan=$disp[id]\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Détails</font></p></a></td><td><p align=\"center\"><a href=\"index.php?page=lan_photos&la_lan=$disp[id]&nbr_photos=$nbr_photos&nbr=21&start=0\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Photos ($nbr_photos)</font></a></p></td><td>&nbsp;<a href=\"bulle.php?id=$disp[id]\"><img border=\"0\" align=\"absmiddle\" src=\"images/zip.gif\"></a>&nbsp;</td>";
	  }
	  else
	  {
	  echo "</td><td></td><td><p align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">$disp[jour] $disp[mois] $disp[annee]</font></p></td><td><p align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$disp[loc]</font></p></td><td><p align=\"center\"><a href=\"index.php?page=lan_details&la_lan=$disp[id]\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Détails</font></p></a></td><td><p align=\"center\"></p></td>";
	  }
	if ($first != "1")
	{
	echo "<td>";
	$first = "1";
	}
	else
	{
	echo "<td background=\"images/nodeg2.gif\">";
	}
	echo "</td></tr>";		// fermeture de la ligne du tableau
								// si le rep de photos n'existe pas 
//      	echo "<tr><td><p align=\"center\" style=\"text-decoration : line-through;\">";					//mais que la lan existe, on affiche quand meme
//      	echo $nom_lan;
//      	echo "</td><td><p align=\"center\">0</p></td>
//	</tr>";
}
?>
    <?
  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches order by id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$nbre =mysql_num_rows($req);
while($disp = mysql_fetch_array($req))
{
?>
    <?
  }
  ?>
  </table>
  </center>

<?
}
else {
$requete  = "SELECT * FROM lan_party WHERE id='$la_lan'";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$disp = mysql_fetch_array($req);
?>
<table width="300" border="0" cellspacing="0" cellpadding="0" align="CENTER">
  <tr> 
    <td colspan="5"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="100%" height="22" valign="baseline"> 
            <div align="right"></div></td>
          <td width="38" height="42" rowspan="2" align="right"> <div align="right"><img src="images/oeuf3.gif" width="40" height="42"></div></td>
        </tr>
        <tr> 
          <td width="100%" height="20" valign="top" background="images/fond.gif"><img src="images/coinghautgauche.gif" width="15" height="15"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<? echo $disp[nom]; ?>=-</font></b></font></td>
        </tr>
      </table></td>
  </tr>
  <tr width="4"> 
    <td background="images/fond.gif" width="20">&nbsp;</td>
    <td width="260"> 
      <?
  $rep_photos = "./lan/".$lan_lan."/photos";
  $rep_piti = $rep_photos."/piti";
  mettre_global($la_lan);
  mettre_global($rep_photos);
  mettre_global($rep_piti);
  verif_piti($la_lan);								// on verifie si les piti existent
  $repertoire = opendir("./lan/".$la_lan."/photos/piti");
  ?>
      <table border="0" cellspacing="0" cellpadding="0" align="center">
        <tr> 
          <?
		  $start1 = 0;
  while($image = readdir($repertoire)) {					// on parcour le repertoire

    if (($image != ".") && ($image != "..")) {

      $extensions = explode(".",$image);
      $nombre_ext = count($extensions);
      $extension = $extensions[$nombre_ext-1];	  
      if($extension == "jpg") {
	  if ($start1 == $start)
	  {
	  if ($nbr > 0)
	  {
      echo "<td valign=\"center\" align=\"center\"><a href=\"photo.php?rep=lan/".$la_lan."/photos&image=".$image."\" target=\"_blank\"><img src=\"./lan/".$la_lan."/photos/piti/".$image."\" border=\"0\"></a></td>";
	  
	$nbr = $nbr-1;
	  $nbimg = $nbimg+1;
	  }
	  }
	  else
	  {
	  $start1 = $start1+1;
	  }
	  
	  if ($nbimg == "3")
	  {
	  echo "</tr><tr>";
	  $nbimg = "0";
	  }
	  }
    }
  }
$all = $start+$nbr;
  ?>
        </tr>
      </table>
    </td>
    <td width="20" background="images/fond.gif">&nbsp;</td>
  </tr>
  <tr> 
    <td width="20" background="images/fond.gif"><img src="images/coingbasgauche.gif" width="15" height="15"></td>
    <td background="images/fond.gif" width="260"> 
      <div align="CENTER"></div></td>
    <td width="20" background="images/fond.gif"><div align="right"><img src="images/coingbasdroite.gif" width="15" height="15"></div></td>
  </tr>
</table>
<center>
  <table width="450" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
	  <?
	  if ($start != '0')
	  {
	  ?>
	  <a href="index.php?page=lan_photos&la_lan=<? echo $la_lan; ?>&nbr_photos=<? echo $nbr_photos; ?>&nbr=21&start=<? echo $start-21; ?>">&lt;</a> 
	  <?
	  }
	  $i = 1;
	  $a = 0;
	  $equ = $nbr_photos/21;
	  $equ = ceil($equ);
	  while ($i <= $equ)
	  {
	  if ($a != $start)
	  {
	  ?>
	  <a href="index.php?page=lan_photos&la_lan=<? echo $la_lan; ?>&nbr_photos=<? echo $nbr_photos; ?>&nbr=21&start=<? echo $a; ?>"><? echo $i; ?></a>&nbsp;
	  <?
	  }
	  else
	  {
	  echo "$i&nbsp;";
	  }
	  $i++;
	  $a = $a+21;
	  }
	  if ($all < $nbr_photos)
	  {
	  if ($start+21 < $nbr_photos)
	  {
	  ?>
	  <a href="index.php?page=lan_photos&la_lan=<? echo $la_lan; ?>&nbr_photos=<? echo $nbr_photos; ?>&nbr=21&start=<? echo $start+21; ?>">&gt;</a> 
	  <?
	  }
	  }
	  ?></strong></font></div>
        </td>
    </tr>
  </table>
<?
}
?>
</center>