<?php
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);
$info=$sql->fetchArray($sql->query('SELECT phrase FROM mod_quote ORDER BY rand() LIMIT 1'));

$info['phrase']=$string->unhtmlentities($info['phrase']);

echo '<data>'.$info['phrase'].'</data>';
exit();
?>