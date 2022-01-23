<?php
/**
 * Bas de la page.
 * Son style CSS est diffrent suivant que l'on utilise le bloc de gauche ou pas.
 *
 * @author    Yannick Croissant
 * @package   K1der
 */
$template->setFile('bas','bas.html');  
$template->setVar('annee',date('Y')); 
$template->setBlock('bas','name');

if($membres->infos('id')) {
	$template->setVar('membrePseudo',$membres->infos('pseudo'));
	$template->parse('name');
}

$this->toparse[]='bas';
?>