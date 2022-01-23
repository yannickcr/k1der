<?php
/**
 * Classe de gestion de l'administration du site.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class admin {

    /**
     * Construit le menu de l'administration  partir des fichiers de configuration des modules.
     *
	 * @author	Yannick Croissant
	 * @access	public
     * @return	string	Menu de l'administration
     */ 
	function genMenu() {
		global $string,$membres;
		$dir = opendir('modules/');
		while($dossier = readdir($dir)) {
			if(file_exists('modules/'.$dossier.'/config.xml')) $modules[]=$dossier;
		}
		closedir($dir);
		sort($modules);
		$menu='';
		foreach($modules as $i => $var) {
			if(isset($admin)) unset($admin);
			$xml=$string->parseXml('modules/'.$var.'/config.xml');
			if(isset($xml['menu']) && is_array($xml['menu'])) {
				$menup='';
				foreach($xml['menu'] as $i2 => $xml2) {
					if(!isset($xml2['attributes']['acces']) || $membres->verifAcces($xml2['attributes']['acces'])) $menup.="\r\n         ".'<li><a href="'.$xml2['attributes']['lien'].'">'.$xml2['attributes']['titre'].'</a></li>';
				}
				if(!empty($menup)) {
					$menu.="\r\n       ".'<li>'.$xml['admin'][0]['attributes']['titre']."\r\n        ".'<ul>';
					$menu.=$menup;
					$menu.="\r\n        ".'</ul>'."\r\n       ".'</li>';
				}
			}
		}
		return $menu;
	}
	
	function configSite($post) {
		global $sql,$string,$site;
		$tab=$string->listDir('modules');
		if(in_array($post['defpage'],$tab)) $sql->query('UPDATE config SET value="'.$site->clear4Sql($post['defpage']).'" WHERE name="default_page"');
		header('location:configuration.html#mess0');
		exit();
	}
	
	function changeTheme($post) {
		global $sql,$site;
		foreach($post as $i=>$var) {
			if(ereg('theme_',$i)) $theme=str_replace('theme_','',$i);
		}
		if(isset($theme)) $sql->query('UPDATE config SET value="'.$site->clear4Sql($theme).'" WHERE name="theme"');
		header('location:apparence.html#mess1');
		exit();
	}
	
	function changeSmileys($post) {
		global $sql,$site;
		$smileys=array();
		foreach($post as $i=>$var) {
			if(ereg('^smiley_([0-9]+)$',$i) && !empty($post[$i.'_txt'])) $smileys[$post[$i.'_txt']]=$post[$i];
		}
		$smileys=serialize($smileys);
		$sql->query('UPDATE config SET value="'.$site->clear4Sql($smileys).'" WHERE name="smileys"');
		header('location:smileys.html#mess2');
		exit();
	}

	function placeModules($ordre) {
		global $sql,$site;
		$ordre=explode(':',$ordre);
		eval('$left='.strtolower($ordre[0]).';');
		eval('$right='.strtolower($ordre[1]).';');
		
		$sql->query('UPDATE config SET value="'.$site->clear4Sql(serialize($left)).'" WHERE name="module_left"');
		$sql->query('UPDATE config SET value="'.$site->clear4Sql(serialize($right)).'" WHERE name="module_right"');
		header('location:modules.html#mess4');
		exit();
	}

}
?>