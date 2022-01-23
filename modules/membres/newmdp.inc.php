<?php
$template->setFile('centre','membres/newmdp.html');  
$site->addToTitle(' - Réinitialisation du mot de passe');

if($membres->infos('id')) $site->error('redirect');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('newMDP')) $membres->newMDP($_GET['newmdp'],$_POST['pass'],$_POST['confpass']);
?>