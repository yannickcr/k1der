<?
include "secu.php";?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS ORDER BY id DESC");
$res = MYSQL_NUM_ROWS($req);
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
	window.open("admin/modifier.php3?id="+data,"Modifier","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=0,height=600,width=500")
//	window.open'admin/modifier.php3?id='+data;
}


function Supprimer(data)
{
 resultat = confirm('Voulez-vous vraiment supprimer la News n°'+data+' de la base ?');
 if(resultat==1)
 {
  window.location='admin/supprimer.php3?id='+data;
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
        <td colspan="2"> 
          <table width="465" border="0" cellspacing="0" cellpadding="0" align="left">
            <tr> 
              <td width="439" height="22" valign="baseline"> 
                <div align="right"></div>
              </td>
              <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF">Modifier 
                / Supprimer une News</font><font color="#FFFFFF">=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
          <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10" color="#000000"> 
          </font></b></font></td>
      </tr>
      <tr> 
        <td width="25">&nbsp;</td>
        <td width="25"> 
          <table border="0" cellpadding="4" width="475" cellspacing="0" height="40">
            <tr valign="bottom"> 
              <td colspan="4" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10" color="#000000"> 
                <? echo "Total: $res"; ?>
                </font></b></font></td>
            </tr>
            <tr bgcolor="#DE0200"> 
              <td class="m9" width="100" align="center"><b><font color="#FFFFFF">Date</font></b></td>
              <td class="m9" width="460" align="center"><b><font color="#FFFFFF">News</font></b></td>
              <td class="m9" width="70" align="center"><b><font color="#FFFFFF">Modifier</font></b></td>
              <td class="m9" width="70" align="center"><b><font color="#FFFFFF">Supprimer</font></b></td>
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
              <td class="m9" width="100" align="center"> 
                <? echo $date; ?>
              </td>
              <td class="m9" width="460"> 
                <? echo "$titre <i>(n°$id)</i>"; ?>
              </td>
              <td class="m9" width="70" align="center">[<b><a href="index.php?page=modif_news&id=<? echo $id; ?>"><font color="#DE0200">modifier</font></a></b>]</td>
              <td class="m9" width="70" align="center">[<b><a href="Javascript:Supprimer('<? echo $id; ?>');"><font color="#DE0200">supprimer</font></a></b>]</td>
            </tr>
            <?
$i++;
}
?>
          </table>
        </td>
      </tr>
    </table>
  </center>
<br>
<form>
<input type="button" value="<< Retour à l'index" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>
</body>
</html>