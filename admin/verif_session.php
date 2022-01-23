<?
if (!session_is_registered("login"))
{
        echo "<script language='Javascript'>alert('ey le gros, il faut te logger pour voir cette page!');
		window.location='../index.php?page=admin';
		</script>";
}
?>