<?php
if(strlen($_GET['activer'])==32 && $membres->activer()==false) {
	$message='<p>Ce compte n\'existe pas ou a d�j� �t� activ�.</p>';
	$site->error($message);
} else if($_GET['activer']=='ok') {
	$template->setFile('centre','membres/activer.html');
}
?>