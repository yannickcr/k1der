<?php
/**
 * Classe de construction du site.
 *
 * @todo Voir si il ne serait pas prfrable de la fusionner avec la classe Modules
 * @todo Mthodes  placer dans la classe String
 * @author	Yannick Croissant
 * @package	K1der
 */
class site {

	var $css=array(1=>array(),2=>array());
	var $js=array(1=>array(),2=>array());
	/**
	 * Constructeur de la classe Site.
	 * - Récupère les données de configuration du site
	 * - Localisation php
	 * - Supprime les Magic Quotes
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function site() {
		global $sql;
		$req=$sql->query('SELECT name,value FROM config');
		while($info=$sql->fetchAssoc($req)) $this->config[$info['name']]=$info['value'];
		$this->defineContentType();
		setlocale(LC_ALL, 'fr_FR');
		setlocale(LC_NUMERIC,'en_UK');
		setlocale(LC_TIME, 'fr');
		$this->supprMagicQuotes();
		if(function_exists('date_default_timezone_set')) date_default_timezone_set('Europe/Paris');
	}

	/**
	 * Retourne la valeur du paramtre $var de la config ou modifie sa valeur pour l'affichage de la page
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Paramtre dont on veut la valeur
	 * @param	string Nouvelle valeur
	 * @return	string
	 */
	function config($var,$newval='') {
		if(!isset($this->config[$var]) && empty($newval)) return false;
		else if(empty($newval)) return $this->config[$var];
		else return $this->config[$var]=$newval;
		return true;
	}

	/**
	 * Construction de la page en fonction des parties  utiliser.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	boolean
	 */
	function makePage() {
		global $template,$membres,$module,$sql,$string;

		$themeConfig=$string->parseXml('templates/'.THEME.'/config.xml');

		foreach($themeConfig['bloc'] as $bloc) {
			$name=$bloc['attributes']['name'];
			if($module->toUse($name)==TRUE) require_once('include/composants/'.$name.'.inc.php');
		}

		/*if(!$this->is_naked_day() && !PSP) {	// Annual Naked Day : http://www.dustindiaz.com/naked-day/
			if(isset($this->css)) {
				for($j=1;isset($this->css[$j]) && $j<3;$j++) for($i=(count($this->css[$j])-1);$i>=0;$i--) $template->setVar('header.css',$this->css[$j][$i],true);
			}
		} else if(PSP) $template->setVar('header.css','<link href="templates/'.THEME.'/psp.css" rel="stylesheet" type="text/css" media="screen" />',true);
		*/

		//if(!$this->is_naked_day()) {
			include('include/css.php');
			if(isset($css)) {
				$template->setBlock('header','css');
				$template->setVar('header.css','css_'.$css.'.css');
				$template->parse('css');
			}
		//}

		if(isset($this->js) && !PSP) {
			for($j=1;isset($this->js[$j]) && $j<3;$j++) for($i=(count($this->js[$j])-1);$i>=0;$i--) $template->setVar('header.js',$this->js[$j][$i],true);
		}
		$template->globalParse('parse',$this->toparse,true);
		return true;
	}

	function is_naked_day() {
		$start = date('U', mktime(-12,0,0,04,09,date('Y')));
		$end = date('U', mktime(36,0,0,04,09,date('Y')));
		$z = date('Z') * -1;
		$now = time() + $z;
		if ( $now >= $start && $now <= $end ) return true;
		else return false;
	}

	/**
	 * Construction de la page d'erreur (interromp tout traitement).
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Message d'erreur  afficher
	 * @return	boolean
	 */
	function error($message) {
		global $template,$membres,$module,$string;
		$messages=array(
			1	=>	'Tu n\'as pas accès à cette page.<p>L\'administrateur a été informé de cette tentative d\'intrusion.</p>'
		);
		if(is_int($message)) $message=$messages[$message];
		else if($message=='redirect') {
			header('location:'.dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/');
			exit();
		}
		// Message  l'administrateur
		if(isset($_SERVER['HTTP_REFERER'])) $referer='[url='.$_SERVER['HTTP_REFERER'].']'.$_SERVER['HTTP_REFERER'].'[/url]';
		else $referer='Aucune';

		if($membres->infos('id')) $profil='[url=membres/'.$string->clean($membres->infos('pseudo')).']'.$membres->infos('pseudo').'[/url]';
		else $profil='Visiteur non identifié';

		$messagepv='[b][u]Rapport d\'une Erreur[/u][/b]'."\r\n\r\n";
		$messagepv.='Date et Heure de l\'erreur : '.$string->formatDate('%A %d %B %Y',date('U'),true).'  '.$string->formatDate('%H:%M',date('U'))."\r\n";
		$messagepv.='Adresse de la page : '.$_SERVER['REQUEST_URI']."\r\n";
		$messagepv.='Adresse d\'où provient le visiteur : '.$referer."\r\n";
		$messagepv.='Adresse IP du visiteur : [url=admin/whois-'.str_replace('.','-',$_SERVER['REMOTE_ADDR']).'.html]'.$_SERVER['REMOTE_ADDR'].'[/url]'."\r\n";
		$messagepv.='Profil du visiteur : '.$profil."\r\n";
		$messagepv.='Message affiché au visiteur :'."\r\n".$message."\r\n";

		if($message===1) $membres->sendMessage(1,'Country','Erreur',$messagepv,1);
		// Fin du message
		header('HTTP/1.1 403 Forbidden');

		$template->unsetVar('centre');
		$template->setFile('centre','membres/acceserror.html');
		$template->setVar('error-message',$message);
	    $this->addToTitle(' - Erreur');

		$this->makePage();
		$template->p("parse");
		exit();
		return true;
	}

	/**
	 * Ajoute un fichier CSS  inclure dans l'en-tte.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Lien vers le fichier CSS
	 * @param	string Media sur lequel sera utilisé le CSS (screen par dfaut)
	 * @return	boolean
	 */
	function addCss($link,$media="screen",$first=false) {
		global $string;
		//$link='<link href="'.$link.'" rel="stylesheet" type="text/css" media="'.$media.'" />'."\n";
		if(in_array($link,$this->css[1]) || in_array($link,$this->css[2])) return false;
		if($first==true) $this->css[1][]=$link;
		else $this->css[2][]=$link;
		return true;
	}

	/**
	 * Ajoute un fichier JS  inclure dans l'en-tte.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Lien vers le fichier JS
	 * @return	boolean
	 */
	function addJs($link,$first=false) {
		global $string;
		$link='<script type="text/javascript" src="'.$link.'"></script>'."\n";
		if(in_array($link,$this->js[1]) || in_array($link,$this->js[2])) return false;
		if($first==true) $this->js[1][]=$link;
		else $this->js[2][]=$link;
		return true;
	}

	/**
	 * Supprime un fichier CSS de ceux  inclure dans l'en-tête.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Lien vers le fichier JS
	 * @todo	Bug zarbi avec :|
	 * @todo	Essayer de le rparer si je me sert de cette mthode
	 * @return	boolean
	 */
	function delCss($link) {
		if(is_array($this->css[1])) foreach($this->css[1] as $i=>$var) if(strpos($var,'href="'.$link.'"')!==false) unset($this->css[1][$i]);
		if(is_array($this->css[2])) foreach($this->css[2] as $i=>$var) if(strpos($var,'href="'.$link.'"')!==false) unset($this->css[2][$i]);
		return true;
	}

	/**
	 * Ajoute un le texte $text  la suite du titre de la page
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Texte  ajouter
	 * @return	boolean
	 */
	function addToTitle($text) {
		global $template,$string;
		$template->setVar('header.title',$text,true);
		$template->setVar('title.clean',$string->clean($text),true);
		return true;
	}

	/**
	 * Ajoute $code dans l'en-tte
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Code  ajouter
	 * @return	boolean
	 */
	function addToHead($code) {
		global $template;
		$template->setVar('header.head',$code,true);
		return true;
	}

	/**
	 * Rcuparation d'infos sur un fichier distant
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string URL du fichier
	 * @param	string Info  rcuprer (taille par dfaut)
	 * @return	mixed
	 */
	function remoteInfos($url,$info='taille') {
		$infos=array('taille'=>'Content-Length','type'=>'Content-Type');
		$urlinfo = parse_url($url);
		if (!isset($urlinfo['scheme']) || $urlinfo['scheme'] != 'http') return false;
		else if (empty($urlinfo['port'])) $urlinfo['port'] = 80;
		if ($fp = fsockopen($urlinfo['host'], $urlinfo['port'], $errno, $errstr, 30)) {
			fwrite($fp,'HEAD '.$url." HTTP/1.1\r\n");
			fwrite($fp,'HOST: '.$urlinfo['host']."\r\n");
			fwrite($fp,"Connection: close\r\n\r\n");
			$headers='';
			while (!feof($fp)) $headers.=fgets($fp, 4096);
			fclose ($fp);
			$headersarray = explode("\n", $headers);
			foreach($headersarray as $header) if (strpos($header, $infos[$info]) === 0) return str_replace($infos[$info].': ','',trim($header));
			return false;
		} else return false;
	}

	/**
	 * Suppression des Magic Quotes si celles-ci sont actives
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @return	boolean
	 */
	function supprMagicQuotes() {
        if (PHP_VERSION<6 && get_magic_quotes_gpc()) {
			foreach($_POST as $i=>$var) if(!is_array($_POST[$i])) $_POST[$i]=htmlspecialchars(stripslashes($var));
			foreach($_GET as $i=>$var) if(!is_array($_GET[$i]))$_GET[$i]=htmlspecialchars(stripslashes($var));
		}
		return true;
	}

	/**
	 * Nettoie le texte avant insertion dans la base de donne
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Texte  nettoyer
	 * @param	boolean Si les caractères doivent tres convertis en entites HTML (true par dfaut)
	 * @param	boolean Si les balises HTML doivent tre supprimes (true par dfaut)
	 * @param	boolean Ajoute des slashes devant les caractères spciaux (true par dfaut)
	 * @return	string
	 */
	function clear4Sql($arg,$htmlentities=true,$nohtml=true,$addslashes=true) {
		global $string;
		if(!@unserialize($arg)) $arg=$string->unhtmlentities($arg);
		if($nohtml==true && !@unserialize($arg)) $arg=strip_tags($arg);
		if($htmlentities==true && !@unserialize($arg)) $arg=htmlentities($arg);

		if($addslashes==true) $arg=addslashes($arg);
		return $arg;
	}

	/**
	 * Construction de la barre de mise en forme pour les message (BBCode)
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	Template Template dans laquelle la barre est utilise
	 * @param	string Type de barre (full par dfaut)
	 */
	function barreMiseEnForme(&$template,$type='full') {
		$this->addJs('include/js/bbcode.inc.js');
		//$this->addJs('include/scripts/spell_checker/spell_checker_compressed.js');
		//$this->addJs('include/scripts/spell_checker/cpaint.inc.js');
		$this->addCss('templates/'.THEME.'/mef.css');
		$mefTemplate = new template("templates/".THEME."/");
		$mefTemplate->setVar("THEME",THEME);
		$mefTemplate->setFile('barre','miseenforme.html');

		$mefTemplate->setBlock('barre','size');

		if($type=='full') $mefTemplate->parse('size');

		$template->setVar('miseEnForme',$mefTemplate->globalParse('parse','barre',true));
	}

	function getRoot() {
		$hostname=$_SERVER['SERVER_NAME'];
		$dir=trim(dirname($_SERVER['PHP_SELF']),'\/');
		if(!empty($dir)) $dir=$dir.'/';
		return 'http://'.$hostname.'/'.$dir;
	}

	function showErrors(&$t,$parent,$erreurs) {
		foreach($erreurs as $val) {
			$t->setBlock($parent,'error'.$val);
			$t->parse('error'.$val);
		}
	}

	function defineContentType() {
		if(isset($_SERVER["HTTP_ACCEPT"]) && stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) define('CONTENTTYPE','application/xhtml+xml');
		else define('CONTENTTYPE','text/html');
		header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);
	}
}
?>
