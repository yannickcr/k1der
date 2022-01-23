<?php
/**
 * Construction de la page
 */
$site->addToTitle(' - Changer mon avatar');
$sub_template->setFile('centredroite','membres/changeavatar.html');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('changeAvatar')) $membres->changeAvatar($_POST['avatar'],$_POST['url'],$_FILES,$sub_template);

$sub_template->setVar(array(
	'maxHeight'=>$site->config('avatar_max_height'),
	'maxWidth'=>$site->config('avatar_max_width'),
	'maxSize'=>$site->config('avatar_max_size')
));

/**
 * Vérifie si le membre à un gravatar
 */
if($membres->gravatarExist()) {
	$sub_template->setBlock('centredroite','gravatar');
	$sub_template->parse('gravatar',true);
}

if($membres->infos('avatar')) {
	$sub_template->setVar(array(
		'pseudo'=>$membres->infos('pseudo'),
		'avatar'=>$membres->getAvatar()
	));
	if($membres->infos('avatar')=='gravatar') $sub_template->setVar('checked3',' checked="checked"');
	else $sub_template->setVar('checked2',' checked="checked"');
	$sub_template->setBlock('centredroite','avatar-set');
	$sub_template->parse('avatar-set',true);
} else {
	$sub_template->setVar(array('checked1'=>' checked="checked"'));
	$sub_template->setBlock('centredroite','avatar-notset');
	$sub_template->parse('avatar-notset',true);
}
?>