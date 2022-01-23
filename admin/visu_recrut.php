<? include("secu.php"); ?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM recrutement ORDER BY id DESC");
$res = MYSQL_NUM_ROWS($req);
?>
<div align="center">
  <center>
    <div align="left"></div>
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td> <table width="600" border="0" cellspacing="0" cellpadding="0" align="left">
            <tr> 
              <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF">Voir les demandes de recrutement</font><font color="#FFFFFF">=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
          <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10" color="#000000"> 
          </font></b></font></td>
      </tr>
      <tr> 
        <td width="25"> <table border="0" cellpadding="4" width="600" cellspacing="0" height="40">
            <tr valign="bottom"> 
              <td colspan="2" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10" color="#000000"> 
                <? echo "Total: $res"; ?> </font></b></font></td>
            </tr>
            <tr> 
              <td width="332" align="center" class="m9"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
              <td width="252" align="center" class="m9"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Action</b></font></td>
            </tr>
            <?
			while($disp = mysql_fetch_array($req))
			{
			?>
            <tr> 
              <td width="332" class="m9"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
			  <?
			  echo "(".ucfirst($disp[section]).") <b>$disp[pseudo]</b> :";
			  if ($disp[section] == 'cs')
			  {
			  echo " $disp[style]";
			  }
			  else
			  {
			  echo " Level $disp[level]";
			  }
			  if ($disp[lu] == 0)
			  {
			  ?> <font color="#FF0000">Pas lu</font>
			  <?
			  }
			  ?>
                </font></td>
              <td align="center" class="m9"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=visu_details_recrut&id=<? echo $disp[id]; ?>">Voir 
                les d&eacute;tails</a> / <a href="admin/suppr_recrut.php?id=<? echo $disp[id]; ?>"><font color="#DE0200">Supprimer</font></a></font></td>
            </tr>
            <?
}
?>
          </table></td>
      </tr>
    </table>
  </center>
<br>
<form>
<input type="button" value="<< Retour à l'index" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>
