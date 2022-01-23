<?php
$template->setFile('centre','association/contacts.html');
$site->addToTitle(' - Association - Contacts');
$site->addCss('templates/'.THEME.'/association/style.css');

$template->setVar('contact',$membres->antiBot('contact@re-so.com'));
?>