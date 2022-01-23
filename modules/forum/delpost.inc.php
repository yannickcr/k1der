<?php
$forum = new forum();

$this->noUse(array('gauche','droite'));
$template->setFile('centre','forum/del_post.html');
$site->addCss('templates/'.THEME.'/forum/style.css');
$site->addToTitle(' - Forum - Supprimer un message');

/**
 * Construction de la barre du haut
 */
$forum->makeHaut($template,2,0);

/**
 * Requete SQL
 */
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		mod_forum_posts.auteur_name auteurName,
		mod_forum_posts.forum_id	forumId,
		mod_forum_posts.topic_id	topicId,
		mod_forum_cats.nom			catNom,
		mod_forum_cats.id			catId,
		mod_forum_forums.titre		forumTitre,
		mod_forum_topics.titre		topicTitre
	FROM 
		mod_forum_posts,
		mod_forum_topics,
		mod_forum_forums,
		mod_forum_cats
	WHERE 
		mod_forum_posts.id="'.$_GET['del'].'" && 
		mod_forum_posts.topic_id=mod_forum_topics.id && 
		mod_forum_posts.forum_id=mod_forum_forums.id && 
		mod_forum_forums.cat=mod_forum_cats.id
'));

/**
 * Vérification des droits d'accès
 */
if(!$membres->verifAcces('forum_'.$info['forumId'].'_del')) $site->error('<p>Vous n\'avez pas l\'autorisation de supprimer ce post.</p>');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('delPost')) $forum->delPost($_GET['del']);

$template->setVar('message','Voulez-vous vraiment supprimer le message de '.$info['auteurName'].' ?');

$template->setVar(array(
	'forum.titre-cat'			=>	$info['catNom'],
	'forum.clean-titre-cat'		=>	$string->clean($info['catNom']),
	'forum.id-cat'				=>	$info['catId'],
	'forum.titre-forum'			=>	$info['forumTitre'],
	'forum.clean-titre-forum'	=>	$string->clean($info['forumTitre']),
	'forum.id-forum'			=>	$info['forumId'],
	'forum.titre-topic'			=>	$info['topicTitre'],
	'forum.clean-titre-topic'	=>	$string->clean($info['topicTitre']),
	'forum.id-topic'			=>	$info['topicId']
));
	

?>