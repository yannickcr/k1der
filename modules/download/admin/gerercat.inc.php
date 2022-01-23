<?php
$download = new download();
if($this->action('moveCat')) $download->moveCat($_GET['id'],$_GET['action2']);

$site->addToTitle(' - Tlchargements - Gérer les catégories');
$site->addCss('templates/'.THEME.'/forum/admin/style.css');
$site->addJs('modules/download/admin/js/conf.js');

$sub_template->setFile('centredroite','download/admin/gerercat.html');
$sub_template->setBlock('centredroite','top');
$sub_template->setBlock('centredroite','bottom');
$sub_template->setBlock('centredroite','cats');

$cats=$download->createCatsTree();

foreach($cats as $i => $var) {
	for($j=1;$j<$var['level'];$j++) $var['nom']='&nbsp;&nbsp;&nbsp;'.$var['nom'];
	$sub_template->setVar(array(
		'catNom'	=>	$var['nom'],
		'catId'		=>	$var['id'],
		'catNbFile'	=>	$var['nbFile']
	));
	if($var['level']==1) $sub_template->parse('top');
	else $sub_template->parse('bottom');
	$sub_template->parse('cats', true);
	$sub_template->clearVar(array('top','bottom'));
}
?>