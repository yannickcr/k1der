<? include "secu.php";

mysql_query("DELETE FROM recrutement WHERE id='".$_GET["id"]."'");
mysql_query("DELETE FROM recrut_comm WHERE id_recrut='".$_GET["id"]."'");

$ALERT = "La demande a �t� effac�e avec succ�s !";
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=visu_recrut';
</script>