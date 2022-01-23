<?php
$site->addToTitle(' - &Eacute;diter un message');
$sub_template->setFile('centredroite','shoutbox/admin/modifier.html');
$site->addCss('templates/'.THEME.'/shoutbox/style.css');

$shoutbox = new shoutbox();

/**
 * Action envoi formulaire
 */
if($this->action('editMessage')) $shoutbox->editMessage($_GET['id'],$_POST['message'],1);

$info=$shoutbox->getMessage($_GET['id']);

# Infos persos
$sub_template->setVar(array(
	'messageAuteur'	=>	$info['auteur'],
	'messageDate'	=>	$string->formatDate('%d %B %Y  %H:%M',$info['date'],true),
	'messageMessage'=>	$info['txt']
));
?>