<?php
$shoutbox = new shoutbox();

/**
 * Action envoi formulaire (sans javascript)
 */
if($module->action('postMessage','shoutbox')) $shoutbox->addMessage($membres->infos('pseudo'),$_POST['shoutbox_message'],1);


$sub_template = new template("templates/".THEME."/");
$sub_template->setFile('module','shoutbox/module.html');

$this->addCss('templates/'.THEME.'/shoutbox/style_mod.css');
$this->addJs('modules/shoutbox/js/scripts.js');

$sub_template->setBlock('module','ident');
$sub_template->setBlock('module','noident');
$sub_template->setBlock('module','message');

if($membres->infos('id')) $sub_template->parse('ident', true);
else $sub_template->parse('noident', true);

$messages=$shoutbox->getMessages(10);

if(is_array($messages)) {
	foreach($messages as $message) {
		$date=date('\L\e d/m/Y à H\hi',$message['date']);
		$sub_template->setVar(array(
			'messageId'		=>	$message['id'],
			'messageDate'	=>	$date,
			'messageAuteur'	=>	$message['auteur'],
			'messageTxt'	=>	$message['txt']
		));
		if($message['ip']==$_SERVER['REMOTE_ADDR'] || $membres->infos('groupe')==4) {
			$sub_template->setVar('ddId',' id="message'.$message['id'].'"');
		}
		$sub_template->parse('message', true);
		$sub_template->unsetVar(array('ddId'));
	}
}
?>