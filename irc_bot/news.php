News Archive
<?
require("../config.inc.php3");
$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base est Down ...</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base n'est pas accessible ...</b></font></center>");

$req = MYSQL_QUERY("SELECT id,titre,date FROM mynewsinfos WHERE conf != '0' && url_source != 'admin' ORDER by id DESC limit 0,3");
$nbre =mysql_num_rows($req);
while($disp = mysql_fetch_array($req)) {
$caca = "_".$disp[id]."_";

$dodo = mysql_query("SELECT * FROM ib_posts WHERE attach_hits='$caca' && forum_id='1'");
$grosse = mysql_fetch_array($dodo);
$popo = mysql_num_rows($dodo);
$grosseconne = $grosse[topic_id];

echo "$disp[date]
";
echo "$disp[titre]
";
echo "http://www.k1der.net/forum/index.php?act=ST&f=1&t=".$grosseconne."
";
}
/*
07.30.03
Services Nickname du mien
02.03.03
DALnet channels for chat, not files
08.14.02
Update about recent connection problems
06.20.02
DALnet webhosting - home.dal.net - goes offline
03.14.02
New Issue of the Zine!
*/
?>
denotes services announcement