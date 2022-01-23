<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM phrases WHERE phrase like '%$phrase%'");

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
    src: url(http://10.0.0.1/nukedklan/MINNIE0.eot);
  }
-->
</STYLE>
<div align="center">
              
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="center">
          <table width="465" border="0" align="left" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="38" height="42" rowspan="2"><img src="../images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="../images/fond.gif"><img src="../images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Modification 
                de la Phrase &agrave; la con=-</font></b></font></td>
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
            <td nowrap> <form method="POST" action="modif_phrase2.php">
                <p> 
                  <input name="old_phrase" type="hidden" id="old_phrase" value="<? echo $phrase; ?>">
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" width="250"> <div align="center"> 
                        <input name="phrase" type="text" id="phrase" value="<? echo $disp[phrase]; ?>" size="70">
                      </div></td>
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

