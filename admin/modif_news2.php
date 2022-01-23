<?
include "secu.php";?>
<?
$Text = str_replace("\\\"", "\"", $Text);
$Text = str_replace("\\\"", "\"", $Text);
$Text = str_replace("border=1", "border=0", $Text);
$Text = str_replace("<IMG alt=", "<IMG border=0 alt=", $Text);
$Text = str_replace("<IMG src=", "<IMG border=0 src=", $Text);
$Text = str_replace("<a href=", "<a target=_blank href=", $Text);

$caca = "_".$_POST["id"]."_";

$Text = "<font size=2 face=Verdana, Arial, Helvetica, sans-serif>$Text</font>";
require("../config.inc.php3");
$Text = addslashes($Text);
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
$titre = addslashes($titre);

$requete  = "UPDATE mynewsinfos SET titre='$titre', url_source='$type', news='$Text' WHERE id='".$_POST["id"]."'";
mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$requete  = "UPDATE ib_topics SET title='$titre' WHERE pinned='$caca' && forum_id='1'";
mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$requete  = "UPDATE ib_posts SET post='$Text' WHERE attach_hits='$caca' && forum_id='1'";
mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('La news à été modifiée avec succès');
window.location='../index.php?page=admin';
</script>