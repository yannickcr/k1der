<? $level = '10'; include "secu.php";

?>
<form name="form1" method="post" action="admin/modif_pages_levels.php">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <?
  $raquete  = "SELECT * FROM admin_cat ORDER BY ordre";
  $raq = mysql_query($raquete) or die('Erreur SQL !<br>'.$raquete.'<br>'.mysql_error());
  while($dasp = mysql_fetch_array($raq))
  {
  ?>
    <tr> 
      <td width="300"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><em><strong><font size="2" face="Arial, Helvetica, sans-serif"><? echo $dasp[nom]; ?></font></strong></em></font></td>
      <td width="300" valign="middle" nowrap>&nbsp; </td>
    </tr>
    <tr> 
      <td colspan="2"><em><strong></strong></em> <table width=100% border=0 cellpadding="0" cellspacing="0">
          <?
	  $requete  = "SELECT * FROM admin WHERE cat_id='$dasp[id]' ORDER BY ordre";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	  while($disp = mysql_fetch_array($req))
	  {
	  $i++;
	  ?>
          <tr> 
            <TD width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color=#cc0000>&nbsp;<? echo $disp[texte]; ?></font></TD>
            <TD width="300" valign="middle"><select name="nivo[<? echo $i; ?>]">
                <option value="0" <? if ($disp[level] == '0') { echo "selected"; } ?>>0</option>
                <option value="1" <? if ($disp[level] == '1') { echo "selected"; } ?>>1</option>
                <option value="2" <? if ($disp[level] == '2') { echo "selected"; } ?>>2</option>
                <option value="3" <? if ($disp[level] == '3') { echo "selected"; } ?>>3</option>
                <option value="4" <? if ($disp[level] == '4') { echo "selected"; } ?>>4</option>
                <option value="5" <? if ($disp[level] == '5') { echo "selected"; } ?>>5</option>
                <option value="6" <? if ($disp[level] == '6') { echo "selected"; } ?>>6</option>
                <option value="7" <? if ($disp[level] == '7') { echo "selected"; } ?>>7</option>
                <option value="8" <? if ($disp[level] == '8') { echo "selected"; } ?>>8</option>
                <option value="9" <? if ($disp[level] == '9') { echo "selected"; } ?>>9</option>
                <option value="10" <? if ($disp[level] == '10') { echo "selected"; } ?>>10</option>
              </select> <input name='id[<? echo $i; ?>]' type='hidden' value='<? echo $disp[id]; ?>'> 
            </TD>
          </tr>
          <?
	  }
	  ?>
          <tr> 
            <td colspan="2" height="5"></td>
          </tr>
        </table>
        <font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
      </td>
    </tr>
    <?
  }
  ?>
  </table>
  <div align="center">
<input name="i" type="hidden" value="<? echo $i; ?>">
<input type="submit" name="Submit" value="Valider les modifications">
  </div>
</form>
<br>
<form>
  <div align="center">
    <input type="button" value="Retour à la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
  </div>
</form>

