<?
// Variables

if ($score_k1 > $score_me)
{
$hohoho = 'win';
}
else if ($score_k1 < $score_me)
{
$hohoho = 'lose';
}
else
{
$hohoho = 'draw';
}

if ($type != 'Internet')
{
$typo = 'Localisation :';
}
else
{
$typo = 'Server :';
}

if ($comm != "")
{
$comm = str_replace("
","<br>",$comm);
$comm2 = "<tr> 
    <td colspan=\"3\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong>Commentaires 
      :</strong></font></td>
  </tr>
  <tr>
    <td colspan=\"3\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$comm</font></td>
  </tr>
  <tr> 
    <td colspan=\"3\">&nbsp;</td>
  </tr>";
}
if ($site != "http://")
{
$site2 = str_replace("http://","",$site);
$site2 = "<tr> 
    <td colspan=\"2\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Site 
      internet : <a href=\"$site2\" target=\"_blank\">$site2</a></font></td>
  </tr>";
}
else
{
$site2 = "<tr><td></td></tr>";
}
if ($irc != "")
  {
  $irc2 = "<tr> 
    <td colspan=\"2\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Channel 
      IRC : <a href=\"irc://quakenet.org/$irc\" target=\"_blank\">#$irc</a></font></td>
  </tr>";
  }
else
{
$irc2 = "<tr><td></td></tr>";
}


if ($map2 != 'Aucune')
{

// Score détaillé ou pas

if (($score_map_k1_t+$score_map_me_ct+$score_map_k1_ct+$score_map_me_t) != 0)
{
$score_det = "<tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan=\"2\"><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong>Scores</strong></font></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">T 
              : $score_map_k1_t/$score_map_me_ct</font></div></td>
          <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">T 
              : $score_map2_k1_t/$score_map2_me_ct</font></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">CT: $score_map_k1_ct/$score_map_me_t              </font></div></td>
          <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"> 
              CT: $score_map2_k1_ct/$score_map2_me_t</font></div></td>
          <td>&nbsp;</td>
        </tr>";
}

$mappy = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr> 
          <td colspan=\"4\">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan=\"4\"><div align=\"center\"><strong><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Cartes 
              jou&eacute;es <br>
              <br>
              </font></strong></div></td>
        </tr>
        <tr> 
          <td width=\"25%\"><div align=\"center\"></div></td>
          <td width=\"25%\"><div align=\"center\"><img src=\"../images/cartes/$map.jpg\" width=\"109\" height=\"81\" border=\"1\"></div></td>
          <td width=\"25%\"> <div align=\"center\"><img src=\"../images/cartes/$map2.jpg\" width=\"109\" height=\"81\" border=\"1\"></div></td>
          <td width=\"25%\">&nbsp;</td>
        </tr>
        <tr> 
          <td><div align=\"center\"><strong></strong></div></td>
          <td><div align=\"center\"><strong><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$map</font></strong></div></td>
          <td width=\"25%\"> <div align=\"center\"><strong><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$map2</font></strong></div></td>
          <td width=\"25%\">&nbsp;</td>
        </tr>
$score_det
      </table>";
}
else
{

if (($score_map_k1_t+$score_map_me_ct+$score_map_k1_ct+$score_map_me_t) != 0)
{
$score_det = "<tr> 
          <td>&nbsp;</td>
          <td colspan=\"2\">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan=\"2\"><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong>Scores</strong></font></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td width=\"25%\">&nbsp;</td>
          <td width=\"25%\"> <div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">T 
              : $score_map_k1_t/$score_map_me_ct</font></div></td>
          <td width=\"25%\"><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">CT: 
              $score_map_k1_ct/$score_map_me_t</font></div></td>
          <td width=\"25%\">&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan=\"2\"><div align=\"center\"></div></td>
          <td>&nbsp;</td>
        </tr>";
}

$mappy = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr> 
          <td colspan=\"4\">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan=\"4\"><div align=\"center\"><strong><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Carte 
              jou&eacute;e<br>
              <br>
              </font></strong></div></td>
        </tr>
        <tr> 
          <td width=\"25%\"><div align=\"center\"></div></td>
          <td colspan=\"2\"> <div align=\"center\"><img src=\"../images/cartes/$map.jpg\" width=\"109\" height=\"81\" border=\"1\"></div>
            <div align=\"center\"></div></td>
          <td width=\"25%\">&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan=\"2\"><div align=\"center\"><strong><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$map</font></strong></div></td>
          <td>&nbsp;</td>
        </tr>
$score_det
      </table>";
}

//$fouchiers = DirTri('matches/fichiers/$id/','ASC');

$allthematch = "<table width=\"600\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
  <tr> 
    <td colspan=\"2\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Equipe 
      affront&eacute;e : <b><b>$mechants</b></b></font></td>
    <td rowspan=\"16\" valign=\"top\"> <div align=\"center\"> 
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
          <tr> 
            <td width=\"50%\"> <div align=\"center\"><font size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong><font size=\"5\">-=K<font color=\"#CC0000\">1der</font>=-</font></strong></font></div></td>
            <td width=\"50%\"> <div align=\"center\"><font size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong><font size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong><font size=\"5\">$mechants</font></strong></font></strong></font></div></td>
          </tr>
          <tr> 
            <td width=\"50%\"> <div align=\"center\"><font size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong><font size=\"5\">$score_k1</font></strong></font></div></td>
            <td width=\"50%\"> <div align=\"center\"><font size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong><font size=\"5\">$score_me</font></strong></font></div></td>
          </tr>
        </table>
        <font size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong><font size=\"5\"> 
        </font></strong></font><img src=\"../images/$hohoho.jpg\" width=\"250\" height=\"200\"></div></td>
  </tr>$site2
$irc2<tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"2\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Date 
      : <b>$jour $mois $annee</b></font></td>
  </tr>
  <tr> 
    <td colspan=\"2\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Type 
      : </font><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>$type</b></font></td>
  </tr>
  <tr> 
    <td colspan=\"2\" valign=\"top\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$typo</font><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>$loc</b></font></td>
  </tr>
  <tr> 
    <td colspan=\"2\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Occasion 
      :</font><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b> 
      $occ</b></font></td>
  </tr>
  <tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"2\" align=\"center\"><font size=\"2\"><font color=\"#FF6600\" face=\"Verdana, Arial, Helvetica, sans-serif\"></font></font></td>
  </tr>
  <tr> 
    <td colspan=\"2\" align=\"center\">&nbsp; 
    </td>
  </tr>
  <tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"2\">&nbsp;</td>
  </tr>
  <tr> 
    <td></td>
  </tr>
  <tr> 
    <td colspan=\"3\" >
	$mappy
	</td>
  </tr>
  <tr> 
    <td colspan=\"3\" >&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"3\" ><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><strong>Composition 
        des Equipes</strong></font></div></td>
  </tr>
</table>
<table width=\"600\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
  <tr> 
    <td width=\"49%\">&nbsp;</td>
    <td width=\"2%\" rowspan=\"9\" align=\"center\" > <div align=\"center\"> 
        <center>
          <hr align=\"center\" width=\"1\" size=\"125\" noshade color=\"#000000\">
        </center>
      </div></td>
    <td width=\"49%\" >&nbsp;</td>
  </tr>
  <tr> 
    <td > <div align=\"center\"><strong><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">-=K<font color=\"#CC0000\">1der</font>=-</font></strong></div>
      <div align=\"center\"></div></td>
    <td > <div align=\"center\"></div>
      <div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>$mechants</b></font></b></font></div></td>
  </tr>
  <tr> 
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_k1</font></div></td>
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_m1</font></div></td>
  </tr>
  <tr> 
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_k2</font></div></td>
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_m2</font></div></td>
  </tr>
  <tr> 
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_k3</font></div></td>
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_m3</font></div></td>
  </tr>
  <tr> 
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_k4</font></div></td>
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_m4</font></div></td>
  </tr>
  <tr> 
    <td><div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_k5</font></div></td>
    <td><div align=\"center\"></div>
      <div align=\"center\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$jou_m5</font></div></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td width=\"49%\">&nbsp;</td>
    <td width=\"49%\">&nbsp;</td>
  </tr>
  $comm2
</table>";
?>