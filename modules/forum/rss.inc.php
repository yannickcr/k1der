<?php
$rss=array(
	'derniers-messages'	=>	array('Derniers messages du forum','forumDerniersMessages','		
		SELECT 
			id id,
			titre title,
			last_post date,
			last_poster_name creator,
			"" description,
			"" content
		FROM 
			mod_forum_topics
		ORDER BY
			last_post DESC
		LIMIT 
			0,10
'));

function forumDerniersMessages($info) {
	global $string;
	$cleanTitle=$string->clean($info['title']);
	return dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/forum/'.$cleanTitle.'-t'.$info['id'].'.html';
}
?>