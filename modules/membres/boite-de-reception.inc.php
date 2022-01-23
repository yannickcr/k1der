<?php
$site->addToTitle(' - Boîte de réception');
$sub_template->setFile('centredroite','membres/boite-de-reception.html');
$site->addJs('modules/membres/js/conf.js');
$site->addJs('modules/membres/js/checker.js');

$sub_template->setBlock('centredroite','messages');
$sub_template->setBlock('centredroite','fromblock1');
$sub_template->setBlock('centredroite','fromblock2');
$sub_template->setBlock('centredroite','select-action');

/**
 * Action envoi formulaire
 */
if($this->action('makeMessage')) {
	$membres->makeMessage($_POST['to'],$_POST['sujet'],$_POST['message'],$sub_template);
	$sub_template->setVar(array(
		'to'			=>	$_POST['to'],
		'sujet'			=>	$_POST['sujet'],
		'message'		=>	$_POST['message']
	));
} else if($this->action('markMessages')) $membres->markMessages($_POST['message'],$sub_template);
else if($this->action('delMessages')) $membres->delMessages($_POST['message'],$sub_template);

$etat=array('message-new','message-open','message-reply');
$etat2=array('Non lu','Lu','Rpondu');

$res=$sql->query('SELECT id,from_id,from_name,date,sujet,etat FROM mod_messages WHERE to_id="'.$membres->infos('id').'" ORDER BY date DESC');
while($info=$sql->fetchAssoc($res)) {

	if($info['from_id']!=0) {
		$sub_template->parse('fromblock1', true);
		$sub_template->parse('fromblock2', true);
	}

	$sub_template->setVar(array(
		'messageId'		=>	$info['id'],
		'linkSujet'		=>	$string->clean($info['sujet']),
		'etatIcon'		=>	$etat[$info['etat']],
		'etat'			=>	$etat2[$info['etat']],
		'sujet'			=>	$info['sujet'],
		'from'			=>	$info['from_name'],
		'date'			=>	$string->formatDate('%d/%m/%Y %H:%M',$info['date'],true),
	));
	$sub_template->parse('messages', true);
	$sub_template->unsetVar(array('fromblock1','fromblock2'));
	
}
if($sql->numRows($res)==0) {
	$sub_template->setBlock('centredroite','nomessages');
	$sub_template->parse('nomessages', true);
} else $sub_template->parse('select-action');
?>