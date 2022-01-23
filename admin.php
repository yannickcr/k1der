<? 
if ($pymembs)
{
$login=$pymembs;
session_start();
session_register('login');
}
else { session_start(); }
include("config.inc.php3");
if(!session_is_registered('login'))
{
?>

<script language="JavaScript">
<!--

function Noprob(){

  with(document.logi){

    if( login.value == '' ){
      alert("Vous n'avez pas rentré de login");
      return false;
    }
    if( pass.value == '' ){
      alert("Vous n'avez pas rentré de mot de passe");
      return false;
    }
  
  }

  return true;
}

//-->
</script>
<form name="logi" method="post" action="login.php" onSubmit="return Noprob()">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
      <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
    </tr>
    <tr> 
      <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Identification=-</font></b></font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
  <p align="center"><font size="5" face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?
	if ($vautrage == '1')
	{
	?>
    </strong></font><font color="#FF0000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
    Tu t&eacute; vautr&eacute; comme une merde,<br>
    Recommence !!!</strong></font></p>
	<?
	}
	?>
  <table width="600" border=0 align="center" cellpadding=2 cellspacing=1>
    <tr> 
      <td> <div align="right"><b><font 
                        face="Arial, Helvetica, sans-serif" size=2>Login :</font></b></div></td>
      <td> 
        <div align=center> 
          <input class="imput" name=login>
        </div>
      </td>
    </tr>
    <tr> 
      <td> <div align="right"><b><font 
                        face="Arial, Helvetica, sans-serif" size=2>Mot de Passe 
          :</font></b></div></td>
      <td> 
        <div align=center> 
          <input class="imput" type=password name=pass>
        </div>
      </td>
    </tr>
    <tr> 
      <td colspan=2> 
        <div align=center> 
          <p>
            <input type="submit" name="Submit" value="Envoyer !">
          </p>
          <p><a href="index.php?page=je_suis_trop_con"><font size="2">Mot de passe 
            perdu ?</font></a></p>
        </div>
      </td>
    </tr>
  </table>
</form>

<?
}
else
{
$user = ucwords($pymembs);

$requete  = "SELECT * FROM equipe WHERE kinder = '$user'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

if ($disp[next_match] == '')
{
$requete  = "SELECT * FROM next_matches ORDER BY date";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);

if ($nbre != 0)
{
 $jour=date("l",$disp[date]); 
  $mois=date("m",$disp[date]); 
  $liste_jour_fr=array( 
  "Monday"=>"Lundi " , 
  "Tuesday"=>"Mardi" , 
  "Wednesday"=>"Mercredi" , 
  "Thursday"=>"Jeudi" , 
  "Friday"=>"Vendredi" , 
  "Saturday"=>"Samedi" , 
  "Sunday"=>"Dimanche" 
  ); 
  
  $liste_mois_fr=array( 
  "01 "=>"janvier" , 
  "02" =>"février" , 
  "03" =>"mars" , 
  "04" =>"avril" , 
  "05" =>"mai" , 
  "06" =>"juin" , 
  "07" =>"juillet" , 
  "08" =>"août" , 
  "09" =>"septembre" , 
  "10" =>"octobre" , 
  "11" =>"novembre" , 
  "12" =>"décembre" 
  ); 
  
  foreach($liste_jour_fr as $j_en => $j_fr ) 
  { 
  if($jour==$j_en) 
  { 
  $datefre .= $j_fr; 
  } 
  } 
  
  $datefre .= date(" d",$disp[date]); 
  
  foreach($liste_mois_fr as $m_en => $m_fr ) 
  { 
  if($mois==$m_en) 
  { 
  $datefre .= " $m_fr"; 
  } 
  } 
  //$datefren .= date(" Y");
  //return $datefren; 
						  
						  //$dute = date_en_francais();
						  $dute = $datefre;

?>
<script language="JavaScript">
if(confirm("Un match est prévu contre les <? echo $disp[mechants]; ?> le <? echo $dute; ?> à <? echo $disp[heure]; ?>\n\n                               Peut-tu y participer ?"))
{
window.location='administ/true.php';
}
else
{
window.location='administ/false.php';
}
</script>
<?
}
}
?>
<script language="JavaScript">
<!--

function scree(){

      alert("Pour changer de Screen:\n - Connectez vous au server FTP de votre hébergeur avec un logiciel FTP ( ex: CuteFTP )\n - Allez dans le dossier images/screen \n - Uploadez-y l'images en taille réel (ne touchez pas aux images existantes !). \n La petite sera crée automatiquement.");
}

//-->
</script>
<script language="JavaScript">
<!--

function fond(){

      alert("Pour changer de Fond:\n - Connectez vous au server FTP de votre hébergeur avec un logiciel FTP ( ex: CuteFTP )\n - Allez dans le dossier images/fond \n - Uploadez-y l'images en taille réel (ne touchez pas aux images existantes !). \n La petite sera crée automatiquement.");
}

//-->
</script>
<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td colspan="2"> <table width="600" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
          <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
        </tr>
        <tr> 
          <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Administration 
            du site=-</font></b></font></td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp; </td>
    <td> 
      <?
$user = "-=K1der=- $user";

echo "<p><b><font face='Verdana, Arial, Helvetica, sans-serif' size='2'>Salut ".stripslashes($user)."</b></p>";

?>
    </td>
  </tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475">&nbsp;</td>
  </tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475"><font face="Arial, Helvetica, sans-serif"><b><i>Actualit&eacute;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(</i></b></font><font face="Arial, Helvetica, sans-serif" size="2"><font size="1"> 
      </font></font><font face="Arial, Helvetica, sans-serif"><em> <font size="1"><strong> 
      <?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT id FROM $TBL_NEWS");
$res = MYSQL_NUM_ROWS($req);

if($res==0){ $res = "Il n'y a pas de"; }
else{ $res = "Il y a $res"; }

$date = date("d/m/Y H:i");

if($m=='1'){ $NEWSADD = "<center><font color=\"red\" class=\"m9\">L'information a été ajoutée avec succès !</font></center><br><br>"; }
else{ $NEWSADD = ""; }
?>
      <? echo $NEWSADD; ?> <font class="m9"> <? echo $res; ?> news enregistrées 
      dans la base</font></strong></font></em><font face="Arial, Helvetica, sans-serif"><font face="Verdana, Arial, Helvetica, sans-serif"><font class="m9"><strong><em>)</em></strong></font></font></font></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=conf_news">Confirmer 
      une News</a> <font size="1">(<font color="#CC0000"> </font>News en attente 
      :<font color="#CC0000"> 
      <?
	  $requete  = "SELECT * FROM mynewsinfos WHERE conf = '0'";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $nbre =mysql_num_rows($req);
	  $disp = mysql_fetch_array($req);
	  echo "<b> $nbre </b>";
	  ?>
      </font>)</font></font></td>
  </tr>
  <tr> 
    <td width="25"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font></td>
    <td width="475"><font face="Arial, Helvetica, sans-serif" size="2">- <a href="index.php?page=ajoutnews">Ajouter 
      des News</a> </font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font face="Arial, Helvetica, sans-serif" size="2">- <a href="index.php?page=modiflistnews">Modifier/Supprimer 
      des News</a></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font face="Arial, Helvetica, sans-serif" size="2">- <a href="index.php?page=del_comment">Supprimer 
      des Commentaires</a> </font></tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475">&nbsp;</tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475"><font face="Arial, Helvetica, sans-serif"><b><i>LAN Party&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(</i></b></font><font face="Arial, Helvetica, sans-serif" size="2"><font size="1"> 
      </font></font><font face="Arial, Helvetica, sans-serif"><em> <font size="1"><strong> 
      <?

$req = MYSQL_QUERY("SELECT id FROM calendrier");
$res = MYSQL_NUM_ROWS($req);

if($res==0){ $res = "Il n'y a pas de"; }
else{ $res = "Il y a $res"; }

$date = date("d/m/Y H:i");

if($m=='1'){ $NEWSADD = "<center><font color=\"red\" class=\"m9\">L'information a été ajoutée avec succès !</font></center><br><br>"; }
else{ $NEWSADD = ""; }
?>
      <? echo $NEWSADD; ?> <font class="m9"> <? echo $res; ?> LANs enregistrées 
      dans la base</font></strong></font></em><font face="Arial, Helvetica, sans-serif"><font face="Verdana, Arial, Helvetica, sans-serif"><font class="m9"><strong><em>)</em></strong></font></font></font></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=conf_lan">Confirmer 
      une LAN</a> <font size="1">(<font color="#CC0000"> </font>LANs en attente 
      :<font color="#CC0000"> 
      <?
	  $requete  = "SELECT * FROM calendrier WHERE conf = '0'";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $nbre =mysql_num_rows($req);
	  $disp = mysql_fetch_array($req);
	  echo "<b> $nbre </b>";
	  ?>
      </font>)</font></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=ajout_lan">Ajouter 
        une LAN</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=lan_liste&action=modif">Modifier une 
        LAN</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=lan_liste&action=suppr">Supprimer 
        une LAN</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font face="Arial, Helvetica, sans-serif">- <font size="2"> <a href="index.php?page=modiflanstatut">Changer 
      son statut pour la prochaine LAN Party</a></font></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td><b><i><font face="Arial, Helvetica, sans-serif">Evènement</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=ajout_event">Ajouter 
      un &eacute;venement</a></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=event_liste&action=modif">Modifier 
      un &eacute;venement</a></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=event_liste&action=suppr">Supprimer 
      un &eacute;venement</a></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td><b><i><font face="Arial, Helvetica, sans-serif">Dossiers</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=ger_dossiers">G&eacute;rer 
      les Dossiers</a></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Matches</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=ajout_matche">Ajouter 
        un Matche</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=matches_liste&action=modif">Modifier 
        un Matche</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=matches_liste&action=suppr">Supprimer 
        un Matche</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Prochains 
      Matches</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=ajout_next_matche">Ajouter 
        un Matche</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=next_matches_liste&action=modif">Modifier 
        un Matche</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=next_matches_liste&action=suppr">Supprimer 
        un Matche</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Joueur</font></i></b></tr>
  <tr> 
    <td width="25"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font></td>
    <td width="475"><font face="Arial, Helvetica, sans-serif" size="2">- <a href="index.php?page=modifplayer">Modifier 
      mes Caract&eacute;ristiques</a></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=config_upload">Uploader 
      ma config</a></font></td>
  </tr>
  <tr> 
    <td width="25"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font></td>
    <td width="475"><font face="Arial, Helvetica, sans-serif" size="2">- <a href="index.php?page=modifpass">Modifier 
      mon Mot de Passe</a></font></td>
  </tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475">&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Liens</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=conf_lien">Confirmer 
      un lien</a> <font size="1">(<font color="#CC0000"> </font>Liens en attente 
      :<font color="#CC0000"> 
      <?
	  $requete  = "SELECT * FROM liens WHERE conf = '0'";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  $nbre =mysql_num_rows($req);
	  $disp = mysql_fetch_array($req);
	  echo "<b> $nbre </b>";
	  ?>
      </font>)</font></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=ajout_lien">Ajouter 
        un lien</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        </font><font size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=liens_list&action=modif">Modifier 
        un lien</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=liens_list&action=suppr">Supprimer 
        un lien</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Cat&eacute;gorie 
      de Download</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=aj_cat">Ajouter 
        une Cat&eacute;gorie</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=cat_liste&action=modif">Modifier une 
        Cat&eacute;gorie</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=cat_liste&action=suppr">Supprimer 
        une Cat&eacute;gorie</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Download</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=aj_down">Ajouter 
        un Fichier</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=down_liste&action=modif">Modifier 
        un Fichier</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=down_liste&action=suppr">Supprimer 
        un Fichier</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font face="Arial, Helvetica, sans-serif">- <font size="2"><a href="dlcountadmin.php3">Compteur 
      de Download</a></font></font></tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Phrases &agrave; 
      la Con</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- </font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"><a class=type1 href="index.php?page=ajout_phrase">Ajouter 
        une Phrases &agrave; la Con</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=phrases_list&action=modif">Modifier 
        une Phrases &agrave; la Con</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">-</font><font color="#FFFFFF" size="2" face="Arial, Helvetica, sans-serif"> 
        <a class=type1 href="index.php?page=phrases_list&action=suppr">Supprimer 
        une Phrases &agrave; la Con</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="475"><b><i><font face="Arial, Helvetica, sans-serif">Dessins</font></i></b></tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=dessins_upload">Ajouter 
        un Dessin</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=dessins_desc_list">Modifier 
        une Description</a></font></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> <p><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=dessins_suppr_list">Supprimer 
        un Dessin</a></font></p></td>
  </tr>
  <?
  $auteur = ucfirst($pymembs);
  
	if (($auteur == "Country") or ($auteur == "Surprise"))
	{
	?>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><b><i><font face="Arial, Helvetica, sans-serif">Equipe</font></i></b></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=ajout_membre">Ajouter 
      un membre</a></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=membres_liste">Supprimer 
      un membre</a></font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475"><font face="Verdana, Arial, Helvetica, sans-serif"><b><i><font face="Arial, Helvetica, sans-serif">Divers</font></i></b></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><table width="600" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="75" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">-Theme 
            :</font></td>
          <td valign="top"> 
            <?
		  $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
		  mysql_select_db("$dbbase",$db) or Die("Base Down !");
		  
		  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='theme'");
		  $disp = mysql_fetch_array($req);
		  $theme = $disp[valeur];
		  ?>
            <form name="form1" method="post" action="administ/theme.php">
              <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="theme" type="radio" value="_normal" <? if ($theme == '_normal') { echo "checked"; } ?>>
              Aucun<br>
              <input type="radio" name="theme" value="_noel" <? if ($theme == '_noel') { echo "checked"; } ?>>
              No&euml;l<br>
              <input type="radio" name="theme" value="_annif" <? if ($theme == '_annif') { echo "checked"; } ?>>
              Anniversaire <br>
              <br>
              <input type="submit" name="Submit2" value="Valider">
              </font> </form></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="475"><font face="Arial, Helvetica, sans-serif" size="2">- <a href="index.php?page=modifpoll">Changer 
      le Sondage</a></font></td>
  </tr>
  <tr> 
    <td><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="stat/index.html" target="_blank">Statistiques 
      du site</a></font></td>
  </tr>
  <tr> 
    <td><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td><font size="2" face="Arial, Helvetica, sans-serif">- <a href="index.php?page=mailing">Mailing</a></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>- <font size="2" face="Arial, Helvetica, sans-serif"><a href="options.php?id=3">D&eacute;connection</a></font></td>
  </tr>
</table>
<br>
<p> 
<?
}
?>
</p>
