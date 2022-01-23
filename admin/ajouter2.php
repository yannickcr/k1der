<?
include "secu.php";
?>
<?
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

$req = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$auteur'");
$disp = mysql_fetch_array($req);
$email = $disp[e_mail];
$req5 = MYSQL_QUERY("SELECT * FROM ib_members WHERE name='$auteur'");
$disp5 = mysql_fetch_array($req5);

mysql_query("INSERT INTO mynewsinfos VALUES('','$titre','$date_verif','$date','$heure','$auteur','$email','non','$nom_source','$type','non','$path_image','$url_image','$Text','1')") or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

$req6 = MYSQL_QUERY("SELECT * FROM mynewsinfos ORDER BY id DESC LIMIT 0, 1");
$disp6 = mysql_fetch_array($req6);

$requete3  = "INSERT INTO ib_topics VALUES ('', '$titre', '', 'open','0', '$disp5[id]','$date666', '0','$date666', 0, '$auteur', '$auteur','0','0','0','1','1','0','_$disp6[id]_', '', '','0');";
$req3 = mysql_query($requete3) or die('Erreur SQL !<br>'.$requete3.'<br>'.mysql_error());
$req = MYSQL_QUERY("SELECT * FROM ib_topics ORDER BY tid DESC LIMIT 0, 1");
$disp = mysql_fetch_array($req);

$requete2  = "INSERT INTO ib_posts VALUES('0','','','$disp5[id]','$auteur','0','1','127.0.0.1','$date666','','$Text','0','$disp[tid]','1','','_$disp6[id]_','','','','1','')";

//$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='1'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]+1;

mysql_query("UPDATE ib_forums SET last_title='$titre' ,topics='$topics' ,last_post='$date666' , last_poster_name='$auteur' WHERE id='$disp[id]'");

//$requete  = "INSERT INTO mynewsinfos VALUES('','$titre','$date_verif','$date','$heure','$auteur','$email','non','$nom_source','$type','non','$path_image','$url_image','$Text','1')";
?>
<script language="Javascript">
alert('News ajoutée avec succès');
window.location='../index.php?page=admin';
</script>