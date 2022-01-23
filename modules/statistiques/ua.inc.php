<?php
$statistiques = new statistiques();

echo $_SERVER['HTTP_USER_AGENT'];
exit();

print_r($statistiques->decodeUA($_SERVER['HTTP_USER_AGENT']));
exit();
?>