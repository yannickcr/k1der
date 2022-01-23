<?php
$rss=array(
	'actualites'	=>	array('Actualités','newsActualites','
		SELECT 
			mod_forum_topics.id id,
			titre title,
			starter_name creator,
			start_date date,
			post description,
			post content
		FROM 
			mod_forum_topics LEFT JOIN mod_forum_posts ON mod_forum_posts.topic_id=mod_forum_topics.id
		WHERE 
			mod_forum_topics.forum_id="'.$site->config('news_forum').'" AND
			mod_forum_posts.new_topic="1"
		GROUP BY 
			mod_forum_topics.id 
		ORDER BY 
			start_date DESC
		LIMIT 0,10')
);

function newsActualites($info) {
	global $string;
	$cleanTitle=$string->clean($info['title']);
	return dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/forum/'.$cleanTitle.'-t'.$info['id'].'.html';
}
?>