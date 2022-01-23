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

    if( nom.value == "" ){
      alert("Vous n'avez pas rentré de nom");
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
<form name="the_form" method="post" action="admin/aj_cat2.php" onSubmit="return isOK()">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="2" colspan="3"><div align="center"> 
          <table width="600" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
            </tr>
            <tr> 
              <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Ajouter 
                une cat&eacute;gorie</b>=-</font></b></font></td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td height="2" colspan="3"> <div align="CENTER">&nbsp; </div></td>
    </tr>
    <tr> 
      <td height="2" colspan="2"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
          :</font></div></td>
      <td height="2"><input type="text" name="nom" size="30"></td>
    </tr>
    <tr> 
      <td height="2" colspan="2"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type 
          :</font></div></td>
      <td height="2"><select name="type" id="type">
          <option value="k1der">K1der</option>
          <option value="war3">Warcraft III</option>
          <option value="cs">Counter-Strike</option>
        </select></td>
    </tr>
    <tr> 
      <td height="2" colspan="2"><div align="right"></div></td>
      <td width="65%" height="2">&nbsp; </td>
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
