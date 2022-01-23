<?php
/**
 * Vérification des accès
 */
if($clans->isClanLeader()==false) $site->error('Seul le leader peut modifier les informations du clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/close.html');
$site->addToTitle(' - Fermer un clan');

/**
 * Action envoi formulaire
 */
if($this->action('close','clans')) $clans->close();

$sub_template->setVar('clan',$membres->infos('clan_nom'));
?>