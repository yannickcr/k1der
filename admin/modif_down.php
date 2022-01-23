<?
include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM liens_down WHERE id='$id'");

$disp = mysql_fetch_array($req);
?>
<title></title>
<body>
<STYLE TYPE="text/css">
<!-- /* $WEFT -- Created by: -=K1der=- Country (glou@ifrance.com) on 21/07/2002 -- */
  @font-face {
    font-family: Minnie;
    font-style:  normal;
    font-weight: normal;
    src: url(<? echo $font_url; ?>);
  }
-->
</STYLE>
<div align="center">
<table width="480" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="center">
          <table width="480" border="0" align="left" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="../images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
            </tr>
            <tr> 
              <td width="442" height="20" background="../images/fond.gif"><img src="../images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                Modifier un Fichier</font></b><font color="#FFFFFF">=-</font></font></td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
    </tr>
    <tr> 
      <td width="475"> <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="modif_down2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td width="200" height="2" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
                      : </font></td>
                    <td height="2" width="300"> <input name="nom" type="text" id="nom" value="<? echo $disp[nom]; ?>" size="30"> 
                    </td>
                  </tr>
                  <tr> 
                    <td width="200" height="2" colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
                      :</font></td>
                    <td height="2" width="300"> <textarea name="descr" cols="30" rows="5"><? echo $disp[descr]; ?></textarea></td>
                  </tr>
                  <tr>
                    <td height="20" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Image 
                      (Optionnel) :</font></td>
                    <td height="20"><input name="img" type="text" id="img" value="<? echo $disp[img]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td width="200" height="20" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lien 
                      Complet (Http://...) :</font></td>
                    <td height="20" width="300"> <input name="lien" type="text" id="lien" value="<? echo $disp[lien]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td height="21"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cat&eacute;gorie</font></td>
                    <td height="21"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Existante 
                        : 
                        <input name="type_cat" type="radio" value="ex_cat" checked>
                        <br>
                        </font></div></td>
                    <td height="20" rowspan="2" valign="top"> <select name="cat" id="cat">
                        <?
		$cat = $disp[cat];
	  include 'config.php';
	  $db = mysql_connect($cfg_hote, $cfg_user, $cfg_password);
	  mysql_select_db($cfg_base,$db);
	  $requete  = "SELECT * FROM cats_down ORDER BY nom";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $nbre =mysql_num_rows($req);
	  while($disp = mysql_fetch_array($req))
	  {
	  ?>
                        <option value="<? echo $disp[id]; ?>" <? if ($disp[id] == $cat) { echo 'SELECTED'; } ?>><? echo $disp[nom]; ?></option>
                        <?
	  }
	  ?>
                      </select> <br> <input name="new_cat" type="text" id="new_cat"> 
                    </td>
                  </tr>
                  <tr> 
                    <td height="20" valign="top">&nbsp;</td>
                    <td height="20"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nouvelle 
                        : 
                        <input type="radio" name="type_cat" value="new_cat">
                        </font></div></td>
                  </tr>
                  <!-- Boutons -->
                  <!-- Boutons -->
                </table>
                <div align="center"><br>
                  <input type="submit" name="envoi" value="Valider les modifications ...">
                </div>
              </form></td>
          </tr>
        </table></td>
    </tr>
  </table>
              
</div>

