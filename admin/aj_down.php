<?
include "secu.php";
?><html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
<!--

function isOK(){

  with(document.the_form){

    if( lien_lien.value == "" ){
      alert("Vous n'avez pas rentré de lien");
      return false;
    }
  
  }

  return true;
}

//-->
</script></head>

<body>
<?
$date = date("d/m/Y");
?>
<form name="the_form" method="post" action="admin/aj_down2.php" onSubmit="return isOK()">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="2" colspan="3"><div align="center"> 
          <table width="590" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
            </tr>
            <tr> 
              <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Ajouter 
                un Fichier</b>=-</font></b></font></td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td height="2" colspan="3"> <div align="CENTER">&nbsp; </div></td>
    </tr>
    <tr> 
      <td width="200" height="2" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
        : </font></td>
      <td height="2" width="200"> <input type="text" name="nom" size="30"></td>
    </tr>
    <tr> 
      <td height="2" colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
        :</font><br> </td>
      <td height="2"><textarea name="descr" cols="30" rows="5"></textarea></td>
    </tr>
    <tr>
      <td height="20" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Image 
        (Optionnel) :</font></td>
      <td height="20"><input name="img" type="text" id="img" size="30"></td>
    </tr>
    <tr> 
      <td width="200" height="20" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lien 
        Complet (Http://...)</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        : </font></td>
      <td height="20" width="200"> <input type="text" name="lien" size="30"> </td>
    </tr>
    <tr> 
      <td height="0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cat&eacute;gorie</font></td>
      <td height="20"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Existante 
          : 
          <input name="type_cat" type="radio" value="ex_cat" checked>
          </font><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <br>
          </font></div></td>
      <td height="20" rowspan="2"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="cat" id="cat">
          <?
	  include 'config.php';
	  $db = mysql_connect($cfg_hote, $cfg_user, $cfg_password);
	  mysql_select_db($cfg_base,$db);
	  $requete  = "SELECT * FROM cats_down ORDER BY type,nom";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $nbre =mysql_num_rows($req);
	  while($disp = mysql_fetch_array($req))
	  {
	  ?>
          <option value="<? echo $disp[id]; ?>"><? echo "$disp[nom] ($disp[type])"; ?></option>
          <?
	  }
	  ?>
        </select>
        <br>
        <input name="new_cat" type="text" id="new_cat">
        Type : 
        <select name="type" id="type">
          <option value="k1der">K1der</option>
          <option value="war3">Warcraft III</option>
          <option value="cs">Counter-Strike</option>
        </select>
        </font></td>
    </tr>
    <tr> 
      <td height="20" valign="top">&nbsp;</td>
      <td height="20"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nouvelle 
          :</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"></font><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input type="radio" name="type_cat" value="new_cat">
          </font></div></td>
    </tr>
    <!-- Boutons -->
    <!-- Boutons -->
  </table>
  <div align="center"> <br>
    <input type="submit" name="Submit" value="Ajouter">
  </div>
</form>
</body>
</html>
