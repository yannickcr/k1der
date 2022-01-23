<?php
$sub_template->setFile('centredroite','quote/admin/liste.html');
$site->addJs('modules/quote/admin/js/conf.js');
$site->addToTitle(' - Phrases  la con - Liste des phrases');

$bbcode = new bbcode;

$sub_template->setBlock('centredroite','message');

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
$nb=100;

$res=$sql->query('
	SELECT 
		id,
		auteur,
		phrase
	FROM 
		mod_quote
	ORDER BY 
		id DESC
	LIMIT '.$start.','.$nb.'
');

/**
 * Pagination
 */
$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_quote'));
$membres->makePages($sub_template,ceil($info['nb']/$nb),($start/$nb)+1,$nb,$start);
if(ceil($info['nb']/$nb)>1) $sub_template->parse('paginnationtopic', true);


while($info=$sql->fetchAssoc($res)) {
	$sub_template->setVar(array(
		'id'			=>	$info['id'],
		'auteur'		=>	$info['auteur'],
		'phrase'		=>	$bbcode->MiniBBCodeToHtml($info['phrase'])
	));
	$sub_template->parse('message', true);
}
?>