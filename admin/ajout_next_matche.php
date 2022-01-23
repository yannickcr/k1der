  <?
include "secu.php"; ?> <?
  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
?>
<SCRIPT LANGUAGE="JavaScript">
function aj_mat(){

  with(document.matform){

    if( mechants.value == '' ){
      alert("Sa peut etre intéressant de mettre le nom de l'équipe adverse, nan ?");
      return false;
    }	
    if( occ.value == '' ){
      alert("Met au moins à quelle occasion se déroule le match...");
      return false;
    }
    if( line[0].checked == true ){

	if( joueur1.value == joueur2.value ){
      alert("Sa m'étonnerai que " + joueur1.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur1.value == joueur3.value ){
      alert("Sa m'étonnerai que " + joueur1.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur1.value == joueur4.value ){
      alert("Sa m'étonnerai que " + joueur1.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur1.value == joueur5.value ){
      alert("Sa m'étonnerai que " + joueur1.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur2.value == joueur3.value ){
      alert("Sa m'étonnerai que " + joueur2.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur2.value == joueur4.value ){
      alert("Sa m'étonnerai que " + joueur2.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur2.value == joueur5.value ){
      alert("Sa m'étonnerai que " + joueur2.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur3.value == joueur4.value ){
      alert("Sa m'étonnerai que " + joueur3.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
    if( joueur3.value == joueur5.value ){
      alert("Sa m'étonnerai que " + joueur3.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }	
    if( joueur4.value == joueur5.value ){
      alert("Sa m'étonnerai que " + joueur4.value + " puisse prendre la place de 2 joueurs\n                        Où alors il est très fort :)");
      return false;
    }
	}
		
		}
  return true;
}
</SCRIPT>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
      un prochain Matche=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<form action="admin/ajout_next_matche2.php" method="post" name="matform" id="matform" onSubmit="return aj_mat()">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="30%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Equipe 
        &agrave; affronter : </font></td>
      <td width="70%" valign="top"> <font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="mechants" type="text" id="mechants">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date et 
        heure : 
        <?
	  $jour = date("d");
	  $mois = date("F");
	  $annee = date("Y");
	  $heure = date("H");
	  $minute = date("i");
	  
	  $auteur = ucfirst($HTTP_COOKIE_VARS[gen]);
	  ?>
        </font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
        <input type="text" name="jour" maxlength="2" size="1" value=<? echo $jour; ?>>
        <select name="mois">
          <option value="01"
	<?
if ($mois =="January")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
          <option value="02"
	<?
if ($mois =="February")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
          <option value="03"
	<?
if ($mois =="March")
	{
		echo " SELECTED";
	}
?>>Mars</option>
          <option value="04"
	<?
if ($mois =="April")
	{
		echo " SELECTED";
	}
?>>Avril</option>
          <option value="05"
	<?
if ($mois =="May")
	{
		echo " SELECTED";
	}
?>>Mai</option>
          <option value="06"
	<?
if ($mois =="June")
	{
		echo " SELECTED";
	}
?>>Juin</option>
          <option value="07"
	<?
if ($mois =="July")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
          <option value="08"
	<?
if ($mois =="August")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
          <option value="09"
	<?
if ($mois =="September")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
          <option value="10"
	<?
if ($mois =="October")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
          <option value="11"
	<?
if ($mois =="November")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
          <option value="12"
	<?
if ($mois =="December")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
        </select>
        <input type="text" name="annee" maxlength="4" size="2" value=<? echo $annee; ?>>
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&agrave;</font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="heure" type="text" id="heure" value="<? echo $heure; ?>" size="1" maxlength="2">
        : 
        <input name="minute" type="text" id="minute" value="<? echo $minute; ?>" size="1" maxlength="2">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Occasion 
        :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="occ" type="text" id="occ">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td width="70%" valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cartes :</font></td>
      <td width="70%" valign="middle"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
        1 :</font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="map" id="map">
          <?php
// ouvrir le répertoire
$dir = opendir("images/cartes");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
if ($fichier != '.' && $fichier != '..' )
{
$fichier = str_replace(".jpg", "", $fichier);
echo "<option value='$fichier'>$fichier</option>";
}
}
// ferme le répertoire
closedir($dir);
?>
        </select>
        </font></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td width="70%" valign="middle"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
        2 :</font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="map2" id="map2">
          <option value='Aucune'>Aucune</option>
          <option value='Sais pas'>Sais pas</option>
          <?php
// ouvrir le répertoire
$dir = opendir("images/cartes");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
if ($fichier != '.' && $fichier != '..' )
{
$fichier = str_replace(".jpg", "", $fichier);
echo "<option value='$fichier'>$fichier</option>";
}
}
// ferme le répertoire
closedir($dir);
?>
        </select>
        </font></font></font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Line up 
        : </font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="line" onclick="this.form.joueur1.style.display='block';this.form.joueur2.style.display='block';this.form.joueur3.style.display='block';this.form.joueur4.style.display='block';this.form.joueur5.style.display='block';" value="oui">
        Oui 
        <input name="line" type="radio" onclick="this.form.joueur1.style.display='none';this.form.joueur2.style.display='none';this.form.joueur3.style.display='none';this.form.joueur4.style.display='none';this.form.joueur5.style.display='none';" value="non" checked>
        Non </font></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="joueur1" id="joueur1" STYLE="font-weight: bold;font-family:Verdana, Arial, Helvetica, sans-serif" style="display:none">
          <?
		$requete  = "SELECT * FROM equipe";
		$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		$nbre =mysql_num_rows($req);
		while($disp = mysql_fetch_array($req))
		{
		if ($disp[next_match] == "oui")
		{
		$color = "#0000FF";
		}
		else if ($disp[next_match] == "non")
		{
		$color = "#CC0000";
		}
		else
		{
		$color = "#000000";
		}
		?>
          <OPTION VALUE="<? echo $disp[kinder]; ?>" STYLE="color:<? echo $color; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        </b></b></font></strong></font></strong></font></strong></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<strong><font color="#0000FF" size="4">-</font></strong> 
        Dispo </font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="joueur2" id="joueur2" STYLE="font-weight: bold;font-family:Verdana, Arial, Helvetica, sans-serif" style="display:none">
          <?
		$requete  = "SELECT * FROM equipe";
		$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		$nbre =mysql_num_rows($req);
		while($disp = mysql_fetch_array($req))
		{
		if ($disp[next_match] == "oui")
		{
		$color = "#0000FF";
		}
		else if ($disp[next_match] == "non")
		{
		$color = "#CC0000";
		}
		else
		{
		$color = "#000000";
		}
		?>
          <OPTION VALUE="<? echo $disp[kinder]; ?>" STYLE="color:<? echo $color; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        </b></b></font></strong></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<font color="#FF0000" size="4"><strong>-</strong></font> 
        Non Dispo </font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="joueur3" id="joueur3" STYLE="font-weight: bold;font-family:Verdana, Arial, Helvetica, sans-serif" style="display:none">
          <?
		$requete  = "SELECT * FROM equipe";
		$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		$nbre =mysql_num_rows($req);
		while($disp = mysql_fetch_array($req))
		{
		if ($disp[next_match] == "oui")
		{
		$color = "#0000FF";
		}
		else if ($disp[next_match] == "non")
		{
		$color = "#CC0000";
		}
		else
		{
		$color = "#000000";
		}
		?>
          <OPTION VALUE="<? echo $disp[kinder]; ?>" STYLE="color:<? echo $color; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        </b></b></font></strong></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<font size="4"><strong>-</strong></font> 
        N'a rien dis</font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="joueur4" id="joueur4" STYLE="font-weight: bold;font-family:Verdana, Arial, Helvetica, sans-serif" style="display:none">
          <?
		$requete  = "SELECT * FROM equipe";
		$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		$nbre =mysql_num_rows($req);
		while($disp = mysql_fetch_array($req))
		{
		if ($disp[next_match] == "oui")
		{
		$color = "#0000FF";
		}
		else if ($disp[next_match] == "non")
		{
		$color = "#CC0000";
		}
		else
		{
		$color = "#000000";
		}
		?>
          <OPTION VALUE="<? echo $disp[kinder]; ?>" STYLE="color:<? echo $color; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        </b></b></font></strong></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="joueur5" id="joueur5" STYLE="font-weight: bold;font-family:Verdana, Arial, Helvetica, sans-serif" style="display:none">
          <?
		$requete  = "SELECT * FROM equipe";
		$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		$nbre =mysql_num_rows($req);
		while($disp = mysql_fetch_array($req))
		{
		if ($disp[next_match] == "oui")
		{
		$color = "#0000FF";
		}
		else if ($disp[next_match] == "non")
		{
		$color = "#CC0000";
		}
		else
		{
		$color = "#000000";
		}
		?>
          <OPTION VALUE="<? echo $disp[kinder]; ?>" STYLE="color:<? echo $color; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        </b></b></font></strong></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top">&nbsp;</td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" name="Submit" value="Envoyer">
  </div>
</form>
