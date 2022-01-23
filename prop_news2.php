<?
//$Text = str_replace("\\\"", "\"", $Text);
//$Text = str_replace("\\\"", "\"", $Text);
//$Text = str_replace("border=1", "border=0", $Text);
//$Text = str_replace("<IMG alt=", "<IMG border=0 alt=", $Text);
//$Text = str_replace("<IMG src=", "<IMG border=0 src=", $Text);
//$Text = str_replace("<a href=", "<a target=_blank href=", $Text);
$Text = addslashes($body);
$titre = addslashes($to);
$auteur = addslashes($auteur);

$Text = "<font size=2 face=Verdana, Arial, Helvetica, sans-serif>$Text</font>";

$date_verif = date("Y-m-d H:i:s");

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$auteur'");
$disp = mysql_fetch_array($req);
$email = $disp[e_mail];

$requete  = "INSERT INTO mynewsinfos VALUES('','$titre','$date_verif','$date','$heure','$auteur','$email','non','$nom_source','$type','non','$path_image','$url_image','$Text','0')";

$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

?>
<script language="Javascript">
alert('Ta News a été proposée, elle sera confirmée ( ou rejetée ) par un administateur.\n\nMerci d\'avoir contribué au site.');
window.location='index.php';
</script>