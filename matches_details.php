<?
function faire_piti($rep_pitis, $nom_image_photo) {
  include "config.photos_lan.php";
  @set_time_limit(86400);							// pour eviter que sa mette une erreur de temps
  @ini_set("memory_limit",'10M');
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
    imagecopyresized($gd_piti, $gd_image, 0,0,0,0, $largeur_piti,$hauteur_piti, $largeur_photo,$hauteur_photo);
    ImageJPEG($gd_piti,$chemin_piti, $qualite_piti);				//ecriture de l'image
    ImageDestroy($gd_image);							// destruction de l'image cree par GD
    Imagedestroy($gd_piti);
  }
}

function verif_piti($dir) {
  @set_time_limit(86400);
  ini_set("max_execution_time", "999999");
  $rep_cats = "./matches/fichiers";
  $rep_photos = $dir;
  $rep_piti = $dir."/piti";
  $open_le_rep_photos = opendir($rep_photos);
  if(!is_dir($rep_piti)) {							// IS y'a pas de repertoire piti on le cree
    mkdir($rep_piti, 0777);
  }
  while ($image_photos = readdir($open_le_rep_photos)) {
    $extensions = explode(".",$image_photos);					// on rcupere l'extension
    $nombre_ext = count($extensions);
    $extension = $extensions[$nombre_ext-1];
    if( (($image_photos != ".") && ($image_photos != "..")) && ($extension == "jpg")) {
      if(  (!file_exists($rep_piti."/".$image_photos)) || (!is_dir($rep_piti)) ){// si n'existe pas le piti
        faire_piti($rep_photos, $image_photos);					// 
      }
    }
  }
}


function DirTri($rep,$tri)
{
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
    if($File != "." && $File != ".." && $File != "index.htm" && $File != "piti")
    {
      $Array[] = "$File";
    }
    $i++;
  }
  closedir($dir);

  if($tri == 'DESC'){
    rsort($Array);
  }else{
    sort($Array);
  }
  $combien = 0;
  $avant_img = 3;
  $Max = count($Array);
  $texto = "Fichiers associés:";
  for($i = 0; $i != $Max; $i++){
  verif_piti($rep);
  if (ereg(".jpg",$Array[$i]))
  {
  if ($avant_img == 0) echo "<br>";
  if ($combien == 2)
  {
  echo "<br>";
  $combien = 0;
  }
  else if ($avant_img == 1)
  {
  echo "&nbsp;&nbsp;";
  }
  echo "<a href=\"/$rep"."$Array[$i]\"><img border=0 src=\"$rep"."piti/"."$Array[$i]\"></a>";
  $combien++;
  $avant_img = 1;
  }
  else
  {
  if ($avant_img == 1) echo "<br><br>";
  echo "<font size='2' face='Verdana, Arial, Helvetica, sans-serif'>- <a href=\"/$rep"."$Array[$i]\">$Array[$i]</a></font><br>";
  $avant_img = 0;
  }
  $texto = "";
  }
  //echo "<br><br>".$Max." fichier(s)" ;
}

// utilisation de la fonction
// param.1 : chemin du répertoire ("." si il s'agit du rép. courant)
// param.2 : ASC ou DESC (A-Z ou Z-A)

  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches WHERE id=$id";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Details 
      du Matche <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>K1der</b> 
      vs <b><? echo $disp[mechants]; ?></b></font>=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Equipe 
      affront&eacute;e : <b><b><? echo $disp[mechants]; ?></b></b></font></td>
    <td rowspan="16" valign="top"> <div align="center"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="50%"> <div align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="5">-=K<font color="#CC0000">1der</font>=-</font></strong></font></div></td>
            <td width="50%"> <div align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="5"><? echo $disp[mechants]; ?></font></strong></font></strong></font></div></td>
          </tr>
          <tr> 
            <td width="50%"> <div align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="5"><? echo $disp[score_k1]; ?></font></strong></font></div></td>
            <td width="50%"> <div align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="5"><? echo $disp[score_me]; ?></font></strong></font></div></td>
          </tr>
        </table>
        <font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="5"> 
        </font></strong></font><img src="images/
		<? if ($disp[score_k1] > $disp[score_me])
		{
		echo "win";
		}
		else if ($disp[score_k1] < $disp[score_me])
		{
		echo "lose";
		}
		else
		{
		echo "draw";
		}
		?>.jpg" width="250" height="200"></div></td>
  </tr>
  <?
  if (($disp[site] != "http://") && ($disp[site] != ""))
  {
  ?>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Site 
      internet : <a href="<? echo $disp[site]; ?>" target="_blank"><? echo str_replace("http://","",$disp[site]); ?></a></font></td>
  </tr>
  <?
  }
  if ($disp[irc] != "")
  {
  ?>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Channel 
      IRC : <a href="irc://quakenet.org/<? echo $disp[irc]; ?>" target="_blank">#<? echo $disp[irc]; ?></a></font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
      : <b><? echo "$disp[jour] $disp[mois] $disp[annee]"; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type 
      : </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $disp[type]; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
			if ($disp[type] != 'Internet')
			{
			echo "Localisation :";
			}
			else
			{
			echo "Server :";
			}
			?>
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $disp[loc]; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Occasion 
      :</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
      <? echo $disp[occ]; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">T&eacute;l&eacute;charger 
      le replay</font><font size="2"><font color="#FF6600" face="Verdana, Arial, Helvetica, sans-serif"></font></font></td>
  </tr>
  <tr> 
    <td colspan="2" align="center"> 
      <?
	if (($disp[hltv] != '') && ($disp[hltv] != 'http://'))
	{
	?>
      <a href="<? echo $disp[hltv]; ?>"><img src="images/hltv.gif" width="60" height="30" border="0"></a> 
      <?
	}
	else
	{
	?>
      <img src="images/hltv2.gif" width="60" height="30" border="0"> 
      <?
	}
	?>
    </td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td></td>
  </tr>
  <tr> 
    <td colspan="3" > 
      <? if ($disp[map2] != 'Aucune') { ?>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="4"><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cartes 
              jou&eacute;es <br>
              <br>
              </font></strong></div></td>
        </tr>
        <tr> 
          <td width="25%"><div align="center"></div></td>
          <td width="25%"><div align="center"><img src="images/cartes/<? echo $disp[map]; ?>.jpg" width="109" height="81" border="1"></div></td>
          <td width="25%"> <div align="center"><img src="images/cartes/<? echo $disp[map2]; ?>.jpg" width="109" height="81" border="1"></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
        <tr> 
          <td><div align="center"><strong></strong></div></td>
          <td><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[map]; ?></font></strong></div></td>
          <td width="25%"> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[map2]; ?></font></strong></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
        <?
		if (($disp[score_map_k1_t]+$disp[score_map_me_ct]+$disp[score_map_k1_ct]+$disp[score_map_me_t]) != 0)
		{
		?>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Scores</strong></font></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">T 
              : <? echo $disp[score_map_k1_t]."/".$disp[score_map_me_ct]; ?></font></div></td>
          <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">T 
              : <? echo $disp[score_map2_k1_t]."/".$disp[score_map2_me_ct]; ?></font></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">CT: <? echo $disp[score_map_k1_ct]."/".$disp[score_map_me_t]; ?>              </font></div></td>
          <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              CT: <? echo $disp[score_map2_k1_ct]."/".$disp[score_map2_me_t]; ?></font></div></td>
          <td>&nbsp;</td>
        </tr>
        <?
		}
		?>
      </table>
      <? } else { ?>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="4"><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
              jou&eacute;e<br>
              <br>
              </font></strong></div></td>
        </tr>
        <tr> 
          <td width="25%"><div align="center"></div></td>
          <td colspan="2"> <div align="center"><img src="images/cartes/<? echo $disp[map]; ?>.jpg" width="109" height="81" border="1"></div>
            <div align="center"></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="2"><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[map]; ?></font></strong></div></td>
          <td>&nbsp;</td>
        </tr>
        <?
		if (($disp[score_map_k1_t]+$disp[score_map_me_ct]+$disp[score_map_k1_ct]+$disp[score_map_me_t]) != 0)
		{
		?>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Scores</strong></font></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td width="25%">&nbsp;</td>
          <td width="25%"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">T 
              : <? echo $disp[score_map_k1_t]."/".$disp[score_map_me_ct]; ?></font></div></td>
          <td width="25%"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">CT: 
              <? echo $disp[score_map_k1_ct]."/".$disp[score_map_me_t]; ?></font></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="2"><div align="center"></div></td>
          <td>&nbsp;</td>
        </tr>
        <?
		}
		?>
      </table>
      <? } ?>
    </td>
  </tr>
  <tr> 
    <td colspan="3" >&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3" ><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Composition 
        des Equipes</strong></font></div></td>
  </tr>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="49%">&nbsp;</td>
    <td width="2%" rowspan="9" align="center" > <div align="center"> 
        <center>
          <hr align="center" width="1" size="125" noshade color="#000000">
        </center>
      </div></td>
    <td width="49%" >&nbsp;</td>
  </tr>
  <tr> 
    <td > <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">-=K<font color="#CC0000">1der</font>=-</font></strong></div>
      <div align="center"></div></td>
    <td > <div align="center"></div>
      <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $disp[mechants]; ?></b></font></b></font></div></td>
  </tr>
  <tr> 
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_k1]; ?></font></div></td>
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_m1]; ?></font></div></td>
  </tr>
  <tr> 
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_k2]; ?></font></div></td>
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_m2]; ?></font></div></td>
  </tr>
  <tr> 
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_k3]; ?></font></div></td>
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_m3]; ?></font></div></td>
  </tr>
  <tr> 
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_k4]; ?></font></div></td>
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_m4]; ?></font></div></td>
  </tr>
  <tr> 
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_k5]; ?></font></div></td>
    <td><div align="center"></div>
      <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[jou_m5]; ?></font></div></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td width="49%">&nbsp;</td>
    <td width="49%">&nbsp;</td>
  </tr>
  <?
  if ($disp[comm] != "")
  {
  ?>
  <tr> 
    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Commentaires 
      :</strong></font></td>
  </tr>
  <tr>
    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?
	echo
	str_replace("
","<br>",$disp[comm]); ?></font></td>
  </tr>
  <tr> 
    <td colspan="3">&nbsp;</td>
  </tr>
  <?
  }
  ?>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td width="125" valign='top' nowrap><font size='2' face='Verdana, Arial, Helvetica, sans-serif'>Fichiers associés:&nbsp;</font></td> 
      <td><? DirTri("matches/fichiers/$disp[id]/","ASC"); ?></td>
  </tr>
  <tr> 
    <td width="125" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font></td>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<br>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><div align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?
$caca = "_".$id."_";
$dodo = mysql_query("SELECT * FROM ib_topics WHERE pinned='$caca' && forum_id='6'");
$grosse = mysql_fetch_array($dodo);
$popo = mysql_num_rows($dodo);
$grosseconne = $grosse['id'];
$dada = mysql_query("SELECT * FROM ib_posts WHERE topic_id='$grosseconne'");
$nombre = mysql_num_rows($dada);
$nombre = $nombre-1;
?>
        <?
if($source!='non'){ $SOURCE = " | <b>$SourceTitle</b> : <a href=\"$url_source\">$nom_source</a> "; }
else{ $SOURCE = ""; }

if ($nombre >= '1')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$nombre Commentaires</a>";
}
if ($nombre == '1')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$nombre Commentaire</a>";
}
if ($nombre == '0')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">Aucun Commentaire</a>";
}
if ($grosse['tid'] <= "3")
{
$reqCOMMENT = mysql_query("SELECT id FROM $TBL_COMMENTAIRES WHERE id_news='$id'");
$resCOMMENT = mysql_num_rows($reqCOMMENT);

if($resCOMMENT>='2'){ $disdonc = "<a target=_blank href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$resCOMMENT $CommentsTitle</a>"; }
elseif($resCOMMENT=='1'){ $disdonc = "<a target=_blank href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$UnCommentTitle</a>"; }
else{ $disdonc = "<a target=_blank href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$NoCommentTitle</a>"; }
}
?>
        <? echo $disdonc; ?> </font></div></td>
  </tr>
</table>
