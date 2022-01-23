<?php
$template->setFile('centre','shoutbox/aide.html');
$site->addToTitle(' - Shoutbox - Aide');
$site->addCss('templates/'.THEME.'/shoutbox/style.css');

$template->setBlock('centre','smileys');

// Smileys existants

$tab=unserialize($site->config('smileys'));
$k=0;
foreach($tab as $i=>$val) {
		$template->setVar(array(
			'smileyTxt'	=>	$i,
			'smileyImg'	=>	$val
		));
	$template->parse('smileys', true);
}
?>