<?php
// Vérification des droits d'accès
if(!$membres->verifAcces('forum_gererforum')) $site->error(1);

$forum = new forum();
if(isset($_GET['id'],$_GET['action2']) && $_GET['action2'][0]=='c' && $_GET['action2']!='csuppr') $forum->moveCat();
else if(isset($_GET['id'],$_GET['action2']) && $_GET['action2'][0]=='f' && $_GET['action2']!='fsuppr' && $_GET['action2']!='fempty') $forum->moveForum();

$site->addToTitle(' - Forum - Gérer le forum');
$site->addCss('templates/'.THEME.'/forum/admin/style.css');

$sub_template->setFile('centredroite','forum/admin/gererforum.html');
$sub_template->setBlock('centredroite','forums');
$sub_template->setBlock('centredroite','cats');

$req=$sql->query('SELECT id,nom FROM mod_forum_cats ORDER BY ordre');
while($info=$sql->fetchAssoc($req)) $cat[$info['id']]['nom']=$info['nom'];

$req=$sql->query('SELECT id,titre,cat,descr FROM mod_forum_forums ORDER BY cat,ordre');
while($info=$sql->fetchAssoc($req)) {
	$for[$info['id']]['id']=$info['id'];
	$for[$info['id']]['titre']=$info['titre'];
	$for[$info['id']]['descr']=$info['descr'];
	$for[$info['id']]['cat']=$info['cat'];
}
foreach($cat as $i => $var) {
	$sub_template->setVar('catNom',$var['nom']);
	$sub_template->setVar('catId',$i);
	foreach($for as $fi => $fvar) {
		if($fvar['cat']==$i) {
			$sub_template->setVar('forId',$fvar['id']);
			$sub_template->setVar('forNom',$fvar['titre']);
			$sub_template->setVar('forDescr',$fvar['descr']);
			$sub_template->parse('forums', true);
		}
	}
	$sub_template->parse('cats', true);
	$sub_template->clearVar('forums');
}
?>