<?php
/**
 * Classe de gestion des clans.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class clans {

    /**
     * Vrifie si le membre est le leader d'un clan ou pas
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	boolean
     */ 
	function isClanLeader() {
		global $sql,$membres;
		$res=$sql->query('SELECT leader_id,leader_pseudo FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"');
		if($sql->numRows($res)==0) return false;
		$info=$sql->fetchAssoc($res);
		if($info['leader_id']==$membres->infos('id') && $info['leader_pseudo']==$membres->infos('pseudo')) return true;
		else return false;
	}

	function histo($message) {
		global $site,$sql,$membres;
		$info=$sql->fetchAssoc($sql->query('SELECT histo FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"'));
		if(!@unserialize($info['histo'])) $histo=array();
		else $histo=unserialize($info['histo']);
		$histo[]=array(date('U'),$message);
		$histo=serialize($histo);
		$sql->query('UPDATE mod_membres_clans SET histo="'.$site->clear4Sql($histo).'" WHERE id="'.$membres->infos('clan_id').'"');
		return true;
	}

	/**
	 * créer un clan
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations du clan
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
	 */
	function create($post,&$template) {
		global $site,$sql,$membres;
		if($membres->infos('clan_nom')!='' && !ereg('postul_(.*)',$membres->infos('clan_nom'))) $site->error('Vous possdez dj un clan.');
		 
		# Vérification des informations saisies
		if(empty($post['nom'])) $erreur=1;
		else if(!eregi("^([a-z0-9 ]+)$",$post['nom'])) $erreur=3;
		else if(strlen($post['nom'])<3 || strlen($post['nom'])>100) $erreur=4;
		else {
			$res=$sql->query('SELECT nom FROM mod_membres_clans WHERE nom="'.$post['nom'].'"');
			if($sql->numRows($res)>=1) $erreur=2;
		}
		
		if(!isset($erreur)) {
			$tag=str_replace(' ','&nbsp;',$site->clear4sql($post['tag']));
			if(trim($post['tag'])=='') $erreur=5;
			else if(strlen($post['tag'])>20) $erreur=6;
			else {
				$res=$sql->query('SELECT tag FROM mod_membres_clans WHERE tag="'.$tag.'"');
				if($sql->numRows($res)>=1) $erreur=7;
			}
		}
		if($post['tagempl']!=1 && $post['tagempl']!=2) $post['tagempl']=1;
		$post['site']=substr(str_replace('http://','',$post['site']),0,255);
		$post['irc']=substr(eregi_replace('^#','',$post['irc']),0,100);
		$post['ircserver']=substr(eregi_replace('[^A-Z0-9\.]','',$post['ircserver']),0,100);
		
		if(isset($erreur)) {
			$template->setBlock('centredroite','erreur-clan'.$erreur);
			$template->parse('erreur-clan'.$erreur, true);
			return false;
		}
		
		// Bon, on va dire que c'est bon, on met tout ce joli monde dans la base
		$sql->query('
			INSERT INTO 
				mod_membres_clans (nom,tag,tagempl,leader_id,leader_pseudo,site,irc,ircserver) 
			VALUES (
				"'.$site->clear4sql($post['nom']).'",
				"'.$tag.'",
				"'.$site->clear4sql($post['tagempl']).'",
				"'.$site->clear4sql($membres->infos('id')).'",
				"'.$site->clear4sql($membres->infos('pseudo')).'",
				"'.$site->clear4sql($post['site']).'",
				"'.$site->clear4sql($post['irc']).'",
				"'.$site->clear4sql($post['ircserver']).'"
		)');
		
		
		// On met  jour le profil du leader
		$sql->query('
			UPDATE
				mod_membres
			SET 
				clan_id="'.$sql->getId().'",
				clan_nom="'.$site->clear4sql($post['nom']).'"
			WHERE 
				id="'.$membres->infos('id').'"
		');

		// Si le crateur du clan avait postul dans un clan avant de créer celui-ci, on supprime tout a
		$this->annulerJoin();
		
		$this->histo('Création du clan '.$site->clear4sql($post['nom'],true,true,false));
		
		header('location:mon-profil.html#mess10');
		exit();
	}

	/**
	 * Mise  jour des informations d'un clan
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations du clan
	 */
	function edit($id,$post,&$template) {
		global $site,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut modifier les informations du clan.');
		 
		# Vérification des informations saisies
		if(empty($post['nom'])) $erreur=1;
		else if(!eregi("^([a-z0-9 ]+)$",$post['nom'])) $erreur=3;
		else if(strlen($post['nom'])<3 || strlen($post['nom'])>100) $erreur=4;
		else {
			$res=$sql->query('SELECT nom FROM mod_membres_clans WHERE nom="'.$post['nom'].'" && id!="'.$id.'"');
			if($sql->numRows($res)>=1) $erreur=2;
		}
		
		if(!isset($erreur)) {
			$tag=str_replace(' ','&nbsp;',$site->clear4sql($post['tag']));
			if(trim($post['tag'])=='') $erreur=5;
			else if(strlen($post['tag'])>20) $erreur=6;
			else {
				$res=$sql->query('SELECT tag FROM mod_membres_clans WHERE tag="'.$tag.'" && id!="'.$id.'"');
				if($sql->numRows($res)>=1) $erreur=7;
			}
		}
		if($post['tagempl']!=1 && $post['tagempl']!=2) $post['tagempl']=1;
		$post['site']=substr(str_replace('http://','',$post['site']),0,255);
		$post['irc']=substr(eregi_replace('^#','',$post['irc']),0,100);
		$post['ircserver']=substr(eregi_replace('[^A-Z0-9\.]','',$post['ircserver']),0,100);
		
		if(isset($erreur)) {
			$template->setBlock('centredroite','erreur-clan'.$erreur);
			$template->parse('erreur-clan'.$erreur, true);
			return false;
		}
		
		/**
		 * Vérification du leader (si il existe et si il fait bien partit de ce clan)
		 */
		$req=$sql->query('SELECT id,pseudo,clan_nom FROM mod_membres WHERE id="'.(int)$post['leader'].'" && clan_id="'.$id.'"');
		// Il n'existe pas ou ne fait pas partit du clan, ce qui est mal (boulet qui a essay de corrompre le systme).
		// Dans ce cas on assigne l'id et le pseudo de l'utilisateur courant (le boulet en question).
		if($sql->numRows($req)==0) return false;
		$info=$sql->fetchAssoc($req);
		$clan_nom=$info['clan_nom'];
		$txt='leader_id="'.$site->clear4sql($info['id']).'",';
		$txt.='leader_pseudo="'.$site->clear4sql($info['pseudo']).'",';
		
		/**
		 * Bon, on va dire que c'est bon, on met tout ce joli monde dans la base
		 */
		$sql->query('
			UPDATE 
				mod_membres_clans 
			SET
				nom="'.$site->clear4sql($post['nom']).'",
				tag="'.$tag.'",
				tagempl="'.$site->clear4sql($post['tagempl']).'",
				'.$txt.'
				site="'.$site->clear4sql($post['site']).'",
				irc="'.$site->clear4sql($post['irc']).'",
				ircserver="'.$site->clear4sql($post['ircserver']).'"
			WHERE 
				id="'.$id.'"
		');
		
		/**
		 * Holala, si le nom du clan a chang, il faut mettre  jours les membres
		 */
		if($clan_nom!=$post['nom']) {
			$sql->query('
				UPDATE
					mod_membres
				SET 
					clan_nom="'.$site->clear4sql($post['nom']).'"
				WHERE 
					clan_id="'.$id.'" &&
					clan_nom="'.$clan_nom.'"
			');
		/**
		 * Et les postulants aussi
		 */
			$sql->query('
				UPDATE
					mod_membres
				SET 
					clan_nom="postul_'.$site->clear4sql($post['nom']).'"
				WHERE 
					clan_id="'.$id.'" &&
					clan_nom="postul_'.$clan_nom.'"
			');
		}
		
		$this->histo('Mise  jour des informations du clan');
		
		if($this->isClanLeader()==false) header('location:mon-profil.html#mess3');
		else header('location:mon-profil-clanedit.html#mess2');
		exit();
	}
	
    /**
     * Changement de la bannière du clan
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Choix de banniere ou pas
	 * @param	array		URL vers la banniere
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
     */ 
	function changeBan($banniere,$url,&$sub_template) {
		global $site,$sql,$membres;
		$types=array('image/x-png','image/gif','image/pjpeg','image/jpeg','image/png');
		# Pas/Plus d'avatar
		if($banniere==1) {
			$sql->query('UPDATE mod_membres_clans SET banniere="" WHERE id="'.$membres->infos('clan_id').'"');
			header("Cache-control: private, no-cache");
			header('location:'.$_SERVER['REQUEST_URI']);
			exit();
		# Récupération de bannière sur internet
		} else if($url!='http://') {
			$taille=$site->remoteInfos($url,'taille');
			if($taille===false) $erreur=1;
			else if($taille>($site->config('banniere_max_size')*1024)) $erreur=2;
			else if(!in_array($site->remoteInfos($url,'type'),$types)) $erreur=5;
			if(empty($erreur)) {
				$image=getimagesize($url);
				if($image[0]>$site->config('banniere_max_width') || $image[1]>$site->config('banniere_max_height')) $erreur=2;
			}
		} else return true;
		
		if(isset($erreur)) {
			$sub_template->setBlock('centredroite','erreur-banniere'.$erreur);
			$sub_template->parse('erreur-banniere'.$erreur, true);
			return false;
		}
		
		$sql->query('UPDATE mod_membres_clans SET banniere="'.$url.'" WHERE id="'.$membres->infos('clan_id').'"');

		$this->histo('Mise  jour de la bannière du clan');

		header('location:mon-profil-clanban.html#mess13');
		exit();
	}
	
	/**
	 * Ajout d'une nouvelle line up au clan
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Nom de la nouvelle line up
	 */
	function addLineUp($nom,$template) {
		global $site,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut modifier les lines up du clan.');

		$info=$sql->fetchAssoc($sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
		$lineup=unserialize($info['lineup']);
		if(isset($lineup[$nom])) {
			$template->setBlock('centredroite','erreur-nom');
			$template->parse('erreur-nom', true);
			return false;
		}
		$lineup[$site->clear4sql($nom,true,true,false)]=array();
		$lineup=serialize($lineup);
		$sql->query('UPDATE mod_membres_clans SET lineup="'.$site->clear4sql($lineup).'" WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"');

		$this->histo('Ajout de la line up "'.$site->clear4sql($nom,true,true,false).'"');

		header('location:mon-profil-clanlineup.html#mess4');
		exit();
	}

	/**
	 * Ajout d'une nouvelle line up au clan
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Numro de la line up  supprimer
	 */
	function delLineUp($del) {
		global $site,$string,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut modifier les lines up du clan.');

		$info=$sql->fetchAssoc($sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
		$lineUp=unserialize($info['lineup']); // Tableaux des lines up
		$k=0;
		foreach($lineUp as $i=>$var) {
			if($k==$del) {
				unset($lineUp[$i]);
				$toDel=$i;
			}
			$k++;
		}
		$lineUp=serialize($lineUp);
		$sql->query('UPDATE mod_membres_clans SET lineup="'.$site->clear4sql($lineUp).'" WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"');

		$this->histo('Suppression de la line up "'.$toDel.'"');

		header('location:mon-profil-clanlineup.html#mess5');
		exit();
	}

	/**
	 * Ajout d'une nouvelle line up au clan
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Nom de la nouvelle line up
	 */
	function majLineUp($post) {
		global $site,$string,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut modifier les lines up du clan.');

		$info=$sql->fetchAssoc($sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
		$lineUp=unserialize($info['lineup']); // Tableaux des lines up

		$req=$sql->query('SELECT id,pseudo FROM mod_membres WHERE clan_id="'.$membres->infos('clan_id').'" && clan_nom="'.$membres->infos('clan_nom').'"');
		while($info=$sql->fetchAssoc($req)) $tab[$info['id']]=$info['pseudo']; // Tableaux des membres du clan
		
		$newLineUp=array();
		foreach($lineUp as $i=>$var) {
			$newLineUp[$i]=array();
			foreach($tab as $j=>$var2) {
				if(isset($post['lineup_'.$string->clean($i).'_membre_'.$j])) $newLineUp[$i][$j]=$var2;
			}
		}
		$newLineUp=serialize($newLineUp);
		$sql->query('UPDATE mod_membres_clans SET lineup="'.$site->clear4sql($newLineUp).'" WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"');

		$this->histo('Mise  jour des line up');

		header('location:mon-profil-clanlineup.html#mess6');
		exit();
	}
	
    /**
     * Envoi d'un message d'acceptation d'une postulation
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Id du postulant
     */ 
	function postulOk($id) {
		global $site,$string,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut accepter ou refuser de nouveaux joueurs dans le clan.');

		$info=$sql->fetchAssoc($sql->query('SELECT postulants FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
		$postulants=unserialize($info['postulants']); // Tableaux des postulants
		if(isset($postulants[$id])) {
			// Ajoute le membre dans le clan
			$sql->query('UPDATE mod_membres SET clan_id="'.$membres->infos('clan_id').'", clan_nom="'.$membres->infos('clan_nom').'" WHERE id="'.$id.'"');
			// Récupère le pseudo du postulant
			$pseudo=$postulants[$id]['membre_pseudo'];
			// Supprime la demande de recrutement
			unset($postulants[$id]);
			$postulants=serialize($postulants);
			$sql->query('UPDATE mod_membres_clans SET postulants="'.$site->clear4Sql($postulants).'" WHERE id="'.$membres->infos('clan_id').'"');
			
			/**
			 * Composition du message
			 */
			$tab1=array('[pseudo]','[clan]');
			$tab2=array($pseudo,$membres->infos('clan_nom'));
			$sujet=str_replace($tab1,$tab2,$site->config('membres_1_title'));
			$message=str_replace($tab1,$tab2,$site->config('membres_1_txt'));
			
			// Envoie du message au membre pour lui dire qu'il a t accept
			$membres->sendMessage($id,$pseudo,$sujet,$message);
		}
		$this->histo('Arrive de '.$pseudo);
		
		header('location:mon-profil-clanpostul.html#mess0');
		exit();
	}
	
    /**
     * Envoi d'un message de refus d'une postulation
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Id du postulant
     */ 
	function postulNok($id) {
		global $site,$string,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut accepter ou refuser de nouveaux joueurs dans le clan.');

		$info=$sql->fetchAssoc($sql->query('SELECT postulants FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
		$postulants=unserialize($info['postulants']); // Tableaux des postulants
		if(isset($postulants[$id])) {
			// Récupère le pseudo du postulant
			$pseudo=$postulants[$id]['membre_pseudo'];
			// Supprime la demande de recrutement
			unset($postulants[$id]);
			$postulants=serialize($postulants);
			$sql->query('UPDATE mod_membres_clans SET postulants="'.$site->clear4Sql($postulants).'" WHERE id="'.$membres->infos('clan_id').'"');
			
			/**
			 * Composition du message
			 */
			$tab1=array('[pseudo]','[clan]');
			$tab2=array($pseudo,$membres->infos('clan_nom'));
			$sujet=str_replace($tab1,$tab2,$site->config('membres_2_title'));
			$message=str_replace($tab1,$tab2,$site->config('membres_2_txt'));
			
			// Envoie du message au membre pour lui dire qu'il a t refus
			$membres->sendMessage($id,$pseudo,$sujet,$message);
		}
		
		header('location:mon-profil-clanpostul.html#mess1');
		exit();
	}
	
    /**
     * Envoi d'une demande de postulation
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Id du clan
	 * @param	Template	Template o afficher les ventuelles erreurs
     */ 
	function newJoin($clan,&$template) {
		global $site,$sql,$membres;
		# Vérification des informations saisies
		if(empty($clan)) $erreur=1;
		else if(!eregi("^([a-z0-9 ]+)$",$clan)) $erreur=3;
		else if(strlen($clan)<3 || strlen($clan)>100) $erreur=4;
		else {
			$res=$sql->query('SELECT postulants FROM mod_membres_clans WHERE nom="'.$clan.'"');
			if($sql->numRows($res)==0) $erreur=2;
		}
		if(isset($erreur)) {
			$template->setBlock('centredroite','erreur-clan'.$erreur);
			$template->parse('erreur-clan'.$erreur, true);
			return false;
		}
		
		$info=$sql->fetchAssoc($res);
		
		// Ajout de la demande dans le tableau
		$postulants=unserialize($info['postulants']);
		$postulants[$membres->infos('id')]=array('date'=>date('U'),'membre_pseudo'=>$membres->infos('pseudo'));
		$postulants=serialize($postulants);
		
		// Enregistrement des modifications
		$sql->query('UPDATE mod_membres_clans SET postulants="'.$site->clear4Sql($postulants).'" WHERE nom="'.$clan.'"');
		
		// Met  jour le profil du membre afin de savoir qu'il postule dj  un clan
		$sql->query('UPDATE mod_membres SET clan_nom="postul_'.$clan.'" WHERE id="'.$membres->infos('id').'"');
		
		header('location:mon-profil.html#mess7');
		exit();
	}
	
    /**
     * Annulation d'une demande de postulation
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function annulerJoin() {
		global $site,$sql,$membres;
		if(!ereg('postul_(.*)',$membres->infos('clan_nom'))) return true;
		$clan=str_replace('postul_','',$membres->infos('clan_nom'));
		$res=$sql->query('SELECT postulants FROM mod_membres_clans WHERE nom="'.$clan.'"');
		$info=$sql->fetchAssoc($res);
		
		$postulants=unserialize($info['postulants']);
		unset($postulants[$membres->infos('id')]);
		$postulants=serialize($postulants);
		// Enregistrement des modifications
		$sql->query('UPDATE mod_membres_clans SET postulants="'.$site->clear4Sql($postulants).'" WHERE nom="'.$clan.'"');
		
		// Met  jour le profil du membre afin de savoir qu'il ne postule  aucun clan
		$sql->query('UPDATE mod_membres SET clan_id=0, clan_nom="" WHERE id="'.$membres->infos('id').'"');
		
		header('location:mon-profil-clanjoin.html#mess8');
		exit();
	}

    /**
     * Quittage d'un clan
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function quit() {
		global $site,$sql,$membres;
		// Met  jour le profil du membre
		$sql->query('UPDATE mod_membres SET clan_id=0, clan_nom="" WHERE id="'.$membres->infos('id').'"');
		
		// Met  jour les lines up du clan
		$info=$sql->fetchAssoc($res=$sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"'));
		$lineUp=unserialize($info['lineup']);
		$newLineUp=array();
		foreach($lineUp as $i=>$var) {
			if(isset($var[$membres->infos('id')])) unset($var[$membres->infos('id')]);
			$newLineUp[$i]=$var;
		}
		$newLineUp=serialize($newLineUp);
		$sql->query('UPDATE mod_membres_clans SET lineup="'.$site->clear4sql($newLineUp).'" WHERE id="'.$membres->infos('clan_id').'"');
		
		$this->histo('Dpart de '.$membres->infos('pseudo'));
		
		header('location:mon-profil-clanjoin.html#mess9');
		exit();
	}

    /**
     * Renvoi d'un membre
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function renvoyer($id) {
		global $site,$sql,$membres;
		// Envoi d'un message afin d'annoncer au membre son renvoi du clan
		$info=$sql->fetchAssoc($res=$sql->query('SELECT id,pseudo,clan_nom FROM mod_membres WHERE id="'.$id.'"'));

		/**
		 * Composition du message
		 */
		$tab1=array('[pseudo]','[clan]');
		$tab2=array($info['pseudo'],$membres->infos('clan_nom'));
		
		$sujet=str_replace($tab1,$tab2,$site->config('membres_5_title'));
		$message=str_replace($tab1,$tab2,$site->config('membres_5_txt'));
			
		$membres->sendMessage($info['id'],$info['pseudo'],$sujet,$message);
		
		$this->histo('Renvoi de '.$info['pseudo']);
		
		// Met  jour le profil du membre
		$sql->query('UPDATE mod_membres SET clan_id=0, clan_nom="" WHERE id="'.$id.'"');
		// Met  jour les lines up du clan
		$info=$sql->fetchAssoc($res=$sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"'));
		$lineUp=unserialize($info['lineup']);
		$newLineUp=array();
		foreach($lineUp as $i=>$var) {
			if(isset($var[$membres->infos('id')])) unset($var[$membres->infos('id')]);
			$newLineUp[$i]=$var;
		}
		$newLineUp=serialize($newLineUp);
		$sql->query('UPDATE mod_membres_clans SET lineup="'.$site->clear4sql($newLineUp).'" WHERE id="'.$membres->infos('clan_id').'"');
		
		header('location:mon-profil.html#mess12');
		exit();
	}
	
    /**
     * Fermeture d'un clan
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function close() {
		global $site,$sql,$membres;
		// Envoi d'un message afin d'annoncer aux membres la fermeture de leur clan
		$res=$sql->query('SELECT id,pseudo,clan_nom FROM mod_membres WHERE clan_nom="'.$membres->infos('clan_nom').'" || clan_nom="postul_'.$membres->infos('clan_nom').'"');
		while($info=$sql->fetchAssoc($res)) {
		
			/**
			 * Composition du message
			 */
			$clan_nom=str_replace('postul_','',$membres->infos('clan_nom'));
			$tab1=array('[pseudo]','[clan]');
			$tab2=array($info['pseudo'],$clan_nom);
			
			$sujet1=str_replace($tab1,$tab2,$site->config('membres_4_title'));
			$message1=str_replace($tab1,$tab2,$site->config('membres_4_txt'));

			$sujet2=str_replace($tab1,$tab2,$site->config('membres_3_title'));
			$message2=str_replace($tab1,$tab2,$site->config('membres_3_txt'));
			
			if(ereg('postul_(.*)',$info['clan_nom'])) $membres->sendMessage($info['id'],$info['pseudo'],$sujet1,$message1);
			else $membres->sendMessage($info['id'],$info['pseudo'],$sujet2,$message2);
		}
		// Met  jour le profil des membre
		$sql->query('UPDATE mod_membres SET clan_id=0, clan_nom="" WHERE clan_nom="'.$membres->infos('clan_nom').'" || clan_nom="postul_'.$membres->infos('clan_nom').'"');
		// Supprime le clan
		$sql->query('DELETE FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'"');
		
		header('location:mon-profil.html#mess11');
		exit();
	}

/**
 * Fonctions admin.
 */

	/**
	 * Mise  jour des informations d'un clan
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations du clan
	 */
	function adminEdit($id,$post,&$template) {
		global $site,$sql,$membres;
		if($this->isClanLeader()==false) $site->error('Seul le leader peut modifier les informations du clan.');
		 
		# Vérification des informations saisies
		if(empty($post['nom'])) $erreur=1;
		else if(!eregi("^([a-z0-9 ]+)$",$post['nom'])) $erreur=3;
		else if(strlen($post['nom'])<3 || strlen($post['nom'])>100) $erreur=4;
		else {
			$res=$sql->query('SELECT nom FROM mod_membres_clans WHERE nom="'.$post['nom'].'" && id!="'.$id.'"');
			if($sql->numRows($res)>=1) $erreur=2;
		}
		
		if(!isset($erreur)) {
			$tag=str_replace(' ','&nbsp;',$site->clear4sql($post['tag']));
			if(trim($post['tag'])=='') $erreur=5;
			else if(strlen($post['tag'])>20) $erreur=6;
			else {
				$res=$sql->query('SELECT tag FROM mod_membres_clans WHERE tag="'.$tag.'" && id!="'.$id.'"');
				if($sql->numRows($res)>=1) $erreur=7;
			}
		}
		if($post['tagempl']!=1 && $post['tagempl']!=2) $post['tagempl']=1;
		$post['site']=substr(str_replace('http://','',$post['site']),0,255);
		$post['irc']=substr(eregi_replace('^#','',$post['irc']),0,100);
		$post['ircserver']=substr(eregi_replace('[^A-Z0-9\.]','',$post['ircserver']),0,100);
		
		if(isset($erreur)) {
			$template->setBlock('centredroite','erreur-clan'.$erreur);
			$template->parse('erreur-clan'.$erreur, true);
			return false;
		}
		
		/**
		 * Vérification du leader (si il existe et si il fait bien partit de ce clan)
		 */
		$req=$sql->query('SELECT id,pseudo,clan_nom FROM mod_membres WHERE id="'.(int)$post['leader'].'" && clan_id="'.$id.'"');
		// Il n'existe pas ou ne fait pas partit du clan, ce qui est mal (boulet qui a essay de corrompre le systme).
		// Dans ce cas on assigne l'id et le pseudo de l'utilisateur courant (le boulet en question).
		if($sql->numRows($req)==0) return false;
		$info=$sql->fetchAssoc($req);
		$clan_nom=$info['clan_nom'];
		$txt='leader_id="'.$site->clear4sql($info['id']).'",';
		$txt.='leader_pseudo="'.$site->clear4sql($info['pseudo']).'",';
		
		/**
		 * Bon, on va dire que c'est bon, on met tout ce joli monde dans la base
		 */
		$sql->query('
			UPDATE 
				mod_membres_clans 
			SET
				nom="'.$site->clear4sql($post['nom']).'",
				tag="'.$tag.'",
				tagempl="'.$site->clear4sql($post['tagempl']).'",
				'.$txt.'
				site="'.$site->clear4sql($post['site']).'",
				irc="'.$site->clear4sql($post['irc']).'",
				ircserver="'.$site->clear4sql($post['ircserver']).'"
			WHERE 
				id="'.$id.'"
		');
		
		/**
		 * Holala, si le nom du clan a chang, il faut mettre  jours les membres
		 */
		if($clan_nom!=$post['nom']) {
			$sql->query('
				UPDATE
					mod_membres
				SET 
					clan_nom="'.$site->clear4sql($post['nom']).'"
				WHERE 
					clan_id="'.$id.'" &&
					clan_nom="'.$clan_nom.'"
			');
		/**
		 * Et les postulants aussi
		 */
			$sql->query('
				UPDATE
					mod_membres
				SET 
					clan_nom="postul_'.$site->clear4sql($post['nom']).'"
				WHERE 
					clan_id="'.$id.'" &&
					clan_nom="postul_'.$clan_nom.'"
			');
		}
		
		$this->histo('Mise  jour des informations du clan par l\'administrateur');
		
		header('location:liste.html#mess0');
		exit();
	}
	
    /**
     * Changement de la bannière du clan
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Choix de banniere ou pas
	 * @param	array		URL vers la banniere
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
     */ 
	function adminChangeBan($id,$banniere,$url,&$sub_template) {
		global $site,$sql,$membres;
		$types=array('image/x-png','image/gif','image/pjpeg','image/jpeg','image/png');
		# Pas/Plus d'avatar
		if($banniere==1) {
			$sql->query('UPDATE mod_membres_clans SET banniere="" WHERE id="'.$id.'"');
		
			$this->histo('Suppression de la bannière du clan par l\'administrateur');
	
			header('location:liste.html#mess1');
			exit();
		# Récupération de bannière sur internet
		} else if($url!='http://') {
			$taille=$site->remoteInfos($url,'taille');
			if($taille===false) $erreur=1;
			else if($taille>($site->config('banniere_max_size')*1024)) $erreur=2;
			else if(!in_array($site->remoteInfos($url,'type'),$types)) $erreur=5;
			if(empty($erreur)) {
				$image=getimagesize($url);
				if($image[0]>$site->config('banniere_max_width') || $image[1]>$site->config('banniere_max_height')) $erreur=2;
			}
		} else return true;
		
		if(isset($erreur)) {
			$sub_template->setBlock('centredroite','erreur-banniere'.$erreur);
			$sub_template->parse('erreur-banniere'.$erreur, true);
			return false;
		}
		
		$sql->query('UPDATE mod_membres_clans SET banniere="'.$url.'" WHERE id="'.$id.'"');

		$this->histo('Mise  jour de la bannière du clan par l\'administrateur');

		header('location:liste.html#mess1');
		exit();
	}

    /**
     * Suppression d'un clan
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function supprimerClans($id,$nom) {
		global $site,$sql,$membres;
		// Envoi d'un message afin d'annoncer aux membres la suppression de leur clan
		$res=$sql->query('SELECT id,pseudo,clan_nom FROM mod_membres WHERE clan_nom="'.$nom.'" || clan_nom="postul_'.$nom.'"');
		while($info=$sql->fetchAssoc($res)) {
		
			/**
			 * Composition du message
			 */
			$clan_nom=str_replace('postul_','',$nom);
			$tab1=array('[pseudo]','[clan]');
			$tab2=array($info['pseudo'],$clan_nom);
			
			$sujet1=str_replace($tab1,$tab2,$site->config('membres_6_title'));
			$message1=str_replace($tab1,$tab2,$site->config('membres_6_txt'));

			$sujet2=str_replace($tab1,$tab2,$site->config('membres_7_title'));
			$message2=str_replace($tab1,$tab2,$site->config('membres_7_txt'));
			
			if(ereg('postul_(.*)',$info['clan_nom'])) $membres->sendMessage($info['id'],$info['pseudo'],$sujet1,$message1);
			else $membres->sendMessage($info['id'],$info['pseudo'],$sujet2,$message2);
		}
		// Met  jour le profil des membre
		$sql->query('UPDATE mod_membres SET clan_id=0, clan_nom="" WHERE clan_nom="'.$nom.'" || clan_nom="postul_'.$nom.'"');
		// Supprime le clan
		$sql->query('DELETE FROM mod_membres_clans WHERE id="'.$id.'"');
		
		header('location:liste.html#mess2');
		exit();
	}

	function configClans($clan) {
		global $sql;
		$sql->query('UPDATE config SET value="'.$clan.'" WHERE name="clan_default"');
		header('location:configuration.html#mess3');
	}

}
?>