<?php
$shoutbox = new shoutbox();

$this->noUse(array('header','titre','gauche','droite','bas','footer'));
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);

if(isset($_POST['id'])) $id=ereg_replace("^message([0-9]+)$","\\1",$_POST['id']);
if(isset($_POST['date'])) $date=ereg_replace("^(.*)([0-9]{2})/([0-9]{2})/([0-9]{4})(.*)$",'\\4-\\3',$_POST['date']);
if(isset($_POST['message'])) $_POST['message']=str_replace('<','',$_POST['message']);

/**
 * Ajout d'un message
 */
if($membres->infos('pseudo') && isset($_POST['message'])) $shoutbox->addMessage($membres->infos('pseudo'),$_POST['message']);

/**
 * Modification d'un message
 */
else if(isset($_POST['new_message'],$_POST['id'])) {
	$shoutbox->editMessage($id,$_POST['new_message']);
	$message=$shoutbox->getMessage($id,1);
	
	$message['txt']=$string->unhtmlentities($message['txt']);
	
	$message['txt']=str_replace('&euro;','',$message['txt']);
	echo '<data>'.$message['txt'].'</data>';

/**
 * Suppression d'un message
 */
} else if(isset($_POST['del'])) $message=$shoutbox->delMessage($id);

/**
 * Récupération des X derniers messages
 */
else if(isset($_POST['update'])) {
	$template->setFile('centre','shoutbox/liveedit.html');
	
	$template->setBlock('centre','message');
	
	$messages=$shoutbox->getMessages(10);
	
	foreach($messages as $message) {
		$message['date']=date('\L\e d/m/Y à H\hi',$message['date']);
		$message['txt']=$string->unhtmlentities($message['txt']);
		
		$template->setVar(array(
			'messageId'		=>	$message['id'],
			'messageDate'	=>	$message['date'],
			'messageAuteur'	=>	$message['auteur'],
			'messageTxt'	=>	$message['txt']
		));
		
		if($message['ip']==$_SERVER['REMOTE_ADDR'] || $membres->infos('groupe')==4) {
			$template->setVar('ddId',' id="message'.$message['id'].'"');
		}

		$template->parse('message', true);
		$template->unsetVar(array('ddId'));
	}
}
/**
 * Récupération d'un message
 */
else if(isset($_POST['id'])) {
	$message=$shoutbox->getMessage($id,0);
	$message['txt']=html_entity_decode($message['txt']);
	$message['txt']=str_replace('&euro;','',$message['txt']);
	echo '<data>'.$message['txt'].'</data>';
}
?>