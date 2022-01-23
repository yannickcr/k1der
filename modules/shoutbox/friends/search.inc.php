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

$searchInfos['shoutbox']=array(
	'query'=>'
		SELECT 
			id,
			MATCH (message) AGAINST ("'.$this->search.'" IN BOOLEAN MODE) score
		FROM 
			mod_shoutbox
		WHERE 
			MATCH (message) AGAINST ("'.$this->search.'" IN BOOLEAN MODE)
		ORDER BY 
			score DESC
	',
	'link'=>'shoutbox/search-{search}.html',
	'prefix'=>'dans la'
);
?>