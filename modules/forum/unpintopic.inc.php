<?php
$forum = new forum();

$this->noUse(array('gauche','droite'));
$template->setFile('centre','forum/unpin_topic.html');
$site->addCss('templates/'.THEME.'/forum/style.css');
$site->addToTitle(' - Forum - D&Eacute;s&eacute;pingler un sujet');

/**
 * Construction de la barre du haut
 */
$forum->makeHaut($template,2,0);

/**
 * Requete SQL
 */
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		mod_forum_cats.id		AS	catId,
		mod_forum_cats.nom		AS	catTitre,
		mod_forum_forums.id		AS	forumId,
		mod_forum_forums.titre	AS	forumTitre,
		mod_forum_topics.titre	AS	topicTitre
	FROM 
		mod_forum_topics LEFT JOIN mod_forum_forums ON mod_forum_topics.forum_id=mod_forum_forums.id,
		mod_forum_forums AS forums LEFT JOIN mod_forum_cats ON forums.cat=mod_forum_cats.id
	WHERE 
		mod_forum_topics.id="'.$_GET['topic'].'"
'));

/**
 * Vérification des droits d'accès
 */
if(!$membres->verifAcces('forum_'.$info['forumId'].'_del')) $site->error('<p>Vous n\'avez pas l\'autorisation de dspingler ce sujet.</p>');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('unpinTopic')) $forum->unpinTopic($_GET['topic']);

$template->setVar('message','Voulez-vous vraiment dspingler le sujet "'.$info['topicTitre'].'" ?');

$template->setVar(array(
	'catTitre'			=>	$info['catTitre'],
	'catLinkTitre'		=>	$string->clean($info['catTitre']),
	'catId'				=>	$info['catId'],
	'forumTitre'		=>	$info['forumTitre'],
	'forumLinkTitre'	=>	$string->clean($info['forumTitre']),
	'forumId'			=>	$info['forumId'],
	'option'			=>	$_POST['option']
));


?>