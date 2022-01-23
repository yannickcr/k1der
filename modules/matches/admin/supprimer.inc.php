<?php
$matches = new matches();

$res=$sql->query('SELECT id FROM mod_matches WHERE id="'.$_GET['id'].'"');
if($sql->numRows($res)==0) $site->error('Match inconnu.');

if($this->action('supprimerMatch')) $matches->supprimerMatch($_GET['id']);
?>