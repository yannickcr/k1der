<?
echo "salut<br>";
/* Configuration */
$irc_server_name = "quakenet"; // adresse principale du serveur irc auquel le script doit se connecter
$chan = "k1der"; // sans le #
$irc_server_port = 6667; // port que le script doit utiliser pour se connecter
$irc_server_timeout = 10; // durée maximale de connexion au serveur (au dela le script part en timeout)

$irc_script_nick = "k2der".time(); // nick par défaut du script


/* Connexion Serveur IRC a ne PAS modifier*/
$stream = fsockopen ($irc_server_name, $irc_server_port, &$errno, &$errstr, $irc_server_timeout);

if($stream) {
fputs($stream, "NICK $irc_script_nick\n\r");
$data = explode(" ", fgets($stream, 1024));

if($data[1] == 433) {
die("Erreur de Connexion : Pseudonyme déjà utilisé...");
}
elseif($data[0] == "PING") {
fputs($stream, "PONG $data[1]\n\r");
}

fputs($stream, "USER InfoServer none none Infos Server by nyxen\n\r");
fputs($stream, "LIST #$chan\n\r");
while(!feof($stream)) {
$streamline = fgets($stream, 1024);
$data = explode(" ", $streamline);

if ($data[1] == 251) {
$connectes = $data[6] + $data[9];
}
if ($data[1] == 322) {
$chatteurs = $data[4];
$topic = split(" :", $streamline);
$topic = chop(str_replace("\"", "''", $topic[1]));
}
if(strstr($streamline, "Fin de la commande.")) {
fputs($stream, "QUIT\n\r");
}
flush();
}

fclose($stream);

echo $data[1];
echo $data[2];
echo $data[3];
echo $data[4];
echo $data[5];
echo $data[6];
echo $data[7];
echo $data[8];
echo $data[9];
echo $connectes;
echo "$chatteurs chatteurs - topic : $topic";
}
print("<br><font face=\"arial\" size=\"2\"><a href=\"http://www.nyxen.net\">powered by nyxen</a></font>");

?> 
