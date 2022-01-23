<?php
$news = new news();

$bbcode = new bbcode;

 // Indication du fichier tpl servant de modle 
$template->setFile('centre','news/news.html');  

// Déclaration dest bloc boucle
$template->setBlock('centre','comments');
$template->setBlock('centre','nocomments');
$site->addCss($template->root.'news/style.css');

// Traitement des news

$info=$sql->fetchAssoc($sql->query('
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
		mod_forum_topics.forum_id="'.$site->config('news_forum').'" && 
		mod_forum_topics.id="'.$_GET['id'].'"
	GROUP BY 
		mod_forum_topics.id 
'));


$site->addToTitle(' - News - '.$info['titre']);

$template->setVar(array(
   'newsId'			=>	$info['id'],
   'newsTitre'		=>	$info['titre'],
   'newsCleanTitre'	=>	$string->clean($info['titre']),
   'newsText'		=>	$bbcode->BBCodeToHtml($info['post']),
   'newsDate'		=>	$string->formatDate('%A %d %B %Y  %H:%M',$info['start_date'],true),
   'newsAuteur'		=>	$info['starter_name'],
   'newsType'		=>	$string->special('news',$info['special'])
));

if($info['posts']>0) {
	if($info['posts']>1) $template->setVar('S','s');
	$template->setVar(array(
		'newsNbComms'		=>	$info['posts'],
		'newsLastPoster'	=>	$info['last_poster_name']
	));
	$template->parse('comments', true);
} else {
	$template->parse('nocomments', true);
}
?>