<?php
/**
 * 1. Instanciation d'objets
 */

/**
 * 2. Actions
 */
//if($this->action('etape1')) $erreurs=$reservations->etape1($_POST['region']);

/**
 * 3. Récupération des données
 */
$id=isset($_POST['id'])?(int)$_POST['id']:1;
if($site->config('serveur_'.$id.'_ip')==false) exit();

$serveur = new serveur($site->config('serveur_'.$id.'_ip'),$site->config('serveur_'.$id.'_port'));

/**
 * 4. Initialisation de la page
 */
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);
$template->setFile('centre','serveur/maj.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));

/**
 * 5. Déclaration des blocs
 */

/**
 * 6. Construction de la page
 */

$infos=$serveur->getInfosHL(false);
 
$mapImg='modules/serveur/jeux/'.$infos['mod'].'/maps/'.$infos['map'].'.jpg';
 
$template->setVar(array(
	'i'					=>	$id,
	'serveurIp'			=>	$serveur->ip,
	'serveurPort'		=>	$serveur->port,
	'serveurStats'		=>	$site->config('serveur_'.$id.'_stats'),
	'serveurMap'		=>	$infos['map'],
	'serveurMapImg'		=>	file_exists($mapImg)?$mapImg:'modules/serveur/jeux/nomap.jpg',
	'serveurNom'		=>	$infos['name'],
	'serveurModname'	=>	$infos['modname'],
	'serveurMap'		=>	$infos['map'],
	'serveurPlayers'	=>	$infos['players'],
	'serveurPlaces'		=>	$infos['places'],
	'serveurPass'		=>	($infos['pass'])?'Oui':'Non'
));

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>