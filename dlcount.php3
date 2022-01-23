<html>
<?
require("config.inc.php3");
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM liens_down WHERE lien='$url'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$disp = mysql_fetch_array($req);

$nbdl = $disp[taille]+1;

$rqt = Mysql_Query("UPDATE liens_down SET taille='$nbdl' WHERE id = '$disp[id]'");

if(!$rqt)
{
$error ="<BR><center>Ce dessin n'a pas été mis en Dessin de la Semaine (ERREUR)</center><BR>";
}
else
{
$error ="<BR><center>Ce dessin a été mis en Dessin de la Semaine</center><BR>";
}
?>
<meta http-equiv="refresh" content="0;URL=<? echo $url; ?>">

<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
  <br>
  Le t&eacute;l&eacute;chargement va D&eacute;marrer<br>
  Si il ne d&eacute;marre pas, <a href="<? echo $url; ?>"><strong>cliquez ici</strong></a>.</font></div>
</html>
<XML style=display:none>
