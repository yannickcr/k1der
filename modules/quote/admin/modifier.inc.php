<?php
$site->addToTitle(' - &Eacute;diter une phrase');
$sub_template->setFile('centredroite','quote/admin/modifier.html');
$site->addCss('templates/'.THEME.'/quote/style.css');

$quote = new quote();

/**
 * Action envoi formulaire
 */
if($this->action('editPhrase')) $quote->editPhrase($_GET['id'],$_POST['auteur'],$_POST['phrase']);

$info=$sql->fetchArray($sql->query('
	SELECT 
		id,
		auteur,
		phrase
	FROM 
		mod_quote
	WHERE
		id='.$_GET['id']));


# Infos persos
$sub_template->setVar(array(
	'auteur'	=>	$info['auteur'],
	'phrase'	=>	$info['phrase']
));
?>