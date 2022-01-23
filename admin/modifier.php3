<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS WHERE id='$id'");

$id         = mysql_result($req,0,"id");
$titre      = stripslashes(mysql_result($req,0,"titre"));
$signature  = stripslashes(mysql_result($req,0,"signature"));
$email_sign = mysql_result($req,0,"email_sign");
$nom_source = stripslashes(mysql_result($req,0,"nom_source"));
$url_source = mysql_result($req,0,"url_source");
$path_image = mysql_result($req,0,"path_image");
$url_image  = mysql_result($req,0,"url_image");
$news       = stripslashes(mysql_result($req,0,"news"));
?>
            <div align="center">
              <table width="500" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="2"> 
                    <table width="465" border="0" cellspacing="0" cellpadding="0" align="left">
                      <tr> 
                        <td width="439" height="22" valign="baseline"> 
                          <div align="right"></div>
                        </td>
                        
            <td width="38" height="42" rowspan="2"><img src="../images/oeuf2.gif" width="31" height="42"></td>
                      </tr>
                      <tr> 
                        
            <td width="439" height="20" background="../images/fond.gif"><img src="../images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF"><b>Modification 
              de la news n° 
              <? echo $id; ?>
              </b></font><font color="#FFFFFF">=-</font></b></font></td>
                      </tr>
                      <tr> 
                        <td colspan="2">&nbsp;</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="25">&nbsp;</td>
                  <td width="475">
                    <table border="0" cellpadding="4" cellspacing="0" width="490">
                      <tr> 
                        <td nowrap> 
                          <form method="POST" action="modification.php3">
                            <p> 
                              <input type="hidden" value="<? echo $id; ?>" name="id">
                            </p>
                            <p><font face=arial color=black size=2> 
                              <input type="text" style="width: 300px" value="<? echo $titre; ?>" name="titre">
                              Titre<br>
                              <input type="text" style="width: 300px" value="<? echo $signature; ?>" name="signature">
                              Signature<br>
                              <input type="text" style="width: 300px" value="<? echo $email_sign; ?>" name="email_sign">
                              Email sur signature<br>
                  <br>
                              <input type="text" style="width: 300px" value="<? echo $url_source; ?>" name="url_source">
                  Sorte de News<br>
                  <br>
                  </font></p>
                            <p align="center"><font face=arial color=black size=2><br>
                              <br>
                              <textarea name="news" wrap="virtual" style="width: 450px; height: 300px" valign="top"><? echo str_replace("<br>","",$news); ?></textarea>
                              </font> <br>
                              <br>
                            </p>
                            <div align="center"> 
                              <input type="submit" name="envoi" value="Valider les modifications ..." style="width: 200px">
                            </div>
                          </form>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <br>
<input type="button" value="<< Retour à la liste" onClick="Javascript:window.location='../../index.php?page=modiflistnews'" style="width: 200px">
</div>

