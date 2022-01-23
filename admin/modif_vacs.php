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
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
  </tr>
  <tr> 
    <td width="442" height="20" background="images/fond.gif"><p><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Modifier 
        les dates des </b></font></b></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF"><b>vacances</b>=-</font></b></font></p>
      </td>
  </tr>
</table>
<br>
<form name="form1" method="post" action="">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="125"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
        de d&eacute;but :</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
        </font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
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
        </font></strong></font></td>
    </tr>
  </table>
  </form>
