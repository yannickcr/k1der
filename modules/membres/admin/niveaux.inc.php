<?php
/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('niveauxMembres')) $membres->niveauxMembres($_POST);

$site->addCss('templates/'.THEME.'/membres/admin/style.css');
$site->addJs('modules/membres/admin/js/niveaux.js');

$site->addToTitle(' - Membres - Niveaux');

$sub_template->setFile('centredroite','membres/admin/niveaux.html');


$sub_template->setBlock('centredroite','imgs');
$sub_template->setBlock('centredroite','images');
$sub_template->setBlock('centredroite','lignes');

$images=$string->listDir('templates/'.THEME.'/images/membres');

$level=unserialize($site->config('membres_level'));
$j=1;

// Boubles pour les diffrentes lignes
foreach($level as $i=>$var) {

	// Valeurs générales
	$sub_template->setVar(array(
		'num'=>$j,
		'design'=>$var[0],
		'points'=>$i,
		'nbImages'=>$var[2]
	));

	// Boucle des images
	for ($k=0;$k<$var[2];$k++) {
		$sub_template->setVar('img',$var[1]);
		$sub_template->parse('imgs',true);
	}

	// Boucle de la liste des images
	foreach($images as $img) {
		$sub_template->setVar('image',$img);
		if($var[1]==$img) $sub_template->setVar('selected','selected="selected"');
		else $sub_template->setVar('selected','');
		$sub_template->parse('images',true);
	}
	$sub_template->parse('lignes',true);
	$sub_template->clearVar(array('images','imgs'));
	$j++; // On incrmente le numro de ligne
}



?>