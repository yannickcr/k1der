<?
include "secu.php";?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM anniv WHERE id='$id'");

$disp = mysql_fetch_array($req);
?>
<title></title>
<body>
<div align="center">
              
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="center">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Modification 
                de l'anniversaire=-</font></b></font></td>
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
            <td nowrap> <form method="POST" action="admin/modif_anniv2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
                      : </font></td>
                    <td height="2" width="250"> <input name="nom" type="text" id="nom" value="<? echo $disp[nom]; ?>" size="30"></td>
                  </tr>
                  <tr> 
                    <td height="24" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                      de naissance: 
                      <?
					  $ladate = date2timestamp("$disp[date]","md");

					  $disp[jour] = substr($disp[date],2,4);
					  $disp[mois] = substr($disp[date],0,2);
					  $disp[annee] = $disp[an];
					  ?>
                      </font></td>
                    <td height="24" width="250"> <font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                      <input type="text" name="jour" maxlength="2" size="1" value=<? echo $disp[jour]; ?>>
                      <select name="mois">
                        <option value="01"
	<?
if ($disp[mois] =="01")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
                        <option value="02"
	<?
if ($disp[mois] =="02")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
                        <option value="03"
	<?
if ($disp[mois] =="03")
	{
		echo " SELECTED";
	}
?>>Mars</option>
                        <option value="04"
	<?
if ($disp[mois] =="04")
	{
		echo " SELECTED";
	}
?>>Avril</option>
                        <option value="05"
	<?
if ($disp[mois] =="05")
	{
		echo " SELECTED";
	}
?>>Mai</option>
                        <option value="06"
	<?
if ($disp[mois] =="06")
	{
		echo " SELECTED";
	}
?>>Juin</option>
                        <option value="07"
	<?
if ($disp[mois] =="07")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
                        <option value="08"
	<?
if ($disp[mois] =="08")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
                        <option value="09"
	<?
if ($disp[mois] =="09")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
                        <option value="10"
	<?
if ($disp[mois] =="10")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
                        <option value="11"
	<?
if ($disp[mois] =="11")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
                        <option value="12"
	<?
if ($disp[mois] =="12")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
                      </select>
                      <input type="text" name="annee" maxlength="4" size="2" value=<? echo $disp[annee]; ?>>
                      </font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
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

