<?php
/**
 * Classe pour la construction de la page principale et l'utilisation des modules.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class modules {

	/**
	 * Création de la page principale
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	boolean
	 */
	public function page() {
		global $template,$site,$sql,$membres,$string;
		if(empty($_GET['module'])) $this->module=$site->config('default_page'); // On dtermine le module  utiliser
		else $this->module=$_GET['module'];
		$page=$this->getPage();
		$fichier='modules/'.$this->module.'/'.$page.'.inc.php';
		if(file_exists($fichier)) require($fichier); 							// Inclusion et traitement de la page
		else require('modules/index/404error.inc.php');
		$this->page=$page;
		return true;
	}
	
	/**
	 * Dtermine la page  afficher
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	string
	 */
	public function getPage() {
		global $string;
		if(!isset($this->xml[$this->module])) $this->xml[$this->module]=$string->parseXml('modules/'.$this->module.'/config.xml');
		for($i=0;isset($this->xml[$this->module]['page'][$i]);$i++) {
			if($this->checkArgs($this->xml[$this->module]['page'][$i]['attributes']['condition'])==true) return $this->xml[$this->module]['page'][$i]['attributes']['page'];
		}
		return 'index';
	}

	/**
	 * Dtermine si une action doit tre effectue ou pas
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Nom de l'action
	 * @return	string
	 */
	public function action($action,$module2use='') {
		global $string;
		if(!empty($module2use)) $module=$module2use;
		else if($this->module=='admin' && isset($_GET['submodule'])) $module=$_GET['submodule'];
		else $module=$this->module;
		if(!isset($this->xml[$module])) $this->xml[$module]=$string->parseXml('modules/'.$module.'/config.xml');
		for($i=0;isset($this->xml[$module]['action'][$i]);$i++) {
			if($this->xml[$module]['action'][$i]['attributes']['action']==$action && $this->checkArgs($this->xml[$module]['action'][$i]['attributes']['condition'])==true) return true;
		}
		return false;
	}
	
	/**
	 * Parse les conditions d'affichage d'une page afin de dterminer si elles sont satisfaites ou pas
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Conditions
	 * @return	boolean
	 */
	public function checkArgs($condition) {
		global $membres;
		$args=explode(',',$condition);
		$tabs=array(
			'_POST'=>'post',
			'_GET'=>'get',
			'_SESSION'=>'session',
			'_COOKIE'=>'cookie',
			'membres->infos'=>'infos'
		);
		
		foreach($tabs as $tab=>$exp) {
			eval('$tab=$'.$tab.';');
			foreach ($args as $val) {
				if (ereg("^".$exp."\[(.*)\]$",$val)) {
					$index=preg_replace("/".$exp."\[(.*)\]/","$1",$val);
					if(!isset($tab[$index])) return false;
				} else if (ereg("^".$exp."\[(.*)\]\((.*)\)$",$val)) {
					$index=preg_replace("/".$exp."\[(.*)\]\((.*)\)/","$1",$val);
					$value=preg_replace("/".$exp."\[(.*)\]\((.*)\)/","$2",$val);
					if(!isset($tab[$index]) || $tab[$index]!=$value) return false;
				} else if (ereg("^!".$exp."\[(.*)\]$",$val)) {
					$index=preg_replace("/!".$exp."\[(.*)\]/","$1",$val);
					if(isset($tab[$index])) return false;
				} else if (ereg("^!".$exp."\[(.*)\]\((.*)\)$",$val)) {
					$index=preg_replace("/!".$exp."\[(.*)\]\((.*)\)/","$1",$val);
					$value=preg_replace("/!".$exp."\[(.*)\]\((.*)\)/","$2",$val);
					if(isset($tab[$index]) && $tab[$index]==$value) return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Dfinition des parties de la page  ne pas utiliser
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	array	Tableau des parties  ne pas utiliser
	 * @return	boolean
	 */
	public function noUse($tab) {
		$this->noUse=$tab;
		return true;
	}
	
	/**
	 * Vrifie si une partie de la page doit tre utilise ou pas
	 *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	Partie  vrifier
	 * @return	boolean
	 */
	public function toUse($val) {
		if(!isset($this->noUse)) return true;
		if(in_array($val,$this->noUse)) return false;
		else return true;
	}
	
	public function getCentreTitre() {
		global $string;
		if(!isset($this->xml[$this->module])) $this->xml[$this->module]=$string->parseXml('modules/'.$this->module.'/config.xml');
		if(isset($this->xml[$this->module]['configuration'][0]['attributes']['titre'])) return $this->xml[$this->module]['configuration'][0]['attributes']['titre'];
		else return ucfirst($this->module);
	}	

}
?>