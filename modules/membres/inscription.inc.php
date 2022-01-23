<?php
$template->setFile('centre','membres/inscription.html');  
$site->addToTitle(' - Inscription');

if($membres->infos('id')) $site->error('redirect');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('inscription')) $membres->inscription($_POST['email'],$_POST['pseudo'],$_POST['reglement']);
?>