<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_addforum')) $site->error(1);

$forum = new forum();

if($this->action('addForum')) $forum->addForum($_POST['cat'],$_POST['nom'],$_POST['descr']);

$site->addToTitle(' - Forum - Ajouter un forum');
$site->addCss('templates/'.THEME.'/forum/admin/style.css');

$sub_template->setFile('centredroite','forum/admin/addforum.html');
$sub_template->setBlock('centredroite','cats');
$req=$sql->query('SELECT id,nom FROM mod_forum_cats ORDER BY ordre');
while($info=$sql->fetchAssoc($req)) {
	$sub_template->setVar('catId',$info['id']);
	$sub_template->setVar('catNom',$info['nom']);
	$sub_template->parse('cats', true);
}
?>