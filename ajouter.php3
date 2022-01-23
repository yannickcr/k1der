<html>
<head>
  <META http-equiv="Content-Language" content="fr">
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Ajouter un commentaire</title>
</head>

<BODY TOPMARGIN="5" LEFTMARGIN="0" BGCOLOR="#FFFFFF" TEXT="#000000" MARGINHEIGHT="5" MARGINWIDTH="0" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<?
require("config.inc.php3");
?>
<form action="ajouter2.php3" method="POST">
<input type="hidden" name="id_news" value="<? echo $id_news; ?>">
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
                  <td width="100%" bgcolor="#EBE7E7"><font class="m8" color="#000000"></font> 
                    <div align="center"> 
                      <table border="0" cellpadding="4" cellspacing="0" width="100%">
                        <tr> 
                          <td height="15" colspan="3"></td>
                        </tr>
                        <tr> 
                          <td height="30" align="right"><font style="<? echo $CorpsNews; ?>">Pseudo 
                            :</font></td>
                          <td height="30" align="center">
                            <input type="text" name="pseudo" style="width:250px;" maxlength="35">
                          </td>
                          <td height="30" align="center">
                            <input type="image" border="0" src="images/ajouter.gif" width="60" height="19" alt="Cliquer pour ajouter le commentaire" name="image">
                          </td>
                        </tr>
                        <tr> 
                          <td height="15" align="center" colspan="3">
                            <textarea wrap="virtual" style="width:400px; height:220px;" rows="1" cols="20" name="commentaire"></textarea>
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