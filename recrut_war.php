<?
if($Envoyer)
{
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT kinder, e_mail FROM equipe";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
while($disp = mysql_fetch_array($req))
{
$date = date("d/m/Y"); 
$heure = date("H:i"); 
$sujet = 'Un Mec veut devenir -=K1der=-'; 
$message = "Le $date à $heure\n\nSalut $disp[kinder],\nUn mec veut devenir -=K1der=-.\n\n Voici ses paramêtres:\n\nPseudo : $pseudo \nNom : $nom2 \nPrénom : $prenom \nAge : $age \n \nICQ/AIM/MSN : $icq \nE-mail : $mail \n\nVille : $ville \nConnection : $conn \nExpérience : $exp \nDisponibilité : $dispo \nLevel : $style \nCompte BattleNet : $battle \nCommentaires : $comm\n\n---------------------------------------\nMerci de vos visites sur -=K1der=- !\nhttp://www.k1der.net\n"; 
//mail ($a,$sujet,$texte,"From:$de"); 
mail ($disp[e_mail],$sujet,$message,"From:$mailmaster");
}
?>
<script language="Javascript">
alert('Votre demande à été envoyée aux membres du clan !');
window.location='index.php';
</script>
<?
}
else
{
?>
<html>
<head>
<title>-=K1der=- The Chocolat Effect</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="JavaScript">
<!--

function Noprob(){

  with(document.recr){

    if( pseudo.value == '' ){
      alert("Met ton pseudo, c bien les pseudos");
      return false;
    }
    if( nom2.value == '' ){
      alert("On aimerai bien ton nom si c pas trop demander");
      return false;
    }
    if( prenom.value == '' ){
      alert("nom ET prenom plize");
      return false;
    }
    if( age.value == '' ){
      alert("Et c'est quoi ton age ? (histoire de savoir si t'es un petit merdeux ou un grand con)");
      return false;
    }
    if( mail.value == '' ){
      alert("bin en fait si tu veut qu'on te réponde c'est mieu de mettre ton e-mail");
      return false;
    }
    if( ville.value == '' ){
      alert("Il manque l'endroit où se trouve ta maison");
      return false;
    }
    if( style.value == '' ){
      alert("T'as oublié de rentrer ton Level");
      return false;
    }
    if( battle.value == '' ){
      alert("On aimerai bien avoir ton compte BattleNet");
      return false;
    }
	adresse = document.recr.mail.value;
	var place = adresse.indexOf("@",1);
	var point = adresse.indexOf(".",place+1);
	if ((place > -1)&&(adresse.length >2)&&(point > 1))
		{
		//document.recr.submit();
		}
	else
		{
		alert('C\'est bien les e-mails avec des @ au milieu et des extensions à la fin...');
		return false;
		}

  }

  return true;
}

//-->
</script>
<body bgcolor="#FFFFFF" text="#000000" link="#CC0000" vlink="#CC0000" alink="#CC0000">
<div align="center"> 
  <table width="465" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="452" height="22" valign="baseline"> <div align="right"></div></td>
      <td width="32" height="42" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
    </tr>
    <tr> 
      <td width="452" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b>On 
        recherche</b></font><font color="#FFFFFF">=-</font></b></font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2" height="92"> <p align="center"><font face="Arial, Helvetica, sans-serif"><strong><font color="#FF0000" size="2">1</font><font size="2"> 
          joueur pour l'&eacute;quipe Warcraft III pour les LAN Party et sur le 
          Net</font></strong><font size="2"><br>
          <font face="Verdana, Arial, Helvetica, sans-serif"><br>
          On vous demande juste de :</font></font></font></p>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
          <tr> 
            <td width="72%" nowrap>&nbsp;</td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              - Pouvoir se d&eacute;placer pour les LAN Party (~Principalement 
              en Bretagne)</font></td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Pouvoir nous supporter.</font></td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Avoir un Pc.</font></td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Savoir faire marcher un ordinateur.</font></td>
          </tr>
          <tr> 
            <td width="72%" height="18" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Savoir Formater.</font></td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Savoir r&eacute;parer un ordinateur.</font></td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Avoir des blagues a la con ou des phrases de merdes</font></td>
          </tr>
          <tr> 
            <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              Accepter de porter un nom &agrave; la con (D&eacute;lice, Tranche 
              au Lait...)</font></td>
          </tr>
          <tr> 
            <td width="72%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">- 
              ........</font></td>
          </tr>
        </table>
        
      </td>
    </tr>
  </table>
  <br>
  <font face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Si 
  vous correspondez a peu pr&egrave;s &agrave; sa<br>
  et que vous &ecirc;tes motiv&eacute; alors remplissez ce formulaire:</font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
  </font><br>
  <form name="recr" method="post" action="" onSubmit="return Noprob()">
    <table width="500" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pseudo:</font></td>
        <td><input name="pseudo" type="text" id="pseudo"></td>
      </tr>
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom: 
          </font></td>
        <td width="242"><input name="nom2" type="text" id="nom2"></td>
      </tr>
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pr&eacute;nom:</font></td>
        <td><input name="prenom" type="text" id="prenom"></td>
      </tr>
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Age:</font></td>
        <td><input name="age" type="text" id="age2" size="5" maxlength="5"></td>
      </tr>
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">ICQ 
          / AIM / MSN: </font></td>
        <td><input name="icq" type="text" id="icq"></td>
      </tr>
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail 
          : </font></td>
        <td><input name="mail" type="text" id="mail"></td>
      </tr>
      <tr> 
        <td width="150"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ville:</font></td>
        <td><input name="ville" type="text" id="ville"></td>
      </tr>
      <tr> 
        <td width="150" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Connection:</font></td>
        <td><select name="conn" id="conn">
            <option value="56k ou moins" selected>56k ou moins</option>
            <option value="Num&eacute;ris">Num&eacute;ris</option>
            <option value="ADSL">ADSL</option>
            <option value="T1 ou plus">T1 ou plus</option>
          </select></td>
      </tr>
      <tr> 
        <td width="150" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Exp&eacute;rience:</font></td>
        <td><SELECT name=exp>
            <option value="- d'un mois" selected>- d'un mois</option>
            <option value="- de 6 mois">- de 6 mois</option>
            <option value="- d'un ans">- d'un ans</option>
            <option value="+ d'un ans">+ d'un ans</option>
            <option value="+ de deux ans">+ de deux ans</option>
          </SELECT></td>
      </tr>
      <tr> 
        <td width="150" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Disponibilit&eacute;:</font></td>
        <td><select name="dispo" id="dispo">
            <option value="Week-Ends" selected>Week-Ends</option>
            <option value="Vacances">Vacances</option>
            <option value="Les 2">Les 2</option>
            <option value="Tout le temps">Tout le temps</option>
          </select></td>
      </tr>
      <tr> 
        <td width="150" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Level:</font></td>
        <td><input name="style" type="text" id="style" size="3" maxlength="3"></td>
      </tr>
      <tr>
        <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
          du Compte BattleNet:</font></td>
        <td><input name="battle" type="text" id="battle"></td>
      </tr>
      <tr> 
        <td width="150" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Commentaires 
          :</font></td>
        <td><textarea name="comm" cols="50" rows="4" id="comm"></textarea></td>
      </tr>
      <tr> 
        <td colspan="2"><p align="center"> 
            <INPUT name="Envoyer" TYPE="submit" VALUE="Envoyer">
            <br>
          </p></td>
      </tr>
    </table>
  </form>
</div>
</body>
</html>
<?
}
?>