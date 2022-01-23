<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_rencat')) $site->error(1);

$forum = new forum();

if($this->action('renCat')) $forum->renCat($_POST['nom']);

$site->addToTitle(' - Forum - Renommer une catégorie');

$sub_template->setFile('centredroite','forum/admin/rencat.html');

$info=$sql->fetchAssoc($sql->query('SELECT nom FROM mod_forum_cats WHERE id="'.$_GET['id'].'"'));
$sub_template->setVar('catNom',$info['nom']);
?>