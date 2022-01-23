<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Exemple d'utilisation du K1der Shoutbox</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="include/styles.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF">
<?
if(!$_GET["demo"]) {
?>
<div style="width:100%;text-align:center; padding-top:200px; font-weight:bold;">
<a target="_blank" style="font-size:24px;" href="http://www.k1der.net/bordel/shoutbox/exemple.php?demo=1">Accèder à la démo</a>
</div>
<?
} else {
?>
<table width="50%" border="0" cellpadding="20" cellspacing="20">
  <tr align="center">
    <td colspan="2"><a href="http://www.k1der.net"><img border="0" src="http://www.k1der.net/images/groslien.gif" alt="K1der | The Chocolat Effect"  /></a></td>
  </tr>
  <tr>
    <td rowspan="3"><? include "index.php"; ?></td>
  </tr>
  <tr>
    <td valign="top">
		Le K1der Shoutbox est une tribune libre o&ugrave; vos visiteurs pourrons laisser de courts messages (longueur personnalisable). Son installation et personnalisation sont tr&egrave;s faciles, il comporte une interface d'administration tr&egrave;s compl&egrave;te (voir ci-dessous).<br />
      <br />
      <b>Shoutbox</b>
			<ul>
			  <li>Postage de message anonyme</li>
		    <li>M&eacute;morisation du pseudo du visiteur (cookie)</li>
		    <li>Mise en forme automatique des liens, emails et smileys</li>
		    <li>Interdit les balises HTML</li>
		    <li>Historique des messages</li>
			</ul>
			<b>Administration</b>
			<ul>
				<li>Module d'installation</li>
				<li>Edition/Suppression des messages</li>
				<li>Ajout/Suppression de mod&eacute;rateur</li>
				<li>Configuration du Shoutbox</li>
				<li>Modification de l'apparence</li>
			</ul>
			</td>
  </tr>
  <tr>
    <td align="center" valign="top"><a href="admin.php" class="Style2" target="_blank">Acc&egrave;s &agrave; l'administration</a><br />
        <span class="Style5">login: admin<br />
        pass: admin</span></td>
  </tr>
</table>
<? } ?>
</body>
</html>