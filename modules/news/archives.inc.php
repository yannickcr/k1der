<?php
$template->setFile('centre','news/archives.html');  
$site->addCss($template->root.'news/style.css');

$template->setBlock('centre','newline');
$template->setBlock('centre','categories');
$template->setBlock('centre','annees');


// Par catégorie

$cats=unserialize($site->config('news_cat'));

$res=$sql->query('SELECT special,count(*) AS nb FROM mod_forum_topics WHERE forum_id="'.$site->config('news_forum').'" GROUP BY special');

$news=array();
while($info=$sql->fetchAssoc($res)) {
	$tmp=unserialize($info['special']);
	$news[$tmp['news']]=$info['nb'];
}

$j=1;
foreach($cats as $i=>$var) {
	$template->setVar(array(
		'catIcon'	=>	$i,
		'catNom'	=>	$var,
		'catNb'	=>	(isset($news[$i]))?$news[$i]:'0'
	));
	if($j%5==0 && $j<count($cats)) $template->parse('newline');
	$template->parse('categories', true);
	$template->unsetVar('newline');
	$j++;
}

// Par date

$info=$sql->fetchAssoc($res=$sql->query('SELECT min(start_date) AS min,max(start_date) AS max FROM mod_forum_topics WHERE forum_id="'.$site->config('news_forum').'"'));
$min=date('Y',$info['min']);
$max=date('Y',$info['max']);
for($i=$min;$i<=$max;$i++) {
	$template->setVar('archAnnee',$i);
	$template->parse('annees', true);
}
?>