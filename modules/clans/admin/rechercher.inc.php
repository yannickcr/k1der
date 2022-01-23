<?php
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/clans/admin/js/rechercher.js');
$site->addJs('modules/clans/admin/js/conf.js');

$site->addToTitle(' - Clans - Rechercher');

$sub_template->setFile('centredroite','clans/admin/rechercher.html');

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