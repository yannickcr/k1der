<?php
$sub_template = new template("templates/".THEME."/");
$sub_template->setFile('module','partenaires/module.html');
$this->addCss('templates/'.THEME.'/partenaires/style_mod.css');

$sub_template->setBlock('module','partenaires');

$res=$sql->query('SELECT nom,url,img FROM mod_partenaires ORDER BY nom');

while($info=$sql->fetchAssoc($res)) {
	
	$sub_template->setVar(array(
		'partenaireNom'		=>	$info['nom'],
		'partenaireImg'		=>	$info['img'],
		'partenaireUrl'		=>	$info['url']
	));
	
	$sub_template->parse('partenaires',true);
}
?>