<?
function DirTri($rep,$tri)
{
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
    if($File != "." && $File != ".." && $File != "index.htm")
    {
      $Array[] = "$File";
    }
    $i++;
  }
  closedir($dir);

  if($tri == 'DESC'){
    rsort($Array);
  }else{
    sort($Array);
  }
  $Max = count($Array);
//  for($i = 0; $i != $Max; $i++){
  if ($Max != '')
  {
  echo "<img src=\"images/screen1.gif\" alt=\"Screenshots disponibles\" width=\"14\" height=\"14\" border=\"0\">";
  }
  else
  {
  echo "<img src=\"images/screen0.gif\" alt=\"Screenshots non disponibles\" width=\"14\" height=\"14\" border=\"0\">";
  }
//  }
  //echo "<br/><br/>".$Max." fichier(s)" ;
}
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Matches=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=matches_stats">Statistiques</a></font></div></td>
  </tr>
</table>
 
<center>
</center>
<br/>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="3"> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Matches<br/>
        </font></strong></div></td>
    <td width="130"> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></strong></div></td>
    <td> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></div></td>
    <td width="50"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></strong></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="11"><hr width="550" size="1" noshade color="#000000"></td>
  </tr>
  <?
$requete  = "SELECT * FROM matches order by orderdate DESC, id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$nbre =mysql_num_rows($req);
while($disp = mysql_fetch_array($req))
{
if ($disp[score_me] == '')
{
$disp[score_me] = "0";
}
if ($disp[score_k1] == '')
{
$disp[score_k1] = "0";
}
					  if ($disp[score_k1] > $disp[score_me])
					  {
					  $back = "#B3FFB3";
					  }
					  else if ($disp[score_k1] < $disp[score_me])
					  {
					  $back = "#FFB3B3";
					  }
					  else
					  {
					  $back = "#B3B3FF";
					  }
?>
  <tr> 
    <td width="120"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>-=K<font color="#CC0000">1der</font>=-</b> 
        </font></div></td>
    <td width="50" nowrap bgcolor="<? echo $back; ?>"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[score_k1]; ?> 
        - <? echo $disp[score_me]; ?></font></div></td>
    <td width="120"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $disp[mechants]; ?></b></font></div></td>
    <td width="130"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? echo "$disp[jour] $disp[mois] $disp[annee]"; ?></font></div></td>
    <td nowrap> 
      <? if ($disp[type] == "LAN Arena") { $disp[type] = "LAN Party"; } ?>
    <div align="center"><font size=" <? if ($disp[type] != 'Jeu en Réseau') { echo '2'; } else { echo '1'; } ?>" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[type]; ?></font></div></td>
    <td width="50"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=matches_details&id=<? echo $disp[id]; ?>">D&eacute;tails</a></font></div></td>
    <td align="center">&nbsp;&nbsp;</td>
    <td align="center"> 
      <?
	if (($disp[hltv] != '') && ($disp[hltv] != 'http://'))
	{
	?>
      <img src="images/tv1.gif" alt="HLTV disponible" width="14" height="16" border="0"> 
      <?
	}
	else
	{
	?>
      <img src="images/tv0.gif" alt="HLTV non disponible" width="14" height="16" border="0"> 
      <?
	}
	?>
    </td>
    <td align="center"> 
      <?
	if (($disp[score_map_k1_t]+$disp[score_map_me_ct]+$disp[score_map_k1_ct]+$disp[score_map_me_t]) != 0)
	{
	?>
      <img src="images/score_det1.gif" alt="Score détaillé" width="16" height="9" border="0"> 
      <?
	}
	else
	{
	?>
      <img src="images/score_det0.gif" alt="Score non détaillé" width="16" height="9" border="0"> 
      <?
	}
	?>
    </td>
    <td align="center"> 
      <? DirTri("matches/fichiers/$disp[id]/","ASC"); ?>
    </td>
    <td align="center">&nbsp;&nbsp;</td>
  </tr>
  <?
  }
  ?>
</table>
<?php /*
<center><iframe src="cyberleague.php" height="280" width="600" frameborder="0" scrolling="no"></iframe></center>
<center><iframe src="graph.php" height="350" width="600" frameborder="0" scrolling="auto"></iframe></center>
*/ ?>