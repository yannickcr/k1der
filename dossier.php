<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_jumpMenuGo(selName,targ,restore){ //v3.0
  var selObj = MM_findObj(selName); if (selObj) MM_jumpMenu(targ,selObj,restore);
}
//-->
</script>
</head>

<body>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Lire 
      un Dossier=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<?
$req1 = MYSQL_QUERY("SELECT * FROM dossiers WHERE id='$id'");
$nbre1 =mysql_num_rows($req1);
$disp1 = mysql_fetch_array($req1);
$letitre = $disp1[titre];
if ($p == '')
{
$p = '1';
}
$req = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' && page='$p'");
$nbre =mysql_num_rows($req);
/*if (($nbre1 == "0") && ($nbre == "0"))
{
$req = MYSQL_QUERY("SELECT * FROM dossiers WHERE id='$id'");
$nbre =mysql_num_rows($req);
}*/
$disp = mysql_fetch_array($req);
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $letitre; ?></strong></font></td>
  </tr>
  <tr> 
    <td colspan="2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[titrepage]; ?></font></strong></td>
  </tr>
  <tr> 
    <td colspan="2"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <?
	if ($p == "1")
	{
	?>
        <tr> 
          <? if ($disp1[image] != '') { ?>
          <td><img src="<? echo $disp1[image]; ?>" border="0"></td>
          <? } ?>
          <td width="100%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp1[resume]; ?></font></td>
        </tr>
        <?
  }
  ?>
      </table></td>
  </tr>
  <tr> 
    <td colspan="2"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note 
        : 
        <?
	  $thenote = 0;
	  $j = 0;
	  $reqCOMMENTS = mysql_query("SELECT * FROM dossiercomments WHERE id_dossier='$id' ORDER BY id DESC");
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
  <tr> 
    <td colspan="2"> <br/> 
      <?
		$req2 = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='".$_GET['id']."' ORDER BY page");
		$nbre2 =mysql_num_rows($req2);
		if ($nbre2 > 1)
		{
	?>
      <form name="form1">
        <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Navigation 
          Rapide : </font> 
          <select name="menu1">
            <?
		while($disp2 = mysql_fetch_array($req2))
		{
		?>
            <option value="index.php?page=dossier&id=<? echo $id; ?>&p=<? echo $disp2[page]; ?>"><? echo $disp2[titrepage]; ?></option>
            <?
		}
		?>
          </select>
          <input type="button" name="Button1" value="Go" onClick="MM_jumpMenuGo('menu1','parent',0)">
        </div>
      </form>
      <br/> 
      <?
	}
	?>
    </td>
  </tr>
  <tr> 
    <td colspan="2"><? echo $disp[text]; ?> </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr> 
    <td width="50%" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
	if ($p == 0)
	{
	$pa = 1;
	}
	else
	{
	$pa = $p;
	}
	$pa = $pa-1;
	$req2 = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' && page='$pa'");
	$nbre2 =mysql_num_rows($req2);
	$disp2 = mysql_fetch_array($req2);
	if ($nbre2 != 0)
	{
	echo "<a href='index.php?page=dossier&id=$id&p=$pa'><< Page $pa: $disp2[titrepage]</a>";
	}
	?>
      </font></td>
    <td width="50%" align="right" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
	if ($p == 0)
	{
	$pb = 1;
	}
	else
	{
	$pb = $p;
	}
	$pb = $pb+1;
	$req2 = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' && page='$pb'");
	$nbre2 =mysql_num_rows($req2);
	$disp2 = mysql_fetch_array($req2);
	if ($nbre2 != 0)
	{
	echo "<a href='index.php?page=dossier&id=$id&p=$pb'>Page $pb: $disp2[titrepage] >></a>";
	}
	?>
      </font></td>
  </tr>
</table>
</body>
</html>
