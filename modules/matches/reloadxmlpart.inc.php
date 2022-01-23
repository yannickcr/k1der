<?php
$template->setFile('centre','matches/reloadxmlpart.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));

$template->setBlock('centre','nbmapsopt');
$template->setBlock('centre','nbmaps');

$template->setBlock('centre','lineup1');
$template->setBlock('centre','lineup2');


$template->setBlock('centre','teamorduel');
$template->setBlock('centre','teamrnd2');
$template->setBlock('centre','dm');
$template->setBlock('centre','scores');




$matches = new matches();

$jeux=$matches->listGames();
$jeu=$jeux[$_GET['loadxml']]['gametype'][$_GET['data']];

// MAJ de la liste du nombre de maps possibles
if($_GET['reloadxmlpart']=='maps') {
	$min=$jeu['minmaps'];
	$max=$jeu['maxmaps'];
	if(isset($jeu['DefNbMaps'])) $def=$jeu['DefNbMaps'];
	
	for($i=$min;$i<=$max;$i++) {
		if((isset($def) && $i==$def) || (!isset($def) && $i==$min)) $template->setVar('selected',' selected="selected"');
		$template->setVar('nbMap',$i);
		$template->parse('nbmapsopt',true);
		$template->unsetVar(array('selected'));
	}
	$template->parse('nbmaps',true);
}

// MAJ des lines up
if($_GET['reloadxmlpart']=='lineup1') {
	if($jeu['type']=='Deathmatch') $jeu['nbplayer']=1;
	for($i=1;$i<=$jeu['nbplayer'];$i++) {
		$template->setVar('i',$i);
		$template->parse('lineup1',true);
	}
}

if($_GET['reloadxmlpart']=='lineup2') {
	for($i=1;$i<=$jeu['nbplayer'];$i++) {
		$template->setVar('i',$i);
		$template->parse('lineup2',true);
	}
}


// MAJ des scores
if($_GET['reloadxmlpart']=='scores') {
	if($jeu['type']=='Team' && isset($jeu['round']) && $jeu['round']=='2') {
		$template->setVar(array(
			'team1'	=>	$jeu['team1'],
			'team2'	=>	$jeu['team2']
		));
		$template->parse('teamrnd2',true);
	} else if($jeu['type']=='Duel' || $jeu['type']=='Team') $template->parse('teamorduel',true);
	$template->parse('scores',true);
}

?>