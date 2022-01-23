<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_emptyforum')) $site->error(1);

$forum = new forum();

if($this->action('emptyForum')) $forum->emptyForum($_GET['id']);

$site->addToTitle(' - Forum - Vider un forum');

$sub_template->setFile('centredroite','forum/admin/emptyforum.html');

$info=$sql->fetchAssoc($sql->query('SELECT titre FROM mod_forum_forums WHERE id="'.$_GET['id'].'"'));
$sub_template->setVar('forumNom',$info['titre']);
?>