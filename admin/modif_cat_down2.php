<?
include "secu.php";

$rqt = Mysql_Query("UPDATE cats_down SET nom='$nom',type='$type' WHERE id='$id'");

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
window.location='../index.php?page=cat_liste&action=modif';
</script>