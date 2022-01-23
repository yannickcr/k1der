<?
include "secu.php";?><?
require("admin/activeuser/user.txt");
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");


$req = MYSQL_QUERY("SELECT * FROM lan");

$jour          = mysql_result($req,0,"jour");
$mois       = stripslashes(mysql_result($req,0,"mois"));
$annee       = stripslashes(mysql_result($req,0,"annee"));
$nom       = stripslashes(mysql_result($req,0,"nom"));
$lanlien       = stripslashes(mysql_result($req,0,"lanlien"));
?>
            
<div align="center"><table width="500" border="0" cellspacing="0" cellpadding="0" height="782"> 
  <tr> <td colspan="2" height="16"><table width="465" border="0" cellspacing="0" cellpadding="0" align="left"> 
  <tr> <td width="439" height="22" valign="baseline"> 
  <div align="right"></div>
  </td><td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td></tr> 
  <tr> <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF"><b>Planification 
  dela prochaine Lan Arena</b></font><font color="#FFFFFF">=-</font></b></font></td></tr> 
  <tr> <td colspan="2"></td></tr> </table></td></tr> <tr> <td width="41" height="800"></td><td width="544" height="800"><table border="0" cellpadding="4" cellspacing="0" width="490" height="806"> 
  <tr> <td nowrap height="800" valign="TOP"> 
  <form method="POST" action="admin/modification4.php3" enctype="multipart/form-data">
    <div align="CENTER"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><br>
      </font></b></div>
                <table width="350" border="0" cellspacing="0" cellpadding="0" bordercolor="#DE0200" height="60" bgcolor="#DE0200" align="CENTER">
                  <tr> 
                    <td width="10" height="10" valign="TOP"><img src="images/littlehautgauche.gif" width="10" height="10" align="TOP"> 
                    </td>
                    <td width="240" height="10" valign="MIDDLE"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Date:</font></div></td>
                    <td width="10" height="20" valign="MIDDLE" rowspan="3">&nbsp;</td>
                    <td width="266" height="10" valign="MIDDLE"><p align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                        <input type="text" name="jour" maxlength="2" size="1" value=<? echo $jour; ?>>
                        <select name="mois">
                          <option value="January"
	<?
if ($mois =="January")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
                          <option value="February"
	<?
if ($mois =="February")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
                          <option value="March"
	<?
if ($mois =="March")
	{
		echo " SELECTED";
	}
?>>Mars</option>
                          <option value="April"
	<?
if ($mois =="April")
	{
		echo " SELECTED";
	}
?>>Avril</option>
                          <option value="May"
	<?
if ($mois =="May")
	{
		echo " SELECTED";
	}
?>>Mai</option>
                          <option value="June"
	<?
if ($mois =="June")
	{
		echo " SELECTED";
	}
?>>Juin</option>
                          <option value="July"
	<?
if ($mois =="July")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
                          <option value="August"
	<?
if ($mois =="August")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
                          <option value="September"
	<?
if ($mois =="September")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
                          <option value="October"
	<?
if ($mois =="October")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
                          <option value="November"
	<?
if ($mois =="November")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
                          <option value="December"
	<?
if ($mois =="December")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
                        </select>
                        <input type="text" name="annee" maxlength="4" size="2" value=<? echo $annee; ?>>
                        </font></p></td>
                    <td width="10" height="10" valign="top"><img src="images/littlehautdroite.gif" width="10" height="10"></td>
                  </tr>
                  <tr> 
                    <td height="10" valign="BOTTOM">&nbsp;</td>
                    <td height="10" valign="MIDDLE"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Dur&eacute;e 
                        :</font></div></td>
                    <td height="10" valign="MIDDLE"><div align="center">
                        <input name="duree" type="text" id="duree" value="<? echo $disp[duree]; ?>" size="2" maxlength="3">
                        <font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">jours</font></div></td>
                    <td height="10" valign="BOTTOM">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td width="10" height="10" valign="BOTTOM">&nbsp;</td>
                    <td width="240" height="10" valign="MIDDLE"> <div align="RIGHT"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom:</font></div></td>
                    <td width="266" height="10" valign="MIDDLE"> <div align="CENTER"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                        <input type="text" name="nom" value="<? echo $nom ; ?>" size="20" maxlength="20">
                        </font></div></td>
                    <td width="10" height="10" valign="BOTTOM">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td width="10" height="10" valign="BOTTOM"><img src="images/littlebasgauche.gif" width="10" height="10"></td>
                    <td width="240" height="10" valign="MIDDLE"> <div align="RIGHT"><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Lien:</font></div></td>
                    <td width="10" height="20" valign="MIDDLE">&nbsp;</td>
                    <td width="266" height="10" valign="MIDDLE"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                      <input type="text" name="lanlien" value="<? echo $lanlien ; ?>" size="40" maxlength="250">
                      </font></td>
                    <td width="10" height="10" valign="BOTTOM"><img src="images/littlebasdroite.gif" width="10" height="10"></td>
                  </tr>
                </table>
    <div align="center"> <input type="submit" name="envoi" value="Valider les modifications ..." style="width: 200px"> 
    </div>
  </form>
  </td></tr></table></td></tr> </table></div>

