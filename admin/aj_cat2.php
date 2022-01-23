<?
include "secu.php";

$requete  = "SELECT * FROM cats_down WHERE nom = '$nom' && type = '$type'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
if ($nbre != '0')
{
?>
<script language="Javascript">
alert('Cette catégorie existe déjà !');
history.go(-1)
</script>
<?
}
else
{
$requete  = "INSERT INTO cats_down VALUES('','$nom','$type')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
?>
<script language="Javascript">
alert('Catégorie ajouté avec succès');
window.location='../index.php?page=admin';
</script>