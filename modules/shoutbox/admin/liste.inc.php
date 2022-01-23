<?php
$sub_template->setFile('centredroite','shoutbox/admin/liste.html');
$site->addJs('modules/shoutbox/admin/js/conf.js');
$site->addToTitle(' - Shoutbox - Liste des messages');

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
		date,
		auteur,
		ip,
		message
	FROM 
		mod_shoutbox
	ORDER BY 
		date DESC
	LIMIT '.$start.','.$nb.'
');

/**
 * Pagination
 */
$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_shoutbox'));
$membres->makePages($sub_template,ceil($info['nb']/$nb),($start/$nb)+1,$nb,$start);
if(ceil($info['nb']/$nb)>1) $sub_template->parse('paginnationtopic', true);


while($info=$sql->fetchAssoc($res)) {
	$sub_template->setVar(array(
		'messageId'			=>	$info['id'],
		'messageAuteur'		=>	$info['auteur'],
		'messageCleanAuteur'=>	$string->clean($info['auteur']),
		'messageIp'			=>	$info['ip'],
		'messageCleanIp'	=>	str_replace('.','-',$info['ip']),
		'messageMessage'	=>	$bbcode->MiniBBCodeToHtml($info['message']),
		'messageDate'		=>	$string->formatDate('%d %B %Y  %H:%M',$info['date'],true)
	));
	$sub_template->parse('message', true);
}
?>