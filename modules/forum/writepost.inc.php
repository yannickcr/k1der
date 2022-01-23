<?php
$forum = new forum();

$bbcode = new bbcode;

/**
 * Informations sur le sujet et le forum
 */
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		mod_forum_cats.id			AS	catId,
		mod_forum_cats.nom			AS	catTitre,
		mod_forum_forums.id			AS	forumId,
		mod_forum_forums.titre		AS	forumTitre,
		mod_forum_topics.titre		AS	topicTitre
	FROM 
		mod_forum_topics,
		mod_forum_forums,
		mod_forum_cats
	WHERE 
		mod_forum_topics.id="'.$_GET['topic'].'" && 
		mod_forum_topics.forum_id=mod_forum_forums.id && 
		mod_forum_forums.cat=mod_forum_cats.id
'));


/**
 * Vérification des droits d'accès
 */
if(!$membres->verifAcces('forum_'.$info['forumId'].'_reply')) {
		$message='<p>Vous n\'avez pas l\'autorisation de rpondre  ce sujet.</p>';
		if(!$membres->infos('id')) {
			$message.='<p>Si vous possédez un compte vous pouvez vous connecter avec le formulaire ci-contre.</p><p>Dans le cas contraire, vous pouvez créer un nouveau compte <a href="membres/inscription.html">en cliquant ici</a>.</p>';
		}
	$site->error($message);
}

/**
 * Construction de la page
 */
$template->setFile('centre','forum/write_post.html');  
$this->noUse(array('gauche','droite'));
$site->addCss('templates/'.THEME.'/forum/style.css');

/**
 * Déclaration des blocks du template
 * Ordre : Intrieur->Exterieur
 */
$template->setBlock('centre','visiteur');
$template->setBlock('centre','forum.smileys');
$template->setBlock('centre','auteur.profil');
$template->setBlock('centre','auteur.profil2');
$template->setBlock('centre','forum.posts');

/**
 * Récupération des informations du message si il s'agit d'une édition
 */
if(isset($_GET['edit'])) $postInfos=$forum->getPostInfos($_GET['edit']);
if(isset($_GET['edit']) && ($postInfos['auteur_id']!=$membres->infos('id') || $postInfos['auteur_id']==0)  && !$membres->verifAcces('forum_'.$info['forumId'].'_edit')) $site->error('<p>Vous n\'avez pas l\'autorisation d\'éditer ce post.</p>');

/**
 * Action envoi formulaire
 */
if($this->action('reply')) $forum->reply($_GET['topic']);
if($this->action('preview'))  $forum->preview();
if($this->action('quote')) $forum->quote($_GET['quote']);
if($this->action('editReply')) $forum->editReply($_GET['edit']);
if($this->action('edit')) $template->setVar('bbmessage',$string->clean($postInfos['post'],'htmlentities'));

/**
 * Construction de la barre du haut et de celle de mise en forme
 */
$forum->makeHaut($template,2,0);
$site->barreMiseEnForme($template);
$site->addToTitle(' - Forum - '.$info['catTitre'].' - '.$info['forumTitre'].' - '.$string->clean($info['topicTitre'],'htmlentities'));

/**
 * Remplacement des valeurs générales
 */
$template->setVar(array(
	'catTitre'					=>	$info['catTitre'],
	'catLinkTitre'				=>	$string->clean($info['catTitre']),
	'catId'						=>	$info['catId'],
	'forumTitre'				=>	$string->clean($info['forumTitre'],'htmlentities'),
	'forumLinkTitre'			=>	$string->clean($info['forumTitre']),
	'forumId'					=>	$info['forumId'],
	'topicTitre'				=>	$string->clean($info['topicTitre'],'htmlentities')
));


/**
 * Listage des smileys
 */
$smileys=unserialize($site->config['smileys']);
foreach($smileys as $i => $var) {
   $template->setVar('smiley',htmlspecialchars(addslashes($i)));
   $template->setVar('alt',htmlentities($i));
   $template->setVar('image',$var);
   $template->parse('forum.smileys', true);
}

/**
 * Affichage du champ pseudo si l'utilisateur n'est pas loggu
 */
if(!$membres->infos('id')) $template->parse('visiteur', true);

/**
 * Listage des messages prcdents
 */
$req=$sql->query('
	SELECT 
		mod_forum_posts.post		AS	postPost,
		mod_forum_posts.auteur_id	AS	postAuteurId,
		mod_forum_posts.auteur_name	AS	postAuteurName,
		mod_forum_posts.post_date	AS	postDate
	FROM 
		mod_forum_posts,
		mod_forum_topics
	WHERE 
		mod_forum_topics.id="'.$_GET['topic'].'" && 
		mod_forum_topics.id=mod_forum_posts.topic_id 
	ORDER BY 
		postDate DESC 
	LIMIT 
		0,10
');


while($info=$sql->fetchAssoc($req)) {

	$template->setVar(array(
		'postAuteurName'	=>	$info['postAuteurName'],
		'postAuteurId'		=>	$info['postAuteurId'],
		'postDate'			=>	$string->formatDate('%A %d %B %Y',$info['postDate'],true),
		'postHeure'			=>	$string->formatDate('%H:%M',$info['postDate']),
		'postPost'			=>	$bbcode->stripBBCode($bbcode->BBCodeToHtml($string->clean($info['postPost'],'htmlentities')))
	));
	
	if($info['postAuteurId']!=0) $template->parse('auteur.profil');
	else $template->parse('auteur.profil2');

	$template->parse('forum.posts', true);
	
	$template->unsetVar(array('auteur.profil','auteur.profil2'));
	
}
?>