<?php
/**
 * Vérification des accès
 */
if($membres->infos('clan_nom')!='' && !ereg('postul_(.*)',$membres->infos('clan_nom'))) $site->error('Vous possdez dj un clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/create.html');
$site->addToTitle(' - créer un clan');
$site->addJs('modules/clans/js/tagpreview.inc.js');

/**
 * Action envoi formulaire
 */
if($this->action('create','clans')) $clans->create($_POST,$sub_template);

$sub_template->setBlock('centredroite','ircservers');

/**
 * Liste des serveurs IRC
 */
$irc=array(
	'Quakenet'	=>	'irc.quakenet.org',
	'Undernet'	=>	'zagreb.hr.eu.undernet.org',
	'Epiknet'	=>	'irc.epiknet.org',
	'IRCnet'	=>	'irc.fu-berlin.de'
);

/**
 * Champs concernant le clan
 */
if($this->action('create','clans')) {
	$info=array(
		'nom'=>$_POST['nom'],
		'tag'=>$_POST['tag'],
		'tagempl'=>$_POST['tagempl'],
		'site'=>$_POST['site'],
		'irc'=>$_POST['irc'],
		'ircserver'=>$_POST['ircserver']
	);

	if($info['tagempl']==1) $tagpreview='exemple : '.$info['tag'].'Joueur';
	else $tagpreview='exemple : Joueur'.$info['tag'];
	
	$info['tag']=str_replace('&nbsp;',' ',$info['tag']);
	
	$sub_template->setVar(array(
		'nom'			=>	$info['nom'],
		'tag'			=>	$info['tag'],
		'tagempl'		=>	$info['tagempl'],
		'site'			=>	$info['site'],
		'irc'			=>	$info['irc']
	));
	if($info['tagempl']==1) $sub_template->setVar('empl1',' checked="checked"');
	else if($info['tagempl']==2) $sub_template->setVar('empl2',' checked="checked"');
	
} else {
	$tagpreview='exemple : Joueur';
	$sub_template->setVar('empl1',' checked="checked"');
}

$sub_template->setVar('tagpreview',$tagpreview);

$j=0;
foreach($irc as $i=>$var) {
	$sub_template->setVar(array(
		'ircServerName'	=>	$i,
		'ircServer'		=>	$var
	));
	if(($this->action('create','clans') && $info['ircserver']==$var) || $j==0) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->setVar('selected','');
	$sub_template->parse('ircservers', true);
	$j++;
}
?>