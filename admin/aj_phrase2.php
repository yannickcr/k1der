<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$phrase = addslashes($phrase);
$requete  = "INSERT INTO phrases VALUES('$phrase')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('Phrase à la con ajoutée avec succès');
         window.location='../index.php?page=admin';
</script>