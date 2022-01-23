<?php
$rss = new rss();
$bbcode = new bbcode;

header('Content-Type: application/rss+xml; charset=ISO-8859-1');
/**
 * Vérification du fichier en cache
 */
$filename='modules/rss/cache/'.$_GET['action'].'-'.$_GET['param'].'.rss';

if(file_exists($filename)) {
	$time=time()-filemtime($filename);
	if($time<600) {
		echo file_get_contents($filename); 
		exit();
	}
}

/**
 * Regénération du fichier
 */
$flux=$rss->getInfos($_GET['action'],$_GET['param']);

$res=$sql->query($flux[2]);

$template->setFile('centre','rss/flux.rss');  

$template->setBlock('centre','itemlist');
$template->setBlock('centre','items');

$template->setVar(array(
	'link'	=>	dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/',
	'title'	=>	utf8_decode(TITLE.' - '.$flux[0]),
	'date'	=>	$string->getIso8601(time())
));

$items=array();
for($i=0;$info=$sql->fetchAssoc($res);$i++) {

	$items[$i]=$flux[1]($info);
	
	$info['description']=str_replace(array('&','<br />'),array('&amp;',' '),substr($string->unhtmlentities(strip_tags($bbcode->BBCodeToHtml($info['description']))),0,255)).'...';
	$info['content']=strip_tags($string->unhtmlentities($bbcode->BBCodeToHtml($info['content'])),'<img><p><table><th><td><tr><div><span><br><a><ul><ol><li><blockquote><code><pre>');
	
	$template->setVar(array(
		'itemLink'			=>	$items[$i],
		'itemTitle'			=>	utf8_decode(str_replace('&',' et ',$string->unhtmlentities($info['title']))),
		'itemDate'			=>	$string->getIso8601($info['date']),
		'itemCreator'		=>	str_replace('&',' et ',$string->unhtmlentities($info['creator'])),
		'itemDescription'	=>	utf8_decode($info['description']),
		'itemContent'		=>	utf8_decode($info['content'])
	
	));
	$template->parse('items',true);
}

foreach($items as $val) {
	$template->setVar('itemResource',$val);
	$template->parse('itemlist',true);
}

$template->globalParse('parse',array('centre'),true);
ob_start();
$template->p('parse');
$file=ob_get_contents();
ob_end_clean();

$fp=fopen($filename,'w');
if (flock($fp, LOCK_EX)) { // pose un verrou exclusif
    fwrite($fp, $file);
    flock($fp, LOCK_UN); // libre le verrou
	echo $file;
} else echo file_get_contents($filename); 
exit();
?>