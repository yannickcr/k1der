<?php
/**
 * 1. Instanciation d'objets
 */
for($i=1;$this->config('serveur_'.$i.'_ip');$i++) {
	$serveur[$i] = new serveur($this->config('serveur_'.$i.'_ip'),$this->config('serveur_'.$i.'_port'));
}

/**
 * 2. Actions
 */
//if($this->action('etape1')) $erreurs=$reservations->etape1($_POST['region']);

/**
 * 3. Récupération des données
 */
//if(isset($_SESSION['reservations_regionId'])) $regionId=$_SESSION['reservations_regionId'];
//else $regionId='';

/**
 * 4. Initialisation de la page
 */
$sub_template = new template("templates/".THEME."/");
$sub_template->setFile('module','serveur/module.html');
$this->addCss('templates/'.THEME.'/serveur/style_mod.css');
$this->addJs('modules/serveur/js/scripts.js');

/**
 * 5. Déclaration des blocs
 */
$sub_template->setBlock('module','serveur');

/**
 * 6. Construction de la page
 */

foreach($serveur as $i=>$var) {

	/**
	 * Vérification du fichier en cache
	 */
	
	$infos	=	$serveur[$i]->getInfosHL();
	$players=	$serveur[$i]->getPlayers();
	 
	$mapImg='modules/serveur/jeux/'.$infos['mod'].'/maps/'.$infos['map'].'.jpg';
	 
	$sub_template->setVar(array(
		'i'					=>	$i,
		'serveurIp'			=>	$serveur[$i]->ip,
		'serveurPort'		=>	$serveur[$i]->port,
		'serveurStats'		=>	$this->config('serveur_'.$i.'_stats'),
		'serveurMap'		=>	$infos['map'],
		'serveurMapImg'		=>	file_exists($mapImg)?$mapImg:'modules/serveur/jeux/nomap.jpg',
		'serveurNom'		=>	$infos['name'],
		'serveurModname'	=>	$infos['modname'],
		'serveurMap'		=>	$infos['map'],
		'serveurPlayers'	=>	$infos['players'],
		'serveurPlaces'		=>	$infos['places'],
		'serveurPass'		=>	($infos['pass'])?'Oui':'Non'
	));
	
	
	$sub_template->parse('serveur',true);
	unset($infos);
}

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'module',$erreurs);
?>