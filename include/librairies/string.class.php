<?php
/**
 * Classe de traitement de chaines de caractère et de fichiers
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class string {

	/**
	 * Nettoie la chaine de caractère
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Chaine à nettoyer
	 * @return	string
	 */
	public function clean($chaine,$mode='url',$unhtml=true) {
		if($unhtml) $chaine=$this->unhtmlentities($chaine);
		switch($mode) {
			case 'url':
				$replace = array(
					'À'=>'a','Á'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a','Å'=>'a','à'=>'a','á'=>'a','â'=>'a','ã'=>'a',
					'ä'=>'a','å'=>'a','Ò'=>'o','Ó'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o','Ø'=>'o','ò'=>'o','ó'=>'o',
					'ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o','È'=>'e','É'=>'e','Ê'=>'e','Ë'=>'e','è'=>'e','é'=>'e',
					'ê'=>'e','ë'=>'e','Ç'=>'c','ç'=>'c','Ì'=>'i','Í'=>'i','Î'=>'i','Ï'=>'i','ì'=>'i','í'=>'i',
					'î'=>'i','ï'=>'i','Ù'=>'u','Ú'=>'u','Û'=>'u','Ü'=>'u','ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
					'ÿ'=>'y','Ñ'=>'n','ñ'=>'n','@'=>'a'
				);
				$chaine = strtr($chaine,$replace);
				// Remplace les caractères invalides
				$chaine = trim(preg_replace("/[^a-z0-9]/i",'-',mb_strtolower($chaine)),'-');
				// Supprime les multiples _
				while(ereg('--',$chaine)) $chaine=str_replace('--','-',$chaine);
				break;
			case 'alnum':
				$replace = array(
					'À'=>'a','Á'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a','Å'=>'a','à'=>'a','á'=>'a','â'=>'a','ã'=>'a',
					'ä'=>'a','å'=>'a','Ò'=>'o','Ó'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o','Ø'=>'o','ò'=>'o','ó'=>'o',
					'ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o','È'=>'e','É'=>'e','Ê'=>'e','Ë'=>'e','è'=>'e','é'=>'e',
					'ê'=>'e','ë'=>'e','Ç'=>'c','ç'=>'c','Ì'=>'i','Í'=>'i','Î'=>'i','Ï'=>'i','ì'=>'i','í'=>'i',
					'î'=>'i','ï'=>'i','Ù'=>'u','Ú'=>'u','Û'=>'u','Ü'=>'u','ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
					'ÿ'=>'y','Ñ'=>'n','ñ'=>'n'
				);
				$chaine = strtr($chaine,$replace);
				$chaine = trim(preg_replace("/[^a-z0-9]/i",'-',$chaine),'-');
				break;
			case 'int':
				$chaine = (int)$chaine;
				break;
			case 'float':
				$chaine = (float)$chaine;
				break;
			case 'htmlentities':
				$chaine = htmlentities($chaine,ENT_COMPAT,CHARSET);
				break;
			case 'slash':
				$chaine = addslashes($chaine);
				break;
		}
		return $chaine;
	}
	
	/**
	 * Parse un fichier XML
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Fichier  parser
	 * @return	array
	 */
	public function parseXml($file) {
		if(!file_exists($file)) return array();
		$data=@implode("",file($file));
		$parser=xml_parser_create('UTF-8');
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($parser,$data,$values,$index);
		xml_parser_free($parser);
		$oldTag='';
		for ($i=0;$i<sizeof($values);$i++) {
			if($values[$i]['tag']!=$oldTag) $j=0;
			if(isset($values[$i]['attributes'])) $xml[$values[$i]['tag']][$j++]=array('tag'=>$values[$i]['tag'],'attributes'=>$values[$i]['attributes']);
			$oldTag=$values[$i]['tag'];
		}
		if(!isset($xml)) return array();
		return $xml;
	}
	
	/**
	 * Vide un tableau de ses valeurs vides
	 *
	 * @author	GML		<gml@tutorials-fr.com>
	 * @access	public
	 * @param	array	Tableau  traiter
	 * @return	mixed
	 */
	public function delEmptyEntry($tableau) {
		if (is_array($tableau)) {
			reset($tableau);
			while (list($key, $val)=each($tableau)) {
				if ($val) $r[$key]=$val;
			}
			return $r;
		} else return false;
	}
	
	/**
	 * Récupère une information dans le champ 'special' du forum
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Information  rcuprer
	 * @param	string	Tableau srialis
	 * @return	mixed
	 */
	public function special($data,$array) {
		if(!is_array($array)) return false;
		$array=unserialize($array);
		if(isset($array[$data])) return $array[$data];
		else return false;
	}

	/**
	 * Transforme une date de la forme 'mardi-22-mars-2005' en timestamp
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Date  transformer
	 * @return	integer	Timestamp
	 */
	public function dateToReq($date) {
		$mois=array('janvier'=>01,'fevrier'=>02,'mars'=>03,'avril'=>04,'mai'=>05,'juin'=>06,'juillet'=>07,'aout'=>08,'septembre'=>09,'octobre'=>10,'novembre'=>11,'decembre'=>12);
		$date=explode('-',$date);
		return array(mktime(0,0,0,$mois[$date[2]],$date[1],$date[3]),mktime(23,59,59,$mois[$date[2]],$date[1],$date[3]));
	}

	/**
	 * Fait la diffrence entre 2 dates au format timestamp, retourne le rsultat en jours
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Date 1
	 * @param	string	Date 2
	 * @param	string	Type d'arrondis  effectuer (voir manuel PHP pour plus de dtails)
	 * @return	integer	Nombre de jours
	 */
	public function diffDate($debut,$fin,$arrondir='floor') {
		$diff = mktime(0, 0, 0, date('m',$fin), date('d',$fin), date('Y',$fin)) - mktime(0, 0, 0, date('m',$debut), date('d',$debut), date('Y',$debut));
		if($arrondir!=false) return $arrondir(($diff / 86400)+1);
		return (($diff / 86400)+1);
	}

	/**
	 * Liste le contenu d'un rpertoire
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Chemin du rpertoire  lister
	 * @return	array	Listing du rpertoire
	 */
	public function listDir($dir,$onlyfiles=false) {
		if(!file_exists($dir) || !is_dir($dir)) return false;
		$diropen = opendir($dir);
		$files=array();
		while($fichier = readdir($diropen)) if((!$onlyfiles && $fichier!='.' && $fichier!='..') || ($onlyfiles && is_file($dir.'/'.$fichier))) $files[]=$fichier;
		closedir($diropen);
		return $files;
	}
	
	/**
	 * Coupe les mots trop long, mais sans couper le contenu des balises HTML
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Texte  traiter
	 * @param	string	Longueur maximum d'un mot
	 * @return	string	Texte trait
	 */
	public function cutLongWords($text,$length=15) {
	 	$text=$this->unhtmlentities($text);
		$balise=false;
		$tmp=array();
		$j=0;
		$tmp[$j]['txt']='';
		for($i=0;$i<strlen($text);$i++) {
			$tmp[$j]['txt'].=$text[$i];
			$tmp[$j]['balise']=$balise;
			if((@isset($text[$i+1]) && $text[$i+1]=='<') || $text[$i]=='>')  {
				if(@isset($text[$i+1]) && $text[$i+1]=='<' && $balise==true) $tmp[$j]['balise']=false;
				else if(@isset($text[$i+1]) && $text[$i+1]=='<') $balise=true;
				if($text[$i]=='>') $balise=false;
				$tmp[++$j]['txt']='';
			}

		}
		if($balise==true) $tmp[$j]['balise']=false;
		
		$text='';
		for($i=0;isset($tmp[$i]);$i++) {
			if(isset($tmp[$i]['balise']) && $tmp[$i]['balise']==false) $tmp[$i]['txt']=preg_replace('/([^ ]{'.$length.'})/si','\\1'." ",$tmp[$i]['txt']);
			$text.=$tmp[$i]['txt'];
		}
		return $text;
	}
	
	/**
	 * Convertit toutes les entits HTML en caractères normaux
	 *
	 * @author	Tir du manuel PHP
	 * @access	public
	 * @param	string	Texte  traiter
	 * @return	string	Texte trait
	 */
	public function unhtmlentities($string) {
		if(function_exists('html_entity_decode')) {
			$string=html_entity_decode($string);
			return $string;
		}

		// Remplace les entits numriques
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
		// Remplace les entits litrales
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
	
	
	/**
	 * Convertit un timestamp Unix en date au format ISO 8601
	 *
	 * @author	ungu@terong.com
	 * @access	public
	 * @param	string	Timestamp Unix
	 * @return	string	Date au format ISO 8601
	 */
	public function getIso8601($date) {
	   $date_mod = date('Y-m-d\TH:i:s', $date);
	   $pre_timezone = date('O', $date);
	   $time_zone = substr($pre_timezone, 0, 3).":".substr($pre_timezone, 3, 2);
	   $date_mod .= $time_zone;
	   return $date_mod;
	}
	
	public function formatDate($format,$date='',$uc=false) {
		if(empty($date)) $date=time();
		else $date=(int)$date;
		
		$date=strftime($format,$date);
		if($uc) $date=ucfirst($date);
		if(CHARSET=='UTF-8') return utf8_encode($date);
		return $date;
	}
	
	public function formatRelativeDate($format,$date='',$uc=false) {
		if(empty($date)) $date=time();
		else $date=(int)$date;
		
		$dateDay=mktime(0,0,0,date('m',$date),date('d',$date)+1,date('Y',$date));
		$dateNow=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$elapsed=time()-$date;
		
		switch($elapsed) {
			case $elapsed<60 :														// < 1 min
				return ($uc?'I':'i').'l y a moins d\'une minute';
			case $elapsed<3600 :													// < 1 heure
				return ($uc?'I':'i').'l y a '.round($elapsed/60).' minutes';
			case date('Ymd',$date)==date('Ymd') :									// < jour même
				return ($uc?'A':'a').'ujourd\'hui &agrave; '.$this->formatDate('%H:%M',$date);
			case $dateDay==$dateNow :												// < 2 jours
				return ($uc?'H':'h').'ier &agrave; '.$this->formatDate('%H:%M',$date);
			default:																// plus
				return $this->formatDate($format,$date,$uc);
		}
	}	
}
?>