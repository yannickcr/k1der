<?
include "secu.php"; ?><html>
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
<form name="the_form" method="post" action="admin/ger_server_aj_fichier2.php" onSubmit="return isOK()">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="2" colspan="2"> <div align="left"> 
          <table width="600" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td width="568" height="22" valign="baseline"> 
                <div align="right"></div></td>
              <td width="32" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="568" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
                un fichier=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td height="2" colspan="2"> <div align="CENTER">&nbsp; </div></td>
    </tr>
    <tr> 
      <td height="2" width="264"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
        :</font></td>
      <td height="2" width="111"><input name="descr" type="text" id="descr" size="30"></td>
    </tr>
    <tr> 
      <td height="2" width="264"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url 
        : <font size="1">(ex: addons/amx/admin.cfg )</font></font></td>
      <td height="2" width="111"><input name="furl" type="text" id="furl" size="30"></td>
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
