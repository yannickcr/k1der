<? include "secu.php"; ?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$user'");
$disp = mysql_fetch_array($req);
?> 
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
function checkCheckBox(form)
{
// on ouvre la fonction
  if (form.checkcs.checked == false )
// C'est ici que l'on regarde si la case a �t� coch�e.
// Ca se pr�sente sous la forme :
// nom-formulaire.nom-checkbox.coch�e == Ben non
    {
	document.getElementById('cs').style.display='none';
// Si c'est le cas on y dit au visiteur kil f� qu'il coche
// Et on bloque la soummission du formulaire
    }
  else
// Sinon...
    {
	document.getElementById('cs').style.display='block';
    }
// Bon ces 3 lignes ne sont pas obligatoires, mais j'aime bien dire merci.
// on envoie le formulaire
}
// et on ferme la fonction 


// fin du script -->
</script>
<script language="JavaScript" type="text/JavaScript">
function checkCheckBox2(form)
{
// on ouvre la fonction
  if (form.checkwar3.checked == false )
// C'est ici que l'on regarde si la case a �t� coch�e.
// Ca se pr�sente sous la forme :
// nom-formulaire.nom-checkbox.coch�e == Ben non
    {
	document.getElementById('war3').style.display='none';
// Si c'est le cas on y dit au visiteur kil f� qu'il coche
// Et on bloque la soummission du formulaire
    }
  else
// Sinon...
    {
	document.getElementById('war3').style.display='block';
    }
// Bon ces 3 lignes ne sont pas obligatoires, mais j'aime bien dire merci.
// on envoie le formulaire
}
// et on ferme la fonction 


// fin du script -->
</script>

</head>
<body> 
<div align="center">
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr> <td colspan="2" height="16"><table width="600" border="0" cellspacing="0" cellpadding="0" align="left">
          <tr> <td width="439" height="22" valign="baseline"> 
  <div align="right"></div>
  </td><td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td></tr> 
  <tr> <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF"><b>Modification 
  du Joueur 
  <? echo $user; ?>
  </b></font><font color="#FFFFFF">=-</font></b></font></td></tr> <tr> <td colspan="2"></td></tr> 
  </table></td></tr> <tr> <td width="41" height="800"></td><td width="544" height="800"><table border="0" cellpadding="4" cellspacing="0" width="490" height="806"> 
  <tr> <td nowrap height="800" valign="TOP"> 
  <form action="admin/modification2.php3" method="POST" name="form" id="form">
                <div align="CENTER"><b></b></div>
                <br>
                <table width="536" border="0" cellspacing="0" cellpadding="0" bordercolor="#DE0200" bgcolor="#DE0200">
                  <tr> 
                    <td width="60" height="137" valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/joueur.gif"></font></td>
                    <td colspan="3" valign="MIDDLE"><table width="465" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
                        <tr> 
                          <td width="190" valign="MIDDLE"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Nom 
                              :</font></div></td>
                          <td width="10" rowspan="6" valign="MIDDLE"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                            </font></td>
                          <td width="276" valign="MIDDLE"> <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input type="text" name="nom" value="<? echo $disp[nom]; ?>">
                              </font></p></td>
                        </tr>
                        <tr> 
                          <td width="190" valign="MIDDLE"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Pr&eacute;nom 
                              :</font></div></td>
                          <td width="276" valign="MIDDLE"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                            <input type="text" name="prenom" value="<? echo $disp[prenom]; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td width="190" valign="MIDDLE"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                              <?
	  $disp[date] = date2timestamp($disp[age],"Ymd");
	  $disp[jour] = date("d",$disp[date]);
	  $disp[mois] = date("m",$disp[date]);
	  $disp[annee] = date("Y",$disp[date]);
	  ?>
                              </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Date 
                              de naissance :</font></div></td>
                          <td width="276" valign="MIDDLE"><input type="text" name="jour" maxlength="2" size="1" value=<? echo $disp[jour]; ?>> 
                            <select name="mois">
                              <option value="01"
	<?
if ($disp[mois] =="01")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
                              <option value="02"
	<?
if ($disp[mois] =="02")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
                              <option value="03"
	<?
if ($disp[mois] =="03")
	{
		echo " SELECTED";
	}
?>>Mars</option>
                              <option value="04"
	<?
if ($disp[mois] =="04")
	{
		echo " SELECTED";
	}
?>>Avril</option>
                              <option value="05"
	<?
if ($disp[mois] =="05")
	{
		echo " SELECTED";
	}
?>>Mai</option>
                              <option value="06"
	<?
if ($disp[mois] =="06")
	{
		echo " SELECTED";
	}
?>>Juin</option>
                              <option value="07"
	<?
if ($disp[mois] =="07")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
                              <option value="08"
	<?
if ($disp[mois] =="08")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
                              <option value="09"
	<?
if ($disp[mois] =="09")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
                              <option value="10"
	<?
if ($disp[mois] =="10")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
                              <option value="11"
	<?
if ($disp[mois] =="11")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
                              <option value="12"
	<?
if ($disp[mois] =="12")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
                            </select> <input type="text" name="annee" maxlength="4" size="2" value=<? echo $disp[annee]; ?>></td>
                        </tr>
                        <tr> 
                          <td valign="MIDDLE"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">MSN 
                              :</font></div></td>
                          <td valign="MIDDLE"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                            <input name="icq" type="text" id="icq" value="<? echo $disp[icq] ; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td width="190" valign="MIDDLE"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">E-Mail 
                              :</font></div></td>
                          <td width="276" valign="MIDDLE"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                            <input type="text" name="e_mail" value="<? echo $disp[e_mail]; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td width="190" valign="MIDDLE"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Ville 
                              :</font></div></td>
                          <td width="276" valign="MIDDLE"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                            <input type="text" name="ville" value="<? echo $disp[ville]; ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td valign="MIDDLE"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">R&ocirc;le 
                              :</font></div></td>
                          <td valign="MIDDLE">&nbsp;</td>
                          <td valign="MIDDLE"><input name="role" type="text" id="role" value="<? echo $disp[role]; ?>"></td>
                        </tr>
                        <tr> 
                          <td valign="MIDDLE"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Statut 
                              :</font></div></td>
                          <td valign="MIDDLE">&nbsp;</td>
                          <td valign="MIDDLE"><select name="statu" id="statu">
                              <option value="Actif" <? if ($disp[statu] == "Actif") { echo " SELECTED"; } ?>>Actif</option>
                              <option value="Mort" <? if ($disp[statu] == "Mort") { echo " SELECTED"; } ?>>Mort</option>
                            </select></td>
                        </tr>
                      </table></td>
                    <td width="10" height="137" valign="top"><img src="images/littlehautdroite.gif" width="10" height="10"></td>
                  </tr>
                  <tr> 
                    <td colspan="5" valign="middle" height="4"><img src="images/ligne.gif" width="536" height="5"></td>
                  </tr>
                  <tr> 
                    <td width="60" valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/connection.gif"><br>
                      </font></td>
                    <td colspan="3" valign="top"><table width="465" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
                        <tr> 
                          <td width="190" valign="top"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Connection 
                              :</font></div></td>
                          <td width="10" rowspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                            </font></td>
                          <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                            <input type="text" name="conn_type" value="<? echo $disp[conn_type] ?>">
                            </font></td>
                        </tr>
                        <tr> 
                          <td valign="top"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">FAI 
                              :</font></div></td>
                          <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                            <input name="conn_fai" type="text" id="conn_fai" value="<? echo $disp[conn_fai] ?>">
                            </font></td>
                        </tr>
                      </table></td>
                    <td valign="middle">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td colspan="5" height="2"><img src="images/ligne.gif" width="536" height="5"></td>
                  </tr>
                  <tr> 
                    <td width="60" height="10" valign="middle"> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/hardware.gif"></font></div></td>
                    <td height="10" colspan="3" valign="TOP"><div align="right"> 
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Processeur 
                                :</font></div></td>
                            <td width="10" rowspan="9" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                              </font></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="proc" type="text" id="proc9" value="<? echo $disp[proc] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                                Graphique :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="graph" type="text" id="graph2" value="<? echo $disp[graph] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Ram 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="ram" type="text" id="ram" value="<? echo $disp[ram] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                                Son :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="son" type="text" id="son" value="<? echo $disp[son] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                                M&egrave;re :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="mere" type="text" id="mere" value="<? echo $disp[mere] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Souris 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="souris" type="text" id="souris" value="<? echo $disp[souris] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Clavier 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="clavier" type="text" id="clavier" value="<? echo $disp[clavier] ?>">
                              </font></td>
                          </tr>
                          <tr>
                            <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Ecran : </font></div></td>
                            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                              <input name="ecran" type="text" id="ecran" value="<? echo $disp[ecran] ?>">
                            </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Tapis 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="tapis" type="text" id="tapis" value="<? echo $disp[tapis] ?>">
                              </font></td>
                          </tr>
                        </table>
                      </div></td>
                    <td valign="middle" height="10">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td colspan="5" height="2"><img src="images/ligne.gif" width="536" height="5"></td>
                  </tr>
                  <tr> 
                    <td valign="middle">&nbsp;</td>
                    <td colspan="3" valign="TOP">&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td valign="middle">&nbsp;</td>
                    <td colspan="3" valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Sections 
                      : 
                      <input type="checkbox" value="0" name="checkcs" onClick="return checkCheckBox(this.form)">
                      Counter-Strike 
                      <input type="checkbox" value="0" name="checkwar3" onClick="return checkCheckBox2(this.form)">
                      Warcraft III</font></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td width="60" valign="middle"> <div align="left"></div></td>
                    <td colspan="3" valign="TOP"> <div align="right"> 
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200" style="display:none;" title="cs" id="cs" name="cs">
                          <tr> 
                            <td valign="top">&nbsp;</td>
                            <td width="10" rowspan="7" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                              </font></td>
                            <td valign="top">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Section 
                              Counter-Strike</strong></font></td>
                            <td valign="top">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td valign="top">&nbsp;</td>
                            <td valign="top">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Arme 
                                pr&eacute;f&eacute;r&eacute;e :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <select name="armecs" id="select2">
                                <option value="Couteau">Couteau</option>
                                <option value="HE Grenade">HE Grenade</option>
                                <option value="H&amp;K USP .45 Tactical">H&amp;K 
                                USP .45 Tactical</option>
                                <option value="Glock 18 Select Fire">Glock 18 
                                Select Fire</option>
                                <option value="Desert Eagle .50 AE">Desert Eagle 
                                .50 AE</option>
                                <option value="SIG P228">SIG P228</option>
                                <option value="Dual Beretta 96G">Dual Beretta 
                                96G</option>
                                <option value="FN Five-Seven">FN Five-Seven</option>
                                <option value="Benneli M3 Super90">Benneli M3 
                                Super90</option>
                                <option value="Benneli XM1014">Benneli XM1014</option>
                                <option value="H&amp;K MP5-Navy">H&amp;K MP5-Navy</option>
                                <option value="Steyr Tactical">Steyr Tactical</option>
                                <option value="FN P90">FN P90</option>
                                <option value="Ingram MAC-10">Ingram MAC-10</option>
                                <option value="H&amp;K UMP">H&amp;K UMP</option>
                                <option value="AK-47">AK-47</option>
                                <option value="Sig SG-552 Commando">Sig SG-552 
                                Commando</option>
                                <option value="Colt M4A1 Carbine">Colt M4A1 Carbine</option>
                                <option value="Steyr AUG">Steyr AUG</option>
                                <option value="Steyr Scout">Steyr Scout</option>
                                <option value="AI Arctic Warfare/Magnum">AI Arctic 
                                Warfare/Magnum</option>
                                <option value="H&amp;K G3/SG-1">H&amp;K G3/SG-1</option>
                                <option value="Sig SG-550 Sniper">Sig SG-550 Sniper</option>
                                <option value="FN M249 Para">FN M249 Para</option>
                              </select>
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" height="26" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                                Carte Pr&eacute;f&eacute;r&eacute;e :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <select name="mapcs" id="select3">
                                <option value="as_oilrig" <? if ($disp[map] == "as_oilrig") { echo " SELECTED"; } ?> >as_oilrig</option>
                                <option value="as_tundra" <? if ($disp[map] == "as_tundra") { echo " SELECTED"; } ?> >as_tundra</option>
                                <option value="cs_747" <? if ($disp[map] == "cs_747") { echo " SELECTED"; } ?> >cs_747</option>
                                <option value="cs_assault" <? if ($disp[map] == "cs_assault") { echo " SELECTED"; } ?> >cs_assault</option>
                                <option value="cs_backalley" <? if ($disp[map] == "cs_backalley") { echo " SELECTED"; } ?> >cs_backalley</option>
                                <option value="cs_estate" <? if ($disp[map] == "cs_estate") { echo " SELECTED"; } ?> >cs_estate</option>
                                <option value="cs_havana" <? if ($disp[map] == "cs_havana") { echo " SELECTED"; } ?> >cs_havana</option>
                                <option value="cs_italy" <? if ($disp[map] == "cs_italy") { echo " SELECTED"; } ?> >cs_italy</option>
                                <option value="cs_militia" <? if ($disp[map] == "cs_militia") { echo " SELECTED"; } ?> >cs_militia</option>
                                <option value="cs_office" <? if ($disp[map] == "cs_office") { echo " SELECTED"; } ?> >cs_office</option>
                                <option value="cs_siege" <? if ($disp[map] == "cs_siege") { echo " SELECTED"; } ?> >cs_siege</option>
                                <option value="de_aztec" <? if ($disp[map] == "de_aztec") { echo " SELECTED"; } ?> >de_aztec</option>
                                <option value="de_cbble" <? if ($disp[map] == "de_cbble") { echo " SELECTED"; } ?> >de_cbble</option>
                                <option value="de_chateau" <? if ($disp[map] == "de_chateau") { echo " SELECTED"; } ?> >de_chateau</option>
                                <option value="de_dust" <? if ($disp[map] == "de_dust") { echo " SELECTED"; } ?> >de_dust</option>
                                <option value="de_dust2" <? if ($disp[map] == "de_dust2") { echo " SELECTED"; } ?> >de_dust2</option>
                                <option value="de_inferno" <? if ($disp[map] == "de_inferno") { echo " SELECTED"; } ?> >de_inferno</option>
                                <option value="de_nuke" <? if ($disp[map] == "de_nuke") { echo " SELECTED"; } ?> >de_nuke</option>
                                <option value="de_piranesi" <? if ($disp[map] == "de_piranesig") { echo " SELECTED"; } ?> >de_piranesi</option>
                                <option value="de_prodigy" <? if ($disp[map] == "de_prodigy") { echo " SELECTED"; } ?> >de_prodigy</option>
                                <option value="de_storm" <? if ($disp[map] == "de_storm") { echo " SELECTED"; } ?> >de_storm</option>
                                <option value="de_survivor" <? if ($disp[map] == "de_survivor") { echo " SELECTED"; } ?> >de_survivor</option>
                                <option value="de_torn" <? if ($disp[map] == "de_torn") { echo " SELECTED"; } ?> >de_torn</option>
                                <option value="de_train" <? if ($disp[map] == "de_train") { echo " SELECTED"; } ?> >de_train</option>
                                <option value="de_vegas" <? if ($disp[map] == "de_vegas") { echo " SELECTED"; } ?> >de_vegas</option>
                                <option value="de_vertigo" <? if ($disp[map] == "de_vertigo") { echo " SELECTED"; } ?> >de_vertigo</option>
                              </select>
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;solution 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="resocs" type="text" id="reso12" value="<? echo $disp[resocs] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Sensitivity 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="senscs" type="text" id="sens2" value="<? echo $disp[senscs] ?>">
                              </font></td>
                          </tr>
                        </table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200" style="display:none;" title="war3" id="war3" name="war3">
                          <tr> 
                            <td valign="top">&nbsp;</td>
                            <td width="10" rowspan="7" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                              </font></td>
                            <td valign="top">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Section 
                              Warcraft III</strong></font></td>
                            <td valign="top">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td valign="top">&nbsp;</td>
                            <td valign="top">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Hero</font><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                                pr&eacute;f&eacute;r&eacute; :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <select name="herow3" id="select4">
                                <option value="Paladin">Paladin</option>
                                <option value="Archmage">Archmage</option>
                                <option value="Mountain King">Mountain King</option>
                                <option value="Demon Hunter">Demon Hunter</option>
                                <option value="Keeper of the Grove">Keeper of 
                                the Grove</option>
                                <option value="Priestess of the Moon">Priestess 
                                of the Moon</option>
                                <option value="Blade Master">Blade Master</option>
                                <option value="Farseer">Farseer</option>
                                <option value="Tauren Chieftain">Tauren Chieftain</option>
                                <option value="Death Knight">Death Knight</option>
                                <option value="Dread Lord">Dread Lord</option>
                                <option value="Lich">Lich</option>
                              </select>
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> 
                              <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                                Carte Pr&eacute;f&eacute;r&eacute;e :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><input name="mapw3" type="text" id="mapw3" value="<? echo $disp[mapw3] ?>" size="30"></font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> 
                              <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;solution 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="resow3" type="text" id="reso13" value="<? echo $disp[resow3] ?>">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"> <div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">URL 
                                du compte BattleNet:</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="urlw3" type="text" id="sens3" value="<? echo $disp[urlw3] ?>" size="30">
                              </font></td>
                          </tr>
                        </table>
                      </div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td colspan="5"><img src="images/ligne.gif" width="536" height="5"></td>
                  </tr>
                  <tr> 
                    <td width="60" valign="middle"> <div align="left"><img src="images/os.gif"></div></td>
                    <td colspan="3" valign="TOP"> <div align="RIGHT"> 
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
                          <tr> 
                            <td width="190" valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Syst&egrave;me 
                              d'exploitation :</font></td>
                            <td width="10" rowspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                              </font></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="os" type="text" id="os" value="<? echo $disp[os] ?>" size="30">
                              </font></td>
                          </tr>
                          <tr> 
                            <td width="190" valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;solution 
                                :</font></div></td>
                            <td width="276" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                              <input name="reso" type="text" id="reso" value="<? echo $disp[reso] ?>">
                              </font></td>
                          </tr>
                        </table>
                      </div>
                      <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                        </font></div>
                      <div align="RIGHT"></div></td>
                    <td valign="bottom">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td width="60" valign="bottom"><img src="images/littlebasgauche.gif" width="10" height="10"></td>
                    <td width="465">&nbsp;</td>
                    <td width="276" colspan="2">&nbsp;</td>
                    <td valign="bottom"><img src="images/littlebasdroite.gif" width="10" height="10"></td>
                  </tr>
                </table>
    <div align="center"> <input type="submit" name="envoi" value="Valider les modifications ..." style="width: 200px"> 
    </div>
  </form>
  </td></tr></table></td></tr> </table>
</div>