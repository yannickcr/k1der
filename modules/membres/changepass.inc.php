<?php
/**
 * Construction de la page
 */
$site->addToTitle(' - Changer mon mot de passe');
$sub_template->setFile('centredroite','membres/changepass.html');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('changeMDP')) $membres->changeMDP($_POST['pass'],$_POST['confpass'],$sub_template);
?>