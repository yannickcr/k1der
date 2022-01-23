<?
include "secu.php";
if (!$_GET["statut"]) {
?>
<FORM NAME='subscribe' method='POST' action='http://admin.k1der.net/mail/creation_auto.php'>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="439" height="42" valign="baseline">
      <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr>
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Créer un mail=-</font></b></font></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="50%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <input type='hidden' name='UrlOk' value='http://www.k1der.net/index.php?page=create_mail&statut=ok'>
          <input type='hidden' name='UrlNotOk' value='http://www.k1der.net/index.php?page=create_mail&statut=pasok'>
Nom :</font></td>
        <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <input type='text' name='Name' value=''>
        </font></td>
      </tr>
      <tr>
        <td width="50%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Prénom :</font></td>
        <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <input type='text' name='Surname'>
        </font></td>
      </tr>
      <tr>
        <td width="50%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Email :</font></td>
        <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <input type='text' name='Email' value=''>
@k1der.net</font></td>
      </tr>
      <tr>
        <td width="50%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mot de passe : </font></td>
        <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <input type='password' name='Password'>
        </font></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
        </tr>
      <tr>
        <td colspan="3"><div align="center">
          <input type='submit' name='btnsubmit' value='Créer le compte e-mail...'>
        </div></td>
        </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<?
} else if ($_GET["statut"] == "ok") {
?>
<center><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>La création du mail à r&eacute;ussie.</strong><br>
Webmail : <a href="http://webmail.infomaniak.ch" target="_blank">http://webmail.infomaniak.ch</a><br> 
Server pop : mail.k1der.net<br>
Server smtp : mail.k1der.net<br>
</font></center>
<?
}
else if ($_GET["statut"] == "pasok") {
?>
<center><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Une erreur est survenue lors de la création du mail. </strong></font></center>
<?
}
?>
