<?
include "secu.php";

$rqt = Mysql_Query("UPDATE cats_down SET nom='$nom',type='$type' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la cat�gorie n\'a pas �t� renomm�e !";
}
else
{
 $ALERT = "La cat�gorie a �t� renomm�e avec succ�s !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=cat_liste&action=modif';
</script>