<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('clans_modifier')) $site->error(1);

$clans = new clans();

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/admin/modifier.html');
$site->addToTitle(' - &Eacute;diter les infos du clan');
$site->addCss('templates/'.THEME.'/clans/admin/style.css');

/**
 * Action envoi formulaire
 */
if($this->action('edit','clans')) $clans->adminEdit($_GET['id'],$_POST,$sub_template);

$site->addJs('modules/clans/js/tagpreview.inc.js');

$sub_template->setBlock('centredroite','ircservers');
$sub_template->setBlock('centredroite','leaders');

/**
 * Champs concernant le clan
 */
if(!$this->action('edit','clans')) {
	$info=$sql->fetchAssoc($sql->query('
		SELECT
			id,
			nom,
			tag,
			tagempl,
			leader_id,
			site,
			irc,
			ircserver
		FROM
			mod_membres_clans
		WHERE
			id="'.$_GET['id'].'"
		LIMIT 0,1
	'));
} else {
	$info=array(
		'id'=>$_GET['id'],
		'nom'=>$_POST['nom'],
		'tag'=>$_POST['tag'],
		'tagempl'=>$_POST['tagempl'],
		'leader_id'=>$_POST['leader'],
		'site'=>$_POST['site'],
		'irc'=>$_POST['irc'],
		'ircserver'=>$_POST['ircserver']
	);
}

/**
 * Liste des serveurs IRC
 */
$irc=array(
	'Quakenet'	=>	'irc.quakenet.org',
	'Undernet'	=>	'zagreb.hr.eu.undernet.org',
	'Epiknet'	=>	'irc.epiknet.org',
	'IRCnet'	=>	'irc.fu-berlin.de'
);

if($info['tagempl']==1) $tagpreview='exemple : '.$info['tag'].'Joueur';
else $tagpreview='exemple : Joueur'.$info['tag'];

$info['tag']=str_replace('&nbsp;',' ',$info['tag']);

$sub_template->setVar(array(
	'clanId'		=>	$info['id'],
	'nom'			=>	$info['nom'],
	'tag'			=>	$info['tag'],
	'tagempl'		=>	$info['tagempl'],
	'tagpreview'	=>	$tagpreview,
	'site'			=>	$info['site'],
	'irc'			=>	$info['irc']
));
if($info['tagempl']==1) $sub_template->setVar('empl1',' checked="checked"');
else if($info['tagempl']==2) $sub_template->setVar('empl2',' checked="checked"');


foreach($irc as $i=>$var) {
	$sub_template->setVar(array(
		'ircServerName'	=>	$i,
		'ircServer'		=>	$var
	));
	if($info['ircserver']==$var) $sub_template->setVar('selected2',' selected="selected"');
	else $sub_template->setVar('selected2','');
	$sub_template->parse('ircservers', true);
}

/**
 * Liste des membres du clan pour le choix du leader
 */
$req=$sql->query('SELECT id,pseudo FROM mod_membres WHERE clan_id="'.$_GET['id'].'"');
while($leader=$sql->fetchAssoc($req)) {
	$sub_template->setVar(array(
		'leaderId'	=>	$leader['id'],
		'leaderNom'	=>	$leader['pseudo']
	));
	if($leader['id']==$info['leader_id']) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->setVar('selected','');
	$sub_template->parse('leaders', true);
}
?>