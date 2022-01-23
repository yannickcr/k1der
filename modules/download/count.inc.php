<?php
$id=(int)$_POST['id'];
$sql->query('UPDATE mod_download SET dl=dl+1 WHERE id='.$id); 
exit();
?>