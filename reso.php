<?
function get_reso ($sortie)
{
if (eregi("Actuellement(.*)Date de création", $sortie, $reso))
{
$reso2 = str_replace("Actuellement, ","",$reso[0]);
$reso2 = str_replace("</tr></table><br><p/>","",$reso2);
$reso2 = str_replace("<p><hr></p>","",$reso2);
$reso2 = str_replace("<p></font>","",$reso2);
$reso2 = str_replace("Date de création","",$reso2);
$reso2 = str_replace("vote","voté",$reso2);
$reso2 = str_replace("<b>","<b><font color=#cc0000>",$reso2);
$reso2 = str_replace("</b>","</font></b>",$reso2);
$reso2 = str_replace("<tr>","",$reso2);
$reso2 = str_replace("</tr>","",$reso2);
$reso2 = str_replace("votés","votes",$reso2);

return "<font face=verdana size=2>".$reso2."</font>";
}
}
ob_start("get_reso");
readfile("http://www.re-so.com/articles.php?lng=fr&pg=173");
ob_end_flush();
?>