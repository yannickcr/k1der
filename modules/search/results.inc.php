<?php
/**
 * 1. Instanciation d'objets
 */
require_once ('modules/search/class.search.php');
$search = new Search($_GET['action'],array('forum','shoutbox'));
/**
 * 2. Actions
 */

/**
 * 3. Récupération des données
 */

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','search/results.html');
$site->addCss('templates/'.THEME.'/search/style.css');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','modules');

/**
 * 6. Construction de la page
 */
$template->setVar(array(
	'search'=>$search->search
)); 

foreach($search->modules as $module) {
	$template->setVar(array(
		'module'	=>	ucfirst($module),
		'link'		=>	str_replace('{search}',urlencode($search->search),$search->searchInfos[$module]['link']),
		'prefix'	=>	$search->searchInfos[$module]['prefix'],
		'i'			=>	$search->results[$module],
		's'			=>	($search->results[$module]>1)?'s':''
	));
	$template->parse('modules',true);
}

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'module',$erreurs);
?>