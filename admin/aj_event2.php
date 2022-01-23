<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$nom = addslashes($nom);
$titre = addslashes($titre);

$requete  = "INSERT INTO events VALUES('','$annee$mois$jour','$titre','$nom')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('Evènement ajouté avec succès');
window.location='../index.php?page=admin';
</script>