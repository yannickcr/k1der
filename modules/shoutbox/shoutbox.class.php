<?php
/**
 * Classe de construction de la shoutbox.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class shoutbox {
	function getMessages($nb,$start=0) {
		global $sql;
		$res=$sql->query('SELECT id,date,auteur,ip,message AS txt FROM mod_shoutbox ORDER BY date DESC LIMIT '.$start.','.$nb);

		$bbcode = new bbcode;

		$message=array();
		while($messages=$sql->fetchAssoc($res)) {
			$message[]=array(
				'id'		=>$messages['id'],
				'date'		=>$messages['date'],
				'auteur'	=>$messages['auteur'],
				'ip'		=>$messages['ip'],
				'txt'		=>$bbcode->MiniBBCodeToHtml($messages['txt'])
			);
		}
		if(count($message)>0) return $message;
		else return false;
	}
	
	function getMessage($id,$html=0) {
		global $sql;
		$messages=array();
		$message=$sql->fetchAssoc($sql->query('SELECT id,date,auteur,ip,message AS txt FROM mod_shoutbox WHERE id='.$id));

		if($html==1) {
			$bbcode = new bbcode();
			$message['txt']=$bbcode->MiniBBCodeToHtml($message['txt']);
		}
		
		if(count($message)>0) return $message;
		else return false;
	}
	
	function addMessage($auteur,$message,$redir=0) {
		global $site,$sql;
		$message=$site->clear4Sql($message,false);
		if(empty($message)) return false;
		
		$sql->query('
			INSERT INTO 
				mod_shoutbox (date,auteur,ip,message) 
			VALUES (
				'.date('U').',
				"'.$auteur.'",
				"'.$_SERVER['REMOTE_ADDR'].'",
				"'.$message.'"				
		)');
		if($redir==1) {
			header("Cache-control: private, no-cache");
			header('location:'.$_SERVER['REQUEST_URI']);
			exit();
		}
	}
	
	function editMessage($id,$new_message,$redir=0) {
		global $site,$membres,$sql;
		$new_message=$site->clear4Sql($new_message,false);
		if(empty($new_message)) return false;
		
		if($membres->infos('groupe')!=4) {	// Admin : peut éditer n'importe quel message
			$sql->query('
				UPDATE 
					mod_shoutbox
				SET
					message="'.$new_message.'"
				WHERE
					id='.$id.' &&
					auteur="'.$membres->infos('pseudo').'"
			');
		} else {							// Membre : ne peut éditer que ses messages
			$sql->query('
				UPDATE 
					mod_shoutbox
				SET
					message="'.$new_message.'"
				WHERE
					id='.$id.'
			');
		}
		if($redir==1) {
			header("Cache-control: private, no-cache");
			header('location:liste.html#mess0');
			exit();
		}
	}
	
	function delMessage($id,$redir=0) {
		global $membres,$sql;
		if($membres->infos('groupe')!=4) {	// Admin : peut supprimer n'importe quel message
			$sql->query('
				DELETE FROM 
					mod_shoutbox
				WHERE
					id='.$id.' &&
					auteur="'.$membres->infos('pseudo').'"
			');
		} else {							// Membre : ne peut supprimer que ses messages
			$sql->query('
				DELETE FROM 
					mod_shoutbox
				WHERE
					id='.$id.'
			');
		}
		if($redir==1) {
			header("Cache-control: private, no-cache");
			header('location:liste.html#mess1');
			exit();
		}
	}
	
	function config($maxcaract,$maxlength) {
		global $sql;
		$sql->query('
			UPDATE 
				config
			SET
				valeur='.(int)$maxcaract.'
			WHERE
				nom="shoutbox_max_caract"
		');
		$sql->query('
			UPDATE 
				config
			SET
				valeur='.(int)$maxlength.'
			WHERE
				nom="shoutbox_max_length";
		');
		header("Cache-control: private, no-cache");
		header('location:configuration.html#mess2');
		exit();
	}
}
?>