<?php
function trouverlataille($u)
{
$ourhead = "";
$url=parse_url($u); 
$host=$url["host"]; 
$path=$url["path"]; 

$fp = fsockopen($host, 80,$errno,$errstr, 20); 
if(!$fp) { 
    echo("error"); 
    exit (); 
} else { 
    fputs($fp,"HEAD $u HTTP/1.1\r\n"); 
    fputs($fp,"HOST: dummy\r\n"); 
    fputs($fp,"Connection: close\r\n\r\n"); 

    while (!feof($fp)) { 
        $ourhead = sprintf("%s%s", $ourhead, fgets ($fp,128)); 
    }
} 
fclose ($fp);

$split_head = explode("Content-Length: ",$ourhead);
$size = round(abs($split_head[1]));
$unit = "Octets";

if ($size > 1024)
{
$size = round($size/1024,2);
$unit = "Ko";

}

if ($size > 1024)
{
$size = round($size/1024,2);
$unit = "Mo";
}

return "<b>$size</b> $unit";
}
?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>

        <div align="center">
  <table width="500" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
      <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
    </tr>
    <tr> 
      <td width="442" height="20" background="images/fond.gif"><img style="float:left;" src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Download</b>=-</font></b></font></td>
    </tr>
  </table>
</div>
<br/>
<?php
if ($_GET["id"] == '')
{
?>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <?php
    if ($_GET["type"] == '') $type = "k1der";
	else $type = $_GET["type"];
	$i = 0;
    $requete  = "SELECT * FROM cats_down WHERE type='$type' ORDER by id";
	$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
	while($disp = mysql_fetch_array($req)) {
		$reqCOMMENT = mysql_query("SELECT * FROM liens_down WHERE cat='$disp[id]' ORDER by id");
		$resCOMMENT = mysql_num_rows($reqCOMMENT);
		if ($resCOMMENT > 1) $resCOMMENT = "$resCOMMENT Fichiers";
		else $resCOMMENT = "$resCOMMENT Fichier";
		echo "<td><center><font face='Verdana, Arial, Helvetica, sans-serif' size='2'><b><a href=index.php?page=download&type=$type&id=$disp[id]>$disp[nom]</a></b><br/><font size='1'>$resCOMMENT</font></font></center></td>";
		$i++;
		if ($i == 4) {
			echo "</tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr>";
			$i = 0;
		}
	}
	?>
  </tr>
</table>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Derniers 
        fichiers ajout&eacute;s</font></strong></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?php
  $i = 1;
  $req = mysql_query("SELECT * FROM liens_down ORDER by id DESC limit 0,10");
  //$resCOMMENT = mysql_num_rows($reqCOMMENT);
  while($disp = mysql_fetch_array($req))
  {
  $requete2  = "SELECT * FROM cats_down WHERE id='$disp[cat]'";
  $req2 = mysql_query($requete2) or die('Erreur SQL !<br/>'.$requete2.'<br/>'.mysql_error());
  $disp2 = mysql_fetch_array($req2);
  if ($disp[taille] == '') { $disp[taille] = 0; }
  $disp2[type] = ucfirst($disp2[type]);
  echo "$i.<a href=index.php?page=down_comments&id=$disp[id]>$disp[nom]</a><font size=1><img src=images/fleche_noire.gif>$disp2[type]<img src=images/fleche_noire.gif>$disp2[nom]</font><br/>";
  $i++;
  }
  ?>
      </font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td> <div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Top 
        10</font></strong></div></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?php
  $req = mysql_query("SELECT * FROM liens_down ORDER by taille DESC limit 0,10");
  //$resCOMMENT = mysql_num_rows($reqCOMMENT);
  while($disp = mysql_fetch_array($req))
  {
  $requete2  = "SELECT * FROM cats_down WHERE id='$disp[cat]'";
  $req2 = mysql_query($requete2) or die('Erreur SQL !<br/>'.$requete2.'<br/>'.mysql_error());
  $disp2 = mysql_fetch_array($req2);
  if ($disp[taille] == '') { $disp[taille] = 0; }
  $disp2[type] = ucfirst($disp2[type]);
  echo "<b>$disp[taille]</b> fois<img src=images/fleche_noire.gif><a href=index.php?page=down_comments&id=$disp[id]>$disp[nom]</a><font size=1><img src=images/fleche_noire.gif>$disp2[type]<img src=images/fleche_noire.gif>$disp2[nom]</font><br/>";
  }
  ?>
      </font></td>
  </tr>
</table>
<?php
}
    if ($_GET["type"] == '') $type = "k1der";
	else $type = $_GET["type"];
$requete  = "SELECT * FROM cats_down WHERE type='$type' && id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$nbre =mysql_num_rows($req);

  while($disp = mysql_fetch_array($req))
  {
  ?>
  <font size="4" face="Arial, Helvetica, sans-serif"><strong><?php echo $disp[nom]; ?></strong><br/><br/>
<font size="2">Classer par <a href="index.php?page=download&type=<?php echo $disp[type]; ?>&id=<?php echo $disp[id]; ?>&lordre=id">Date</a> 
- <a href="index.php?page=download&type=<?php echo $disp[type]; ?>&id=<?php echo $disp[id]; ?>&lordre=nom">Nom</a> 
- <a href="index.php?page=download&type=<?php echo $disp[type]; ?>&id=<?php echo $disp[id]; ?>&lordre=taille">Download</a> 
- <a href="index.php?page=download&type=<?php echo $disp[type]; ?>&id=<?php echo $disp[id]; ?>&lordre=pop">Popularité</a> 
</font></font><br/>
<br/>
<?php
if ($lordre == '') { $lordre = "id"; }

if (($lordre == "taille") or ($lordre == "pop"))
{
$reqCOMMENT = mysql_query("SELECT * FROM liens_down WHERE cat='$disp[id]' ORDER by $lordre DESC");
}
else
{
$reqCOMMENT = mysql_query("SELECT * FROM liens_down WHERE cat='$disp[id]' ORDER by $lordre");
}
$resCOMMENT = mysql_num_rows($reqCOMMENT);
while($disp2 = mysql_fetch_array($reqCOMMENT))
{
?>
  <?php 
  if ($disp2[img] != '')
  {
  ?>
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
    <tr> 
      <td colspan="2"><font size="2"face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo $disp2[nom]; ?></b></font></td>
    </tr>
    <tr> 
      <td colspan="2"><table width="500" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr> 
            <td> <table border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
                <tr> 
                  <td bordercolor="#000000"> <div align="center"><img onload=this.style.filter='progid:DXImageTransform.Microsoft.Shadow(color=#000000,direction=135,strength=3)' src="<?php echo $disp2[img]; ?>" border="0"></div></td>
                </tr>
              </table></td>
            <td width="100%" colspan="4" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $disp2[descr]; ?></font></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2"><div align="center">&nbsp; </div></td>
    </tr>
    <tr> 
      <td width="30%"> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note: 
      	  <?php
	  $thenote = 0;
	  $j = 0;
	  $reqCOMMENTS = mysql_query("SELECT * FROM downcomments WHERE id_down='$disp2[id]' ORDER BY id DESC");
	  $resCOMMENTS = mysql_num_rows($reqCOMMENTS);
	  if ($resCOMMENTS == '0')
	  {
	  echo "<i> Pas noté </i>";
	  echo "<br/><a href='index.php?page=down_comments&id=$disp2[id]'> Aucun commentaire</a>";
	  }
	  else
	  {
	  while($dispc = mysql_fetch_array($reqCOMMENTS))
	  {
	  $thenote = $dispc[note]+$thenote;
	  }
	  $realnote = $thenote/$resCOMMENTS;
	  if ($realnote != $disp2[pop])
	  {
	  mysql_query("UPDATE liens_down SET pop='$realnote' WHERE id='$disp2[id]'");
	  }
	  $nonote = round($thenote/$resCOMMENTS,1);
	  $thenote = floor($thenote/$resCOMMENTS);
	  
	  $result = $nonote-$thenote;
	  $result = $result-0.5;
	  if ($result >= 0)
	  {
	  if ($result >= 0.2)
	  {
	  $demi = "<img src='images/star.gif' width='16' height='16' align='absmiddle'>";
	  }
	  else
	  {
	  $demi = "<img src='images/stardemi.gif' width='16' height='16' align='absmiddle'>";
	  }
	  }
	  else
	  {
	  if ($result <= -0.2)
	  {
	  $demi = "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
	  }
	  else
	  {
	  $demi = "<img src='images/stardemi.gif' width='16' height='16' align='absmiddle'>";
	  }
	  }
	  $n = $thenote;
	  while($n > 0.5)
	  {
	  echo "<img src='images/star.gif' width='16' height='16' align='absmiddle'>";
	  $j++;
	  $n--;
	  }
	  if ($thenote != $nonote)
	  {
	  echo $demi;
	  $j++;
	  }
	  $m = 6-round($thenote)-1;
	  while($m > 0)
	  {
	  echo "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
	  $j++;
	  $m--;
	  }
	  if ($j < 6)
	  {
	  echo "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
	  }
	  if ($resCOMMENTS > 1)
	  {
	  $kum = "s";
	  }
	  else
	  {
	  $kum = "";
	  }
	  echo "<br/><a href='index.php?page=down_comments&id=$disp2[id]'> $resCOMMENTS commentaire$kum</a>";
	  }
	  ?>
      <br/>
      Download&eacute; <b> 
      <?php if ($disp2[taille] == '') { echo '0'; } else { echo $disp2[taille]; } ?>
      </b> fois 
      <?php
			if (($disp2[cat] != "12") && ($disp2[cat] != "24"))
			{
			if(ereg("http://www.k1der.net",$disp2[lien]))
			{
			?>
      <br/>
      Taille : 
      <?php
			$size = str_replace("http://www.k1der.net/","",$disp2[lien]);
			$size = filesize($size);
			$unit = "Octets";

			if ($size > 1024)
			{
			$size = $size/1024;
			$unit = "Ko";
			}
			
			if ($size > 1024)
			{
			$size = $size/1024;
			$unit = "Mo";
			}
			
			$size= round($size,2);
			
			echo "<b>$size</b> $unit";
			}
			}
			?>
      </font></div></td>
      <td width="70%">
	  <?php
	  //if ($disp2[cat] != "12")
	 // {
	  ?>
	  <table width="140" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr> 
            <td width="139" bordercolor="#000000" bgcolor="#CC0000"> <div align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href=<?php echo "dlcount.php3?url=$disp2[lien]"; ?> target="_blank" class=type1><font color="#000000">D</font><font color="#FFFFFF">ownloader</font></a></font></b></div></td>
          </tr>
        </table>
  <?php
 /* }
  else
  {
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=800><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">800x600</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1024><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1024x468</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1152><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1152x864</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1280><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1280x1024</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1600><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1600x1200</font></a>";
  }*/
  }
  else
  {
  ?>
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
      <tr> 
      <td colspan="2"><font size="2"face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo $disp2[nom]; ?></b></font></td>
    </tr>
    <tr> 
      <td colspan="2"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $disp2[descr]; ?></font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
        <td width="30%"> 
          <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note: 
            <?php
	  $thenote = 0;
	  $j = 0;
	  $reqCOMMENTS = mysql_query("SELECT * FROM downcomments WHERE id_down='$disp2[id]' ORDER BY id DESC");
	  $resCOMMENTS = mysql_num_rows($reqCOMMENTS);
	  if ($resCOMMENTS == '0')
	  {
	  echo "<i> Pas noté </i>";
	  echo "<br/><a href='index.php?page=down_comments&id=$disp2[id]'> Aucun commentaire</a>";
	  }
	  else
	  {
	  while($dispc = mysql_fetch_array($reqCOMMENTS))
	  {
	  $thenote = $dispc[note]+$thenote;
	  }
	  $realnote = $thenote/$resCOMMENTS;
	  if ($realnote != $disp2[pop])
	  {
	  mysql_query("UPDATE liens_down SET pop='$realnote' WHERE id='$disp2[id]'");
	  }
	  $nonote = round($thenote/$resCOMMENTS,1);
	  $thenote = floor($thenote/$resCOMMENTS);
	  
	  $result = $nonote-$thenote;
	  $result = $result-0.5;
	  if ($result >= 0)
	  {
	  if ($result >= 0.2)
	  {
	  $demi = "<img src='images/star.gif' width='16' height='16' align='absmiddle'>";
	  }
	  else
	  {
	  $demi = "<img src='images/stardemi.gif' width='16' height='16' align='absmiddle'>";
	  }
	  }
	  else
	  {
	  if ($result <= -0.2)
	  {
	  $demi = "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
	  }
	  else
	  {
	  $demi = "<img src='images/stardemi.gif' width='16' height='16' align='absmiddle'>";
	  }
	  }
	  $n = $thenote;
	  while($n > 0.5)
	  {
	  echo "<img src='images/star.gif' width='16' height='16' align='absmiddle'>";
	  $j++;
	  $n--;
	  }
	  if ($thenote != $nonote)
	  {
	  echo $demi;
	  $j++;
	  }
	  $m = 6-round($thenote)-1;
	  while($m > 0)
	  {
	  echo "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
	  $j++;
	  $m--;
	  }
	  if ($j < 6)
	  {
	  echo "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
	  }
	  if ($resCOMMENTS > 1)
	  {
	  $kum = "s";
	  }
	  else
	  {
	  $kum = "";
	  }
	  echo "<br/><a href='index.php?page=down_comments&id=$disp2[id]'> $resCOMMENTS commentaire$kum</a>";
	  }
	  ?>
      <br/>Download&eacute; <b><?php echo $disp2[taille]; ?></b> fois 
            <?php
			if (($disp2[cat] != "12") && ($disp2[cat] != "24"))
			{
			if(ereg("http://www.k1der.net",$disp2[lien]))
			{
			?>
            <br/>
            Taille : 
            <?php
			$size = str_replace("http://www.k1der.net/","",$disp2[lien]);
			$size = filesize($size);
			$unit = "Octets";

			if ($size > 1024)
			{
			$size = $size/1024;
			$unit = "Ko";
			}
			
			if ($size > 1024)
			{
			$size = $size/1024;
			$unit = "Mo";
			}
			
			$size= round($size,2);
			
			echo "<b>$size</b> $unit";
			}
			}
			?>
            </font></div></td>
        <td width="70%"> 
          <?php
	 // if ($disp2[cat] != "12")
	 // {
	  ?>
          <table width="140" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr> 
            <td width="139" bordercolor="#000000" bgcolor="#CC0000"> <div align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href=<?php echo "dlcount.php3?url=$disp2[lien]"; ?> target="_blank" class=type1><font color="#000000">D</font><font color="#FFFFFF">ownloader</font></a></font></b></div></td>
          </tr>
        </table>
  <?php
 /* }
  else
  {
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=800><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">800x600</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1024><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1024x468</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1152><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1152x864</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1280><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1280x1024</font></a>&nbsp;|&nbsp;";
  echo "<a target=_blank href=dlcountimg.php3?url=$disp2[lien]&img_x=1600><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">1600x1200</font></a>";
  }*/
  }
  ?></td>
    </tr>
  </table>
  <font color="#FFFFFF" size="2"face="Verdana, Arial, Helvetica, sans-serif"><b></b></font><br/>
  <br/>
  <br/>
        <br/>
        <?php
}
	}
	?>
        <br/>
      
</body>
</html>
