<?php
$forum = new forum();

/**
 * Construction de la page
 */
$template->setFile('centre','forum/index.html');  
$this->noUse(array('gauche','droite'));
$site->addToTitle(' - Forum');
$site->addCss('templates/'.THEME.'/forum/style.css');

/**
 * Déclaration des blocks du template
 * Ordre : Intrieur->Exterieur
 */
$template->setBlock('centre','last');
$template->setBlock('centre','last2');
$template->setBlock('centre','last3');
$template->setBlock('centre','last4');
$template->setBlock('centre','forum');
$template->setBlock('centre','cat');

/**
 * Construction de la barre du haut
 */
$forum->makeHaut($template,0,0);

/**
 * Listage des catégories
 */
$req=$sql->query('
	SELECT 
		id,
		nom 
	FROM 
		mod_forum_cats 
	ORDER BY 
		ordre
');

while($info=$sql->fetchAssoc($req)) {

	/**
	 * Infos de la catégorie
	 */
	$template->setVar(array(
		'catTitre'=>$info['nom'],
		'catLinkTitre'=>$string->clean($info['nom']),
		'catId'=>$info['id']
	));

	/**
	 * Listage des forums
	 */
	$sub_req=$sql->query('
		SELECT 
			id,
			titre,
			descr,
			nb_topics,
			nb_posts,
			last_post_date,
			last_post,
			last_poster_name,
			last_poster_id,
			last_post_id 
		FROM 
			mod_forum_forums 
		WHERE 
			cat="'.$info['id'].'" 
		ORDER BY 
			ordre
	');
	
	$nbForums=0;
	while($sub_info=$sql->fetchAssoc($sub_req)) {

		/**
		 * Infos du forum
		 */
		$template->setVar(array(
			'forumTitre'			=>	$sub_info['titre'],
			'forumLinkTitre'		=>	$string->clean($sub_info['titre']),
			'forumId'				=>	$sub_info['id'],
			'forumDescr'			=>	$sub_info['descr'],
			'forumNbTopics'			=>	$sub_info['nb_topics'],
			'forumNbPosts'			=>	$sub_info['nb_posts'],
			'forumLastPostDate'		=>	$string->formatDate('%A %d %B %Y',$sub_info['last_post_date'],true),
			'forumLastPostHeure'	=>	$string->formatDate('%H:%M',$sub_info['last_post_date']),
			'forumLastPost'			=>	$string->clean($sub_info['last_post'],'htmlentities'),
			'forumLinkLastPost'		=>	$string->clean($sub_info['last_post']),
			'forumLastPostId'		=>	$sub_info['last_post_id'],
			'forumLastPosterName'	=>	$sub_info['last_poster_name'],
			'forumLastPosterId'		=>	$sub_info['last_poster_id']
		));
				
		/**
		 * Si il n'y a aucun sujet dans le forum
		 */
		if($sub_info['nb_topics']==0) {
			$template->setVar(array(
				'forumLastPostDate'		=>	'---',
				'forumLastPostHeure'	=>	'---',
				'forumLastPost'			=>	'---',
				'forumLastPosterName'	=>	'---'
			));
		/**
		 * Sinon on affiche les liens vers le dernier sujet/posteur
		 */
		} else {
			$template->parse('last', true);
			$template->parse('last2', true);
			if($sub_info['last_poster_id']!=0) {
				$template->parse('last3', true);
				$template->parse('last4', true);
			}
		}
		
		/**
		 * Si le membre a accès en lecture au forum, on l'affiche et on incrmente le compeur
		 */
	   	if($membres->verifAcces('forum_'.$sub_info['id'].'_read')) {
	   		$template->parse('forum', true);
			$nbForums++;
		}
		$template->clearVar(array('last','last2','last3','last4')); // RAZ des liens (sinon ils s'affichent aussi pour le forum suivant)
	} // Fin boucle forums
	
	/**
	 * Si le nombre de forums  afficher dans la catégorie est >0 alors on affiche la catégorie
	 */
	if($nbForums>0) $template->parse('cat', true);
	
	$template->clearVar('forum');  // RAZ des forums (sinon ils s'affichent aussi pour la catégorie suivante)
} // Fin boucle catégories
?>