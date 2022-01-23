<?php
$template->setFile('centre','clans/fiche.html');
$site->addCss('templates/'.THEME.'/clans/style.css');

if(!isset($_GET['clan'])) $site->error('Clan inconnu');

// 1re partie : Infos générales sur le clan
$nom=eregi_replace("[^A-Z0-9-]",'',$_GET['clan']);
$nom=eregi_replace('-',' ',$nom);

$res=$sql->query('
	SELECT 
		id,
		nom,
		tag,
		tagempl,
		banniere,
		leader_pseudo,
		site,
		irc,
		ircserver
	FROM
		mod_membres_clans
	WHERE 
		nom="'.$nom.'"
');

if($sql->numRows($res)==0) $site->error('Clan inconnu');

$info=$sql->fetchAssoc($res);

$site->addToTitle(' - Fiche du clan '.$info['nom']);

if(!empty($info['site'])) {
	$template->setBlock('centre','site1');
	$template->setBlock('centre','site2');
	$template->setBlock('centre','site3');
	$template->setBlock('centre','site4');
	$template->parse('site1',true);
	$template->parse('site2',true);
	$template->parse('site3',true);
	$template->parse('site4',true);
}
if(!empty($info['irc'])) {
	$template->setBlock('centre','irc1');
	$template->setBlock('centre','irc2');
	$template->parse('irc1',true);
	$template->parse('irc2',true);
}

$irc=array(
	'Quakenet'	=>	'irc.quakenet.org',
	'Undernet'	=>	'zagreb.hr.eu.undernet.org',
	'Epiknet'	=>	'irc.epiknet.org',
	'IRCnet'	=>	'irc.fu-berlin.de'
);

$defTxt=' class="noinfo">Aucune information';

$template->setVar(array(
	'clanNom'			=>	$info['nom'],
	'clanTag'			=>	$info['tag'],
	'clanLeaderPseudo'	=>	$info['leader_pseudo'],
	'clanSite'			=>	(empty($info['site']))?$defTxt:$info['site'],
	'clanIrc'			=>	(empty($info['irc']))?$defTxt:$info['irc'],
	'clanIrcServer'		=>	$info['ircserver'],
	'clanIrcServerName'	=>	array_search($info['ircserver'],$irc)
));
$tagempl=$info['tagempl'];
if($tagempl==0) $tagempl=1;

	$template->setBlock('centre','banniere');

if(!empty($info['banniere'])) {
	$template->setVar('clanBanniere',$info['banniere']);
	$template->parse('banniere',true);
}


// 2me partie : Infos sur les membres du clan

$template->setBlock('centre','avatar');
$template->setBlock('centre','natio1');
$template->setBlock('centre','natio2');
$template->setBlock('centre','www1');
$template->setBlock('centre','www2');
$template->setBlock('centre','membre');

$res=$sql->query('
	SELECT 
		pseudo,
		avatar,
		mail,
		date_nes,
		nom,
		prenom,
		natio,
		www
	FROM
		mod_membres
	WHERE 
		clan_id="'.$info['id'].'"
');

$side='right';

$template->setVar('clanNbMembres',$sql->numRows($res));

while($info=$sql->fetchAssoc($res)) {
	if($side!='right') $side='right';
	else $side='left';

	if(!empty($info['natio'])) {
		$template->parse('natio1');
		$template->parse('natio2');
	}
	if(!empty($info['www'])) {
		$template->parse('www1');
		$template->parse('www2');
	}
	if(!empty($info['avatar'])) $template->parse('avatar');

	$template->setVar(array(
		'side'					=>	$side,
		'membrePseudo'.$tagempl	=>	$info['pseudo'],
		'membrePseudo'			=>	$info['pseudo'],
		'membreAvatar'			=>	$membres->getAvatar($info['avatar'],$info['mail']),
		'membreDateNes'			=>	($info['date_nes']==0)?$defTxt:'>'.$string->formatDate('%d %B %Y',$info['date_nes'],true),
		'membreAge'				=>	($info['date_nes']==0)?$defTxt:'>'.$membres->getAge($info['date_nes']),
		'membreNom'				=>	(empty($info['nom']))?$defTxt:'>'.$info['nom'],
		'membrePrenom'			=>	(empty($info['prenom']))?$defTxt:'>'.$info['prenom'],
		'membreNatio'			=>	(empty($info['natio']))?$defTxt:$info['natio'],
		'membreWww'				=>	(empty($info['www']))?$defTxt:$info['www']
	));
	$template->parse('membre', true);
	$template->unsetVar(array('membreAvatar','avatar','membreWww','www1','www2'));
}
?>