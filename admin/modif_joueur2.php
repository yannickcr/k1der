<?
include "secu.php"; ?><?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$nom = addslashes($nom);
$prenom = addslashes($prenom);
$annee = addslashes($annee);
$mois = addslashes($mois);
$jour = addslashes($jour);
$e_mail = addslashes($e_mail);
$ville = addslashes($ville);
$icq = addslashes($icq);
$role = addslashes($role);
$statu = addslashes($statu);
$conn_type = addslashes($conn_type);
$conn_fai = addslashes($conn_fai);
$proc = addslashes($proc);
$graph = addslashes($graph);
$mere = addslashes($mere);
$son = addslashes($son);
$souris = addslashes($souris);
$clavier = addslashes($clavier);
$tapis = addslashes($tapis);
$ram = addslashes($ram);
$armecs = addslashes($armecs);
$mapcs = addslashes($mapcs);
$resocs = addslashes($resocs);
$senscs = addslashes($senscs);
$herow3 = addslashes($herow3);
$mapw3 = addslashes($mapw3);
$resow3 = addslashes($resow3);
$urlw3 = addslashes($urlw3);
$reso = addslashes($reso);
$os = addslashes($os);
$ecran = addslashes($ecran);
$sens2cs = addslashes($sens2cs);

if ($checkcs == 1)
{
$cs = "oui";
}
else
{
$cs = "non";
}
if ($checkwar3 == 1)
{
$war3 = "oui";
}
else
{
$war3 = "non";
}

$rqt = Mysql_Query("UPDATE equipe SET nom='$nom', prenom='$prenom', age='$annee$mois$jour', age2='$mois$jour', pass='$pass', e_mail='$e_mail', ville='$ville', icq='$icq', role='$role',conn_type='$conn_type',conn_fai='$conn_fai',proc='$proc',graph='$graph',son='$son',mere='$mere',souris='$souris',clavier='$clavier',tapis='$tapis',ram='$ram',cs='$cs',war3='$war3',armecs='$armecs',mapcs='$mapcs',resocs='$resocs',senscs='$senscs',sens2cs='$sens2cs',herow3='$herow3',mapw3='$mapw3',resow3='$resow3',urlw3='$urlw3',reso2='$reso',os='$os',ecran='$ecran' WHERE kinder='$HTTP_COOKIE_VARS[gen]'") or die(mysql_error());

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, le joueur $user a pas été mis à jour !";
}
else
{
 $ALERT = "Le joueur a été mis à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>
