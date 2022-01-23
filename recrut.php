<?
if($Envoyer)
{
$date = date("U");
$exp = addslashes($exp);
$lettre = addslashes($lettre);
mysql_query("INSERT INTO recrutement VALUES('','$pseudo','$nom','$prenom','$sexe','$mens','$age','$icq','$mail','$ville','$conn','$exp','$dispo','$section','$stylecs','$levelw3','$urlw3','$lettre','$REMOTE_ADDR','$date','0')") or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
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
    if( nom.value == '' ){
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
    if(( mens.value == '') && (document.getElementById('mens').style.display == 'block')){
      alert("Aller...soit pas timide et rentre tes mensurations :)");
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
<script language="JavaScript" type="text/JavaScript">
function checkCheckBox(form)
{
	document.getElementById('mens').style.display='block';
}

function checkCheckBox2(form)
{
	document.getElementById('mens').style.display='none';
}

function checkCheckBox3(form)
{
	document.getElementById('cs').style.display='block';
	document.getElementById('war3').style.display='none';
}

function checkCheckBox4(form)
{
	document.getElementById('cs').style.display='none';
	document.getElementById('war3').style.display='block';
}

</script>
<body bgcolor="#FFFFFF" text="#000000" link="#CC0000" vlink="#CC0000" alink="#CC0000">
<div align="center"> 
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="452" height="22" valign="baseline"> <div align="right"></div></td>
      <td width="32" height="42" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
    </tr>
    <tr> 
      <td width="452" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Recrutement</b>=-</font></b></font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td height="92" colspan="2"><p><font face="Arial, Helvetica, sans-serif" size="2">
	  <?
	  $requete  = "SELECT * FROM config WHERE nom='nb_cs'";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $disp = mysql_fetch_array($req);
	  $entou = $disp[valeur];
	  $requete  = "SELECT * FROM config WHERE nom='nb_war3'";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $disp = mysql_fetch_array($req);
	  $entou = $disp[valeur] + $entou;
	  if ($entou != 0)
	  {
	$requete  = "SELECT * FROM config WHERE nom='charte_gen'";
	$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	$disp = mysql_fetch_array($req);

	  ?><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Pour devenir K1der il faut tout d'abord que vous correspondiez &agrave; 
          sa:</b><br>
          <br>
          <?
		  $valeur = str_replace("
","<br>",$disp[valeur]);
echo $valeur; ?>
          <br>
          <br>
		  <?
		  $requete  = "SELECT * FROM config WHERE nom='nb_cs'";
		  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		  $disp = mysql_fetch_array($req);
		  if ($disp[valeur] != 0)
		  {
		  ?>
          <b>Nous recherchons <font color="#CC0000">
		  <?
		  echo $disp[valeur];
		  ?></font> joueur<? if ($disp[valeur] > 1) { echo "s"; } ?> de Counter-Strike :</b><br><br>
		  <?
		  $requete  = "SELECT * FROM config WHERE nom='charte_cs'";
		  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		  $disp = mysql_fetch_array($req);
		  $valeur = str_replace("
","<br>",$disp[valeur]);
echo $valeur; } else { ?>
          <b>Nous ne recherchons pas de joueurs pour Counter-Strike pour le moment.</b>	<? } ?>
          <br><br>
		  <?
		  $requete  = "SELECT * FROM config WHERE nom='nb_war3'";
		  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		  $disp = mysql_fetch_array($req);
		  if ($disp[valeur] != 0)
		  {
		  ?>
          <b>Nous recherchons <font color="#CC0000">
		  <?
		  echo $disp[valeur];
		  ?></font> joueur<? if ($disp[valeur] > 1) { echo "s"; } ?> de Warcraft III :</b><br><br>
		  <?
		  $requete  = "SELECT * FROM config WHERE nom='charte_war3'";
		  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		  $disp = mysql_fetch_array($req);
		  $valeur = str_replace("
","<br>",$disp[valeur]);
echo $valeur; } else { ?>
          <b>Nous ne recherchons pas de joueurs pour Warcraft III pour le moment.</b>	<? } ?>	  
		  <br></font>
		  <?
		  }
		  else
		  {
		  echo "<center><b>Nous ne recrutons plus pour l'instant, désolé</b></center>";
		  }
		  ?></font></p></td>
    </tr>
  </table>
  <?
  if ($entou != 0)
  {
  ?><br>
  <font face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Si 
  vous correspondez &agrave; sa<br>
  et que vous &ecirc;tes motiv&eacute; alors remplissez ce formulaire:</font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
  </font><br>
  <form name="recr" method="post" action="" onSubmit="return Noprob()">
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pseudo:</font></td>
        <td width="420"> 
          <input name="pseudo" type="text" id="pseudo"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom: 
          </font></td>
        <td width="420"> 
          <input name="nom" type="text" id="nom"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pr&eacute;nom:</font></td>
        <td width="420"> 
          <input name="prenom" type="text" id="prenom"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Sexe:</font></td>
        <td width="420"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input type="radio" onClick="return checkCheckBox2(this.form)" name="sexe" value="m" id="sexe" checked>
          M 
          <input type="radio" onClick="return checkCheckBox(this.form)" name="sexe" value="f" id="sexe">
          F </font></td>
      </tr>
      <tr id="mens" style='display:none'> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mensurations 
          :)</font></td>
        <td width="420"> 
          <input name="mens" type="text" id="mens"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Age:</font></td>
        <td width="420"> 
          <input name="age" type="text" id="age2" size="5" maxlength="5"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">ICQ 
          / AIM / MSN: </font></td>
        <td width="420"> 
          <input name="icq" type="text" id="icq"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail 
          : </font></td>
        <td width="420"> 
          <input name="mail" type="text" id="mail"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ville:</font></td>
        <td width="420"> 
          <input name="ville" type="text" id="ville"></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Connection:</font></td>
        <td width="420"> 
          <select name="conn" id="conn">
            <option value="ADSL">ADSL</option>
            <option value="Cable">Cable</option>
            <option value="T1 ou plus">T1 ou plus</option>
          </select></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Exp&eacute;rience:</font></td>
        <td width="420"> 
          <SELECT name=exp>
            <option value="- d'un mois" selected>- d'un mois</option>
            <option value="- de 6 mois">- de 6 mois</option>
            <option value="- d'un ans">- d'un ans</option>
            <option value="+ d'un ans">+ d'un ans</option>
            <option value="+ de deux ans">+ de deux ans</option>
          </SELECT></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Disponibilit&eacute;:</font></td>
        <td width="420"> 
          <select name="dispo" id="dispo">
            <option value="Week-Ends" selected>Week-Ends</option>
            <option value="Vacances">Vacances</option>
            <option value="Les 2">Les 2</option>
            <option value="Tout le temps">Tout le temps</option>
          </select></td>
      </tr>
      <tr> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Section:</font></td>
        <td width="420"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input type="radio" onClick="return checkCheckBox3(this.form)" name="section" value="cs" id="radio" checked>
          Counter-Strike 
          <input type="radio" onClick="return checkCheckBox4(this.form)" name="section" value="war3" id="radio">
          Warcraft III</font></td>
      </tr>
      <tr id="cs"> 
        <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Style 
          de jeu:</font></td>
        <td width="420"> 
          <select name="stylecs" id="stylecs">
            <option value="Campeur" selected>Campeur</option>
            <option value="Rusher">Rusher</option>
            <option value="Sniper">Sniper</option>
            <option value="Strateur">Strateur</option>
          </select></td>
      </tr>
      <tr> 
        <td colspan="2" valign="top"><table width="600" border="0" cellpadding="0" cellspacing="0" id="war3" style="display:none">
            <tr> 
              <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Level 
                :</font></td>
              <td width="420"><input name="levelw3" type="text" id="levelw3"></td>
            </tr>
            <tr> 
              <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url 
                du compte Battlenet:</font></td>
              <td width="420"><input name="urlw3" type="text" id="urlw3"></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td width="180" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lettre 
          de motivation :</font></td>
        <td width="420"> 
          <textarea name="lettre" cols="50" rows="4" id="lettre"></textarea></td>
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
<?
}
}
?>