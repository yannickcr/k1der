<?php
/**
 * 1. Instanciation d'objets
 */
$galeries = new galeries();

/**
 * 2. Actions
 */
if($this->action('addGallery')) $erreurs=$galeries->addGallery($_POST['nom'],$_POST['descr'],$_POST['jour'],$_POST['mois'],$_POST['annee'],$_POST['jour2'],$_POST['mois2'],$_POST['annee2']);

/**
 * 3. Récupération des données
 */
if(isset($_POST['nom'])) {
	$nom=$site->clear4Sql($_POST['nom']);
	$descr=$site->clear4Sql($_POST['descr']);
	
	$jour=$site->clear4Sql($_POST['jour']);
	$mois=$site->clear4Sql($_POST['mois']);
	$annee=$site->clear4Sql($_POST['annee']);
	
	$jour2=$site->clear4Sql($_POST['jour2']);
	$mois2=$site->clear4Sql($_POST['mois2']);
	$annee2=$site->clear4Sql($_POST['annee2']);
} else $nom=$descr=$jour=$jour2=$mois=$mois2=$annee=$annee2='';

/**
 * 4. Initialisation de la page
 */
$sub_template->setFile('centredroite','galeries/admin/addgallery.html');
$site->addToTitle(' - Galeries - Ajouter une galerie');

/**
 * 5. Déclaration des blocs
 */
$sub_template->setBlock('centredroite','jours');
$sub_template->setBlock('centredroite','moiss');
$sub_template->setBlock('centredroite','annees');

$sub_template->setBlock('centredroite','jours2');
$sub_template->setBlock('centredroite','moiss2');
$sub_template->setBlock('centredroite','annees2');

/**
 * 6. Construction de la page
 */
 
$sub_template->setVar(array(
	'nom'	=>	$nom,
	'descr'	=>	$descr
));
 
# Debut Bloc Jours
for($i=1;$i<32;$i++) {
	$sub_template->setVar('jour',$i);
	if($jour==$i) $sub_template->setVar('selected',' selected="selected"');
	else if($jour2==$i) $sub_template->setVar('selected2',' selected="selected"');
	$sub_template->parse('jours', true);
	$sub_template->parse('jours2', true);
	$sub_template->unsetVar(array('selected','selected2'));
}
# Fin Bloc Jours

# Debut Bloc Mois
$AllMois=array('1'=>'Janvier','2'=>'Fvrier','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Aot','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Dcembre');

foreach($AllMois as $i => $var) {
	$sub_template->setVar('mois',$i);
	$sub_template->setVar('nommois',$var);
	if($mois==$i) $sub_template->setVar('selected',' selected="selected"');
	else if($mois2==$i) $sub_template->setVar('selected2',' selected="selected"');
	$sub_template->parse('moiss', true);
	$sub_template->parse('moiss2', true);
	$sub_template->unsetVar(array('selected','selected2'));
}
# Fin Bloc Mois

# Debut Bloc Annes
for($i=(date('Y')-10);$i<=date('Y');$i++) {
	$sub_template->setVar('annee',$i);
	if($annee==$i) $sub_template->setVar('selected',' selected="selected"');
	else if($annee2==$i) $sub_template->setVar('selected2',' selected="selected"');
	$sub_template->parse('annees', true);
	$sub_template->parse('annees2', true);
	$sub_template->unsetVar(array('selected','selected2'));
}
# Fin Bloc Annes

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($sub_template,'centredroite',$erreurs);
?>