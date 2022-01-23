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
<form name="the_form" method="post" action="admin/aj_sondage2.php" onSubmit="return isOK()">
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="2" colspan="2"> <div align="left"> 
          <table width="500" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="31" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
                un Sondage=-</font></b></font></td>
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
      <td height="2" width="264"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Titre 
        : </font></td>
      <td height="2" width="111"><input name="titre" type="text" id="titre" size="30"></td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
      <td height="20">&nbsp;</td>
    </tr>
    <tr> 
      <td height="20" width="264"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        1 :</font></td>
      <td height="20" width="111"><input name="r1" type="text" id="r1" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        2:</font></td>
      <td height="20"><input name="r2" type="text" id="r2" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        3:</font></td>
      <td height="20"><input name="r3" type="text" id="r3" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        4:</font></td>
      <td height="20"><input name="r4" type="text" id="r4" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        5:</font></td>
      <td height="20"><input name="r5" type="text" id="r5" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        6:</font></td>
      <td height="20"><input name="r6" type="text" id="r6" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        7:</font></td>
      <td height="20"><input name="r7" type="text" id="r7" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        8 :</font></td>
      <td height="20"><input name="r8" type="text" id="r8" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        9 :</font></td>
      <td height="20"><input name="r9" type="text" id="r9" size="30"></td>
    </tr>
    <tr> 
      <td height="20"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;ponse 
        10 :</font></td>
      <td height="20"><input name="r10" type="text" id="r10" size="30"></td>
    </tr>
    <tr> 
      <td height="2">&nbsp;</td>
      <td height="2">&nbsp;</td>
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
