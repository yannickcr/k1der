<?
include "secu.php";

$rqt = Mysql_Query("UPDATE admin_cat SET nom='$nom' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la catégorie n\'a pas été renommée !";
}
else
{
 $ALERT = "La catégorie a été renommée avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin_pages';
</script>