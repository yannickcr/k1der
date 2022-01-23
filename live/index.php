<?php 
include("cssource_class.php");
set_time_limit(120);

$css = new cssource($_GET['ip'], $_GET['port']);
echo $css->status();
?>