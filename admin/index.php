<?
include "config.inc.php3";

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

if ($del_sug)
{
mysql_query("DELETE FROM suggest WHERE id='$del_sug'");
?>
<script language="Javascript">
alert('Suggestion supprimée avec succès');
window.location='index.php?page=admin';
</script>
<?
}
//include "secu.php";

$requete  = "SELECT * FROM equipe WHERE kinder='".$_COOKIE["gen"]."' && pass='".$_COOKIE["mdpass"]."'";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);

			$dur = date2timestamp($disp[date],"Ymd");
			$jour = date("d",$dur);
			$mois = date("m",$dur);
			$an = date("Y",$dur);
			$jour2 = date("d");
			$mois2 = date("m");
			$an2 = date("Y");
			$dur = diff_date($jour2 , $mois2 , $an2 , $jour , $mois , $an);

if ($error == "level") { echo "<br/><center><font size=2 face=Verdana, Arial, Helvetica, sans-serif color=red><strong>Désolé, tu n'a pas le niveau requis pour accéder à cette page</strong></font></center><br/><br/>"; }
if ($error == "log") { echo "<br/><center><font size=2 face=Verdana, Arial, Helvetica, sans-serif color=red><strong>Désolé, tu doit être loggué pour accéder à cette page</strong></font></center><br/><br/>"; }

if ($_COOKIE["gen"] && !$_COOKIE["mdpass"]) {
setcookie("gen");
setcookie("mdpass");
Header("Location:index.php?page=admin");
}
if($nbre == "1") {

// si le cookie est correct
// alors la page normale s'affiche
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ChangeMessage(champ)
  {
  if(document.getElementById(champ).gro != 1)
  {
  if(document.getElementById)
    document.getElementById(champ).style.display = "block";
	document.getElementById(champ).gro = 1;
  }
  else
  {
  if(document.getElementById)
    document.getElementById(champ).style.display = "none";
	document.getElementById(champ).gro = 0;
  }
  }
-->
</script>
<?
function count_files($dir)
{
    $num = 0;
    
    $dir_handle = opendir($dir);
    while($entry = readdir($dir_handle))
        if(is_file($dir.'/'.$entry))
            $num++;
    closedir($dir_handle);

    return $num;
}   
?>
<form name="form1" method="post" action="">
<table width="600" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#000000">
  <tr> 
    <td bordercolor="#FFFFFF"> <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="314"><font size="4" face="Arial, Helvetica, sans-serif"><em><strong>Informations 
              de toi </strong></em></font><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong></strong></font></td>
            <td width="314" bgcolor="#EEEEEE"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>A-Level 
                : <font color="#CC0000"><? echo $disp[level]; ?></font></strong></font></div></td>
            <td width="284" bgcolor="#EEEEEE"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>K-Level 
                : <font color="#CC0000"> 
                <?
				$lepeudo = ucfirst($HTTP_COOKIE_VARS[gen]);
			  $req  = mysql_query("SELECT * FROM mynewsinfos WHERE signature='$lepeudo'");
			  $nbre_news =mysql_num_rows($req);
			  $rooq  = mysql_query("SELECT * FROM ib_members WHERE name='$lepeudo'");
			  $doosp = mysql_fetch_array($rooq);
			  $nbre_forum = $doosp[posts];
			  if ($lepeudo == 'Surprise')
			  {
			  $req  = mysql_query("SELECT * FROM shoutbox WHERE pseudo='$lepeudo' or pseudo='SurPriseS' or pseudo='SurPr][seS'");
			  }
			  else
			  {
			  $req  = mysql_query("SELECT * FROM shoutbox WHERE pseudo='$lepeudo'");
			  }
			  $nbre_chat =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM lan_party WHERE joueurs like '%;$lepeudo;%'");
			  $nbre_lan =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM dossiers WHERE auteur='$lepeudo'");
			  $nbre_dossier =mysql_num_rows($req);
			  
			  $req  = mysql_query("SELECT * FROM matches WHERE jou_k1='$HTTP_COOKIE_VARS[gen]' or jou_k2='$HTTP_COOKIE_VARS[gen]' or jou_k3='$HTTP_COOKIE_VARS[gen]' or jou_k4='$HTTP_COOKIE_VARS[gen]' or jou_k5='$HTTP_COOKIE_VARS[gen]'");
			  $nbre_matches =mysql_num_rows($req);

			  if ($lepeudo == "Maxi")
			  {
			  $nbre_des1 =  count_files("images/dessins/BD"); 
			  $nbre_des2 =  count_files("images/dessins/Dessins Divers"); 
			  $nbre_des3 =  count_files("images/dessins/Techniques");
			  $nbre_des = $nbre_des1 + $nbre_des2 + $nbre_des3;
			  
			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='11'");
			  $nbre_flash =mysql_num_rows($req);

			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='12'");
			  $nbre_wall =mysql_num_rows($req);

			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='13'");
			  $nbre_theme =mysql_num_rows($req);
			  
			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='14'");
			  $nbre_div =mysql_num_rows($req);
			  
			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='24'");
			  $nbre_skin =mysql_num_rows($req);
			  
			  }

			  $level = $nbre_news*3 + $nbre_forum*2 + $nbre_chat*1 + $nbre_dossier*10 + $nbre_lan*25 + $nbre_matches*5 + $nbre_des*20 + $nbre_wall*5 + $nbre_flash*80 + $nbre_theme*5 + $nbre_div*5 + $nbre_skin*5;
			  
			  mysql_query("UPDATE equipe SET klevel='$level' WHERE kinder='$HTTP_COOKIE_VARS[gen]'");

			  echo $level; ?>
                </font></strong></font></div></td>
          </tr>
          <tr> 
            <td width="314"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            <td width="314" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
          <tr> 
            <td width="314" rowspan="6"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
			<?
			$k1der = str_replace("é","e",$disp[kinder]);
			?>
              <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="75" height="100">
                    <param name="movie" value="flash/<? echo $k1der; ?>.swf">
                    <param name="quality" value="high">
                    <embed src="flash/<? echo $k1der; ?>.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="75" height="100"></embed></object>
              </font></td>
            <td width="314" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Inscrit 
              depuis le: 
              <?
			echo "$jour/$mois/$an ($dur jours)";			
			?>
              </font></td>
          </tr>
          <tr> 
            <td width="314" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Participation:</font></td>
          </tr>
          <tr> 
            <td width="314" colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
                <?
				if($level==0) $level=1;
			$part = $dur/$level;
			if ($part > 3)
			{
			echo "Tu est comme le H de Hawaï";
			}
			else if ($part > 2)
			{
			echo "Tu fout pas grand chose quand même";
			}
			else if ($part > 1)
			{
			echo "Sa serai bien de participer";
			}
			else if ($part > 1.5)
			{
			echo "Ya des efforts de fait :)";
			}
			else if ($part > 1)
			{
			echo "Tu participe, mais pas trop";
			}
			else if ($part > 0.5)
			{
			echo "T'es asser actif, c'est bien";
			}
			else if ($part > 0.3)
			{
			echo "T'est un bon membre, bien actif";
			}
			else if ($part > 0.2)
			{
			echo "Oua ! Toi t'est hyper actif !";
			}
			else
			{
			echo "Ta vie, ton clan";
			}
			?>                </b></font></div></td>
          </tr>
          <tr> 
            <td width="314" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
          </tr>
          <tr> 
            <td width="314" colspan="3">&nbsp;</td>
          </tr>
          <tr> 
            <td width="314" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><u><strong>Stats</strong></u></font></td>
          </tr>
          <tr> 
            <td width="314"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Sections 
              : <strong> 
              <?
			if (($disp[cs] == 'oui') && ($disp[war3] == 'non'))
			{
			echo "Counter-Strike";
			}
			if (($disp[cs] == 'non') && ($disp[war3] == 'oui'))
			{
			echo "Warcraft III";
			}
			if (($disp[cs] == 'oui') && ($disp[war3] == 'oui'))
			{
			echo "Counter-Strike, Warcraft III";
			}
			?>
              </strong></font></td>
            <td width="314" colspan="2" rowspan="6" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">News 
              post&eacute;es : <strong><? echo $nbre_news; ?><br/>
              </strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Posts 
              sur le Forum : <strong><? echo $nbre_forum; ?></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br/>
              Message dans le Rapid'chat : <strong><? echo $nbre_chat; ?></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br/>
              Dossiers : <b><? echo $nbre_dossier; ?></b></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br/>
              LAN Party : <b><? echo $nbre_lan; ?></b></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br/>
              Matches : <b><? echo $nbre_matches; ?></b><br/>
			  <?
			  if ($lepeudo == "Maxi")
			  {
			  ?>
              Dessins : <b><? echo $nbre_des; ?></b><br/>
              Anims flash : <b><? echo $nbre_flash; ?></b><br/>
              Wallpapers : <b><? echo $nbre_wall; ?></b><br/>
              Autres : <b><? echo $nbre_div+$nbre_skin+$nbre_theme; ?></b>
			  <?
			  }
			  ?></font></td>
          </tr>
          <tr> 
            <td width="314" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?
		  if ($disp[statut] == 'pas') { echo "<font color=red><b>"; } 
		  ?>
              Participe à la prochaine LAN : 
              <?
		  if ($disp[statut] == 'pas') { echo "</b></font>"; } 
		  ?>
              <strong> 
              <?
			if ($disp[statut] == 'pas')
			{
			?>
              <select name="menu1" onChange="MM_jumpMenu('parent',this,0)" style="font-size:9; font-family: Verdana, Arial, Helvetica, sans-serif;">
                <option value="" selected>Je sais pas</option>
                <option value="admin/true2.php">Oui</option>
                <option value="admin/false2.php">Non</option>
              </select>
              <?
			}
			else if ($disp[statut] == 'oui' && $lepeudo!='Surprise')
			{
			echo "Oui <font size=1><a href=admin/false2.php>, en fait non ?</a></font>";
			}
			else if ($disp[statut] == 'non' && $lepeudo!='Surprise')
			{
			echo "Non <font size=1><a href=admin/true2.php>, en fait oui ?</a></font>";
			}
			?>
              </strong></font> </td>
          </tr>
		  <?
		  	  $roq = MYSQL_QUERY("SELECT * FROM next_matches");
	  $nbro =mysql_num_rows($roq);

	  
	  if ($nbro != 0)
	  {
	  ?>
          <tr> 
            <td width="314"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?
		  if ($disp[next_match] == '') { echo "<font color=red><b>"; } 
		  ?>
              Participe au prochain match : 
              <?
		  if ($disp[next_match] == '') { echo "</b></font>"; } 
		  ?>
              <strong> 
              <?
			if ($disp[next_match] == '')
			{
			?>
              <select name="menu2" onChange="MM_jumpMenu('parent',this,0)" style="font-size:9; font-family: Verdana, Arial, Helvetica, sans-serif;">
                <option value="" selected>Je sais pas</option>
                <option value="admin/true.php">Oui</option>
                <option value="admin/false.php">Non</option>
              </select>
              <?
			}
			else if ($disp[next_match] == 'oui')
			{
			echo "Oui <font size=1><a href=admin/false.php>, en fait non ?</a></font>";
			}
			else if ($disp[next_match] == 'non')
			{
			echo "Non <font size=1><a href=admin/true.php>, en fait oui ?</a></font>";
			}
			?>
              </strong></font></td>
          </tr>
		  <?
		  }
		  ?>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td width="314">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="314"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=modifplayer">Modifer 
              les infos de la fiche &agrave; moi</a></font></td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="314">&nbsp;</td>
            <td width="314" colspan="2"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=admin&logout=1">D&eacute;connection</a></font></div></td>
          </tr>
        </table></td>
  </tr>
</table>
</form>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2"><font size="4" face="Arial, Helvetica, sans-serif"><strong><em>Boite 
      &agrave; la con</em></strong></font></td>
  </tr>
  <tr> 
    <td colspan="2"><iframe width="100%" height="200px" src="admin/boitealacon.php" scrolling="no" frameborder="0"></iframe></td>
  </tr>
  <tr>
    <td colspan="2"><strong><font size="4" face="Arial, Helvetica, sans-serif"><em>Suggestions &agrave; la con</em></font></strong></td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
	<?
	$requetes  = "SELECT * FROM suggest ORDER BY id DESC";
	$reqs = mysql_query($requetes) or die('Erreur SQL !<br/>'.$requetes.'<br/>'.mysql_error());
	$nbres =mysql_num_rows($reqs);
	if ($nbres != 0)
	{
	while($disps = mysql_fetch_array($reqs))
	{
	?>
      <tr>
        <td width="550"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $disps[pseudo]; ?></b>:  <? echo $disps[profil]; ?></font></td>
        <td width="50"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=admin&del_sug=<? echo $disps[id]; ?>"><img src="images/blank.gif" alt="Supprimer la suggestion" width="13" height="13" border="0"></a></font></div></td>
      </tr>
      <tr>
        <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><i><? echo $disps[txt]; ?></i></font></td>
        </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
        </tr>
		<?
		}
		}
		else
		{
		?>
      <tr>
        <td colspan="2"><strong><font color=red size="2" face="Verdana, Arial, Helvetica, sans-serif">Aucune</font></strong></td>
        </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
        </tr>
		<?
		}
		?>
    </table></td>
  </tr>
  <tr> 
    <td colspan="2"><strong><font size="4" face="Arial, Helvetica, sans-serif"><em>Stats du clan</em></font></strong></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td width="300" valign="top"> <div align="center"><strong></strong></div></td>
  </tr>
  <tr> 
    <td width="300"><table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
  $roq = MYSQL_QUERY("SELECT * FROM config WHERE nom='last_vote'");
  $disp = mysql_fetch_array($roq);
  
  $jour = date("d",$disp[valeur]);
  $mois = date("m",$disp[valeur]);
  $an = date("Y",$disp[valeur]);
  
  $jour2 = date("d");
  $mois2 = date("m");
  $an2 = date("Y");
  
  $nbre = diff_date($jour2 , $mois2 , $an2 , $jour , $mois , $an);
  $plop = $plop+$nbre;
  if ($nbre == '0')
  {
  $nbre = "Aujourd'hui";
  $color = "green";
  }
  else if ($nbre == "1")
  {
  $nbre = "Il y a $nbre jour";
  $color = "green";
  }
  else if ($nbre >= "5")
  {
  $nbre = "Il y a $nbre jours";
  $color = "red";
  }
  else if ($nbre >= "2")
  {
  $nbre = "Il y a $nbre jours";
  $color = "#FF6600";
  }
  ?>
            Dernier vote au sondage : <font color="<? echo $color; ?>"><b><? echo $nbre; ?></b></font></font></td>
        </tr>
        <tr> 
          <td width="300"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
  $roq = MYSQL_QUERY("SELECT * FROM mynewsinfos ORDER BY id DESC");
  $disp = mysql_fetch_array($roq);
  $thedate = explode("/",$disp[date]);
  $nbre = diff_date($jour2 , $mois2 , $an2 , $thedate[0] , $thedate[1] , $thedate[2]);
  $plop = $plop+$nbre;
  if ($nbre == '0')
  {
  $nbre = "Aujourd'hui";
  $color = "green";
  }
  else if ($nbre == "1")
  {
  $nbre = "Il y a $nbre jour";
  $color = "green";
  }
  else if ($nbre >= "5")
  {
  $nbre = "Il y a $nbre jours";
  $color = "red";
  }
  else if ($nbre >= "2")
  {
  $nbre = "Il y a $nbre jours";
  $color = "#FF6600";
  }
  ?>
            Dernière news postée : <font color="<? echo $color; ?>"><b><? echo $nbre; ?></b></font></font> 
          </td>
        </tr>
        <tr> 
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
  $roq = MYSQL_QUERY("SELECT * FROM matches ORDER BY orderdate DESC");
  $disp = mysql_fetch_array($roq);
  $an = substr($disp[orderdate],0,4);
  $mois = substr($disp[orderdate],4,2);
  $jour = substr($disp[orderdate],6,2);
  $nbre = diff_date($jour2 , $mois2 , $an2 , $jour , $mois , $an);
  $plop = $plop+$nbre*2;
  if ($nbre == '0')
  {
  $nbre = "Aujourd'hui";
  $color = "green";
  }
  else if ($nbre == "1")
  {
  $nbre = "Il y a $nbre jour";
  $color = "green";
  }
  else if ($nbre >= "10")
  {
  $nbre = "Il y a $nbre jours";
  $color = "red";
  }
  else if ($nbre >= "5")
  {
  $nbre = "Il y a $nbre jours";
  $color = "#FF6600";
  }
  else
  {
  $nbre = "Il y a $nbre jours";
  $color = "green";
  }
  ?>
            Dernier match jou&eacute; : <font color="<? echo $color; ?>"><b><? echo $nbre; ?></b></font></font> 
          </td>
        </tr>
        <tr> 
          <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Clicks sur la banni&egrave;re du sponsor :&nbsp;
		  <?php 
		  include ('compt_sponsor.php');
		  echo $clicks;
		  ?>
		  </font></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td width="300">&nbsp; </td>
        </tr>
        <?
  $roq = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$HTTP_COOKIE_VARS[gen]'");
  $disp = mysql_fetch_array($roq);
  if ($disp[level] == 10)
  {
  ?>
        <?
	  }
	  ?>
      </table></td>
    <td width="300" valign="top"><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Taux 
        d'activit&eacute; du clan </font></strong> </div>
      <table width="300" height="100" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="10" rowspan="3">&nbsp;</td>
		  <?
		  if ($plop >= "50")
		  {
		  $color = "red";
		  }
		  else if ($plop >= "25")
		  {
		  $color = "#FF6600";
		  }
		  else if ($plop >= "10")
		  {
		  $color = "green";
		  }
		  $plop2 = $plop;
		  if ($plop2 >= 96) { $plop2 = "95"; }
		  ?>
          <td width="15" rowspan="3" valign="top"> <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" bgcolor="<? echo $color; ?>">
              <tr> 
                <td valign="top" bordercolor="#000000"><img src="images/white.gif" width="15" height="<? echo $plop2; ?>"></td>
              </tr>
            </table></td>
          <td width="10" rowspan="3">&nbsp;</td>
          <td height="33%"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? if ($color == "green") { ?>Elev&eacute;<? } ?>&nbsp;</font></td>
        </tr>
        <tr> 
          <td height="33%"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? if ($color == "#FF6600") { ?>Moyen<? } ?>&nbsp;</font></td>
        </tr>
        <tr> 
          <td height="33%"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? if ($color == "red") { ?>Bas<? } ?>&nbsp;</font></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <?
  $roq = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$HTTP_COOKIE_VARS[gen]'");
  $disp = mysql_fetch_array($roq);
  if ($disp[level] == 10)
  {
  ?>
  <tr> 
    <td colspan="2"><font size="4" face="Arial, Helvetica, sans-serif"><strong><em>Machins 
      pour administrer l'&eacute;quipe</em></strong></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2"><em><strong><font size="2" face="Arial, Helvetica, sans-serif"><a href="Javascript:ChangeMessage('999997')"><font color="#000000">Membres</font></a></font></strong></em> 
      <table width=100% border=0 cellpadding="0" cellspacing="0" ID=999997 style='display:none;'>
        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=ajout_membre">Ajouter 
            des membres</a></font></TD>
        </tr>
        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=membres_liste">Supprimer 
            des membres</a></font></TD>
        </tr>
        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=visu_membres_levels">Voir 
            les levels des membres</a></font></TD>
        </tr>
        <tr> 
          <TD height="5"></TD>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <?
	  }
	  ?>
</table>
<?
  if ($disp[level] == 10)
  {
  ?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td><font size="4" face="Arial, Helvetica, sans-serif"><strong><em>Bidules 
      pour administrer l'administration</em></strong></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><em><strong><font size="2" face="Arial, Helvetica, sans-serif"><a href="Javascript:ChangeMessage('999998')"><font color="#000000">Membres</font></a></font></strong></em> 
      <table width=100% border=0 cellpadding="0" cellspacing="0" ID=999998 style='display:none;'>
        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=membres_liste_levels">Modifier les Levels des membres</a></font></TD>
        </tr>
        <tr> 
          <TD height="5"></TD>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td><em><strong><font size="2" face="Arial, Helvetica, sans-serif"><a href="Javascript:ChangeMessage('999999')"><font color="#000000">Catégories et Pages</font></a></font></strong></em>
      <table width=100% border=0 cellpadding="0" cellspacing="0" ID=999999 style='display:none;'>
        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=admin_pages">Gérer 
            les pages et les catégories</a></font></TD>
        </tr>        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href="index.php?page=admin_pages_levels">Gérer 
            les levels des pages</a></font></TD>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
}
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td><font size="4" face="Arial, Helvetica, sans-serif"><strong><em>Trucs pour 
      administrer le site</em></strong></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <?
  $raquete  = "SELECT * FROM admin_cat ORDER BY ordre";
  $raq = mysql_query($raquete) or die('Erreur SQL !<br/>'.$raquete.'<br/>'.mysql_error());
  while($dasp = mysql_fetch_array($raq))
  {
  ?>
  <tr> 
    <td><em><strong><font size="2" face="Arial, Helvetica, sans-serif"><a href="Javascript:ChangeMessage('<? echo $dasp[id]; ?>')"><font color="#000000"><? echo $dasp[nom]; ?></font></a></font></strong></em> 
      <table width=100% border=0 cellpadding="0" cellspacing="0" ID=<? echo $dasp[id]; ?> style='display:none;'>
        <?
	  $requete  = "SELECT * FROM admin WHERE cat_id='$dasp[id]' ORDER BY ordre";
	  $req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
	  while($disp = mysql_fetch_array($req))
	  {
	  ?>
        <tr> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<a href=<? echo $disp[lien]; ?>><? echo $disp[texte]; ?></a></font></TD>
        </tr>
        <?
	  }
	  ?>
        <tr> 
          <TD height="5"></TD>
        </tr>
      </table></td>
  </tr>
  <?
  }
  ?>
</table><? } else {
// sinon, le formulaire s'affiche
?>
<form action="index.php?page=admin" method="post">
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
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Login :</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="login" type="text" id="login">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mot de Passe 
        :</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="pass" type="password" id="pass">
        </font></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=je_suis_trop_con">Mot 
          de passe perdu ?</a></font></div></td>
    </tr>
    <tr> 
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td colspan="2"> <div align="center"> 
          <input type="Submit" value="Valider">
        </div></td>
    </tr>
  </table>
</form>
<? } ?>
