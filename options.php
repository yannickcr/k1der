<?
if($id=="3") {
session_start();
$old_user = $logger;
$resultat = session_unregister("login");
Setcookie("pymembs");
Header("Location: index.php?page=admin");
exit;
}
session_start();
include("verif_session.php3");
include("config.php3");
include("connexiondb.php3");
if($confirm)
{
$query = mysql_query("DELETE FROM $db_table WHERE login='$login'");
session_destroy('login'); 
echo"<script language=\"Javascript\">alert('Votre compte a été effacé avec succès...');window.location='index.php3';</script>";
}
if($Submit)
{
if(empty($loginn) OR empty($passwd) OR empty($email)) 
{
echo"<script language=\"Javascript\">alert('Il y a un champ vide... Tous les champs doivent être remplis !');history.back();</script>";
exit;
}
$query = mysql_query("UPDATE $db_table SET login='$loginn', passwd='$passwd', email='$email' WHERE login='$login'"); 
session_destroy();
session_start();
$login=$loginn;
session_register("login");
echo"<script language=\"Javascript\">alert('Vos informations ont été mises à jour !');window.location='index.php3';</script>";
exit;
}
include("avant.php3");
if($id=="1")
// Modificatinos
{
$query = mysql_query("SELECT email, passwd FROM $db_table WHERE login='$login'"); 
list($email, $passwd) = mysql_fetch_row($query);
?> 
<form method="post" action="">
  <div align="center"><b><i><font color="#990033"><u>Modifiaction des informations 
    </u> </font></i></b> 
    <table cellspacing=1 cellpadding=2 width="60%" border=0>
      <tr> 
        <td height="33"> 
          <div align=center><b><font 
                        face="Arial, Helvetica, sans-serif" size=2>Identifiant</font></b></div>
        </td>
        <td height="33"> 
          <div align=center> 
            <input name=loginn maxlength="15" value="<? echo $login ?>">
          </div>
        </td>
      </tr>
      <tr> 
        <td> 
          <div align=center><b><font 
                        face="Arial, Helvetica, sans-serif" size=2>Password</font></b></div>
        </td>
        <td> 
          <div align=center> 
            <input type=text name=passwd maxlength="15" value="<? echo $passwd ?>">
          </div>
        </td>
      </tr>
      <tr> 
        <td> 
          <div align=center><b><font face="Arial, Helvetica, sans-serif" size="2">E-Mail</font></b></div>
        </td>
        <td> 
          <div align=center> 
            <input name=email value="<? echo $email ?>">
          </div>
        </td>
      </tr>
      <tr> 
        <td colspan=2> 
          <div align=center> 
            <input type="submit" name="Submit" value="Modifier les infos !">
          </div>
        </td>
      </tr>
    </table>
  </div>
</form>
<?
}
elseif($id=="2")
{
// Supression du compte 
echo"<SCRIPT>confirmation = confirm('Etes vous sur de vouloir supprimer votre compte ? ');	if(confirmation)	{ window.location='?confirm=1';	} else {  window.location='index.php3'; }</SCRIPT>";}
include("apres.php3"); ?>