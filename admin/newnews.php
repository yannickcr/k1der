<?
include "secu.php";?><?

require("admin/config.php");

$source = "admin/user/mes/";

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

$cont1 = str_replace("ic01", "<img src=\\\"images/smileys/ic01.gif\\\" border=\\\"0\\\">", $chaine);
$cont1 = str_replace("ic02", "<img src=\\\"images/smileys/ic02.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic03", "<img src=\\\"images/smileys/ic03.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic04", "<img src=\\\"images/smileys/ic04.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic05", "<img src=\\\"images/smileys/ic05.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic06", "<img src=\\\"images/smileys/ic06.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic07", "<img src=\\\"images/smileys/ic07.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic08", "<img src=\\\"images/smileys/ic08.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic09", "<img src=\\\"images/smileys/ic09.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic10", "<img src=\\\"images/smileys/ic10.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic11", "<img src=\\\"images/smileys/ic11.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic12", "<img src=\\\"images/smileys/ic12.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic13", "<img src=\\\"images/smileys/ic13.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic14", "<img src=\\\"images/smileys/ic14.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic15", "<img src=\\\"images/smileys/ic15.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic16", "<img src=\\\"images/smileys/ic16.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic17", "<img src=\\\"images/smileys/ic17.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic18", "<img src=\\\"images/smileys/ic18.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic19", "<img src=\\\"images/smileys/ic19.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic20", "<img src=\\\"images/smileys/ic20.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic21", "<img src=\\\"images/smileys/ic21.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic22", "<img src=\\\"images/smileys/ic22.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic23", "<img src=\\\"images/smileys/ic23.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic24", "<img src=\\\"images/smileys/ic24.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic25", "<img src=\\\"images/smileys/ic25.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic26", "<img src=\\\"images/smileys/ic26.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic27", "<img src=\\\"images/smileys/ic27.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic28", "<img src=\\\"images/smileys/ic28.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic29", "<img src=\\\"images/smileys/ic29.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic30", "<img src=\\\"images/smileys/ic30.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic31", "<img src=\\\"images/smileys/ic31.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic32", "<img src=\\\"images/smileys/ic32.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic33", "<img src=\\\"images/smileys/ic33.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic34", "<img src=\\\"images/smileys/ic34.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic35", "<img src=\\\"images/smileys/ic35.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic36", "<img src=\\\"images/smileys/ic36.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic37", "<img src=\\\"images/smileys/ic37.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic38", "<img src=\\\"images/smileys/ic38.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic39", "<img src=\\\"images/smileys/ic39.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic40", "<img src=\\\"images/smileys/ic40.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic41", "<img src=\\\"images/smileys/ic41.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic42", "<img src=\\\"images/smileys/ic42.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic43", "<img src=\\\"images/smileys/ic43.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic44", "<img src=\\\"images/smileys/ic44.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic45", "<img src=\\\"images/smileys/ic45.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic46", "<img src=\\\"images/smileys/ic46.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic47", "<img src=\\\"images/smileys/ic47.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic48", "<img src=\\\"images/smileys/ic48.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic49", "<img src=\\\"images/smileys/ic49.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic50", "<img src=\\\"images/smileys/ic50.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic51", "<img src=\\\"images/smileys/ic51.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic52", "<img src=\\\"images/smileys/ic52.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic53", "<img src=\\\"images/smileys/ic53.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic54", "<img src=\\\"images/smileys/ic54.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic55", "<img src=\\\"images/smileys/ic55.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic56", "<img src=\\\"images/smileys/ic56.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic57", "<img src=\\\"images/smileys/ic57.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic58", "<img src=\\\"images/smileys/ic58.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic59", "<img src=\\\"images/smileys/ic59.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic60", "<img src=\\\"images/smileys/ic60.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic61", "<img src=\\\"images/smileys/ic61.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic62", "<img src=\\\"images/smileys/ic62.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic63", "<img src=\\\"images/smileys/ic63.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic64", "<img src=\\\"images/smileys/ic64.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic65", "<img src=\\\"images/smileys/ic65.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("ic66", "<img src=\\\"images/smileys/ic66.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("sitof", "<img src=\\\"images/ie.gif\\\" border=\\\"0\\\">", $cont1);
$cont1 = str_replace("dnit", "<img src=\\\"images/zip.gif\\\" border=\\\"0\\\">", $cont1);
return $cont1;
}

$txt = souriez($txt);
$ti = souriez($ti);

$src = "<?


\$ntitre = \"$ti\";
\$ndate = \"$date à $heure\";
\$nauteur = \"Country\";
\$nemail = \"x100@oreka.com\";
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
    <td background="images/deg.gif" width="50"><img src="images/deg.gif" width="50" height="8"></td>
    <td width="35%" bgcolor="#CC0000"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b>-=Administration 
      du Site=-</b></font> </td>
  </tr>
</table>
<p><a href="index.php?page=delnews"><font size="2" face="Arial, Helvetica, sans-serif">Supprimer 
  une News</font></a><font size="2" face="Arial, Helvetica, sans-serif"> || Ecrire 
  une News</font></p>
<hr size="1" color="#CC0000">
<table width="38%" border="0" cellspacing="0" cellpadding="10" height="174">
  <tr> 
    <td align="center" valign="top"> 
      <table width="22%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic01.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic01</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic02.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic02</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic03.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic03</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic04.gif" width="15" height="22" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic04</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic05.gif" width="37" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic05</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic06.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic06</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic07.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic07</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic08.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic08</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic09.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic09</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic10.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic10</font></td>
        </tr>
      </table>
    </td>
    <td align="center" valign="top"> 
      <table width="22%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic11.gif" width="28" height="20" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic11</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic12.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic12</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic13.gif" width="22" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic13</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic14.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic14</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic15.gif" width="51" height="18" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic15</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic16.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic16</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic17.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic17</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic18.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic18</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic19.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic19</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic20.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic20</font></td>
        </tr>
      </table>
    </td>
    <td align="center" valign="top"> 
      <table width="22%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic21.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic21</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic22.gif" width="34" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic22</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic23.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic23</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic24.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic24</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic25.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic25</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic26.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic26</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic27.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic27</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic28.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic28</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic29.gif" width="22" height="22" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic29</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic30.gif" width="20" height="20" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic30</font></td>
        </tr>
      </table>
    </td>
    <td align="center" valign="top"> 
      <table width="17%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic31.gif" width="33" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic31</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic32.gif" width="75" height="26" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic32</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic33.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic33</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic34.gif" width="30" height="20" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic34</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic35.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic35</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic36.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic36</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic37.gif" width="17" height="17" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic37</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic38.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic38</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic39.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic39</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic40.gif" width="21" height="19" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic40</font></td>
        </tr>
      </table>
    </td>
    <td align="center" valign="top"> 
      <table width="22%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic41.gif" width="20" height="24" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic41</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic42.gif" width="20" height="18" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic42</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic43.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic43</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic44.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic44</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic45.gif" width="16" height="16" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic45</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic46.gif" width="16" height="16" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic46</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic47.gif" width="100" height="33" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic47</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic48.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic48</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic49.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic49</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic50.gif" width="15" height="32" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic50</font></td>
        </tr>
      </table>
    </td>
    <td align="center" valign="top"> 
      <table width="22%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic51.gif" width="50" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic51</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic52.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic52</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic53.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic53</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic54.gif" width="20" height="20" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic54</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic55.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic55</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic16.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic56</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic57.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic57</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic58.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic58</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic59.gif" width="20" height="24" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic59</font></td>
        </tr>
        <tr> 
          <td height="5" width="28%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic60.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="72%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic60</font></td>
        </tr>
      </table>
    </td>
    <td align="center" valign="top"> 
      <table width="43%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic61.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic61</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic62.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic62</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic63.gif" width="16" height="16" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic63</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic64.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic64</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic65.gif" width="16" height="16" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic65</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic66.gif" width="39" height="18" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">ic66</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2"><img src="images/ie.gif" width="15" height="15"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2">sitof</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2"><img src="images/zip.gif" width="15" height="16"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2">dnit</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font></td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font size="2" face="Arial, Helvetica, sans-serif" color="#000000"><a href="http://www.lien.com">Lien</a></font></div>
          </td>
          <td height="5" width="88%" nowrap><font size="2" face="Arial, Helvetica, sans-serif" color="#000000">&lt;a 
            href=&quot;http://www.lien.com&quot;&gt;Lien&lt;/a&gt;</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><img src="images/smileys/ic01.gif" width="15" height="15" align="absmiddle"></font></div>
          </td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">&lt;img 
            src=&quot;images/ic01.gif&quot;&gt;</font></td>
        </tr>
        <tr> 
          <td height="5" width="12%"><font color="#00CC00" face="Arial, Helvetica, sans-serif" size="2">couleur</font></td>
          <td height="5" width="88%" nowrap><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">&lt;font 
            color=&quot;#00CC00&quot;&gt;couleur&lt;/font&gt;</font></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<hr size="1" color="#CC0000">
<form action="index.php?page=delnews" method="POST">
<input type="hidden" name="action" value="newz">
  <table cellspacing="0" cellpadding="0" border="0" align="center">
    <tr> 
      <td align="center"> 
        <p align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">Le 
          titre :<br>
          <input type="texte" size="60" name="ti" class="boite">
          </font></p>
      </td>
    </tr>
    <tr> 
      <td align="center"> 
        <p align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">L</font><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">a 
          News :<br>
          <textarea name="txt" cols="60" rows="15"></textarea>
          </font></p>
      </td>
    </tr>
    <tr> 
      <td align="center"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">Type 
        de News :</font></td>
    </tr>
    <tr> 
      <td align="center"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000"> 
        <input type="radio" name="ico" value="k1der" checked>
        K1der 
        <input type="radio" name="ico" value="cs">
        Cs 
        <input type="radio" name="ico" value="hl">
        Hl </font></td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
    <tr> 
      <td align="center"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000"> 
        <input type="submit" value="Envoyer" class="bouton3">
        </font></td>
    </tr>
  </table>
</form>

<hr size="1" color="#CC0000">
<p><a href="index.php?page=delnews"><font face="Arial, Helvetica, sans-serif" size="2">Supprimer 
  une News</font></a><font face="Arial, Helvetica, sans-serif" size="2"> || Ecrire 
  une News</font></p>
<hr size="1" color="#CC0000">
</body>
</html>