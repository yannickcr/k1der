<?php
$news = new news();

$bbcode = new bbcode;

 // Indication du fichier tpl servant de modle 
$template->setFile('centre','news/jour.html');  

// Déclaration dest bloc boucle
$template->setBlock('centre','comments');
$template->setBlock('centre','nocomments');
$template->setBlock('centre','news');
$site->addCss($template->root.'news/style.css');

$date=$string->dateToReq($_GET['date']);
$site->addToTitle(' - News - '.ucwords($string->formatDate('%A %d %B %Y',$date[0])));

// Traitement des news

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
		mod_forum_topics.forum_id="'.$site->config('news_forum').'" &&
		mod_forum_topics.start_date>"'.$date[0].'" && 
		mod_forum_topics.start_date<"'.$date[1].'"
	GROUP BY 
		mod_forum_topics.id 
	ORDER BY 
		start_date DESC
	LIMIT 0,20
');
while($info=$sql->fetchAssoc($req)) {

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
	$template->parse('news', true);
	$template->unsetVar(array('nocomments','comments'));
}
?>