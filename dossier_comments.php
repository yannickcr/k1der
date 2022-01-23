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
$req = MYSQL_QUERY("SELECT * FROM dossiers WHERE id='$id'");
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
	  $disp2 = mysql_fetch_array($req2);
		if($disp2[e_mail] != '')
		{
		$mail_0 = strstr ($disp2[e_mail] , "@");
		$mail_1 = str_replace ($mail_0,"",$disp2[e_mail]);
		$mail_2 = str_replace ("@","",$mail_0);
		?>
		<b>
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
    <td width="100"><div align="center"><a href="index.php?page=dossier&id=<? echo $disp[id]; ?>"><img src="<? echo $disp[image]; ?>" border="0"></a></div></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[resume]; ?> 
      <strong><a href="index.php?page=dossier&id=<? echo $disp[id]; ?>"><br/>
      Lire le dossier...</a></strong></font></td>
  </tr>
</table><br/><br/><br/>
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
	  ?>
      <b><a href="mailto:<? echo $disp2[e_mail]; ?>" title="Ecrire à <? echo $disp[auteur]; ?>"><? echo $disp[auteur]; ?></a></b> 
      </font></font></td>
  </tr>
  <tr> 
    <td> <div align="center"></div>
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[resume]; ?> 
      <strong><a href="index.php?page=dossier&id=<? echo $disp[id]; ?>"><br/>
      Lire le dossier...</a></strong></font></td>
  </tr>
</table><br/><br/><br/>
<?
}
}
?>
<?
#-=-=-=-=-=-=-=- COMMENTAIRES -=-=-=-=-=-=-=-#
$reqCOMMENT = mysql_query("SELECT * FROM dossiercomments WHERE id_dossier='$id' ORDER BY id DESC");
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
          <div align="center"><a href="Javascript:Comments('ajouter_comm_dossier.php?id=<? echo $id; ?>')"> 
            <font style="<? echo $TitreNews; ?>">Ajouter un commentaire</font> 
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
              <?
			  while($disp = mysql_fetch_array($reqCOMMENT))
			  {
			  ?>
              <tr> 
                <td width="50%" bgcolor="<? echo $bgcolor_haut; ?>" background="../images/fond.gif"><font style="<? echo $Comment ?>"><b> 
                  <? echo $disp[auteur]; ?> </b></font><font style="<? echo $Comment ?>"> 
                  <? echo " - $disp[date] à $disp[heure]"; ?> </font></td>
                <td width="20%" nowrap bgcolor="#FFFFFF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Note:</font> 
                  <?
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
                <td width="100%" colspan="2" bgcolor="<? echo $bgcolor_corp; ?>"><font style="<? echo $Comment2 ?>"><? echo "<blockquote><p style=\"text-align: justify\">$disp[text]</p></blockquote>"; ?></font></td>
              </tr>
              <?
}
if ($resCOMMENT == '0') // Aucun commentaires
{
?>
              <tr> 
                <td width="100%" colspan="2" align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Aucun 
                  commentaires</b></font></td>
              </tr>
              <?
}
?>
            </table>
        </div>
      </td>
    </tr>
  </table>

<p>&nbsp;</p>
</body>
</html>
