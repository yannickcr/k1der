<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_editforum')) $site->error(1);

$forum = new forum();
if($this->action('editForum')) $forum->editForum($_POST);

$site->addToTitle(' - Forum - Ajouter un forum');
$site->addCss('templates/'.THEME.'/forum/admin/style.css');

$sub_template->setFile('centredroite','forum/admin/editforum.html');
$sub_template->setBlock('centredroite','cats');
$info=$sql->fetchAssoc($sql->query('SELECT cat,titre,descr,special FROM mod_forum_forums WHERE id="'.$_GET['id'].'"'));
$sub_template->setVar('nom',$info['titre']);
$sub_template->setVar('descr',$info['descr']);

// Liste des catégories
$req=$sql->query('SELECT id,nom FROM mod_forum_cats ORDER BY ordre');
while($cat=$sql->fetchAssoc($req)) {
	$sub_template->setVar('catId',$cat['id']);
	$sub_template->setVar('catNom',$cat['nom']);
	if($info['cat']==$cat['id']) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->setVar('selected','');
	$sub_template->parse('cats', true);
}

/**
 * Options spciales
 */
$special=unserialize($info['special']);
if(is_array($special) && in_array('news',$special)) $sub_template->setVar('checkedNews',' checked="checked"');
?>