<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "INSERT INTO server VALUES('','$furl','$descr')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('Fichier ajouté avec succès');
window.location='../index.php?page=ger_server';
</script>