<?php
/**
 * Classe de gestion des membres et de leurs accès.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class membres {

	/**
	 * Constructeur de la classe Membres.
	 * - Récupère les infos du membre
	 * - Met  jour les sujets non-lu du forum
	 * - Met  jour la date de sa dernire visite
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function membres() {
		global $site,$sql;
		$this->getInfos();
		$this->liveUpdate();
		/*if(isset($_COOKIE['forum_unread'])) {
			$unread=$unread1=unserialize(stripslashes($_COOKIE['forum_unread']));
			$req=$sql->query('SELECT topic_id,id FROM mod_forum_posts WHERE post_date>"'.$this->infos('last_visit').'" ORDER BY post_date DESC');
			while($info=$sql->fetchAssoc($req)) $unread[$info['topic_id']]=$info['id'];
			$unread=array_chunk($unread, 100);
			if(isset($unread[0]) && $unread1!=$unread[0] && isset($_COOKIE['idm']) && isset($_COOKIE['redir'])) {
				setcookie('forum_unread',serialize($unread[0]),time()+63072000,'/');
				setcookie('redir','ouai',time()+63072000,'/');
				header("Cache-control: private, no-cache");
				header('location:'.$_SERVER['REQUEST_URI']);
				exit();
			}
		} else setcookie('forum_unread',serialize(array()),time()+63072000,'/');*/
		setcookie('redir','',time()+63072000,'/');
		$sql->query('UPDATE mod_membres SET last_visit="'.date('U').'" WHERE id="'.$this->infos('id').'"');
		$theme=$this->infos('theme');
		if(!empty($theme)) $site->config('theme',$theme);
	}
	
	/**
	 * Identifie l'utilisateur
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	Login de l'utilisateur
     * @param	string	Mot de passe de l'utilisateur ({@link http://fr3.php.net/md5 cryptage MD5})
	 */
	function ident($pseudo,$pass) {
		global $sql;
		$pseudo=eregi_replace("[^A-Z0-9]",'',$pseudo); // Suppressions des caractères indsirables
		$req=$sql->query('SELECT id,pseudo,pass,act FROM mod_membres WHERE pseudo="'.$pseudo.'"');
		if($sql->numRows()==0) $this->error(0);
		else {
			$info=$sql->fetchAssoc($req);
			if($info['pass']!=$pass) $this->error(1);
			else {
				if($info['act']==0) $this->error(2);
				else {
					if(isset($_POST['auto'])) $this->addCookies($info['id'],$info['pseudo'],$info['pass']);
					$this->addSession($info['id'],$info['pseudo'],$info['pass']);
				}
			}
		}
		if(!isset($this->error)) {
			if(!isset($_GET['activer'])) {
				header("Cache-control: private, no-cache");
				header('location:'.$_SERVER['REQUEST_URI']);
			} else if(strlen($_GET['activer'])==32) header('location:activer-ok.html');
			exit();
		}
	}
	
	/**
	 * Affiche les messages d'erreur lorqu'une identification choue
	 *
	 * @todo voir celle du forum si elle est pas mieu
	 * @todo non implmente dans le forum d'identification
	 * @author	Yannick Croissant
	 * @access	public
     * @param	integer	Id de l'erreur
	 */
	function error($index) {
		$error_tab=array('Utilisateur inconnu','Mot de passe invalide','Compte non activ');
		(isset($this->error)?$this->error.=$error_tab[$index]."\r\n":$this->error=$error_tab[$index]."\r\n");
	}
	
	/**
	 * Retourne l'erreur
	 *
	 * @todo h ! mais c'est naze !
	 * @author	Yannick Croissant
	 * @access	public
     * @return	string	Erreur
	 */
	function showError() {
		return $this->error;
	}
	
	/**
	 * Création des cookies d'identification
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	integer	Id de l'utilisateur
     * @param	string	Login de l'utilisateur
     * @param	string	Mot de passe de l'utilisateur ({@link http://fr3.php.net/md5 cryptage MD5})
	 */
	function addCookies($id,$pseudo,$pass) {
		setcookie('idm',$id,time()+63072000,'/');
		setcookie('pseudo',$pseudo,time()+63072000,'/');
		setcookie('pass',$pass,time()+63072000,'/');
	}
	
	/**
	 * Création des variables de session d'identification
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	integer	Id de l'utilisateur
     * @param	string	Login de l'utilisateur
     * @param	string	Mot de passe de l'utilisateur ({@link http://fr3.php.net/md5 cryptage MD5})
     * @return	string	Texte sans le BBCode
	 */
	function addSession($id,$pseudo,$pass) {
		$_SESSION["idm"]=$id;
		$_SESSION["pseudo"]=$pseudo;
		$_SESSION["pass"]=$pass;
	}
	
	/**
	 * Loggue l'utilisateur et places ses infos et accès dans un tableau
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function getInfos() {
		global $sql,$string;
		if(!isset($_SESSION['idm']) && !isset($_COOKIE['idm']) && isset($_POST['loginpseudo']) && isset($_POST['loginpass'])) $this->ident($_POST['loginpseudo'],md5($_POST['loginpass']));
		if(!isset($_SESSION['idm']) && isset($_COOKIE['idm']) && !empty($_COOKIE['idm'])) {
			$_SESSION['idm']=$string->clean($_COOKIE['idm'],'int');
			$_SESSION['pseudo']=$string->clean($_COOKIE['pseudo'],'alnum');
			$_SESSION['pass']=$string->clean($_COOKIE['pass'],'alnum');
			header("Cache-control: private, no-cache");
			header('location:'.$_SERVER['REQUEST_URI']);
			exit();
		}
		if(isset($_SESSION['idm'],$_SESSION['pseudo'],$_SESSION['pass'])) {
			$res=$sql->query('SELECT mod_membres.*,groupes.acces gacces FROM mod_membres,groupes WHERE groupes.id=groupe && mod_membres.id="'.$_SESSION['idm'].'" && pseudo="'.$_SESSION['pseudo'].'" && pass="'.$_SESSION['pass'].'" && act=1');
			if($sql->numRows($res)==0) $this->logout();
			$this->infos=$sql->fetchAssoc($res);
			$this->infos['acces']=str_replace('||','|',$this->infos['acces'].$this->infos['gacces']);
		} else {
			$this->infos=$sql->fetchAssoc($sql->query('SELECT acces FROM groupes WHERE id=2'));
			$this->infos['acces']=$this->infos['acces'];
		}
	
		$req=$sql->query('SELECT * FROM acces ORDER BY id');
		while ($info=$sql->fetchAssoc($req)) $this->acces[$info['name']]=$info['id'];
	}	


	/**
	 * Retourne l'id d'un utilisateur en fonction de son pseudo, retourne false si il n'existe pas
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	mixed
	 */
	function getId($pseudo) {
		global $sql;
		$res=$sql->query('SELECT id FROM mod_membres WHERE pseudo="'.$pseudo.'"');
		if($sql->numRows($res)==0) return false;
		$info=$sql->fetchAssoc($res);
		return $info['id'];
	}	


	/**
	 * Retourne le pseudo d'un utilisateur en fonction de son id, retourne false si il n'existe pas
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	mixed
	 */
	function getPseudo($id) {
		global $sql;
		$res=$sql->query('SELECT pseudo FROM mod_membres WHERE id="'.$id.'"');
		if($sql->numRows($res)==0) return false;
		$info=$sql->fetchAssoc($res);
		return $info['pseudo'];
	}	

	/**
	 * Retourne ou modifie les informations de l'utilisateur courant
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	Nom de l'information
     * @param	string	Nouvelle valeur de l'information (si vide la valeur courante est retourne)
     * @return	mixed
	 */
	function infos($index,$set='') {
		if(!empty($set)) $this->infos[$index]=$set;
		else if(isset($this->infos[$index])) return $this->infos[$index];
		else return false;
	}
	
	/**
	 * Vrifie les accès de l'utilisateur courant
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	Nom de la ressource dont on doit vérifier l'accès
     * @return	boolean
	 */
	function verifAcces($nom) {
		if(!isset($this->infos) || !isset($nom) || !isset($this->acces[$nom])) return false;
		else if(ereg('\|'.$this->acces[$nom].'\|',$this->infos['acces'])) return true;
		else return false;
	}
	
	/**
	 * Ajoute des accès  la chaine $acces
	 *
	 * Exemple d'utilisation:
	 * <code>
	 * <?php
	 * $acces='|1|2|5|'; // accès courant
	 * $toadd='|3|4|'; // accès  ajouter
	 * $newacces=$membres->addAcces($acces,$toadd);
	 * echo $newacces; // Affiche '|1|2|3|4|5|'
	 * ?>
	 * </code>
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	accès courant
     * @param	string	accès  ajouter
     * @return	string	Nouveaux accès
	 */
	function addAcces($acces,$toadd) {
		$acces=$acces.$toadd;
		$acces=explode('|',$acces);
		$retour=array();
		foreach($acces as $a) if(!empty($a)) array_push($retour,$a);
		$acces=array_unique($retour);
		sort($acces);
		return '|'.implode('|',$acces).'|';
	}

	/**
	 * Supprime des accès  la chaine $acces
	 *
	 * Exemple d'utilisation:
	 * <code>
	 * <?php
	 * $acces='|1|2|3|4|5|'; // accès courant
	 * $toremove='|3|4|'; // accès  supprimer
	 * $newacces=$membres->removeAcces($acces,$toremove);
	 * echo $newacces; // Affiche '|1|2|5|'
	 * ?>
	 * </code>
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	accès courant
     * @param	string	accès  supprimer
     * @return	string	Nouveaux accès
	 */
	function removeAcces($acces,$toremove) {
		if(isset($this->test)) $this->test++;
		else $this->test=1;
		$toremove=explode('|',$toremove);
		foreach($toremove as $r) $acces=ereg_replace('\|'.$r.'\|','|',$acces);
		$acces=explode('|',$acces);
		$retour=array();
		foreach($acces as $a) if(!empty($a)) array_push($retour,$a);
		$acces=array_unique($retour);
		sort($acces);
		return '|'.implode('|',$acces).'|';
	}
	
	/**
	 * Inscrit un membre et lui envoie un e-mail afin de valider son compte
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	E-mail
     * @param	string	Pseudo
     * @param	string	Si le rglement a t accept
     * @param	boolean	Mode de Création du compte (par dfaut true)
	 */
	function inscription($mail,$pseudo,$reglement,$auto=true,$pass='') {
		global $template,$sql,$site;
		$template->setVar('insEmail',$mail);
		$template->setVar('insPseudo',$pseudo);
		# Vérification des informations saisies
		if(empty($pseudo)) $erreur=1;
		else if(!eregi("^([a-z0-9]+)$",$pseudo)) $erreur=3;
		else if(strlen($pseudo)<3 || strlen($pseudo)>20) $erreur=4;
		else if($sql->numRows($sql->query('SELECT pseudo FROM mod_membres WHERE pseudo="'.$pseudo.'"'))>0) $erreur=2;
		if(isset($erreur)) {
			$template->setBlock('centre','erreur-pseudo'.$erreur);
			$template->parse('erreur-pseudo'.$erreur, true);
			return false;
		}
		if(empty($mail)) $erreur=1;
		else if(!eregi('^([_\.0-9a-z-]+)([\+]{0,1})([_\.0-9a-z-]+)@([0-9a-z-]+)\.([a-z]{2,4})$',$mail)) $erreur=3;
		else if($sql->numRows($sql->query('SELECT mail FROM mod_membres WHERE mail="'.$mail.'"'))>0) $erreur=2;
		if(isset($erreur)) {
			$template->setBlock('centre','erreur-mail'.$erreur);
			$template->parse('erreur-mail'.$erreur, true);
			return false;
		}
		if($reglement!='ok') {
			$template->setBlock('centre','erreur-reglement');
			$template->parse('erreur-reglement', true);
			return false;
		}
		# The End
		if($auto==true) $pass=substr(md5(uniqid(rand())),0,6);
		if($auto==true) $act_key=md5(uniqid(rand()));
		else $act_key='0';
		
		/**
		 * Vrifie si un gravatar correspond  cet e-mail
		 */
		if(!$this->gravatarExist($mail)) $avatar='0';
		else $avatar='gravatar';
		
		$sql->query('
		INSERT INTO 
			mod_membres (pseudo,pass,mail,natio,date_ins,last_visit,act_key,groupe,avatar) 
		VALUES (
			"'.addslashes($pseudo).'",
			"'.md5($pass).'",
			"'.addslashes($mail).'",
			"fr",
			"'.date('U').'",
			"'.date('U').'",
			"'.$act_key.'",
			"3",
			"'.$avatar.'"
		)');
		
		if($auto==true) {
			/**
			 * Composition de l'email
			 */
			$lien=$site->getRoot().'membres/activer-'.$act_key.'.html';
			$lien=ereg_replace("([^:])//","\\1/",$lien);
			
			$tab1=array('[pseudo]','[pass]','[activation]');
			$tab2=array($pseudo,$pass,$lien);
			$message=str_replace($tab1,$tab2,$site->config('membres_mail_txt'));
	
			mail($mail,$site->config('membres_mail_title'),$message,
				"From: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
				."Reply-To: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
				."X-Mailer: PHP/".phpversion());
			header('location:inscriptionfinish.html');
		}
	}

	/**
	 * Envoi  un membre un e-mail contenant un lien lui permettant de gnrer un nouveau mot de passe
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function recupMDP() {
		global $template,$sql,$site;
		$template->setVar('insEmail',$_POST['email']);
		# Vérification des informations saisies
		if(empty($_POST['email'])) $erreur=1;
		else if(!eregi('^([_\.0-9a-z-]+)([\+]{0,1})([_\.0-9a-z-]+)@([0-9a-z-]+)\.([a-z]{2,4})$',$_POST['email'])) $erreur=3;
		else if($sql->numRows($sql->query('SELECT mail FROM mod_membres WHERE mail="'.$_POST['email'].'" && act=1'))==0) $erreur=2;
		if(isset($erreur)) {
			$template->setBlock('centre','erreur-mail'.$erreur);
			$template->parse('erreur-mail'.$erreur, true);
			return false;
		}
		# The End
		$act_key=md5(uniqid(rand()));
		
		$sql->query('
		UPDATE
			mod_membres
		SET 
			act_key="'.$act_key.'"
		WHERE
			mail="'.$_POST['email'].'"
		');
		
		/**
		 * Composition de l'email
		 */
		$lien=$site->getRoot().'membres/newmdp-'.$act_key.'.html';
		$lien=ereg_replace("([^:])//","\\1/",$lien);

		$tab1=array('[activation]','[ip]');
		$tab2=array($lien,$_SERVER['REMOTE_ADDR']);
		$message=str_replace($tab1,$tab2,$site->config('membres_mail_recupmdp_txt'));

		mail($_POST['email'],$site->config('membres_mail_recupmdp_title'),$message,
     		"From: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
    		."Reply-To: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
   			."X-Mailer: PHP/".phpversion());
		header('location:recupmdpfinish.html');
	}
	
	/**
	 * Active un compte
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @return	boolean
	 */
	function activer() {
		global $sql;
		if($this->infos('id')) return true;
		$info=$sql->fetchAssoc($sql->query('SELECT pseudo,pass FROM mod_membres WHERE act_key="'.$_GET['activer'].'" && act="0"'));
		$sql->query('UPDATE mod_membres SET act_key="0",act="1" WHERE act_key="'.$_GET['activer'].'" && act="0"');
		if($sql->affectedRows()==0) return false;
		$this->ident($info['pseudo'],$info['pass']);
		return true;
	}

	/**
	 * Change le mot de passe
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @return	boolean
	 */
	function newMDP($newmdp,$pass,$confpass) {
		global $template,$sql,$site;
		$template->setVar('newPass',$pass);
		if(empty($pass)) $erreur=1;
		else if($pass!=$confpass) $erreur=2;
		else if(strlen($pass)<6) $erreur=3;
		if(isset($erreur)) {
			$template->setBlock('centre','erreur-pass'.$erreur);
			$template->parse('erreur-pass'.$erreur, true);
			return false;
		}
		$info=$sql->fetchAssoc($sql->query('SELECT pseudo,mail FROM mod_membres WHERE act_key="'.$newmdp.'" && act="1"'));
		$sql->query('UPDATE mod_membres SET act_key="0",pass="'.md5($pass).'" WHERE act_key="'.$newmdp.'" && act="1"');
		header("Cache-control: private, no-cache");
		if($sql->affectedRows()==0) {
			header('location:'.$site->getRoot());
			return false;
		}
		/**
		 * Composition de l'email
		 */
		$tab1=array('[pseudo]','[pass]');
		$tab2=array($info['pseudo'],$pass);
		$message=str_replace($tab1,$tab2,$site->config('membres_mail_newmdp_txt'));

		mail($info['mail'],$site->config('membres_mail_newmdp_title'),$message,
     		"From: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
    		."Reply-To: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
   			."X-Mailer: PHP/".phpversion());
		header('location:newmdpfinish.html');
	}


	/**
	 * Change le mot de passe
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @return	boolean
	 */
	function changeMDP($pass,$confpass,&$template) {
		global $sql,$site;
		$template->setVar('newPass',$pass);
		if(empty($pass)) $erreur=1;
		else if($pass!=$confpass) $erreur=2;
		else if(strlen($pass)<6) $erreur=3;
		if(isset($erreur)) {
			$template->setBlock('centredroite','erreur-pass'.$erreur);
			$template->parse('erreur-pass'.$erreur, true);
			return false;
		}
		$sql->query('UPDATE mod_membres SET pass="'.md5($pass).'" WHERE id='.$this->infos('id'));
		
		if(isset($_COOKIE['pass'])) $this->addCookies($this->infos('id'),$this->infos('pseudo'),md5($pass));
		$this->addSession($this->infos('id'),$this->infos('pseudo'),md5($pass));
		/**
		 * Composition de l'email
		 */
		$tab1=array('[pseudo]','[pass]');
		$tab2=array($this->infos('pseudo'),$pass);
		$message=str_replace($tab1,$tab2,$site->config('membres_mail_newmdp_txt'));

		mail($this->infos('mail'),$site->config('membres_mail_newmdp_title'),$message,
     		"From: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
    		."Reply-To: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
   			."X-Mailer: PHP/".phpversion());
		header('location:mon-profil.html');
	}


	/**
	 * Supprime tous les cookies crs par le site
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function clearCookies() {
		if (is_array($_COOKIE)) {
			while (list($key, $val) = each($_COOKIE)) {
				setcookie($key,'', 0,'/');
			}
		}
	}


	/**
	 * Dconnexion d'un utilisateur (destruction de la session et des cookies)
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function logout() {
		session_destroy();
		if (is_array($_COOKIE)) {
			while (list($key, $val) = each($_COOKIE)) {
				setcookie($key,'', 0,'/');
			}
		}
		if(isset($_SERVER['HTTP_REFERER'])) header('location:'.$_SERVER['HTTP_REFERER']);
		else header('location:http://'.$_SERVER['SERVER_NAME']);
	}
	
    /**
     * Mise  jour des informations de l'utilisateur
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations de l'utilisateur
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
     */ 
	function editInfos($post,&$sub_template) {
		global $sql,$site;
		# Vérification des informations saisies (seul l'e-mail est obligatoire)
		if(empty($post['mail'])) $erreur=1;
		else if(!eregi('^([_\.0-9a-z-]+)([\+]{0,1})([_\.0-9a-z-]+)@([0-9a-z-]+)\.([a-z]{2,4})$',$post['mail'])) $erreur=3;
		else if($sql->numRows($sql->query('SELECT mail FROM mod_membres WHERE mail="'.$post['mail'].'" && id!="'.$this->infos('id').'"'))>0) $erreur=2;
		if(isset($erreur)) {
			$sub_template->setBlock('centredroite','erreur-mail'.$erreur);
			$sub_template->parse('erreur-mail'.$erreur, true);
		}
		if(!eregi('^([_\.0-9a-z-]+)([\+]{0,1})([_\.0-9a-z-]+)@([0-9a-z-]+)\.([a-z]{2,4})$',$post['msn']) && !empty($post['msn'])) {
			$sub_template->setBlock('centredroite','erreur-msn');
			$sub_template->parse('erreur-msn', true);
			$erreur=4;
		}
		if(!eregi('^([_\.0-9a-z-]+)([\+]{0,1})([_\.0-9a-z-]+)@([0-9a-z-]+)\.([a-z]{2,4})$',$post['gtalk']) && !empty($post['gtalk'])) {
			$sub_template->setBlock('centredroite','erreur-gtalk');
			$sub_template->parse('erreur-gtalk', true);
			$erreur=5;
		}
		if(isset($erreur)) return false;
		
		/**
		 * Si l'e-mail change et que l'on utilise un gravatar alors on vérifie si il en existe un avec la nouvelle adresse e-mail
		 */
		if($post['mail']!=$this->infos('mail') && ($this->infos('avatar')=='gravatar' || $this->infos('avatar')==0)) {
			if(!$this->gravatarExist($post['mail'])) $avatar='avatar="0",';
			else $avatar='avatar="gravatar",';
		} else $avatar='';
		
		$post['www']=substr(str_replace('http://','',$post['www']),0,255);
		
		$sql->query('
		UPDATE 
			mod_membres 
		SET 
			nom="'.$site->clear4Sql($post['nom']).'",
			prenom="'.$site->clear4Sql($post['prenom']).'",
			'.$avatar.'
			date_nes="'.mktime(0,0,0,$post['mois'],$post['jour'],$post['annee']).'",
			natio="'.$site->clear4Sql($post['natio']).'",
			www="'.$site->clear4Sql($post['www']).'",
			mail="'.$site->clear4Sql($post['mail']).'",
			msn="'.$site->clear4Sql($post['msn']).'",
			icq="'.$site->clear4Sql($post['icq']).'",
			yahoo="'.$site->clear4Sql($post['yahoo']).'",
			aim="'.$site->clear4Sql($post['aim']).'",
			skype="'.$site->clear4Sql($post['skype']).'",
			xfire="'.$site->clear4Sql($post['xfire']).'",
			gtalk="'.$site->clear4Sql($post['gtalk']).'",
			hard_1="'.$site->clear4Sql($post['hard_1']).'",
			hard_2="'.$site->clear4Sql($post['hard_2']).'",
			hard_3="'.$site->clear4Sql($post['hard_3']).'",
			hard_4="'.$site->clear4Sql($post['hard_4']).'",
			hard_5="'.$site->clear4Sql($post['hard_5']).'",
			hard_6="'.$site->clear4Sql($post['hard_6']).'",
			hard_7="'.$site->clear4Sql($post['hard_7']).'"
		WHERE id="'.$this->infos('id').'"
		');
		header("Cache-control: private, no-cache");
		header('location:'.$_SERVER['REQUEST_URI']);
	}
	
    /**
     * Changement de l'avatar de l'utilisateur
	 * Ne rien toucher, cette fonction est trs caline :)
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations du nouvel avatar
	 * @param	array		Informations sur le fichier envoy
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
     */ 
	function changeAvatar($avatar,$url,$file,&$sub_template) {
		global $site,$sql;
		$types=array('image/x-png','image/gif','image/pjpeg','image/jpeg','image/png');
		# Pas/Plus d'avatar
		if($avatar==1) {
			$this->delAvatar();
			$sql->query('UPDATE mod_membres SET avatar="0" WHERE id="'.$this->infos('id').'"');
			header("Cache-control: private, no-cache");
			header('location:'.$_SERVER['REQUEST_URI']);
			exit();
		# Gravatar (www.gravatar.com)
		} else if($avatar==3) {
			$this->delAvatar();
			$sql->query('UPDATE mod_membres SET avatar="gravatar" WHERE id="'.$this->infos('id').'"');
			header("Cache-control: private, no-cache");
			header('location:'.$_SERVER['REQUEST_URI']);
			exit();
		# Upload d'avatar
		} else if(isset($file['upload']['name']) && !empty($file['upload']['name'])) {
		
			$extension = strtolower(substr($file['upload']['name'], strrpos($file['upload']['name'], ".")));
			if(!in_array($file['upload']['type'],$types)) $erreur=3; 																		// Vérification du type
			else if($file['upload']['size']>($site->config('avatar_max_size')*1024)) $erreur=4;											// Vérification de la taille
			else if(move_uploaded_file($file['upload']['tmp_name'],'modules/membres/avatars/'.$this->infos('id').'-tmp'.$extension)) {		// Déplacement dans le dossier avatar sous un nom temporaire
				$image=getimagesize('modules/membres/avatars/'.$this->infos('id').'-tmp'.$extension);										// Récupération des dimensions
				if($image[0]>$site->config('avatar_max_height') || $image[1]>$site->config('avatar_max_width')) {							// Vérification des dimensions
					$erreur=4;
					$this->delAvatar('tmp');																								// Suppression de l'image temporaire
				} else {
					$this->delAvatar('notmp');																								// Suppression de l'avatar prcdent
					rename(																													// Renomage de l'image
						'modules/membres/avatars/'.$this->infos('id').'-tmp'.$extension,
						'modules/membres/avatars/'.$this->infos('id').$extension
					);
				}
			}
		# Récupération d'avatar sur internet
		} else if($url!='http://') {
			$taille=$site->remoteInfos($url,'taille');																				// Récupération de la taille
			if($taille===false) $erreur=1;																									// Fichier introuvable (ou on le considérée comme tel)
			else if($taille>($site->config('avatar_max_size')*1024)) $erreur=2;																							// Vérification de la taille
			else if(!in_array($site->remoteInfos($url,'type'),$types)) $erreur=5;													// Vérification du type
			if(empty($erreur)) {
				$image=getimagesize($url);																							// Récupération des dimensions
				if($image[0]>$site->config('avatar_max_height') || $image[1]>$site->config('avatar_max_width')) $erreur=2;																					// Vérification des dimensions
				else {
					# Récupération de l'image
					$fp=fopen($url,'r');
					$image='';
					for($i=ceil($taille/4096);$i>0;$i--) $image.=fread($fp,4096);
					fclose($fp);
					# End
					# Enregistrement de l'image
					$url=parse_url($url);
					$extension = strtolower(substr($url['path'], strrpos($url['path'], ".")));
					$fp=fopen('modules/membres/avatars/'.$this->infos('id').$extension,'w');
					fwrite($fp,$image);
					fclose($fp);
					# End
				}
			}
		} else return true;
		
		if(isset($erreur)) {
			$sub_template->setBlock('centredroite','erreur-avatar'.$erreur);
			$sub_template->parse('erreur-avatar'.$erreur, true);
			return false;
		}
		
		$sql->query('UPDATE mod_membres SET avatar="'.$this->infos('id').$extension.'" WHERE id="'.$this->infos('id').'"');
		header("Cache-control: private, no-cache");
		header('location:'.$_SERVER['REQUEST_URI']);
		exit();
	}
	
    /**
     * Supprime l'avatar de l'utilisateur
	 * Ne rien toucher, cette fonction est trs calinne aussi :)
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Avatar  supprimer (all par dfaut)
	 * @param	integer	Id du membre (membre courant par dfaut)
	 * @return	boolean
     */ 
	function delAvatar($del='all',$id=0) {
		if($id==0) $id=$this->infos('id');
		$File='modules/membres/avatars/'.$id;
		$fichiers=array($File.'.gif',$File.'.jpg',$File.'.png');
		$fichiersTmp=array($File.'-tmp.gif',$File.'-tmp.jpg',$File.'-tmp.png');
		
		if($del=='all') $fichiers=array_merge($fichiers,$fichiersTmp);
		else if($del=='tmp') $fichiers=$fichiersTmp;
		
		foreach($fichiers as $i=>$var) if(file_exists($var)) unlink($var);
		return true;
	}
	
    /**
     * Mise  jour de la signature de l'utilisateur
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations de la nouvelle signature
	 * @return	boolean
     */ 
	function editSignature($post) {
		global $sql,$site;
		$signature=addslashes(strip_tags($post['message']));
		$sql->query('UPDATE mod_membres SET signature="'.$signature.'" WHERE id="'.$this->infos('id').'"');
		header("Cache-control: private, no-cache");
		header('location:'.$_SERVER['REQUEST_URI']);
		exit();
	}
	
	/**
	 * Retourne le chemin de l'avatar du membre
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Avatar du membre
	 * @param	string		Email du membre
	 * @param	integer		Taille du gravatar dsire
	 * @return	string
	 */
	function getAvatar($avatar='',$mail='',$gravatarSize=80) {
		if(!empty($avatar) && !empty($mail)) {
			if($avatar!='gravatar') return 'modules/membres/avatars/'.$avatar;
			else return 'http://www.gravatar.com/avatar.php?gravatar_id='.md5($mail).'&amp;size='.$gravatarSize;
		} else if(empty($avatar) && !empty($mail)) return 'templates/'.THEME.'/images/membres/noavatar.gif';
		else {
			if($this->infos('avatar')!='gravatar') return 'modules/membres/avatars/'.$this->infos('avatar');
			else return 'http://www.gravatar.com/avatar.php?gravatar_id='.md5($this->infos('mail')).'&amp;size='.$gravatarSize;
		}
	}
	
	/**
	 * Vrifie si le membre possde un gravatar ou non
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Email du membre
	 * @return	boolean
	 */
	function gravatarExist($mail='') {
		return false;	// Down trop souvent
		if(empty($mail)) $mail=$this->infos('mail');
		list($width,$height)=getimagesize("http://www.gravatar.com/avatar.php?gravatar_id=".md5($mail)."&size=6");
		if($width!=1 && $height!=1) return true;
		else return false;
	}
	
	/**
	 * Retourne l'age du membre  partir de sa date de naissance
	 * Si aucune date de naissance n'est fournie alors on utilise celle du membre courant
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Date de naissance du membre
	 * @return	string
	 */
	function getAge($nes='') {
		if(!empty($nes)) return floor((date('Ymd')-date('Ymd',$nes))/10000).' ans';
		else return floor((date('Ymd')-date('Ymd',$this->infos('date_nes')))/10000).' ans';
	}
	
    /**
     * Gnration d'image afin d'empcher les spambots de rcuprer le adresses e-mails
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Texte  convertir en image
	 * @return	string		Lien vers l'image
     */ 
	function antiBot($txt) {
		if(!file_exists('modules/membres/mail/'.md5($txt).'.png')) {
			// select the font. '2' is a builtin kind,
			// with each letter about 6px wide
			$font = 2;
			$width = strlen($txt) * 6; // 6px wide per letter
			$height = 15; // this font size needs about this height
			
			// create the GD image
			$im = imagecreate($width, $height);
			
			// allocate the background colour. The first call
			// to this function sets the background
			$white = imagecolorallocate($im, 255, 255, 255);
			
			// the text colour.. black
			$textcolor = imagecolorallocate($im, 0, 0, 0);
			
			// write the email address to the image
			imagestring($im, $font, 0, 0, $txt, $textcolor);
			
			// output the content-type header and the image
			//header('Content-type: image/png');
			imagepng($im,'modules/membres/mail/'.md5($txt).'.png');
		}
		return 'modules/membres/mail/'.md5($txt).'.png';
	}
	
    /**
     * Recherche du forum o le membre est le plus actif
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		ID du membre concern
	 * @return	array
     */ 
	function moreAct($id) {
		global $sql;
		$info=$sql->fetchAssoc($sql->query('SELECT COUNT(mod_forum_posts.id) AS somme,forum_id,titre FROM mod_forum_posts,mod_forum_forums WHERE auteur_id="'.$id.'" && forum_id=mod_forum_forums.id GROUP BY forum_id ORDER BY somme DESC LIMIT 0,1'));
		return array($info['forum_id'],$info['titre'],$info['somme']);
	}
	
    /**
     * Recherche la date de la dernire action du membre
     *
	 * @todo Placer ceci dans l'enregistrement du membre
	 * @todo Afin de faciliter les recherches lorsque qu'il y aura mass modules
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		ID du membre concern
	 * @return	string
     */ 
	function lastAct($id) {
		global $sql;
		$info=$sql->fetchAssoc($sql->query('SELECT post_date FROM mod_forum_posts WHERE auteur_id="'.$id.'" ORDER BY post_date DESC LIMIT 0,1'));
		return $info['post_date'];
	}
	
    /**
     * Retourne l'état du membre (en ligne ou pas)
	 * Si la dernire action (changement de page) du membre est iffrieur
	 *  2min alors il est considéré comme en ligne.
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Timestamp de la dernire action du membre
	 * @return	boolean
     */ 
	function statut($lastVis) {
		global $sql;
		$ecart=(date('U')-$lastVis)/60;
		if($ecart<5) return true;
		else return false;
	}

    /**
     * Envoi un message privé à un membre
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Id du membre
	 * @param	string		Pseudo du membre
	 * @param	string		Sujet du message
	 * @param	string		Texte du message
	 * @return	boolean
     */ 
	function sendMessage($to_id,$to_name,$sujet,$message,$auto=0) {
		global $site,$sql;
		if($auto==1) {
			$from_id=0;
			$from_pseudo='Administrateur';
		} else {
			$from_id=$this->infos('id');
			$from_pseudo=$this->infos('pseudo');
		}
		# Insertion du message dans la base		
		$sql->query('
			INSERT INTO 
				mod_messages (to_id,to_name,from_id,from_name,date,sujet,message,etat)
			VALUES (
				"'.$to_id.'",
				"'.$to_name.'",
				"'.$from_id.'",
				"'.$from_pseudo.'",
				"'.date('U').'",
				"'.$site->clear4sql($sujet).'",
				"'.$site->clear4sql($message).'",
				"0"
			)
		');
		return true;
	}
	
    /**
     * Envoi un message privé à un membre par l'intermdiaire de la messagerie
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Id du membre
	 * @param	string		Pseudo du membre
	 * @param	string		Sujet du message
	 * @param	string		Texte du message
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
	 * @return	boolean
     */ 
	function makeMessage($to_name,$sujet,$message,&$template) {
		global $site,$sql;
		# Vérification des informations saisies
		if(empty($to_name)) $erreur=1;
		else if(!eregi("^([a-z0-9]+)$",$to_name)) $erreur=3;
		else if(strlen($to_name)<3 || strlen($to_name)>20) $erreur=4;
		else {
			$res=$sql->query('SELECT id FROM mod_membres WHERE pseudo="'.$to_name.'"');
			if($sql->numRows($res)==0) $erreur=2;
		}
		if(isset($erreur)) {
			$template->setBlock('centredroite','erreur-pseudo'.$erreur);
			$template->parse('erreur-pseudo'.$erreur, true);
			return false;
		}
		if(empty($sujet)) {
			$template->setBlock('centredroite','erreur-sujet');
			$template->parse('erreur-sujet', true);
			return false;
		}
		if(empty($message)) {
			$template->setBlock('centredroite','erreur-message');
			$template->parse('erreur-message', true);
			return false;
		}
		
		$info=$sql->fetchAssoc($res);
		
		// Envoi du message
		$this->sendMessage($info['id'],$to_name,$sujet,$message);
		
		if(ereg('reply',$_GET['action']) || ereg('quote',$_GET['action'])) {
			$action=explode('-',$_GET['action']);
			if (ereg('reply',$action[2])) $id=str_replace('reply','',$action[2]);
			else $id=str_replace('quote','',$action[2]);
			$sql->query('UPDATE mod_messages SET etat="2" WHERE id="'.$id.'" && to_id="'.$this->infos('id').'"');
		}
		header('location:mon-profil-boite-de-reception.html');
		exit();
	}
	
    /**
     * Suppression d'un message privé
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Id du message  supprimer
     */ 
	function delMessage($id) {
		global $sql;
		$sql->query('
			DELETE FROM 
				mod_messages 
			WHERE 
				id="'.$id.'" && 
				to_id="'.$this->infos('id').'"
		');
		header('location:mon-profil-boite-de-reception.html');
		exit();
	}

    /**
     * Suppression de plusieurs messages privé
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Tableau des Ids des messages  supprimer
     */ 
	function delMessages($messages) {
		global $sql;
		if(!is_array($messages)) return false;
		foreach($messages as $i=>$id) {
			$sql->query('
				DELETE FROM 
					mod_messages 
				WHERE 
					id="'.$id.'" && 
					to_id="'.$this->infos('id').'"
			');
		}
		header('location:mon-profil-boite-de-reception.html');
		exit();
	}

    /**
     * Marque plusieurs messages privé comme lu
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Tableau des Ids des messages  supprimer
     */ 
	function markMessages($messages) {
		global $sql;
		if(!is_array($messages)) return false;
		foreach($messages as $i=>$id) {
			$sql->query('
				UPDATE 
					mod_messages 
				SET
					etat=1
				WHERE 
					id="'.$id.'" && 
					to_id="'.$this->infos('id').'"
			');
		}
		header('location:mon-profil-boite-de-reception.html');
		exit();
	}
	
    /**
     * Pagination de la liste des membres
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Template
	 * @param	integer	Nombre de page  raliser
	 * @param	integer	Page courrante
	 * @param	integer	Nombre de messages par page
	 * @param	integer	Message auquel commencer
     */ 
	function makePages(&$template,$nbpages,$currpage,$numtoview=20,$start=0) {
		// Nb de pages avant !
		$avant=$currpage-1;

		// Nb de pages aprs !
		$apres=$nbpages-$currpage;
		
		if($nbpages>1) {
			$template->setVar(					'nbpages',		$nbpages);
			$template->parse(					'pages.num',	true);
			$template->setVar(					'numpage0',		$currpage);   
			$template->parse(					'pages.c',		true);

			if($avant>=2) {
			   $template->setVar(				'num.mm',		$start-2*$numtoview);   
			   $template->setVar(				'numpage-2',	$currpage-2);   
			   $template->parse(				'pages.mm',		true);
			   if($avant>2) $template->parse(	'pages.first',	true);
			}
			if($avant>=1) {
			   $template->setVar(				'num.m',		$start-$numtoview);   
			   $template->setVar(				'numpage-1',	$currpage-1);   
			   $template->parse(				'pages.m',		true);
			   $template->parse(				'pages.prev',	true);
			}
			if($apres>=1) {
			   $template->setVar(				'num.p',		$start+$numtoview);   
			   $template->setVar(				'numpage+1',	$currpage+1);   
			   $template->parse(				'pages.p',		true);
			   $template->parse(				'pages.next',	true);
			}
			if($apres>=2) {
			   $template->setVar(				'num.pp',		$start+2*$numtoview);   
			   $template->setVar(				'numpage+2',	$currpage+2);   
			   $template->parse(				'pages.pp',		true);
			   if($apres>2) {
			   $template->setVar(				'num.last',		$numtoview*($nbpages-1));   
			   $template->parse(				'pages.last',	true);
			   }
			}
		}
	}
	
	/**
	 * Met  jour les points de participation des membres
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Nombre de points  ajouter
	 */
	function majPart($points) {
		global $sql;
		if(!is_int($points)) return false;
		$sql->query('UPDATE mod_membres SET part=part+'.$points.' WHERE id='.$membres->infos('id'));
		return true;
	}
	
/**
 * Fonctions admin.
 */
 
	/**
	 * Met  jour la configuration des membres
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string		Etat des inscriptions
	 * @param	string		Titre de l'e-mail
	 * @param	string		Texte de l'e-mail
	 */
	function configMembres($inscr,$title,$txt) {
		global $sql,$site;
		if($inscr!=$site->config('membres_inscr') && ($inscr=='open' || $inscr=='close')) $sql->query('UPDATE config SET value="'.$inscr.'" WHERE name="membres_inscr"');
		$title=$site->clear4Sql($title);
		$txt=$site->clear4Sql($txt);
		if($title!=$site->config('membres_mail_title')) $sql->query('UPDATE config SET value="'.$title.'" WHERE name="membres_mail_title"');
		if($txt!=$site->config('membres_mail_txt')) $sql->query('UPDATE config SET value="'.$txt.'" WHERE name="membres_mail_txt"');
		header('location:configuration.html#mess3');
		exit();
	}

	/**
	 * Met  jour les niveaux des membres
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Donnes du formulaire
	 */
	function niveauxMembres($post) {
		global $sql,$site;
		$tab=array();
		for($i=1;isset($post['points'.$i]);$i++) {
			if(!empty($post['points'.$i]) || $post['points'.$i]==='0') {
				$tab[$post['points'.$i]]=array(
					$post['design'.$i],
					$post['image'.$i],
					$post['img'.$i]
				);
			}
		}
		ksort($tab);
		
		$sql->query('UPDATE config SET value="'.$site->clear4Sql(serialize($tab)).'" WHERE name="membres_level"');
		header('location:niveaux.html#mess2');
		exit();
	}
	
    /**
     * Mise  jour des informations d'un membre
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations du membre
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
     */ 
	function editMembres($post,&$sub_template) {
		global $sql,$site;
		# Vérification des informations saisies (seul l'e-mail est obligatoire)
		if(empty($post['mail'])) $erreur=1;
		else if(!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$post['mail'])) $erreur=3;
		else if($sql->numRows($sql->query('SELECT mail FROM mod_membres WHERE mail="'.$post['mail'].'" && id!="'.$_GET['id'].'"'))>0) $erreur=2;
		if(isset($erreur)) {
			$sub_template->setBlock('centredroite','erreur-mail'.$erreur);
			$sub_template->parse('erreur-mail'.$erreur, true);
		}
		if(!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$post['msn']) && !empty($post['msn'])) {
			$sub_template->setBlock('centredroite','erreur-msn');
			$sub_template->parse('erreur-msn', true);
			$erreur=4;
		}
		if(!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$post['gtalk']) && !empty($post['gtalk'])) {
			$sub_template->setBlock('centredroite','erreur-gtalk');
			$sub_template->parse('erreur-gtalk', true);
			$erreur=5;
		}
		if(isset($erreur)) return false;
		
		/**
		 * Si l'e-mail change et que l'on utilise un gravatar alors on vérifie si il en existe un avec la nouvelle adresse e-mail
		 */
		 
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				mail,
				avatar
			FROM 
				mod_membres
			WHERE 
				id="'.$_GET['id'].'"
		'));
		 
		if($post['mail']!=$info['mail'] && ($info['avatar']=='gravatar' || $info['avatar']==0)) {
			if(!$this->gravatarExist($post['mail'])) $avatar='avatar="0",';
			else $avatar='avatar="gravatar",';
		} else $avatar='';
		
		$post['www']=substr(str_replace('http://','',$post['www']),0,255);
		
		$sql->query('
			UPDATE 
				mod_membres 
			SET 
				nom="'.$site->clear4Sql($post['nom']).'",
				prenom="'.$site->clear4Sql($post['prenom']).'",
				'.$avatar.'
				signature="'.$site->clear4Sql($post['message']).'",
				date_nes="'.mktime(0,0,0,$post['mois'],$post['jour'],$post['annee']).'",
				natio="'.$site->clear4Sql($post['natio']).'",
				www="'.$site->clear4Sql($post['www']).'",
				mail="'.$site->clear4Sql($post['mail']).'",
				msn="'.$site->clear4Sql($post['msn']).'",
				icq="'.$site->clear4Sql($post['icq']).'",
				yahoo="'.$site->clear4Sql($post['yahoo']).'",
				aim="'.$site->clear4Sql($post['aim']).'",
				skype="'.$site->clear4Sql($post['skype']).'",
				xfire="'.$site->clear4Sql($post['xfire']).'",
				gtalk="'.$site->clear4Sql($post['gtalk']).'",
				hard_1="'.$site->clear4Sql($post['hard_1']).'",
				hard_2="'.$site->clear4Sql($post['hard_2']).'",
				hard_3="'.$site->clear4Sql($post['hard_3']).'",
				hard_4="'.$site->clear4Sql($post['hard_4']).'",
				hard_5="'.$site->clear4Sql($post['hard_5']).'",
				hard_6="'.$site->clear4Sql($post['hard_6']).'",
				hard_7="'.$site->clear4Sql($post['hard_7']).'"
			WHERE 
				id="'.$_GET['id'].'"
		');
		header('location:liste.html#mess0');
	}

    /**
     * Changement de l'avatar d'un membre
	 * Ne rien toucher, cette fonction est trs calinne :)
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array		Informations du nouvel avatar
	 * @param	array		Informations sur le fichier envoy
	 * @param	Template	Template dans laquelle afficher les ventuelles erreurs
     */ 
	function changeMembresAvatar($avatar,$url,$file,&$sub_template) {
		global $site,$sql;
		$types=array('image/x-png','image/gif','image/pjpeg','image/jpeg','image/png');
		# Pas/Plus d'avatar
		if($avatar==1) {
			$this->delAvatar('all',$_GET['id']);
			$sql->query('UPDATE mod_membres SET avatar="0" WHERE id="'.$_GET['id'].'"');
			header('location:liste.html#mess4');
			exit();
		# Gravatar (www.gravatar.com)
		} else if($avatar==3) {
			$this->delAvatar('all',$_GET['id']);
			$sql->query('UPDATE mod_membres SET avatar="gravatar" WHERE id="'.$_GET['id'].'"');
			header('location:liste.html#mess4');
			exit();
		# Upload d'avatar
		} else if(isset($file['upload']['name']) && !empty($file['upload']['name'])) {
		
			$extension = strtolower(substr($file['upload']['name'], strrpos($file['upload']['name'], ".")));
			if(!in_array($file['upload']['type'],$types)) $erreur=3; 																// Vérification du type
			else if($file['upload']['size']>($site->config('avatar_max_size')*1024)) $erreur=4;										// Vérification de la taille
			else if(move_uploaded_file($file['upload']['tmp_name'],'modules/membres/avatars/'.$_GET['id'].'-tmp'.$extension)) {		// Déplacement dans le dossier avatar sous un nom temporaire
				$image=getimagesize('modules/membres/avatars/'.$_GET['id'].'-tmp'.$extension);										// Récupération des dimensions
				if($image[0]>$site->config('avatar_max_height') || $image[1]>$site->config('avatar_max_width')) {					// Vérification des dimensions
					$erreur=4;
					$this->delAvatar('tmp',$_GET['id']);																						// Suppression de l'image temporaire
				} else {
					$this->delAvatar('notmp',$_GET['id']);																						// Suppression de l'avatar prcdent
					rename(																											// Renomage de l'image
						'modules/membres/avatars/'.$_GET['id'].'-tmp'.$extension,
						'modules/membres/avatars/'.$_GET['id'].$extension
					);
				}
			}
		# Récupération d'avatar sur internet
		} else if($url!='http://') {
			$taille=$site->remoteInfos($url,'taille');																				// Récupération de la taille
			if($taille===false) $erreur=1;																							// Fichier introuvable (ou on le considérée comme tel)
			else if($taille>($site->config('avatar_max_size')*1024)) $erreur=2;														// Vérification de la taille
			else if(!in_array($site->remoteInfos($url,'type'),$types)) $erreur=5;													// Vérification du type
			if(empty($erreur)) {
				$image=getimagesize($url);																							// Récupération des dimensions
				if($image[0]>$site->config('avatar_max_height') || $image[1]>$site->config('avatar_max_width')) $erreur=2;																					// Vérification des dimensions
				else {
					# Récupération de l'image
					$fp=fopen($url,'r');
					$image='';
					for($i=ceil($taille/4096);$i>0;$i--) $image.=fread($fp,4096);
					fclose($fp);
					# End
					# Enregistrement de l'image
					$url=parse_url($url);
					$extension = strtolower(substr($url['path'], strrpos($url['path'], ".")));
					$fp=fopen('modules/membres/avatars/'.$_GET['id'].$extension,'w');
					fwrite($fp,$image);
					fclose($fp);
					# End
				}
			}
		} else return true;
		
		if(isset($erreur)) {
			$sub_template->setBlock('centredroite','erreur-avatar'.$erreur);
			$sub_template->parse('erreur-avatar'.$erreur, true);
			return false;
		}
		
		$sql->query('UPDATE mod_membres SET avatar="'.$_GET['id'].$extension.'" WHERE id="'.$_GET['id'].'"');
		header('location:liste.html#mess4');
		exit();
	}
	
    /**
     * Suppression d'un membre
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Id du membre
     */ 
	function supprimerMembres($id) {
		global $sql;
		$sql->query('DELETE FROM mod_membres WHERE id="'.$id.'"');
		header('location:liste.html#mess1');
		exit();
	}
	
    /**
     * Modification d'un message privé automatique
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer		Id du message
	 * @param	string		Nouveau titre du message
	 * @param	string		Nouveau texte du message
     */ 
	function modifierMessage($id,$titre,$txt) {
		global $site,$sql;
		$sql->query('UPDATE config SET value="'.$site->clear4Sql($titre).'" WHERE name="membres_'.$id.'_title"');
		$sql->query('UPDATE config SET value="'.$site->clear4Sql($txt).'" WHERE name="membres_'.$id.'_txt"');
		header('location:liste-messages.html#mess5');
		exit();
	}
	function listAcces() {
		global $sql;
		$res=$sql->query('SELECT id,name,descr FROM acces ORDER BY name');
		$liste=array();
		for($i=0;$info=$sql->fetchRow($res);$i++) {
			list($liste[$i]['id'],$liste[$i]['nom'],$liste[$i]['descr'])=$info;
			list($liste[$i]['module'])=explode('_',$liste[$i]['nom'],2);
			if(ereg('^forum_([0-9]+)',$liste[$i]['nom'])) unset($liste[$i--]);
		}
		return $liste;
	}
	
	function listGroupAcces() {
		global $sql;
		$res=$sql->query('SELECT id,name,acces FROM groupes ORDER BY id');
		$liste=array();
		for($i=0;$info=$sql->fetchAssoc($res);$i++) {
			$liste[$info['name']]=$info['acces'];
		}
		return $liste;
	}
	
	function updateAcces($post) {
		global $sql;
		$acces=$this->listAcces();
		$gacces=$this->listGroupAcces();
		$liste='|';
		foreach($acces as $info) {
			$liste.=$info['id'].'|';
		}
		
		$tab=array('A'=>'Administrateurs','M'=>'Membres','V'=>'Visiteurs','B'=>'Bannis');
		foreach($tab as $i=>$var) {
			$gacces[$var]=$this->removeAcces($gacces[$var],$liste);
			$$var='|';
			foreach($post as $j=>$var2) {
				if(strpos($j,'acces'.$i.'_')!==false) $$var.=$var2.'|';
			}
			$gacces[$var]=$this->addAcces($gacces[$var],$$var);
			$sql->query('UPDATE groupes SET acces="'.$gacces[$var].'" WHERE name="'.$var.'"');
		}
		header("Cache-control: private, no-cache");
		header('location:acces.html#mess3');
		exit();
	}
	
	function liveUpdate() {
		$min=5;	
		$file='modules/membres/live.txt';
		
		// Lecture du fichier
		if(!file_exists($file)) return false;
				
		$live = implode('',file($file));
		if(!empty($live)) eval('$live='.$live.';');
		else $live=array();
		
		// Suppression des personnes hors-ligne
		if(!is_array($live)) $live=array(); 
		foreach($live as $i=>$val) if(($val+($min*60))<time()) unset($live[$i]);
		
		// Ajout de l'utilisateur courant
		$live[$_SERVER["REMOTE_ADDR"]]=time();
		
		// Tranformation du tableau pour exportation
		$live=var_export($live,true);

		// Ecriture du fichier
		$fp=fopen($file,"w");

		// Verrou criture
		@flock($fp, LOCK_EX);

		// a-t-il ete supprime par le locker ?
		if (!@file_exists($file)) {
			@fclose($fp);
			return false;
		}

		@fputs($fp,$live);
		
		// Liberer le verrou
		@flock($fp, LOCK_UN);
		
		fclose($fp);
	}

	function liveRead() {
		$file='modules/membres/live.txt';
		
		// Lecture du fichier
		if(!file_exists($file)) return false;
		$live = implode('',file($file));
		if(!empty($live)) eval('$live='.$live.';');
		else $live=array();
		if(!is_array($live)) $live=array(); 
		
		return count($live);
	}
	
	function createBlog($pass,$pass2) {
		global $site;
		/**
		 * Vérifications des informations saisies
		 */
		$erreur=array();
		if($pass!=$pass2) $erreur[]='pass1';
		else if(empty($pass)) $erreur[]='pass2';
		else if(md5($pass)!=$this->infos('pass')) $erreur[]='pass3';
		if(count($erreur)>0) return $erreur;
		/* Fin Vérifications */
	
		if (isset($_SERVER['DC_RC_PATH'])) {
			$rc_path = $_SERVER['DC_RC_PATH'];
		} elseif (isset($_SERVER['REDIRECT_DC_RC_PATH'])) {
			$rc_path = $_SERVER['REDIRECT_DC_RC_PATH'];
		} else {
			$rc_path = dirname(__FILE__).'/../blogs/inc/config.php';
		}
		
		if (!is_file($rc_path)) {
			printf('Configuration file does not exist. Please create one
		first. You may use the <a href="%s">wizard</a>.','wizard.php');
			exit;
		}
		
		//calcul du nom de domaine à utiliser en création
		$url_expl=explode('.',$_SERVER['HTTP_HOST']);
		$url_expl=array_reverse($url_expl);
		$domain_name='.'.$url_expl[1].'.'.$url_expl[0];
		
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/dblayer/dblayer.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/class.rest.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/class.url.handler.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.rest.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.core.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.auth.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.error.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.session.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.modules.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.utils.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.settings.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/lib.files.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/lib.http.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/lib.date.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/lib.crypt.php';
		require_once dirname(__FILE__).'/../blogs/inc/prepend.php';
		require_once dirname(__FILE__).'/../blogs/inc/clearbricks/lib.form.php';
		require_once dirname(__FILE__).'/../blogs/inc/core/class.dc.rs.extensions.php';
		
		# Loading locales for detected language
		$dlang = http::getAcceptLanguage();
		if ($dlang) {
			l10n::init();
			l10n::set(dirname(__FILE__).'/../blogs/locales/'.$dlang.'/main');
		}
		
		if (!defined('DC_MASTER_KEY') || DC_MASTER_KEY == '') {
			echo __('Please set a master key (DC_MASTER_KEY) in configuration file.');
			exit;
		}
		
		# Check if dotclear is already installed
		/*if (in_array($core->prefix.'version',$core->con->getTables())) {
			echo __('DotClear is already installed.');
			exit;
		}*/
		
		# Get information and perform install
		$u_email = $u_firstname = $u_name= $u_blog ='';
		$mail_sent = false;
		if (!empty($_POST)) {
			$u_email = ($this->infos('mail')!='') ? $this->infos('mail') : null;
			$u_firstname = ($this->infos('prenom')!='') ? $this->infos('prenom') : null;
			$u_name = ($this->infos('nom')!='') ? $this->infos('nom') : null;
			$u_displayname = ($this->infos('pseudo')!='') ? $this->infos('pseudo') : null;
			$u_blog = ($this->infos('pseudo')!='') ? strtolower($this->infos('pseudo')) : null;
			
			
			$user_pwd = $pass;
			
			$cur = $core->con->openCursor($core->prefix.'user');
			$cur->user_id = (string) $u_blog;
			$cur->user_super = 0;
			$cur->user_pwd = crypt::hmac(DC_MASTER_KEY,$user_pwd);
			$cur->user_name = (string) $u_name;
			$cur->user_firstname = (string) $u_firstname;
			$cur->user_displayname = (string) $u_displayname;
			$cur->user_email = (string) $u_email;
			$cur->user_lang = $dlang;
			$cur->user_tz = 'Europe/Paris';
			$cur->user_creadt = array('NOW()');
			$cur->user_upddt = array('NOW()');
			$cur->user_post_status = 1;
			$cur->user_options = serialize($core->userDefaults());
			//$cur->user_options['post_format'] = 'xhtml';
			$cur->insert();
			
			$core->auth->checkUser('admin');
			
			$admin_url = 'http://blog'.$domain_name.'/admin';
			$root_url = 'http://blog'.$domain_name.'/'.$u_blog;
			
			$cur = $core->con->openCursor($core->prefix.'blog');
			$cur->blog_id = $u_blog;
			$cur->blog_url = 'http://www'.$domain_name.'/blog/'.$u_blog.'/';
			$cur->blog_name = __('Blog de '.$u_displayname);
			$core->addBlog($cur);
	
			$core->setUserBlogPermissions($u_blog, $u_blog,  array('admin'=>1), true);
	
			$core->blogDefaults($cur->blog_id);
			
			$blog_settings = new dcSettings($core,$u_blog);
			$blog_settings->setNameSpace('system');
			$blog_settings->put('lang',$dlang);
			$blog_settings->put('public_url','/modules/blogs/public/'.$u_blog);
			$blog_settings->put('public_path','public/'.$u_blog);//.$u_blog.$domain_name);
			$blog_settings->put('themes_url','/modules/blogs/themes');
			mkdir('modules/blogs/public/'.$u_blog,0777);
			@chmod('modules/blogs/public/'.$u_blog,0777); // On est jamais trop prudent
		
			$tab1=array('[pseudo]','[pass]');
			$tab2=array($u_blog,$pass);
			$message=str_replace($tab1,$tab2,$site->config('blogs_mail_txt'));

		
			/*mail($u_email,$site->config('blogs_mail_title'),utf8_decode($message),
				"From: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
				."Reply-To: noreply@".str_replace('www.','',$_SERVER['SERVER_NAME'])."\r\n"
				."X-Mailer: PHP/".phpversion());*/
		}
		

		
		header("Cache-control: private, no-cache");
		header('location:mon-profil-creer-blog-fin.html');
		exit();
	}
	
	function checkBlog($pseudo) {
		global $bdd;
		$sql2	= new mysql($bdd['Host'], $bdd['User'], $bdd['Pass'], $bdd['Base2']);
		$res=$sql2->query('SELECT blog_id FROM dc_blog WHERE blog_id="'.strtolower($pseudo).'"');
		if ($sql2->numRows($res)>0) return true;
		else return false;
	}
}
?>