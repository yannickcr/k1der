<?
$texte = htmlentities($texte);
if ($dans == "Forum")
{
?>
<script language="JavaScript">
window.location='http://www.k1der.net/forum/index.php?act=Search&CODE=01&keywords=<? echo $texte; ?>&cat_forum=cat&cats=all&prune=0&namesearch=&exactname=1&joinname=1&search_in=posts&result_type=topics&prune_type=newer&sort_key=last_post&sort_order=desc'
</script>
<?
}

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

if ($dans == "News")
{
//$req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf = '1' ORDER BY id DESC");
$req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf = '1' and news like '%$texte%' or titre like '%$texte%' or signature like '%$texte%' ORDER BY id DESC");
//$res = MYSQL_NUM_ROWS($req);
$nbre =mysql_num_rows($req);
}

if ($dans == "LAN")
{
//$req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf = '1' ORDER BY id DESC");
$req = MYSQL_QUERY("SELECT * FROM calendrier WHERE nom like '%$texte%' or ville like '%$texte%' or adresse like '%$texte%' or site like '%$texte%' ORDER BY nom ASC");
//$res = MYSQL_NUM_ROWS($req);
$nbre =mysql_num_rows($req);
}

if ($dans == "Matches")
{
//$req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf = '1' ORDER BY id DESC");
$req = MYSQL_QUERY("SELECT * FROM matches WHERE mechants like '%$texte%' or loc like '%$texte%' or jou_k1 like '%$texte%' or jou_k2 like '%$texte%' or jou_k3 like '%$texte%' or jou_k4 like '%$texte%' or jou_k5 like '%$texte%' or jou_m1 like '%$texte%'  or jou_m2 like '%$texte%'  or jou_m3 like '%$texte%'  or jou_m4 like '%$texte%' ORDER BY mechants ASC");
//$res = MYSQL_NUM_ROWS($req);
$nbre =mysql_num_rows($req);
}
?>
<br>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="452" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="32" height="42" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
  </tr>
  <tr> 
    <td width="452" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>R&eacute;sultats 
      de la Recherche</b>=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $nbre; ?> 
      </font></strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;sultat<? if ($nbre >= "2") { echo "s"; } ?> pour "<i><? echo $texte; ?></i>"</font>
      <hr size="1" noshade color="#000000"></td>
  </tr>
</table>
<?
if ($dans == "News")
{
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <?
  while($disp = mysql_fetch_array($req))
  {
  $zymois = substr($disp[date],3,7);
  
#=-=-=-=- Comptage des commentaires pour la news -=-=-=-=-=-=-=-=-=-=-=-=-=-#
$caca = "_".$disp[id]."_";
$dodo = mysql_query("SELECT * FROM ib_posts WHERE attach_hits='$caca' && forum_id='1'");
$grosse = mysql_fetch_array($dodo);
$popo = mysql_num_rows($dodo);
$grosseconne = $grosse[topic_id];
$dada = mysql_query("SELECT * FROM ib_posts WHERE topic_id='$grosseconne'");
$nombre = mysql_num_rows($dada);
$nombre = $nombre-1;
?>
                                    <?
if($source!='non'){ $SOURCE = " | <b>$SourceTitle</b> : <a href=\"$url_source\">$nom_source</a> "; }
else{ $SOURCE = ""; }

if ($nombre >= '1')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$disp[titre]</a>";
}
if ($nombre == '1')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$disp[titre]</a>";
}
if ($nombre == '0')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$disp[titre]</a>";
}
if ($grosse[topic_id] <= "3")
{
$reqCOMMENT = mysql_query("SELECT id FROM $TBL_COMMENTAIRES WHERE id_news='$disp[id]'");
$resCOMMENT = mysql_num_rows($reqCOMMENT);

if($resCOMMENT>='2'){ $disdonc = "<a href=\"index.php?page=read_comment&id_news=$disp[id]\" class=\"plein\">$disp[titre]</a>"; }
elseif($resCOMMENT=='1'){ $disdonc = "<a href=\"index.php?page=read_comment&id_news=$disp[id]\" class=\"plein\">$disp[titre]</a>"; }
else{ $disdonc = "<a href=\"index.php?page=read_comment&id_news=$disp[id]\" class=\"plein\">$disp[titre]</a>"; }
}
  
  
  ?>
  <tr> 
    <td width="25"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
      </b><br>
      </font></td>
    <td width="575"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
      <?
	if ($zymois != $zymois1)
	{
	echo "<br>$zymois<br>";
	}
	?>
      </b><? echo $disdonc; ?></font></td>
  </tr>
  <?
  $zymois1 = $zymois;
  }
  ?>
</table>
<?
}
if ($dans == "LAN")
{
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
<?
  while($disp = mysql_fetch_array($req))
  {
?>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="575"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="cal/lan.php?id=<? echo $disp[id]; ?>"><? echo $disp[nom]; ?></a> (<? echo $disp[dep]; ?>)</font></td>
  </tr>
  <?
  }
  ?>
</table>
<?
}
if ($dans == "Matches")
{
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
<?
  while($disp = mysql_fetch_array($req))
  {
?>
  <tr> 
    <td width="25">&nbsp;</td>
    <td width="575"><a href="index.php?page=matches_details&id=<? echo $disp[id]; ?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>-=K<font color="#CC0000">1der</font>=-</b></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> vs <? echo $disp[mechants]; ?></font></a></td>
  </tr>
  <?
  }
  ?>
</table>
<?
}
?>