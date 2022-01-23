<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('admin_whois')) $site->error(1);

$utils = new utils();

$template->setFile('centre','admin/whois.html');

$template->setVar(array(
	'whoisIp'		=>	str_replace('-','.',$_GET['ip']),
	'whoisWhois'	=>	$utils->whois(str_replace('-','.',$_GET['ip']))
));
?>