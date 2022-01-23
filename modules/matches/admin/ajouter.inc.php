<?php
$matches = new matches();

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('ajouterMatch')) $matches->ajouterMatch($_POST);

$sub_template->setFile('centredroite','matches/admin/ajouter.html');
$site->addToTitle(' - Matches - Ajouter');
$site->addJs('modules/matches/admin/js/addmatch.js');
$site->addJs('modules/matches/admin/js/livesearchlan.js');
$site->addJs('modules/matches/admin/js/livesearchplayer.js');



$sub_template->setBlock('centredroite','jeux');

$jeux=$matches->listGames();

foreach($jeux as $i=>$var) {
	$sub_template->setVar(array(
		'matchJeuxShortName'	=>	$i,
		'matchJeux'				=>	$var['Infos']['name']
	));
	$sub_template->parse('jeux',true);
}

# Debut Bloc Jours
$sub_template->setBlock('centredroite','jours');

for($i=1;$i<32;$i++) {
	$sub_template->setVar('jour',$i);
	if(date('j')==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('jours', true);
}
# Fin Bloc Jours

# Debut Bloc Mois
$sub_template->setBlock('centredroite','moiss');

$mois=array('1'=>'Janvier','2'=>'Fvrier','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Aot','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Dcembre');

foreach($mois as $i => $var) {
	$sub_template->setVar('mois',$i);
	$sub_template->setVar('nommois',$var);
	if(date('n')==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('moiss', true);
}
# Fin Bloc Mois

# Debut Bloc Annes
$sub_template->setBlock('centredroite','annees');

for($i=(date('Y')-5);$i<=(date('Y')+1);$i++) {
	$sub_template->setVar('annee',$i);
	if(date('Y')==$i) $sub_template->setVar('selected2',' selected="selected"');
	else $sub_template->unsetVar('selected2');
	$sub_template->parse('annees', true);
}
# Fin Bloc Annes

?>