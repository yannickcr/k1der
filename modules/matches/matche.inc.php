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
if($this->action('editComm')) $erreur=$matches->editComm($_GET['id'],$_GET['edit'],$_POST['message'],$_POST['note']);
else if($this->action('addComm')) $erreur=$matches->addComm($_GET['id'],(($membres->infos('id'))?$membres->infos('pseudo'):$_POST['pseudo']),$_POST['message'],$_POST['note']);
else if($this->action('delComm')) $erreur=$matches->delComm($_GET['id'],$_GET['del']);

/**
 * 3. Récupération des données
 */

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','matches/matche.html');
$site->addCss('templates/'.THEME.'/matches/style.css');
$site->addToTitle(' - Matches');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','profil11');
$template->setBlock('centre','profil12');
$template->setBlock('centre','lineup1');

$template->setBlock('centre','profil21');
$template->setBlock('centre','profil22');
$template->setBlock('centre','lineup2');

$template->setBlock('centre','teams1');
$template->setBlock('centre','teams2');
$template->setBlock('centre','teams3');

$template->setBlock('centre','maps');

$template->setBlock('centre','screen');
$template->setBlock('centre','total');
$template->setBlock('centre','rounds');
$template->setBlock('centre','scores');

// Commentaires
$template->setBlock('centre','commstar');
$template->setBlock('centre','commhalfstar');
$template->setBlock('centre','commemptystar');
$template->setBlock('centre','comm-note');
$template->setBlock('centre','avatarimg');
$template->setBlock('centre','quote-comm');
$template->setBlock('centre','edit-comm');
$template->setBlock('centre','del-comm');
$template->setBlock('centre','auteurprofil1');
$template->setBlock('centre','auteurprofil2');
$template->setBlock('centre','comm');
$template->setBlock('centre','erreur-pseudo');
$template->setBlock('centre','erreur-pseudo2');
$template->setBlock('centre','erreur-message');
$template->setBlock('centre','comms');
$template->setBlock('centre','add-commtitle');
$template->setBlock('centre','edit-commtitle');
$template->setBlock('centre','nolog');
$template->setBlock('centre','add-comm');
$template->setBlock('centre','noadd-comm');

/**
 * 6. Construction de la page
 */
 
// Récupération des infos
$matche=$matches->getMatchInfos($_GET['id']);
$jeux=$matches->listGames();
$maps=unserialize($matche['scores']);

$lineup[1]=unserialize($matche['lineup1']);
$lineup[2]=unserialize($matche['lineup2']);

// Si 1vs1
if(empty($matche['adversaire']) && sizeof($lineup[2])==1) {
		if((int)$lineup[1][0]>0) $clan=$membres->getPseudo($lineup[1][0]);
		else $clan=$lineup[1][0];
		if((int)$lineup[2][0]>0) $matche['adversaire']=$membres->getPseudo($lineup[2][0]);
		else $matche['adversaire']=$lineup[2][0];
		$adversaire2='adversaires';
} else {
	$clan=$site->config('clan_default');
	$adversaire2=$matche['adversaire'];
}

$site->addToTitle(' - '.$clan.' vs '.$matche['adversaire']);

$template->setVar(array(
	'clan'			=>	$clan,
	'clan2'			=>	$site->config('clan_default'),
	'adversaire2'	=>	$adversaire2,
	'adversaire'	=>	$matche['adversaire'],
	'jeu'			=>	$jeux[$matche['jeu']]['Infos']['name'],
	'type'			=>	ucfirst($matche['type']),
	'lieu'			=>	$matches->lieux[$matche['lieu']],
	'lieu_nom'		=>	(empty($matche['lieu_nom']))?'&nbsp;':$matche['lieu_nom'],
	'lieu_id'		=>	$matche['lieu_id'],
	'date'			=>	$string->formatDate('%A %d %B %Y',$matche['date'],true),
	'mode'			=>	$matche['mode'],
	'niveau'		=>	$matches->niveaux[$matche['niveau']],
	'nbmaps'		=>	($matche['nbmaps']>1)?'s':''
));

// Boucle des maps
for($i=0;isset($maps[$i]);$i++) {
	$template->setVar(array(
		'map'	=>	$maps[$i]['map'],
		'sep'	=>	isset($maps[$i+1])?(isset($maps[$i+2])?', ':' et '):'' 
	));
	$template->parse('maps',true);
}

// Boucles des Lines Up
for($j=1;$j<3;$j++) {
	for($i=0;$i<sizeof($lineup[$j]);$i++) {
		if((int)$lineup[$j][$i]>0) $pseudo=$membres->getPseudo($lineup[$j][$i]);
		else $pseudo=$lineup[$j][$i];
		
		$template->setVar(array(
			'joueur'	=>	htmlentities($pseudo),
			'sep'		=>	isset($lineup[$j][$i+1])?(isset($lineup[$j][$i+2])?', ':' et '):'' 
		));
		
		if((int)$lineup[$j][$i]>0) {
			$template->parse('profil'.$j.'1');
			$template->parse('profil'.$j.'2');
		}
		$template->parse('lineup'.$j,true);
		$template->unsetVar(array('profil'.$j.'1','profil'.$j.'2'));
	}
}

// Boucles des Scores par map
$fulltotal0=$fulltotal1=0;
if(!isset($jeux[$matche['jeu']]['gametype'][$matche['mode']]['round'])) $round=1;
else $round=$jeux[$matche['jeu']]['gametype'][$matche['mode']]['round'];

for($i=0;isset($maps[$i]);$i++) {
	$total0=$total1=0;
	for($j=1;$j<=$round;$j++) {
		$template->setVar(array(
			'j'			=>	$j,
			'team'		=>	isset($jeux[$matche['jeu']]['gametype'][$matche['mode']]['team'.$j])?$jeux[$matche['jeu']]['gametype'][$matche['mode']]['team'.$j]:'Score',
			'score0'	=>	$maps[$i]['rnd'.$j][0],
			'score1'	=>	$maps[$i]['rnd'.$j][1]
		));
		$total0+=$maps[$i]['rnd'.$j][0];
		$total1+=$maps[$i]['rnd'.$j][1];
		
		if(isset($jeux[$matche['jeu']]['gametype'][$matche['mode']]['team1'])) $template->parse('teams3');
		$template->parse('rounds',true);
	}
	$template->setVar(array(
		'i'			=>	($i+1),
		'map'		=>	$maps[$i]['map'],
		'mapsrc'	=>	isset($jeux[$matche['jeu']]['maps'][$maps[$i]['map']]['image'])?'modules/matches/jeux/'.$matche['jeu'].'/'.$jeux[$matche['jeu']]['maps'][$maps[$i]['map']]['image']:'modules/matches/jeux/nomap.jpg',
		'total0'	=>	$total0,
		'total1'	=>	$total1
	));
	
	$fulltotal0+=$total0;
	$fulltotal1+=$total1;

	if(isset($jeux[$matche['jeu']]['gametype'][$matche['mode']]['team1'])) {
		$template->parse('teams1');
		$template->parse('teams2');
	}
	$template->parse('scores',true);
	if($round>1) $template->parse('total');
	
	$template->unsetVar(array('rounds','teams1','teams2','teams3'));
}

// Score
$status=$matches->getMatchStatus($fulltotal0,$fulltotal1);

	$template->setVar(array(
		'fulltotal0'	=>	$fulltotal0,
		'fulltotal1'	=>	$fulltotal1,
		'matchIllus'	=>	$matches->illus[$status][0],
		'matchIllusTxt'	=>	$matches->illus[$status][1],
	));

// Commentaires du matche
if($matche['comms']>0) {
	$res=$sql->query('
		SELECT 
			c.id,
			author_id,
			author_name,
			note,
			date,
			message,
			mail,
			avatar
		FROM 
			mod_comments c LEFT JOIN mod_membres m ON c.author_id=m.id
		WHERE 
			resource_id='.$_GET['id'].' && 
			module="matches"
	');
	
	while($info=$sql->fetchAssoc($res)) {
		$template->setVar(array(
			'matchId'			=>	$_GET['id'],
			'clanCleanNom'		=>	$string->clean($site->config('clan_default')),
			'adversaireCleanNom'=>	$string->clean($matche['adversaire']),
			'commId'			=>	$info['id'],
			'commAuteur'		=>	$info['author_name'],
			'commDate'			=>	$string->formatDate('%A %d %B %Y  %H:%M',$info['date'],true),
			'commMessage'		=>	$bbcode->BBCodeToHtml($info['message'])
		));
		if($info['avatar']) {
			$template->setVar('commAvatar',$membres->getAvatar($info['avatar'],$info['mail']));
			$template->parse('avatarimg', true);
		}
		if(($info['author_id']!=0 && $membres->infos('id')==$info['author_id']) || $membres->verifAcces('download_comm_edit') || $membres->infos('groupe')==4) {
			$template->parse('edit-comm');
			$template->parse('del-comm');
		}
		if($membres->verifAcces('download_post_comm')) $template->parse('quote-comm');
		if($info['author_id']!=0) {
			$template->parse('auteurprofil1');
			$template->parse('auteurprofil2');
		}
		// Note du fichier
		if($info['note']!=-1) {
			$template->setVar('rowspan',' rowspan="2"');
			$star=5;
			
			$reste=$info['note']-floor($info['note']);
			if($reste!=0 && $reste>=0.3 && $reste<0.7) {
				$template->parse('commhalfstar');
				$star--;
			}
			
			for($i=0;$i<round($info['note']) && $star>0;$i++) {
				$template->parse('commstar',true);
				$star--;
			}
			
			for($star;$star>0;$star--) $template->parse('commemptystar',true);
	
			$template->parse('comm-note');
		} else $template->setVar('nonote','nonote');

		$template->parse('comm',true);
		$template->unsetVar(array('auteurprofil1','auteurprofil2','edit-comm','quote-comm','rowspan','nonote','avatarimg','commstar','commhalfstar','commemptystar','comm-note','commedit'));
	}
	$template->parse('comms');
}

if($membres->verifAcces('download_post_comm')) {

	if(!$membres->infos('id')) $template->parse('nolog');

	// Ajout d'un commentaire
	$site->barreMiseEnForme($template,'lite');
	
	// Erreur lors de la soumission du formulaire
	if(isset($erreur)) {
		if(!in_array($_POST['note'],array(0,1,2,3,4,5))) $_POST['note']='-1';
		
		$template->setVar(array(
			'pseudo'				=>	(isset($_POST['pseudo'])?$_POST['pseudo']:''),
			'message'				=>	$_POST['message'],
			'checked'.$_POST['note']=>	' checked="checked"'
		));
		if($erreur==1) $template->parse('erreur-message');
		else if($erreur==2) $template->parse('erreur-pseudo');
		else if($erreur==3) $template->parse('erreur-pseudo2');
		$template->parse('add-commtitle');
	// Si il s'agit d'une édition de commentaire
	} else if(!empty($_GET['edit'])) {
		$template->parse('edit-commtitle');
		$res=$sql->query('SELECT author_id,note,message FROM mod_comments WHERE id='.$_GET['edit']);
		$info=$sql->fetchAssoc($res);
		if($info['author_id']==$membres->infos('id') || $membres->verifAcces('download_post_comm')) {
			$template->setVar(array(
				'message'				=>	$info['message'],
				'checked'.$info['note']	=>	' checked="checked"'
			));
			
		}
	// Si il s'agit d'une citaction de commentaire
	} else if(!empty($_GET['quote'])) {
		$template->parse('add-commtitle');
		$res=$sql->query('SELECT author_name,message FROM mod_comments WHERE id='.$_GET['quote']);
		$info=$sql->fetchAssoc($res);
		$template->setVar('message','[QUOTE='.$info['author_name'].']'.$info['message'].'[/QUOTE]'."\r\n");
	} else {
		$template->setVar('checked-1',' checked="checked"');
		$template->parse('add-commtitle');
	}
	$template->parse('add-comm');
} else $template->parse('noadd-comm');


/**
 * 7. Affichage des erreurs
 */
if(isset($erreurs)) $site->showErrors($template,'module',$erreurs);
?>