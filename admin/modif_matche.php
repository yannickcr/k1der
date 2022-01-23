  <?
include "secu.php";
?> <?
  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches WHERE id=$id";
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
<form name="form1" method="post" action="admin/modif_matche2.php">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="30%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Equipe 
        affront&eacute;e : <b> 
        <input name="id" type="hidden" id="id" value="<? echo $disp[id]; ?>">
        </b></font></td>
      <td width="70%" valign="top"> <font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="mechants" type="text" id="mechants" value="<? echo $disp[mechants]; ?>">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url du site 
        internet :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="site" type="text" id="site" value="<? echo $disp[site]; ?>" size="30">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Channel 
        IRC :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"># 
        <input name="irc" type="text" id="irc" value="<? echo $disp[irc]; ?>">
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
	  ?>
        </font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
        <input type="text" name="jour" maxlength="2" size="1" value=<? echo $disp[jour]; ?>>
        <select name="mois">
          <option value="Janvier"
	<?
if ($disp[mois] =="Janvier")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
          <option value="F&eacute;vrier"
	<?
if ($disp[mois] =="Février")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
          <option value="Mars"
	<?
if ($disp[mois] =="Mars")
	{
		echo " SELECTED";
	}
?>>Mars</option>
          <option value="Avril"
	<?
if ($disp[mois] =="Avril")
	{
		echo " SELECTED";
	}
?>>Avril</option>
          <option value="Mai"
	<?
if ($disp[mois] =="Mai")
	{
		echo " SELECTED";
	}
?>>Mai</option>
          <option value="Juin"
	<?
if ($disp[mois] =="Juin")
	{
		echo " SELECTED";
	}
?>>Juin</option>
          <option value="Juillet"
	<?
if ($disp[mois] =="Juillet")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
          <option value="Ao&ucirc;t"
	<?
if ($disp[mois] =="Août")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
          <option value="Septembre"
	<?
if ($disp[mois] =="Septembre")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
          <option value="Octobre"
	<?
if ($disp[mois] =="Octobre")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
          <option value="Novembre"
	<?
if ($disp[mois] =="Novembre")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
          <option value="D&eacute;cembre"
	<?
if ($disp[mois] =="Décembre")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
        </select>
        <input type="text" name="annee" maxlength="4" size="2" value=<? echo $disp[annee]; ?>>
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type : </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input onclick="this.form.lan.style.display='block';this.form.paslan.style.display='none';" name="type" type="radio" value="LAN Arena" <? if ($disp[type] == "LAN Arena") { echo 'checked'; } ?>>
        </b></b></font></strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">LAN 
        Arena<strong><b><b> </b></b></strong></font></font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><b><b> 
        <input onclick="this.form.lan.style.display='none';this.form.paslan.style.display='block';" type="radio" name="type" value="Internet" <? if ($disp[type] == "Internet") { echo 'checked'; } ?>>
        </b></b></strong>Internet</font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><b><b> 
        <input onclick="this.form.lan.style.display='none';this.form.paslan.style.display='block';" type="radio" name="type" value="Jeu en R&eacute;seau" <? if ($disp[type] == "Jeu en R&eacute;seau") { echo 'checked'; } ?>>
        </b></b></strong>Jeu en R&eacute;seau</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Localisation/Server 
        : </font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <select name="lan" id="lan" <? if ($disp[type] != "LAN Arena") { echo "style='display:none'"; } ?>>
          <?
		$requete2  = "SELECT * FROM lan_party";
		$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());  
		$nbre2 =mysql_num_rows($req2);
		while($disp2 = mysql_fetch_array($req2))
		{
		?>
          <option value="<? echo $disp2[nom]; ?>" <? if($disp[loc] == $disp2[nom]) { echo "selected"; } ?>><? echo $disp2[nom]; ?></option>
          <?
		}
		?>
        </select>
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="paslan" type="text" id="paslan" <? if ($disp[type] == "LAN Arena") { echo "style='display:none'"; } ?> value="<? echo $disp[loc]; ?>">
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Occasion 
        :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="occ" type="text" id="occ" value="<? echo $disp[occ]; ?>">
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
if ($fichier == $disp[map]) { $sel = 'selected'; } else { $sel = ''; }
echo "<option value='$fichier' $sel>$fichier</option>";
}
}
// ferme le répertoire
closedir($dir);
?>
        </select>
        </font></font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td valign="middle"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2">Carte 
        2 : 
        <select name="map2" id="select2" onChange="return plopplop(this.form)">
          <option value='Aucune'>Aucune</option>
          <?php
// ouvrir le répertoire
$dir = opendir("images/cartes");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
if ($fichier != '.' && $fichier != '..' )
{
$fichier = str_replace(".jpg", "", $fichier);
if ($fichier == $disp[map2]) { $sal = 'selected'; } else { $sal = ''; }
echo "<option value='$fichier' $sal>$fichier</option>";
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
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score d&eacute;taill&eacute; 
        :</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" onClick="return checkCheckBox666(this.form)" name="section" value="cs" id="radio" <? if ($disp[det] == '1') { echo "checked"; } ?>>
        Oui 
        <input type="radio" onClick="return checkCheckBox667(this.form)" name="section" value="war3" id="radio" <? if ($disp[det] == '0') { echo "checked"; } ?>>
        Non</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="war3" <? if ($disp[det] == '1') { echo "style=\"display:none\""; } ?>>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score K1der 
        :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="score_k1" type="text" id="score_k14" value="<? echo $disp[score_k1]; ?>" size="4" maxlength="4">
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></td>
    </tr>
    <tr> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score Adversaires 
        :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="score_me" type="text" id="score_me4" value="<? echo $disp[score_me]; ?>" size="4" maxlength="4">
        </b></b></font></strong></font></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="cs" <? if ($disp[det] == '0') { echo "style=\"display:none\""; } ?>>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score Carte 
        1 :</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Terro 
        : 
        <input name="score_map_k1_t" type="text" id="score_map_k1_t5" value="<? echo $disp[score_map_k1_t]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map_me_ct" type="text" id="score_map_me_ct5" value="<? echo $disp[score_map_me_ct]; ?>" size="1" maxlength="3">
        <strong>|</strong> Ct : 
        <input name="score_map_k1_ct" type="text" id="score_map_k1_ct5" value="<? echo $disp[score_map_k1_ct]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map_me_t" type="text" id="score_map_me_t5" value="<? echo $disp[score_map_me_t]; ?>" size="1" maxlength="3">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Score Carte 
        2 :</font></td>
      <td valign="middle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Terro 
        : 
        <input name="score_map2_k1_t" type="text" id="score_map2_k1_t5" value="<? echo $disp[score_map2_k1_t]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map2_me_ct" type="text" id="score_map2_me_ct5" value="<? echo $disp[score_map2_me_ct]; ?>" size="1" maxlength="3">
        <strong>|</strong> Ct : 
        <input name="score_map2_k1_ct" type="text" id="score_map2_k1_ct5" value="<? echo $disp[score_map2_k1_ct]; ?>" size="1" maxlength="3">
        / 
        <input name="score_map2_me_t" type="text" id="score_map2_me_t5" value="<? echo $disp[score_map2_me_t]; ?>" size="1" maxlength="3">
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
        <input name="hltv" type="text" id="hltv2" value="<? echo $disp[hltv]; ?>" size="40">
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
        <?
	  $nbre = mysql_num_rows(mysql_query("SELECT * FROM equipe WHERE kinder='$disp[jou_k1]'"));
	  ?>
        <select name="jou_k1_k" id="jou_k1_k" style="display:<? if ($nbre == 0) { echo "none"; } else { echo "block"; } ?>">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($dasp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $dasp[kinder]; ?>" <? if ($disp[jou_k1] ==  $dasp[kinder]) { echo "selected"; } ?>><? echo $dasp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k1_t" type="text" id="jou_k1_t" style="display:<? if ($nbre == 0) { echo "block"; } else { echo "none"; } ?>" value="<? echo $disp[jou_k1]; ?>">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k1" onClick="return checkCheckBox(this.form)" <? if ($nbre == 0) { echo "checked"; } ?>>
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <?
	  $nbre = mysql_num_rows(mysql_query("SELECT * FROM equipe WHERE kinder='$disp[jou_k2]'"));
	  ?>
        <select name="jou_k2_k" id="jou_k2_k" style="display:<? if ($nbre == 0) { echo "none"; } else { echo "block"; } ?>">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($dasp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $dasp[kinder]; ?>" <? if ($disp[jou_k2] ==  $dasp[kinder]) { echo "selected"; } ?>><? echo $dasp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k2_t" type="text" id="jou_k2_t" style="display:<? if ($nbre == 0) { echo "block"; } else { echo "none"; } ?>" value="<? echo $disp[jou_k2]; ?>">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k2" onClick="return checkCheckBox2(this.form)" <? if ($nbre == 0) { echo "checked"; } ?>>
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <?
	  $nbre = mysql_num_rows(mysql_query("SELECT * FROM equipe WHERE kinder='$disp[jou_k3]'"));
	  ?>
        <select name="jou_k3_k" id="jou_k3_k" style="display:<? if ($nbre == 0) { echo "none"; } else { echo "block"; } ?>">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($dasp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $dasp[kinder]; ?>" <? if ($disp[jou_k3] ==  $dasp[kinder]) { echo "selected"; } ?>><? echo $dasp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k3_t" type="text" id="jou_k3_t" style="display:<? if ($nbre == 0) { echo "block"; } else { echo "none"; } ?>" value="<? echo $disp[jou_k3]; ?>">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k3" onClick="return checkCheckBox3(this.form)" <? if ($nbre == 0) { echo "checked"; } ?>>
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <?
	  $nbre = mysql_num_rows(mysql_query("SELECT * FROM equipe WHERE kinder='$disp[jou_k4]'"));
	  ?>
        <select name="jou_k4_k" id="jou_k4_k" style="display:<? if ($nbre == 0) { echo "none"; } else { echo "block"; } ?>">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($dasp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $dasp[kinder]; ?>" <? if ($disp[jou_k4] ==  $dasp[kinder]) { echo "selected"; } ?>><? echo $dasp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k4_t" type="text" id="jou_k4_t" style="display:<? if ($nbre == 0) { echo "block"; } else { echo "none"; } ?>" value="<? echo $disp[jou_k4]; ?>">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k4" onClick="return checkCheckBox4(this.form)" <? if ($nbre == 0) { echo "checked"; } ?>>
        Non K1der </font></font></td>
    </tr>
    <tr> 
      <td width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <?
	  $nbre = mysql_num_rows(mysql_query("SELECT * FROM equipe WHERE kinder='$disp[jou_k5]'"));
	  ?>
        <select name="jou_k5_k" id="jou_k5_k" style="display:<? if ($nbre == 0) { echo "none"; } else { echo "block"; } ?>">
          <?
		$requete  = mysql_query("SELECT * FROM equipe ORDER BY kinder");
		while ($dasp = mysql_fetch_array($requete))
		{
		?>
          <option value="<? echo $dasp[kinder]; ?>" <? if ($disp[jou_k5] ==  $dasp[kinder]) { echo "selected"; } ?>><? echo $dasp[kinder]; ?></option>
          <?
		}
		?>
        </select>
        <input name="jou_k5_t" type="text" id="jou_k5_t" style="display:<? if ($nbre == 0) { echo "block"; } else { echo "none"; } ?>" value="<? echo $disp[jou_k5]; ?>">
        </font></font></td>
      <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2"> 
        <input type="checkbox" value="1" name="k5" onClick="return checkCheckBox5(this.form)" <? if ($nbre == 0) { echo "checked"; } ?>>
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
        <input name="jou_m1" type="text" id="jou_m12" value="<? echo $disp[jou_m1]; ?>">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m2" type="text" id="jou_m22" value="<? echo $disp[jou_m2]; ?>">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m3" type="text" id="jou_m32" value="<? echo $disp[jou_m3]; ?>">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m4" type="text" id="jou_m42" value="<? echo $disp[jou_m4]; ?>">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td width="200">&nbsp;</td>
      <td width="70%" colspan="2" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="jou_m5" type="text" id="jou_m52" value="<? echo $disp[jou_m5]; ?>">
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
      <td colspan="3" ><textarea name="comm" rows="5" id="comm" style="width=100%"><? echo $disp[comm]; ?></textarea></td>
    </tr>
    <tr> 
      <td colspan="3" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" name="Submit" value="Envoyer">
  </div>
</form>
