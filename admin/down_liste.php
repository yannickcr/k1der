<?
include "secu.php";
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM liens_down ORDER BY id DESC");
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
	window.open("admin/modif_down.php?id="+data,"Modifier","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=0,height=350,width=500")
//	window.open'admin/modifier.php3?id='+data;
}


function Supprimer(data,data2)
{
 resultat = confirm('Voulez-vous vraiment supprimer le download '+data2+'  ?');
 if(resultat==1)
 {
  window.location='admin/suppr_down.php?id='+data;
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
    <table width="500" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td colspan="2"> <div align="center">
            <table width="590" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
                <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
              </tr>
              <tr> 
                <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                  <?
		if ($action == 'modif')
		{
		echo 'Modifier';
		}
		else
		{
		echo 'Supprimer';
		}
		?>
                  un download</font></b><font color="#FFFFFF">=-</font></font></td>
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
              <td colspan="2" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10"> 
                <? echo "Total: $nbre"; ?> </font></b><br>
                <br>
                </font></td>
            </tr>
            <tr> 
              <td width="460" align="center" class="m9"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Download</font></strong></td>
              <td align="center" class="m9"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Action</font></strong></td>
            </tr>
            <?
while($disp = mysql_fetch_array($req))
{
?>
            <tr> 
              <td class="m9" width="460"> <font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[nom]; ?> 
                </font></td>
              <td align="center" class="m9"> 
                <?
		if ($action == 'modif')
		{
		echo "<b><a class=type1 href=\"Javascript:Modifier('$disp[id]');\">Modifier</a></b>";
		}
		else
		{
		echo "<b><a class=type1 href=\"Javascript:Supprimer('$disp[id]','$disp[nom]');\">Supprimer</a></b>";
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
<input type="button" value="Retour à la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>
</body>
</html>