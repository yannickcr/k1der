<?
if ($action == '')
{
$url = "$PHP_SELF?page=$page";
}
else
{
$url = "$PHP_SELF?page=$page&action=$action";
}
$url = str_replace("/k1der/","",$url);
if ($HTTP_COOKIE_VARS[gen] != "")
{
include "../config.inc.php3";
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM equipe WHERE kinder='$HTTP_COOKIE_VARS[gen]'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$disp = mysql_fetch_array($req);

$raquete  = "SELECT * FROM admin WHERE lien='$url'";
$raq = mysql_query($raquete) or die('Erreur SQL !<br>'.$raquete.'<br>'.mysql_error());
$dasp = mysql_fetch_array($raq);
$nbre =mysql_num_rows($raq);

if ($nbre == '0')
{
$disp[level] = 0;
}

if ($dasp[level] > $disp[level])
{
$error = "level";
Header("Location:index.php?page=admin&error=level");
exit;
}

if ($HTTP_COOKIE_VARS[gen] == "")
{
$error = "log";
Header("Location:index.php?page=admin&error=log");
exit;
}
}
?>