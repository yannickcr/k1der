<?php
$download = new download();

//if($this->action('addComm')) $erreur=$download->addComm($_GET['file'],$_POST['message'],$_POST['note']);

$utils = new utils();
$bbcode = new bbcode;

$template->setFile('centre','download/cat.html');

$info=$sql->fetchAssoc($sql->query('SELECT id,nom,descr FROM mod_download_cats WHERE id='.$_GET['cat']));

$site->addToTitle(' - Téléchargements - '.$info['nom']);
$site->addCss('templates/'.THEME.'/download/style.css');

$template->setBlock('centre','subs');
$template->setBlock('centre','file');

$template->setVar(array(
	'catNom'	=>	$info['nom'],
	'catDescr'	=>	$info['descr']
));

$res=$sql->query('SELECT id,nom FROM mod_download_cats WHERE cat='.$_GET['cat']);

if($sql->numRows($res)>0) {
	$liste='';
	while($info=$sql->fetchAssoc($res)) {
		$liste.='<a href="download/'.$string->clean($info['nom']).'-id'.$info['id'].'">'.$info['nom'].'</a>, ';
		$ids[]=$info['id'];
	}
	$template->setVar('catListSub',trim($liste,', '));
	$template->parse('subs');
}

$res=$sql->query('
	SELECT 
		d.id,
		d.nom,
		d.descr,
		d.illus,
		c.nom cat,
		d.cat catid
	FROM 
		mod_download d 
			LEFT JOIN mod_download_cats c ON d.cat=c.id
	WHERE 
		d.cat='.$_GET['cat'].' 
	ORDER BY 
		d.ordre
');

if(isset($liste) && $sql->numRows($res)==0) $template->setVar('subTxt','Choisissez une sous-catégorie : ');
else if(isset($liste) && $sql->numRows($res)!=0) $template->setVar('subTxt','Sous-catégories : ');
else if(!isset($liste) && $sql->numRows($res)==0) {
	$template->setVar('subTxt','Aucun fichier dans cette catégorie.');
	$template->parse('subs');
}

while($info=$sql->fetchAssoc($res)) {
	$descr=explode('<br />',nl2br($info['descr']));
	if(strlen($descr[0])>80) $descr[0]=substr($descr[0],0,400).'...';
	$template->setVar(array(
		'catId'			=>	$info['catid'],
		'catCleanNom'	=>	$string->clean($info['cat']),
		'fileId'		=>	$info['id'],
		'fileNom'		=>	$info['nom'],
		'fileCleanNom'	=>	$string->clean($info['nom']),
		'fileDescr'		=>	$descr[0],
		'fileIllus'		=>	$utils->miniature($info['illus'],100,80),
	));
	$template->parse('file',true);
}
?>