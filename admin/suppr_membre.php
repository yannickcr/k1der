<? $level = "10"; include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM equipe WHERE id='$id'");
$disp = mysql_fetch_array($req);

$message  = "Salut !\n\n";
$message .= "Ton compte sur le site -=K1der=- vient d'être fermé.\n\n";
$message .= "Nous t'informons donc de ton départ de la team (volontaire ou forcé).\n\n";
$message .= "\n\nVoila, nous te souhaitons bonne chance pour la suite et espéront te revoir sur le net ou en LAN.";
$message .= "\n\nA bientôt sur http://www.k1der.net";

mail("$disp[e_mail]","Bye Bye","$message","From: k1der_bot@k1der.net\nReply-To: k1der_bot@k1der.net");

mysql_query("UPDATE matches SET jou_k1='[OLD] $disp[kinder]' WHERE jou_k1='$disp[kinder]'");
mysql_query("UPDATE matches SET jou_k2='[OLD] $disp[kinder]' WHERE jou_k2='$disp[kinder]'");
mysql_query("UPDATE matches SET jou_k3='[OLD] $disp[kinder]' WHERE jou_k3='$disp[kinder]'");
mysql_query("UPDATE matches SET jou_k4='[OLD] $disp[kinder]' WHERE jou_k4='$disp[kinder]'");
mysql_query("UPDATE matches SET jou_k5='[OLD] $disp[kinder]' WHERE jou_k5='$disp[kinder]'");
mysql_query("UPDATE mynewsinfos SET signature='[OLD] $disp[kinder]' WHERE signature='$disp[kinder]'");
mysql_query("UPDATE shoutbox SET pseudo='[OLD] $disp[kinder]' WHERE pseudo='$disp[kinder]'");
mysql_query("UPDATE dossiers SET auteur='[OLD] $disp[kinder]' WHERE auteur='$disp[kinder]'");

$requete  = "SELECT * FROM lan_party";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
while($disp = mysql_fetch_array($req))
{
$joueurs = str_replace(";$disp[kinder];",";[OLD] $disp[kinder];",$disp[joueurs]);
mysql_query("UPDATE lan_party SET joueurs='$joueurs' WHERE id='$disp[id]'");
}

$rqt = Mysql_Query("DELETE FROM equipe WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le membre n\'a pas été effacée !";
}
else
{
 $ALERT = "Le membre a été effacée avec succès ! Un mail lui a été envoyé.";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>