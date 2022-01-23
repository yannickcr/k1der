<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_config')) $site->error(1);

$forum = new forum();

if($this->action('configForum')) $forum->configForum($_POST['nbmess']);

$site->addToTitle(' - Forum - Configuration du forum');

$sub_template->setFile('centredroite','forum/admin/configuration.html');

$sub_template->setBlock('centredroite','nbmess');

$tab=array(1,5,10,15,20,50,100);
foreach($tab as $val) {
	$sub_template->setVar('choix',$val);
	if($site->config('forum_nbmess')==$val) $sub_template->setVar('selected',' selected="selected"');
	$sub_template->parse('nbmess', true);
	$sub_template->unsetVar(array('selected'));
}
?>