<?php
$sub_template->setFile('centredroite','membres/admin/modifier-message.html');
$site->addToTitle(' - Membres - Modifier un message');
$site->addCss('templates/'.THEME.'/membres/admin/style.css');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('modifierMessage')) $membres->modifierMessage($_GET['id'],$_POST['messagetitle'],$_POST['messagetxt']);

$sub_template->setVar('messageTitle',$site->config('membres_'.$_GET['id'].'_title'));
$sub_template->setVar('messageTxt',$site->config('membres_'.$_GET['id'].'_txt'));
?>