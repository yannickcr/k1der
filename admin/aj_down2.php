<?
include "secu.php";

$descr=addslashes($descr);


if ($type_cat == 'ex_cat')
{
$requete  = "INSERT INTO liens_down VALUES('','$nom','$img','$descr','$lien','$thesize','','$cat')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
else
{
$requete  = "SELECT * FROM cats_down WHERE nom = '$new_cat' && type = '$type'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
if ($nbre != '0')
{
$requete  = "INSERT INTO liens_down VALUES('','$nom','$img','$descr','$lien','$thesize','','$disp[id]')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
else
{
$requete  = "INSERT INTO cats_down VALUES('','$new_cat','$type')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$requete  = "SELECT * FROM cats_down WHERE nom = '$new_cat' && type='$type'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);
$requete  = "INSERT INTO liens_down VALUES('','$nom','$img','$descr','$lien','$thesize','','$disp[id]')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
}
}
?>
<script language="Javascript">
alert('Download ajouté avec succès');
         window.location='../index.php?page=admin';
</script>