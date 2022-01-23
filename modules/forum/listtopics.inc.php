<?php
$forum = new forum();

/**
 * Construction de la page
 */
$template->setFile('centre','forum/list_topics.html');  
$this->noUse(array('gauche','droite'));
$site->addCss('templates/'.THEME.'/forum/style.css');

/**
 * Vérification des droits d'accès
 */
if(!$membres->verifAcces('forum_'.$_GET["forum"].'_read')) {
		$message='<p>Vous n\'avez pas l\'autorisation de lire les sujets de ce forum.</p>';
		if(!$membres->infos('id')) {
			$message.='<p>Si vous possdez un compte vous pouvez vous connecter avec le formulaire ci-contre.</p><p>Dans le cas contraire, vous pouvez créer un nouveau compte <a href="membres/inscription.html">en cliquant ici</a>.</p>';
		}
	$site->error($message);
}

/**
 * Déclaration des blocks du template
 * Ordre : Intrieur->Exterieur
 */
$template->setBlock('centre','poll');
$template->setBlock('centre','pin');
$template->setBlock('centre','pinpoll');
$template->setBlock('centre','topicpage.un');
$template->setBlock('centre','topicpage.deux');
$template->setBlock('centre','topicpage.trois');
$template->setBlock('centre','topicpage.quatre');
$template->setBlock('centre','new');
$template->setBlock('centre','start1');
$template->setBlock('centre','start2');
$template->setBlock('centre','last3');
$template->setBlock('centre','last4');
$template->setBlock('centre','topic');
$template->setBlock('centre','notopic');

/**
 * Récupération des informations (sujet de dpart et nombre  afficher)
 */
$numtoview=$site->config('forum_nbmess');
if(!isset($_GET['start'])) $start=0;
else $start=$_GET['start'];

/**
 * Informations des sujets et de la catégorie
 */
$req=$sql->query('
SELECT 
	mod_forum_cats.nom					AS	catTitre,
	mod_forum_cats.id					AS	catId,
	mod_forum_forums.titre				AS	forumTitre,
	mod_forum_forums.id					AS	forumId,
	mod_forum_forums.nb_topics			AS	forumNbTopics,
	mod_forum_topics.id					AS	topicId,
	mod_forum_topics.titre				AS	topicTitre,
	mod_forum_topics.descr				AS	topicDescr,
	mod_forum_topics.starter_id			AS	topicStarterId,
	mod_forum_topics.starter_name		AS	topicStarterName,
	mod_forum_topics.posts				AS	topicNbPosts,
	mod_forum_topics.views				AS	topicNbViews,
	mod_forum_topics.last_post			AS	topicLastPost,
	mod_forum_topics.last_poster_name	AS	topicLastPosterName,
	mod_forum_topics.last_poster_id		AS	topicLastPosterId,
	mod_forum_topics.pinned 			AS 	topicPinned,
	mod_forum_topics.poll 				AS 	topicPoll
FROM 
	mod_forum_forums,
	mod_forum_topics,
	mod_forum_cats 
WHERE 
	mod_forum_forums.id="'.$_GET["forum"].'" && 
	mod_forum_forums.cat=mod_forum_cats.id && 
	mod_forum_forums.id=mod_forum_topics.forum_id 
ORDER BY 
	topicPinned DESC,
	topicLastPost DESC 
LIMIT 
	'.$start.','.$numtoview.'
');

if($sql->numRows($req)==0) {
	$req=$sql->query('
		SELECT 
			mod_forum_cats.nom					AS	catTitre,
			mod_forum_cats.id					AS	catId,
			mod_forum_forums.titre				AS	forumTitre,
			mod_forum_forums.id					AS	forumId,
			mod_forum_forums.nb_topics			AS	forumNbTopics
		FROM 
			mod_forum_forums,
			mod_forum_cats 
		WHERE 
			mod_forum_forums.id="'.$_GET["forum"].'" && 
			mod_forum_forums.cat=mod_forum_cats.id
	');
}

/**
 * Premire requete
 */
$info=$sql->fetchAssoc($req);
$site->addToTitle(' - Forum - '.$info['forumTitre']);
$nbTopics=$info['forumNbTopics'];
$nbpages=ceil(($nbTopics)/$numtoview);

/**
 * Construction de la barre du haut et de celle du bas
 */
$forum->makeHaut($template,2,1,'',$nbpages,$numtoview,$start,'f');
$forum->makeBas($template,'',$nbpages,$numtoview,$start,'f');

/**
 * Remplacement des valeurs générales
 */
$template->setVar(array(
		'catTitre'			=>	$string->clean($info['catTitre'],'htmlentities'),
		'catLinkTitre'		=>	$string->clean($info['catTitre']),
		'catId'				=>	$info['catId'],
		'forumTitre'		=>	$string->clean($info['forumTitre'],'htmlentities'),
		'forumLinkTitre'	=>	$string->clean($info['forumTitre']),
		'forumId'			=>	$info['forumId']
));

if(isset($info['topicTitre'])) {
	/**
	 * Listage des sujets
	 */
	do {
		$template->setVar(array(
			'topicTitre'			=>	$string->clean($info['topicTitre'],'htmlentities'),
			'topicLinkTitre'		=>	$string->clean($info['topicTitre']),
			'topicId'				=>	$info['topicId'],
			'topicDescr'			=>	$string->clean($info['topicDescr'],'htmlentities'),
			'topicStarterId'		=>	$info['topicStarterId'],
			'topicStarterName'		=>	$info['topicStarterName'],
			'topicNbPosts'			=>	$info['topicNbPosts'],
			'topicNbViews'			=>	$info['topicNbViews'],
			'topicLastPostDate'		=>	$string->formatDate('%A %d %B %Y',$info['topicLastPost'],true),
			'topicLastPostHeure'	=>	$string->formatDate('%H:%M',$info['topicLastPost']),
			'topicLastPosterName'	=>	$info['topicLastPosterName'],
			'topicLastPosterId'		=>	$info['topicLastPosterId']
		));
		
		/**
		 * Si l'auteur est un visiteur ou pas
		 */
		 if($info['topicStarterId']!=0) {
			$template->parse('start1', true);
			$template->parse('start2', true);
		 }
		
		/**
		 * Vérification du sujet : si pingl, si nouveau message, si sondage
		 */
		if($info['topicPinned']==1 && $info['topicPoll']!=0) $template->parse('pinpoll', true);
		else if($info['topicPinned']==1) $template->parse('pin', true);
		else if($info['topicPoll']!=0) $template->parse('poll', true);
		if($forum->unRead($info['topicId'])) $template->parse('new', true);

		if($info['topicLastPosterId']!=0) {
			$template->parse('last3', true);
			$template->parse('last4', true);
		}
		
		/**
		 * Paginnation du sujet
		 */
		$nbpagestopics=ceil(($info['topicNbPosts']+1)/$numtoview);
		$forum->makeTopicsPages($nbpagestopics,$numtoview);
		
		$template->parse('topic', true);
		$template->unsetVar(array('start1','start2','last3','last4','new','pin','topicpage.un','topicpage.deux','topicpage.trois','topicpage.quatre')); // RAZ de l'pinglement et des nouveaux posts (sinon ils resteront pour les topics suivants)
		
	} while($info=$sql->fetchAssoc($req)); // Fin boucle topics
} else $template->parse('notopic');
?>