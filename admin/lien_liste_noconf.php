<?
include "secu.php";?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM liens WHERE conf = '0' ORDER BY id DESC");
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

<title>ADMIN - MyNEWS v1.2</title>

<script language="Javascript">
function Modifier(data)
	{
	window.open("admin/modif_lien.php?id="+data,"Modifier","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=0,height=300,width=500")
//	window.open'admin/modifier.php3?id='+data;
}


function Supprimer(data,data2)
{
 resultat = confirm('Voulez-vous vraiment supprimer le lien '+data2+'  ?');
 if(resultat==1)
 {
  window.location='admin/suppr_lien.php?id='+data;
 }
 else
 {
  alert('Suppression annulée !');
 }
}
</script>


</head>

<body bgcolor="#EEEEFC">

<div align="center">
  <center>
    <div align="left"></div>
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td> <div align="left"><font color="#FFFF00" size="5" face="Minnie"> </font> 
            <table width="600" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
              </tr>
              <tr> 
                <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                  Confirmer un lien=-</font></b></font></td>
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
        <td width="25"> <table border="0" cellpadding="4" width="600" cellspacing="0" height="40">
            <tr valign="bottom"> 
              <td colspan="2" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10"> 
                <? echo "Total: $nbre"; ?> </font></b></font></td>
            </tr>
            <?
while($disp = mysql_fetch_array($req))
{
?>
            <tr> 
              <td class="m9" width="400"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
<?
	if ($disp[image] != "")
	{
	if(ereg(".swf",$disp[image]))
	{
	?>
	<div align="center"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="88" height="31">
	<param name="movie" value="<? echo $disp[image]; ?>">
	<param name="quality" value="high">
	<embed src="<? echo $disp[image]; ?>" width="88" height="31" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed></object></div>
	<?
	}
	else
	{
	?>
	<div align="center"><a href="<? echo $disp[lien]; ?>" target="_blank"><img src="<? echo $disp[image]; ?>" border="0" alt="<? echo $disp[nom]; ?>"></a></div>
	<?
	}
	}
	else
	{
	?>
	<div align="center"><a class=type2 href="<? echo $disp[lien]; ?>" target="_blank"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[nom]; ?></font></a></div>
	<?
	}
	?>
 
                </font></td>
              <td align="center" class="m9"><a href="admin/lien_conf.php?id=<? echo $disp[id]; ?>">Confirmer</a> 
                / <a href="admin/lien_noconf.php?id=<? echo $disp[id]; ?>">Envoyer chier</a> </td>
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
<input type="button" value="Retour à la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>
</body>
</html>