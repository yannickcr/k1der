<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Dossiers=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<?
$req = MYSQL_QUERY("SELECT * FROM dossiers WHERE conf='1' ORDER BY id DESC");
$nbre =mysql_num_rows($req);
while($disp = mysql_fetch_array($req))
{
if ($disp[image] != '')
{
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $disp[titre]; ?><br/>
      </strong><font size="1">le <b><? echo $disp[date]; ?></b> par 
      <?
	  $req2 = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$disp[auteur]'");
	  if(mysql_num_rows($req2)!=0) {
	  $disp2 = mysql_fetch_array($req2);
	  
	  ?>
      <b>
	<?
	if($disp2[e_mail] != '')
	{
	$mail_0 = strstr ($disp2[e_mail] , "@");
	$mail_1 = str_replace ($mail_0,"",$disp2[e_mail]);
	$mail_2 = str_replace ("@","",$mail_0);
	?>
	
	  <script language="JavaScript" type="text/javascript">
	  var un="<? echo $mail_1; ?>";
	  var deux = "<? echo $mail_2; ?>";
	  var texteCrypte="05204A0E420E22556659262B1B1A1A53";
	  var texteCrypte2="1B7F";
	  var texteCrypte3="056E0B58";
	  document.write(decrypte(texteCrypte)+un+"[AT]"+deux+decrypte(texteCrypte2)+"<? echo $disp[auteur]; ?>"+decrypte(texteCrypte3));
	  </script>
	<?
	}
	else
	{
	echo $signature;
	}
	} else echo $disp['auteur'];
	?>
	</b> 
      </font></font></td>
  </tr>
  <tr> 
    <td width="100"><div align="center"><a href="index.php?page=dossier&id=<? echo $disp[id]; ?>"><img src="<? echo $disp[image]; ?>" border="0"></a></div></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[resume]; ?> 
      <strong><a href="index.php?page=dossier&id=<? echo $disp[id]; ?>"><br/>
      Lire le dossier...</a></strong></font></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note 
        : 
        <?
	  $thenote = 0;
	  $j = 0;
	  $reqCOMMENTS = mysql_query("SELECT * FROM dossiercomments WHERE id_dossier='$disp[id]' ORDER BY id DESC");
	  $resCOMMENTS = mysql_num_rows($reqCOMMENTS);
	  if ($resCOMMENTS == '0')
	  {
	  echo "<i> Pas noté </i>";
	  echo "<br/><a href='index.php?page=dossier_comments&id=$disp[id]'> Aucun commentaire</a>";
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
	  if ($resCOMMENTS > 1)
	  {
	  $kum = "s";
	  }
	  else
	  {
	  $kum = "";
	  }
	  echo "<br/><a href='index.php?page=dossier_comments&id=$disp[id]'> $resCOMMENTS commentaire$kum</a>";
	  }
	  ?>
        </font></div></td>
  </tr>
</table>
<br/><br/><br/>
<?
}
else
{
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $disp[titre]; ?><br/>
      </strong><font size="1">le <b><? echo date("d/m/Y",$disp[date]); ?></b> 
      par 
      <?
	  $req2 = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$disp[auteur]'");
	  $disp2 = mysql_fetch_array($req2);
	  ?><b>
	<?
	if($disp2[e_mail] != '')
	{
	$mail_0 = strstr ($disp2[e_mail] , "@");
	$mail_1 = str_replace ($mail_0,"",$disp2[e_mail]);
	$mail_2 = str_replace ("@","",$mail_0);
	?>
	
	  <script language="JavaScript" type="text/javascript">
	  var un="<? echo $mail_1; ?>";
	  var deux = "<? echo $mail_2; ?>";
	  var texteCrypte="05204A0E420E22556659262B1B1A1A53";
	  var texteCrypte2="1B7F";
	  var texteCrypte3="056E0B58";
	  document.write(decrypte(texteCrypte)+un+"[AT]"+deux+decrypte(texteCrypte2)+"<? echo $disp[auteur]; ?>"+decrypte(texteCrypte3));
	  </script>
	<?
	}
	else
	{
	echo $signature;
	}
	?>
	</b> 
      </font></font></td>
  </tr>
  <tr> 
    <td> <div align="center"></div>
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[resume]; ?> 
      <strong><a href="index.php?page=dossier&id=<? echo $disp[id]; ?>"><br/>
      Lire le dossier...</a></strong></font></td>
  </tr>
  <tr>
    <td><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note 
        : 
        <?
	  $thenote = 0;
	  $j = 0;
	  $reqCOMMENTS = mysql_query("SELECT * FROM dossiercomments WHERE id_dossier='$disp[id]' ORDER BY id DESC");
	  $resCOMMENTS = mysql_num_rows($reqCOMMENTS);
	  if ($resCOMMENTS == '0')
	  {
	  echo "<i> Pas noté </i>";
	  echo "<br/><a href='index.php?page=dossier_comments&id=$id'> Aucun commentaire</a>";
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
	  if ($resCOMMENTS > 1)
	  {
	  $kum = "s";
	  }
	  else
	  {
	  $kum = "";
	  }
	  echo "<br/><a href='index.php?page=dossier_comments&id=$id'> $resCOMMENTS commentaire$kum</a>";
	  }
	  ?>
        </font></div></td>
  </tr>
</table>
<br/><br/><br/>
<?
}
}
?>
<p>&nbsp;</p>
</body>
</html>
