<?php
/**
 * 1. Instanciation d'objets
 */
$matches = new matches();
$utils = new utils();
$bbcode = new bbcode;

/**
 * 2. Actions
 */

/**
 * 3. Récupération des données
 */

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','matches/index.html');
$site->addCss('templates/'.THEME.'/matches/style.css');
$site->addToTitle(' - Matches');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','matches');

/**
 * 6. Construction de la page
 */
 
// Récupération des infos
$liste=$matches->getMatchsList();

foreach($liste as $info) {
	$score1=$score2=0;
	$score=unserialize($info['scores']);
	for($i=0;isset($score[$i]);$i++) {
		for($j=1;isset($score[$i]['rnd'.$j]);$j++) {
			$score1+=$score[$i]['rnd'.$j][0];
			$score2+=$score[$i]['rnd'.$j][1];
		}
	}

	$template->setVar(array(
		'matchAdversaire'		=>	$info['adversaire'],
		'matchCleanClan'		=>	$string->clean($site->config('clan_default')),
		'matchCleanAdversaire'	=>	$string->clean($info['adversaire']),
		'matchId'				=>	$info['id'],
		'matchScore1'			=>	$score1,
		'matchScore2'			=>	$score2,
		'matchJeu'				=>	$info['jeu'],
		'matchComms'			=>	$info['comms'],
		'matchStatus'			=>	($score1>$score2)?'win':(($score1<$score2)?'lose':'draw')
	));
	$template->parse('matches', true);
}

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'module',$erreurs);
?>