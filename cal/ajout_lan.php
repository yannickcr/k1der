<?
  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
?> 
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
      une LAN=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<form name="form1" method="post" action="cal/ajout_lan2.php">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><font color="#FF0000">ATTENTION 
        :</font> </strong>Vous n'avez aucun moyen de modifier les 
        donn&eacute;es rentr&eacute;es, seuls les admins peuvent le faire, v&eacute;rifier 
        donc bien avant de confirmer, merci.</font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td width="30%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
        : </font></td>
      <td width="70%" valign="top"> <font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="nom" type="text" id="nom">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date de 
        d&eacute;but : 
        <?
	  $jour = date("d");
	  $mois = date("F");
	  $annee = date("Y");
	  ?>
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
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Dur&eacute;e 
        :</font></td>
      <td valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="dur" type="text" id="dur" size="1" maxlength="2">
        Jours</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lieu ou 
        Adresse :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="adresse" type="text" id="loc2">
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ville :</font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="ville" type="text" id="loc3">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">D&eacute;partement 
        :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">N&deg; 
        <select name="dep" id="dep">
          <option value='01'>01</option>
          <option value='02'>02</option>
          <option value='2A'>2A</option>
          <option value='2B'>2B</option>
          <?
		$a = 03;
		while ($a <= 95)
		{
		if ($a < 10)
		{
		echo "<option value='0$a'>0$a</option>";
		}
		else if ($a == 29)
		{
		echo "<option value='$a' selected>$a</option>";
		}
		else
		{
		echo "<option value='$a'>$a</option>";
		}
		$a++;
		}
		?>
        </select>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Adresse 
        Internet :</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="site" type="text" id="url2" value="http://" size="40">
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail :</font></td>
      <td valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        <input name="mail" type="text" id="url3" size="40">
        </b></b></font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nb de Places 
        :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="places" type="text" id="places" size="3" maxlength="4">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Prix :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="prix" type="text" id="prix" size="3" maxlength="4">
        &euro; </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tournois 
        :</font></td>
      <td> <li></li>
        <input name="tournois1" type="text" id="tournois1"> </td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois2" type="text" id="tournois2"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois3" type="text" id="tournois3"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois4" type="text" id="tournois4"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois5" type="text" id="tournois5"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois6" type="text" id="tournois6"></td>
    </tr>
    <tr> 
      <td height="23"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois7" type="text" id="tournois7"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><li></li>
        <input name="tournois8" type="text" id="tournois8"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lots 
        :</font></td>
      <td valign="top"><textarea name="lots" cols="50" rows="3" id="lots"></textarea></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Commentaires 
        :</font></td>
      <td valign="top"><textarea name="infos" cols="50" rows="3" id="infos"></textarea></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2" >&nbsp;</td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" name="Submit" value="Envoyer">
  </div>
</form>
