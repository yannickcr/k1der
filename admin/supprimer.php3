<?
include "secu.php";?>
<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
$caca = "_".$_GET["id"]."_";
$rqt = Mysql_Query("DELETE FROM mynewsinfos WHERE id='".$_GET["id"]."'");
$req = MYSQL_QUERY("SELECT * FROM ib_topics WHERE pinned='$caca' && forum_id='1'");
$disp = mysql_fetch_array($req);

mysql_query("DELETE FROM ib_topics WHERE pinned='$caca' && forum_id='1'");

mysql_query("DELETE FROM ib_posts WHERE attach_hits='$caca' && topic_id='$disp[tid]'");

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='1'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]-1;

mysql_query("UPDATE ib_forums SET topics='$topics' WHERE id='$disp[id]'");


if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la news n° ".$_GET["id"]." n\'a pas été effacée !";
}
else
{
 $ALERT = "La news n° ".$_GET["id"]." a été effacée avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=modiflistnews';
</script>