<?php

class quote {
	
	function addPhrase($auteur,$phrase) {
		global $sql,$site;
		$sql->query('INSERT INTO mod_quote (auteur,phrase) VALUES ("'.$site->clear4Sql($auteur).'", "'.$site->clear4Sql($phrase).'")');
		
		header("Cache-control: private, no-cache");
		header('location:liste.html');
		exit();
	}
	
	function editPhrase($id,$auteur,$phrase) {
		global $sql,$site;
		$sql->query('UPDATE mod_quote SET auteur="'.$site->clear4Sql($auteur).'", phrase="'.$site->clear4Sql($phrase).'" WHERE id='.$id);
		
		header("Cache-control: private, no-cache");
		header('location:liste.html');
		exit();
	}

	function delPhrase($id) {
		global $sql;
		$sql->query('DELETE FROM mod_quote WHERE id='.$id);
		
		header("Cache-control: private, no-cache");
		header('location:liste.html');
		exit();
	}

}

?>