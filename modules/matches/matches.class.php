<?php
/**
 * Classe de gestion des matches.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class matches {

	public $niveaux=array(
		'PCW',
		'Amical',
		'Ladder',
		'Poules',
		'16&egrave;mes winner',
		'8&egrave;mes winner',
		'Quarts winner',
		'Demi-finale winner',
		'Finale winner',
		'16&egrave;mes looser',
		'8&egrave;mes looser',
		'Quarts looser',
		'Demi-finale looser',
		'Finale looser',
		'Grande Finale'
	);
	
	public $lieux=array(
		'internet'	=>	'Internet',
		'lanparty'	=>	'Lan Party',
		'sallejeux'	=>	'Salle de jeux'
	);
	
	public $illus=array(
		'1'		=>	array('win.jpg'	,'Matche gagn'),
		'0'		=>	array('draw.jpg','Matche nul'),
		'-1'	=>	array('lose.jpg','Matche perdu'),
	);
	
	public function getMatchInfos($id) {
		global $sql;
		
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				jeu,
				type,
				lieu,
				lieu_nom,
				lieu_id,
				m.date,
				mode,
				adversaire,
				niveau,
				nbmaps,
				lineup1,
				lineup2,
				scores,
				COUNT(c.message) comms 
			FROM 
				mod_matches m 
					LEFT JOIN mod_comments c ON m.id=c.resource_id AND c.module="matches"
			WHERE 
				m.id='.$id.' AND 
				(c.module="matches" || m.votes=0)
			GROUP BY
				m.id
		'));
		
		return $info;
	}
	
	public function listGames() {
		global $string;
		$jeux=$string->listDir('modules/matches/jeux');
		$tab=array();
		$tmp=array();
		foreach($jeux as $jeu) {
			$file='modules/matches/jeux/'.$jeu.'/config.xml';
			if(file_exists($file)) {
				$tmp[$jeu]=$string->parseXml($file);
			
				// Infos du jeu
				$tab[$jeu]['Infos']=$tmp[$jeu]['jeu'][0]['attributes'];
				
				// Types de jeu
				foreach($tmp[$jeu]['gametype'] as $i=>$var) {
					$tab[$jeu]['gametype'][$var['attributes']['nom']]=$var['attributes'];
				}
				
				// Maps
				foreach($tmp[$jeu]['map'] as $i=>$var) {
					$tab[$jeu]['maps'][$var['attributes']['nom']]=$var['attributes'];
				}
			}
		}
		return $tab;
	}

	public function addComm($matche,$pseudo,$message,$note) {
		global $membres,$sql,$site,$string;
		if(!$membres->verifAcces('matches_post_comm')) return false;
		
		$message=$site->clear4Sql($message);

		if($membres->infos('id')) $id=$membres->infos('id');
		else $id=0;

		
		# Vérification des informations saisies
		if(empty($pseudo)) return 2;
		if(eregi('[^A-Z0-9]',$pseudo)) return 3;
		if(empty($message)) return 1;
		if(!in_array($note,array(0,1,2,3,4,5))) $note=-1;
		# The End
		
		// Récupération des informations du fichier
		list($adversaire,$votes,$oldnote)=$sql->fetchRow($sql->query('
			SELECT 
				adversaire,
				votes,
				note
			FROM 
				mod_matches
			WHERE 
				id='.$matche
		));

		// Postage du commentaire
		$sql->query('
			INSERT INTO 
				mod_comments (module,resource_id,note,author_name,author_id,date,message)
			VALUES (
				"matches",
				"'.$matche.'",
				"'.$note.'",
				"'.$pseudo.'",
				"'.$id.'",
				"'.time().'",
				"'.$message.'"
		)');
		
		// Mise  jour de la note
		if(in_array($note,array(0,1,2,3,4,5))) {
			$newnote=round((($oldnote*$votes)+$note)/($votes+1),2);
			$sql->query('UPDATE mod_matches SET votes=votes+1,note="'.$newnote.'" WHERE id="'.$matche.'"');
		}

		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$string->clean($site->config('clan_default')).'-vs-'.$string->clean($adversaire).'-id'.$matche.'.html#comms');
		exit();
	}

	public function editComm($matche,$id,$message,$note) {
		global $membres,$sql,$site,$string;
		
		$info=$sql->fetchAssoc($sql->query('SELECT author_id,note FROM mod_comments WHERE id='.$id));
		$oldnote=$info['note'];
		
		if($membres->infos('groupe')!=4 && ($info['author_id']==0 || ($info['author_id']!=$membres->infos('id') && $membres->verifAcces('galeries_post_comm')))) return false;
		
		$message=$site->clear4Sql($message);
		
		# Vérification des informations saisies
		if(empty($message)) return 1;
		if(!in_array($note,array(0,1,2,3,4,5))) $note=-1;
		# The End
		
		// Postage du commentaire
		$sql->query('
			UPDATE 
				mod_comments
			SET 
				note="'.$note.'",
				message="'.$message.'"
			WHERE
				id='.$id
		);

		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				adversaire,
				lineup2,
				note,
				votes
			FROM 
				mod_matches
			WHERE 
				id='.$matche.'
		'));

		
		// Mise  jour de la note
		if(in_array($note,array(0,1,2,3,4,5))) {
			$newnote=round((($info['note']*$info['votes'])-$oldnote+$note)/($info['votes']),2);
			$sql->query('UPDATE mod_matches SET note="'.$newnote.'" WHERE id='.$matche);
		}

		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$string->clean($site->config('clan_default')).'-vs-'.$string->clean($info['adversaire']).'-id'.$matche.'.html#comms');
		exit();
	}
	
	public function delComm($matche,$id) {
		global $membres,$sql,$site,$string;
		
		$info=$sql->fetchAssoc($sql->query('SELECT author_id,note FROM mod_comments WHERE id='.$id));
		$oldnote=$info['note'];
		
		if($membres->infos('groupe')!=4 && ($info['author_id']==0 || ($info['author_id']!=$membres->infos('id') && $membres->verifAcces('galeries_post_comm')))) return false;
		
		// Suppression du commentaire
		$info=$sql->fetchAssoc($sql->query('DELETE FROM mod_comments WHERE id='.$id));

		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				adversaire,
				lineup2,
				note,
				votes
			FROM 
				mod_matches
			WHERE 
				id='.$matche.'
		'));

		
		// Mise  jour de la note
		if(in_array($oldnote,array(0,1,2,3,4,5))) {
			if($info['votes']-1==0) $newnote=0;
			else $newnote=round((($info['note']*$info['votes'])-$oldnote)/($info['votes']-1),2);
			$sql->query('UPDATE mod_matches SET note="'.$newnote.'",votes=votes-1 WHERE id='.$matche);
		}
		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$string->clean($site->config('clan_default')).'-vs-'.$string->clean($info['adversaire']).'-id'.$matche.'.html#comms');
		exit();
	}
	
	public function ajouterMatch($post) {
		global $sql,$site,$membres;
		// Dates de début et de fin
		$datedebut=mktime(0,0,0,$post['moisdebut'],$post['jourdebut'],$post['anneedebut']);
		$datefin=mktime(0,0,0,$post['moisfin'],$post['jourfin'],$post['anneefin']);
		if($post['type']=='unique') $datedebut=mktime(0,0,0,$post['mois'],$post['jour'],$post['annee']);

		// Nom et Id du tournoi ou de la manifestation
		if($post['lieu']=='lanparty') $lieu_nom=$post['lanparty'];
		else if($post['lieu']=='sallejeux') $lieu_nom=$post['sallejeux'];
		else if($post['lieu']=='internet' && $post['type']=='tournoi') $lieu_nom=$post['tournoi'];
		else $lieu_nom='';
		
		if(!empty($lieu_nom) && $post['type']=='tournoi') {
			$res=$sql->query('SELECT id FROM mod_tournois WHERE nom="'.$site->clear4Sql($lieu_nom).'"');
			if($sql->numRows($res)==0) {
				$sql->query('INSERT INTO mod_tournois (nom,date_debut,date_fin,jeu) VALUES ("'.$site->clear4Sql($lieu_nom).'","'.$datedebut.'","'.$datefin.'","'.$site->clear4Sql($post['jeu']).'")');
				$lieu_id=$sql->getId();
			} else {
				$info=$sql->fetchAssoc($res);
				$lieu_id=$info['id'];
			}
		} else if($post['lieu']=='lanparty') {
			$res=$sql->query('SELECT id FROM mod_lanparty WHERE nom="'.$site->clear4Sql($lieu_nom).'"');
			$info=$sql->fetchAssoc($res);
			$lieu_id=$info['id'];
		} else $lieu_id=0;
		
		// Line up 1 et 2
		$lineup1=array();
		$lineup2=array();
		foreach($post as $i=>$var) {
			if(ereg('^joueur([0-9]+)$',$i) && !empty($var)) {
				$id=$membres->getId($var);
				if($id!=false) $lineup1[]=$id;
				else $lineup1[]=$var;
			}
			if(ereg('^adv([0-9]+)$',$i) && !empty($var)) {
				$id=$membres->getId($var);
				if($id!=false) $lineup2[]=$id;
				else $lineup2[]=$var;
			}
		}
		
		// Scores
		$scores=array();
		if($post['mode']=='Deathmatch') {
			$scores[0]=array('map'=>$post['mapCarte1']);
			for($i=1;isset($post['scorejoueur'.$i.'Carte1']);$i++) {
				$scores[0]['lineup1'][]=$post['scorejoueur'.$i.'Carte1'];
			}
			for($i=1;isset($post['scoreadv'.$i.'Carte1']);$i++) {
				$scores[0]['lineup2'][]=$post['scoreadv'.$i.'Carte1'];
			}
		} else {
			for($i=1;isset($post['mapCarte'.$i]) && $i<=$post['nbmaps'];$i++) {
				$scores[$i-1]=array('map'=>$post['mapCarte'.$i]);
				for($j=1;isset($post['rnd'.$j.'score1Carte'.$i]);$j++) {
					$scores[$i-1]['rnd'.$j]=array($post['rnd'.$j.'score1Carte'.$i],$post['rnd'.$j.'score2Carte'.$i]);
				}
			}
		}

		$sql->query('
			INSERT INTO 
				mod_matches (jeu,type,lieu,lieu_nom,lieu_id,date,mode,adversaire,niveau,nbmaps,lineup1,lineup2,scores)
			VALUES (
				"'.$site->clear4Sql($post['jeu']).'",
				"'.$site->clear4Sql($post['type']).'",
				"'.$site->clear4Sql($post['lieu']).'",
				"'.$site->clear4Sql($lieu_nom).'",
				"'.$site->clear4Sql($lieu_id).'",
				"'.$site->clear4Sql($datedebut).'",
				"'.$site->clear4Sql($post['mode']).'",
				"'.$site->clear4Sql($post['adver']).'",
				"'.$site->clear4Sql($post['matchlevel']).'",
				"'.$site->clear4Sql($post['nbmaps']).'",
				"'.$site->clear4Sql(serialize($lineup1)).'",
				"'.$site->clear4Sql(serialize($lineup2)).'",
				"'.$site->clear4Sql(serialize($scores)).'"
		)');
		
		if($post['submit']=="Terminer") header('location:liste.html#mess0');
		else header('location:ajouter.html#mess0');
		exit();
	}
	
	public function modifierMatch($post) {
		global $sql,$site,$membres;
		// Dates de début et de fin
		$datedebut=mktime(0,0,0,$post['moisdebut'],$post['jourdebut'],$post['anneedebut']);
		$datefin=mktime(0,0,0,$post['moisfin'],$post['jourfin'],$post['anneefin']);
		if($post['type']=='unique') $datedebut=mktime(0,0,0,$post['mois'],$post['jour'],$post['annee']);

		// Nom et Id du tournoi ou de la manifestation
		if($post['lieu']=='lanparty') $lieu_nom=$post['lanparty'];
		else if($post['lieu']=='sallejeux') $lieu_nom=$post['sallejeux'];
		else if($post['lieu']=='internet' && $post['type']=='tournoi') $lieu_nom=$post['tournoi'];
		else $lieu_nom='';
		
		if(!empty($lieu_nom) && $post['type']=='tournoi') {
			$res=$sql->query('SELECT id FROM mod_tournois WHERE nom="'.$site->clear4Sql($lieu_nom).'"');
			if($sql->numRows($res)==0) {
				$sql->query('INSERT INTO mod_tournois (nom,date_debut,date_fin,jeu) VALUES ("'.$site->clear4Sql($lieu_nom).'","'.$datedebut.'","'.$datefin.'","'.$site->clear4Sql($post['jeu']).'")');
				$lieu_id=$sql->getId();
			} else {
				$info=$sql->fetchAssoc($res);
				$lieu_id=$info['id'];
			}
		} else if($post['lieu']=='lanparty') {
			$res=$sql->query('SELECT id FROM mod_lanparty WHERE nom="'.$site->clear4Sql($lieu_nom).'"');
			$info=$sql->fetchAssoc($res);
			$lieu_id=$info['id'];
		} else $lieu_id=0;
		
		// Line up 1 et 2
		$lineup1=array();
		$lineup2=array();
		foreach($post as $i=>$var) {
			if(ereg('^joueur([0-9]+)$',$i)) {
				$id=$membres->getId($var);
				if($id!=false) $lineup1[]=$id;
				else $lineup1[]=$var;
			}
			if(ereg('^adv([0-9]+)$',$i)) {
				$id=$membres->getId($var);
				if($id!=false) $lineup2[]=$id;
				else $lineup2[]=$var;
			}
		}
		
		// Scores
		$scores=array();
		if($post['mode']=='Deathmatch') {
			$scores[0]=array('map'=>$post['mapCarte1']);
			for($i=1;isset($post['scorejoueur'.$i.'Carte1']);$i++) {
				$scores[0]['lineup1'][]=$post['scorejoueur'.$i.'Carte1'];
			}
			for($i=1;isset($post['scoreadv'.$i.'Carte1']);$i++) {
				$scores[0]['lineup2'][]=$post['scoreadv'.$i.'Carte1'];
			}
		} else {
			for($i=1;isset($post['mapCarte'.$i]) && $i<=$post['nbmaps'];$i++) {
				$scores[$i-1]=array('map'=>$post['mapCarte'.$i]);
				for($j=1;isset($post['rnd'.$j.'score1Carte'.$i]);$j++) {
					$scores[$i-1]['rnd'.$j]=array($post['rnd'.$j.'score1Carte'.$i],$post['rnd'.$j.'score2Carte'.$i]);
				}
			}
		}
	
		$sql->query('
			UPDATE 
				mod_matches
			SET
				jeu="'.$site->clear4Sql($post['jeu']).'",
				type="'.$site->clear4Sql($post['type']).'",
				lieu="'.$site->clear4Sql($post['lieu']).'",
				lieu_nom="'.$site->clear4Sql($lieu_nom).'",
				lieu_id="'.$site->clear4Sql($lieu_id).'",
				date="'.$site->clear4Sql($datedebut).'",
				mode="'.$site->clear4Sql($post['mode']).'",
				adversaire="'.$site->clear4Sql($post['adver']).'",
				niveau="'.$site->clear4Sql($post['matchlevel']).'",
				nbmaps="'.$site->clear4Sql($post['nbmaps']).'",
				lineup1="'.$site->clear4Sql(serialize($lineup1)).'",
				lineup2="'.$site->clear4Sql(serialize($lineup2)).'",
				scores="'.$site->clear4Sql(serialize($scores)).'"
			WHERE
				id="'.$_GET['id'].'"
		');
		
		header('location:liste.html#mess1');
		exit();
	}
	
	public function supprimerMatch($id) {
		global $sql;
		$sql->query('DELETE FROM mod_matches WHERE id="'.$id.'"');
		header('location:liste.html#mess2');
		exit();
	}
	
	public function getMatchStatus($score1,$score2) {
		if($score1>$score2) return 1;
		else if($score1<$score2) return -1;
		else return 0;
	}
	
	public function getMatchsList() {
		global $sql;
		$res=$sql->query('
			SELECT 
				m.id,
				jeu,
				adversaire,
				scores,
				COUNT(c.message) comms 
			FROM 
				mod_matches m 
					LEFT JOIN mod_comments c ON m.id=c.resource_id AND c.module="matches"
			WHERE 
				(c.module="matches" || m.votes=0)
			GROUP BY
				m.id
			ORDER BY 
				m.date DESC, 
				m.id DESC
		');
		$infos=array();
		while($info=$sql->fetchAssoc($res)) {
			$infos[]=$info;
		}
		return $infos;
	}
}
?>