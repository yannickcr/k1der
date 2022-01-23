<?php
/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('configMembres')) $membres->configMembres($_POST['inscr'],$_POST['mailtitle'],$_POST['mailtxt']);

$site->addCss('templates/'.THEME.'/membres/admin/style.css');

$site->addToTitle(' - Membres - Configuration');

$sub_template->setFile('centredroite','membres/admin/configuration.html');

if($site->config('membres_inscr')=='open') $sub_template->setVar('checked1',' checked="checked"');
else if($site->config('membres_inscr')=='close') $sub_template->setVar('checked2',' checked="checked"');
$sub_template->setVar('mailtitle',$site->config('membres_mail_title'));
$sub_template->setVar('mailtxt',$site->config('membres_mail_txt'));
?>