<?php
function trouverlataille($u)
{
$ourhead = "";
$url=parse_url($u); 
$host=$url["host"]; 
$path=$url["path"]; 

$fp = fsockopen($host, 80, $errno, $errstr, 20); 
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

        
<div align="center"></div>
<blockquote> <br/>
  <?php
	  include 'config.inc.php3';
	  $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
	  mysql_select_db("$dbbase",$db) or Die("Base Down !");
$requete  = "SELECT * FROM liens_down WHERE id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$nbre =mysql_num_rows($req);

?>
  <?php
  while($disp2 = mysql_fetch_array($req))
  {
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
            <td width="100%" colspan="4" valign="top" align="justify"><img onload=this.style.filter='progid:DXImageTransform.Microsoft.Shadow(color=#000000,direction=135,strength=3)' src="<?php echo $disp2[img]; ?>" border="0" align="left">
            <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $disp2[descr]; ?></font></td>
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
	  }
	  else
	  {
	  while($dispc = mysql_fetch_array($reqCOMMENTS))
	  {
	  $thenote = $dispc[note]+$thenote;
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
	  }
	  ?>
      <br/>
      Download&eacute; <b> 
      <?php if ($disp2[taille] == '') { echo '0'; } else { echo $disp2[taille]; } ?>
      </b> fois 
      <?php
			if ($disp2[cat] != "12")
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
	  //{
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
	  }
	  else
	  {
	  while($dispc = mysql_fetch_array($reqCOMMENTS))
	  {
	  $thenote = $dispc[note]+$thenote;
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
	  }
	  ?>
            <br/>
            Download&eacute; <b><?php echo $disp2[taille]; ?></b> fois 
            <?php
			if ($disp2[cat] != "12")
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
  <br/>
        <br/>
        <?php
}
	?>

</div>

<?php
#-=-=-=-=-=-=-=- COMMENTAIRES -=-=-=-=-=-=-=-#
$reqCOMMENT = mysql_query("SELECT * FROM downcomments WHERE id_down='$id' ORDER BY id DESC");
$resCOMMENT = mysql_num_rows($reqCOMMENT);
?>

<script language="Javascript">
function Comments(data){
window.open(data,'Sondage','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=1,width=430,height=335,left=0,right=0');
}
</script>

<br/>
<div align="center">
  <center>
    <table width="180" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#FFFFFF">
      <tr> 
        <td valign="middle" bgcolor="#DE0200" bordercolor="#000000"> 
          <div align="center"><a href="Javascript:Comments('ajouter_comm_down.php?id=<?php echo $id; ?>')"> 
            <font style="<?php echo $TitreNews; ?>">Ajouter un commentaire</font> 
            </a></div>
        </td>
      </tr>
    </table>
    <br/>
    <br/>
    <table border="0" cellspacing="0" cellpadding="0" width="500" bgcolor="#000000">
      <tr>
      <td width="100%">
        <div align="center">
            <table border="0" cellspacing="1" width="500" cellpadding="3">
              <?php
			  while($disp = mysql_fetch_array($reqCOMMENT))
			  {
			  ?>
              <tr> 
                <td width="50%" bgcolor="<?php echo $bgcolor_haut; ?>" background="../images/fond.gif"><font style="<?php echo $Comment ?>"><b> 
                  <?php echo $disp[auteur]; ?> </b></font><font style="<?php echo $Comment ?>"> 
                  <?php echo " - $disp[date] à $disp[heure]"; ?> </font></td>
                <td width="20%" nowrap bgcolor="#FFFFFF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note:</font> 
                  <?php
				$n = $disp[note];
				while($n > 0)
				{
				echo "<img src='images/star.gif' width='16' height='16' align='absmiddle'>";
				$n--;
				}
				$m = 6-$disp[note];
				while($m > 0)
				{
				echo "<img src='images/star2.gif' width='16' height='16' align='absmiddle'>";
				$m--;
				}
				?>
                </td>
              </tr>
              <tr> 
                <td width="100%" colspan="2" bgcolor="<?php echo $bgcolor_corp; ?>"><font style="<?php echo $Comment2 ?>"><?php echo "<blockquote><p style=\"text-align: justify\">$disp[text]</p></blockquote>"; ?></font></td>
              </tr>
              <?php
}
if ($resCOMMENT == '0') // Aucun commentaires
{
?>
              <tr> 
                <td width="100%" colspan="2" align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Aucun 
                  commentaires</b></font></td>
              </tr>
              <?php
}
?>
            </table>
        </div>
      </td>
    </tr>
  </table>
        <br/>
      
</body>
</html>
