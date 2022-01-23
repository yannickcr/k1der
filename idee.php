<?
if (!empty($idee))
{
$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base est Down !</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE;

mysql_query("INSERT INTO idees VALUES('','$pseudo','$idee')");
echo "<br><br><center><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Merci :)<br>Reste AWARE !<br>Résulats du concours fin février</font></center>";
echo "<br><br><br><br><center><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><a href=\"index.php?page=news\">Retour aux News</a></font></center>";
}
?>