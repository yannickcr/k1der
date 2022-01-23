<?php
/**
 * Vérification des accès
 */
if($clans->isClanLeader()==false) $site->error('Seul le leader peut grer les membres du clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/membres.html');
$site->addToTitle(' - Membres du clan');
$site->addJs('modules/clans/js/confrenv.js');

$sub_template->setBlock('centredroite','nomembres');
$sub_template->setBlock('centredroite','membres');

/**
 * Listing des membres
 */
$res=$sql->query('SELECT id,pseudo FROM mod_membres WHERE clan_id="'.$membres->infos('clan_id').'" && clan_nom="'.$membres->infos('clan_nom').'" && id!="'.$membres->infos('id').'"');
if($sql->numRows($res)==0) {
	$sub_template->parse('nomembres', true);
} else {
	while($info=$sql->fetchAssoc($res)) {
		$sub_template->setVar(array(
			'membreId'		=>	$info['id'],
			'membrePseudo'	=>	$info['pseudo']
		));
		$sub_template->parse('membres', true);
	}
}
?>
