<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_editdroits')) $site->error(1);

$forum = new forum();
if($this->action('editDroits')) $forum->editDroits();

$site->addToTitle(' - Forum - Modifier les droits d\'accès');
$site->addCss('templates/'.THEME.'/forum/admin/style.css');

$sub_template->setFile('centredroite','forum/admin/editdroits.html');
$sub_template->setBlock('centredroite','cats');
$req=$sql->query('SELECT id,acces FROM groupes ORDER BY id');
$tab=array(1=>'B',2=>'V',3=>'M',4=>'A');
while($info=$sql->fetchAssoc($req)) {
	if(ereg('\|'.$membres->acces['forum_'.$_GET['id'].'_read'].'\|',$info['acces'])) 	$sub_template->setVar('read'.$tab[$info['id']],'checked="checked"');
	if(ereg('\|'.$membres->acces['forum_'.$_GET['id'].'_reply'].'\|',$info['acces'])) 	$sub_template->setVar('reply'.$tab[$info['id']],'checked="checked"');
	if(ereg('\|'.$membres->acces['forum_'.$_GET['id'].'_start'].'\|',$info['acces'])) 	$sub_template->setVar('start'.$tab[$info['id']],'checked="checked"');
	if(ereg('\|'.$membres->acces['forum_'.$_GET['id'].'_edit'].'\|',$info['acces'])) 	$sub_template->setVar('edit'.$tab[$info['id']],'checked="checked"');
	if(ereg('\|'.$membres->acces['forum_'.$_GET['id'].'_del'].'\|',$info['acces'])) 	$sub_template->setVar('del'.$tab[$info['id']],'checked="checked"');
}
?>