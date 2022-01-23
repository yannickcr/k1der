<?php
$site->addToTitle(' - Rédiger un message privé');
$sub_template->setFile('centredroite','membres/rediger-message.html');
$site->barreMiseEnForme($sub_template,'lite');
$site->addJs('modules/membres/js/livesearch.js');
/**
 * Action envoi formulaire
 */
if($this->action('makeMessage')) {
	if(isset($_POST['valider'])) $membres->makeMessage($_POST['to'],$_POST['sujet'],$_POST['message'],$sub_template);
	if(isset($_POST['previsualiser'])) {
		$bbcode = new bbcode;
		$sub_template->setBlock('centredroite','previsualiser');
		$sub_template->setVar('messageHtml',$bbcode->BBCodeToHtml($_POST['message']));
		$sub_template->parse('previsualiser', true);
	}
	$sub_template->setVar(array(
		'to'			=>	$_POST['to'],
		'sujet'			=>	$_POST['sujet'],
		'message'		=>	$_POST['message']
	));
}
$action=explode('-',$_GET['action']);
$index=count($action)-1;
if (isset($action[$index]) && (ereg('reply',$action[$index]) || ereg('quote',$action[$index]))) {
	if (ereg('reply',$action[$index])) $id=str_replace('reply','',$action[$index]);
	else if (ereg('quote',$action[$index])) $id=str_replace('quote','',$action[$index]);
	$info=$sql->fetchAssoc($sql->query('SELECT from_name,sujet,message FROM mod_messages WHERE mod_messages.id="'.$id.'" && to_id="'.$membres->infos('id').'"'));
	$sub_template->setVar(array(
		'to'	=>	$info['from_name'],
		'sujet'	=>	'Re: '.$info['sujet']
	));
	if (ereg('quote',$action[$index])) $sub_template->setVar('message','[quote='.$info['from_name'].']'.$info['message'].'[/quote]');
} else if (isset($action[$index])) {
	$info=$sql->fetchAssoc($sql->query('SELECT pseudo FROM mod_membres WHERE pseudo="'.$action[$index].'"'));
	$sub_template->setVar('to',$info['pseudo']);
}
?>