<?php
/**
 * Classe du forum.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class forum {

    /**
     * Réponse  un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet auquel rpondre
	 * @return	mixed
     */ 
	function reply($id) {
		global $site,$sql,$membres,$template,$string;
		$bbcode = new bbcode;
		if(!$membres->infos('id')) {
			$pseudo=$site->clear4Sql($_POST['pseudo'],false);
			if($this->isSpam($_POST['message'])) return false;
		}
		else $pseudo=$membres->infos('pseudo');
		$message=$site->clear4Sql($_POST['message'],false,false,true);
		
		# Vérification des informations saisies
		if(empty($message)) {
			$template->setBlock('centre','erreur-message');
			$template->parse('erreur-message', true);
			return false;
		}
		if(empty($pseudo)) {
			$template->setBlock('centre','erreur-pseudo');
			$template->parse('erreur-pseudo', true);
			return false;
		}
		
		if(!eregi("^([a-z0-9]+)$",$pseudo)) return false;
		
		$template->setVar('pseudo',$pseudo);
		$template->setVar('bbmessage',$message);
		# The End

		$info=$sql->fetchAssoc($sql->query('
			SELECT
				mod_forum_topics.titre,
				mod_forum_topics.posts,
				mod_forum_topics.forum_id,
				mod_forum_forums.nb_posts
			FROM
				mod_forum_topics,
				mod_forum_forums
			WHERE
				mod_forum_topics.id="'.$id.'" &&
				mod_forum_forums.id=mod_forum_topics.forum_id
		'));
		$sql->query('
		INSERT INTO 
			mod_forum_posts (post,post_date,auteur_id,auteur_name,topic_id,forum_id) 
		VALUES (
			"'.$message.'",
			"'.date('U').'",
			"'.$membres->infos('id').'",
			"'.$pseudo.'",
			"'.$id.'",
			"'.$info['forum_id'].'"
		)');
		$sql->query('UPDATE mod_forum_topics SET posts="'.($info['posts']+1).'",last_post="'.date('U').'",last_poster_name="'.$pseudo.'",last_poster_id="'.$membres->infos('id').'" WHERE id="'.$id.'"');
		$sql->query('UPDATE mod_forum_forums SET last_post_date="'.date('U').'",nb_posts="'.($info['nb_posts']+1).'",last_poster_name="'.$pseudo.'",last_poster_id="'.$membres->infos('id').'",last_post="'.$info['titre'].'",last_post_id="'.$id.'" WHERE id="'.$info['forum_id'].'"');
		$sql->query('UPDATE mod_membres SET posts="'.($membres->infos('posts')+1).'" WHERE id="'.$membres->infos('id').'"');
		$s_info=$sql->fetchAssoc($sql->query('SELECT id FROM mod_forum_posts ORDER BY id DESC LIMIT 0,1'));
		header('location:'.$string->clean($info['titre']).'-t'.$id.'.html#post'.$s_info['id']);
		exit();
	}
	
    /**
     * Nouveau sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du forum o poster le sujet
	 * @return	mixed
     */ 
	function newTopic($id) {
		global $sql,$membres,$template,$site,$string;
		$bbcode = new bbcode;
		if(!$membres->infos('id')) {
			$idm='0';
			$pseudo=$site->clear4Sql($_POST['pseudo']);
			if($this->isSpam($_POST['message'])) return false;
		} else {
			$idm=$membres->infos('id');
			$pseudo=$membres->infos('pseudo');
		}
		$titre		=	$string->clean($_POST['titre'],'slash');
		$descr		=	$string->clean($_POST['descr'],'slash');
		$message	=	$string->clean($_POST['message'],'slash');

		# Vérification des informations saisies
		if(empty($titre) || empty($message) || empty($pseudo)) {
			$template->setBlock('centre','erreur-pseudo');
			$template->setBlock('centre','erreur-titre');
			$template->setBlock('centre','erreur-message');
			if(empty($pseudo)) $template->parse('erreur-pseudo', true);
			if(empty($titre)) $template->parse('erreur-titre', true);
			if(empty($message)) $template->parse('erreur-message', true);
			if(isset($_POST['pseudo'])) $template->setVar('pseudo',$string->clean($_POST['pseudo'],'htmlentities'));
			if(isset($_POST['titre'])) $template->setVar('titremessage',$string->clean($_POST['titre'],'htmlentities'));
			if(isset($_POST['descr'])) $template->setVar('descrmessage',$string->clean($_POST['descr'],'htmlentities'));
			$template->setVar('bbmessage',$string->clean($_POST['message'],'htmlentities'));
			return false;
		}
		# The End
		
		// Récupération des options spciales
		$special=array();
		if(isset($_POST['news'])) $special['news']=$_POST['news'];
		$special=$site->clear4Sql(serialize($special));
		# The End
		
		// Insertion du sujet
		$sql->query('
		INSERT INTO 
			mod_forum_topics (titre,descr,etat,starter_id,start_date,last_poster_id,last_post,starter_name,last_poster_name,forum_id,approved,special) 
		VALUES (
			"'.$titre.'",
			"'.$descr.'",
			"open",
			"'.$idm.'",
			"'.date('U').'",
			"'.$idm.'",
			"'.date('U').'",
			"'.$pseudo.'",
			"'.$pseudo.'",
			"'.$id.'",
			"1",
			"'.$special.'"
		)');
		$last_post_id=$sql->getId();
		// Mise  jours des infos du forum
		$sql->query('
		UPDATE 
			mod_forum_forums 
		SET 
			last_post_date="'.date('U').'",
			nb_topics=nb_topics+1,
			last_poster_name="'.$pseudo.'",
			last_poster_id="'.$idm.'",
			last_post="'.$titre.'",
			last_post_id="'.$last_post_id.'" 
		WHERE 
			id="'.$id.'"
		');
		
		// Mise  jour des infos du membre
		$sql->query('
		UPDATE 
			mod_membres 
		SET 
			posts=posts+1 
		WHERE 
			id="'.$idm.'"
		');
		
		// Récupération de l'id du sujet
		$s_info=$sql->fetchAssoc($sql->query('
			SELECT 
				id 
			FROM 
				mod_forum_topics 
			ORDER BY 
				id DESC 
			LIMIT 
				0,1
		'));
		
		// Insertion du post
		$sql->query('
		INSERT INTO 
			mod_forum_posts (post,post_date,auteur_id,auteur_name,topic_id,forum_id,new_topic) 
		VALUES (
			"'.$message.'",
			"'.date('U').'",
			"'.$idm.'",
			"'.$pseudo.'",
			"'.$s_info['id'].'",
			"'.$id.'",
			"1"
		)');
		
		// Récupération de l'id du post
		$ss_info=$sql->fetchAssoc($sql->query('
		SELECT 
			id 
		FROM 
			mod_forum_posts 
		ORDER BY id DESC LIMIT 0,1
		'));
		
		header('location:'.$string->clean($titre).'-t'.$s_info['id'].'.html#post'.$ss_info['id']);
	}
	
    /**
     * Nouveau sondage
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du forum o poster le sondage
	 * @return	mixed
     */ 
	function newPoll($id) {
		global $sql,$membres,$template,$site,$string;
		$bbcode = new bbcode;
		
		$titre=$string->clean($_POST['titre'],'slash');
		$descr=$string->clean($_POST['descr'],'slash');
		$message=$string->clean($_POST['message'],'slash');

		$quest=$string->clean($_POST['quest'],'slash');
		$choix=$string->clean($_POST['choix'],'slash');
		
		# Vérification des informations saisies
		if(empty($titre) || empty($message) || empty($choix) || count(explode('<br />',nl2br($choix)))<=1) {
			$template->setBlock('centre','erreur-titre');
			$template->setBlock('centre','erreur-message');
			$template->setBlock('centre','erreur-choix');
			if(empty($titre)) $template->parse('erreur-titre', true);
			if(empty($message)) $template->parse('erreur-message', true);
			if(empty($choix)) $template->parse('erreur-choix', true);
			if(isset($_POST['titre'])) $template->setVar('titremessage',$string->clean($_POST['titre'],'htmlentities'));
			if(isset($_POST['descr'])) $template->setVar('descrmessage',$string->clean($_POST['descr'],'htmlentities'));
			if(isset($_POST['quest'])) $template->setVar('questsondage',$string->clean($_POST['quest'],'htmlentities'));
			if(isset($_POST['choix'])) $template->setVar('choixsondage',$string->clean($_POST['choix'],'htmlentities'));
			$template->setVar('bbmessage',$string->clean($_POST['message'],'htmlentities'));
			return false;
		}
		# The End
		
		if(empty($quest)) $quest=$titre;
		$choix=explode('<br />',nl2br($choix));
		foreach($choix as $i=>$var) $choix[$i]=trim($var);
		$choix=$string->delEmptyEntry($choix);
		$choix=serialize($choix);
		
		// Slection du nombre de sujets dans le forum
		$info=$sql->fetchAssoc($sql->query('
		SELECT 
			mod_forum_forums.nb_topics 
		FROM 
			mod_forum_forums 
		WHERE 
			mod_forum_forums.id="'.$id.'"
		'));

		// Insertion du sondage
		$sql->query('
		INSERT INTO 
			mod_poll (quest,choix) 
		VALUES (
			"'.$quest.'",
			"'.addslashes($choix).'"
		)');
		$poll_id=$sql->getId();
		
		// Insertion du sujet
		$sql->query('
		INSERT INTO 
			mod_forum_topics (titre,descr,etat,starter_id,start_date,last_poster_id,last_post,starter_name,last_poster_name,forum_id,approved,poll) 
		VALUES (
			"'.$titre.'",
			"'.$descr.'",
			"open",
			"'.$membres->infos('id').'",
			"'.date('U').'",
			"'.$membres->infos('id').'",
			"'.date('U').'",
			"'.$membres->infos('pseudo').'",
			"'.$membres->infos('pseudo').'",
			"'.$id.'",
			"1",
			"'.$poll_id.'"
		)');
		$topic_id=$sql->getId();
		
		// Mise  jours des infos du forum
		$sql->query('
		UPDATE 
			mod_forum_forums 
		SET 
			last_post_date="'.date('U').'",
			nb_topics=nb_topics+1,
			last_poster_name="'.$membres->infos('pseudo').'",
			last_poster_id="'.$membres->infos('id').'",
			last_post="'.$titre.'" 
		WHERE 
			id="'.$id.'"
		');
		
		// Mise  jour des infos du membre
		$sql->query('
		UPDATE 
			mod_membres 
		SET 
			posts=posts+1 
		WHERE 
			id="'.$membres->infos('id').'"
		');
		
		// Récupération de l'id du sujet
		
		// Insertion du post
		$sql->query('
		INSERT INTO 
			mod_forum_posts (post,post_date,auteur_id,auteur_name,topic_id,forum_id,new_topic) 
		VALUES (
			"'.$message.'",
			"'.date('U').'",
			"'.$membres->infos('id').'",
			"'.$membres->infos('pseudo').'",
			"'.$topic_id.'",
			"'.$id.'",
			"1"
		)');
		$post_id=$sql->getId();
		
		header('location:'.$string->clean($titre).'-t'.$topic_id.'.html#post'.$post_id);
	}
	
    /**
     * Edition d'une rponse
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id de la rponse à éditer
	 * @return	mixed
     */ 
	function editReply($id) {
		global $sql,$template,$site,$string;
		$bbcode = new bbcode;		
		$message=$string->clean($_POST['message'],'slash');
		
		# Vérification des informations saisies
		if(empty($message)) {
			$template->setBlock('centre','erreur-message');
			$template->parse('erreur-message', true);
			$template->setVar('bbmessage',$string->clean($_POST['message'],'htmlentities'));
			return false;
		}
		# The End
		
		$info=$sql->fetchAssoc($sql->query('
			SELECT
				mod_forum_topics.titre
			FROM
				mod_forum_topics,
				mod_forum_posts
			WHERE
				mod_forum_posts.id="'.$_GET['edit'].'" &&
				mod_forum_posts.topic_id=mod_forum_topics.id
		'));
		$sql->query('UPDATE mod_forum_posts SET post="'.$message.'" WHERE id="'.$_GET['edit'].'"');
		header('location:'.$string->clean($info['titre']).'-t'.$_GET['topic'].'.html#post'.$_GET['edit']);
	}

    /**
     * Edition d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet à éditer
	 * @return	mixed
     */ 
	function editTopic($id) {
		global $sql,$template,$site,$string;
		$bbcode = new bbcode;
		$titre=$string->clean($_POST['titre'],'slash');
		$descr=$string->clean($_POST['descr'],'slash');
		$message=$string->clean($_POST['message'],'slash');
		
		# Vérification des informations saisies
		if(empty($titre) || empty($message)) {
			$template->setBlock('centre','erreur-titre');
			$template->setBlock('centre','erreur-message');
			if(empty($titre)) $template->parse('erreur-titre', true);
			if(empty($message)) $template->parse('erreur-message', true);
			if(isset($_POST['titre'])) $template->setVar('titremessage',$_POST['titre']);
			if(isset($_POST['descr'])) $template->setVar('descrmessage',$_POST['descr']);
			$template->setVar('bbmessage',$_POST['message']);
			return false;
		}
		# The End

		$info=$sql->fetchAssoc($sql->query('
			SELECT
				mod_forum_posts.id,
				mod_forum_topics.titre
			FROM
				mod_forum_topics,
				mod_forum_posts
			WHERE
				mod_forum_topics.id="'.$_GET['edit'].'" && 
				mod_forum_posts.topic_id=mod_forum_topics.id && 
				mod_forum_posts.new_topic="1"
		'));
		$sql->query('UPDATE mod_forum_posts SET post="'.$message.'" WHERE id="'.$info['id'].'"');
		$sql->query('UPDATE mod_forum_topics SET titre="'.$titre.'", descr="'.$descr.'" WHERE id="'.$_GET['edit'].'"');
		header('location:'.$string->clean($info['titre']).'-t'.$_GET['edit'].'.html');
	}

    /**
     * Edition d'un sondage
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sondage à éditer
	 * @return	mixed
     */ 
	function editPoll($id) {
		global $sql,$template,$site,$string;
		$bbcode = new bbcode;
		$titre=$string->clean($_POST['titre'],'slash');
		$descr=$string->clean($_POST['descr'],'slash');
		$message=$string->clean($_POST['message'],'slash');

		$quest=$string->clean($_POST['quest'],'slash');
		$choix=$string->clean($_POST['choix'],'slash');
		
		# Vérification des informations saisies
		if(empty($titre) || empty($message) || empty($choix) || count(explode('<br />',nl2br($choix)))<=1) {
			$template->setBlock('centre','erreur-titre');
			$template->setBlock('centre','erreur-message');
			if(empty($titre)) $template->parse('erreur-titre', true);
			if(empty($message)) $template->parse('erreur-message', true);
			if(empty($choix)) $template->parse('erreur-choix', true);
			if(isset($_POST['titre'])) $template->setVar('titremessage',$_POST['titre']);
			if(isset($_POST['descr'])) $template->setVar('descrmessage',$_POST['descr']);
			if(isset($_POST['quest'])) $template->setVar('questsondage',$_POST['quest']);
			if(isset($_POST['choix'])) $template->setVar('choixsondage',$_POST['choix']);
			$template->setVar('bbmessage',$_POST['message']);
			return false;
		}
		# The End

		if(empty($quest)) $quest=$titre;
		$choix=explode('<br />',nl2br($choix));
		foreach($choix as $i=>$var) $choix[$i]=trim($var);
		$choix=$string->delEmptyEntry($choix);
		$choix=serialize($choix);
		
		$info=$sql->fetchAssoc($sql->query('
			SELECT
				mod_forum_topics.id,
				mod_forum_topics.titre,
				mod_forum_topics.poll
			FROM
				mod_forum_topics,
				mod_forum_posts
			WHERE
				mod_forum_topics.id="'.$_GET['edit'].'" &&
				mod_forum_posts.topic_id=mod_forum_topics.id && 
				mod_forum_posts.new_topic="1"
		'));
		$sql->query('UPDATE mod_forum_posts SET post="'.$message.'" WHERE id="'.$_GET['edit'].'"');
		$sql->query('UPDATE mod_forum_topics SET titre="'.$titre.'", descr="'.$descr.'" WHERE id="'.$info['id'].'"');
		$sql->query('UPDATE mod_poll SET quest="'.$quest.'", choix="'.addslashes($choix).'" WHERE id="'.$info['poll'].'"');
		
		header('location:'.$string->clean($info['titre']).'-t'.$info['id'].'.html#post'.$_GET['edit']);
	}

    /**
     * Affichage de l'aperu du message
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function preview() {
		global $template,$site,$string;
		$bbcode = new bbcode;
		$template->setBlock('centre','preview');
		$message=$bbcode->stripBBCode($bbcode->BBCodeToHtml($string->clean($_POST['message'],'htmlentities')));
  	 	$template->setVar('message',$message);
		if(isset($_POST['pseudo'])) $template->setVar('pseudo',$string->clean($_POST['pseudo'],'htmlentities'));
  	 	if(isset($_POST['titre'])) $template->setVar('titremessage',$string->clean($_POST['titre'],'htmlentities'));
  	 	if(isset($_POST['descr'])) $template->setVar('descrmessage',$string->clean($_POST['descr'],'htmlentities'));
  	 	$template->setVar('bbmessage',$string->clean($_POST['message'],'htmlentities'));
	    $template->parse('preview', true);
	}
	
    /**
     * Dtermine si un sujet  t lu ou pas
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  vrifier
	 * @return	mixed
     */ 
	function unRead($topic) {
		if(!isset($_COOKIE['forum_unread'])) return false;
		$unread=unserialize(stripslashes($_COOKIE['forum_unread']));
		if(isset($unread[$topic])) return $unread[$topic];
		else return false;
	}

    /**
     * Marque un sujet comme lu
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  marquer comme lu
     */ 
	function setRead($topic) {
		if(!isset($_COOKIE['forum_unread'])) return false;
		$unread=unserialize(stripslashes($_COOKIE['forum_unread']));
		if(isset($unread[$topic])) unset($unread[$topic]);
		setcookie('forum_unread',serialize($unread),time()+63072000,'/');
	}

    /**
     * Pagination des sujets
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Nombre de page  raliser
	 * @param	string	Nombre de sujets par page
     */ 
	function makeTopicsPages($nbpages,$numtoview) {
		global $template;

		if($nbpages>1) $template->parse('topicpage.un',true);
		if($nbpages>=2) {
			$template->setVar('numpost.p',1*$numtoview);
			$template->parse('topicpage.deux',true);
		}
		if($nbpages>=3) {
			$template->setVar('numpost.pp',2*$numtoview);
			$template->parse('topicpage.trois',true);
		}
		if($nbpages>4) {
			$template->setVar('numpost.l',($nbpages-1)*$numtoview);
			$template->setVar('styleOfTopicPage','lastpage');
			$template->setVar('numOfTopicPage','&raquo;&nbsp;'.$nbpages);
		} else 	{
			$template->setVar('numpost.l',3*$numtoview);
			$template->setVar('styleOfTopicPage','apage');
			$template->setVar('numOfTopicPage','4');
		}
		if($nbpages>=4) $template->parse('topicpage.quatre',true);
		
	}

    /**
     * Pagination du sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Nombre de page  raliser
	 * @param	string	Page courrante
	 * @param	string	Nombre de messages par page
	 * @param	string	Message auquel commencer
     */ 
	function makePages(&$template,$nbpages,$currpage,$numtoview=20,$start=0,$type='f') {
		// Nb de pages avant !
		$avant=$currpage-1;

		// Nb de pages aprs !
		$apres=$nbpages-$currpage;
		
		if($nbpages>1) {
			$template->setVar(					'nbpages',				$nbpages);
			$template->parse(					'pages'.$type.'.num',	true);
			$template->setVar(					'numpage0',				$currpage);   
			$template->parse(					'pages'.$type.'.c',		true);

			if($avant>=2) {
			   $template->setVar(				'numpost.mm',			$start-2*$numtoview);   
			   $template->setVar(				'numpage-2',			$currpage-2);   
			   $template->parse(				'pages'.$type.'.mm',	true);
			   if($avant>2) $template->parse(	'pages'.$type.'.first',	true);
			}
			if($avant>=1) {
			   $template->setVar(				'numpost.m',			$start-$numtoview);   
			   $template->setVar(				'numpage-1',			$currpage-1);   
			   $template->parse(				'pages'.$type.'.m',		true);
			   $template->parse(				'pages'.$type.'.prev',	true);
			}
			if($apres>=1) {
			   $template->setVar(				'numpost.p',			$start+$numtoview);   
			   $template->setVar(				'numpage+1',			$currpage+1);   
			   $template->parse(				'pages'.$type.'.p',		true);
			   $template->parse(				'pages'.$type.'.next',	true);
			}
			if($apres>=2) {
			   $template->setVar(				'numpost.pp',			$start+2*$numtoview);   
			   $template->setVar(				'numpage+2',			$currpage+2	);   
			   $template->parse(				'pages'.$type.'.pp',	true);
			   if($apres>2) {
			   $template->setVar(				'numpost.last',			$numtoview*($nbpages-1));   
			   $template->parse(				'pages'.$type.'.last',	true);
			   }
			}
		}
	}

    /**
     * Récupère les informations d'une rponse  un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id de la rponse
	 * @return	mixed
     */ 
	function getPostInfos($postid) {
		global $sql;
		$info=$sql->fetchAssoc($sql->query('SELECT * FROM mod_forum_posts WHERE id="'.$postid.'"'));
		if(!empty($info)) return $info;
		else return false;
	}

    /**
     * Récupère les informations d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet
	 * @return	mixed
     */ 
	function getTopicInfos($postid) {
		global $sql;
		$info=$sql->fetchAssoc($sql->query('SELECT * FROM mod_forum_topics WHERE id="'.$postid.'"'));
		if(!empty($info)) return $info;
		else return false;
	}

    /**
     * Initialise le message avec une citation
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du message  citer
	 * @return	mixed
     */ 
	function quote($quote) {
		global $template,$string;
		$info=$this->getPostInfos($quote);
		if(!empty($info)) $template->setVar('bbmessage','[quote='.$info['auteur_name'].']'.$string->clean($info['post'],'htmlentities').'[/quote]');
		else return false;
	}

    /**
     * Suppression d'un message
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du message  supprimer
     */ 
	function delPost($id) {
		global $sql,$string;
		$info=$this->getPostInfos($id);
		// Suppr du post
		$sql->query('DELETE FROM mod_forum_posts WHERE id="'.$id.'"');
		
		// Maj. des infos du sujet
		$s_info=$sql->fetchAssoc($sql->query('SELECT post_date,auteur_name,auteur_id FROM mod_forum_posts WHERE topic_id="'.$info['topic_id'].'" ORDER BY post_date DESC LIMIT 0,1'));
		$sql->query('UPDATE mod_forum_topics SET posts=posts-1,last_post="'.$s_info['post_date'].'",last_poster_name="'.$s_info['auteur_name'].'",last_poster_id="'.$s_info['auteur_id'].'" WHERE id="'.$info['topic_id'].'"');
		
		// Maj. des infos du forum
		$s_info=$sql->fetchAssoc($sql->query('SELECT mod_forum_topics.id,titre,post_date,auteur_name,auteur_id FROM mod_forum_topics LEFT JOIN mod_forum_posts ON mod_forum_topics.id=mod_forum_posts.topic_id WHERE mod_forum_posts.forum_id="'.$info['forum_id'].'" ORDER BY post_date DESC LIMIT 0,1'));
		$sql->query('UPDATE mod_forum_forums SET last_post_date="'.$s_info['post_date'].'",nb_posts=nb_posts-1,last_poster_name="'.$s_info['auteur_name'].'",last_poster_id="'.$s_info['auteur_id'].'",last_post="'.$s_info['titre'].'",last_post_id="'.$s_info['id'].'" WHERE id="'.$info['forum_id'].'"');
		
		// Maj. des infos du membre
		$sql->query('UPDATE mod_membres SET posts=posts-1 WHERE id="'.$info['auteur_id'].'"');
		
		$s_info=$sql->fetchAssoc($sql->query('SELECT mod_forum_topics.id,mod_forum_topics.titre FROM mod_forum_topics,mod_forum_posts WHERE mod_forum_posts.forum_id="'.$info['forum_id'].'" && mod_forum_posts.topic_id=mod_forum_topics.id'));
		header('location:'.$string->clean($s_info['titre']).'-t'.$s_info['id'].'.html');
	}

    /**
     * Suppression d'un sujet
     *
	 * @todo	Virer le bouton supprimer
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  supprimer
     */ 
	function delTopic($id) {
		global $sql,$string;
		$info=$this->getTopicInfos($id);
		// Maj. des infos des membres
		$req=$sql->query('SELECT auteur_id FROM mod_forum_posts WHERE topic_id="'.$id.'"');
		$num=-1;
		while($res=$sql->fetchAssoc($req)) {
			$sql->query('UPDATE mod_membres SET posts=posts-1 WHERE id="'.$res['auteur_id'].'"');
			$num++;
		}
		// Suppr des rponses du sujet
		$sql->query('DELETE FROM mod_forum_posts WHERE topic_id="'.$id.'"');
		
		// Suppr du sujet
		$sql->query('DELETE FROM mod_forum_topics WHERE id="'.$id.'"');
		
		// Maj. des infos du forum
		$s_info=$sql->fetchAssoc($sql->query('SELECT mod_forum_topics.id,titre,post_date,auteur_name,auteur_id FROM mod_forum_topics,mod_forum_posts WHERE mod_forum_posts.forum_id="'.$info['forum_id'].'" && mod_forum_posts.topic_id=mod_forum_topics.id ORDER BY post_date DESC LIMIT 0,1'));
		$sql->query('UPDATE mod_forum_forums SET last_post_date="'.$s_info['post_date'].'",nb_posts=nb_posts-'.$num.',nb_topics=nb_topics-1,last_poster_name="'.$s_info['auteur_name'].'",last_poster_id="'.$s_info['auteur_id'].'",last_post="'.$s_info['titre'].'" WHERE id="'.$info['forum_id'].'"');

		$s_info=$sql->fetchAssoc($sql->query('SELECT id,titre FROM mod_forum_forums WHERE id="'.$info['forum_id'].'"'));
		header('location:'.$string->clean($s_info['titre']).'-f'.$s_info['id'].'.html');
	}

    /**
     * Déplacement d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  déplacer
	 * @param	integer	Id du forum de destination
     */ 
	function moveTopic($id,$forum) {
		global $sql,$string;
		$info=$this->getTopicInfos($id);
		
		$info2=$sql->fetchAssoc($sql->query('SELECT COUNT(id) FROM mod_forum_posts WHERE topic_id="'.$id.'"'));
		$num=$info2[0]-1;
		// Maj. des rponses du sujet
		$sql->query('UPDATE mod_forum_posts SET forum_id="'.$forum.'" WHERE topic_id="'.$id.'"');
		
		// Maj. du sujet
		$sql->query('UPDATE mod_forum_topics SET forum_id="'.$forum.'" WHERE id="'.$id.'"');
		
		// Maj. des infos du forum source
		$s_info=$sql->fetchAssoc($sql->query('SELECT mod_forum_topics.id,titre,post_date,auteur_name,auteur_id FROM mod_forum_topics,mod_forum_posts WHERE mod_forum_posts.forum_id="'.$info['forum_id'].'" && mod_forum_posts.topic_id=mod_forum_topics.id ORDER BY post_date DESC LIMIT 0,1'));
		$sql->query('UPDATE mod_forum_forums SET last_post_date="'.$s_info['post_date'].'",nb_posts=nb_posts-'.$num.',nb_topics=nb_topics-1,last_poster_name="'.$s_info['auteur_name'].'",last_poster_id="'.$s_info['auteur_id'].'",last_post="'.$s_info['titre'].'" WHERE id="'.$info['forum_id'].'"');
		
		// Maj. des infos du forum destination
		$s_info=$sql->fetchAssoc($sql->query('SELECT mod_forum_topics.id,titre,post_date,auteur_name,auteur_id FROM mod_forum_topics,mod_forum_posts WHERE mod_forum_posts.forum_id="'.$forum.'" && mod_forum_posts.topic_id=mod_forum_topics.id ORDER BY post_date DESC LIMIT 0,1'));
		$sql->query('UPDATE mod_forum_forums SET last_post_date="'.$s_info['post_date'].'",nb_posts=nb_posts-'.$num.',nb_topics=nb_topics+1,last_poster_name="'.$s_info['auteur_name'].'",last_poster_id="'.$s_info['auteur_id'].'",last_post="'.$s_info['titre'].'" WHERE id="'.$forum.'"');

		$s_info=$sql->fetchAssoc($sql->query('SELECT id,titre FROM mod_forum_forums WHERE id="'.$forum.'"'));
		header('location:'.$string->clean($s_info['titre']).'-f'.$s_info['id'].'.html');
	}

    /**
     * Fermeture d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  fermer
     */ 
	function closeTopic($id) {
		global $sql,$string;
		// Maj. du sujet
		$sql->query('UPDATE mod_forum_topics SET etat="close" WHERE id="'.$id.'"');

		$info=$this->getTopicInfos($id);
		header('location:'.$string->clean($info['titre']).'-t'.$info['id'].'.html');

	}
	
    /**
     * Rouverture d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  rouvrir
     */ 
	function openTopic($id) {
		global $sql,$string;
		// Maj. du sujet
		$sql->query('UPDATE mod_forum_topics SET etat="open" WHERE id="'.$id.'"');

		$info=$this->getTopicInfos($id);
		header('location:'.$string->clean($info['titre']).'-t'.$info['id'].'.html');

	}

    /**
     * Epinglement d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  pingler
     */ 
	function pinTopic($id) {
		global $sql,$string;
		// Maj. du sujet
		$sql->query('UPDATE mod_forum_topics SET pinned=1 WHERE id="'.$id.'"');

		$info=$this->getTopicInfos($id);
		header('location:'.$string->clean($info['titre']).'-t'.$info['id'].'.html');

	}

    /**
     * Dspinglement d'un sujet
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet  dspingler
     */ 
	function unpinTopic($id) {
		global $sql,$string;
		// Maj. du topic
		$sql->query('UPDATE mod_forum_topics SET pinned=0 WHERE id="'.$id.'"');

		$info=$this->getTopicInfos($id);
		header('location:'.$string->clean($info['titre']).'-t'.$info['id'].'.html');

	}

    /**
     * Similarit d'un sujet par rapport  tout le message
     *
	 * @author	Yannick Croissant
	 * @deprecated on utilise similar qui est moins précis mais plus rapide
	 * @see function similar
	 * @access	public
	 * @param	integer	Id du sujet  comparer
	 * @return	resource
     */ 
	function allSimilar($id) {
		global $sql;
		
		$info=$sql->fetchAssoc($sql->query('SELECT post FROM mod_forum_posts WHERE topic_id="'.$id.'" && new_topic="1" LIMIT 0,1'));
		
		$req=$sql->query('
		SELECT 
			mod_forum_topics.id,
			mod_forum_topics.titre,
			mod_forum_topics.descr,
			mod_forum_topics.posts,
			mod_forum_topics.starter_id,
			mod_forum_topics.last_poster_id,
			mod_forum_topics.last_post,
			mod_forum_topics.starter_name,
			mod_forum_topics.last_poster_name,
			mod_forum_topics.views,
			MATCH (post) AGAINST ("'.$info['post'].'") AS score
		FROM 
			mod_forum_posts,mod_forum_topics 
		WHERE 
			MATCH (post) AGAINST ("'.$info['post'].'") &&
			new_topic="1" && 
			mod_forum_topics.id!="'.$id.'" && 
			mod_forum_posts.topic_id=mod_forum_topics.id
		ORDER BY score LIMIT 0,5
		');
		
		return $req;
	}
	
    /**
     * Similarit d'un sujet par rapport au titre
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Titre du sujet  comparer
	 * @param	integer	Id du sujet  comparer
	 * @return	resource
     */ 
	function similar($titre,$id) {
		global $sql,$membres;

		$res=$sql->query('
		SELECT 
			id,
			name
		FROM 
			acces 
		WHERE 
			name LIKE "forum_%_read"
		');
		$forums=array();
		while($info=$sql->fetchAssoc($res)) {
			if(strpos($membres->infos('acces'),'|'.$info['id'].'|')!==false) $forums[]=eregi_replace('([^0-9]*)','',$info['name']);
		}

		
		$res=$sql->query('
		SELECT 
			id,
			titre,
			descr,
			posts,
			starter_id,
			last_poster_id,
			last_post,
			starter_name,
			last_poster_name,
			views,
			MATCH (titre) AGAINST ("'.$titre.'") AS score
		FROM 
			mod_forum_topics 
		WHERE 
			id!="'.$id.'" && 
			forum_id IN ('.implode(',',$forums).') &&
			MATCH (titre) AGAINST ("'.$titre.'")>0
		ORDER BY score DESC
		LIMIT 0,5
		');
		return $res;
	}
	
    /**
     * Déplacement d'une catégorie (haut/bas)
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function moveCat() {
		global $sql,$site;
		$info=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) ordre FROM mod_forum_cats'));
		$max=$info['ordre'];
		$info=$sql->fetchAssoc($sql->query('SELECT ordre FROM mod_forum_cats WHERE id="'.$_GET['id'].'"'));
		if($_GET['action2']=='cup' && $info['ordre']!=1) { 				// Déplace la catégorie vers le haut
			$sql->query('UPDATE mod_forum_cats SET ordre=ordre-1 WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_cats SET ordre="'.$info['ordre'].'" WHERE ordre="'.($info['ordre']-1).'" && id!="'.$_GET['id'].'"');
		} else if($_GET['action2']=='cdown' && $info['ordre']!=$max) { 	// Déplace la catégorie vers le bas
			$sql->query('UPDATE mod_forum_cats SET ordre=ordre+1 WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_cats SET ordre="'.$info['ordre'].'" WHERE ordre="'.($info['ordre']+1).'" && id!="'.$_GET['id'].'"');
		} else if($_GET['action2']=='ctop' && $info['ordre']!=1) { 		// Déplace la catégorie tout en haut
			$sql->query('UPDATE mod_forum_cats SET ordre=1 WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_cats SET ordre=ordre+1 WHERE ordre<="'.($info['ordre']-1).'" && id!="'.$_GET['id'].'"');
		} else if($_GET['action2']=='cbottom' && $info['ordre']!=$max) { // Déplace la catégorie tout en bas
			$sql->query('UPDATE mod_forum_cats SET ordre="'.$max.'" WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_cats SET ordre=ordre-1 WHERE ordre>"'.$info['ordre'].'" && id!="'.$_GET['id'].'"');
		}
		if(isset($_SERVER['HTTP_REFERER'])) header('location:'.$_SERVER['HTTP_REFERER']);
		else $site->error('<p>Utilisation incorrecte de cette page : Referer inconnu.</p>');
		exit();
	}
	
    /**
     * Ajout d'une catégorie
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations de la catégorie  ajouter
     */ 
	function addCat($nom) {
		global $sql,$site;
		$info=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) ordre FROM mod_forum_cats'));
		$max=$info['ordre'];
		$nom=$site->clear4Sql($nom);
		$sql->query('INSERT INTO mod_forum_cats (nom,ordre) VALUES ("'.$nom.'","'.($max+1).'")');
		header('location:gererforum.html');
		exit();
	}
	
    /**
     * Renommage d'une catégorie
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations de la catégorie  renommer
     */ 
	function renCat($nom) {
		global $sql,$site;
		$nom=$site->clear4Sql($nom);
		$sql->query('UPDATE mod_forum_cats SET nom="'.$nom.'" WHERE id="'.$_GET['id'].'"');
		header('location:gererforum.html');
		exit();
	}
	
    /**
     * Supprime une catégorie
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations de la catégorie  supprimer
     */ 
	function supprCat($moveto) {
		global $sql,$site;
		// Déplacement des forums
		$sql->query('UPDATE mod_forum_forums SET cat="'.$moveto.'" WHERE cat="'.$_GET['id'].'"');
		// Suppression de la catégorie
		$sql->query('DELETE FROM mod_forum_cats WHERE id="'.$_GET['id'].'"');
		header('location:gererforum.html');
		exit();
	}
	
    /**
     * Déplacement d'un forum (haut/bas)
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function moveForum() {
		global $sql,$site;
		$action=explode('-',$_GET['action2']);
		$catId=(int)str_replace('f','',$action[0]);
		$action=$action[1];
		$info=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) ordre FROM mod_forum_forums WHERE cat="'.$catId.'"'));
		$max=$info['ordre'];
		$info=$sql->fetchAssoc($sql->query('SELECT ordre FROM mod_forum_forums WHERE id="'.$_GET['id'].'"'));
		if($action=='up' && $info['ordre']!=1) { 				// Déplace la catégorie vers le haut
			$sql->query('UPDATE mod_forum_forums SET ordre=ordre-1 WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_forums SET ordre="'.$info['ordre'].'" WHERE ordre="'.($info['ordre']-1).'" && id!="'.$_GET['id'].'" && cat="'.$catId.'"');
		} else if($action=='down' && $info['ordre']!=$max) { 	// Déplace la catégorie vers le bas
			$sql->query('UPDATE mod_forum_forums SET ordre=ordre+1 WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_forums SET ordre="'.$info['ordre'].'" WHERE ordre="'.($info['ordre']+1).'" && id!="'.$_GET['id'].'" && cat="'.$catId.'"');
		} else if($action=='top' && $info['ordre']!=1) { 		// Déplace la catégorie tout en haut
			$sql->query('UPDATE mod_forum_forums SET ordre=1 WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_forums SET ordre=ordre+1 WHERE ordre<="'.($info['ordre']-1).'" && id!="'.$_GET['id'].'" && cat="'.$catId.'"');
		} else if($action=='bottom' && $info['ordre']!=$max) { // Déplace la catégorie tout en bas
			$sql->query('UPDATE mod_forum_forums SET ordre="'.$max.'" WHERE id="'.$_GET['id'].'"');
			$sql->query('UPDATE mod_forum_forums SET ordre=ordre-1 WHERE ordre>"'.$info['ordre'].'" && id!="'.$_GET['id'].'" && cat="'.$catId.'"');
		}
		if(isset($_SERVER['HTTP_REFERER'])) header('location:'.$_SERVER['HTTP_REFERER']);
		else $site->error('<p>Utilisation incorrecte de cette page : Referer inconnu.</p>');
		exit();
	}
	
    /**
     * Ajout d'un forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations du forum  ajouter
     */ 
	function addForum($cat,$nom,$descr) {
		global $sql,$site,$membres;
		$post['nom']=$site->clear4Sql($nom);
		$post['descr']=$site->clear4Sql($descr);
		$post['cat']=$site->clear4Sql($cat);
		
		$info=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) ordre FROM mod_forum_forums WHERE cat="'.$cat.'"'));
		$max		=	$info['ordre'];

		$sql->query('INSERT INTO mod_forum_forums (titre,descr,cat,ordre) VALUES ("'.$nom.'","'.$descr.'","'.$cat.'","'.($max+1).'")');
		$id			=	$sql->getId();
		$sql->query('INSERT INTO acces (name) VALUES ("forum_'.$id.'_read")');
		$id_read	=	$sql->getId();
		$sql->query('INSERT INTO acces (name) VALUES ("forum_'.$id.'_reply")');
		$id_reply	=	$sql->getId();
		$sql->query('INSERT INTO acces (name) VALUES ("forum_'.$id.'_start")');
		$id_start	=	$sql->getId();
		$sql->query('INSERT INTO acces (name) VALUES ("forum_'.$id.'_edit")');
		$id_edit	=	$sql->getId();
		$sql->query('INSERT INTO acces (name) VALUES ("forum_'.$id.'_del")');
		$id_del		=	$sql->getId();
		
		$toremove		= 	$id_read.'|'.$id_reply.'|'.$id_start.'|'.$id_edit.'|'.$id_del;
		$groupe['4']	=	'|'.((isset($_POST['readA']))?$id_read:'').'|'.((isset($_POST['replyA']))?$id_reply:'').'|'.((isset($_POST['startA']))?$id_start:'').'|'.((isset($_POST['editA']))?$id_edit:'').'|'.((isset($_POST['delA']))?$id_del:'').'|';
		$groupe['3']	=	'|'.((isset($_POST['readM']))?$id_read:'').'|'.((isset($_POST['replyM']))?$id_reply:'').'|'.((isset($_POST['startM']))?$id_start:'').'|'.((isset($_POST['editM']))?$id_edit:'').'|'.((isset($_POST['delM']))?$id_del:'').'|';
		$groupe['2']	=	'|'.((isset($_POST['readV']))?$id_read:'').'|'.((isset($_POST['replyV']))?$id_reply:'').'|'.((isset($_POST['startV']))?$id_start:'').'|'.((isset($_POST['editV']))?$id_edit:'').'|'.((isset($_POST['delV']))?$id_del:'').'|';
		$groupe['1']	=	'|'.((isset($_POST['readB']))?$id_read:'').'|'.((isset($_POST['replyB']))?$id_reply:'').'|'.((isset($_POST['startB']))?$id_start:'').'|'.((isset($_POST['editB']))?$id_edit:'').'|'.((isset($_POST['delB']))?$id_del:'').'|';
		
		// Update des groupes
		$req=$sql->query('SELECT id,acces FROM groupes ORDER BY id');
		for($i=1;$info=$sql->fetchAssoc($req);$i++) {
			$acces=$membres->removeAcces($info['acces'],$toremove);
			$acces=$membres->addAcces($acces,$groupe[$info['id']]);
			$sql->query('UPDATE groupes SET acces="'.$acces.'" WHERE id="'.$info['id'].'"');
		}
		header('location:gererforum.html');
		exit();
	}
	
    /**
     * Modifications des droits d'un forum
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function editDroits() {
		global $sql,$membres;
		$id			=	$_GET['id'];
		$id_read	=	$membres->acces['forum_'.$_GET['id'].'_read'];
		$id_reply	=	$membres->acces['forum_'.$_GET['id'].'_reply'];
		$id_start	=	$membres->acces['forum_'.$_GET['id'].'_start'];
		$id_edit	=	$membres->acces['forum_'.$_GET['id'].'_edit'];
		$id_del		=	$membres->acces['forum_'.$_GET['id'].'_del'];
		
		$toremove		= 	$id_read.'|'.$id_reply.'|'.$id_start.'|'.$id_edit.'|'.$id_del;
		$groupe['4']	=	((isset($_POST['readA']))?$id_read:'').'|'.((isset($_POST['replyA']))?$id_reply:'').'|'.((isset($_POST['startA']))?$id_start:'').'|'.((isset($_POST['editA']))?$id_edit:'').'|'.((isset($_POST['delA']))?$id_del:'');
		$groupe['3']	=	((isset($_POST['readM']))?$id_read:'').'|'.((isset($_POST['replyM']))?$id_reply:'').'|'.((isset($_POST['startM']))?$id_start:'').'|'.((isset($_POST['editM']))?$id_edit:'').'|'.((isset($_POST['delM']))?$id_del:'');
		$groupe['2']	=	((isset($_POST['readV']))?$id_read:'').'|'.((isset($_POST['replyV']))?$id_reply:'').'|'.((isset($_POST['startV']))?$id_start:'').'|'.((isset($_POST['editV']))?$id_edit:'').'|'.((isset($_POST['delV']))?$id_del:'');
		$groupe['1']	=	((isset($_POST['readB']))?$id_read:'').'|'.((isset($_POST['replyB']))?$id_reply:'').'|'.((isset($_POST['startB']))?$id_start:'').'|'.((isset($_POST['editB']))?$id_edit:'').'|'.((isset($_POST['delB']))?$id_del:'');

		// Update des groupes
		$req=$sql->query('SELECT id,acces FROM groupes ORDER BY id');
		for($i=1;$info=$sql->fetchAssoc($req);$i++) {
			$acces=$membres->removeAcces($info['acces'],$toremove);
			$acces=$membres->addAcces($acces,$groupe[$info['id']]);
			$sql->query('UPDATE groupes SET acces="'.$acces.'" WHERE id="'.$info['id'].'"');
		}
		header('location:gererforum.html');
		exit();
	}

    /**
     * Modification d'un forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations du forum  modifier
     */ 
	function editForum($post) {
		global $sql,$site;
		$info=$sql->fetchAssoc($sql->query('SELECT cat FROM mod_forum_forums WHERE id="'.$_GET['id'].'"'));
		
		// Si changement de catégorie, on le place en dernire position
		if($post['cat']!=$info['cat']) {
			$post['cat']=$site->clear4Sql($post['cat']);
			$info=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) ordre FROM mod_forum_forums WHERE cat="'.$post['cat'].'"'));
			$ordre='ordre="'.($info['ordre']+1).'",cat="'.$post['cat'].'",';
		} else $ordre='';
		$post['nom']=$site->clear4Sql($post['nom']);
		$post['descr']=$site->clear4Sql($post['descr']);
		
		$special=array();
		if(isset($post['news'])) $special[]='news';
		$special=',special="'.$site->clear4Sql(serialize($special)).'"';
		
		$sql->query('UPDATE mod_forum_forums SET '.$ordre.'titre="'.$post['nom'].'",descr="'.$post['descr'].'"'.$special.' WHERE id="'.$_GET['id'].'"');
		header('location:gererforum.html');
		exit();
	}

    /**
     * Supprime un forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations du forum  supprimer
     */ 
	function supprForum($moveto) {
		global $sql,$membres;
		$id			=	$_GET['id'];
		$id_read	=	$membres->acces['forum_'.$_GET['id'].'_read'];
		$id_reply	=	$membres->acces['forum_'.$_GET['id'].'_reply'];
		$id_start	=	$membres->acces['forum_'.$_GET['id'].'_start'];
		$id_edit	=	$membres->acces['forum_'.$_GET['id'].'_edit'];
		$id_del		=	$membres->acces['forum_'.$_GET['id'].'_del'];
		
		$toremove	= 	$id_read.'|'.$id_reply.'|'.$id_start.'|'.$id_edit.'|'.$id_del;

		// Update des groupes
		$req=$sql->query('SELECT id,acces FROM groupes ORDER BY id');
		for($i=1;$info=$sql->fetchAssoc($req);$i++) {
			$acces=$membres->removeAcces($info['acces'],$toremove);
			$sql->query('UPDATE groupes SET acces="'.$acces.'" WHERE id="'.$info['id'].'"');
		}
		// Déplacement des posts
		$sql->query('UPDATE mod_forum_posts SET forum_id="'.$moveto.'" WHERE forum_id="'.$id.'"');
		// Déplacement des sujets
		$sql->query('UPDATE mod_forum_topics SET forum_id="'.$moveto.'" WHERE forum_id="'.$id.'"');
		// Suppression des droits du forum
		$sql->query('DELETE FROM acces WHERE name LIKE "forum_'.$id.'_%"');
		// Suppression du forum
		$sql->query('DELETE FROM mod_forum_forums WHERE id="'.$id.'"');

		header('location:gererforum.html');
		exit();
	}

    /**
     * Vide un forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du forum  vider
     */ 
	function emptyForum($id) {
		global $sql;
		// Suppression des posts
		$sql->query('DELETE FROM mod_forum_posts WHERE forum_id="'.$id.'"');
		// Suppression des sujets
		$sql->query('DELETE FROM mod_forum_topics WHERE forum_id="'.$id.'"');

		$sql->query('UPDATE mod_forum_forums SET nb_topics=0, nb_posts=0, last_post_date="", last_poster_name="", last_poster_id=0, last_post="", last_post_id=0 WHERE id="'.$id.'"');
		
		header('location:gererforum.html');
		exit();
	}

    /**
     * Vote  un sondage
     *
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function vote() {
		global $sql,$membres,$site;
		if(!isset($_POST['choix'])) $site->error('Vous n\'avez pas slectionn un choix du sondage pour voter. Veuillez revenir en arrire et assurez vous de cliquer sur un des boutons radio  ct du choix pour lequel vous souhaitez voter.');
		$info=$sql->fetchAssoc($sql->query('SELECT poll,results FROM mod_poll,mod_forum_topics WHERE mod_forum_topics.id="'.$_GET['topic'].'" && mod_forum_topics.poll=mod_poll.id'));
		if(!empty($info['results'])) $results=unserialize($info['results']);
		else $results=array();
		if(!isset($results[(int)$_POST['choix']])) $results[(int)$_POST['choix']]=0;
		$results[(int)$_POST['choix']]++;
		$sql->query('INSERT INTO mod_poll_votes (id_poll,id_membre,choix) VALUES ("'.$info['poll'].'","'.$membres->infos('id').'","'.(int)$_POST['choix'].'")');
		$sql->query('UPDATE mod_poll SET votes=votes+1,results="'.serialize($results).'"');
		header("Cache-control: private, no-cache");
		header('location:'.$_SERVER['REQUEST_URI']);
		exit();
	}

    /**
     * Vrifie si le membre  dj vot au sondage
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sondage
	 * @return	boolean
     */ 
	function avote($poll) {
		global $sql,$membres;
		$vote=$sql->query('SELECT id FROM mod_poll_votes WHERE id_poll="'.$poll.'" && id_membre="'.$membres->infos('id').'"');
		if($sql->numRows($vote)>=1) return true;
		else return false;
	}
	
    /**
     * Affiche les rsultats du vote (sans voter)
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet du sondage
     */ 
	function showVotes($topic) {
		global $sql,$string;
		$info=$sql->fetchAssoc($sql->query('SELECT titre FROM mod_forum_topics WHERE id="'.$topic.'"'));
		header('location:'.$string->clean($info['titre']).'-t'.$topic.'-show.html');
	}

    /**
     * Affiche les choix du sondage (aprs avoir t voir les rsultats)
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Id du sujet du sondage
     */ 
	function showOptions($topic) {
		global $sql,$string;
		$info=$sql->fetchAssoc($sql->query('SELECT titre FROM mod_forum_topics WHERE id="'.$topic.'"'));
		header('location:'.$string->clean($info['titre']).'-t'.$topic.'.html');
	}
	
    /**
     * Mise  jour de la configuration du forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	array	Informations du forum  mettre  jour
     */ 
	function configForum($nbmess) {
		global $sql;
		$sql->query('UPDATE config SET value="'.$nbmess.'" WHERE name="forum_nbmess"');
		header('location:configuration.html');
	}
	
    /**
     * Création de la barre de navigation situe en haut du forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	Template	Template dans laquelle afficher la barre
	 * @param	interger	Niveau de navigation  afficher
	 * @param	interger	Si les boutons d'action doivent tre afficher ou pas
	 * @param	string		Si il s'agit d'un topic : ouvert ou ferm
	 * @param	interger	Pagination : nombre de pages
	 * @param	interger	Pagination : nombre d'lment par page
	 * @param	interger	Pagination :  quelle page il faut commencer
	 * @param	string		Si il s'agit d'un forum ou d'un topic
     */ 
	function makeHaut(&$template,$niveau,$barreactions=0,$etat='',$nbpages=NULL,$numtoview=NULL,$start=NULL,$type='f') {
		global $membres,$sql;
		$hautTemplate = new template("templates/".THEME."/",'keep');
		$hautTemplate->setVar("THEME",THEME);
		$hautTemplate->setFile('barre','forum/haut.html');
		
		/**
		 * Déclaration des blocks du template
		 * Ordre : Intrieur->Exterieur
		 */
		$hautTemplate->setBlock('barre','admin');
		$hautTemplate->setBlock('barre','navident');
		$hautTemplate->setBlock('barre','navnoident');
		$hautTemplate->setBlock('barre','levelone');
		$hautTemplate->setBlock('barre','leveltwo');
		$hautTemplate->setBlock('barre','open1');
		$hautTemplate->setBlock('barre','close1');
		$hautTemplate->setBlock('barre','pagesf.num');
		$hautTemplate->setBlock('barre','pagesf.first');
		$hautTemplate->setBlock('barre','pagesf.prev');
		$hautTemplate->setBlock('barre','pagesf.mm');
		$hautTemplate->setBlock('barre','pagesf.m');
		$hautTemplate->setBlock('barre','pagesf.c');
		$hautTemplate->setBlock('barre','pagesf.p');
		$hautTemplate->setBlock('barre','pagesf.pp');
		$hautTemplate->setBlock('barre','pagesf.next');
		$hautTemplate->setBlock('barre','pagesf.last');
		$hautTemplate->setBlock('barre','paginnationforum');
		$hautTemplate->setBlock('barre','pagest.num');
		$hautTemplate->setBlock('barre','pagest.first');
		$hautTemplate->setBlock('barre','pagest.prev');
		$hautTemplate->setBlock('barre','pagest.mm');
		$hautTemplate->setBlock('barre','pagest.m');
		$hautTemplate->setBlock('barre','pagest.c');
		$hautTemplate->setBlock('barre','pagest.p');
		$hautTemplate->setBlock('barre','pagest.pp');
		$hautTemplate->setBlock('barre','pagest.next');
		$hautTemplate->setBlock('barre','pagest.last');
		$hautTemplate->setBlock('barre','paginnationtopic');
		$hautTemplate->setBlock('barre','barreactions');
		
		/**
		 * Si le membre est identifi
		 */
		if($membres->infos('id')) {
			$hautTemplate->parse('navident', true);
			if($membres->infos('groupe')==4) $hautTemplate->parse('admin', true);
			$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_messages WHERE to_id="'.$membres->infos('id').'" && etat=0'));
			$hautTemplate->setVar(array(
				'mbrPseudo'		=>	$membres->infos('pseudo'),
				'mbrMessPv'		=>	$info['nb'],
				'mbrMessPvS'	=>	($info['nb']>1)?'s':''
			));
		} else $hautTemplate->parse('navnoident', true);
		
		/**
		 * Niveau de la racine  afficher
		 */
		if($niveau>=1) $hautTemplate->parse('levelone', true);
		if($niveau>=2) $hautTemplate->parse('leveltwo', true);
		
		/**
		 * Boutons  afficher ?
		 */
		if($barreactions==0) $hautTemplate->setVar('style','only');
		else $hautTemplate->parse('barreactions', true);
		
		/**
		 * Etat du sujet (boutons diffrents)
		 */
		if($etat=='open') $hautTemplate->parse('open1', true);
		else if($etat=='close') $hautTemplate->parse('close1', true);
		
		/**
		 * Paginnation du forum/sujet
		 */
		if(isset($nbpages,$numtoview,$start) && $nbpages>1) {
			$currpage=ceil(($start+$numtoview)/$numtoview);
			$this->makePages($hautTemplate,$nbpages,$currpage,$numtoview,$start,$type);
			if($type=='f') $hautTemplate->parse('paginnationforum', true);
			else $hautTemplate->parse('paginnationtopic', true);
		}
						
		$template->setVar('barrehaut',$hautTemplate->globalParse('parse','barre',true));
	}
	
    /**
     * Création de la barre de navigation situe en bas du forum
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	Template	Template dans laquelle afficher la barre
	 * @param	string		Si il s'agit d'un topic : ouvert ou ferm
	 * @param	interger	Pagination : nombre de pages
	 * @param	interger	Pagination : nombre d'lment par page
	 * @param	interger	Pagination :  quelle page il faut commencer
	 * @param	string		Si il s'agit d'un forum ou d'un topic
     */ 
	function makeBas(&$template,$etat='',$nbpages=NULL,$numtoview=NULL,$start=NULL,$type='f') {
		global $membres;
		$basTemplate = new template("templates/".THEME."/");
		$basTemplate->setVar("THEME",THEME);
		$basTemplate->setFile('barre','forum/bas.html');
		
		/**
		 * Déclaration des blocks du template
		 * Ordre : Intrieur->Exterieur
		 */
		$basTemplate->setBlock('barre','open2');
		$basTemplate->setBlock('barre','close2');
		$basTemplate->setBlock('barre','pagesf.num');
		$basTemplate->setBlock('barre','pagesf.first');
		$basTemplate->setBlock('barre','pagesf.prev');
		$basTemplate->setBlock('barre','pagesf.mm');
		$basTemplate->setBlock('barre','pagesf.m');
		$basTemplate->setBlock('barre','pagesf.c');
		$basTemplate->setBlock('barre','pagesf.p');
		$basTemplate->setBlock('barre','pagesf.pp');
		$basTemplate->setBlock('barre','pagesf.next');
		$basTemplate->setBlock('barre','pagesf.last');
		$basTemplate->setBlock('barre','paginnationforum');
		$basTemplate->setBlock('barre','pagest.num');
		$basTemplate->setBlock('barre','pagest.first');
		$basTemplate->setBlock('barre','pagest.prev');
		$basTemplate->setBlock('barre','pagest.mm');
		$basTemplate->setBlock('barre','pagest.m');
		$basTemplate->setBlock('barre','pagest.c');
		$basTemplate->setBlock('barre','pagest.p');
		$basTemplate->setBlock('barre','pagest.pp');
		$basTemplate->setBlock('barre','pagest.next');
		$basTemplate->setBlock('barre','pagest.last');
		$basTemplate->setBlock('barre','paginnationtopic');
		
		/**
		 * Etat du sujet (boutons diffrents)
		 */
		if($etat=='open') $basTemplate->parse('open2', true);
		else if($etat=='close') $basTemplate->parse('close2', true);

		/**
		 * Paginnation du forum/sujet
		 */
		if(isset($nbpages,$numtoview,$start) && $nbpages>1) {
			$currpage=ceil(($start+$numtoview)/$numtoview);
			$this->makePages($basTemplate,$nbpages,$currpage,$numtoview,$start,$type);
			if($type=='f') $basTemplate->parse('paginnationforum', true);
			else $basTemplate->parse('paginnationtopic', true);
		}

		$template->setVar('barrebas',$basTemplate->globalParse('parse','barre',true));
	}
	
    /**
     * Création de la barre des options spciales des forums
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	Template	Template dans laquelle afficher la barre
	 * @param	interger	Tableau des options spciales
     */ 
	function makeSpecial(&$template,$special) {
		global $site;
		$special=unserialize($special);
		
		//$this->addJs('include/js/bbcode.inc.js');
		$specTemplate = new template('templates/'.THEME.'/');
		$specTemplate->setFile('special','forum/special.html');
		
		$specTemplate->setBlock('special','newsoption');
		$specTemplate->setBlock('special','news');
		
		if(in_array('news',$special)) {
			$specTemplate->parse('news');
			$cats=is_array($site->config('news_cat'))?unserialize($site->config('news_cat')):array('Aucune');
			foreach($cats as $i=>$var) {
				$specTemplate->setVar(array(
					'value'=>$i,
					'text'=>$var
				));
				if(isset($_POST['news']) && $_POST['news']==$i) $specTemplate->setVar('selected',' selected="selected"');
				else $specTemplate->setVar('selected','');
				$specTemplate->parse('newsoption',true);
				$specTemplate->clearVar('text','selected');
			}
		}
		$template->setVar('special',$specTemplate->globalParse('parse','special',true));
	}
	
	function isSpam($message) {
		global $site;
		$spamwords=explode(',',$site->config('forum_spamwords'));
		foreach ($spamwords as $word) {
			if(eregi($word,$message)) return true;
		}
		return false;
	}

}
?>