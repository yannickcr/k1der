<? include "secu.php"; ?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="Javascript">

function Supprimer(data,data2)
{
 resultat = confirm('Voulez-vous vraiment supprimer la page ?');
 if(resultat==1)
 {
  window.location='admin/suppr_page.php?id='+data+'&idp='+data2;
 }
 else
 {
  alert('Suppression annulée !');
 }
}

function Supprimerd(data)
{
 resultat = confirm('Voulez-vous vraiment supprimer le dossier en entier ?');
 if(resultat==1)
 {
  window.location='admin/suppr_dossier.php?id='+data;
 }
 else
 {
  alert('Suppression annulée !');
 }
}

</script>
<body>
<?
if ($aj_page == 1)
{
$req = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' ORDER BY page DESC LIMIT 0,1");
$disp = mysql_fetch_array($req);
$num = $disp[page]+1;
mysql_query("INSERT INTO dossiers_p VALUES('','Sans Titre','','$num','$id')");
}
$date = date("d/m/Y");
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
                le dossier=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      
    <td width="300" height="2">
      <?
	  $req = MYSQL_QUERY("SELECT * FROM dossiers WHERE id='$id'");
	  $nbre =mysql_num_rows($req);
	  $disp = mysql_fetch_array($req);
	  ?>
    </td>
      <td width="200" colspan="2">&nbsp;</td>
    </tr>
    <tr valign="baseline"> 
      
    <td width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $disp[titre]; ?><br>
      </strong></font><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=modif2_dossier&id=<? echo $id; ?>">Modifier 
      (titre, r&eacute;sum&eacute;,...)</a></font></td>
      <td width="200"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="Javascript:Supprimerd('<? echo $id; ?>');">Supprimer 
        le dossier</a></font></div></td>
    </tr>
    <tr> 
      <td height="2" colspan="3"><table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
          <?
		  $req = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' ORDER BY page");
		  $nbre =mysql_num_rows($req);
		  while($disp = mysql_fetch_array($req))
		  {
		  ?>
		  <tr> 
            <td width="300" height="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Page <? echo $disp[page]; ?> : <? echo $disp[titrepage]; ?></font></td>
            <td width="100" height="2"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=modif_dossier&id=<? echo $disp[id_dossier]; ?>&idp=<? echo $disp[page]; ?>">Modifier</a></font></div></td>
            <td width="100"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="Javascript:Supprimer('<? echo $id; ?>','<? echo $disp[page]; ?>');">Supprimer</a></font></div></td>
          </tr>
		  <?
		  }
		  ?>
        </table></td>
    </tr>
    <tr> 
      
    <td height="2" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=ger_dossier&id=<? echo $id; ?>&aj_page=1">Ajouter 
      une page</a></font></td>
    </tr>
    <tr> 
      <td height="2" colspan="3">&nbsp;</td>
    </tr>
  </table>
  <div align="center"> <br>
  </div>
</body>
</html>
