<?php
if(!$membres->infos('id')) $template->setFile('centre','membres/inscriptionfinish.html');  
else $site->error('redirect');
$site->addToTitle(' - Fin de l\'inscription');
?>