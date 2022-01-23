<? include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

mysql_query("UPDATE equipe SET statut='oui' WHERE kinder='$HTTP_COOKIE_VARS[gen]'");

$date = date("Ymd");
$requete  = "SELECT * FROM calendrier WHERE debut>='$date' && k1der ='oui' ORDER by debut";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

$requete  = "SELECT * FROM lan_party WHERE nom = '$disp[nom]'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

$joueur = ucfirst($HTTP_COOKIE_VARS[gen]);
$joueur = ";$joueur;";
$joueur = "$disp[joueurs]$joueur";
while (ereg(";;",$joueur))
{
$joueur = str_replace(";;",";",$joueur);
}

mysql_query("UPDATE lan_party SET joueurs='$joueur' WHERE id='$disp[id]'");

?>
<script language="JavaScript">
alert("Tu as été ajouté dans la liste des joueurs qui viennent à la LAN");
window.location='../index.php?page=news'
</script>
