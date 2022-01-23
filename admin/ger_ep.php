<?
include "secu.php";

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

?>
<script language="JavaScript" type="text/JavaScript">
function checkCheckBox(form)
{
  if (form.inconnue.checked == true )
    {
	document.getElementById('jour').disabled = true;
	document.getElementById('mois').disabled = true;
	document.getElementById('annee').disabled = true;
    }
  else
    {
	document.getElementById('jour').disabled = false;
	document.getElementById('mois').disabled = false;
	document.getElementById('annee').disabled = false;
    }
}


</script>
<title></title>
<body>
<div align="center">
              
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    
    
    <tr> 
      <td width="475"> <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="admin/ger_ep2.php">
                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" colspan="3"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
                        </tr>
                        <tr> 
                          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                            G&eacute;rer l'avancement des &eacute;pisodes=-</font></b></font></td>
                        </tr>
                        <tr> 
                          <td colspan="2">&nbsp;</td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr> 
                    <td colspan="3">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td colspan="3"> <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="20%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Titre 
                            : </font></td>
                          <td width="80%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                            <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='episode'");
						  $disp = mysql_fetch_array($req);
						  ?>
                            <input name="episode" type="text" id="episode" value="<? echo $disp[valeur]; ?>" size="26" maxlength="26">
                            </font></td>
                        </tr>
                        <!-- Boutons -->
                        <!-- Boutons -->
                      </table></td>
                  </tr>
                  <tr> 
                    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                  </tr>
                  <tr> 
                    <td width="200"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Sc&eacute;nario 
                        : 
                        <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='ep_scen'");
						  $disp = mysql_fetch_array($req);
						  ?>
                        <input name="ep_scen" type="text" id="ep_scen" value="<? echo $disp[valeur]; ?>" size="2" maxlength="3">
                        % </font></div></td>
                    <td width="200"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Story-Board 
                        : 
                        <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='ep_story'");
						  $disp = mysql_fetch_array($req);
						  ?>
                        <input name="ep_story" type="text" id="ep_story" value="<? echo $disp[valeur]; ?>" size="2" maxlength="3">
                        %</font></div></td>
                    <td width="200"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Flash 
                        : 
                        <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='ep_flash'");
						  $disp = mysql_fetch_array($req);
						  ?>
                        <input name="ep_flash" type="text" id="ep_flash" value="<? echo $disp[valeur]; ?>" size="2" maxlength="3">
                        %</font></div></td>
                  </tr>
                  <tr>
                    <td colspan="3">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td colspan="3"> 
                      <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="20%" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                            de sortie :</font></td>
                          <td width="80%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                            <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='ep_date'");
						  $disp = mysql_fetch_array($req);
						  $jour = date("d",$disp[valeur]);
						  $mois = date("m",$disp[valeur]);
						  $annee = date("Y",$disp[valeur]);
						  ?><input type="text" name="jour" maxlength="2" size="1" value=<? echo $jour; ?> <? if ($disp[valeur] == "Non prévue") { echo "disabled"; } ?>>
                            <select name="mois" id="mois" <? if ($disp[valeur] == "Non prévue") { echo "disabled"; } ?>>
                              <option value="01"
	<?
if ($mois =="01")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
                              <option value="02"
	<?
if ($mois =="02")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
                              <option value="03"
	<?
if ($mois =="03")
	{
		echo " SELECTED";
	}
?>>Mars</option>
                              <option value="04"
	<?
if ($mois =="04")
	{
		echo " SELECTED";
	}
?>>Avril</option>
                              <option value="05"
	<?
if ($mois =="05")
	{
		echo " SELECTED";
	}
?>>Mai</option>
                              <option value="06"
	<?
if ($mois =="06")
	{
		echo " SELECTED";
	}
?>>Juin</option>
                              <option value="07"
	<?
if ($mois =="07")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
                              <option value="08"
	<?
if ($mois =="08")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
                              <option value="09"
	<?
if ($mois =="09")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
                              <option value="10"
	<?
if ($mois =="10")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
                              <option value="11"
	<?
if ($mois =="11")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
                              <option value="12"
	<?
if ($mois =="12")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
                            </select>
                            <input type="text" name="annee" maxlength="4" size="2" value="<? echo $annee; ?>" <? if ($disp[valeur] == "Non prévue") { echo "disabled"; } ?>>
                            <br>
                            <input type="checkbox" name="inconnue" value="1" onClick="return checkCheckBox(this.form)" <? if ($disp[valeur] == "Non prévue") { echo "checked"; } ?>>
                            Non pr&eacute;vue</font></td>
                        </tr>
                        <!-- Boutons -->
                        <!-- Boutons -->
                      </table></td>
                  </tr>
                  <tr> 
                    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
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

