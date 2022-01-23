<?php
$sub_template->setFile('centredroite','admin/modules.html');
$site->addToTitle(' - Site - Placement des modules');

if($this->action('placeModules','admin')) $admin->placeModules($_POST['ordre']);

$site->addJs('modules/admin/js/coordinates.js');
$site->addJs('modules/admin/js/drag.js');
$site->addJs('modules/admin/js/dragdrop.js');
$site->addJs('modules/admin/js/modules.js');

$sub_template->setBlock('centredroite','listemoduleleft');
$sub_template->setBlock('centredroite','listemoduleright');
$sub_template->setBlock('centredroite','listemoduletrash');

$dossiers=$string->listDir('modules');
$modules=array();
foreach($dossiers as $var) {
	$dir = opendir('modules/'.$var);
	while($fichier = readdir($dir)) if($fichier=='module.inc.php') $modules[]=$var;
	closedir($dir);
}

$left=unserialize($site->config('module_left'));
$right=unserialize($site->config('module_right'));

$leftList=array();
$rightList=array();
$trashList=array();

// Liste des modules non-utilisés
foreach($modules as $var) if(!in_array($var,$left) && !in_array($var,$right)) $trashList[]=ucfirst($var);

// Liste propre des modules de gauche
foreach($left as $var) if(in_array($var,$modules)) $leftList[]=ucfirst($var);

// Liste propre des modules de droite
foreach($right as $var) if(in_array($var,$modules)) $rightList[]=ucfirst($var);


foreach($leftList as $var) {
	$sub_template->setVar('moduleLeft',ucfirst($var));
	$sub_template->parse('listemoduleleft',true);
}
foreach($rightList as $var) {
		$sub_template->setVar('moduleRight',ucfirst($var));
		$sub_template->parse('listemoduleright',true);
}
foreach($trashList as $var) {
		$sub_template->setVar('moduleTrash',ucfirst($var));
		$sub_template->parse('listemoduletrash',true);
}
?>