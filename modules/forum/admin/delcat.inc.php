<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_delcat')) $site->error(1);

$forum = new forum();

if($this->action('supprCat')) $forum->supprCat($_POST['moveto']);

$site->addToTitle(' - Forum - Supprimer une catégorie');

$sub_template->setFile('centredroite','forum/admin/delcat.html');

$sub_template->setBlock('centredroite','cats');


$sub_template->setVar('currentCatId',$_GET['id']);

$req=$sql->query('SELECT id,nom FROM mod_forum_cats WHERE id!="'.$_GET['id'].'" ORDER BY ordre');
while($info=$sql->fetchAssoc($req)) {
	$sub_template->setVar(array(
		'catId'=>$info['id'],
		'catNom'=>$info['nom']
	));
	$sub_template->parse('cats', true);
}
?>