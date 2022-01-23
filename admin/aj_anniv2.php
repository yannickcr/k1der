<?
include "secu.php";?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

if (strlen($jour) == '1')
{
$jour = "0".$jour;
}

$nom= addslashes($nom);

$requete  = "INSERT INTO anniv VALUES('','$nom','$mois$jour','$annee')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('Anniversaire ajouté avec succès');
window.location='../index.php?page=admin';
</script>