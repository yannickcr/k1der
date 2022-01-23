<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('clans_config')) $site->error(1);

$clans = new clans();

if($this->action('configClans','clans')) $clans->configClans($_POST['clan']);

$site->addToTitle(' - Clans - Configuration');
$site->addJs('modules/clans/js/livesearchclan.js');

$sub_template->setFile('centredroite','clans/admin/configuration.html');

$sub_template->setVar('clan',$site->config('clan_default'));
?>