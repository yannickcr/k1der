<?php
$template->setFile('centre','shoutbox/index.html');
$site->addToTitle(' - Shoutbox - Historique des messages');
$site->addCss('templates/'.THEME.'/shoutbox/style.css');

$bbcode = new bbcode;

$template->setBlock('centre','message');

$template->setBlock('centre','pages.num');
$template->setBlock('centre','pages.first');
$template->setBlock('centre','pages.prev');
$template->setBlock('centre','pages.mm');
$template->setBlock('centre','pages.m');
$template->setBlock('centre','pages.c');
$template->setBlock('centre','pages.p');
$template->setBlock('centre','pages.pp');
$template->setBlock('centre','pages.next');
$template->setBlock('centre','pages.last');
$template->setBlock('centre','paginnation');

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
$membres->makePages($template,ceil($info['nb']/$nb),($start/$nb)+1,$nb,$start);
if(ceil($info['nb']/$nb)>1) $template->parse('paginnation', true);


while($info=$sql->fetchAssoc($res)) {
	$template->setVar(array(
		'messageId'			=>	$info['id'],
		'messageAuteur'		=>	$info['auteur'],
		'messageCleanAuteur'=>	$string->clean($info['auteur']),
		'messageMessage'	=>	$bbcode->MiniBBCodeToHtml($info['message']),
		'messageDate'		=>	$string->formatDate('%d %B %Y  %H:%M',$info['date'],true)
	));
	$template->parse('message', true);
}
?>