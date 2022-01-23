<?php
/**
 * Droite de la page.
 * Y affiche les modules du site (non implenté pour).
 *
 * @author    Yannick Croissant
 * @package   K1der
 */
$template->setFile('gauche','gauche.html');

$template->setBlock('gauche','modulesgauche');

$right=unserialize($this->config('module_left'));

foreach($right as $var) {
	$fichier='modules/'.$var.'/module.inc.php';
	if(file_exists($fichier)) {
		require ($fichier);
		$template->setVar('modulecontent',$sub_template->globalParse('parse','module',true));
		$template->parse('modulesgauche', true);
	}
}
$this->toparse[]='gauche';
?>