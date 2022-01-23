<?php
$sub_template->setFile('centredroite','matches/admin/liste.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/matches/admin/js/conf.js');
$site->addToTitle(' - Matches - Liste des matches');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('niveauxMembres')) $membres->niveauxMembres($_POST);

$sub_template->setBlock('centredroite','match');

$sub_template->setBlock('centredroite','pages.num');
$sub_template->setBlock('centredroite','pages.first');
$sub_template->setBlock('centredroite','pages.prev');
$sub_template->setBlock('centredroite','pages.mm');
$sub_template->setBlock('centredroite','pages.m');
$sub_template->setBlock('centredroite','pages.c');
$sub_template->setBlock('centredroite','pages.p');
$sub_template->setBlock('centredroite','pages.pp');
$sub_template->setBlock('centredroite','pages.next');
$sub_template->setBlock('centredroite','pages.last');
$sub_template->setBlock('centredroite','paginnationtopic');

if(isset($_GET['param'])) $start=$_GET['param'];
else $start=0;

// Nombre de message par page
$nb=20;

$res=$sql->query('
	SELECT 
		id,
		jeu,
		mode,
		adversaire,
		date,
		lieu,
		lieu_nom,
		lieu_id,
		lineup2
	FROM 
		mod_matches
	ORDER BY 
		date
	LIMIT '.$start.','.$nb.'
');

/**
 * Pagination
 */
$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_matches'));
$membres->makePages($sub_template,ceil($info['nb']/$nb),($start/$nb)+1,$nb,$start);
if(ceil($info['nb']/$nb)>1) $sub_template->parse('paginnationtopic', true);


while($info=$sql->fetchAssoc($res)) {

	if(empty($info['adversaire'])) {
		$info['lineup2']=unserialize($info['lineup2']);
		for($i=0;isset($info['lineup2'][$i]) && $i<3;$i++) {
			if(ereg("^([0-9]+)$",$info['lineup2'][$i])) $info['adversaire'][$i]=$membres->getPseudo($info['lineup2'][$i]);
			else $info['adversaire'][$i]=$info['lineup2'][$i];
		}
		$info['adversaire']=implode(', ',$info['adversaire']).'...';
	}

	$sub_template->setVar(array(
		'matchId'		=>	$info['id'],
		'matchCleanClan'=>	$string->clean($site->config('clan_default')),
		'matchAdv'		=>	$info['adversaire'],
		'matchCleanAdv'	=>	$string->clean($info['adversaire']),
		'matchJeu'		=>	$info['jeu'],
		'matchMode'		=>	$info['mode'],
		'matchDate'		=>	$string->formatDate('%d %B %Y',$info['date'],true),
		'matchLieu'		=>	($info['lieu_id']!=0)?$info['lieu_nom']:$info['lieu']
	));
	$sub_template->parse('match', true);
}
?>