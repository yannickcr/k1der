<?php
$forum = new forum();

$this->noUse(array('gauche','droite'));
$template->setFile('centre','forum/move_topic.html');
$site->addCss('templates/'.THEME.'/forum/style.css');
$site->addToTitle(' - Forum - Déplacer un sujet');

/**
 * Construction de la barre du haut
 */
$forum->makeHaut($template,2,0);

/**
 * Requete SQL
 */
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		mod_forum_cats.nom		AS	catNom,
		mod_forum_cats.id		AS	catId,
		mod_forum_forums.titre	AS	forumTitre,
		mod_forum_forums.id		AS	forumId,
		mod_forum_topics.titre	AS	topicTitre
	FROM 
		mod_forum_topics LEFT JOIN mod_forum_forums ON mod_forum_topics.forum_id=mod_forum_forums.id,
		mod_forum_forums AS forums LEFT JOIN mod_forum_cats ON forums.cat=mod_forum_cats.id
	WHERE 
		mod_forum_topics.id="'.$_GET['topic'].'"
'));

/**
 * Vérification des droits d'accès (le mme que celui pour supprimer)
 */
if(!$membres->verifAcces('forum_'.$info['forumId'].'_del')) $site->error('<p>Vous n\'avez pas l\'autorisation de déplacer ce sujet.</p>');

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('moveTopic')) $forum->moveTopic($_GET['topic'],$_POST['forum']);

$template->setVar('message','Slectionnez le forum dans lequel déplacer le sujet "'.$info['topicTitre'].'" :');

$template->setVar(array(
	'catTitre'			=>	$info['catNom'],
	'catLinkTitre'		=>	$string->clean($info['catNom']),
	'catId'				=>	$info['catId'],
	'forumTitre'		=>	$info['forumTitre'],
	'forumLinkTitre'	=>	$string->clean($info['forumTitre']),
	'forumId'			=>	$info['forumId'],
	'topicTitre'		=>	$info['topicTitre'],
	'topicLinkTitre'	=>	$string->clean($info['topicTitre']),
	'topicId'			=>	$_GET['topic'],
	'option'			=>	$_POST['option']
));

$template->setBlock('centre','options');
$template->setBlock('centre','group');

$req=$sql->query('SELECT id,nom FROM mod_forum_cats ORDER BY ordre');
while($info2=$sql->fetchAssoc($req)) {
	$template->setVar('groupNom',$info2['nom']);
	$sub_req=$sql->query('SELECT id,titre FROM mod_forum_forums WHERE cat="'.$info2['id'].'" && id!="'.$info['forumId'].'" ORDER BY ordre');
	while($sub_info=$sql->fetchAssoc($sub_req)) {
		$template->setVar(array(
			'optionsId'=>$sub_info['id'],
			'optionsTitre'=>$sub_info['titre'],
		));
		$template->parse('options', true);
	}
	$template->parse('group', true);
	$template->clearVar('options');
}
?>