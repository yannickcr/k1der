<?php
$download = new download();
if($this->action('addFile')) $erreur=$download->addFile($_POST['nom'],$_POST['cat'],$_POST['descr'],$_FILES,$_POST['miroir']);

$site->addToTitle(' - Tlchargements - Ajouter d\'un fichier');
$site->addJs('modules/download/admin/js/addfile.inc.js');

$sub_template->setFile('centredroite','download/admin/addfile.html');

$sub_template->setBlock('centredroite','erreur-cat');
$sub_template->setBlock('centredroite','erreur-nom');
$sub_template->setBlock('centredroite','erreur-newimg');
$sub_template->setBlock('centredroite','erreur-miroir');
$sub_template->setBlock('centredroite','miroir');
$sub_template->setBlock('centredroite','cats');
$sub_template->setBlock('centredroite','images');

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

$dir='images';
$images=$string->listDir($dir,true);
foreach($images as $var) {
	$sub_template->setVar(array(
		'imageSrc'=>$dir.'/'.$var,
		'imageNom'=>$var
	));
	if(isset($erreur) && $dir.'/'.$var==$_POST['image']) $sub_template->setVar('selected2',' selected="selected"');
	$sub_template->parse('images',true);
	$sub_template->unsetVar(array('selected2'));
}

if(isset($erreur)) {
	$sub_template->setVar(array(
		'nom'=>$site->clear4Sql($_POST['nom']),
		'descr'=>$site->clear4Sql($_POST['descr']),
	));
	if($erreur==1) $sub_template->parse('erreur-nom');
	else if($erreur==2) $sub_template->parse('erreur-cat');
	else if($erreur==3) $sub_template->parse('erreur-newimg');
	else if($erreur==4) $sub_template->parse('erreur-miroir');
	$i=1;
	do {
		$sub_template->setVar(array(
			'miroirNum'=>$i,
			'miroirUrl'=>$site->clear4Sql($_POST['miroir'][$i-1]),
		));
		$sub_template->parse('miroir',true);
		$sub_template->unsetVar(array('erreur-miroir'));
		$i++;
	} while ($i<=count($_POST['miroir']));
} else {
	$sub_template->setVar('miroirNum',1);
	$sub_template->parse('miroir',true);
}
?>