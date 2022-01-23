<?
include "secu.php";?>
<?
function effacer($fichier) {
  if (file_exists($fichier)) {
    chmod($fichier,0777);
    if (is_dir($fichier)) {
      $id_dossier = opendir($fichier); 
      while($element = readdir($id_dossier)) {
        if ($element != "." && $element != "..")
	        unlink($fichier."/".$element);
      }
      closedir($id_dossier);
      rmdir($fichier);
    }
    else unlink($fichier);
  }
}

require("../config.inc.php3");

$repertoire = "../matches/fichiers/$id";
effacer($repertoire);

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("DELETE FROM matches WHERE id='$id'");

$caca = "_".$id."_";
$rqt = Mysql_Query("DELETE FROM matches WHERE id='$id'");
$req = MYSQL_QUERY("SELECT * FROM ib_topics WHERE pinned='$caca' && forum_id='6'");
$disp = mysql_fetch_array($req);

mysql_query("DELETE FROM ib_topics WHERE pinned='$caca' && forum_id='6'");

mysql_query("DELETE FROM ib_posts WHERE attach_hits='$caca' && topic_id='$disp[tid]'");

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='6'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]-1;

mysql_query("UPDATE ib_forums SET topics='$topics' WHERE id='6'");





if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le matche n\'a pas été effacé !";
}
else
{
 $ALERT = "Le matche a été effacé avec succès !";
}

?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=matches_liste&action=suppr';
</script>