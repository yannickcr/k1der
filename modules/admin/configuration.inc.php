<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('admin_config')) $site->error(1);

if($this->action('configSite','admin')) $admin->configSite($_POST);

$site->addToTitle(' - Site - Configuration générale');

$sub_template->setFile('centredroite','admin/configuration.html');

$sub_template->setBlock('centredroite','modules');

$tab=$string->listDir('modules');

foreach($tab as $val) {
	$sub_template->setVar('choix',$val);
	if($site->config('default_page')==$val) $sub_template->setVar('selected',' selected="selected"');
	$sub_template->parse('modules', true);
	$sub_template->unsetVar(array('selected'));
}
?>