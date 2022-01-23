<html>
<head>
  <META http-equiv="Content-Language" content="fr">
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<BODY TOPMARGIN=5 LEFTMARGIN=0 TEXT=#000000 MARGINHEIGHT=5 MARGINWIDTH=0>
<?
require("config.inc.php3");

if(empty($pseudo) || empty($commentaire))
{
 echo "<br><br><center><font color=red face=arial size=2><b>Attention, un champ est vide !</font></b></center>";
 echo "<br><br><center><a href=\"Javascript:history.back();\"><font style=\"$CorpsNews\">[Retour au formulaire]</font></center>";
}
else
{
$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base est Down !</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE;

$date = date("Y-m-d");
$heure = date("H\hi");
$commentaire = nl2br($commentaire);

$req = MYSQL_QUERY("INSERT INTO matchescomments VALUES('','$id_matche','$date','$heure','$pseudo','$commentaire')");

if(!$req){ echo "<br><br><center><font color=red face=arial size=2><b>Ooops, la requête a échouée ...</b></center>"; }
else
{
 echo "<br><br><center><font style=\"$CorpsNews\"><b>Commentaire ajouté !</font></b><br><br>";
 echo "<a href=\"Javascript:window.close();\"><font style=\"$CorpsNews\">[Fermer la fenêtre]</font></a></center>";
}
}
?>
</BODY>