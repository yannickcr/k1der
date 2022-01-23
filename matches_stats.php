<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Stats 
      d&eacute;taill&eacute;es des matches=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
 
<?
   $win = 0;
  $lose = 0;
  $draw = 0;
  require("config.inc.php3");
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="50%" align="center">&nbsp;</td>
    <td width="50%" align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td width="50%" align="center" valign="top"> <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><strong><font size="1">&nbsp;</font></strong></font></font><font size="2"><strong>Carte 
        la plus jou&eacute;e</strong></font></font></div></td>
    <td width="50%" align="center"> <div align="right"><font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><strong>Carte 
        o&ugrave; on g&eacute;rationne le plus</strong></font><font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><strong><font size="1">&nbsp;</font></strong></font></font></font></div></td>
  </tr>
  <tr> 
    <td width="50%" align="left" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
$req = MYSQL_QUERY("SELECT DISTINCT map FROM matches ORDER BY map");
$res = MYSQL_NUM_ROWS($req);
$i = 1;
$j = 1;
while($disp = mysql_fetch_array($req))
{
$ledisp[$j] = $disp[map];
$j++;
}
while($j > 0)
{
$leres[$i] = mysql_num_rows(mysql_query("SELECT * FROM matches WHERE map='$ledisp[$j]' or map2='$ledisp[$j]'"));
$nomdelacat[$i] = $ledisp[$j];
$i++;
$j--;
}
$percent2 = '';
$percent3 = 99999;
while ($i > 0)
{
if ($leres[$i] > $percent2)
{
$percent2 = $leres[$i];
$style2 = $nomdelacat[$i];
}
if (($leres[$i] < $percent3)  && ($leres[$i] != 0))
{
$percent3 = $leres[$i];
$style3 = $nomdelacat[$i];
}
$i--;
}
//$percent2 = ($percent2/$res1)*100;
?>
      <img src="images/cartes/<? echo $style2; ?>.jpg" width="109" height="81" border="1" align="left"><b><br>
      <? echo ucfirst($style2); ?></b><br>
      <font size="1"><? echo $percent2; ?> fois</font><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong><br>
      <br>
      <br>
      <br>
      <br>
      </strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">On 
      aime po <strong><? echo ucfirst($style3); ?></strong><br>
      On l'a jou&eacute;e seulement <? echo $percent3; ?> fois</font></font></font></td>
    <td width="50%" align="center" valign="top"> <div align="right"> 
        <?
$req = MYSQL_QUERY("SELECT * FROM matches ORDER BY id");
$res = MYSQL_NUM_ROWS($req);
$i = 1;
$movai_score = 99999;
$bad_ratio = 99999;
while($disp = mysql_fetch_array($req))
{
$score[$disp[map]] = $score[$disp[map]]+$disp[score_map_k1_t]+$disp[score_map_k1_ct];
if ($score[$disp[map]] > $best_score) { $best_map = $disp[map]; $best_score = $score[$disp[map]]; }
if ($score[$disp[map]] < $movai_score) { $movai_map = $disp[map]; $movai_score = $score[$disp[map]]; }
$score[$disp[map2]] = $score[$disp[map]]+$disp[score_map2_k1_t]+$disp[score_map2_k1_ct];
if ($score[$disp[map2]] > $best_score) { $best_map = $disp[map2]; $best_score = $score[$disp[map2]]; }
if ($score[$disp[map2]] < $movai_score) { $movai_map = $disp[map2]; $movai_score = $score[$disp[map2]]; }
}

// ouvrir le répertoire
$dir = opendir("images/cartes");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
if ($fichier != '.' && $fichier != '..' )
{
$fscorem = 0;
$fscore = 0;
$fscorem2 = 0;
$fscore2 = 0;
$fichier = str_replace(".jpg", "", $fichier);
$score[$fichier] = 0;
$scorem[$fichier] = 0;
$req = mysql_query("SELECT * FROM matches WHERE map2='$fichier'");
while($disp = mysql_fetch_array($req))
{
$score[$disp[map2]] = $score[$disp[map2]]+$disp[score_map2_k1_t]+$disp[score_map2_k1_ct];
$scorem[$disp[map2]] = $scorm[$disp[map2]]+$disp[score_map2_me_t]+$disp[score_map2_me_ct];
$fscorem2 = $scorem[$disp[map2]];
$fscore2 = $score[$disp[map2]];
}

//echo "$fichier : ".$fscore2." / ".$fscorem2."<br>";

$fscorem = 0;
$fscore = 0;
//$fscorem2 = 0;
//$fscore2 = 0;
$score[$fichier] = 0;
$scorem[$fichier] = 0;

$req = mysql_query("SELECT * FROM matches WHERE map='$fichier'");
while($disp = mysql_fetch_array($req))
{
$score[$disp[map]] = $score[$disp[map]]+$disp[score_map_k1_t]+$disp[score_map_k1_ct];
$scorem[$disp[map]] = $scorem[$disp[map]]+$disp[score_map_me_t]+$disp[score_map_me_ct];
$fscorem = $scorem[$disp[map]];
$fscore = $score[$disp[map]];
}
if (($fscorem+$fscorem2) != 0)
{
$plop = round(($fscore+$fscore2)/($fscorem+$fscorem2),2);
}
else
{
$plop = $fscore+$fscore2;
}
if ($plop > $best_ratio)
{
$best_map = $fichier;
$best_ratio = $plop;
$best_score = $fscore+$fscore2;
$best_scorem = $fscorem+$fscorem2;
}
else if ($plop < $bad_ratio && $plop != 0)
{
$bad_map = $fichier;
$bad_ratio = $plop;
$bad_score = $fscore+$fscore2;
$bad_scorem = $fscorem+$fscorem2;
}
//echo "$fichier : ".$plop."<br>";
}
}
// ferme le répertoire
closedir($dir);
?></font></font></font>
        <font size="2"> <img src="images/cartes/<? echo $best_map; ?>.jpg" width="109" height="81" border="1" align="right"><b><br>
        </b><font face="Verdana, Arial, Helvetica, sans-serif"><b><? echo ucfirst($best_map); ?></b><br>
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><font size="1"><? echo $best_score; ?> 
        rounds gagnés</font></font><font size="1"><br>
        <? echo $best_scorem; ?> rounds perdus</font></font><font size="1"><br>
        <font face="Verdana, Arial, Helvetica, sans-serif"><b>Ratio : </b><font face="Verdana, Arial, Helvetica, sans-serif"><? echo $best_ratio; ?></font></font><br>
        <br>
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
        On est movais sur <strong><? echo ucfirst($bad_map); ?></strong><br>
        On a un ratio de seulement<font face="Verdana, Arial, Helvetica, sans-serif"> 
        <? echo $bad_ratio; ?></font> dessus</font></font><font size="1"> </font></font></font></font></p> 
      </div></td>
  </tr>
  <tr> 
    <td width="50%"> <div align="left"> 
        <?
$requete  = "SELECT * FROM matches order by orderdate DESC, id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$roundswin = 0;
$roundslose = 0;
$win_lan = 0;
$win_rezo = 0;
$win_net = 0;
$lose_lan = 0;
$lose_rezo = 0;
$lose_net = 0;
$draw_lan = 0;
$draw_rezo = 0;
$draw_net = 0;
while($disp = mysql_fetch_array($req))
{
if ($disp[score_k1] > $disp[score_me])
		{
		$win++;
		if ($disp[type] == "LAN Arena") $win_lan++;
		else if ($disp[type] == "Jeu en Réseau") $win_rezo++;
		else $win_net++;
		}
		else if ($disp[score_k1] < $disp[score_me])
		{
		$lose++;
		if ($disp[type] == "LAN Arena") $lose_lan++;
		else if ($disp[type] == "Jeu en Réseau") $lose_rezo++;
		else $lose_net++;
		}
		else
		{
		$draw++;
		if ($disp[type] == "LAN Arena") $draw_lan++;
		else if ($disp[type] == "Jeu en Réseau") $draw_rezo++;
		else $draw_net++;
		}
		$roundswin = $roundswin+$disp[score_k1];
		$roundslose = $roundslose+$disp[score_me];

}
if ($lose_lan == 0) $lose_lan = 1;
if ($lose_rezo == 0) $lose_rezo = 1;
if ($lose_net == 0) $lose_net = 1;
$ratio_lan = round(($win_lan/$lose_lan),2);
$ratio_net = round(($win_net/$lose_net),2);
$ratio_rezo = round(($win_rezo/$lose_rezo),2);
$roundswin_p = round((($roundswin/($roundswin+$roundslose))*100),1);
$roundslose_p = round((($roundslose/($roundswin+$roundslose))*100),1);
$win_p = round((($win/($win+$lose+$draw))*100),1);
$lose_p = round((($lose/($win+$lose+$draw))*100),1);
$draw_p = round((($draw/($win+$lose+$draw))*100),1);
?>
      </div></td>
    <td width="50%"> <div align="right"></div></td>
  </tr>
  <tr> 
    <td align="center"><div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Rounds 
        jou&eacute;s : <? echo ($roundswin+$roundslose); ?></strong></font></div></td>
    <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <tr> 
    <td colspan="2" align="center"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td align="center" nowrap> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gagn&eacute;s</font></div></td>
          <td align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">:&nbsp;&nbsp;</font></td>
          <td width="100%" align="center"><div align="left"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
              <img src="images/barre5.gif"><img src="images/barre4.gif" width="<? echo $roundswin_p*4; ?>" height="7"><img src="images/barre6.gif"> 
              <? echo $roundswin_p."% (".$roundswin.")"; ?></font></div></td>
        </tr>
        <tr> 
          <td align="center" nowrap> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Perdus</font></div></td>
          <td align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">:&nbsp;&nbsp;</font></td>
          <td width="100%" align="center"><div align="left"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
              <img src="images/barre2.gif"><img src="images/barre.gif" width="<? echo $roundslose_p*4; ?>" height="7"><img src="images/barre3.gif"> 
              <? echo $roundslose_p."% (".$roundslose.")"; ?></font></div></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" align="center"><div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2">Matches 
        jou&eacute;s : <? echo ($win+$lose+$draw); ?></font></strong></font></div></td>
  </tr>
  <tr> 
    <td colspan="2" align="center"><table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td align="center" nowrap> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gagn&eacute;s</font></div></td>
          <td align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">:&nbsp;&nbsp;</font></td>
          <td width="100%" align="center"><div align="left"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
              <img src="images/barre5.gif"><img src="images/barre4.gif" width="<? echo $win_p*4; ?>" height="7"><img src="images/barre6.gif"> 
              <? echo $win_p."% (".$win.")"; ?></font></div></td>
        </tr>
        <tr> 
          <td align="center" nowrap><div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Egalit&eacute;s</font></div></td>
          <td align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">:&nbsp;&nbsp;</font></td>
          <td width="100%" align="center"><div align="left"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
              <img src="images/barre8.gif"><img src="images/barre7.gif" width="<? echo $draw_p*4; ?>" height="7"><img src="images/barre9.gif"> 
              <? echo $draw_p."% (".$draw.")"; ?></font></div></td>
        </tr>
        <tr> 
          <td align="center" nowrap> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Perdus</font></div></td>
          <td align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">:&nbsp;&nbsp;</font></td>
          <td width="100%" align="center"><div align="left"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
              <img src="images/barre2.gif"><img src="images/barre.gif" width="<? echo $lose_p*4; ?>" height="7"><img src="images/barre3.gif"> 
              <? echo $lose_p."% (".$lose.")"; ?></font></div></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  	<?
	$requete  = "SELECT * FROM matches order by orderdate DESC, id DESC";
	$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	$nbre =mysql_num_rows($req);
	$ct_win = 0;
	$ct_lose = 0;
	$t_win = 0;
	$t_lose = 0;
	while($disp = mysql_fetch_array($req))
	{
	$ct_win = $ct_win+$disp[score_map_k1_ct]+$disp[score_map2_k1_ct];
	$ct_lose = $ct_lose+$disp[score_map_me_ct]+$disp[score_map2_me_ct];
	$t_win = $t_win+$disp[score_map_k1_t]+$disp[score_map2_k1_t];
	$t_lose = $t_lose+$disp[score_map_me_t]+$disp[score_map2_me_t];
	}
	?>
  <tr>
    <td colspan="2" align="center"><div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">On est carrement meilleur c&ocirc;t&eacute;          <? if ($ct_win > $t_win) { echo "CT"; } else { echo "Terro"; } ?>
        :<br>
        <b><? if ($ct_win > $t_win) { echo $ct_win; } else { echo $t_win; } ?></b>
        rounds gagnés<br>
        <font size="1">(et seulement
        <b><? if ($ct_win > $t_win) { echo $t_win; } else { echo $ct_win; } ?></b>
        côté        <? if ($ct_win > $t_win) { echo "Terro"; } else { echo "CT"; } ?>
    )</font></font></div></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">On est vraiment des merdes 
      <?
	if ($ratio_lan < $ratio_net) echo "en <b>LAN Party</b>";
	else if($ratio_net < $ratio_rezo) echo "sur <b>Internet</b>";
	else echo "en <b>Jeu en Réseau</b>";
	?>
    </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
      <b>
      <?
    if ($ratio_lan < $ratio_net) echo $win_lan;
	else if($ratio_net < $ratio_rezo) echo $win_net;
	else echo $win_rezo;
	?>
</b> victoires<b>
</b> pour 
      <b>      </b><b>
      <?
	if ($ratio_lan < $ratio_net) echo $lose_lan;
	else if($ratio_net < $ratio_rezo) echo $lose_net;
	else echo $lose_rezo;
	?>
      </b> defaites<br>
      <font size="1"><strong>Ratio : </strong>
      <?
    if ($ratio_lan < $ratio_net) echo $ratio_lan;
	else if($ratio_net < $ratio_rezo) echo $ratio_net;
	else echo $ratio_rezo;
	?>
      </font></font></td>
    <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">On roxx plus 
        <?
	if ($ratio_lan > $ratio_net) echo "en <b>LAN Party</b>";
	else if($ratio_net > $ratio_rezo) echo "sur <b>Internet</b>";
	else echo "en <b>Jeu en R&eacute;seau</b>";
	?>
    </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
  <b>  </b> <b>
  <?
    if ($ratio_lan > $ratio_net) echo $win_lan;
	else if($ratio_net > $ratio_rezo) echo $win_net;
	else echo $win_rezo;
	?>
  </b> victoires pour<b>
  <?
	if ($ratio_lan > $ratio_net) echo $lose_lan;
	else if($ratio_net > $ratio_rezo) echo $lose_net;
	else echo $lose_rezo;
	?>
  </b> defaites<br>
  <font size="1"><strong>Ratio : </strong>
  <?
    if ($ratio_lan > $ratio_net) echo $ratio_lan;
	else if($ratio_net > $ratio_rezo) echo $ratio_net;
	else echo $ratio_rezo;
	?>
</font></font></div></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div align="left">
	  <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
<?
	$requete  = "SELECT DISTINCT loc FROM matches WHERE occ='Poules'";
	$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	$nbre_poules =mysql_num_rows($req);
	$requete  = "SELECT * FROM matches WHERE occ='Train'";
	$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	$nbre_trains =mysql_num_rows($req);
	$requete  = "SELECT DISTINCT loc FROM matches WHERE occ='8ème de Finale'";
	$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	$nbre_8 =mysql_num_rows($req);
	?>
Bon, les <strong>Tournois</strong> c'est pas notre fort, on est all&eacute;: </font></div></td>
  </tr>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $nbre_poules; ?></b> fois en Poules (d&eacute;j&agrave; arriver jusque l&agrave; c pas mal)</font></td>
  </tr>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $nbre_8; ?></b> fois en 


 8&egrave;me de Finale (d&eacute;j&agrave; l&agrave; c limite de la science-fiction) </font></td>
  </tr>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <tr>
    <td colspan="2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Bon, on est pas all&eacute; plus haut mais pour rattraper sa on &agrave; fait <b><? echo $nbre_trains; ?></b> Trains (nan sa rattrape pas ? tan pis) </font></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" align="center">
	<?
		$year = date("Y");
		$mois = '01';
		$date1 = '2001';
		$date2 = date("m");
		//echo $year.$mois.":".$date;
while ("$year$mois" >= "$date1$mois")
{
echo "<font face=arial size=2><b>$year</b></font>";
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td>&nbsp;</td>
          <td width="1" valign="bottom"> <div align="center"><img src="images/blkspacer.gif" width="1" height="200"></div></td>
        <?
while ($mois < 13)
{
$win = 0;
$lose = 0;
$draw = 0;
$requete  = "SELECT * FROM matches WHERE orderdate like '$year$mois%'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
//echo $nbre."<br>";
$roundswin = 0;
$roundslose = 0;
while($disp = mysql_fetch_array($req))
{
if ($disp[score_k1] > $disp[score_me])
		{
		$win++;
		}
		else if ($disp[score_k1] < $disp[score_me])
		{
		$lose++;
		}
		else
		{
		$draw++;
		}
		$roundswin = $roundswin+$disp[score_k1];
		$roundslose = $roundslose+$disp[score_me];

}
if ($win+$lose+$draw == 0 || $roundswin == 0 && $roundslose == 0) { $win_p = 0; $lose_p = 0; $draw_p = 0; $roundswin_p = 0; $roundslose_p = 0; }
else {
$roundswin_p = round((($roundswin/($roundswin+$roundslose))*100),1);
$roundslose_p = round((($roundslose/($roundswin+$roundslose))*100),1);
$win_p = round((($win/($win+$lose+$draw))*100),1);
$lose_p = round((($lose/($win+$lose+$draw))*100),1);
$draw_p = round((($draw/($win+$lose+$draw))*100),1);
}
?>
		  <td width="8%" valign="bottom" > 
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="bottom"> 
                <td> 
                  <div align="right"><img src="images/h_barre6.gif"><br>
                    <img src="images/h_barre4.gif" width="7" height="<? echo $win*10; ?>" alt="<? echo $win_p."% (".$win.")"; ?>"></div></td>
                <td> 
                  <div align="center"><img src="images/h_barre9.gif"><br>
                    <img src="images/h_barre7.gif" width="7" height="<? echo $draw*10; ?>" alt="<? echo $draw_p."% (".$draw.")"; ?>"></div></td>
                <td><img src="images/h_barre3.gif"><br>
                  <img src="images/h_barre.gif" width="7" height="<? echo $lose*10; ?>" alt="<? echo $lose_p."% (".$lose.")"; ?>"></td>
              </tr>
            </table>
		  </td>
			<?
$mois++;
if ($mois < 10) { $mois = "0$mois"; }
}
			?>
        </tr>
        <tr> 
          <td colspan="14"><img src="images/blkspacer.gif" width="100%" height="1"></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td width="1" valign="top"> <div align="center"><img src="images/blkspacer.gif" width="1" height="70"></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
              <br>
              <img src="images/stats/janvier.gif" width="8" height="32"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/fevrier.gif" width="8" height="31"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/mars.gif" width="8" height="22"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/avril.gif" width="8" height="20"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/mai.gif" width="8" height="15"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/juin.gif" width="8" height="19"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/juillet.gif" width="8" height="26"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/aout.gif" width="8" height="21"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/septembre.gif" width="10" height="48"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/octobre.gif" width="8" height="36"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/novembre.gif" width="8" height="46"></font></div></td>
          <td width="8%" valign="top"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <img src="images/stats/decembre.gif" width="8" height="46"></font></div></td>
        </tr>
      </table><br><br>
	  <?
$year--;
$mois = '01';
}
	  ?></td>
  </tr>
  <tr> 
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" align="center"> 
      <?
// ouvrir le répertoire
$dir = opendir("images/cartes");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
while($fichier = readdir($dir)) {
if ($fichier != '.' && $fichier != '..' )
{
$fichier = str_replace(".jpg", "", $fichier);
$nbre = mysql_num_rows(mysql_query("SELECT * FROM matches WHERE map='$fichier' or map2='$fichier'"));
if ($nbre != 0)
{
//echo "$fichier : $nbre<br>";
}
}
}
// ferme le répertoire
closedir($dir);
		?>
    </td>
  </tr>
</table>
