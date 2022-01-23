<?php
/**
 * Vérification des accès
 */
if($clans->isClanLeader()==false) $site->error('Seul le leader peut accepter ou refuser de nouveaux joueurs dans le clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/postul.html');
$site->addToTitle(' - Postulants au clan');

$sub_template->setBlock('centredroite','nopostulants');
$sub_template->setBlock('centredroite','postulants');

/**
 * Récupération des postulants
 */
$info=$sql->fetchAssoc($sql->query('SELECT postulants FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
$postulants=unserialize($info['postulants']);
if(!is_array($postulants) || count($postulants)==0) {
	$postulants=array();
	$sub_template->parse('nopostulants', true);
} else {
	foreach($postulants as $i=>$var) {
		$sub_template->setVar(array(
			'date'			=>	$string->formatDate('%d/%m/%Y %H:%M',$var['date'],true),
			'membreId'		=>	$i,
			'membrePseudo'	=>	$var['membre_pseudo']
		));
		$sub_template->parse('postulants', true);
	}
}
?>
