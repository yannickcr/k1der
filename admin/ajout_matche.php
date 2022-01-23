<? include "secu.php";

  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
?> 
<script language="JavaScript" type="text/JavaScript">
function checkCheckBox666(form)
{
	document.getElementById('cs').style.display='block';
	document.getElementById('war3').style.display='none';
}

function checkCheckBox667(form)
{
	document.getElementById('cs').style.display='none';
	document.getElementById('war3').style.display='block';
}

</script>

<script language="JavaScript" type="text/JavaScript">
function checkCheckBox(form)
{
  if (form.k1.checked == false )
    {
	document.getElementById('jou_k1_t').style.display='none';
	document.getElementById('jou_k1_k').style.display='block';
    }
  else
    {
	document.getElementById('jou_k1_t').style.display='block';
	document.getElementById('jou_k1_k').style.display='none';
    }
}
function checkCheckBox2(form)
{
  if (form.k2.checked == false )
    {
	document.getElementById('jou_k2_t').style.display='none';
	document.getElementById('jou_k2_k').style.display='block';
    }
  else
    {
	document.getElementById('jou_k2_t').style.display='block';
	document.getElementById('jou_k2_k').style.display='none';
    }
}
function checkCheckBox3(form)
{
  if (form.k3.checked == false )
    {
	document.getElementById('jou_k3_t').style.display='none';
	document.getElementById('jou_k3_k').style.display='block';
    }
  else
    {
	document.getElementById('jou_k3_t').style.display='block';
	document.getElementById('jou_k3_k').style.display='none';
    }
}
function checkCheckBox4(form)
{
  if (form.k4.checked == false )
    {
	document.getElementById('jou_k4_t').style.display='none';
	document.getElementById('jou_k4_k').style.display='block';
    }
  else
    {
	document.getElementById('jou_k4_t').style.display='block';
	document.getElementById('jou_k4_k').style.display='none';
    }
}
function checkCheckBox5(form)
{
  if (form.k5.checked == false )
    {
	document.getElementById('jou_k5_t').style.display='none';
	document.getElementById('jou_k5_k').style.display='block';
    }
  else
    {
	document.getElementById('jou_k5_t').style.display='block';
	document.getElementById('jou_k5_k').style.display='none';
    }
}
</script>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
      un Matche=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<form name="form1" method="post" action="admin/ajout_matche2.php">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="30%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Equipe 
        affront&eacute;e : <b></b></font></td>
      <td width="70%" valign="top"> <font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="mechants" type="text" id="mechants">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url du site 
        internet :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="site" type="text" id="site" size="30">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Channel 
        IRC :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"># 
        <input name="irc" type="text" id="irc">
        </font></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date : 
        <?
	  $jour = date("d");
	  $mois = date("F");
	  $annee = date("Y");
	  
	  $auteur = ucfirst($HTTP_COOKIE_VARS[gen]);
	  ?>
        <input name="auteur" type="hidden" id="auteur" value="<? echo $auteur; ?>">
        </font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
        <input type="text" name="jour" maxlength="2" size="1" value=<? echo $jour; ?>>
        <select name="mois">
          <option value="Janvier"
	<?
if ($mois =="January")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
          <option value="F&eacute;vrier"
	<?
if ($mois =="February")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
          <option value="Mars"
	<?
if ($mois =="March")
	{
		echo " SELECTED";
	}
?>>Mars</option>
          <option value="Avril"
	<?
if ($mois =="April")
	{
		echo " SELECTED";
	}
?>>Avril</option>
          <option value="Mai"
	<?
if ($mois =="May")
	{
		echo " SELECTED";
	}
?>>Mai</option>
          <option value="Juin"
	<?
if ($mois =="June")
	{
		echo " SELECTED";
	}
?>>Juin</option>
          <option value="Juillet"
	<?
if ($mois =="July")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
          <option value="Ao&ucirc;t"
	<?
if ($mois =="August")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
          <option value="Septembre"
	<?
if ($mois =="September")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
          <option value="Octobre"
	<?
if ($mois =="October")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
          <option value="Novembre"
	<?
if ($mois =="November")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
          <option value="D&eacute;cembre"
	<?
if ($mois =="December")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
        </select>
        <input type="text" name="annee" maxlength="4" size="2" value=<? echo $annee; ?>>
        </font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td height="50" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type 
        : </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" onClick="this.form.lan.style.display='block';this.form.paslan.style.display='none';" name="type" value="LAN Arena">
        LAN Arena<br>
        <input type="radio" onClick="this.form.paslan.style.display='block';this.form.lan.style.display='none';" name="type" value="Internet">
        Internet<br>
        <input type="radio" onClick="this.form.paslan.style.display='block';this.form.lan.style.display='none';" name="type" value="Jeu en R&eacute;seau">
        Jeu en R&eacute;seau </font></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Localisation/Server 
        : </font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="lan" id="lan" style="display:none">
          <?
		$requete  = "SELECT * FROM lan_party";
		$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
		$nbre =mysql_num_rows($req);
		while($disp = mysql_fetch_array($req))
		{
		?>
          <option value="<? echo $disp[nom]; ?>"><? echo $disp[nom]; ?></option>
          <?
		}
		?>
        </select>
        <input name="paslan" type="text" id="paslan">
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
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cartes jou&eacute;es 
        :</font></td>
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
      <td>&nbsp;</td>
      <td valign="middle">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score d&eacute;taill&eacute; 
        :</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" onClick="return checkCheckBox666(this.form)" name="section" value="cs" id="radio" checked>
        Oui 
        <input type="radio" onClick="return checkCheckBox667(this.form)" name="section" value="war3" id="radio">
        Non</font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="war3" style="display:none">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score K1der 
        :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="score_k1" type="text" id="score_k1" value="<? echo $disp[score_k1]; ?>" size="4" maxlength="4">
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></td>
    </tr>
    <tr> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score Adversaires 
        :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="score_me" type="text" id="score_me" value="<? echo $disp[score_me]; ?>" size="4" maxlength="4">
        </b></b></font></strong></font></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="cs">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score Carte 
        1 :</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Terro 
        : 
        <input name="score_map_k1_t" type="text" id="score_map_k1_t" value="<? echo $disp[score_map_k1_t]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map_me_ct" type="text" id="score_map_me_ct" value="<? echo $disp[score_map_me_ct]; ?>" size="1" maxlength="3">
        <strong>|</strong> Ct : 
        <input name="score_map_k1_ct" type="text" id="score_map_k1_ct" value="<? echo $disp[score_map_k1_ct]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map_me_t" type="text" id="score_map_me_t" value="<? echo $disp[score_map_me_t]; ?>" size="1" maxlength="3">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score Carte 
        2 :</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Terro 
        : 
        <input name="score_map2_k1_t" type="text" id="score_map2_k1_t" value="<? echo $disp[score_map2_k1_t]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map2_me_ct" type="text" id="score_map2_me_ct" value="<? echo $disp[score_map2_me_ct]; ?>" size="1" maxlength="3">
        <strong>|</strong> Ct : 
        <input name="score_map2_k1_ct" type="text" id="score_map2_k1_ct" value="<? echo $disp[score_map2_k1_ct]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map2_me_t" type="text" id="score_map2_me_t" value="<? echo $disp[score_map2_me_t]; ?>" size="1" maxlength="3">
        </font></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr> 
      <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">HLTV :</font></td>
      <td colspan="2" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="hltv" type="text" id="hltv2" value="http://" size="40">
        </font></td>
    </tr>
    <tr> 
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="3"><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Composition 
          des &eacute;quipes</font></strong></div>
        <div align="center"></div></td>
    </tr>
    <tr> 
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">K1der 
        :</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <select name="jou_k1_k" id="jou_k1_k">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($disp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k1_t" type="text" id="jou_k1_t" style="display:none">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k1" onClick="return checkCheckBox(this.form)">
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <select name="jou_k2_k" id="jou_k2_k">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($disp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k2_t" type="text" id="jou_k2_t" style="display:none">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k2" onClick="return checkCheckBox2(this.form)">
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <select name="jou_k3_k" id="jou_k3_k">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($disp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k3_t" type="text" id="jou_k3_t" style="display:none">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k3" onClick="return checkCheckBox3(this.form)">
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <select name="jou_k4_k" id="jou_k4_k">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($disp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k4_t" type="text" id="jou_k4_t" style="display:none">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k4" onClick="return checkCheckBox4(this.form)">
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <select name="jou_k5_k" id="jou_k5_k">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($disp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k5_t" type="text" id="jou_k5_t" style="display:none">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k5" onClick="return checkCheckBox5(this.form)">
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td colspan="2" valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Adversaires 
        :</font></td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m1" type="text" id="jou_m12">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m2" type="text" id="jou_m22">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m3" type="text" id="jou_m32">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m4" type="text" id="jou_m42">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"><b><b> 
        <input name="jou_m5" type="text" id="jou_m52">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Commentaires:</font></td>
      <td colspan="2" valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="3" ><textarea name="comm" rows="5" id="comm" style="width=100%"></textarea></td>
    </tr>
    <tr> 
      <td colspan="3" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" name="Submit" value="Envoyer">
  </div>
</form>
