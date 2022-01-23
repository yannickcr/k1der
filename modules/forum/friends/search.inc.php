<?php

/*function searchList() {
	global $sql;
	$res=$sql->query('SELECT id,nom FROM mod_forum_cats');
	$cats=array();
	while($info=$sql->fetchAssoc($res)) {
		$cats[$info['id']]=$info['nom'];
	}
	
	$res=$sql->query('SELECT id,cat,titre FROM mod_forum_forums');
	$forums=array();
	while($info=$sql->fetchAssoc($res)) {
		$forums[$info['id']]=array($info['cat'],$info['titre']);
	}
	
	$where=array();
	foreach($cats as $i=>$val) {
		$where['c_'.$i]=$val;
		foreach($forums as $j=>$valf) {
			if($valf[0]==$i) $where['f_'.$j]=$valf[1];
		}
	}
	return $where;
}*/

$searchInfos['forum']=array(
	'query'=>'
		SELECT 
			t.id,
			MATCH (p.post) AGAINST ("'.$this->search.'" IN BOOLEAN MODE) score
		FROM 
			mod_forum_topics t
				LEFT JOIN mod_forum_posts p ON p.topic_id=t.id
		WHERE 
			MATCH (p.post) AGAINST ("'.$this->search.'" IN BOOLEAN MODE)
		GROUP BY 
			t.id 
		ORDER BY 
			score DESC
	',
	'link'=>'forum/search-{search}.html',
	'prefix'=>'sur le'
);

/*$query['forum']='SELECT 
		mod_forum_topics.id topicId,
		titre topicTitle,
		starter_name topicStarterName,
		posts topicNbPosts,
		views topicNbViews
	FROM 
		mod_forum_topics 
			LEFT JOIN mod_forum_posts ON mod_forum_posts.topic_id=mod_forum_topics.id
	WHERE 
		mod_forum_posts.post LIKE "%'.$search.'%"
	GROUP BY 
		mod_forum_topics.id 
	ORDER BY 
		last_post DESC
	LIMIT 0,10';
*/

?>