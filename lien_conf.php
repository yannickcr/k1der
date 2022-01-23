<?
session_start();
include("verif_session.php");
?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE liens SET conf='1' WHERE id='$id'");

$requete  = "SELECT * FROM liens WHERE id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$disp = mysql_fetch_array($req);

$message  = "Bonjour,\n\n";
$message .= "Votre demande de partenariat à été validée\n";
$message .= "Voici le code à intégrer dans votre page:\n\n";
$message .= '<a href="http://www.k1der.net" target="_blank" ><img src="http://www.k1der.net/images/lienk1der.gif" border="0" alt="-=K1der=- The Chocolat Effect"></a>';
$message .= "\n\nA bientôt sur http://www.k1der.net";

mail("$disp[mail]","Partenariat avec -=K1der=-","$message","From: $EMAIL\nReply-To: $EMAIL");


if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le lien n\'a pas été validé !";
}
else
{
 $ALERT = "Le lien a été validé !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>