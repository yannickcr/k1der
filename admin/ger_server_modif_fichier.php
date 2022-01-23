<?
require("config.inc.php3");

include "secu.php";

$req = MYSQL_QUERY("SELECT * FROM server WHERE id='$id'");

$disp = mysql_fetch_array($req);
?>
<title></title>
<body>
<div align="center">
              
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="600">
<div align="center"> 
          <table width="600" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td width="568" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="32" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="568" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Modifier 
                un fichier=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td width="600">&nbsp; </td>
    </tr>
    <tr> 
      <td width="600"> 
        <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="admin/ger_server_modif_fichier2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" width="264"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
                      :</font></td>
                    <td height="2" width="111"><input name="descr" type="text" id="descr" value="<? echo $disp[descr]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td height="2" width="264"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url 
                      : <font size="1">(ex: addons/amx/admin.cfg )</font></font></td>
                    <td height="2" width="111"><input name="furl" type="text" id="furl" value="<? echo $disp[url]; ?>" size="30"></td>
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
              
  <br>
  <form>
    <input name="button" type="button" onClick="Javascript:window.location='index.php?page=ger_server_list_fichier&action=modif';" value="Retour &agrave; la liste">
  </form>
</div>

