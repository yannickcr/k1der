<? include("secu.php"); ?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS WHERE conf = '0' ORDER BY id DESC");
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
              <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF">Confirmer 
                une News</font><font color="#FFFFFF">=-</font></b></font></td>
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
              <td width="332" align="center" class="m9"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>News</b></font></td>
              <td width="252" align="center" class="m9"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Action</b></font></td>
            </tr>
            <?
$i=0;
WHILE($i!=$res)
{
$id        = mysql_result($req,$i,"id");
$titre     = mysql_result($req,$i,"titre");
$date      = mysql_result($req,$i,"date");
?>
            <tr> 
              <td width="332" class="m9"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo "$titre"; ?> 
                </font></td>
              <td align="center" class="m9"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=visu_news&id=<? echo $id; ?>">Visualiser</a> 
                / <a href="admin/news_conf.php?id=<? echo $id; ?>"><font color="#DE0200">Confirmer</font></a> 
                / <a href="admin/news_noconf.php?id=<? echo $id; ?>"><font color="#DE0200">Envoyer 
                chier</font></a></font></td>
            </tr>
            <?
$i++;
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
