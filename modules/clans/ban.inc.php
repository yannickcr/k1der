<?php
/**
 * Construction de la page
 */
$site->addToTitle(' - Changer la bannière du clan');
$sub_template->setFile('centredroite','clans/ban.html');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('changeBan','clans')) $clans->changeBan($_POST['banniere'],$_POST['url'],$sub_template);

$sub_template->setVar(array(
	'maxHeight'=>$site->config('banniere_max_height'),
	'maxWidth'=>$site->config('banniere_max_width'),
	'maxSize'=>$site->config('banniere_max_size')
));

/**
 * Vrifie si le membre  un gravatar
 */
$info=$sql->fetchAssoc($sql->query('SELECT nom,banniere FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"'));

if(!empty($info['banniere'])) {
	$sub_template->setVar(array(
		'clanNom'		=>	$info['nom'],
		'clanBanniere'	=>	$info['banniere'],
		'checked2'		=>	' checked="checked"'
	));
	$sub_template->setBlock('centredroite','banniere-set');
	$sub_template->parse('banniere-set',true);
} else {
	$sub_template->setVar(array(
		'checked1'		=>	' checked="checked"',
		'clanBanniere'	=>	'http://'
	));
	$sub_template->setBlock('centredroite','banniere-notset');
	$sub_template->parse('banniere-notset',true);
}
?>