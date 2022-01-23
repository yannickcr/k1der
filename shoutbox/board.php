<?
/*----------------------------------------
            K1der Shoutbox 1.7 Beta7
               par Country
              www.k1der.net
----------------------------------------*/

// Description : Fichier principal contenant le shoutbox, la page d'aide et l'historique

// Configuration SQL : voir le fichier config.php
if(!file_exists("config.php")) header("location:install.php");
else include "config.php";
include "include/fonctions.php";

// Ajout d'un message
if ($_POST && $_GET["act"] == "add") add_mess($_POST);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>K1der Shoutbox 1.7</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="include/styles.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="include/scripts.js"></script>
</head>
<?
if ($_GET["act"]== 'histo' || $scroll=="oui") $scrolling="auto";
else $scrolling="hidden";
?>
<body style="overflow:<?=$scrolling;?>;">
<div id="bulle"></div>
<?
// Affichage de l'aide
if ($_GET["act"] == 'help')
{
?>
<table width="100%" border="0" cellspacing="3" cellpadding="0">
	<tr> 
		<td width="50%" align="center"><b>Code</b></td>
		<td width="50%" align="center"><b>Smiley</b></td>
	</tr>
	<?
	foreach($smileys as $code => $image) {
	?>
	<tr> 
		<td width="50%" align="center"><?=$code;?></td>
		<td width="50%" align="center"><img alt="<?=$code;?>" src="<?=$rep_smileys."/".$image;?>" /></td>
	</tr>
	<? } ?>
</table>
<br/>
<div align="center">
	[ <a href="javascript:self.close()">Fermer</a> ]<br/><br/>
	<b>Script : </b><br /><a href="http://www.k1der.net" target="_blank">K1der Shoutbox 1.7 Beta7</a>
</div>
</body>
</html>
<?
exit;
}
?>
<?
// Si on veut afficher tous les messages
if($_GET["page"]) $page=$_GET["page"];
else $page=1;
$start=50*($page-1);
if ($_GET["act"] == 'histo') $req = sql("SELECT * FROM ".$sql["table"]." ORDER BY id DESC LIMIT ".$start.",50");
// Sinon affichage normal
else $req = sql("SELECT * FROM ".$sql["table"]." ORDER BY id DESC LIMIT 0,".$nb_mess);
$nbre=mysql_fetch_row(sql("SELECT COUNT(*) FROM ".$sql["table"])); //Comptage du nombre de messages

if($scroll=="oui") $barre=18;
else $barre=0;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<?
  if ($_GET["act"] != 'histo')
	{
		if ($_COOKIE["shoutbox_pseudo"]) $contenu=$_COOKIE["shoutbox_pseudo"];	
		else $contenu="pseudo";
		$hauteur2=count($smileys)*22+65;
		if($nbre[0]>1) $esse="s";
		else unset($esse);
		if($pl_liens=="haut") {
		$hauteur++;
		?>
 <tr>
	<td align="center">[ <? if($lien_adm==1) echo "<a href=\"admin.php\" target=\"_blank\">Admin</a> : "; ?><a href="javascript:openscript('<?=$_SERVER["SCRIPT_NAME"];?>?act=histo','<?=($largeur+26);?>','600')"><? if($lien_adm==1) echo "Histo."; else echo "Historique"; ?></a> : <a href="javascript:openscript('<?=$_SERVER["SCRIPT_NAME"];?>?act=help','150','<?=$hauteur2;?>')">Aide</a> ]</td>
 </tr>
	 <?
	 }
	 ?>
 <tr> 
  <td align="center"><br />
   <form name="form" action="<?=$_SERVER["SCRIPT_NAME"];?>?act=add" method="post">
   <?
	 if($nb_posts) echo "<b>".$nbre[0]."</b><br/>message".$esse." posté".$esse."<br/><br/>";
	 else $hauteur+=35;
	 ?>
   <input name="pseudo" onfocus="if(this.value=='<?=$contenu?>') this.value=''" value="<?=$contenu?>" size="17" maxlength="<?=$nb_caracp;?>" /><br/>
   <input name="message" onfocus="if(this.value=='message') this.value=''" value="message" size="17" maxlength="<?=$nb_carac;?>" /><br/>
   <input type="submit" value="Poster" name="Submit" /><br/><br/>
   [ <a href="<?=$_SERVER["SCRIPT_NAME"];?>">Actualiser</a> ] 
   </form><br/>
  </td>
 </tr>
 <?
 } else {
 ?>
 <tr>
	<td style="text-align:center; font-weight:bold;">Historique</td>
 </tr>
 <tr>
	<td>&nbsp;</td>
 </tr>
 <tr>
	<td style="text-align:right;"><?=pagination($page,"act=histo",50,$nbre[0]);?></td>
 </tr>
 <?
 }
 ?>
 <tr>
  <td>
	<?
	if($_GET["act"]!="histo") {
		echo "<div style=\"overflow:".$scrolling.";height:".($hauteur-160)."px;\">";
		$largeur=$largeur-$barre;
	} else $largeur="100%";
	?>
   <table width="<?=$largeur;?>" class="liste" cellspacing="1" cellpadding="2">
   <?
   $style="td1";
   while($disp = mysql_fetch_array($req)) {
    $date = date("d/m/Y",$disp["timestamp"]);
    $heure = date("H:i",$disp["timestamp"]);
    $mess = replace_aff(substr(stripslashes($disp["mess"]),0,$nb_carac));
    $pseudo = stripslashes($disp["pseudo"]) ;
		if(array_search($pseudo,$lesadmins)===0) $type="a";
		else if(in_array($pseudo,$lesadmins)) $type="m";
		else $type="v";
   ?>
 <tr>
     <td width="<?=$largeur;?>" class="<?=$style;?>">
      <a class="pseudo<?=$type;?>" onmouseover="affiche('','le <?=$date;?>&lt;br/&gt;à <?=$heure;?>')" onmouseout="affiche('cache')"><?=$pseudo;?></a> : <?=$mess."\n";?>
     </td>
    </tr>
    <?
    if ($style=="td1") $style="td2";
    else $style="td1";
    }
    ?>
	</table>
	<? if($_GET["act"]!="histo") echo "</div>"; ?>
  </td>
 </tr>
<? if($_GET["act"]=="histo") { ?>
 <tr>
	<td style="text-align:right;"><?=pagination($page,"act=histo",50,$nbre[0]);?></td>
 </tr>
<? } else if($pl_liens=="bas") {
?>
 <tr>
	<td height="10px"></td>
 </tr>
 <tr>
	<td align="center">[ <? if($lien_adm==1) echo "<a href=\"admin.php\" target=\"_blank\">Admin</a> : "; ?><a href="javascript:openscript('<?=$_SERVER["SCRIPT_NAME"];?>?act=histo','<?=($largeur+26);?>','600')"><? if($lien_adm==1) echo "Histo."; else echo "Historique"; ?></a> : <a href="javascript:openscript('<?=$_SERVER["SCRIPT_NAME"];?>?act=help','150','<?=$hauteur2;?>')">Aide</a> ]</td>
 </tr>
<? } ?>
</table>
</body>
</html>