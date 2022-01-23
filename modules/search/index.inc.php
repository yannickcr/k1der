<?php
$template->setFile('centre','search/index.html');
$site->addCss('templates/'.THEME.'/search/style.css');

$template->setBlock('centre','xml');
$template->setBlock('centre','text');

$params=explode('&',$_SERVER["REQUEST_URI"]);
foreach($params as $param) {
	if(ereg('q=',$param)) $q=substr($param,2);
}

if(isset($q)) {
$site->addToTitle(' - Résultats de la recherche pour &quot;'.$string->clean(urldecode($q),'htmlentities').'&quot;');

	$template->setVar(array(
		'search'	=>	$string->clean(urldecode($q),'htmlentities'),
		'searchQ'	=>	$q
	));
} else $site->addToTitle(' - Rechercher');

if(CONTENTTYPE=='application/xhtml+xml') $template->parse('xml');
else $template->parse('text');

?>