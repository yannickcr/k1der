<?
include("config.inc.php3");
if($Submit)
{
	if(!($db = mysql_connect($dbhost,$dblogi,$dbpass))) { 	
		echo "Erreur lors de la connexion";
		exit;
	}
	
	//On choisit sa base
	if(!mysql_select_db($dbbase)) {
		echo "Erreur lors de la sélection de la base";
		exit;
	}
$query = mysql_query("SELECT kinder, pass FROM equipe WHERE e_mail='$email'"); 
list($kinder, $pass) = mysql_fetch_row($query);
$nb=mysql_num_rows($query);
if($nb<1)
{ echo"<script language=\"Javascript\">alert('Aucun membre ne correspond à votre e-mail !');window.location='index.php';</script>";
exit;}
$date = date("d/m/Y"); 
$heure = date("H:i"); 
$sujet = 'Vos login et Mot de passe'; 
$message = "Le $date à $heure, vous avez demmandé vos login et mot de passe. Les voici :\n\nLogin : $kinder \nMot de passe : $pass \n---------------------------------------\nMerci de vos visites sur -=K1der=- !\nhttp://www.k1der.net\n"; 
//mail ($a,$sujet,$texte,"From:$de"); 
mail ($email,$sujet,$message,"From:$mailmaster");
echo"<script language=\"Javascript\">alert('Vos login et mot de passe ont été envoyés par mail !'); alert('Merci d\'avoir fait confiance à -=K1der=- Telecom'); window.location='index.php?page=admin&mail=ok';</script>";
exit;
}
?>
<form method="post" action="">
  <div align="center">
    <table width="465" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
        <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
      </tr>
      <tr> 
        <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Perte 
          du Mot de Passe=-</font></b></font></td>
      </tr>
      <tr> 
        <td colspan="2">&nbsp;</td>
      </tr>
    </table>
    <p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><br>
      T'as perdu ton mot de passe ?<br>
      C'est con sa !</font><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><br>
      Si tu est tr&egrave;s gentil et que tu nous indique ton e-mail<br>
      on pourra peut-&ecirc;tre faire quelque chose.<br>
      <br>
      (Ce service te sera factur&eacute; 186 &euro; par <strong>-=K1der=- Telecom</strong>)</font></p>
    </div>
  <table cellspacing=1 cellpadding=2 width="500" border=0 align="center">
    <tr> 
      <td> 
        <div align=center><b><font face="Arial, Helvetica, sans-serif" size="2">E-Mail</font></b></div>
      </td>
      <td> 
        <div align=center> 
          <input name=email>
        </div>
      </td>
    </tr>
    <tr> 
      <td colspan=2> 
        <div align=center><br>
          <input type="submit" name="Submit" value="Retrouver mon mot de passe">
        </div>
      </td>
    </tr>
  </table>
  </form>