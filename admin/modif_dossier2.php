<? include "secu.php";

$Text = str_replace("\\\"", "\"", $Text);
$Text = str_replace("\\\"", "\"", $Text);
$Text = str_replace("border=1", "border=0", $Text);
$Text = str_replace("<IMG alt=", "<IMG border=0 alt=", $Text);
$Text = str_replace("<IMG src=", "<IMG border=0 src=", $Text);
$Text = str_replace("<a href=", "<a target=_blank href=", $Text);

$Text = "<font size=2 face=Verdana, Arial, Helvetica, sans-serif>$Text</font>";

$date666 = date("U");

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("UPDATE dossiers_p SET titrepage='$titrepage', text='$Text' WHERE id_dossier='$id_dossier' && page='$numpage'");

?>
<script language="Javascript">
alert('Page modifiée avec succès');
window.location='../index.php?page=ger_dossier&id=<? echo $id_dossier; ?>';
</script>