<?
include "secu.php";?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");


$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='titre'");

$titre       = stripslashes(mysql_result($req,0,"valeur"));

$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='r1'");

$r1       = stripslashes(mysql_result($req,0,"valeur"));

$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='r2'");

$r2       = stripslashes(mysql_result($req,0,"valeur"));

$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='r3'");

$r3       = stripslashes(mysql_result($req,0,"valeur"));

$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='r4'");

$r4       = stripslashes(mysql_result($req,0,"valeur"));

$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='r5'");

$r5       = stripslashes(mysql_result($req,0,"valeur"));
?>
            
<div align="center"><table width="500" border="0" cellspacing="0" cellpadding="0" height="782"> 
  <tr> <td colspan="2" height="16"><table width="465" border="0" cellspacing="0" cellpadding="0" align="left"> 
  <tr> <td width="439" height="22" valign="baseline"> 
  <div align="right"></div>
  </td><td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td></tr> 
  <tr> <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF"><b>Changement 
  de Sondage</b></font><font color="#FFFFFF">=-</font></b></font></td></tr> <tr> 
  <td colspan="2"></td></tr> </table></td></tr> <tr> <td width="41" height="800"></td><td width="544" height="800"><table border="0" cellpadding="4" cellspacing="0" width="490" height="806"> 
  <tr> <td nowrap height="800" valign="TOP"> 
  <form method="POST" action="admin/modification6.php3" enctype="multipart/form-data">
    <div align="CENTER"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><br>
      </font></b></div>
    <table width="350" border="0" cellspacing="0" cellpadding="0" bordercolor="#DE0200" height="20" bgcolor="#DE0200" align="CENTER"><tr><td width="10" height="10" valign="TOP"><img src="images/littlehautgauche.gif" width="10" height="10" align="TOP"> 
    </td><td width="82" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Titre:</font></div>
    </td><td width="186" height="10" valign="MIDDLE"><p align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                        <input type="text" name="titre" size="35" value="<? echo $titre; ?>"> 
    </font></p></td><td width="11" height="10" valign="top">
    <div align="RIGHT"><img src="images/littlehautdroite.gif" width="10" height="10"></div>
    </td></tr><tr><td width="10" height="10" valign="BOTTOM">&nbsp;</td><td width="82" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Reponse 
      1:</font></div>
    </td><td width="186" height="10" valign="MIDDLE"> 
    <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                        <input type="text" name="r1" size="25" value="<? echo $r1; ?>">
                        </font></div>
    </td><td width="11" height="10" valign="BOTTOM">&nbsp;</td></tr><tr><td width="10" height="10" valign="BOTTOM">&nbsp;</td><td width="82" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Reponse 
      2:</font></div>
    </td><td width="186" height="10" valign="MIDDLE"> 
    <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                        <input type="text" name="r2" size="25" value="<? echo $r2; ?>">
                        </font></div>
    </td><td width="11" height="10" valign="BOTTOM">&nbsp;</td></tr><tr><td width="10" height="10" valign="BOTTOM">&nbsp;</td><td width="82" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Reponse 
      3:</font></div>
    </td><td width="186" height="10" valign="MIDDLE"> 
    <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                        <input type="text" name="r3" size="25" value="<? echo $r3; ?>">
                        </font></div>
    </td><td width="11" height="10" valign="BOTTOM">&nbsp;</td></tr><tr><td width="10" height="10" valign="BOTTOM">&nbsp;</td><td width="82" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Reponse 
      4:</font></div>
    </td><td width="186" height="10" valign="MIDDLE"> 
    <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                        <input type="text" name="r4" size="25" value="<? echo $r4; ?>">
                        </font></div>
    </td><td width="11" height="10" valign="BOTTOM">&nbsp;</td></tr><tr><td width="10" height="10" valign="BOTTOM"><img src="images/littlebasgauche.gif" width="10" height="10"></td><td width="82" height="10" valign="MIDDLE"> 
    <div align="RIGHT"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Reponse 
      5:</font></div>
    </td><td width="186" height="10" valign="MIDDLE"> 
    <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                        <input type="text" name="r5" size="25" value="<? echo $r5; ?>">
                        </font></div>
    </td><td width="11" height="10" valign="BOTTOM">
    <div align="RIGHT"><img src="images/littlebasdroite.gif" width="10" height="10"></div>
    </td></tr> 
    </table>
    <div align="center"> <input type="submit" name="envoi" value="Valider les modifications ..." style="width: 200px"> 
    </div>
  </form>
  </td></tr></table></td></tr> </table></div>

