<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_delforum')) $site->error(1);

$forum = new forum();

if($this->action('supprForum')) $forum->supprForum($_POST['moveto']);

$site->addToTitle(' - Forum - Supprimer un forum');

$sub_template->setFile('centredroite','forum/admin/delforum.html');

$sub_template->setBlock('centredroite','forums');
$sub_template->setBlock('centredroite','cats');


$req=$sql->query('SELECT id,nom FROM mod_forum_cats ORDER BY ordre');
while($info=$sql->fetchAssoc($req)) $cat[$info['id']]=$info['nom'];
	
$req=$sql->query('SELECT id,titre,cat FROM mod_forum_forums WHERE id!="'.$_GET['id'].'" ORDER BY ordre');
while($info=$sql->fetchAssoc($req)) {
	$for[$info['id']]['cat']=$info['cat'];
	$for[$info['id']]['nom']=$info['titre'];
}
foreach($cat as $i=>$val) {
	$sub_template->setVar('catNom',$val);
	foreach($for as $j=>$val2) {
		if($val2['cat']==$i) {
			$sub_template->setVar('forumId',$j);
			$sub_template->setVar('forumNom',$val2['nom']);
			$sub_template->parse('forums', true);
		}
	}
	$sub_template->parse('cats', true);
	$sub_template->unsetVar(array('forums'));
}
?>