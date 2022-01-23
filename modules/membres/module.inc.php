<?php
$clans = new clans();

$sub_template = new template("templates/".THEME."/");
$sub_template->setFile('module','membres/module.html');
$this->addCss('templates/'.THEME.'/membres/style_mod.css');

/**
 * Déclaration des blocks du template
 * Ordre : Intrieur->Exterieur
 */
$sub_template->setBlock('module','error');
$sub_template->setBlock('module','connex');
$sub_template->setBlock('module','avatarimg');
$sub_template->setBlock('module','haveclan');
$sub_template->setBlock('module','havenoclan');
$sub_template->setBlock('module','isclanleader');
$sub_template->setBlock('module','admin');
$sub_template->setBlock('module','lanparty');
$sub_template->setBlock('module','havenotblog');
$sub_template->setBlock('module','haveblog');
$sub_template->setBlock('module','profil');
$sub_template->setBlock('module','online');

if(!$membres->infos('id')) { 							// Si pas connect
	$sub_template->setVar('ident','Connexion');
	if(isset($membres->error)) {
		$sub_template->setVar('error',$membres->showError());
    	$sub_template->parse('error');
	}
    $sub_template->parse('connex', true);
} else { 												// Si connect

	$sub_template->setVar('ident','Profil');
	if($membres->infos('avatar')) {
		$sub_template->setVar('profilAvatar',$membres->getAvatar());
		$sub_template->parse('avatarimg', true);
	}
	
	if($membres->checkBlog($membres->infos('pseudo'))) {
		$sub_template->setVar('blogPseudo',strtolower($membres->infos('pseudo')));
		$sub_template->parse('haveblog', true);
	} else $sub_template->parse('havenotblog', true);
	
	
	// Nombre de messages privés
	$info=$sql->fetchAssoc($sql->query('SELECT count(*) AS nb FROM mod_messages WHERE to_id="'.$membres->infos('id').'" && etat=0'));
	$nb=$info['nb'];
	
	$sub_template->setVar(array(
		'profilPseudo'		=>	$membres->infos('pseudo'),
		'profilClanName'	=>	$membres->infos('clan_nom'),
		'profilNbMess'		=>	$nb
	));
	
	if($nb>1) $sub_template->setVar(array('x'=>'x','s'=>'s'));
	if($membres->infos('clan_id')!=0) {
		if($clans->isClanLeader()) $sub_template->parse('isclanleader');
		$sub_template->parse('haveclan');
	} else $sub_template->parse('havenoclan');
	if($membres->infos('groupe')==4) $sub_template->parse('admin');
    $sub_template->parse('profil', true);
	
	// Modifier son inscription  la Lan Party
	if($sql->numRows($sql->query('SELECT id FROM mod_lanparty_inscrits WHERE pseudo="'.$membres->infos('pseudo').'"'))>0) $sub_template->parse('lanparty');

}

$res=$sql->query('SELECT pseudo,groupe FROM mod_membres WHERE id!=0 && last_visit>'.(date('U')-(60*5)));
while($info=$sql->fetchAssoc($res)) {
	if($info['groupe']==4) $sub_template->setVar('onlineClass','admin');
	else $sub_template->setVar('onlineClass','membre');
	$sub_template->setVar('onlinePseudo',$info['pseudo']);
    $sub_template->parse('online', true);
}
$nb=$membres->liveRead()-$sql->numRows($res);

$sub_template->setVar('onlineVisiteurs',$nb);
if($nb>1) $sub_template->setVar('onlineS','s');
?>