<?php
$sub_template->setFile('centredroite','membres/admin/liste-messages.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/membres/admin/js/conf.js');
$site->addToTitle(' - Membres - Liste des messages automatiques');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('niveauxMembres')) $membres->niveauxMembres($_POST);

$site->error('Page en travaux');

$messages=array(
	'1'	=>	'Demande de postulation acceptée',
	'2'	=>	'Demande de postulation refusée',
	'3'	=>	'Fermeture du clan',
	'4'	=>	'Fermeture d\'un clan auquel on postulé',
);


$sub_template->setBlock('centredroite','membre');

if(isset($_GET['param'])) $start=$_GET['param'];
else $start=0;
$res=$sql->query('
	SELECT 
		name
	FROM 
		config 
	WHERE
		name like "membres_%_title" && 
		name not like "membre_mail_%_title"
	ORDER BY 
		name
');

/**
 * Pagination
 */

while($info=$sql->fetchAssoc($res)) {
	$seg=explode('_',$info['name']);
	$sub_template->setVar(array(
		'messageId'=>$seg[1],
		'messageNom'=>$messages[$seg[1]],
	));
	$sub_template->parse('membre', true);
}
?>