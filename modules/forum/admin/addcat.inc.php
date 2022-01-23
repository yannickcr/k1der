<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_addcat')) $site->error(1);

$forum = new forum();

if($this->action('addCat')) $forum->addCat($_POST['nom']);

$site->addToTitle(' - Forum - Ajouter une catégorie');

$sub_template->setFile('centredroite','forum/admin/addcat.html');
?>