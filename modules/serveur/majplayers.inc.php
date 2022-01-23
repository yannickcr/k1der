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
$cache=isset($_POST['cache'])?true:false;
if($site->config('serveur_'.$id.'_ip')==false) exit();

$serveur = new serveur($site->config('serveur_'.$id.'_ip'),$site->config('serveur_'.$id.'_port'));

/**
 * 4. Initialisation de la page
 */
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);
$template->setFile('centre','serveur/majplayers.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','noplayers');
$template->setBlock('centre','players');

/**
 * 6. Construction de la page
 */

$players=$serveur->getPlayers($cache);

$template->setVar('i',$id);

foreach($players as $player) {

	$player['time']=round($player['time']);
	$timeSec=$player['time']-floor($player['time']/60)*60;
	$timeSec=(strlen($timeSec)==2)?$timeSec:'0'.$timeSec;

	$timeMin=floor($player['time']/60)-floor($player['time']/3600)*60;
	$timeMin=(strlen($timeMin)==2)?$timeMin:'0'.$timeMin;
	
	$timeHou=floor($player['time']/3600);

	$template->setVar(array(
		'playerId'			=>	$player['id'],
		'playerName'		=>	$player['name'],
		'playerScore'		=>	$player['score'],
		'playerTime'		=>	$timeHou.':'.$timeMin.':'.$timeSec
	));
	$template->parse('players',true);
}
if(!count($players)) $template->parse('noplayers',true);

/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>