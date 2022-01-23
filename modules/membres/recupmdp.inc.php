<?php
$template->setFile('centre','membres/recupmdp.html');  
$site->addToTitle(' - Récupération de mot de passe');

if($membres->infos('id')) $site->error('redirect');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('recupMDP')) $membres->recupMDP();

?>