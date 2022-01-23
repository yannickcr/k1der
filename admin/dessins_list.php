<?
include "secu.php";?><html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table width="500" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td colspan="3"> <table width="465" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
        </tr>
        <tr> 
          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Description 
            des images=-</font></b></font></td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="50">&nbsp;</td>
    <td width="150">
<div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom</font></strong></div></td>
    <td width="300">
<div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></div></td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td width="150" valign="top">&nbsp;</td>
    <td width="300">&nbsp;</td>
  </tr>
  <?
  $dossier = 'Techniques';
  $dir = opendir("images/dessins/$dossier");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
      $extensions = explode(".",$fichier);
      $nombre_ext = count($extensions);
      $extension = $extensions[$nombre_ext-1];
//	  echo $nombre_ext;
	  if($extension == "jpg") {
	  	  $image_desc = str_replace(".jpg", ".txt", $fichier);
	  if(!file_exists("images/dessins/$dossier/desc/$image_desc"))
	  {
	  $desc = "";
	  }
	  else
	  {
	  include "images/dessins/$dossier/desc/$image_desc";
	  }

	  ?>
  <tr> 
    <td width="50" valign="top"> <div align="center"><a href="images/dessins/<? echo $fichier; ?>" target="_blank"><img src="images/app.gif" alt="Afficher" width="16" height="16" border="0"></a></div></td>
    <td width="150" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $fichier; ?></font> 
    </td>
    <td width="300"> 
      <form name="form1" method="post" action="admin/sav_desc.php">
        <input name="nom" type="hidden" id="nom" value="<? echo $image_desc; ?>">
        <input name="desc" type="text" id="desc" value="<? echo $desc; ?>" size="32">
        <input type="submit" name="Submit" value="Enregistrer">
      </form></td>
  </tr>
  <?
	}
}
// ferme le répertoire
closedir($dir);
?>
</table>
</body>
</html>
