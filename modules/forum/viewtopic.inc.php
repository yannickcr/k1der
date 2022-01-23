<?php
$forum = new forum();
$bbcode = new bbcode;

/**
 * Construction de la page
 */
$template->setFile('centre','forum/view_topic.html');  
$this->noUse(array('gauche','droite'));
$site->addCss('templates/'.THEME.'/forum/style.css');
$site->addJs('modules/forum/js/forum.inc.js');

/**
 * Si lecture des nouveaux posts
 */
if(isset($_GET['newpost'])) {
	$unread=unserialize(stripslashes($_COOKIE['forum_unread']));
	$postid=$unread[$_GET['topic']];
	$req=$sql->query('
		SELECT 
			p.id,
			t.titre 
		FROM 
			mod_forum_posts p LEFT JOIN mod_forum_topics t ON p.topic_id=t.id
		WHERE 
			p.topic_id="'.$_GET['topic'].'"
		ORDER BY 
			p.post_date ASC
	');
	for($i=1;$info=$sql->fetchAssoc($req);$i++) {
		$posts[$info['id']]=$i;
		$titre=$info['titre'];
	}
	$start=((ceil($posts[$postid]/$site->config('forum_nbmess'))-1)*$site->config('forum_nbmess'));
	if($start>0) header('location:'.$string->clean($titre).'-t'.$_GET['topic'].'-start'.$start.'.html#post'.$postid);
	else header('location:'.$string->clean($titre).'-t'.$_GET['topic'].'.html#post'.$postid);
}

/**
 * Action envoi formulaire
 */
if($this->action('vote')) $forum->vote();
if($this->action('showVotes')) $forum->showVotes($_GET['topic']);
if($this->action('showOptions')) $forum->showOptions($_GET['topic']);

/**
 * Dtermination du message de dpart et le nombre  afficher
 */
if(!isset($_GET['start'])) $start=0;
else $start=$_GET['start'];
if($start!=0) define("FIRST",false);
$numtoview=$site->config('forum_nbmess');

/**
 * Informations des sujets et des réponses
 */
$req=$sql->query('
	SELECT 
		c.id			AS	catId,
		c.nom			AS	catTitre,
		f.id			AS	forumId,
		f.titre			AS	forumTitre,
		t.id			AS	topicId,
		t.titre			AS	topicTitre,
		t.posts			AS	topicNbPosts,
		t.descr			AS	topicDescr,
		t.id			AS	topicId,
		t.poll			AS	topicPoll,
		t.etat 			AS	topicEtat,
		t.pinned		AS	topicPinned,
		p.id			AS	postId,
		p.post			AS	postPost,
		p.auteur_id		AS	postAuteurId,
		p.auteur_name	AS	postAuteurName,
		p.post_date		AS	postDate,
		m.id			AS	membreId,
		m.avatar		AS	membreAvatar,
		m.mail			AS	membreMail,
		m.aim			AS	membreAim,
		m.yahoo			AS	membreYahoo,
		m.msn			AS	membreMsn,
		m.icq			AS	membreIcq,
		m.skype			AS	membreSkype,
		m.xfire			AS	membreXfire,
		m.www			AS	membreWww,
		m.natio			AS	membreNatio,
		m.date_ins		AS	membreDateIns,
		m.posts			AS	membreNbPosts,
		m.part			AS	membrePart,
		m.date_nes		AS	membreDateNes,
		m.signature		AS	membreSignature
	FROM 
		mod_forum_posts p 
			LEFT JOIN mod_forum_topics t ON p.topic_id=t.id 
			LEFT JOIN mod_membres m ON p.auteur_id=m.id,
		mod_forum_forums f 
			RIGHT JOIN mod_forum_topics top ON top.forum_id=f.id
			LEFT JOIN mod_forum_cats c ON f.cat=c.id
	WHERE 
		p.topic_id="'.$_GET['topic'].'"
	GROUP BY 
		p.id 
	ORDER BY 
		p.post_date 
	LIMIT 
		'.$start.','.$numtoview.'
');

$reqFo=$sql->query('
	SELECT 
		c.id			AS	catId,
		c.nom			AS	catTitre,
		f.id			AS	forumId,
		f.titre			AS	forumTitre,
		t.id			AS	topicId,
		t.titre			AS	topicTitre,
		t.posts			AS	topicNbPosts,
		t.descr			AS	topicDescr,
		t.id			AS	topicId,
		t.poll			AS	topicPoll,
		t.etat 			AS	topicEtat,
		t.pinned		AS	topicPinned
	FROM 
		mod_forum_forums f 
			RIGHT JOIN mod_forum_topics t ON t.forum_id=f.id
			LEFT JOIN mod_forum_cats c ON f.cat=c.id
	WHERE 
		t.id="'.$_GET['topic'].'"
');
/**
 * Premire requete
 */
$info=$sql->fetchAssoc($req);
$infoFo=$sql->fetchAssoc($reqFo);

$forumId=$infoFo['forumId'];
$topicEtat=$infoFo['topicEtat'];
$topicPin=$infoFo['topicPinned'];
$topicNbPosts=$infoFo['topicNbPosts'];
$similarTitre=$infoFo['topicTitre'];
$similarId=$infoFo['topicId'];
$nbpages=ceil(($topicNbPosts+1)/$numtoview);
$site->addToTitle(' - Forum - '.$string->clean($infoFo['topicTitre'],'htmlentities'));

// Vérification des droits d'accès
if(!$membres->verifAcces('forum_'.$forumId.'_read')) {
		$message='<p>Vous n\'avez pas l\'autorisation de lire ce sujet.</p>';
		if(!$membres->infos('id')) {
			$message.='<p>Si vous possdez un compte vous pouvez vous connecter avec le formulaire ci-contre.</p><p>Dans le cas contraire, vous pouvez créer un nouveau compte <a href="membres/inscription.html">en cliquant ici</a>.</p>';
		}
	$site->error($message);
}

/**
 * Déclaration des blocks du template
 * Ordre : Intrieur->Exterieur
 */
$template->setBlock('centre','descr');
$template->setBlock('centre','tedit');
$template->setBlock('centre','edit');
$template->setBlock('centre','suppr');

$template->setBlock('centre','mp');
$template->setBlock('centre','email');
$template->setBlock('centre','aim');
$template->setBlock('centre','yahoo');
$template->setBlock('centre','im');
$template->setBlock('centre','msn');
$template->setBlock('centre','icq');
$template->setBlock('centre','skype');
$template->setBlock('centre','xfire');
$template->setBlock('centre','www');
$template->setBlock('centre','hr');
$template->setBlock('centre','auteur.sign');
$template->setBlock('centre','buttons');
$template->setBlock('centre','auteur.profil');
$template->setBlock('centre','auteur.profil2');
$template->setBlock('centre','avatar');
$template->setBlock('centre','pipes');
$template->setBlock('centre','level');
$template->setBlock('centre','age');
$template->setBlock('centre','auteur.infos');
$template->setBlock('centre','liveedit');

$template->setBlock('centre','forum.posts');

$template->setBlock('centre','visiteur');
$template->setBlock('centre','topicpage.un');
$template->setBlock('centre','topicpage.deux');
$template->setBlock('centre','topicpage.trois');
$template->setBlock('centre','topicpage.quatre');
$template->setBlock('centre','similar');
$template->setBlock('centre','similartable');

$template->setBlock('centre','choix');
$template->setBlock('centre','poll');
$template->setBlock('centre','results');
$template->setBlock('centre','showoptions');
$template->setBlock('centre','showmessage');
$template->setBlock('centre','poll2');

$template->setBlock('centre','modo');

$template->setBlock('centre','open3');

/**
 * Construction de la barre du haut et de celle du bas
 */
$forum->makeHaut($template,3,1,$topicEtat,$nbpages,$numtoview,$start,'t');
$forum->makeBas($template,$topicEtat,$nbpages,$numtoview,$start,'t');


/**
 * Remplacement des valeurs générales
 */
$template->setVar(array(
		'catTitre'			=>	$infoFo['catTitre'],
		'catLinkTitre'		=>	$string->clean($infoFo['catTitre']),
		'catId'				=>	$infoFo['catId'],
		'forumTitre'		=>	$infoFo['forumTitre'],
		'forumLinkTitre'	=>	$string->clean($infoFo['forumTitre']),
		'forumId'			=>	$infoFo['forumId']
));
if(!empty($infoFo['topicDescr'])) {
	$template->setVar('forum.descr',$infoFo['topicDescr']);
	$template->parse('descr', true);
}


/**
 * Si il s'agit d'un sondage
 */
if($infoFo['topicPoll']!=0 && !$forum->avote($infoFo['topicPoll']) && !isset($_GET['show']) && $membres->infos('id')) {
	$s_info=$sql->fetchAssoc($sql->query('
		SELECT 
			quest	AS	pollQuest,
			choix	AS	pollChoix
		FROM 
			mod_poll 
		WHERE 
			id="'.$infoFo['topicPoll'].'"
	'));
	$template->parse('poll', true);
	$template->setVar('pollQuestion',$string->clean($s_info['pollQuest'],'htmlentities'));
	$choix=unserialize($s_info['pollChoix']);
	foreach($choix as $i=>$var) {
		$template->setVar('poll.id',$i);
		$template->setVar('poll.choix',$string->clean($var,'htmlentities'));
		$template->parse('choix', true);
	}
	$template->setVar('captionClass','sondage');
} else if($infoFo['topicPoll']!=0 && ($forum->avote($infoFo['topicPoll']) || isset($_GET['show']) || !$membres->infos('id'))) {
	$s_info=$sql->fetchAssoc($sql->query('
		SELECT 
			quest	AS	pollQuest,
			choix	AS	pollChoix,
			results	AS	pollResults,
			votes	AS	pollVotes 
		FROM 
			mod_poll 
		WHERE 
			id="'.$infoFo['topicPoll'].'"
	'));
	$template->parse('poll2', true);
	$template->setVar(array(
		'pollQuestion'	=>	$string->clean($s_info['pollQuest'],'htmlentities'),
		'pollVotes'		=>	$s_info['pollVotes']
	));
	$choix=unserialize($s_info['pollChoix']);
	
	if(empty($s_info['pollResults'])) $s_info['pollResults']= serialize(array());
	$results=unserialize($s_info['pollResults']);
	foreach($choix as $i=>$var) {
		if(!isset($results[$i])) $results[$i]=0;
		if($s_info['pollVotes']==0) $percent=0;
		else $percent=round(($results[$i]/$s_info['pollVotes'])*100,2);
		
		$template->setVar(array(
			'pollId'		=>	$i,
			'pollChoix'		=>	$string->clean($var,'htmlentities'),
			'pollResult'	=>	$results[$i],
			'pollWidth'		=>	$percent*2,
			'pollPercent'	=>	$percent.'%'
		));
		$template->parse('results', true);
	}
	if(isset($_GET['show']) && $membres->infos('id')) $template->parse('showoptions', true);
	else if(!$membres->infos('id')) $template->parse('showmessage', true);
	$template->setVar('captionClass','sondage');
} else $template->setVar('captionClass','sujet');

/**
 * Listage des réponses
 */
do {

	/**
	 * Niveau du membre
	 */	
	$level=unserialize($site->config('membres_level'));
	foreach($level as $i=>$var) {
		if($info['membrePart']>=$i) {
			$designation=$var[0];
			for ($k=0;$k<$var[2];$k++) {
				$template->setVar('membrePipe',$var[1]);
				$template->parse('pipes', true);
			}
		}
	}
	if(isset($designation)) {
		$template->setVar('membreDesignation',$designation);
		$template->parse('level', true);
	}

	$signature=$bbcode->BBCodeToHtml($info['membreSignature']);
	$template->setVar(array(
		'topicTitre'		=>	$string->clean($infoFo['topicTitre'],'htmlentities'),
		'topicLinkTitre'	=>	$string->clean($infoFo['topicTitre']),
		'topicDescr'		=>	$string->clean($infoFo['topicDescr'],'htmlentities'),
		'topicId'			=>	$infoFo['topicId'],
		'postAuteurName'	=>	$info['postAuteurName'],
		'postAuteurId'		=>	$info['postAuteurId'],
		'postDate'			=>	$string->formatDate('%A %d %B %Y',$info['postDate'],true),
		'postHeure'			=>	$string->formatDate('%H:%M',$info['postDate']),
		'postPost'			=>	$bbcode->stripBBCode($bbcode->BBCodeToHtml($string->clean($info['postPost'],'htmlentities'))),
		'postId'			=>	$info['postId'],
		'membreAvatar'		=>	$membres->getAvatar($info['membreAvatar'],$info['membreMail']),
		'membreSignature'	=>	$signature.'<br />',
		'membreNbPosts'		=>	$info['membreNbPosts'],
		'membreDateIns'		=>	$string->formatDate('%d %B %Y',$info['membreDateIns'],true),
		'membreDateNes'		=>	$membres->getAge($info['membreDateNes']),
		'membreNatio'		=>	$info['membreNatio']
	));

	/**
	 * Auteur connu, affichage de ses infos : signature,avatar,etc...
	 */
	if($info['postAuteurId']!=0 && $info['membreId']!='') {
		$template->parse('auteur.infos');
		$template->parse('auteur.profil');
		$template->parse('buttons');
		$template->parse('mp');
		//$template->parse('email');
		if(!empty($signature)) {
			$template->parse('auteur.sign');
			$template->parse('hr');
		}
		if($info['membreAvatar']) $template->parse('avatar');
		if($info['membreDateNes']!=0) $template->parse('age');
		if($membres->infos('id')==$info['postAuteurId'] && !defined("FIRST") && $infoFo['topicPoll']!=0) $template->parse('tedit', true);
		else if($membres->verifAcces('forum_'.$forumId.'_edit') && !defined("FIRST") && $infoFo['topicPoll']!=0) $template->parse('tedit', true);
		
		else if($membres->infos('id')==$info['postAuteurId'] && !defined("FIRST")) $template->parse('tedit', true);
		else if($membres->verifAcces('forum_'.$forumId.'_edit') && !defined("FIRST")) $template->parse('tedit', true);
		
		else if($membres->infos('id')==$info['postAuteurId'] && defined("FIRST")) $template->parse('edit', true);
		else if($membres->verifAcces('forum_'.$forumId.'_edit') && defined("FIRST")) $template->parse('edit', true);
		if($membres->verifAcces('forum_'.$forumId.'_del') && defined("FIRST")) $template->parse('suppr', true);
		
		if($membres->infos('id')==$info['postAuteurId']) $template->parse('liveedit', true);
		else if($membres->verifAcces('forum_'.$forumId.'_edit')) $template->parse('liveedit', true);

		if($forum->unRead($infoFo['topicId'])!=false) $forum->setRead($infoFo['topicId']);
		if(!empty($info['membreAim'])) {
			$template->setVar('aimNick',$info['membreAim']);
			$template->parse('aim', true);
		}
		if(!empty($info['membreSkype'])) {
			$template->setVar('skypeNick',$info['membreSkype']);
			$template->parse('skype', true);
		}
		if(!empty($info['membreXfire'])) {
			$template->setVar('xfireNick',$info['membreXfire']);
			$template->parse('xfire', true);
		}
		if(!empty($info['membreYahoo'])) {
			$template->setVar('yahooNick',$info['membreYahoo']);
			$template->parse('yahoo', true);
		}
		if(!empty($info['membreMsn'])) $template->parse('msn', true);
		if(!empty($info['membreIcq'])) {
			$template->setVar('icqId',$info['membreIcq']);
			$template->parse('icq', true);
		}
		if(!empty($info['membreWww'])) {
			$template->setVar('sitePerso',str_replace('http://','',$info['membreWww']));
			$template->parse('www', true);
		}
	/**
	 * Auteur inconnu, affichage des boutons de modifications pour les admins
	 */
	} else {
		if($membres->verifAcces('forum_'.$forumId.'_edit') && !defined("FIRST") && $infoFo['topicPoll']!=0) $template->parse('tedit', true);
		else if($membres->verifAcces('forum_'.$forumId.'_edit') && !defined("FIRST")) $template->parse('tedit', true);
		else if($membres->verifAcces('forum_'.$forumId.'_edit') && defined("FIRST")) $template->parse('edit', true);
		if($membres->verifAcces('forum_'.$forumId.'_del') && defined("FIRST")) $template->parse('suppr', true);
		$template->parse('auteur.profil2');

		if($membres->verifAcces('forum_'.$forumId.'_edit')) $template->parse('liveedit', true);

	}
	$template->parse('forum.posts', true);
	$template->unsetVar(array(
		'level',
		'pipes',
		'age',
		'edit',
		'tedit',
		'suppr',
		'mp',
		'email',
		'aim',
		'yahoo',
		'skype',
		'xfire',
		'msn',
		'icq',
		'www',
		'hr',
		'auteur.infos',
		'auteur.sign',
		'auteur.profil',
		'auteur.profil2',
		'forum.post-auteur',
		'forum.post-auteur-id',
		'avatar',
		'liveedit'
	));
	if(!defined("FIRST")) define("FIRST",false);
	
} while($info=$sql->fetchAssoc($req)); // Fin boucle topics

/**
 * Affichage du champ pseudo si l'utilisateur n'est pas loggu
 */
if(!$membres->infos('id')) $template->parse('visiteur', true);

/**
 * Sujets similaires
 */
$req=$forum->similar($similarTitre,$similarId);
if($sql->numRows()!=0) $template->parse('similartable', true);
while($info=$sql->fetchAssoc($req)) {

	$template->setVar(array(
		'similarTopicId'				=>	$info['id'],
		'similarTopicTitre'				=>	$string->clean($info['titre'],'htmlentities'),
		'similarTopicLinkTitre'			=>	$string->clean($info['titre']),
		'similarTopicDescr'				=>	$string->clean($info['descr'],'htmlentities'),
		'similarTopicStarterId'			=>	$info['starter_id'],
		'similarTopicStarterName'		=>	$info['starter_name'],
		'similarTopicNbPosts'			=>	$info['posts'],
		'similarTopicViews'				=>	$info['views'],
		'similarTopicLastPostDate'		=>	$string->formatDate('%A %d %B %Y',$info['last_post'],true),
		'similarTopicLastPostHeure'		=>	$string->formatDate('%H:%M',$info['last_post']),
		'similarTopicLastPosterId'		=>	$info['last_poster_id'],
		'similarTopicLastPosterName'	=>	$info['last_poster_name']
	));

	$nbpagestopics=ceil(($info['posts']+1)/$numtoview);
	$forum->makeTopicsPages($nbpagestopics,$numtoview);
	
	$template->parse('similar', true);
	$template->clearVar(array('topicpage.un','topicpage.deux','topicpage.trois','topicpage.quatre'));
}

/**
 * Options de modération
 */
if($membres->verifAcces('forum_'.$forumId.'_del')) $template->parse('modo', true);

/**
 * Sujet Ouvert ou Ferm ?
 */
if($topicEtat=='open') {
	$template->parse('open2', true);
	$template->parse('open3', true);
	$template->setVar(array(
		'openClose'=>'Fermer',
		'openCloseValue'=>'close'
	));
} else {
	$template->parse('close2', true);
	$template->setVar(array(
		'openClose'=>'Rouvrir',
		'openCloseValue'=>'open'
	));
}

/**
 * Sujet Epingl ou pas ?
 */
if($topicPin==1) {
	$template->setVar(array(
		'pinUnpin'=>'Dspingler',
		'pinUnpinValue'=>'unpin'
	));
} else {
	$template->setVar(array(
		'pinUnpin'=>'Epingler',
		'pinUnpinValue'=>'pin'
	));
}

/**
 * Incrmentation du nombre de lectures
 */
$sql->query('UPDATE mod_forum_topics SET views=views+1 WHERE id="'.$_GET['topic'].'"');
?>