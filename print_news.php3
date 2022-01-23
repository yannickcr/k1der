<?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS WHERE id='$news'");

$titre      = stripslashes(mysql_result($req,0,"titre"));
$date       = mysql_result($req,0,"date");
$heure      = mysql_result($req,0,"heure");
$signature  = stripslashes(mysql_result($req,0,"signature"));
$news       = stripslashes(mysql_result($req,0,"news"));
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<script language="Javascript">
function IMPRIMER()
{
 window.print();
 window.close();
}
</script>

</head>

<body>
<table border="1" cellpadding="5" width="100%" cellspacing="0">
  <tr>
    <td width="100%"><b><? echo $titre; ?></b> @ <? echo "<font size=1>$date - $heure</font>"; ?></td>
  </tr>
  <tr>
    <td width="100%"><? echo $news; ?><br><br><i><b><? echo $signature; ?></b></i></td>
  </tr>
</table>
<br>
<div align="center">
<input type="button" onClick="Javascript:IMPRIMER();" value="Imprimer" style="width: 200px; Font-Family: arial; Font-Size: 12px;">
</div>
</body>
</html>

<script language="Javascript">
window.print();
window.close();
</script>
