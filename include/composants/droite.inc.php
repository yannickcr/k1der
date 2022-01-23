<?php
/**
 * Droite de la page.
 * Y affiche les modules du site.
 *
 * @author    Yannick Croissant
 * @package   K1der
 */
$template->setFile('droite','droite.html');

$template->setBlock('droite','modulesdroite');

$right=unserialize($this->config('module_right'));

foreach($right as $var) {
	$fichier='modules/'.$var.'/module.inc.php';
	if(file_exists($fichier)) {
		require ($fichier);
		$template->setVar('modulecontent',$sub_template->globalParse('parse','module',true));
		$template->parse('modulesdroite', true);
	}
}

$this->toparse[]='droite';
?>