<?php
$template->setFile('centre','matches/loadxml.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));

$matches = new matches();

$template->setBlock('centre','mode');
$template->setBlock('centre','maps');
$template->setBlock('centre','nbmaps');
$template->setBlock('centre','teamorduel');
$template->setBlock('centre','teamrnd2');
$template->setBlock('centre','dm');
$template->setBlock('centre','carte');
$template->setBlock('centre','lineup1');
$template->setBlock('centre','lineup2');

$jeux=$matches->listGames();

if(count($_POST)>0) {

$info=$sql->fetchAssoc($sql->query('
	SELECT
		mod_matches.*,
		mod_tournois.date_fin
	FROM
		mod_matches LEFT JOIN mod_tournois ON mod_matches.lieu_id=mod_tournois.id
	WHERE
		mod_matches.id="'.$_POST['id'].'"
'));

	// Liste des modes de jeu
	$gametype=$info['mode'];
	foreach($jeux[$_GET['loadxml']]['gametype'] as $i=>$var) {
		$template->setVar(array(
			'matchJeuxMode'	=>	$var['nom']
		));
		if($info['mode']==$var['nom']) $template->setVar('modeselected',' selected="selected"');
		else $template->unsetVar('modeselected');
		$template->parse('mode',true);
	}
	
	$template->setVar(array(
		'matchAdv'							=>	str_replace('','&euro;',$info['adversaire']),
		'level'.$info['niveau'].'selected'	=>	' selected="selected"'
	));
	
	$jeu=$jeux[$_GET['loadxml']]['gametype'][$gametype];
	
	// Liste des lines up
	$lineup1=unserialize($info['lineup1']);
	for($i=0;isset($lineup1[$i]);$i++) {
		if(ereg("^([0-9]+)$",$lineup1[$i])) $lineup1[$i]=$membres->getPseudo($lineup1[$i]);
		$template->setVar(array(
			'lineup1i'		=>	($i+1),
			'lineup1value'	=>	str_replace('','&euro;',$lineup1[$i]),
		));
		$template->parse('lineup1',true);
	}
	$lineup2=unserialize($info['lineup2']);
	for($i=0;isset($lineup2[$i]);$i++) {
		if(ereg("^([0-9]+)$",$lineup2[$i])) $lineup2[$i]=$membres->getPseudo($lineup2[$i]);
		$template->setVar(array(
			'lineup2i'		=>	($i+1),
			'lineup2value'	=>	str_replace('','&euro;',$lineup2[$i]),
		));
		$template->parse('lineup2',true);
	}
		
	// Liste du nombre de maps joues
	$min=$jeux[$_GET['loadxml']]['gametype'][$gametype]['minmaps'];
	$max=$jeux[$_GET['loadxml']]['gametype'][$gametype]['maxmaps'];
	if(isset($jeux[$_GET['loadxml']]['gametype'][$gametype]['DefNbMaps'])) $def=$jeux[$_GET['loadxml']]['gametype'][$gametype]['DefNbMaps'];
	for($i=$min;$i<=$max;$i++) {
		if($info['nbmaps']==$i) $template->setVar('selected',' selected="selected"');
		$template->setVar('nbMap',$i);
		$template->parse('nbmaps',true);
		$template->unsetVar(array('selected'));
	}
	
	// Scores par maps
	$scores=unserialize($info['scores']);
	foreach($scores as $j=>$var) $tab[]=$j;
	for($i=1;$i<=$max;$i++) {


		// Liste des maps
		foreach($jeux[$_GET['loadxml']]['maps'] as $j=>$var) {
			$template->setVar(array(
				'matchJeuxMaps'	=>	$var['nom']
			));
			if(isset($tab[$i-1]) && $tab[$i-1]==$var['nom']) $template->setVar('mapselected',' selected="selected"');
			else $template->unsetVar('mapselected');
			$template->parse('maps',true);
		}

		if($i==1) $template->setVar('carteNbExt','ère');
		else $template->setVar('carteNbExt','ème');
		$template->setVar('carteNb',$i);
	
		if($jeu['type']=='Team' && isset($jeu['round']) && $jeu['round']=='2') {
			$template->setVar(array(
				'team1'				=>	$jeu['team1'],
				'team2'				=>	$jeu['team2'],
				'rnd1score1value'	=>	(isset($scores[$i-1]['rnd1'][0]))?$scores[$i-1]['rnd1'][0]:'',
				'rnd1score2value'	=>	(isset($scores[$i-1]['rnd1'][1]))?$scores[$i-1]['rnd1'][1]:'',
				'rnd2score1value'	=>	(isset($scores[$i-1]['rnd2'][0]))?$scores[$i-1]['rnd2'][0]:'',
				'rnd2score2value'	=>	(isset($scores[$i-1]['rnd2'][1]))?$scores[$i-1]['rnd2'][1]:''
			));
			$template->parse('teamrnd2',true);
		} else if($jeu['type']=='Deathmatch') {
			for($i=0;isset($lineup1[$i]);$i++) {
				$template->setVar(array(
					'playerId'	=>	'joueur'.($i+1),
					'player'	=>	$lineup1[$i],
					'score'		=>	$scores[0]['lineup1'][$i]
				));
				$template->parse('dm',true);
			}
			for($i=0;isset($lineup2[$i]);$i++) {
				$template->setVar(array(
					'playerId'	=>	'adv'.($i+1),
					'player'	=>	$lineup2[$i],
					'score'		=>	$scores[0]['lineup2'][$i]
				));
				$template->parse('dm',true);
			}
			
		}else if($jeu['type']=='Duel' || $jeu['type']=='Team') $template->parse('teamorduel',true);
		$template->parse('carte',true);
		$template->unsetVar(array('teamrnd2','teamorduel'));
	}
	
} else {

	// Liste des modes de jeu
	foreach($jeux[$_GET['loadxml']]['gametype'] as $i=>$var) {
		if(!isset($gametype)) $gametype=$var['nom'];
		$template->setVar(array(
			'matchJeuxMode'	=>	$var['nom']
		));
		$template->parse('mode',true);
	}
	
	$jeu=$jeux[$_GET['loadxml']]['gametype'][$gametype];
	
	// Liste des lines up
	for($i=1;$i<=$jeu['nbplayer'];$i++) {
		$template->setVar('lineup2i',$i);
		$template->parse('lineup2',true);
	}
	if($jeu['type']=='Deathmatch') $jeu['nbplayer']=1;
	if($gametype=='Deathmatch') $jeu['nbplayer']=1;
	for($i=1;$i<=$jeu['nbplayer'];$i++) {
		$template->setVar('lineup1i',$i);
		$template->parse('lineup1',true);
	}
	
	// Liste des maps
	foreach($jeux[$_GET['loadxml']]['maps'] as $i=>$var) {
		$template->setVar(array(
			'matchJeuxMaps'	=>	htmlentities($var['nom'])
		));
		$template->parse('maps',true);
	}
	
	// Liste du nombre de maps joues
	$min=$jeux[$_GET['loadxml']]['gametype'][$gametype]['minmaps'];
	$max=$jeux[$_GET['loadxml']]['gametype'][$gametype]['maxmaps'];
	if(isset($jeux[$_GET['loadxml']]['gametype'][$gametype]['DefNbMaps'])) $def=$jeux[$_GET['loadxml']]['gametype'][$gametype]['DefNbMaps'];
	for($i=$min;$i<=$max;$i++) {
		if((isset($def) && $i==$def) || (!isset($def) && $i==$min)) $template->setVar('selected',' selected="selected"');
		$template->setVar('nbMap',$i);
		$template->parse('nbmaps',true);
		$template->unsetVar(array('selected'));
	}
	
	// Scores par maps
	for($i=1;$i<=$max;$i++) {
		if($i==1) $template->setVar('carteNbExt','ère');
		else $template->setVar('carteNbExt','ème');
		$template->setVar('carteNb',$i);
	
		if($jeu['type']=='Team' && isset($jeu['round']) && $jeu['round']=='2') {
			$template->setVar(array(
				'team1'	=>	$jeu['team1'],
				'team2'	=>	$jeu['team2']
			));
			$template->parse('teamrnd2',true);
		} else if($jeu['type']=='Duel' || $jeu['type']=='Team') $template->parse('teamorduel',true);
		$template->parse('carte',true);
		$template->unsetVar(array('teamrnd2','teamorduel','dm'));
	}

}
?>