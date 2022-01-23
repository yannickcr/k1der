<?
include "secu.php";?>
<?

function effacer($fichier) {
  if (file_exists($fichier)) {
    chmod($fichier,0777);
    if (is_dir($fichier)) {
	  $REP = opendir($fichier);
	  while($entree = readdir($REP))
	  {
	  if ($entree != "." && $entree != "..")
	  {
	  unlink($fichier."/".$entree);
	  }
	  }
	  closedir($REP);
      rmdir($fichier);
	}
    else unlink($fichier);
  }
}

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM calendrier WHERE id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

if ($disp[k1der] == 'oui')
{
$requete  = "SELECT * FROM lan_party WHERE nom='$nom'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

$repertoire = "../lan/$disp[id]/fichiers";
effacer($repertoire);
$repertoire = "../lan/$disp[id]/photos/piti";
effacer($repertoire);
$repertoire = "../lan/$disp[id]/photos";
effacer($repertoire);
$repertoire = "../lan/$disp[id]";
effacer($repertoire);

mysql_query("DELETE FROM lan_party WHERE id='$disp[id]'");

$caca = "_".$id_lan."_";

$req = MYSQL_QUERY("SELECT * FROM ib_topics WHERE pinned='$caca' && forum_id='4'");
$disp = mysql_fetch_array($req);

mysql_query("DELETE FROM ib_topics WHERE pinned='$caca' && forum_id='4'");

mysql_query("DELETE FROM ib_posts WHERE attach_hits='$caca' && topic_id='$disp[tid]'");

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='4'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]-1;

mysql_query("UPDATE ib_forums SET topics='$topics' WHERE id='$disp[id]'");

}

$rqt = Mysql_Query("DELETE FROM calendrier WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la LAN n\'a pas été effacée !";
}
else
{
 $ALERT = "La LAN a été effacée avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=lan_liste&action=suppr';
</script>