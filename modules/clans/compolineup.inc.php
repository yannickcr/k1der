<?php
require_once ('modules/clans/class.clans.php');
$clans = new Clans();

/**
 * Vérification des accès
 */
if($clans->isClanLeader()==false) $site->error('Seul le leader peut gérer les membres du clan.');

/**
 * Construction de la page
 */
$this->noUse(array('gauche','droite'));
$template->setFile('centre','clans/compolineup.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addToTitle(' - Composition des Lines Up');

$template->setBlock('centre','lineuptitle');
$template->setBlock('centre','checkline');
$template->setBlock('centre','membres');

/**
 * Récupération des lines up sous forme de tableau
 */
$info=$sql->fetchArray($sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"'));
$lineups=unserialize($info['lineup']);

foreach($lineups as $i=>$var) {
	$template->setVar('lineupTitre',$i);
	$template->parse('lineuptitle', true);
}
/**
 * Listing des membres
 */
$res=$sql->query('SELECT id,pseudo FROM mod_membres WHERE clan_id="'.$membres->infos('clan_id').'" && clan_nom="'.$membres->infos('clan_nom').'"');
while($info=$sql->fetchArray($res)) {
	$template->setVar('membrePseudo',$info['pseudo']);
	foreach($lineups as $i=>$var) {
		if(in_array($info['pseudo'],$var)) $template->setVar('inLine','oui');
		$template->parse('checkline', true);
	$template->unsetVar('inLine');
	}
	$template->parse('membres', true);
	$template->unsetVar('checkline');
}
?>