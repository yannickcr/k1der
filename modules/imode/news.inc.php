<?php
/**
 * 1. Instanciation d'objets
 */
$galeries = new galeries();
$utils = new utils();

/**
 * 2. Actions
 */

/**
 * 3. Récupération des données
 */
if(isset($_GET['action2'])) $act = (int)$_GET['action2'];
else $act=0;

if($act==0) {
	$prev=2;
	$next=false;
	$start=0;
} else {
	$next=$act-1;
	$prev=$act+1;
	$start=($act-1)*10;
}

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','imode/news.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));
header('Content-Type: text/html; charset='.CHARSET);
$site->addToTitle(' - iMode');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','news');
$template->setBlock('centre','prev');
$template->setBlock('centre','next');

/**
 * 6. Construction de la page
 */
$req=$sql->query('
	SELECT 
		id,
		titre,
		start_date
	FROM 
		mod_forum_topics
	WHERE 
		forum_id="'.$site->config('news_forum').'"
	ORDER BY 
		start_date DESC
	LIMIT '.$start.',10
');
while($info=$sql->fetchAssoc($req)) {

	$template->setVar(array(
	   'newsId'			=>	$info['id'],
	   'newsTitre'		=>	$info['titre'],
	   'newsCleanTitre'	=>	$string->clean($info['titre']),
	   'newsDate'		=>	$string->formatDate('le %d/%m &agrave; %H:%M',$info['start_date'],false)
   ));
   
	$template->parse('news', true);
}

$template->setVar(array(
	'newsPrev'	=>	$prev,
	'newsNext'	=>	$next
));

if($prev) $template->parse('prev');
if($next) $template->parse('next');


/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>