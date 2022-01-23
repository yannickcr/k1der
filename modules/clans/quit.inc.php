<?php
/**
 * Vérification des accès
 */
if($membres->infos('clan_nom')=='' && ereg('postul_(.*)',$membres->infos('clan_nom'))) $site->error('Vous ne possdez pas de clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/quit.html');
$site->addToTitle(' - Quitter un clan');

/**
 * Action envoi formulaire
 */
if($this->action('quit','clans')) $clans->quit();

$sub_template->setVar('clan',$membres->infos('clan_nom'));
?>