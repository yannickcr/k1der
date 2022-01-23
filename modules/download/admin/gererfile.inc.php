<?php
$download = new download();
if($this->action('moveFile')) $download->moveFile($_GET['id'],$_GET['action2']);

$site->addToTitle(' - Tlchargements - Gérer les fichiers');
$site->addCss('templates/'.THEME.'/forum/admin/style.css');
$site->addJs('modules/download/admin/js/conf.js');

$sub_template->setFile('centredroite','download/admin/gererfile.html');
$sub_template->setBlock('centredroite','files');

$res=$sql->query('SELECT d.id,d.nom,c.nom AS cat FROM mod_download d LEFT JOIN mod_download_cats c ON d.cat=c.id WHERE d.cat="'.$_GET['id'].'" ORDER BY d.ordre' );

while($info=$sql->fetchAssoc($res)) {
	$sub_template->setVar(array(
		'fileId'	=>	$info['id'],
		'fileNom'	=>	$info['nom'],
		'fileCat'	=>	$info['cat']
	));
	$sub_template->parse('files', true);
}
?>