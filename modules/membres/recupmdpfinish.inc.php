<?php
$template->setFile('centre','membres/recupmdpfinish.html');  
$site->addToTitle(' - Récupération de mot de passe');

if($membres->infos('id')) $site->error('redirect');
?>