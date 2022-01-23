<?php
/**
 * Vérification des accès
 */
if($clans->isClanLeader()==false) $site->error('Seul le leader peut grer les membres du clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/histo.html');
$site->addToTitle(' - Historique du clan');

$sub_template->setBlock('centredroite','messages');

/**
 * Listing des membres
 */
$info=$sql->fetchAssoc($res=$sql->query('SELECT histo FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"'));
$histo=unserialize($info['histo']);
if(!is_array($histo)) $histo=array();
rsort($histo);
foreach($histo as $var) {
	$sub_template->setVar(array(
		'messageDate'	=>	$string->formatDate('%d/%m/%Y %H:%M',$var[0],true),
		'messageTxt'	=>	$var[1]
	));
	$sub_template->parse('messages', true);
}
?>
