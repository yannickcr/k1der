<?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$month = $month+1;

if ($month < 10) { $month = "0".$month; }
if ($dayFight < 10) { $dayFight = "0".$dayFight; }

$orderdate = "$year$month$dayFight";
$comm = addslashes($comm);

$requete  = "INSERT INTO defi VALUES('','$pseudo','$clan','$leader','$num','$map','$mail','$irc','$msn','$server','$comm','$dayFight','$month','$year','$hour','$minute','$orderdate')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('Votre demande de match à été envoyée avec succès aux membres du clan -=K1der=-');
window.location='index.php?page=news';
</script>