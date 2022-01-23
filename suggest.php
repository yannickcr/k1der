<?
if(empty($pseudo) || empty($txt))
{
if ($Submit) echo "<center><font color=red face=\"Verdana, Arial, Helvetica, sans-serif\" size=2><b>Attention, un champ est vide !</font></b></center>";
}
if ($Submit && !empty($pseudo) && !empty($txt))
{
$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base est Down !</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE;

$txt = nl2br($txt);

$req = MYSQL_QUERY("SELECT * FROM suggest ORDER BY id DESC");
$disp = mysql_fetch_array($req);
if ($disp[txt] != $txt && $disp[pseudo] != $pseudo)
{
$requetes  = "SELECT * FROM suggest2 ORDER BY rand() LIMIT 1";
$reqs = mysql_query($requetes) or die('Erreur SQL !<br>'.$requetes.'<br>'.mysql_error());
$disps = mysql_fetch_array($reqs);
$profil = $disps[txt];

mysql_query("INSERT INTO suggest VALUES('','$pseudo','$profil','$txt')");
}
echo "<br><br><center><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Merci d'avoir contribué au site</font></center>";
echo "<br><br><br><br><center><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><a href=\"index.php?page=news\">Retour aux News</a></font></center>";
}
else
{
?>
<form name="form1" method="post" action="">
  <table width="465" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
      <td width="446" height="22" valign="baseline">
        <div align="right"></div></td>
      <td width="31" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
    </tr>
    <tr>
      <td width="446" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Suggestion/Bug=-</font></b></font></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pseudo: 
        <input name="pseudo" type="text" id="pseudo" value="<? echo $pseudo; ?>">
      </font></td>
    </tr>
    <tr>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Suggestion/Bug trouv&eacute;: </font></td>
    </tr>
    <tr>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <textarea name="txt" rows="5" id="txt" style="width:100%"><? echo $txt; ?></textarea>
      </font></td>
    </tr>
    <tr>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr>
      <td colspan="2"><div align="center">
        <input type="submit" name="Submit" value="Envoyer">
      </div></td>
    </tr>
  </table>
</form>
<?
}
?>
