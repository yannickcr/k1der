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
if(isset($_GET['param'])) $start=$_GET['param'];
else $start=0;

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','galeries/galerie.html');
$site->addToTitle(' - Galeries');
$site->addCss('templates/'.THEME.'/galeries/style.css');
$site->addJs('modules/galeries/js/galerie.js');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','star');
$template->setBlock('centre','halfstar');
$template->setBlock('centre','emptystar');

$template->setBlock('centre','photo');

$template->setBlock('centre','pages.num');
$template->setBlock('centre','pages.first');
$template->setBlock('centre','pages.prev');
$template->setBlock('centre','pages.mm');
$template->setBlock('centre','pages.m');
$template->setBlock('centre','pages.c');
$template->setBlock('centre','pages.p');
$template->setBlock('centre','pages.pp');
$template->setBlock('centre','pages.next');
$template->setBlock('centre','pages.last');
$template->setBlock('centre','paginnation');

/**
 * 6. Construction de la page
 */


$info=$sql->fetchArray($sql->query('SELECT nom,descr,datedebut,datefin,tags FROM mod_galeries WHERE id='.$_GET['id']));

$template->setVar(array(
	'galerieId'			=>	$_GET['id'],
	'galerieCleanNom'	=>	$string->clean($info['nom']),
	'galerieNom'		=>	$info['nom'],
	'galerieDate'		=>	$galeries->date($info['datedebut'],$info['datefin']),
	'galerieDescr'		=>	$info['descr'],
	'galerieTags'		=>	$galeries->formatTags($info['tags'])
));

$site->addToTitle(' - '.$info['nom']);


// Nombre de photos par page
$nb=20;

$photos=$string->listDir('medias/galeries/'.$string->clean($info['nom']).'/photos/',true);

/**
 * Pagination
 */
$membres->makePages($template,ceil(count($photos)/$nb),($start/$nb)+1,$nb,$start);
if(ceil(count($photos)/$nb)>1) $template->parse('paginnation', true);

$res=$sql->query('
	SELECT
		p.file,
		p.note,
		COUNT(c.message) comms 
	FROM 
		mod_galeries_photos p 
			LEFT JOIN mod_comments c ON p.id=c.resource_id
	WHERE 
		id_galerie='.$_GET['id'].' AND 
		(c.module="galeries" || p.votes=0)
	GROUP BY
		p.id
');

$photoInfos=array();
while($info2=$sql->fetchAssoc($res)) {
	$photoInfos[$info2['file']]=array('note'=>$info2['note'],'comms'=>$info2['comms']);
}

for($i=$start;isset($photos[$i]) && $i<($nb+$start);$i++) {

	if(!isset($photoInfos[$photos[$i]]['note'])) $photoInfos[$photos[$i]]=array('note'=>0,'comms'=>0);
	
	$template->setVar(array(
		'photoNom'		=>	$photos[$i],
		'photoComms'	=>	$photoInfos[$photos[$i]]['comms'],
		's'				=>	($photoInfos[$photos[$i]]['comms']>1)?'s':'',
		'photoPath'		=>	$utils->miniature('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$photos[$i])
	));
	
	// Note du fichier
	$star=5;
	
	$reste=$photoInfos[$photos[$i]]['note']-floor($photoInfos[$photos[$i]]['note']);
	if($reste!=0 && $reste>=0.3 && $reste<0.7) {
		$template->parse('halfstar');
		$star--;
	}
	if($reste==0.5) $photoInfos[$photos[$i]]['note']--;
	for($j=0;$j<round($photoInfos[$photos[$i]]['note']) && $star>0;$j++) {
		$template->parse('star',true);
		$star--;
	}
	for($star;$star>0;$star--) $template->parse('emptystar',true);
	
	$template->parse('photo',true);
	$template->unsetVar(array('star','halfstar','emptystar'));
}

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>