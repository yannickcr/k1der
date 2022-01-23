<?php
$download = new download();

if($this->action('editComm')) $erreur=$download->editComm($_GET['file'],$_GET['edit'],$_POST['message'],$_POST['note']);
else if($this->action('addComm')) $erreur=$download->addComm($_GET['file'],(($membres->infos('id'))?$membres->infos('pseudo'):$_POST['pseudo']),$_POST['message'],$_POST['note']);
else if($this->action('delComm')) $erreur=$download->delComm($_GET['file'],$_GET['del']);

$utils = new utils();
$bbcode = new bbcode();

$template->setFile('centre','download/fiche.html');
$site->addToTitle(' - Téléchargements - ');
$site->addCss('templates/'.THEME.'/download/style.css');
$site->addJs('modules/download/js/count.js');

// Déclaration des blocs
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

// Informations sur le fichier
$info=$sql->fetchAssoc($sql->query('
	SELECT 
		d.id,
		d.nom,
		d.descr,
		d.illus,
		c.nom cat,
		d.size,
		d.note,
		d.votes,
		d.dl,
		d.mirrors,
		d.active,
		d.cat catId,
		COUNT(co.message) comms 
	FROM 
		mod_download d 
			LEFT JOIN mod_download_cats c ON d.cat=c.id
			LEFT JOIN mod_comments co ON d.id=co.resource_id
	WHERE 
		d.id='.$_GET['file'].' &&
		(co.module="download" || d.votes=0)
	GROUP BY
		d.id
'));

$miroir=unserialize($info['mirrors']);

$site->addToTitle($info['nom']);

$template->setVar(array(
	'dlId'		=>	$info['id'],
	'dlNom'		=>	$info['nom'],
	'dlCleanNom'=>	$string->clean($info['nom']),
	'dlCat'		=>	$string->clean($info['cat']),
	'dlCatId'	=>	$string->clean($info['catId']),
	'dlDescr'	=>	nl2br($info['descr']),
	'dlIllus'	=>	$info['illus'],
	'dlSize'	=>	$utils->size($info['size'],true),
	'dlVote'	=>	$info['votes'],
	'dlComms'	=>	$info['comms'],
	'dlCommsS'	=>	($info['comms']>1)?'s':'',
	'dlDl'		=>	$info['dl'],
	'dlLink'	=>	$miroir[$info['active']]
));

// Note du fichier
$star=5;

$reste=$info['note']-floor($info['note']);
if($reste!=0 && $reste>=0.3 && $reste<0.7) {
	$template->parse('halfstar');
	$star--;
}
if($reste==0.5) $info['note']--;
for($i=0;$i<round($info['note']) && $star>0;$i++) {
	$template->parse('star',true);
	$star--;
}

for($star;$star>0;$star--) $template->parse('emptystar',true);

// Commentaires du fichier
if($info['comms']>0) {
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
			resource_id='.$info['id'].' && 
			module="download"
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
		if(($info['author_id']!=0 && $membres->infos('id')==$info['author_id']) || $membres->verifAcces('galeries_comm_edit') || $membres->infos('groupe')==4) {
			$template->parse('edit-comm');
			$template->parse('del-comm');
		}
		if($membres->verifAcces('galeries_post_comm')) $template->parse('quote-comm');
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

if($membres->verifAcces('galeries_post_comm')) {

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
		if($info['author_id']==$membres->infos('id') || $membres->verifAcces('galeries_post_comm')) {
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
?>