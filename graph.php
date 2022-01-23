<?
  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM matches order by orderdate ASC,id ASC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$width = 28+($nbre*24);
?> 
<table width="<? echo $width; ?>" height="250" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="1" height="250" rowspan="3"><img src="images/rnd.gif" width="20" height="250"></td>
    <td height="124" valign="bottom"> &nbsp; 
      <?
	  $requete  = "SELECT * FROM matches order by orderdate ASC,id ASC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  

	while($disp = mysql_fetch_array($req))
	{
	$gr1 = $disp[score_k1]*100;
	$gr2 = $disp[score_k1]+$disp[score_me];
	$gr1 = $gr1/$gr2;
	
	if ($disp[score_me] == '')
	{
	$disp[score_me] = "0";
	}
	if ($disp[score_k1] == '')
	{
	$disp[score_k1] = "0";
	}
	
	?><img src="images/v.gif" width="20" height=<? echo $gr1; ?> align="absbottom" alt="<? echo "-=K1der=- $disp[score_k1] - $disp[score_me] $disp[mechants]"; ?>">
	<? }
	?></td>
  </tr>
  <tr> 
    <td><img src="images/dot.gif" width="100%" height="1"></td>
  </tr>
  <tr> 
    <td height="124" valign="top">&nbsp; 
      <?
	  $requete  = "SELECT * FROM matches order by orderdate ASC,id ASC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
	while($disp = mysql_fetch_array($req))
	{
	$gr1 = $disp[score_me]*100;
	$gr2 = $disp[score_me]+$disp[score_k1];
	$gr1 = $gr1/$gr2;
	
	if ($disp[score_me] == '')
	{
	$disp[score_me] = "0";
	}
	if ($disp[score_k1] == '')
	{
	$disp[score_k1] = "0";
	}
	
	?><img src="images/r.gif" width="20" height=<? echo $gr1; ?> align="top"  alt="<? echo "-=K1der=- $disp[score_k1] - $disp[score_me] $disp[mechants]"; ?>">
	<?
	}
	?></td>
  </tr>
</table>
