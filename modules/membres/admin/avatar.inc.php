<?php
$site->addToTitle(' - &Eacute;diter l\'avatar d\'un membre');
$sub_template->setFile('centredroite','membres/admin/avatar.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
	
/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('changeMembresAvatar')) $membres->changeMembresAvatar($_POST['avatar'],$_POST['url'],$_FILES,$sub_template);
	
$info=$sql->fetchAssoc($sql->query('
	SELECT
		pseudo, 
		mail,
		avatar
	FROM 
		mod_membres
	WHERE 
		id="'.$_GET['id'].'"
'));

# Debut Bloc Avatar

$sub_template->setVar(array(
	'maxHeight'=>$site->config('avatar_max_height'),
	'maxWidth'=>$site->config('avatar_max_width'),
	'maxSize'=>$site->config('avatar_max_size')
));

/**
 * Vérifie si le membre à un gravatar
 */
 
if($membres->gravatarExist($info['mail'])) {
	$sub_template->setBlock('centredroite','gravatar');
	$sub_template->parse('gravatar',true);
}

if($info['avatar']) {
	$sub_template->setVar(array(
		'pseudo'=>$info['pseudo'],
		'avatar'=>$membres->getAvatar($info['avatar'],$info['mail'])
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
# Fin Bloc Avatar
?>