<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('clans_lister')) $site->error(1);

$sub_template->setFile('centredroite','clans/admin/liste.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/clans/admin/js/conf.js');
$site->addToTitle(' - Membres - Liste des clans');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('niveauxMembres')) $membres->niveauxMembres($_POST);

$sub_template->setBlock('centredroite','clan');

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
		mod_membres_clans.id,
		mod_membres_clans.nom,
		leader_pseudo,
		count(*) AS num
	FROM 
		mod_membres_clans LEFT JOIN mod_membres ON mod_membres_clans.id=clan_id
	GROUP BY mod_membres_clans.id
	ORDER BY 
		nom
	LIMIT '.$start.','.$nb.'
');

/**
 * Pagination
 */
$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_membres_clans'));
$membres->makePages($sub_template,ceil($info['nb']/$nb),($start/$nb)+1,$nb,$start);
if(ceil($info['nb']/$nb)>1) $sub_template->parse('paginnationtopic', true);

while($info=$sql->fetchAssoc($res)) {
	$sub_template->setVar(array(
		'clanId'=>$info['id'],
		'clanNom'=>$info['nom'],
		'clanCleanNom'=>str_replace(' ','-',$info['nom']),
		'clanLeader'=>$info['leader_pseudo'],
		'clanMembres'=>$info['num']
	));
	$sub_template->parse('clan', true);
}
?>