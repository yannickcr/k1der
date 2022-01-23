<? include "secu.php";

$req = MYSQL_QUERY("SELECT * FROM recrut_comm WHERE id_recrut='$id' && nom='$nom'");
$res = MYSQL_NUM_ROWS($req);

if ($res != 0)
{
mysql_query("UPDATE recrut_comm SET comment='$comment' WHERE id_recrut='$id' && nom='$nom'");
}
else
{
mysql_query("INSERT INTO recrut_comm VALUES('','$id','$nom','$comment','')");
}
?>
<script language="Javascript">
alert('Commentaire ajouté avec succès');
window.location='../index.php?page=visu_details_recrut&id=<? echo $id; ?>';
</script>
