<?
include "secu.php";?><?
require("../config.inc.php3");

$date666 = date("U");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rqt = Mysql_Query("UPDATE mynewsinfos SET conf='1' WHERE id='$id'");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS WHERE id= '$id'");
$res = MYSQL_NUM_ROWS($req);
$disp = mysql_fetch_array($req);

$disp[news] = addslashes($disp[news]);
$disp[titre] = addslashes($disp[titre]);
$disp[signature] = addslashes($disp[signature]);

$requete3  = "INSERT INTO ib_topics VALUES ('', '$disp[titre]', '', 'open','0', '0','$date666', '0','$date666', 0, '$disp[signature]', '$disp[signature]','0','0','0','1','1','0','_$disp[id]_', '', '','0');";
$req3 = mysql_query($requete3) or die('Erreur SQL !<br>'.$requete3.'<br>'.mysql_error());
$req9 = MYSQL_QUERY("SELECT * FROM ib_topics ORDER BY tid DESC LIMIT 0, 1");
$disp9 = mysql_fetch_array($req9);

$requete2  = "INSERT INTO ib_posts VALUES('0','','','0','$disp[signature]','0','1','127.0.0.1','$date666','','$disp[news]','0','$disp9[tid]','1','','_$disp[id]_','','','','1','')";
$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());

$req10 = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='1'");
$disp10 = mysql_fetch_array($req10);
$topics = $disp10[topics]+1;

mysql_query("UPDATE ib_forums SET last_title='$disp[titre]' ,topics='$topics' ,last_post='$date666' , last_poster_name='$disp[signature]' WHERE id='1'");



if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la news n\'a pas été validée !";
}
else
{
 $ALERT = "La news a été validée !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>