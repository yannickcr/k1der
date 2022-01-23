<?php
$shoutbox = new shoutbox();

if($this->action('config')) $shoutbox->config($_POST['maxcaract'],$_POST['maxlength']);

$site->addToTitle(' - Shoutbox - Configuration');

$sub_template->setFile('centredroite','shoutbox/admin/configuration.html');

$sub_template->setVar(array(
	'maxcaract'	=>	$site->config('shoutbox_max_caract'),
	'maxlength'	=>	$site->config('shoutbox_max_length')
));
?>