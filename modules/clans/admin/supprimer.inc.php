<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('clans_supprimer')) $site->error(1);

$clans = new clans();

$res=$sql->query('SELECT nom FROM mod_membres_clans WHERE id="'.$_GET['id'].'"');
if($sql->numRows($res)==0) $site->error('Clan inconnu.');

$info=$sql->fetchAssoc($res);

if($this->action('supprimerClans')) $clans->supprimerClans($_GET['id'],$info['nom']);
?>