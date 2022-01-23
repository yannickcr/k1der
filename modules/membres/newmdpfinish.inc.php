<?php
$template->setFile('centre','membres/newmdpfinish.html');  
$site->addToTitle(' - Réinitialisation du mot de passe');

if($membres->infos('id')) $site->error('redirect');
?>