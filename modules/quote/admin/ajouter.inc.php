<?php
$site->addToTitle(' - Ajouter une phrase');
$sub_template->setFile('centredroite','quote/admin/ajouter.html');
$site->addCss('templates/'.THEME.'/quote/style.css');

$quote = new quote();

/**
 * Action envoi formulaire
 */
if($this->action('addPhrase')) $quote->addPhrase($_POST['auteur'],$_POST['phrase']);

# Infos persos
/*$sub_template->setVar(array(
	'auteur'	=>	$info['auteur'],
	'phrase'	=>	$info['phrase']
));*/
?>