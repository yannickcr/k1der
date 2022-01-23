<?
include "secu.php";

$req = MYSQL_QUERY("SELECT * FROM server ORDER BY descr");
?> 
<form name="form1" method="post" action="index.php?page=ger_server_ftp">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="568" height="22" valign="baseline"> <div align="right"></div></td>
      <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
    </tr>
    <tr> 
      <td width="568" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Configuration 
        du server</b>=-</font></b></font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="300"> <div align="right"> 
          <select name="thefichier" id="thefichier">
            <?
		  while($disp = mysql_fetch_array($req))
		  {
		  ?>
            <option value="<? echo $disp[url]; ?>"><? echo $disp[descr]; ?></option>
            <?
		  }
		  ?>
          </select>
          <br>
        </div></td>
      <td width="300"> &nbsp;&nbsp;&nbsp; <input type="submit" name="Submit" value="Modifier"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><a href="Javascript:ChangeMessage('fichiers_serv')"><font size="2" face="Arial, Helvetica, sans-serif" color="#000000"><strong><em><u>Gestion 
        des fichiers &eacute;ditables</u></em></strong></font></a>
        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="fichiers_serv" style='display:none;'>
          <tr> 
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=ajout_membre"></a><a href="index.php?page=ger_server_aj_fichier">Ajouter 
              un fichier</a></font></td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=ger_server_list_fichier&action=modif">Modifier 
              un fichier</a></font></td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=ger_server_list_fichier&action=suppr">Surpprimer 
              un fichier</a></font></td>
            <td width="300">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>

<div align="center"><form>
  <input name="button" type="button" onClick="Javascript:window.location='index.php?page=admin';" value="Retour à l'administration">
</form>
</div>
