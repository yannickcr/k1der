<?php
/**
 * 1. Instanciation d'objets
 */
$galeries = new galeries();
$utils = new utils();

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
$template->setFile('centre','galeries/index.html');
$site->addToTitle(' - Galeries');
$site->addCss('templates/'.THEME.'/galeries/style.css');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','illus');
$template->setBlock('centre','galerie');

/**
 * 6. Construction de la page
 */
$template->setVar('activeReservations',' class="active"'); 

$res=$sql->query('SELECT id,nom,descr,datedebut,datefin,tags FROM mod_galeries ORDER BY datefin DESC');

while($info=$sql->fetchArray($res)) {
	$cleanNom=$string->clean($info['nom']);
	$nbPhotos=$galeries->countPhotos($cleanNom);
	$rand=rand(1,$nbPhotos);
	if(strlen($rand)==1) $rand='00'.$rand;
	if(strlen($rand)==2) $rand='0'.$rand;
	$rand='img'.$rand.'.jpg';
	
	$template->setVar(array(
		'galerieId'			=>	$info['id'],
		'galerieCleanNom'	=>	$cleanNom,
		'galerieNom'		=>	$info['nom'],
		'galerieDescr'		=>	$info['descr'],
		'galerieDate'		=>	$galeries->date($info['datedebut'],$info['datefin']),
		'galeriePhotos'		=>	$nbPhotos,
		'galerieVideos'		=>	$galeries->countVideos($cleanNom),
		'galerieDivers'		=>	$galeries->countDivers($cleanNom),
		'galerieTags'		=>	$galeries->formatTags($info['tags']),
		'galeriePhoto'		=>	$utils->miniature('medias/galeries/'.$cleanNom.'/photos/'.$rand,160,120),
		'galeriePhotoNom'	=>	$rand
	));
	if($nbPhotos>0) $template->parse('illus');

	$template->parse('galerie',true);
	$template->unsetVar('illus');
}

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>