<?
//CS-Fusion Stats for -=K1der=- Team
//Version 1.0
require("config.inc.php3");
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

function salope($url)
{ 
// URL de la page à capturer
//$url="http://zeroping.noos.fr/stats/27021/player_MzE0OTcyNw.php";; 

// Ouvre la page HTML en lecture
if (!fopen($url, "r"))
{
$info2 = "no";
}
else
{
$fd = fopen($url, "r") or die("Pas réussi"); 

// Boucle jusqu'à la dernière ligne de $fd 
while (!feof($fd)) { 
$ligne = fgets($fd, 4096);
if(ereg("is ranked",$ligne))
{
$info2 = strstr ($ligne, "is ranked <b>");
$separat = " and has played for ";
$info2 = substr($info2, 0, strlen($info2)-strlen (strstr ($info2,$separat)));
$info2 = str_replace("is ranked","", $info2);
$info2 = str_replace("<b>","", $info2);
$info2 = str_replace("</b>","", $info2);
mysql_query("UPDATE equipe SET stats='$info2' WHERE id='$disp[id]'");
}
} // Fin de la boucle 

fclose( $fd ); 
}
}
$requete  = "SELECT id, kinder, fusion FROM equipe";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
while($disp = mysql_fetch_array($req))
{
if ($disp[fusion] != '')
{
salope($disp[fusion]);
}
}
?>