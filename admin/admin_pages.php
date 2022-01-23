<? $level = '10'; include "secu.php";

include "config.inc.php3";
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

//Monter une catégorie

if (($cat != '') && ($ordre3 != ''))
{
$modif = $ordre3-1;
mysql_query("UPDATE admin_cat SET ordre='$ordre3' WHERE ordre='$modif'");
mysql_query("UPDATE admin_cat SET ordre='$modif' WHERE id='$cat'");
}

//Descendre une catégorie

if (($cat != '') && ($ordre4 != ''))
{
$modif = $ordre4+1;
mysql_query("UPDATE admin_cat SET ordre='$ordre4' WHERE ordre='$modif'");
mysql_query("UPDATE admin_cat SET ordre='$modif' WHERE id='$cat'");
}

//Monter une page

if (($cat != '') && ($ordre != '') && ($id != ''))
{
$modif = $ordre-1;
mysql_query("UPDATE admin SET ordre='$ordre' WHERE ordre='$modif' && cat_id='$cat'");
mysql_query("UPDATE admin SET ordre='$modif' WHERE id='$id' && cat_id='$cat'");
}

//Descendre une page

if (($cat != '') && ($ordre2 != '') && ($id != ''))
{
$modif = $ordre2+1;
mysql_query("UPDATE admin SET ordre='$ordre2' WHERE ordre='$modif' && cat_id='$cat'");
mysql_query("UPDATE admin SET ordre='$modif' WHERE id='$id' && cat_id='$cat'");
}

//Ajouter une page

if (($action == 'ajouter_page') && ($id != ''))
{
$req = MYSQL_QUERY("SELECT * FROM admin WHERE cat_id='$id'");
$nbre = mysql_num_rows($req);
$nbre = $nbre+1;
mysql_query("INSERT INTO admin VALUES('','$id','Sans Titre','','1','$nbre')");
}

//Ajouter une catégorie

if ($action == 'ajouter_cat')
{
$req = MYSQL_QUERY("SELECT * FROM admin_cat");
$nbre = mysql_num_rows($req);
$nbre = $nbre+1;
mysql_query("INSERT INTO admin_cat VALUES('','Sans Titre','$nbre')");
}

//Supprimer une page

if (($cat != '') && ($action == 'supprimer') && ($id != ''))
{
mysql_query("DELETE FROM admin WHERE id='$id' && cat_id='$cat'");

//$ALERT = "La page a été effacé avec succès !";

$req = MYSQL_QUERY("SELECT * FROM admin WHERE cat_id='$cat' ORDER BY ordre DESC");
$nbr = MYSQL_NUM_ROWS($req);
while ($disp = mysql_fetch_array($req))
{
mysql_query("UPDATE admin SET ordre='$nbr' WHERE id='$disp[id]'");
$nbr--;
}
}

//Supprimer une catégorie

if (($action == 'supprimer_cat') && ($id != ''))
{
mysql_query("DELETE FROM admin_cat WHERE id='$id'");
mysql_query("DELETE FROM admin WHERE cat_id='$id'");

//$ALERT = "La catégorie a été effacé avec succès !";

$req = MYSQL_QUERY("SELECT * FROM admin_cat ORDER BY ordre DESC");
$nbr = MYSQL_NUM_ROWS($req);
while ($disp = mysql_fetch_array($req))
{
mysql_query("UPDATE admin_cat SET ordre='$nbr' WHERE id='$disp[id]'");
$nbr--;
}
}

?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
        </tr>
        <tr> 
          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
            Modifier les pages et cat&eacute;gories de l'admin=-</font></b></font></td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=admin_pages&action=ajouter_cat">Ajouter 
      une cat&eacute;gorie</a></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <?
  $raquete  = "SELECT * FROM admin_cat ORDER BY ordre";
  $raq = mysql_query($raquete) or die('Erreur SQL !<br>'.$raquete.'<br>'.mysql_error());
  while($dasp = mysql_fetch_array($raq))
  {
  ?>
  <tr> 
    <td width="300"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><em><strong><font size="2" face="Arial, Helvetica, sans-serif"><? echo $dasp[nom]; ?></font></strong></em></font></td>
    <td width="300" valign="middle" nowrap> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=admin_pages&id=<? echo $dasp[id]; ?>&action=supprimer_cat">Supprimer</a> 
      | <a href="index.php?page=modif_cat_admin&id=<? echo $dasp[id]; ?>">Renommer</a> 
      | </font><a href="index.php?page=admin_pages&ordre3=<? echo $dasp[ordre]; ?>&cat=<? echo $dasp[id]; ?>"><img src="images/fleche_haut.gif" width="15" height="15" border="0" align="middle"></a><a href="index.php?page=admin_pages&ordre4=<? echo $dasp[ordre]; ?>&cat=<? echo $dasp[id]; ?>"><img src="images/fleche_bas.gif" width="15" height="15" border="0" align="middle"></a> 
    </td>
  </tr>
  <tr> 
    <td colspan="2"><em><strong></strong></em> <table width=100% border=0 cellpadding="0" cellspacing="0">
        <?
	  $requete  = "SELECT * FROM admin WHERE cat_id='$dasp[id]' ORDER BY ordre";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	  while($disp = mysql_fetch_array($req))
	  {
	  ?>
        <tr> 
          <TD width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color=#cc0000>&nbsp;<? echo $disp[texte]; ?></font></TD>
          <TD width="300" valign="middle"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=admin_pages&id=<? echo $disp[id]; ?>&action=supprimer&cat=<? echo $dasp[id]; ?>">Supprimer</a> 
            | <a href="index.php?page=modif_page_admin&id=<? echo $disp[id]; ?>">Modifier</a> 
            | <a href="index.php?page=admin_pages&id=<? echo $disp[id]; ?>&ordre=<? echo $disp[ordre]; ?>&cat=<? echo $dasp[id]; ?>"><img src="images/fleche_haut.gif" width="15" height="15" border="0" align="middle"></a><a href="index.php?page=admin_pages&id=<? echo $disp[id]; ?>&ordre2=<? echo $disp[ordre]; ?>&cat=<? echo $dasp[id]; ?>"><img src="images/fleche_bas.gif" width="15" height="15" align="middle" border="0"></a> 
            </font> </TD>
        </tr>
        <?
	  }
	  ?>
        <tr> 
          <td colspan="2" height="5"></td>
        </tr>
        <tr> 
          <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=admin_pages&id=<? echo $dasp[id]; ?>&action=ajouter_page">Ajouter</a></font></td>
        </tr>
      </table>
      <font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
    </td>
  </tr>
  <?
  }
  ?>
</table>
<form>
  <div align="center"> 
    <input name="button" type="button" onClick="Javascript:window.location='index.php?page=admin';" value="Retour à la page d'administration">
  </div>
</form>
