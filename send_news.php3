<html><body bgcolor="#FFFFFF">
<?


if(empty($email) OR empty($nom))
{
?>
  <form method="POST" action="send_news.php3">
  <center><font face="arial" size="2" color="#800000"><b>Remplissez les 2 champs</b></font><br><br>
  <center><font face="verdana" size="1" color="#000000">Vous pouvez envoyer cette info à plusieurs personnes, il<br>vous suffit de séparer les adresses email par une virgule.</font><br><br>
  <input type="hidden" size="20" name="news" value="<? echo $news ?>">
  <font face="arial" size="2" color="#000000">

  <textarea style="width: 300px; height: 80px" name="email">Email du ou des destinataires</textarea><br><br>
  <input type="text" name="nom" value="Votre nom" style="width: 300px;">

  <br><br><input type="submit" value="Envoyer..." name="envoi" style="width: 300px;">
  </font></form>
  </center>
<?
}
elseif($email=='Email du ou des destinataires')
{
 echo "<br><center><font face=\"VERDANA\" size=\"2\" color=\"red\">- <b>Erreur dans l'email !</b> -<br><br>";
 echo "<a href=\"Javascript:history.back()\"><< RETOUR AU FORMULAIRE</a></font></center>";
}
elseif($nom=='Votre nom')
{
 echo "<br><center><font face=\"VERDANA\" size=\"2\" color=\"red\">- <b>Erreur dans le nom !</b> -<br><br>";
 echo "<a href=\"Javascript:history.back()\"><< RETOUR AU FORMULAIRE</a></font></center>";
}
else
{
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS WHERE id='$news'");

$titre = mysql_result($req,0,"titre");
$date  = mysql_result($req,0,"date");
$heure = mysql_result($req,0,"heure");
$news  = mysql_result($req,0,"news");
$news  = strip_tags(stripslashes($news));

// Envoi du mail ----------------------
$message  = "Bonjour,\n\n";
$message .= "Lors de son récent passage sur le site $NAME, ";
$message .= "$nom a tenu à vous faire part de cette information qu'il a relevé :\n\n";
$message .= "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";
$message .= "$date @ $heure : $titre\n";
$message .= "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";
$message .= "$news\n";
$message .= "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";
$message .= "$NAME - $URL\n";
$message .= "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-";


mail("$email","Info relevée par $nom","$message","From: $EMAIL\nReply-To: $EMAIL");

echo "<center><font face=\"VERDANA\" size=\"2\" color=\"#000080\">- <b>Message envoyé !</b> -</font></center><br>";
echo "<font face=\"courier new\" size=\"2\">";
echo "<b>From</b> : $nom<br><b>To</b> : $email<br><b>Message</b> : <blockquote>$news</blockquote>";
echo "</font><br><br>";
echo "<center><b><font face=\"VERDANA\" size=\"1\"><a href=\"Javascript:window.close()\">-- FERMER --</a></font></b></center>";
}
?>
</body></html>
