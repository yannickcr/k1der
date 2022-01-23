<? include("secu.php"); ?><?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM recrutement WHERE id='$id'");
$disp = mysql_fetch_array($req);
mysql_query("UPDATE recrutement SET lu='1' WHERE id='$disp[id]'");
?>
<script language="JavaScript" type="text/JavaScript">
function checkCheckBox(form)
{
	document.getElementById('person').style.display='block';
}

function checkCheckBox2(form)
{
	document.getElementById('person').style.display='none';
}
</script>
<table width="550" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
          <td>
<table width="550" border="0" cellspacing="0" cellpadding="0" align="left">
        <tr> 
                <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
              </tr>
              <tr> 
                <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=</font><font class="m10" color="#FFFFFF">Détails 
                  de la demande</font><font color="#FFFFFF">=-</font></b></font></td>
              </tr>
              <tr> 
                <td colspan="2">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          
    <td>
<table width="550" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date:</font></td>
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <? $recr_date=date("d/m/Y à h:m",$disp[date]); echo "le $recr_date"; ?>
            </font></td>
        </tr>
        <tr> 
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Son 
            ip:</font></td>
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[ip]; ?></font></td>
        </tr>
        <tr> 
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pseudo:</font></td>
          <td width="370"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[pseudo]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom: 
            </font></td>
          <td width="370"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[nom]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pr&eacute;nom:</font></td>
          <td width="370"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[prenom]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Sexe:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo ucfirst($disp[sexe]); ?></font></td>
        </tr>
        <?
				  if ($disp[sexe] == 'f')
				  {
				  ?>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mensurations:</font></td>
          <td width="370"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[mens]; ?></font></td>
        </tr>
        <?
				  }
				  ?>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Age:</font></td>
          <td width="370"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo str_replace("an","",str_replace("ans","",str_replace(" ","",$disp[age]))); ?> 
            ans</font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">ICQ 
            / AIM / MSN: </font></td>
          <td width="370"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[icq]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail 
            : </font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="mailto:<? echo $disp[mail]; ?>"><? echo $disp[mail]; ?></a></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ville:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[ville]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Connection:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[connection]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Exp&eacute;rience:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[xp]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Disponibilit&eacute;:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[dispo]; ?></font></td>
        </tr>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Section:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
					if ($disp[section] == "cs")
					{
					echo "Counter-Strike";
					}
					else
					{
					echo "Warcraft III";
					}
					?>
            </font></td>
        </tr>
        <?
					if ($disp[section] == "cs")
					{
					?>
        <tr> 
          <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Style 
            de jeu:</font></td>
          <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[style]; ?></font></td>
        </tr> <?
				  }
				  else
				  {
				  ?>
        <tr> 
          <td colspan="2"> 
           
            <table width="550" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Level 
                  :</font></td>
                <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[level]; ?></font></td>
              </tr>
              <tr> 
                <td width="180"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Url 
                  du compte Battlenet:</font></td>
                <td width="370"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="<? echo $disp[battlenet]; ?>"><? echo $disp[battlenet]; ?></a></font></td>
              </tr>

            </table></td>
        </tr>
		              <?
						}
						?>
        <tr> 
          <td>&nbsp;</td>
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        </tr>
        <tr> 
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lettre 
            de motivation :</font></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[lettre]; ?></font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Commentaires:</font></td>
        </tr>
        <?
				  $raq = MYSQL_QUERY("SELECT * FROM recrut_comm WHERE id_recrut='$disp[id]'");
				  while($dasp = mysql_fetch_array($raq))
				  {
				  if ($dasp[comment] != '')
				  {
$dasp[comment] = str_replace("
","<br>",$dasp[comment]);
                  ?>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo "<b>$dasp[nom]</b>: $dasp[comment]"; ?></font></td>
        </tr>
        <? }
				  }
				  ?>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Actions:</strong></font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="admin/suppr_recrut.php?id=<? echo $disp[id]; ?>">Supprimer</a></font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lui 
            Envoyer un mail: 
            <?
					  $raq = MYSQL_QUERY("SELECT * FROM recrut_comm WHERE id_recrut='$disp[id]'");
				      while($dasp = mysql_fetch_array($raq))
					  {
					  if ($dasp[mail] != '')
					  {
					  $dej .= "<b>$dasp[nom]</b> ($dasp[mail]) ";
					  }
					  }
					  if ($dej != '')
					  {
					  $dej = "<font size=1>Déjà envoyé par: $dej</font>";
					  }
					  echo $dej;
					  ?>
            </font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"> <form name="form1" method="post" action="admin/envoi_mail_recrut.php">
              <table width="550" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="180" valign="top">&nbsp;</td>
                  <td width="400" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <input name="type" type="radio" onClick="return checkCheckBox2(this.form)" value="mechant" checked>
                    Rejeter m&eacute;chament (envoi chier) 
                    <input name="mail" type="hidden" id="mail3" value="<? echo $disp[mail]; ?>">
                    <br>
                    <input type="radio" name="type" onClick="return checkCheckBox2(this.form)" value="gentil">
                    Rejeter gentiment | Raison : 
                    <input name="raison" type="text" id="raison3">
                    <input name="nom" type="hidden" id="nom4" value="<? echo ucfirst($HTTP_COOKIE_VARS[gen]); ?>">
                    <input name="id" type="hidden" id="id4" value="<? echo $disp[id]; ?>">
                    <br>
                    <input type="radio" name="type" onClick="return checkCheckBox2(this.form)" value="chan">
                    Demander de passer sur le chan<br>
                    <input type="radio" name="type" onClick="return checkCheckBox(this.form)" value="perso">
                    Personnalis&eacute;</font></td>
                </tr>
                <tr id="person" style="display:none"> 
                  <td colspan="2" valign="top"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <textarea name="message" rows="10" id="textarea3" style="width=550px"></textarea>
                      </font></div></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top"><div align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <input type="submit" name="Submit" value="Envoyer">
                      </font></div></td>
                </tr>
              </table>
            </form></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Laisser 
            un commentaire:</font></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"> <form name="form2" method="post" action="admin/ajout_comm_recrut.php">
              <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="nom" type="hidden" id="nom5" value="<? echo ucfirst($HTTP_COOKIE_VARS[gen]); ?>">
                <input name="id" type="hidden" id="id5" value="<? echo $disp[id]; ?>">
                <textarea name="comment" rows="3" id="textarea4" style="width=550px"></textarea>
                <br>
                <input type="submit" name="Submit2" value="Valider">
                </font> </div>
            </form></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"><form>
              <div align="center"><br>
                <br>
                <input name="button" type="button" onClick="Javascript:window.location='index.php?page=admin';" value="<< Retour à l'index">
              </div>
            </form></td>
        </tr>
      </table> </td>
        </tr>
      </table>
