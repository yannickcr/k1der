<?php
$clans = new clans();
$utils = new utils();

$sub_template = new template("templates/".THEME."/");
$sub_template->setFile('module','clans/module.html');
$this->addCss('templates/'.THEME.'/clans/style_mod.css');

$sub_template->setBlock('module','membre');

$clan=$this->config('clan_default');

$res=$sql->query('SELECT pseudo,avatar,mail FROM mod_membres WHERE clan_nom="'.$clan.'"');

while($info=$sql->fetchAssoc($res)) {
	$avatar=$membres->getAvatar($info['avatar'],$info['mail'],44);
	$sub_template->setVar(array(
		'pseudo'	=>	$info['pseudo'],
		'avatar'	=>	((ereg('gravatar',$avatar))?$avatar:$utils->miniature($avatar,44,44))
	));
	
	$sub_template->parse('membre', true);
}
?>