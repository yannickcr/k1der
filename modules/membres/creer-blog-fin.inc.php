<?php
$sub_template->setFile('centredroite','membres/creer-blog-fin.html');  
$site->addToTitle(' - Créer son blog (fin)');

$sub_template->setVar(array(
	'pseudo'	=>	strtolower($membres->infos('pseudo')),
	'mail'		=>	$membres->infos('mail')
));

if(isset($erreurs)) $site->showErrors($sub_template,'centredroite',$erreurs);
?>