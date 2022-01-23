<?php
/**
 * 1. Instanciation d'objets
 */
$galeries = new galeries();
$utils = new utils();
$bbcode = new bbcode;

/**
 * 2. Actions
 */
if($this->action('editComm')) $erreur=$galeries->editComm($_GET['id'],$_GET['photo'],$_GET['edit'],$_POST['message'],$_POST['note']);
else if($this->action('addComm')) $erreur=$galeries->addComm($_GET['id'],$_GET['photo'],(($membres->infos('id'))?$membres->infos('pseudo'):$_POST['pseudo']),$_POST['message'],$_POST['note']);
else if($this->action('delComm')) $erreur=$galeries->delComm($_GET['id'],$_GET['photo'],$_GET['del']);

/**
 * 3. Récupération des données
 */

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','galeries/photo.html');
$site->addToTitle(' - Galeries');
$site->addCss('templates/'.THEME.'/galeries/style.css');

/**
 * 5. Déclaration des blocs
 */
$template->setBlock('centre','star');
$template->setBlock('centre','halfstar');
$template->setBlock('centre','emptystar');

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

$template->setBlock('centre','noprev');
$template->setBlock('centre','prev');

$template->setBlock('centre','nonext');
$template->setBlock('centre','next');

/**
 * 6. Construction de la page
 */


$info=$sql->fetchArray($sql->query('SELECT nom,descr,datedebut,datefin,tags FROM mod_galeries WHERE id='.$_GET['id']));

if(file_exists('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$_GET['photo'])) {

	$template->setVar(array(
		'galerieId'			=>	$_GET['id'],
		'galerieCleanNom'	=>	$string->clean($info['nom']),
		'galerieNom'		=>	$info['nom'],
		'galerieDate'		=>	$galeries->date($info['datedebut'],$info['datefin']),
		'galerieDescr'		=>	$info['descr'],
		'galerieTags'		=>	$galeries->formatTags($info['tags'])
	));
	
	$site->addToTitle(' - '.$info['nom']);

	$photo=$galeries->getPhotosInfos($_GET['id'],$_GET['photo']);
	
	$num=ereg_replace('[^0-9]','',$_GET['photo']);
	$prev=$num-1;
	$next=$num+1;
	$prev=((strlen($prev)==2)?'0'.$prev:((strlen($prev)==1)?'00'.$prev:$prev));
	$next=((strlen($next)==2)?'0'.$next:((strlen($next)==1)?'00'.$next:$next));
	$prev='img'.$prev.'.jpg';
	$next='img'.$next.'.jpg';

	if(file_exists('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$prev)) {
		$template->setVar(array(
			'photoPrevNom'	=>	$prev,
			'photoPrevPath'	=>	$utils->miniature('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$prev,80,60)
		));
		$template->parse('prev',true);
	} else $template->parse('noprev',true);

	$template->setVar(array(
		'photoCurrentNom'	=>	$_GET['photo'],
		'photoCurrentPath'	=>	$utils->miniature('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$_GET['photo'],80,60)
	));

	if(file_exists('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$next)) {
		$template->setVar(array(
			'photoNextNom'	=>	$next,
			'photoNextPath'	=>	$utils->miniature('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$next,80,60)
		));
		$template->parse('next',true);
	} else $template->parse('nonext',true);

	$template->setVar(array(
		'photoFile'		=>	$_GET['photo'],
		'photoTitle'	=>	$photo['title'],
		'photoDate'		=>	($photo['date'])?$string->formatDate('%A %d %B %Y  %H:%M',$photo['date'],true):'Inconnue',
		'photoHeight'	=>	$photo['height'],
		'photoWidth'	=>	$photo['width'],
		'photoWeight'	=>	$utils->size($photo['weight'],true),
		'photoViews'	=>	$photo['views'],
		'photoVotes'	=>	$photo['votes'],
		's'				=>	($photo['votes']>1)?'s':'',
		'photoModel'	=>	$photo['model'],
		'photoFlash'	=>	$photo['flash'],
		'photoFullUrl'	=>	'medias/galeries/'.$string->clean($info['nom']).'/photos/'.$_GET['photo'],
		'photoUrl'		=>	$utils->miniature('medias/galeries/'.$string->clean($info['nom']).'/photos/'.$_GET['photo'],590,450),
		'galerieNom'	=>	$info['nom']
	));
	
	// Note du fichier
	$star=5;
	
	$reste=$photo['note']-floor($photo['note']);
	if($reste!=0 && $reste>=0.3 && $reste<0.7) {
		$template->parse('halfstar');
		$star--;
	}
	if($reste==(5/10)) $photo['note']--;
	for($i=0;$i<round($photo['note']) && $star>0;$i++) {
		$template->parse('star',true);
		$star--;
	}
	
	for($star;$star>0;$star--) $template->parse('emptystar',true);
}


// Commentaires du fichier
if($photo['comms']>0) {
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
			resource_id='.$photo['id'].' && 
			module="galeries"
	');
	
	while($info=$sql->fetchAssoc($res)) {
		$template->setVar(array(
			'commId'		=>	$info['id'],
			'commAuteur'	=>	$info['author_name'],
			'commDate'		=>	$string->formatDate('%A %d %B %Y  %H:%M',$info['date'],true),
			'commMessage'	=>	$bbcode->BBCodeToHtml($info['message'])
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
if(isset($erreurs)) $site->showErrors($template,'centre',$erreurs);
?>