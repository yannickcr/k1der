<?php
/**
 * Vérification des accès
 */
if($membres->infos('clan_nom')!='' && !ereg('postul_(.*)',$membres->infos('clan_nom'))) $site->error('Vous possdez dj un clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/join.html');
$site->addToTitle(' - Rejoindre un clan');

/**
 * Action envoi formulaire
 */
if($this->action('annulerJoin','clans')) $clans->annulerJoin();
else if($this->action('newJoin','clans')) {
	$clans->newJoin($_POST['clan'],$sub_template);
	$sub_template->setVar('clan',$_POST['clan']);
}

$sub_template->setBlock('centredroite','postuldeja');
$sub_template->setBlock('centredroite','nopostul');


if(ereg('postul_(.*)',$membres->infos('clan_nom'))) {
	$sub_template->setVar('clan',str_replace('postul_','',$membres->infos('clan_nom')));
	$sub_template->parse('postuldeja', true);
} else {
	$site->addJs('modules/clans/js/livesearchclan.js');
	$sub_template->parse('nopostul', true);
}
?>