<?php
/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','membres/infos.html');
$site->addToTitle(' - Informations');

$sub_template->setVar(array(
	'pseudo'	=>	$membres->infos('pseudo'),
	'mail'		=>	$membres->infos('mail'),
	'posts'		=>	$membres->infos('posts'),
	'date_ins'	=>	$string->formatDate('%A %d %B %Y',$membres->infos('date_ins'),true).'  '.$string->formatDate('%H:%M',$membres->infos('date_ins')),
	'posts'		=>	$membres->infos('posts'),
	'moyenne'	=>	round($membres->infos('posts')/$string->diffDate($membres->infos('date_ins'),date('U')),2)
));
?>