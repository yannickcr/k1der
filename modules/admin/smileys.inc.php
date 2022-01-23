<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('admin_smileys')) $site->error(1);

if($this->action('changeSmileys','admin')) $admin->changeSmileys($_POST);

$sub_template->setFile('centredroite','admin/smileys.html');
$site->addToTitle(' - Site - Smileys');
$site->addJs('modules/admin/js/smiley.js');



$sub_template->setBlock('centredroite','smileylist');
$sub_template->setBlock('centredroite','newsmile');
$sub_template->setBlock('centredroite','smileys');

// Smileys existants

$tab=unserialize($site->config('smileys'));
$smileys=$string->listDir('templates/'.THEME.'/images/smileys',true);
$k=0;
foreach($tab as $i=>$val) {
	$sub_template->setVar(array(
		'smileyNb'	=>	$k,
		'smileyTxt'	=>	$i,
		'smileyImg'	=>	$val
	));
	$k++;
	$sub_template->parse('smileys', true);
}
foreach($smileys as $val2) {
	$sub_template->setVar(array(
		'smileyNb'	=>	$k,
		'smileyImg'	=>	$val2
	));
	$sub_template->parse('smileylist', true);
}

// Champ vide pour un nouveau smiley

$sub_template->setVar(array(
	'newSmileyNb'	=>	$k
));
?>