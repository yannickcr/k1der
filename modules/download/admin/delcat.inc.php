<?php
$download = new download();

$res=$sql->query('SELECT id FROM mod_download_cats WHERE id="'.$_GET['id'].'"');
if($sql->numRows($res)==0) $site->error('Catégorie inconnue.');

if($this->action('delCat')) $download->delCat($_GET['id']);
?>