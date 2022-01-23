<?
include "secu.php";

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

?>
<title></title>
<body>
<div align="center">
              
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="475"> <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="admin/ger_recrut2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
                        </tr>
                        <tr> 
                          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                            G&eacute;rer la page de recrutement=-</font></b></font></td>
                        </tr>
                        <tr> 
                          <td colspan="2">&nbsp;</td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr> 
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="219" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nombre 
                            de joueurs recherch&eacute;s : </font></td>
                          <td width="175"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                            <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='nb_cs'");
						  $disp = mysql_fetch_array($req);
						  ?>
                            <select name="nb_cs" id="nb_cs">
                              <option value="0" <? if ($disp[valeur] == '0') { echo "selected"; } ?>>0</option>
                              <option value="1" <? if ($disp[valeur] == '1') { echo "selected"; } ?>>1</option>
                              <option value="2" <? if ($disp[valeur] == '2') { echo "selected"; } ?>>2</option>
                              <option value="3" <? if ($disp[valeur] == '3') { echo "selected"; } ?>>3</option>
                              <option value="4" <? if ($disp[valeur] == '4') { echo "selected"; } ?>>4</option>
                              <option value="5" <? if ($disp[valeur] == '5') { echo "selected"; } ?>>5</option>
                              <option value="6" <? if ($disp[valeur] == '6') { echo "selected"; } ?>>6</option>
                            </select>
                            pour Counter-Strike </font></td>
                          <td width="206"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                            |
						  <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='type_cs'");
						  $disp = mysql_fetch_array($req);
						  ?>
                            <select name="type_cs" id="select">
                              <option value="LAN Party" <? if ($disp[valeur] == 'LAN Party') { echo "selected"; } ?>>LAN Party</option>
                              <option value="Internet" <? if ($disp[valeur] == 'Internet') { echo "selected"; } ?>>Internet</option>
                              <option value="LAN Party &amp; Internet" <? if ($disp[valeur] == 'LAN Party &amp; Internet') { echo "selected"; } ?>>LAN Party 
                              &amp; Internet</option>
                            </select>
                            </font></td>
                        </tr>
                        <tr> 
                          <td valign="top"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">(mettre 
                            &agrave; 0 pour d&eacute;sactiver)</font></td>
                          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                            <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='nb_war3'");
						  $disp = mysql_fetch_array($req);
						  ?>
                            <select name="nb_war3" id="nb_war3">
                              <option value="0" <? if ($disp[valeur] == '0') { echo "selected"; } ?>>0</option>
                              <option value="1" <? if ($disp[valeur] == '1') { echo "selected"; } ?>>1</option>
                              <option value="2" <? if ($disp[valeur] == '2') { echo "selected"; } ?>>2</option>
                              <option value="3" <? if ($disp[valeur] == '3') { echo "selected"; } ?>>3</option>
                              <option value="4" <? if ($disp[valeur] == '4') { echo "selected"; } ?>>4</option>
                              <option value="5" <? if ($disp[valeur] == '5') { echo "selected"; } ?>>5</option>
                              <option value="6" <? if ($disp[valeur] == '6') { echo "selected"; } ?>>6</option>
                            </select>
                            pour Warcraft III </font></td>
                          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                            |
						  <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='type_war3'");
						  $disp = mysql_fetch_array($req);
						  ?>
                            <select name="type_war3" id="type_war3">
                              <option value="LAN Party" <? if ($disp[valeur] == 'LAN Party') { echo "selected"; } ?>>LAN Party</option>
                              <option value="Internet" <? if ($disp[valeur] == 'Internet') { echo "selected"; } ?>>Internet</option>
                              <option value="LAN Party &amp; Internet" <? if ($disp[valeur] == 'LAN Party &amp; Internet') { echo "selected"; } ?>>LAN Party 
                              &amp; Internet</option>
                            </select>
                            </font></td>
                        </tr>
                        <!-- Boutons -->
                        <!-- Boutons -->
                      </table></td>
                  </tr>
                  <tr> 
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Charte 
                      g&eacute;n&eacute;rale :</font></td>
                  </tr>
                  <tr> 
                    <td>
					<?
					$req = MYSQL_QUERY("SELECT * FROM config WHERE nom='charte_gen'");
					$disp = mysql_fetch_array($req);
					?>
					<div align="center">
                        <textarea name="charte_gen" rows="8" id="charte_gen" style="width:550px"><? echo $disp[valeur]; ?></textarea>
                      </div></td>
                  </tr>
                  <tr> 
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Charte 
                      pour Counter-Strike :</font></td>
                  </tr>
                  <tr> 
                    <td>
					<?
					$req = MYSQL_QUERY("SELECT * FROM config WHERE nom='charte_cs'");
					$disp = mysql_fetch_array($req);
					?>
					<div align="center">
                        <textarea name="charte_cs" rows="8" id="charte_cs" style="width:550px"><? echo $disp[valeur]; ?></textarea>
                      </div></td>
                  </tr>
                  <tr> 
                    <td height="18">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td height="18"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Charte 
                      pour Warcraft III :</font> </td>
                  </tr>
                  <tr> 
                    <td>
					<?
					$req = MYSQL_QUERY("SELECT * FROM config WHERE nom='charte_war3'");
					$disp = mysql_fetch_array($req);
					?>
					<div align="center">
                        <textarea name="charte_war3" rows="8" id="charte_war3" style="width:550px"><? echo $disp[valeur]; ?></textarea>
                      </div></td>
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

