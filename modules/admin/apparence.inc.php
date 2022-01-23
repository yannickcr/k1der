<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('admin_apparence')) $site->error(1);

if($this->action('changeTheme','admin')) $admin->changeTheme($_POST);

$sub_template->setFile('centredroite','admin/apparence.html');
$site->addToTitle(' - Site - Apparence');

$sub_template->setBlock('centredroite','noap');
$sub_template->setBlock('centredroite','ap');
$sub_template->setBlock('centredroite','themes');

$tab=$string->listDir('templates');

foreach($tab as $val) {

	if(file_exists('templates/'.$val.'/apercu.png')) {
		$sub_template->setVar('themeApercu','templates/'.$val.'/apercu.png');
		$sub_template->parse('ap');
	}
	else $sub_template->parse('noap');

	$sub_template->setVar(array(
		'themeNom'	=>	$val
	));
	if(file_exists('templates/'.$val.'/infos.txt')) {
		$infos=implode("\n",file('templates/'.$val.'/infos.txt'));
		$infos=nl2br(htmlentities($infos));
		$sub_template->setVar('themeInfos',$infos);
	}
	$sub_template->parse('themes', true);
	$sub_template->unsetVar(array('ap','noap','themeInfos'));
}
?>