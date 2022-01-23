<?
include "secu.php";?><?

//require("admin/config.php");
include "admin/activeuser/user.txt";

if ($nuser == "country"){
				$auteur = "-=K1der=- Country";
				$email = "x100@oreka.com";
				}	
if ($nuser == "surprise"){
				$auteur = "-=K1der=- Surprise";
				$email = "surprise@minitel.net";
				}
if ($nuser == "maxi"){
				$auteur = "-=K1der=- Maxi";
				$email = "em.garcia@wanadoo.fr";
				}
if ($nuser == "bueno"){
				$auteur = "-=K1der=- Bueno";
				$email = "molskevich@aol.com";
				}
if ($nuser == "pingui"){
				$auteur = "-=K1der=- pingui";
				$email = "pikyo2000@aol.com";
				}

$source = "news/";
				
if ($action == "supprimer") {

$rec = file($source."enregistrer.txt");
$enlever = fopen($source."enregistrer.txt", "w");
for ($i = 0; $i < count($rec); $i++) {
$rec[$i] = trim($rec[$i]);

if ($suppr == $i) {
unlink($source.$rec[$i]);
continue;}
fputs($enlever, $rec[$i]."\n");
}
fclose($enlever);

}

elseif ($action == "newz") {

$messages = file($source."enregistrer.txt");

// Parametrer les variables

$date = date("d/m/Y");
$heure = date("H:i");

$txt = nl2br($txt);
$txt = eregi_replace("<(mailto:)([^ >\n\t]+)>", "<a href=\"\\1\\2\">\\2</a>", $txt);
$txt = eregi_replace("<([http|news|ftp]+://)([^ >\n\t]+)>", "<a href=\"\\1\\2\" target=\"_blank\">\\2</a>", $txt);

// Creation du message, transformation des smileys.

function souriez($chaine) {

$cont1 = str_replace("ic01", "<img src=\\\"images/smileys/ic01.gif\\\" border=\\\"0\\\" align='middle'>", $chaine);
$cont1 = str_replace("ic02", "<img src=\\\"images/smileys/ic02.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic03", "<img src=\\\"images/smileys/ic03.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic04", "<img src=\\\"images/smileys/ic04.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic05", "<img src=\\\"images/smileys/ic05.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic06", "<img src=\\\"images/smileys/ic06.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic07", "<img src=\\\"images/smileys/ic07.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic08", "<img src=\\\"images/smileys/ic08.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic09", "<img src=\\\"images/smileys/ic09.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic10", "<img src=\\\"images/smileys/ic10.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic11", "<img src=\\\"images/smileys/ic11.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic12", "<img src=\\\"images/smileys/ic12.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic13", "<img src=\\\"images/smileys/ic13.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic14", "<img src=\\\"images/smileys/ic14.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic15", "<img src=\\\"images/smileys/ic15.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic16", "<img src=\\\"images/smileys/ic16.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic17", "<img src=\\\"images/smileys/ic17.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic18", "<img src=\\\"images/smileys/ic18.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic19", "<img src=\\\"images/smileys/ic19.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic20", "<img src=\\\"images/smileys/ic20.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic21", "<img src=\\\"images/smileys/ic21.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic22", "<img src=\\\"images/smileys/ic22.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic23", "<img src=\\\"images/smileys/ic23.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic24", "<img src=\\\"images/smileys/ic24.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic25", "<img src=\\\"images/smileys/ic25.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic26", "<img src=\\\"images/smileys/ic26.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic27", "<img src=\\\"images/smileys/ic27.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic28", "<img src=\\\"images/smileys/ic28.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic29", "<img src=\\\"images/smileys/ic29.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic30", "<img src=\\\"images/smileys/ic30.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic31", "<img src=\\\"images/smileys/ic31.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic32", "<img src=\\\"images/smileys/ic32.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic33", "<img src=\\\"images/smileys/ic33.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic34", "<img src=\\\"images/smileys/ic34.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic35", "<img src=\\\"images/smileys/ic35.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic36", "<img src=\\\"images/smileys/ic36.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic37", "<img src=\\\"images/smileys/ic37.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic38", "<img src=\\\"images/smileys/ic38.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic39", "<img src=\\\"images/smileys/ic39.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic40", "<img src=\\\"images/smileys/ic40.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic41", "<img src=\\\"images/smileys/ic41.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic42", "<img src=\\\"images/smileys/ic42.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic43", "<img src=\\\"images/smileys/ic43.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic44", "<img src=\\\"images/smileys/ic44.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic45", "<img src=\\\"images/smileys/ic45.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic46", "<img src=\\\"images/smileys/ic46.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic47", "<img src=\\\"images/smileys/ic47.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic48", "<img src=\\\"images/smileys/ic48.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic49", "<img src=\\\"images/smileys/ic49.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic50", "<img src=\\\"images/smileys/ic50.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic51", "<img src=\\\"images/smileys/ic51.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic52", "<img src=\\\"images/smileys/ic52.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic53", "<img src=\\\"images/smileys/ic53.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic54", "<img src=\\\"images/smileys/ic54.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic55", "<img src=\\\"images/smileys/ic55.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic56", "<img src=\\\"images/smileys/ic56.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic57", "<img src=\\\"images/smileys/ic57.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic58", "<img src=\\\"images/smileys/ic58.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic59", "<img src=\\\"images/smileys/ic59.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic60", "<img src=\\\"images/smileys/ic60.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic61", "<img src=\\\"images/smileys/ic61.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic62", "<img src=\\\"images/smileys/ic62.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic63", "<img src=\\\"images/smileys/ic63.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic64", "<img src=\\\"images/smileys/ic64.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic65", "<img src=\\\"images/smileys/ic65.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("ic66", "<img src=\\\"images/smileys/ic66.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("sitof", "<img src=\\\"images/ie.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
$cont1 = str_replace("dnit", "<img src=\\\"images/zip.gif\\\" border=\\\"0\\\" align='middle'>", $cont1);
return $cont1;
}

$txt = souriez($txt);
$ti = souriez($ti);

$src = "<?


\$ntitre = \"$ti\";
\$ndate = \"$date à $heure\";
\$nauteur = \"$auteur\";
\$nemail = \"$email\";
\$ntexte = \"$txt\";
\$nico = \"$ico\";

?>

";

// Nombre de messages.
$num = fopen($source."num.txt", "r");
$nbmes = fread($num, filesize($source."num.txt"));
fclose($num);

// ID du message, nom de fichier.
$index = ($nbmes + 1);
$nomf = "mes$index.txt";

// Augmenter le nombre d'index.
$num = fopen($source."num.txt", "w+");
fputs($num, $index);
fclose($num);

// Creer le fichier.
$nfichier = fopen($source."$nomf", "w");
fclose($nfichier);

// Enregistrer la newz.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);

// Enregistrer l'ID.
$ajid = fopen($source."enregistrer.txt", "w+");
fputs($ajid, "$nomf\n");
fputs($ajid, implode("", $messages));
fclose($ajid);

}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title>-=K1der=- The Chocolat Effect</title>
<link href="style.css" type="text/css" rel="stylesheet">
<script language="JavaScript">

function spr() {
	var popup = window.confirm("Voulez-vous supprimer la newz ?");
	if (popup == true) {
		document.supprimer.submit();}}

</script></head>
<body topmargin="5" leftmargin="5" marginheight="5" marginwidth="5"> 
<table width="97%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td width="50">&nbsp;</td>
    <td width="35%"> 
      <div align="center"></div>
    </td>
    <td rowspan="2" height="31" width="679"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"> 
      </b></font><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
      <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> </b></font></b></font> 
      <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
      </b></font></b></font> <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
      </b></font> </b></font></td>
  </tr>
  <tr> 
    <td background="images/deg.gif" width="50"><img src="images/deg.gif" width="50" height="1"></td>
    <td width="35%" bgcolor="#CC0000"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b>-=Administration 
      du Site=-</b></font> </td>
  </tr>
</table>
<p><font face="Arial, Helvetica, sans-serif" size="2">Supprimer une News || <a href="index.php?page=newnews">Ecrire 
  une News</a></font></p>
<hr size="1" color="#CC0000">
<form name="supprimer" action="index.php?page=delnews" method="POST" onSubmit="spr(); return false;">
  <input type="hidden" name="action" value="supprimer">
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <? // AFFICHAGE DU FORMULAIRE DE SUPPRESSION.

$rec = file($source."enregistrer.txt");
for ($i = 0; $i < count($rec); $i++) {

$rec[$i] = trim($rec[$i]);

?>
    <tr> 
      <td nowrap valign="top"> 
        <input type="radio" name="suppr" value="<? echo $i; ?>">
        &nbsp;&nbsp; </td>
      <td width="100%" valign="top"> 
        <?

include($source.$rec[$i]);

echo "<p><font face='Arial, Helvetica, sans-serif' size='2'>".stripslashes($ntitre)." par <a href=\"mailto:".stripslashes($nemail)."\">".stripslashes($nauteur)."</a></p>";

?>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <?
}
?>
    <tr>
      <td colspan="2" align="center">
        <p align="center"> 
          <input type="submit" value="Supprimer" class="bouton2">
        </p>
      </td>
    </tr>
  </table>
</form>

<hr size="1" color="#CC0000">
<p><font face="Arial, Helvetica, sans-serif" size="2">Supprimer une News || <a href="index.php?page=newnews">Ecrire 
  une News</a></font></p>
<hr size="1" color="#CC0000">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>

</html>
