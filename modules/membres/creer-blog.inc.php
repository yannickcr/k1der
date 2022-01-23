<?php
$sub_template->setFile('centredroite','membres/creer-blog.html');  
$site->addToTitle(' - Créer son blog');

$sub_template->setBlock('centredroite','blog');
$sub_template->setBlock('centredroite','noblog');
 
if ($membres->checkBlog($membres->infos('pseudo'))) $sub_template->parse('blog');
else $sub_template->parse('noblog');
 
 
/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('creerBlog')) $erreurs=$membres->createBlog($_POST['pass'],$_POST['pass2']);

$sub_template->setVar('pseudo',strtolower($membres->infos('pseudo')));

if(isset($erreurs)) $site->showErrors($sub_template,'centredroite',$erreurs);
?>