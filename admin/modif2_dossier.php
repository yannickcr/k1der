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
  alert('Suppression annul�e !');
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
  alert('Suppression annul�e !');
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
    <td height="2" colspan="2"> <div align="left"> 
        <table width="500" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="100%" height="22" valign="baseline"> <div align="right"></div></td>
            <td height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
          </tr>
          <tr> 
            <td width="100%" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Modifier 
              le dossier=-</font></b></font></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </div></td>
  </tr>
  <tr> 
    <td height="2" colspan="2"> 
      <?
	  $req = MYSQL_QUERY("SELECT * FROM dossiers WHERE id='$id'");
	  $nbre =mysql_num_rows($req);
	  $disp = mysql_fetch_array($req);
	  ?>
    </td>
  </tr>
  <tr valign="baseline"> 
    <td> <div align="center"> 
        <form name="form1" method="post" action="admin/modif2_dossier2.php">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Titre 
                :</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="titre" type="text" id="titre" value="<? echo $disp[titre]; ?>" size="30">
                </font></td>
            </tr>
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                :</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[date]; ?> 
                <font size="1"> 
                <? if ($disp[conf] == 0) { echo "(la date sera mise &agrave; jour quand le dossier 
                sera publi&eacute;)"; } ?>
                </font></font></td>
            </tr>
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Auteur 
                :</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[auteur]; ?> 
                <input name="old_conf" type="hidden" id="old_conf" value="<? echo $disp[conf]; ?>">
                <input name="id" type="hidden" id="id" value="<? echo $disp[id]; ?>">
                <input name="date" type="hidden" id="date" value="<? echo $disp[date]; ?>">
                </font></td>
            </tr>
            <tr> 
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;sum&eacute; 
                :</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <textarea name="resume" cols="50" rows="4" id="resume"><? echo $disp[resume]; ?></textarea>
                </font></td>
            </tr>
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Image 
                :</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="image" type="text" id="image" value="<? echo $disp[image]; ?>" size="50">
                </font></td>
            </tr>
            <tr> 
              <td nowrap>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr> 
              <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Publier 
                le dossier :</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input type="radio" name="conf" value="1" <? if ($disp[conf] == 1) { echo "checked"; } ?>>
                Oui 
                <input type="radio" name="conf" value="0" <? if ($disp[conf] == 0) { echo "checked"; } ?>>
                Non </font></td>
            </tr>
            <tr>
              <td colspan="2" nowrap>&nbsp;</td>
            </tr>
            <tr> 
              <td colspan="2" nowrap><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <input type="submit" name="Submit" value="Valider">
                  </font></div></td>
            </tr>
          </table>
          </form>
      </div></td>
  </tr>
  <tr> 
    <td height="2" colspan="2">&nbsp;</td>
  </tr>
</table>
  <div align="center"> <br>
  </div>
</body>
</html>
      