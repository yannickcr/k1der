<html>
<head>
  <META http-equiv="Content-Language" content="fr">
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Ajouter un commentaire</title>
</head>
<script language="JavaScript">
<!--

function part(){

  with(document.comment){

    if( commentaire.value == '' ){
      alert("Vous n'avez pas rentré de commentaire");
      return false;
    }
    if( pseudo.value == '' ){
      alert("Vous n'avez pas rentré de pseudo");
      return false;
    }
	if( note.value == 'rien' ){
      alert("Vous n'avez pas rentré de note");
      return false;
    }
  
  }

  return true;
}

//-->
</script>
<BODY TOPMARGIN="5" LEFTMARGIN="0" BGCOLOR="#FFFFFF" TEXT="#000000" MARGINHEIGHT="5" MARGINWIDTH="0" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<?
require("config.inc.php3");
?>
<form action="ajouter_comm_dossier2.php" method="POST" name="comment" id="comment" onSubmit="return part()">
  <input name="id_dossier" type="hidden" id="id_dossier" value="<? echo $id; ?>">
  <div align="center">
  <center>
      <table border="0" cellspacing="0" bgcolor="#000000" width="95%">
        <tr> 
          <td> 
            <div align="center"> 
              <table border="0" cellspacing="0" cellpadding="2" width="100%">
                <tr> 
                  <td width="100%" bgcolor="<? echo $bgcolor_haut; ?>" align="center" height="23" background="../images/fond.gif"><font style="<? echo $TitreNews; ?>">Ajouter 
                    un commentaire</font></td>
                </tr>
                <tr> 
                  <td width="100%" bgcolor="#EBE7E7"><font class="m8" color="#000000">&nbsp;</font> 
                    <div align="center"> 
                      <table border="0" cellpadding="4" cellspacing="0" width="100%">
                        <tr> 
                          <td colspan="2" align="right"><div align="center"><font style="<? echo $CorpsNews; ?>">Pseudo 
                              :</font> 
                              <input type="text" name="pseudo" style="width:250px;" maxlength="35">
                            </div></td>
                        </tr>
                        <tr> 
                          <td width="41%" align="center"> 
                            <div align="right"><font style="<? echo $CorpsNews; ?>">Note 
                              donn&eacute;e &agrave; ce dossier :</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                              </font></div></td>
                          <td align="center">
<div align="left" name="select"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                              <select name="note" id="note">
                                <option value="rien"></option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                              </select>
                              /6</font></div></td>
                        </tr>
                        <tr> 
                          <td height="15" colspan="2" align="center"> <textarea wrap="virtual" style="width:400px; height:220px;" rows="1" cols="20" name="commentaire"></textarea> 
                            <br> <input type="image" border="0" src="images/ajouter.gif" width="60" height="19" alt="Cliquer pour ajouter le commentaire" name="image"> 
                          </td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>
    </center>

<a href="Javascript:window.close();"><font style="<? echo $Comment2; ?>"><b>[fermer la fenêtre]</b></font></a>

</div>
</form>
</body>
</html>