<?
include "secu.php";
include "config.inc.php3";

// Création de la connexion
/*$conn_id = ftp_connect("$ftp_server");

// Authentification avec nom de compte et mot de passe
$login_result = ftp_login($conn_id, "$ftp_user_name", "$ftp_user_pass");

// Vérification de la connexion
if ((!$conn_id) || (!$login_result)) {
        echo "La connexion FTP a échoué!";
        echo "Tentative de connexion à $ftp_server avec $ftp_user_name";
        die;
    } else {
        echo "Connecté à $ftp_server, avec $ftp_user_name";
    }*/

$dataf = file("ftp://".$ftp_user_name.":".$ftp_user_pass."@".$ftp_server."/".$thefichier);

//ftp_get ($conn_id,"/home/users/k1der/www/admin/ftp_temp/tmp.txt","$thefichier",FTP_ASCII);
//$dataf=file("/home/users/k1der/www/admin/ftp_temp/tmp.txt");

// Fermeture de la connexion FTP.
//ftp_quit($conn_id);

?>
<form name="form1" method="post" action="admin/ftp2.php">
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
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fichier: 
        <b><? echo $thefichier; ?></b></font></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><div align="center"> 
          <input name="thefichier" type="hidden" value="<? echo $thefichier; ?>">
          <textarea name="text" rows="30" wrap="OFF" id="text" style="width:600px"><?
foreach ( $dataf as $contenu ) //on parcours le tableau 
{
$thecontinue .= $contenu;//on affiche le contenu
}

//$thecontinue = str_replace("
//","\n",$thecontinue);

echo $thecontinue;
?></textarea>
          <br/>
          <br/>
          <input type="submit" name="Submit" value="Valider">
        </div></td>
    </tr>
  </table>
</form>