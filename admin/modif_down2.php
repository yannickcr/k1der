<?
require("../config.inc.php3");

$descr=addslashes($descr);
$lien=addslashes($lien);
$nom=addslashes($nom);
$img=addslashes($img);

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

include "secu.php";

if ($type_cat == 'ex_cat')
{
$requete  = "UPDATE liens_down SET nom='$nom', descr='$descr', lien='$lien' , img='$img' , cat='$cat' WHERE id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
else
{
$requete  = "SELECT * FROM cats_down WHERE nom = '$new_cat'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
if ($nbre != '0')
{
$requete  = "UPDATE liens_down SET nom='$nom', descr='$descr', lien='$lien' , img='$img' , cat='$disp[id]' WHERE id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
else
{
$requete  = "INSERT INTO cats_down VALUES('','$new_cat')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$requete  = "SELECT * FROM cats_down WHERE nom = '$new_cat'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
$requete  = "UPDATE liens_down SET nom='$nom', descr='$descr', lien='$lien' , img='$img' , cat='$disp[id]' WHERE id='$id'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
}

if(!$req)
{
 $ALERT = "Une erreur s\'est produite, le download n\'a pas été mis à jour !";
}
else
{
 $ALERT = "Le download a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='Javascript:window.close();';
</script>