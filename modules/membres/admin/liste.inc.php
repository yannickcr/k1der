<?php
$sub_template->setFile('centredroite','membres/admin/liste.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/membres/admin/js/conf.js');
$site->addToTitle(' - Membres - Liste des membres');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('niveauxMembres')) $membres->niveauxMembres($_POST);




$sub_template->setBlock('centredroite','membre');

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
		mod_membres.id,
		pseudo,
		groupes.name AS groupe,
		part 
	FROM 
		mod_membres LEFT JOIN groupes ON mod_membres.groupe=groupes.id 
	ORDER BY 
		pseudo
	LIMIT '.$start.','.$nb.'
');

/**
 * Pagination
 */
$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_membres'));
$membres->makePages($sub_template,ceil($info['nb']/$nb),($start/$nb)+1,$nb,$start);
if(ceil($info['nb']/$nb)>1) $sub_template->parse('paginnationtopic', true);

while($info=$sql->fetchAssoc($res)) {
	$sub_template->setVar(array(
		'membreId'=>$info['id'],
		'membrePseudo'=>$info['pseudo'],
		'membreGroupe'=>$info['groupe'],
		'membrePart'=>$info['part']
	));
	$sub_template->parse('membre', true);
}
?>