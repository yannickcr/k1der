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
function num1(form)
{
	document.getElementById('champ1a').style.display='block';
	document.getElementById('champ1b').style.display='block';
	document.getElementById('champ1c').style.display='block';
	document.getElementById('champ1d').style.display='block';
	document.getElementById('champ1e').style.display='block';
	document.getElementById('champ1f').style.display='block';
	document.getElementById('champ1g').style.display='block';
	document.getElementById('champ1h').style.display='block';
	
	document.getElementById('champ2a').style.display='none';
	document.getElementById('champ2b').style.display='none';
	document.getElementById('champ2c').style.display='none';
	document.getElementById('champ2d').style.display='none';
	document.getElementById('champ2e').style.display='none';
	document.getElementById('champ2f').style.display='none';
	document.getElementById('champ2g').style.display='none';
	document.getElementById('champ2h').style.display='none';
	
	document.getElementById('champ3a').style.display='none';
	document.getElementById('champ3b').style.display='none';
	document.getElementById('champ3c').style.display='none';
	document.getElementById('champ3d').style.display='none';
	document.getElementById('champ3e').style.display='none';
	document.getElementById('champ3f').style.display='none';
	document.getElementById('champ3g').style.display='none';
	document.getElementById('champ3h').style.display='none';
}

</script>
<script language="JavaScript" type="text/JavaScript">
function num2(form)
{
	document.getElementById('champ1a').style.display='none';
	document.getElementById('champ1b').style.display='none';
	document.getElementById('champ1c').style.display='none';
	document.getElementById('champ1d').style.display='none';
	document.getElementById('champ1e').style.display='none';
	document.getElementById('champ1f').style.display='none';
	document.getElementById('champ1g').style.display='none';
	document.getElementById('champ1h').style.display='none';
	
	document.getElementById('champ2a').style.display='block';
	document.getElementById('champ2b').style.display='block';
	document.getElementById('champ2c').style.display='block';
	document.getElementById('champ2d').style.display='block';
	document.getElementById('champ2e').style.display='block';
	document.getElementById('champ2f').style.display='block';
	document.getElementById('champ2g').style.display='block';
	document.getElementById('champ2h').style.display='block';
	
	document.getElementById('champ3a').style.display='none';
	document.getElementById('champ3b').style.display='none';
	document.getElementById('champ3c').style.display='none';
	document.getElementById('champ3d').style.display='none';
	document.getElementById('champ3e').style.display='none';
	document.getElementById('champ3f').style.display='none';
	document.getElementById('champ3g').style.display='none';
	document.getElementById('champ3h').style.display='none';
}

</script>
<script language="JavaScript" type="text/JavaScript">
function num3(form)
{
	document.getElementById('champ1a').style.display='none';
	document.getElementById('champ1b').style.display='none';
	document.getElementById('champ1c').style.display='none';
	document.getElementById('champ1d').style.display='none';
	document.getElementById('champ1e').style.display='none';
	document.getElementById('champ1f').style.display='none';
	document.getElementById('champ1g').style.display='none';
	document.getElementById('champ1h').style.display='none';
	
	document.getElementById('champ2a').style.display='none';
	document.getElementById('champ2b').style.display='none';
	document.getElementById('champ2c').style.display='none';
	document.getElementById('champ2d').style.display='none';
	document.getElementById('champ2e').style.display='none';
	document.getElementById('champ2f').style.display='none';
	document.getElementById('champ2g').style.display='none';
	document.getElementById('champ2h').style.display='none';
	
	document.getElementById('champ3a').style.display='block';
	document.getElementById('champ3b').style.display='block';
	document.getElementById('champ3c').style.display='block';
	document.getElementById('champ3d').style.display='block';
	document.getElementById('champ3e').style.display='block';
	document.getElementById('champ3f').style.display='block';
	document.getElementById('champ3g').style.display='block';
	document.getElementById('champ3h').style.display='block';
}
</script>
<script language="JavaScript" type="text/JavaScript">
function man1(form)
{
	document.getElementById('manche1').style.display='block';
	document.getElementById('manche2').style.display='none';
	document.getElementById('manche3').style.display='none';
}
</script>
<script language="JavaScript" type="text/JavaScript">
function man2(form)
{
	document.getElementById('manche1').style.display='block';
	document.getElementById('manche2').style.display='block';
	document.getElementById('manche3').style.display='none';
}
</script>
<script language="JavaScript" type="text/JavaScript">
function man3(form)
{
	document.getElementById('manche1').style.display='block';
	document.getElementById('manche2').style.display='block';
	document.getElementById('manche3').style.display='block';
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
<form name="form1" method="post" action="admin/ajout_matchew32.php">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="30%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Style 
        : </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="style" type="radio" value="1vs1" checked onClick="return num1(this.form)">
        1vs1 
        <input type="radio" name="style" value="2vs2" onClick="return num2(this.form)">
        2vs2 
        <input type="radio" name="style" value="3vs3" onClick="return num3(this.form)">
        3vs3</font></td>
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
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
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
        </font></td>
    </tr>
    <tr> 
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Joueurs</strong></font></td>
          </tr>
          <tr> 
            <td colspan="2"><div align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                </font> 
                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1a">
                  <tr> 
                    <td> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <select name="gentil1_1" id="select4">
                          <?
				$req = MYSQL_QUERY("SELECT * FROM equipe where war3='oui'");
				while($disp = mysql_fetch_array($req))
				{
				?>
                          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
                          <?
				}
				?>
                        </select>
                        </font></div></td>
                  </tr>
                  <tr> 
                    <td><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong></font></div></td>
                  </tr>
                </table>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2a" style="display:none">
                  <tr> 
                    <td width="299"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <select name="gentil1_2" id="select5">
                          <?
				$req = MYSQL_QUERY("SELECT * FROM equipe where war3='oui'");
				while($disp = mysql_fetch_array($req))
				{
				?>
                          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
                          <?
				}
				?>
                        </select>
                        </font></div></td>
                    <td width="2"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;et&nbsp; 
                        </font></div></td>
                    <td width="299"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <select name="gentil2_2" id="gentil2_2">
                        <?
				$req = MYSQL_QUERY("SELECT * FROM equipe where war3='oui'");
				while($disp = mysql_fetch_array($req))
				{
				?>
                        <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
                        <?
				}
				?>
                      </select>
                      </font></td>
                  </tr>
                  <tr> 
                    <td><div align="right"><font size="1"><strong><font face="Verdana, Arial, Helvetica, sans-serif">J1</font></strong></font></div></td>
                    <td><font size="1">&nbsp;</font></td>
                    <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">J2</font></strong></td>
                  </tr>
                </table>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3a" style="display:none">
                  <tr> 
                    <td width="249"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <select name="gentil1_3" id="select6">
                          <?
				$req = MYSQL_QUERY("SELECT * FROM equipe where war3='oui'");
				while($disp = mysql_fetch_array($req))
				{
				?>
                          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
                          <?
				}
				?>
                        </select>
                        </font></div></td>
                    <td width="1"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;,&nbsp;</font></div></td>
                    <td width="100"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <select name="gentil2_3" id="gentil2_3">
                          <?
				$req = MYSQL_QUERY("SELECT * FROM equipe where war3='oui'");
				while($disp = mysql_fetch_array($req))
				{
				?>
                          <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
                          <?
				}
				?>
                        </select>
                        </font></div></td>
                    <td width="1"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;et&nbsp; 
                        </font></div></td>
                    <td width="249"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <select name="gentil3_3" id="gentil3_3">
                        <?
				$req = MYSQL_QUERY("SELECT * FROM equipe where war3='oui'");
				while($disp = mysql_fetch_array($req))
				{
				?>
                        <option value="<? echo $disp[kinder]; ?>"><? echo $disp[kinder]; ?></option>
                        <?
				}
				?>
                      </select>
                      </font></td>
                  </tr>
                  <tr> 
                    <td><div align="right"><font size="1"><strong><font face="Verdana, Arial, Helvetica, sans-serif">J1</font></strong></font></div></td>
                    <td><font size="1">&nbsp;</font></td>
                    <td><div align="center"><font size="1"><strong><font face="Verdana, Arial, Helvetica, sans-serif">J2</font></strong></font></div></td>
                    <td><font size="1">&nbsp;</font></td>
                    <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">J3</font></strong></td>
                  </tr>
                </table>
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">contre</font> 
                <table width="100%" border="0" cellspacing="0" cellpadding="0" id="champ1b">
                  <tr> 
                    <td id="champ1"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <input name="mechant1_1" type="text" id="mechant1_1">
                        </font></div></td>
                  </tr>
                  <tr> 
                    <td id="champ1"><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong></font></div></td>
                  </tr>
                </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" id="champ2b" style="display:none">
                  <tr> 
                    <td width="299"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <input name="mechant1_2" type="text" id="mechant1_2">
                        </font></div>
                      <div align="center"></div>
                      <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        </font></div></td>
                    <td width="2"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;et&nbsp; 
                        </font></div></td>
                    <td width="299"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <input name="mechant2_2" type="text" id="mechant2_2">
                      </font></td>
                  </tr>
                  <tr> 
                    <td><div align="right"><font size="1"><strong><font face="Verdana, Arial, Helvetica, sans-serif">J1</font></strong></font></div></td>
                    <td><font size="1">&nbsp;</font></td>
                    <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">J2</font></strong></td>
                  </tr>
                </table>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3b" style="display:none">
                  <tr> 
                    <td width="249"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <input name="mechant1_3" type="text" id="mechant1_3">
                        </font></div></td>
                    <td width="1"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;,&nbsp;</font></div></td>
                    <td width="100"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                        <input name="mechant2_3" type="text" id="mechant2_3">
                        </font></div></td>
                    <td width="1"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;et&nbsp; 
                        </font></div></td>
                    <td width="249"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <input name="mechant3_3" type="text" id="mechant3_3">
                      </font></td>
                  </tr>
                  <tr> 
                    <td><div align="right"><font size="1"><strong><font face="Verdana, Arial, Helvetica, sans-serif">J1</font></strong></font></div></td>
                    <td><font size="1">&nbsp;</font></td>
                    <td><div align="center"><font size="1"><strong><font face="Verdana, Arial, Helvetica, sans-serif">J2</font></strong></font></div></td>
                    <td><font size="1">&nbsp;</font></td>
                    <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">J3</font></strong></td>
                  </tr>
                </table>
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> </font> 
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> </font></div></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nombre de 
        manches :</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="manche" value="1" onClick="return man1(this.form)" checked>
        1
        <input type="radio" name="manche" value="2" onClick="return man2(this.form)">
        2 
        <input type="radio" name="manche" value="3" onClick="return man3(this.form)">
        3 </font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"> <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="manche1">
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Manche 
              1</strong></font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Vainqueurs 
              :</font></td>
            <td valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <select name="win1" id="win1">
                <option value="nou">K1der</option>
                <option value="eu">Adversaires</option>
              </select>
              </font></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Race 
              des K1der :</font></td>
            <td valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1c">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m1_j1_1" id="k_m1_j1_1">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2c"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m1_j1_2" id="k_m1_j1_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J2</strong>: 
                    <select name="k_m1_j2_2" id="k_m1_j2_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3c" style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m1_j1_3" id="k_m1_j1_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    , <strong>J2</strong>: 
                    <select name="k_m1_j2_3" id="k_m1_j2_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J3</strong>: 
                    <select name="k_m1_j3_3" id="k_m1_j3_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Race 
              des adversaires :</font></td>
            <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1d">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m1_j1_1" id="m_m1_j1_1">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2d"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m1_j1_2" id="m_m1_j1_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J2</strong>: 
                    <select name="m_m1_j2_2" id="m_m1_j2_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3d"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m1_j1_3" id="m_m1_j1_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    , <strong>J2</strong>: 
                    <select name="m_m1_j2_3" id="m_m1_j2_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J3</strong>: 
                    <select name="m_m1_j3_3" id="m_m1_j3_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
              : </font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="map1" type="text" id="map1">
              </font></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
        </table>
        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="manche2">
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Manche 
              2 </strong></font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Vainqueurs 
              :</font></td>
            <td valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <select name="win2" id="win2">
                <option value="nou">K1der</option>
                <option value="eu">Adversaires</option>
              </select>
              </font></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Race 
              des K1der :</font></td>
            <td valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1e">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m2_j1_1" id="k_m2_j1_1">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2e"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m2_j1_2" id="k_m2_j1_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J2</strong>: 
                    <select name="k_m2_j2_2" id="k_m2_j2_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3e" style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m2_j1_3" id="k_m2_j1_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    , <strong>J2</strong>: 
                    <select name="k_m2_j2_3" id="k_m2_j2_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J3</strong>: 
                    <select name="k_m2_j3_3" id="k_m2_j3_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Race 
              des adversaires :</font></td>
            <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1f">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m2_j1_1" id="m_m2_j1_1">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2f"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m2_j1_2" id="m_m2_j1_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J2</strong>: 
                    <select name="m_m2_j2_2" id="m_m2_j2_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3f"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m2_j1_3" id="m_m2_j1_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    , <strong>J2</strong>: 
                    <select name="m_m2_j2_3" id="m_m2_j2_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J3</strong>: 
                    <select name="m_m2_j3_3" id="m_m2_j3_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
              : </font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="map2" type="text" id="map2">
              </font></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
        </table>
        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" id="manche3">
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Manche 
              3 </strong></font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Vainqueurs 
              :</font></td>
            <td valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <select name="win3" id="win3">
                <option value="nou">K1der</option>
                <option value="eu">Adversaires</option>
              </select>
              </font></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Race 
              des K1der :</font></td>
            <td valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1g">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m3_j1_1" id="k_m3_j1_1">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2g"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m3_j1_2" id="k_m3_j1_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J2</strong>: 
                    <select name="k_m3_j2_2" id="k_m3_j2_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3g" style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="k_m3_j1_3" id="k_m3_j1_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    , <strong>J2</strong>: 
                    <select name="k_m3_j2_3" id="k_m3_j2_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J3</strong>: 
                    <select name="k_m3_j3_3" id="k_m3_j3_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Race 
              des adversaires :</font></td>
            <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ1h">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m3_j1_1" id="m_m3_j1_1">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ2h"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m3_j1_2" id="m_m3_j1_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J2</strong>: 
                    <select name="m_m3_j2_2" id="m_m3_j2_2">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" id="champ3h"  style="display:none">
                <tr> 
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>J1</strong>: 
                    <select name="m_m3_j1_3" id="m_m3_j1_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    , <strong>J2</strong>: 
                    <select name="m_m3_j2_3" id="m_m3_j2_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    et <strong>J3</strong>: 
                    <select name="m_m3_j3_3" id="m_m3_j3_3">
                      <option value="Mort-Vivant">Mort-Vivant</option>
                      <option value="Humain">Humain</option>
                      <option value="Elfe de la Nuit">Elfe de la Nuit</option>
                      <option value="Orc">Orc</option>
                    </select>
                    </font></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
              : </font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="map3" type="text" id="map3">
              </font></td>
          </tr>
          <tr> 
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td height="50" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type 
        : </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td width="70%" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" onclick="this.form.lan.style.display='block';this.form.paslan.style.display='none';" name="type" value="LAN Arena">
        LAN Arena<br>
        <input type="radio" onclick="this.form.paslan.style.display='block';this.form.lan.style.display='none';" name="type" value="Internet">
        Internet<br>
        <input type="radio" onclick="this.form.paslan.style.display='block';this.form.lan.style.display='none';" name="type" value="Jeu en R&eacute;seau">
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
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" name="Submit" value="Envoyer">
  </div>
</form>
