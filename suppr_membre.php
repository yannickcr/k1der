<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM equipe WHERE id='$id'");
$disp = mysql_fetch_array($req);

mail("$disp[mail]","Bye Bye","�� kick: ($disp[nom]) was kicked by ($pymembs) from -=K1der=-","From: $EMAIL\nReply-To: $EMAIL");


$rqt = Mysql_Query("DELETE FROM equipe WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le membre n\'a pas �t� effac�e !";
}
else
{
 $ALERT = "Le membre a �t� effac�e avec succ�s ! Un mail lui a �t� envoy�.";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>