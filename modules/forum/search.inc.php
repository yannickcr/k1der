<?php
$forum = new forum();

/**
 * Construction de la page
 */
$template->setFile('centre','forum/search.html');  
$this->noUse(array('gauche','droite'));
$site->addCss('templates/'.THEME.'/forum/style.css');

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
	c.nom				AS	catTitre,
	c.id				AS	catId,
	f.titre				AS	forumTitre,
	f.id				AS	forumId,
	f.nb_topics			AS	forumNbTopics,
	t.id				AS	topicId,
	t.titre				AS	topicTitre,
	t.descr				AS	topicDescr,
	t.starter_id		AS	topicStarterId,
	t.starter_name		AS	topicStarterName,
	t.posts				AS	topicNbPosts,
	t.views				AS	topicNbViews,
	t.last_post			AS	topicLastPost,
	t.last_poster_name	AS	topicLastPosterName,
	t.last_poster_id	AS	topicLastPosterId,
	t.pinned 			AS 	topicPinned,
	t.poll 				AS 	topicPoll,
	MATCH (p.post) AGAINST ("'.$_GET['search'].'" IN BOOLEAN MODE) score
FROM 
	mod_forum_topics t
		LEFT JOIN mod_forum_posts p ON p.topic_id=t.id
		LEFT JOIN mod_forum_forums f ON f.id=t.forum_id 
		JOIN mod_forum_cats c ON f.cat=c.id
WHERE 
	MATCH (p.post) AGAINST ("'.$_GET['search'].'" IN BOOLEAN MODE)
GROUP BY 
	t.id 
ORDER BY 
	score DESC
LIMIT 
	'.$start.','.$numtoview.'
');

/*

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
	mod_forum_topics.poll 				AS 	topicPoll,
	MATCH (mod_forum_posts.post) AGAINST ("'.$_GET['search'].'" IN BOOLEAN MODE) score
FROM 
	mod_forum_forums,
	mod_forum_topics,
	mod_forum_cats,
	mod_forum_posts
WHERE 
	MATCH (mod_forum_posts.post) AGAINST ("'.$_GET['search'].'" IN BOOLEAN MODE) &&
	mod_forum_forums.cat=mod_forum_cats.id && 
	mod_forum_forums.id=mod_forum_topics.forum_id 
ORDER BY 
	topicPinned DESC,
	score DESC 
LIMIT 
	'.$start.','.$numtoview.'

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
*/
/**
 * Premire requete
 */
$info=$sql->fetchAssoc($req);
$site->addToTitle(' - Forum - Recherche de '.$_GET['search']);
$nbTopics=$sql->numRows($req);
$nbpages=ceil(($nbTopics)/$numtoview);

/**
 * Construction de la barre du haut et de celle du bas
 */
$forum->makeHaut($template,0,0,'',$nbpages,$numtoview,$start,'f');
$forum->makeBas($template,'',$nbpages,$numtoview,$start,'f');

/**
 * Remplacement des valeurs générales
 */
$template->setVar(array(
		'catTitre'			=>	$info['catTitre'],
		'catLinkTitre'		=>	$string->clean($info['catTitre']),
		'catId'				=>	$info['catId'],
		'forumTitre'		=>	$info['forumTitre'],
		'forumLinkTitre'	=>	$string->clean($info['forumTitre']),
		'forumId'			=>	$info['forumId']
));

if(isset($info['topicTitre'])) {
	/**
	 * Listage des sujets
	 */
	do {
		$template->setVar(array(
			'topicTitre'			=>	$info['topicTitre'],
			'topicLinkTitre'		=>	$string->clean($info['topicTitre']),
			'topicId'				=>	$info['topicId'],
			'topicDescr'			=>	$info['topicDescr'],
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