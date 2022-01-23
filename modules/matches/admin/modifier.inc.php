<?php
$matches = new matches();

/**
 * ACTION ENVOI FORMULAIRE
 */
if($this->action('modifierMatch')) $matches->modifierMatch($_POST);

$sub_template->setFile('centredroite','matches/admin/modifier.html');
$site->addToTitle(' - Matches - Modifier');
$site->addJs('modules/matches/admin/js/editmatch.js');
$site->addJs('modules/matches/admin/js/livesearchlan.js');
$site->addJs('modules/matches/admin/js/livesearchplayer.js');



$sub_template->setBlock('centredroite','jeux');

$jeux=$matches->listGames();


$info=$sql->fetchAssoc($sql->query('
	SELECT
		mod_matches.*,
		mod_tournois.date_fin
	FROM
		mod_matches LEFT JOIN mod_tournois ON mod_matches.lieu_id=mod_tournois.id
	WHERE
		mod_matches.id="'.$_GET['id'].'"
'));

foreach($jeux as $i=>$var) {
	$sub_template->setVar(array(
		'matchJeuxShortName'	=>	$i,
		'matchJeux'				=>	$var['Infos']['name']
	));
	if($info['jeu']==$i) $sub_template->setVar('jeuselected',' selected="selected"');
	else $sub_template->unsetVar('jeuselected');
	$sub_template->parse('jeux',true);
}

$sub_template->setVar(array(
	'matchId'					=>	$_GET['id'],
	$info['type'].'selected'	=>	' selected="selected"',
	$info['lieu'].'selected'	=>	' selected="selected"',
	$info['lieu'].'value'		=>	$info['lieu_nom']
));

# Debut Bloc Jours
$sub_template->setBlock('centredroite','jours');

for($i=1;$i<32;$i++) {
	$sub_template->setVar('jour',$i);
	if(date('j',$info['date'])==$i) $sub_template->setVar('selected',' selected="selected"');
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
	if(date('n',$info['date'])==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('moiss', true);
}
# Fin Bloc Mois

# Debut Bloc Annes
$sub_template->setBlock('centredroite','annees');

for($i=(date('Y')-5);$i<=(date('Y')+1);$i++) {
	$sub_template->setVar('annee',$i);
	if(date('Y',$info['date'])==$i) $sub_template->setVar('selected2',' selected="selected"');
	else $sub_template->unsetVar('selected2');
	$sub_template->parse('annees', true);
}
# Fin Bloc Annes


# Debut Bloc Jours Fin
$sub_template->setBlock('centredroite','joursfin');

for($i=1;$i<32;$i++) {
	$sub_template->setVar('jourfin',$i);
	if(date('j',$info['date_fin'])==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('joursfin', true);
}
# Fin Bloc Jours Fin

# Debut Bloc Mois Fin
$sub_template->setBlock('centredroite','moissfin');

$mois=array('1'=>'Janvier','2'=>'Fvrier','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Aot','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Dcembre');

foreach($mois as $i => $var) {
	$sub_template->setVar('moisfin',$i);
	$sub_template->setVar('nommoisfin',$var);
	if(date('n',$info['date_fin'])==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('moissfin', true);
}
# Fin Bloc Mois Fin

# Debut Bloc Annes Fin
$sub_template->setBlock('centredroite','anneesfin');

for($i=(date('Y')-5);$i<=(date('Y')+1);$i++) {
	$sub_template->setVar('anneefin',$i);
	if(date('Y',$info['date_fin'])==$i) $sub_template->setVar('selected2',' selected="selected"');
	else $sub_template->unsetVar('selected2');
	$sub_template->parse('anneesfin', true);
}
# Fin Bloc Annes Fin

?>