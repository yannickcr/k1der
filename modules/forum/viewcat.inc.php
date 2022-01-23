<?php
$forum = new forum();

/**
 * Construction de la page
 */
$template->setFile('centre','forum/view_cat.html');  
$this->noUse(array('gauche','droite'));
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
$template->setBlock('centre','noforum');

/**
 * Construction de la barre du haut
 */
$forum->makeHaut($template,1,0);

/**
 * Informations de la catégorie
 */
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		id,
		nom 
	FROM 
		mod_forum_cats 
	WHERE 
		id="'.$_GET['cat'].'"
'));

$template->setVar(array(
	'catTitre'		=>	$info['nom'],
	'catLinkTitre'	=>	$string->clean($info['nom']),
	'catId'			=>	$info['id']
));

$site->addToTitle(' - Forum - '.$string->clean($info['nom'],'htmlentities'));

/**
 * Listage des forums
 */
$req=$sql->query('
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
		cat="'.$_GET['cat'].'" 
	ORDER BY 
		ordre
');

$nbforums=0;
while($info=$sql->fetchAssoc($req)) {

	/**
	 * Infos du forum
	 */
	$template->setVar(array(
		'forumTitre'			=>	$string->clean($info['titre'],'htmlentities'),
   		'forumLinkTitre'		=>	$string->clean($info['titre']),
  		'forumId'				=>	$info['id'],
   		'forumDescr'			=>	$string->clean($info['descr'],'htmlentities'),
   		'forumNbTopics'			=>	$info['nb_topics'],
   		'forumNbPosts'			=>	$info['nb_posts'],
		'forumLastPostDate'		=>	$string->formatDate('%A %d %B %Y',$info['last_post_date'],true),
		'forumLastPostHeure'	=>	$string->formatDate('%H:%M',$info['last_post_date']),
		'forumLastPost'			=>	$string->clean($info['last_post'],'htmlentities'),
		'forumLinkLastPost'		=>	$string->clean($info['last_post']),
		'forumLastPosterId'		=>	$info['last_poster_id'],
		'forumLastPosterName'	=>	$info['last_poster_name'],
		'forumLastPostId'		=>	$info['last_post_id']
	));

	/**
	 * Si il n'y a aucun sujet dans le forum
	 */	
	if($info['nb_topics']==0) {
		$template->setVar('forumLastPostDate','---');
		$template->setVar('forumLastPost','---');
		$template->setVar('forumLastPosterName','---');
	/**
	 * Sinon on affiche les liens vers le dernier sujet/posteur
	 */
	} else {
		$template->parse('last', true);
		$template->parse('last2', true);
		if($info['last_poster_id']!=0) {
			$template->parse('last3', true);
			$template->parse('last4', true);
		}
	}
	
	/**
	 * Si le membre a accès en lecture au forum, on l'affiche et on incrmente le compeur
	 */
	if($membres->verifAcces('forum_'.$info['id'].'_read')) {
   		$template->parse('forum', true);
		$nbforums++;
	}
	$template->clearVar(array('last','last2','last3','last4')); // RAZ des liens (sinon ils s'affichent aussi pour le forum suivant)
} // Fin boucle forums

	/**
	 * Si le nombre de forums  afficher est nul alors on affiche un bloc diffrent
	 */
	if($nbforums==0) $template->parse('noforum', true);
?>