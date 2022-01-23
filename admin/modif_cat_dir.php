<?
require("../config.php");

?>
<div align="center">
              
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="center"><font color="#FFFF00" size="5">Modification 
          de la categorie</font></div></td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
    </tr>
    <tr> 
      <td width="475"> <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="modif_cat_dir2.php">
                <p>
				 <?
					  $dir = opendir("../images/gallerie");
					  // parcourir le répertoire en lisant le nom d'un fichier
					  // à chaque itération
					  while($fichier = readdir($dir)) {
					  if ($fichier == $nom)
					  {
					  echo "<input type=\"hidden\" value=\"$nom\" name=\"nom\">";
					  }
					  }// ferme le répertoire
					  closedir($dir);
					  ?> 
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" width="200"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
                      : </font></td>
                    <td height="2" width="300">    
					  <?
					  $dir = opendir("../images/gallerie");
					  // parcourir le répertoire en lisant le nom d'un fichier
					  // à chaque itération
					  while($fichier = readdir($dir)) {
					  if ($fichier == $nom)
					  {
					  echo "<input name=\"new_nom\" type=\"text\" id=\"nom\" value=\"$fichier\" size=\"30\">";
					  }
					  }// ferme le répertoire
					  closedir($dir);
					  ?> 
                    </td>
                  </tr>
                  <!-- Boutons -->
                  <!-- Boutons -->
                </table>
                <p>&nbsp;</p>
                <div align="center"> 
                  <input type="submit" name="envoi" value="Valider les modifications ...">
                </div>
              </form></td>
          </tr>
        </table></td>
    </tr>
  </table>
              
</div>

