<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM liens WHERE id='$id'");

$disp = mysql_fetch_array($req);
?>
<title></title>
<body>
<div align="center">
              
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="center">
          <table width="465" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="38" height="42" rowspan="2"><img src="../images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="../images/fond.gif"><img src="../images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Modification 
                du lien=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
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
            <td nowrap> <form method="POST" action="modif_lien2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
                      : </font></td>
                    <td height="2" width="250"> <input name="nom_lien" type="text" value="<? echo $disp[nom]; ?>" size="30"> 
                    </td>
                  </tr>
                  <tr> 
                    <td height="24" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lien 
                      :</font></td>
                    <td height="24" width="250"> 
                      <input name="lien_lien" type="text" value="<? echo $disp[lien]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td height="20" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url 
                      de l'image :</font></td>
                    <td height="20" width="250"> <input name="image_lien" type="text" value="<? echo $disp[image]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td height="2" width="250"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">(Laisser 
                      vide si il n'y a pas d'image)</font></td>
                    <td height="2" width="250">&nbsp;</td>
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

