<?php
$bbcode = new bbcode;

$site->addToTitle(' - Lire un message privé');
$sub_template->setFile('centredroite','membres/read.html');
$site->addJs('modules/membres/js/conf.js');

$id=eregi_replace('^read-([a-z0-9\-]+)-id','',$_GET['action']);

$info=$sql->fetchAssoc($sql->query('SELECT * FROM mod_messages LEFT JOIN mod_membres ON mod_messages.from_id=mod_membres.id WHERE mod_messages.id="'.$id.'" && to_id="'.$membres->infos('id').'"'));


$sub_template->setBlock('centredroite','auteur.profil');
$sub_template->setBlock('centredroite','auteur.profil2');
$sub_template->setBlock('centredroite','citer');
$sub_template->setBlock('centredroite','avatar');
$sub_template->setBlock('centredroite','pipes');
$sub_template->setBlock('centredroite','level');
$sub_template->setBlock('centredroite','auteur.infos');
$sub_template->setBlock('centredroite','barreaction');

if($info['avatar']) $sub_template->parse('avatar', true);

/**
 * Niveau du membre
 */

$level=unserialize($site->config('membres_level'));
foreach($level as $i=>$var) {
	if($info['part']>=$i) {
		$designation=$var[0];
		for ($k=0;$k<$var[2];$k++) {
			$sub_template->setVar('membrePipe',$var[1]);
			$sub_template->parse('pipes', true);
		}
	}
}
if(isset($designation)) {
	$sub_template->setVar('membreDesignation',$designation);
	$sub_template->parse('level', true);
}

if($info['from_id']!=0) {
	$sub_template->parse('auteur.profil');
	$sub_template->parse('auteur.infos');
	$sub_template->parse('citer');
	$sub_template->parse('barreaction');
} else $sub_template->parse('auteur.profil2');

$sub_template->setVar(array(
	'messageId'			=>	$id,
	'topicTitre'		=>	$info['sujet'],
	'postAuteurName'	=>	$info['from_name'],
	'postDate'			=>	$string->formatDate('%A %d %B %Y',$info['date'],true),
	'postHeure'			=>	$string->formatDate('%H:%M',$info['date']),
	'postPost' 			=>	$bbcode->BBCodeToHtml($info['message']),
	'membreAvatar'		=>	$membres->getAvatar($info['avatar'],$info['mail']),
	'membreNbPosts'		=>	$info['posts'],
	'membreDateIns'		=>	$string->formatDate('%d %B %Y',$info['date_ins'],true),
	'membreNatio'		=>	$info['natio'],
	'membreDateNes'		=>	$membres->getAge($info['date_nes'])
));

if($info['etat']==0) $sql->query('UPDATE mod_messages SET etat="1" WHERE id="'.$id.'" && to_id="'.$membres->infos('id').'"');
?>