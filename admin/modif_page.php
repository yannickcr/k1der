<?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM admin WHERE id='$id'");

$disp = mysql_fetch_array($req);
?>
<title></title>
<body>
<div align="center">
              
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="475"> <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="admin/modif_page2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" colspan="2"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
                        </tr>
                        <tr> 
                          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                            Modifier la page =-</font></b></font></td>
                        </tr>
                        <tr> 
                          <td colspan="2">&nbsp;</td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr> 
                    <td height="2" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
                      : </font></td>
                    <td height="2" width="250"> <input name="texte" type="text" id="texte" value="<? echo $disp[texte]; ?>" size="30"> 
                    </td>
                  </tr>
                  <tr> 
                    <td height="24" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lien 
                      :</font></td>
                    <td height="24" width="250"> <input name="lien" type="text" id="lien" value="<? echo $disp[lien]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td height="20" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Level 
                      minimum :</font></td>
                    <td height="20" width="250"> <input name="level" type="text" id="level" value="<? echo $disp[level]; ?>" size="30"></td>
                  </tr>
                  <!-- Boutons -->
                  <!-- Boutons -->
                </table>
                <p align="center"> 
                  <input type="submit" name="envoi" value="Valider les modifications ...">
                </p>
                </form></td>
          </tr>
        </table></td>
    </tr>
  </table>
              
</div>

