<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM defi WHERE id=$id";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);

$message = "Salut<br><b>$disp[pseudo]</b> a demandé que votre team (<b>$disp[clan]</b>) fasse un matche contre nous.<br>Nous avons le regret de vous informer que nous ne pourrons participer à ce matche, date/heure qui ne correspond pas ou manque de couille (2ème solution plutôt :)<br><br>Pour rappel voici les informations que <b>$disp[pseudo]</b> à rentré concernant le matche:<br>Date : le <b>$disp[jour]/$disp[mois]/$disp[annee]</b> à <b>$disp[heure]:$disp[minute]</b><br>Votre Carte : <b>$disp[map]</b><br>Server : <b>$disp[server]</b><br><br>Voila, donc pour toute questions/modifications envoyer un mail à country@k1der.net ou passez sur irc ( #k1der sur Quakenet )<br><br>Team K1der";

$date = date("d/m/Y"); 
$heure = date("H:i");
$from="From:k1der_bot@k1der.net\n"; 
$from.="MIME-version: 1.0\n"; 
$from.="Content-type: text/html; charset= iso-8859-1\n"; 
$sujet = 'Matche vs K1der'; 
$message = str_replace("
","<br>",$message);
$message = "<font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$message<br><br>---------------------------------------<br>@+ sur <a target=_blank href=http://www.k1der.net>-=K1der=- The Chocolat Effect</a><br></font>"; 
$message = stripslashes($message);
mail ($disp[mail],$sujet,$message,$from);

$rqt = Mysql_Query("DELETE FROM defi WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le\'matche n\'a pas été rejeté !";
}
else
{
 $ALERT = "Le matche a été rejeté avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>