<?php
/**
 * 1. Instanciation d'objets
 */
$galeries = new galeries();
$utils = new utils();
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
$template->setFile('centre','wap/readnews.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));
header('Expires: Thu, 01 Dec 2094 16:00:00 GMT');
header('Content-Type: text/vnd.wap.wml; charset='.CHARSET);
$site->addToTitle(' - Wap');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','prev');
$template->setBlock('centre','next');


/**
 * 6. Construction de la page
 */
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
		mod_forum_topics.id="'.$_GET['param'].'" AND
		mod_forum_posts.new_topic="1"
	GROUP BY 
		mod_forum_topics.id
');

$info=$sql->fetchAssoc($req);

$template->setVar(array(
   'newsId'			=>	$info['id'],
   'newsTitre'		=>	str_replace('&','&amp;',$string->unhtmlentities($info['titre'])),
   'newsCleanTitre'	=>	$string->clean($info['titre']),
   'newsDate'		=>	$string->formatDate('le %d/%m &agrave; %H:%M',$info['start_date'],false),
   'newsText'		=>	str_replace(array('&','< '),array('&amp;',' '),strip_tags($string->unhtmlentities($bbcode->BBCodeToiHtml($info['post'])),'<a><br><en><strong><b><u><i>'))
));

$template->parse('news', true);

$req2=$sql->query('
	SELECT 
		id
	FROM 
		mod_forum_topics
	WHERE 
		start_date<'.$info['start_date'].' AND
		forum_id="'.$site->config('news_forum').'"
	ORDER BY
		start_date DESC
	LIMIT 
		0,1
');

$info2=$sql->fetchAssoc($req2);
$template->setVar('newsPrevId',$info2['id']);
if($sql->numRows($req2)>0) $template->parse('prev');

$req=$sql->query('
	SELECT 
		id
	FROM 
		mod_forum_topics
	WHERE 
		start_date>'.$info['start_date'].' AND
		forum_id="'.$site->config('news_forum').'"
	ORDER BY
		start_date ASC
	LIMIT 
		0,1
');

$info=$sql->fetchAssoc($req);
$template->setVar('newsNextId',$info['id']);
if($sql->numRows($req)>0) $template->parse('next');

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>