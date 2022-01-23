<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('admin_acces')) $site->error(1);

if($this->action('updateAcces','membres')) $membres->updateAcces($_POST);

$sub_template->setFile('centredroite','admin/acces.html');
$site->addToTitle(' - Site - Accs');


$sub_template->setBlock('centredroite','module');
$sub_template->setBlock('centredroite','admin');
$sub_template->setBlock('centredroite','liste');

$acces=$membres->listAcces();
$gacces=$membres->listGroupAcces();

$oldModule='';
foreach($acces as $info) {
	
	if(strpos($info['descr'],'(admin)')!==false) $sub_template->parse('admin');
	$info['descr']=str_replace('(admin) ','',$info['descr']);

	$sub_template->setVar(array(
		'accesId'		=>	$info['id'],
		'accesDescr'	=>	$info['descr'],
		'accesModule'	=>	ucfirst($info['module']),
	));
	
	
	if(strpos($gacces['Administrateurs'],'|'.$info['id'].'|')!==false) $sub_template->setVar('checkedA',' checked="checked"');
	if(strpos($gacces['Membres'],'|'.$info['id'].'|')!==false) $sub_template->setVar('checkedM',' checked="checked"');
	if(strpos($gacces['Visiteurs'],'|'.$info['id'].'|')!==false) $sub_template->setVar('checkedV',' checked="checked"');
	if(strpos($gacces['Bannis'],'|'.$info['id'].'|')!==false) $sub_template->setVar('checkedB',' checked="checked"');
	
	if($info['module']!=$oldModule) $sub_template->parse('module');
	$sub_template->parse('liste',true);
	$oldModule=$info['module'];
	$sub_template->unsetVar(array('admin','module','checkedA','checkedM','checkedV','checkedB'));
}

?>