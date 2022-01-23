<?php
$download = new download();

if($this->action('addCat')) $erreur=$download->addCat($_POST['cat'],$_POST['nom'],$_POST['descr']);

$site->addToTitle(' - Tlchargements - Ajouter une catégorie');

$sub_template->setFile('centredroite','download/admin/addcat.html');

$sub_template->setBlock('centredroite','cats');
$sub_template->setBlock('centredroite','erreur-cat');
$sub_template->setBlock('centredroite','erreur-nom');

$cats=$download->createCatsTree();

foreach($cats as $i=>$var) {
	$var['nom']='&gt;&nbsp;'.$var['nom'];
	for($j=0;$j<$var['level'];$j++) $var['nom']='&nbsp;&nbsp;&nbsp;'.$var['nom'];
	$sub_template->setVar(array(
		'catId'=>$var['id'],
		'catNom'=>$var['nom']
	));
	if(isset($erreur) && $var['id']==(int)$_POST['cat']) $sub_template->setVar('selected',' selected="selected"');
	$sub_template->parse('cats',true);
	$sub_template->unsetVar(array('selected'));
}

if(isset($erreur)) {
	$sub_template->setVar(array(
		'nom'=>$site->clear4Sql($_POST['nom']),
		'descr'=>$site->clear4Sql($_POST['descr']),
	));
	if($erreur==1) $sub_template->parse('erreur-nom');
	else if($erreur==2) $sub_template->parse('erreur-cat');
}

?>