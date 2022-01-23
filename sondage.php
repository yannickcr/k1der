<?
if ($vote == '1')
{
$requete  = "SELECT * FROM sondages ORDER BY id DESC LIMIT 0,1";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
$sondage_id=$disp['id'];
$le_vote2 = $le_vote + 10;
$le_vote = $le_vote - 2;
$total = $disp[$le_vote2] + 1;

$ruquete  = "SELECT * FROM config WHERE nom='$badip'";
$ruq = mysql_query($ruquete) or die('Erreur SQL !<br>'.$ruquete.'<br>'.mysql_error());  
$dusp = mysql_fetch_array($ruq);


if (($_COOKIE['sondage'] != $disp[id]) or ($dusp[badip] != $REMOTE_ADDR)) {
mysql_query("UPDATE sondages SET v$le_vote='$total' WHERE id='$sondage_id'");
$time = date("U");
mysql_query("UPDATE config SET valeur='$time' WHERE nom='last_vote'");
mysql_query("UPDATE config SET valeur='$REMOTE_ADDR' WHERE nom='badip'");
}
}
?>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
  </tr>
  <tr> 
    <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b><? if ($old == 1) { echo "Anciens Sondages"; } else { echo "Sondage"; } ?></b>=-</font></b></font></td>
  </tr>
</table>
<?
if ($old != 1)
{

$requete  = "SELECT * FROM sondages ORDER BY id DESC LIMIT 0,1";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
$total = $disp[v1] + $disp[v2] + $disp[v3] + $disp[v4] + $disp[v5] + $disp[v6] + $disp[v7] + $disp[v8] + $disp[v9] + $disp[v10];
?> 
  
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><div align="center"><font size="2"><strong><font face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[titre]; ?></font></strong></font></div></td>
  </tr>
  <tr>
    <td><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">(<? echo $total; ?> 
        vote<? if ($total > 1) { echo "s"; } ?>)</font></div></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <?
	$i = 3;
	$j = 13;

	while($disp[nb] > 0)
	{
	?>
  <tr> 
    <td valign="top"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[$i]; ?></font></td>
  </tr>
  <tr> 
    <td> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
	  if ($total == 0) { $total = 1; }
	$percent = round((($disp[$j]/$total)*100),1);
	?>
      <img src="images/barre2.gif"><img src="images/barre.gif" width="<? echo $percent*4; ?>" height="7"><img src="images/barre3.gif"> 
      <?	echo $percent."% (".$disp[$j].")"; ?>
      </font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <?
	$i++;
	$j++;
	$disp[nb]--;
	}
	?>
  <tr> 
    <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="index.php?page=sondage&old=1">Anciens sondages</a></font></div></td>
  </tr>
</table>
<?
}
if ($old == 1)
{
$requete  = "SELECT * FROM sondages ORDER BY id DESC LIMIT 1,-1";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  

?>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <?
  while($disp = mysql_fetch_array($req))
  {
  $total = $disp[v1] + $disp[v2] + $disp[v3] + $disp[v4] + $disp[v5] + $disp[v6] + $disp[v7] + $disp[v8] + $disp[v9] + $disp[v10];
  ?>
  <tr> 
    <td><div align="center"><font size="2"><strong><font face="Verdana, Arial, Helvetica, sans-serif"><a href="Javascript:ChangeMessage('sond<? echo $disp[id]; ?>')"><font color=black><? echo $disp[titre]; ?></font></a></font></strong></font></div></td>
  </tr>
  <tr> 
    <td><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">(<? echo $total; ?> 
        vote<? if ($total > 1) { echo "s"; } ?>)</font></div></td>
  </tr>
  <tr> 
    <td> <table width="100%" border="0" cellpadding="0" cellspacing="0" id="sond<? echo $disp[id]; ?>" style='display:none;'>
        <?
	$i = 3;
	$j = 13;

	while($disp[nb] > 0)
	{
	?>
        <tr> 
          <td valign="top"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[$i]; ?></font></td>
        </tr>
        <tr> 
          <td> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
	  if ($total == 0) { $total = 1; }
	$percent = round((($disp[$j]/$total)*100),1);
	?>
            <img src="images/barre2.gif"><img src="images/barre.gif" width="<? echo $percent*4; ?>" height="7"><img src="images/barre3.gif"> 
            <?	echo $percent."% (".$disp[$j].")"; ?>
            </font></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <?
	$i++;
	$j++;
	$disp[nb]--;
	}
	?>
      </table></td>
  </tr>
  <?
  }
  ?>
  <tr> </tr>
</table>
<?
}
?>
