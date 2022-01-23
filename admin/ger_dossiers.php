<? include "secu.php"; ?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<body>
<?
$date = date("d/m/Y");

if ($new == 1)
{
$auteur = ucfirst($HTTP_COOKIE_VARS[gen]);
mysql_query("INSERT INTO dossiers VALUES('','$auteur','$date','Nouveau Dossier','','','0')");
}

?>
  
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="2" colspan="3"> <div align="left"> 
        <table width="500" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="100%" height="22" valign="baseline"> <div align="right"></div></td>
            <td height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
          </tr>
          <tr> 
            <td width="100%" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=G&eacute;rer 
              les dossiers=-</font></b></font></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </div></td>
  </tr>
  <tr> 
    <td height="2" colspan="3"> <table width="500" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="250"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=ger_dossiers&new=1">Nouveau 
              dossier</a></font></div></td>
          <td width="250">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="300" height="2"> 
      <?
	  $req = MYSQL_QUERY("SELECT * FROM dossiers ORDER BY id DESC");
	  $nbre =mysql_num_rows($req);
	  ?>
    </td>
    <td width="200" colspan="2">&nbsp;</td>
  </tr>
  <?
  while($disp = mysql_fetch_array($req))
  {
  ?>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="baseline"> 
    <td width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[titre]; ?></font></td>
    <td width="200"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=ger_dossier&id=<? echo $disp[id]; ?>">Editer</a></font></div></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td height="2" colspan="3">&nbsp;</td>
  </tr>
</table>
  <div align="center"> <br>
</div>
</body>
</html>
