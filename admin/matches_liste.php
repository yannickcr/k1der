<?
include "secu.php";?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM matches ORDER BY orderdate DESC");
$res = MYSQL_NUM_ROWS($req);
$nbre =mysql_num_rows($req);

?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<style>
<!--
.m10,.m9,.m10b,.m9b {font-family:verdana;}
.m8,.m8b            {font-family:verdana;}
.m10,.m10b          {font-size:10pt;}
.m9,.m9b            {font-size:9pt;}
.m8,.m8b            {font-size:8pt;}
.m10b,.m9b,.m8b     {font-weight:bold;}

A:link    {text-decoration: none; color: #DE0200;}
A:visited {text-decoration: none; color: #DE0200;}
A:active  {text-decoration: none; color: #DE0200;}
A:hover   {text-decoration: underline; color: red;}
-->
</style>

<title></title>

<script language="Javascript">
function Supprimer(data,data2)
{
 resultat = confirm('Voulez-vous vraiment supprimer le match contre les '+data2+'  ?');
 if(resultat==1)
 {
  window.location='admin/suppr_matche.php?id='+data;
 }
 else
 {
  alert('Suppression annul�e !');
 }
}

</script>


</head>

<body>
<div align="center">
  <center>
    <div align="left"></div>
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td colspan="2"> <div align="left"><font color="#FFFF00" size="5" face="Minnie"> 
            </font> 
            <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
                <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
              </tr>
              <tr> 
                <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<? if ($action == "modif") { echo "Modifier"; } else { echo "Supprimer"; } ?>
                  un Matche=-</font></b></font></td>
              </tr>
              <tr> 
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
          </div></td>
      </tr>
      <tr> 
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr> 
        <td width="25">&nbsp;</td>
        <td width="25"> <table border="0" cellpadding="4" width="475" cellspacing="0" height="40">
            <tr valign="bottom"> 
              <td colspan="2" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10"> 
                <? echo "Total: $nbre"; ?> </font></b></font></td>
            </tr>
            <?
while($disp = mysql_fetch_array($req))
{
?>
            <tr> 
              <td class="m9" width="460"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>-=K<font color="#CC0000">1der</font>=-</b> 
                vs <b><? echo $disp[mechants]; ?></b></font></td>
              <td align="center" class="m9"> 
                <?
		if ($action == 'modif')
		{
		echo "<b><a class=type1 href=\"index.php?page=modif_matche&id=$disp[id]\">Modifier</a></b>";
		}
		else
		{
		echo "<b><a class=type1 href=\"Javascript:Supprimer('$disp[id]','$disp[mechants]');\">Supprimer</a></b>";
		}
		?>
              </td>
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
<input type="button" value="Retour � la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>
</body>
</html>