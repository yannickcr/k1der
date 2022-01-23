<?php
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/membres/admin/js/rechercher.js');
$site->addJs('modules/membres/admin/js/conf.js');

$site->addToTitle(' - Membres - Rechercher');

$sub_template->setFile('centredroite','membres/admin/rechercher.html');

$sub_template->setBlock('centredroite','groupes');

$res=$sql->query('SELECT id,name FROM groupes ORDER BY id ASC');
while($info=$sql->fetchAssoc($res)) {
	$sub_template->setVar(array(
		'groupId'=>$info['id'],
		'groupNom'=>$info['name']
	));
	$sub_template->parse('groupes', true);
}
?>