<?
include "secu.php";?><?
$user = ucfirst($HTTP_COOKIE_VARS[gen]);
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");


$req = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$user'");

$nom          = mysql_result($req,0,"nom");
$pass       = stripslashes(mysql_result($req,0,"pass"));
?>
            
<div align="center"><table width="500" border="0" cellspacing="0" cellpadding="0" height="782"> 
  <tr> <td colspan="2" height="16"><table width="465" border="0" cellspacing="0" cellpadding="0" align="left"> 
  <tr> <td width="439" height="22" valign="baseline"> 
  <div align="right"></div>
  </td><td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td></tr> 
  <tr> <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF"><b>Modification 
  du Mot de Passe de
  <? echo $nuser; ?>
  </b></font><font color="#FFFFFF">=-</font></b></font></td></tr> <tr> <td colspan="2"></td></tr> 
  </table></td></tr> <tr> <td width="41" height="800"></td><td width="544" height="800"><table border="0" cellpadding="4" cellspacing="0" width="490" height="806"> 
  <tr> <td nowrap height="800" valign="TOP"> 
  <form method="POST" action="admin/modification3.php3">
    <div align="CENTER"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><br>
      </font></b></div>
    <table width="300" border="0" cellspacing="0" cellpadding="0" bordercolor="#DE0200" height="20" bgcolor="#DE0200" align="CENTER"><tr><td width="10" height="10" valign="TOP"><img src="images/littlehautgauche.gif" width="10" height="10" align="TOP"> 
    </td><td width="240" height="10" valign="MIDDLE"> 
    <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Nom:</font></div>
    </td><td width="10" height="20" valign="MIDDLE" rowspan="2">&nbsp;</td><td width="266" height="10" valign="MIDDLE"><p align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
    <? echo $user ; ?>
    </font></p></td><td width="10" height="10" valign="top"><img src="images/littlehautdroite.gif" width="10" height="10"></td></tr> 
    <tr><td width="10" height="10" valign="BOTTOM"><img src="images/littlebasgauche.gif" width="10" height="10"></td><td width="240" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Mot 
      de Passe:</font></div>
    </td><td width="266" height="10" valign="MIDDLE">
    <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><input type="text" name="pass" value="<? echo $pass ; ?>"></font></div>
    </td><td width="10" height="10" valign="BOTTOM"><img src="images/littlebasdroite.gif" width="10" height="10"></td></tr> 
    </table>
    <div align="center"> <input type="submit" name="envoi" value="Valider les modifications ..." style="width: 200px"> 
    </div>
  </form>
  </td></tr></table></td></tr> </table></div>

