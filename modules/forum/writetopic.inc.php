<?php
$forum = new forum();

$bbcode = new bbcode;

/**
 * Construction de la page
 */
$template->setFile('centre','forum/write_topic.html');  
$this->noUse(array('gauche','droite'));
$site->addCss('templates/'.THEME.'/forum/style.css');

/**
 * Déclaration des blocks du template
 * Ordre : Intrieur->Exterieur
 */
$template->setBlock('centre','visiteur');
$template->setBlock('centre','forum.smileys');
$template->setBlock('centre','forum.posts');
$template->setBlock('centre','poll');

/**
 * Informations sur le sujet et le forum
 */
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		mod_forum_cats.id			AS	catId,
		mod_forum_cats.nom			AS	catTitre,
		mod_forum_forums.id			AS	forumId,
		mod_forum_forums.titre		AS	forumTitre,
		mod_forum_forums.special	AS	forumSpecial
	FROM 
		mod_forum_forums LEFT JOIN mod_forum_cats ON mod_forum_cats.id=mod_forum_forums.cat
	WHERE 
		mod_forum_forums.id="'.$_GET['forum'].'"
'));

/**
 * Vérification des droits d'accès
 */
if(!$membres->verifAcces('forum_'.$info['forumId'].'_start')) {
	$message='<p>Vous n\'avez pas l\'autorisation de commencer un nouveau sujet dans ce forum.</p>';
	if(!$membres->infos('id')) {
		$message.='<p>Si vous possdez un compte vous pouvez vous connecter avec le formulaire ci-contre.</p><p>Dans le cas contraire, vous pouvez créer un nouveau compte <a href="membres/inscription.html">en cliquant ici</a>.</p>';
	}
	$site->error($message);
}
if(!$membres->infos('id') && isset($_GET['poll'])) {
	$message='<p>Vous n\'avez pas l\'autorisation de commencer un nouveau sondage dans ce forum.</p>';
	if(!$membres->infos('id')) {
		$message.='<p>Si vous possdez un compte vous pouvez vous connecter avec le formulaire ci-contre.</p><p>Dans le cas contraire, vous pouvez créer un nouveau compte <a href="membres/inscription.html">en cliquant ici</a>.</p>';
	}
	$site->error($message);
}

/**
 * Récupération des informations du sujet si il s'agit d'une édition
 */
if(isset($_GET['edit'])) {
	$topicInfos=$forum->getTopicInfos($_GET['edit']);
	$postInfos=$sql->fetchAssoc($sql->query('SELECT post FROM mod_forum_posts WHERE topic_id="'.$topicInfos['id'].'" && new_topic=1'));
}

if(isset($_GET['edit']) && $topicInfos['starter_id']!=$membres->infos('id') && !$membres->verifAcces('forum_'.$info['forumId'].'_edit')) $site->error('<p>Vous n\'avez pas l\'autorisation d\'éditer ce sujet.</p>');
if($this->action('edit')) {																													// Edition
		$template->setVar('titremessage',$string->clean($topicInfos['titre'],'htmlentities'));
		$template->setVar('descrmessage',$string->clean($topicInfos['descr'],'htmlentities'));
		$template->setVar('bbmessage',$string->clean($postInfos['post'],'htmlentities'));
}


/**
 * Action envoi formulaire
 */
if($this->action('newTopic')) $forum->newTopic($_GET['forum']);
if($this->action('newPoll')) $forum->newPoll($_GET['forum']);
if($this->action('preview')) $forum->preview();
if($this->action('editPoll') && $topicInfos['poll']!=0) $forum->editPoll($_GET['edit']);
if($this->action('editTopic')) $forum->editTopic($_GET['edit']);


/**
 * Construction de la barre du haut et de celle de mise en forme
 */
$forum->makeHaut($template,2,0);
if(!empty($info['forumSpecial'])) $forum->makeSpecial($template,$info['forumSpecial']);
$site->barreMiseEnForme($template);
$site->addToTitle(' - Forum - '.$info['forumTitre']);

/**
 * Affichage du champ pseudo si l'utilisateur n'est pas loggu
 */
if(!$membres->infos('id')) $template->parse('visiteur', true);

$template->setVar(array(
	'catTitre'					=>	$string->clean($info['catTitre'],'htmlentities'),
	'catLinkTitre'				=>	$string->clean($info['catTitre']),
	'catId'						=>	$info['catId'],
	'forumTitre'				=>	$string->clean($info['forumTitre'],'htmlentities'),
	'forumLinkTitre'			=>	$string->clean($info['forumTitre']),
	'forumId'					=>	$info['forumId']
));

/**
 * Listage des smileys
 */
$smileys=unserialize($site->config['smileys']);
foreach($smileys as $i => $var) {
   $template->setVar('smiley',htmlspecialchars(addslashes($i)));
   $template->setVar('alt',htmlentities($i));
   $template->setVar('image',$var);
   $template->parse('forum.smileys', true);
}

/**
 * Si c'est un nouveau sondage ou non
 */
if(isset($_GET['poll'])) $template->parse('poll');

/**
 * Si on dite un sondage ou non
 */
if(isset($topicInfos) && $topicInfos['poll']!=0) {
	$template->parse('poll');
	$info=$sql->fetchAssoc($sql->query('
	SELECT 
		quest	AS	pollQuest,
		choix	AS	pollChoix
	FROM 
		mod_poll
	WHERE 
		id="'.$topicInfos['poll'].'"
	'));
    $template->setVar('questsondage',$string->clean($info['pollQuest'],'htmlentities'));
	$choix=unserialize($info['pollChoix']);
	$choixsondage='';
	foreach($choix as $i=>$var) $choixsondage.=$string->clean($var,'htmlentities').'
';
    $template->setVar('choixsondage',$choixsondage);
}
?>