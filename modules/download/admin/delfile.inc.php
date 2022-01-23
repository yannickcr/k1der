<?php
$download = new download();

$res=$sql->query('SELECT id FROM mod_download WHERE id="'.$_GET['id'].'"');
if($sql->numRows($res)==0) $site->error('Fichier inconnu.');

if($this->action('delFile')) $download->delFile($_GET['id']);
?>