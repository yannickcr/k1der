<?php
/**
 * 1. Instanciation d'objets
 */
$news = new news();
$bbcode = new bbcode;

/**
 * 2. Actions
 */
//if($this->action('etape1')) $erreurs=$reservations->etape1($_POST['region']);

/**
 * 3. Récupération des données
 */
//if(isset($_SESSION['reservations_regionId'])) $regionId=$_SESSION['reservations_regionId'];
//else $regionId='';

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','index/index.html');
$site->addToTitle(' - Clan Counter-Strike de Bretagne');
$site->addCss($template->root.'index/style.css');
$site->addCss($template->root.'news/style.css');

/**
 * 5. Déclaration des blocs
 */
 
// Forum
$template->setBlock('centre','forumtopics');

// Matches
$template->setBlock('centre','matches');

// News
$template->setBlock('centre','comments');
$template->setBlock('centre','nocomments');
$template->setBlock('centre','news');

/**
 * 6. Construction de la page
 */

// Forum
$res=$sql->query('
	SELECT 
		mod_forum_forums.id AS forumId,
		mod_forum_forums.titre AS forumTitre,
		mod_forum_topics.id AS topicId,
		mod_forum_topics.titre AS topicTitre,
		mod_forum_topics.last_poster_name AS topicLastPosterName,
		posts AS topicPosts
	FROM 
		mod_forum_topics LEFT JOIN mod_forum_forums ON mod_forum_topics.forum_id=mod_forum_forums.id
	ORDER BY
		mod_forum_topics.last_post DESC
	LIMIT 
		0,20
');

for($i=0;$i<7;$i++) {
	$info=$sql->fetchAssoc($res);
	if($membres->verifAcces('forum_'.$info['forumId'].'_read')) {
		$template->setVar(array(
			'forumTitre'			=>	$info['forumTitre'],
			'topicId'				=>	$info['topicId'],
			'topicTitre'			=>	$string->clean($info['topicTitre'],'htmlentities'),
			'topicCleanTitre'		=>	$string->clean($info['topicTitre']),
			'topicLastPosterName'	=>	$info['topicLastPosterName'],
			'topicPosts'			=>	$info['topicPosts']
		));
		$template->parse('forumtopics', true);
	} else $i--;
}

// Matches
$res=$sql->query('
	SELECT 
		m.id,
		jeu,
		adversaire,
		scores,
		COUNT(c.message) comms 
	FROM 
		mod_matches m 
			LEFT JOIN mod_comments c ON m.id=c.resource_id AND c.module="matches"
	WHERE 
		(c.module="matches" || m.votes=0)
	GROUP BY
		m.id
	ORDER BY 
		m.date DESC, 
		m.id DESC
	LIMIT 0,7
');

while($info=$sql->fetchAssoc($res)) {

	$score1=$score2=0;
	$score=unserialize($info['scores']);
	for($i=0;isset($score[$i]);$i++) {
		for($j=1;isset($score[$i]['rnd'.$j]);$j++) {
			$score1+=$score[$i]['rnd'.$j][0];
			$score2+=$score[$i]['rnd'.$j][1];
		}
	}

	$template->setVar(array(
		'matchAdversaire'		=>	$info['adversaire'],
		'matchCleanClan'		=>	$string->clean($site->config('clan_default')),
		'matchCleanAdversaire'	=>	$string->clean($info['adversaire']),
		'matchId'				=>	$info['id'],
		'matchScore1'			=>	$score1,
		'matchScore2'			=>	$score2,
		'matchJeu'				=>	$info['jeu'],
		'matchComms'			=>	$info['comms'],
		'matchStatus'			=>	($score1>$score2)?'win':(($score1<$score2)?'lose':'draw')
	));
	$template->parse('matches', true);
}


// News
$req=$sql->query('
	SELECT 
		mod_forum_topics.id,
		titre,
		starter_name,
		starter_id,
		last_poster_name,
		last_poster_id,
		start_date,
		posts,
		special,
		post 
	FROM 
		mod_forum_topics LEFT JOIN mod_forum_posts ON mod_forum_posts.topic_id=mod_forum_topics.id
	WHERE 
		mod_forum_topics.forum_id="'.$site->config('news_forum').'" AND
		mod_forum_posts.new_topic="1"
	GROUP BY 
		mod_forum_topics.id 
	ORDER BY 
		pinned DESC, start_date DESC
	LIMIT 0,5
');

while($info=$sql->fetchAssoc($req)) {
	$template->setVar(array(
	   'newsId'			=>	$info['id'],
	   'newsTitre'		=>	$info['titre'],
	   'newsCleanTitre'	=>	$string->clean($info['titre']),
	   'newsText'		=>	$bbcode->BBCodeToHtml($info['post']),
	   'newsDate'		=>	$string->formatDate('%A %d %B %Y &agrave; %H:%M',$info['start_date'],true),
	   'newsAuteur'		=>	$info['starter_name'],
	   'newsType'		=>	$string->special('news',$info['special'])
   ));
   
	if($info['posts']>0) {
		if($info['posts']>1) $template->setVar('s','s');
		$template->setVar(array(
			'newsNbComms'		=>	$info['posts'],
			'newsLastPoster'	=>	$info['last_poster_name']
		));
		$template->parse('comments', true);
	} else {
		$template->parse('nocomments', true);
	}
	$template->parse('news', true);
	$template->unsetVar(array('nocomments','comments'));
}

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
/*
?>
<?php
$forum = new forum();

// Indication du fichier tpl servant de modle 
$template->setFile('centre','index/index.html');  

// Déclaration du bloc boucle NEWS
$template->setBlock('centre','news');
$template->setBlock('centre','datenews');

$template->setBlock('centre','choix');
$template->setBlock('centre','poll');
$template->setBlock('centre','results');
$template->setBlock('centre','showoptions');
$template->setBlock('centre','showmessage');
$template->setBlock('centre','poll2');
$template->setBlock('centre','comments');
$template->setBlock('centre','nocomments');

$template->setBlock('centre','forumtopics');

$site->addCss($template->root.'index/style.css');

// Bloc des news
$req=$sql->query('SELECT id,titre,start_date,special FROM mod_forum_topics WHERE forum_id="'.$site->config('news_forum').'" ORDER BY start_date DESC LIMIT 0,20');

while($info=$sql->fetchAssoc($req)) {
	$date=strtoupper($string->formatDate('%A %d %B %Y',$info['start_date']));
	
	if(isset($oldDate) && $date!=$oldDate) {
		$template->parse('datenews', true);
		$template->unsetVar(array('news'));
	}
	$oldDate=$date;
	
	$template->setVar(array(
		'date'=>$date,
		'dateClean'=>$string->clean($date),
		'id'=>$info['id'],
		'titreNews'=>$info['titre'],
		'typeNews'=>$string->special('news',$info['special']),
		'titreNewsClean'=>$string->clean($info['titre'])
	));
	$template->parse('news', true);
}
$template->parse('datenews', true);
// Fin Bloc des news

// Bloc sondage

$s_info=$sql->fetchAssoc($sql->query('SELECT mod_poll.id AS id,mod_forum_topics.id AS idtopic,titre,quest,choix,results,votes,posts FROM mod_forum_topics,mod_poll WHERE forum_id="'.$site->config('sondage_forum').'" && poll=mod_poll.id ORDER BY start_date DESC LIMIT 0,1'));

$template->setVar(array(
	'poll.question'=>$s_info['quest'],
	'id'=>$s_info['idtopic'],
	'titreSondage'=>$string->clean($s_info['titre'])
));

if($s_info['posts']>0) {
	if($s_info['posts']>1) $template->setVar('esse2','s');
	$template->setVar('poll.comms',$s_info['posts']);
	$template->parse('comments', true);
} else {
	$template->parse('nocomments', true);
}

if(!$forum->avote($s_info['id']) && $membres->infos('id')) {
	$template->parse('poll', true);
	$choix=unserialize($s_info['choix']);
	foreach($choix as $i=>$var) {
		$template->setVar('poll.id',$i);
		$template->setVar('poll.choix',stripslashes($var));
		$template->parse('choix', true);
	}
} else if($forum->avote($s_info['id']) || !$membres->infos('id')) {
	$template->parse('poll2', true);
	$template->setVar('votes',$s_info['votes']);
	if($s_info['votes']>1) $template->setVar('esse','s');
	$choix=unserialize($s_info['choix']);
	$results=unserialize($s_info['results']);
	foreach($choix as $i=>$var) {
		if(!isset($results[$i])) $results[$i]=0;
		if($s_info['votes']==0) $percent=0;
		else $percent=round(($results[$i]/$s_info['votes'])*100,2);
		
		$template->setVar('poll.id',$i);
		$template->setVar('poll.choix',stripslashes($var));
		$template->setVar('poll.result',$results[$i]);
		$template->setVar('poll.width',$percent*2);
		$template->setVar('poll.percent',$percent.'%');
		$template->parse('results', true);
	}
	if(isset($_GET['show']) && $membres->infos('id')) $template->parse('showoptions', true);
	else if(!$membres->infos('id')) $template->parse('showmessage', true);
}
// Fin Bloc sondage

// Bloc Forum

$res=$sql->query('
	SELECT 
		mod_forum_forums.id AS forumId,
		mod_forum_forums.titre AS forumTitre,
		mod_forum_topics.id AS topicId,
		mod_forum_topics.titre AS topicTitre,
		mod_forum_topics.last_poster_name AS topicLastPosterName,
		posts AS topicPosts
	FROM 
		mod_forum_topics LEFT JOIN mod_forum_forums ON mod_forum_topics.forum_id=mod_forum_forums.id
	ORDER BY
		mod_forum_topics.last_post DESC
	LIMIT 
		0,20
');

for($i=0;$i<7;$i++) {
	$info=$sql->fetchAssoc($res);
	if($membres->verifAcces('forum_'.$info['forumId'].'_read')) {
		$template->setVar(array(
			'forumTitre'			=>	$info['forumTitre'],
			'topicId'				=>	$info['topicId'],
			'topicTitre'			=>	$info['topicTitre'],
			'topicCleanTitre'		=>	$string->clean($info['topicTitre']),
			'topicLastPosterName'	=>	$info['topicLastPosterName'],
			'topicPosts'			=>	$info['topicPosts']
		));
		$template->parse('forumtopics', true);
	}
}
*/
?>