<? $level = "10"; include "secu.php";

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM equipe ORDER by kinder");
$res = MYSQL_NUM_ROWS($req);
$nbre =mysql_num_rows($req);

?>
<div align="center">
  <center>
    <div align="left"></div>
	<form name="form1" method="post" action="admin/modif_membres_levels.php">
      <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td> <div align="left"><font color="#FFFF00" size="5" face="Minnie"> </font> 
            <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
                <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
              </tr>
              <tr> 
                  <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                    Modifier les Levels des membre=-</font></b></font></td>
              </tr>
              <tr> 
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
          </div></td>
      </tr>
      <tr> 
        <td>&nbsp;</td>
      </tr>
      <tr> 
        <td width="25"> <table border="0" cellpadding="4" width="600" cellspacing="0">
              <tr valign="bottom"> 
                <td colspan="3" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10"><? echo "Total: $nbre"; ?></font></b></font></td>
              </tr>
              <tr valign="bottom"> 
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Levels</strong></font></td>
              </tr>
              <?
			  $i = 0;
while($disp = mysql_fetch_array($req))
{
$i++;
?>
              <tr> 
                <td class="m9" width="50"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                <td class="m9" width="400"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $disp[kinder]; ?></b></font> 
                </td>
                <td width="200" align="center" class="m9">
				<?
				if($disp[kinder] != "Country")
				{
				?><select name="nivo[<? echo $i; ?>]">
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
                  </select><input name='id[<? echo $i; ?>]' type='hidden' value='<? echo $disp[id]; ?>'>
				  <?
				  }
				  else
				  {
				  echo "<input name='id[$i]' type='hidden' value='$disp[id]'>";
				  echo "<input name='nivo[$i]' type='hidden' value='$disp[level]'>";
				  echo "<font size='2' face='Verdana, Arial, Helvetica, sans-serif'><b>$disp[level]</b></font>";
				  }
				  ?></td>
              </tr>
              <?
}
?>
            </table></td>
      </tr>
    </table>
      <br>
      <input type="submit" name="Submit" value="Valider les modifications">
    </form>
  </center>
<br>
<form>
<input type="button" value="Retour à la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>