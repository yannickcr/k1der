<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM liens WHERE id='".$_GET["id"]."'");

$requete  = "SELECT * FROM liens WHERE id='".$_GET["id"]."'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$disp = mysql_fetch_array($req);

$message  = "Bonjour,\n\n";
$message .= "Votre demande de partenariat � �t� rejet�e\n";
$message .= "Votre site ne correspondai peu �tre pas � nos crit�res de qualit� ou de contenu (ou alors on vous aime pas, au choix)";
$message .= "\n\nA bient�t sur http://www.k1der.net";

mail("$disp[mail]","Partenariat avec -=K1der=-","$message","From: $EMAIL\nReply-To: $EMAIL");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le lien n\'a pas �t� rejet� !";
}
else
{
 $ALERT = "Le lien a �t� rejet� !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>