<?php
/**
 * Classe contenant diffrentes mthodes pouvant tre assimiles  des utilitaires.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class utils {

	/**
	 * Effectue un Whois sur une adresse IP
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Adresse IP à whoiser
	 * @return	string
	 */ 
	public function whois($ip) {
		if(!ereg("^((([0-9]{1,3})\.){4})$",$ip.'.')) return false;
		$servers=array('whois.ripe.net','whois.arin.net','whois.apnic.net','whois.registro.br','whois.nic.ad.jp');
		$chaines=array('','www.iana.org','whois.apnic.net','whois.registro.br','nic.ad.jp');
		for($i=0;isset($servers[$i]);$i++) {
			$fp=@fsockopen($servers[$i],43);
			if ($fp!=0) {
				fwrite($fp,$ip."\n");
				$tampon[$i]='';
				while(feof($fp)==0) $tampon[$i].= fgets($fp,1000);
				fclose($fp);
			} else {
				if($i>0) return $tampon[$i-1];
				else $tampon=$chaines[$i+1];
			}
			if(!eregi($chaines[$i+1],$tampon[$i])) return nl2br($tampon[$i]);
		}
		return nl2br($tampon[$i-1]);
	}
	
	/**
	 * créer une miniature d'une image et la place dans le dossier pass en argument
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Lien vers l'image
	 * @param	integer	Largeur maximum de la miniature
	 * @param	integer	Hauteur maximum de la miniature
	 * @param	string	Dossier de destination
	 * @return	string	Lien vers la miniature
	 */ 
	public function miniature($image,$maxLargeur=160,$maxHauteur=120,$dest='medias/temp',$quality=90) {
		@set_time_limit(86400);
		@ini_set("max_execution_time",86400);
		if(!is_file($image)) return $image;
		if(!is_dir($dest) || !is_writable($dest)) return false;
		
		list($largeur,$hauteur)=getimagesize($image);	// on recupere la taille de l'image
		if ($largeur > $hauteur) {
			$coeff = $largeur / $maxLargeur;			// cacul du coefficient de reduction
			$miniLargeur = round($largeur / $coeff);	// calcul de la largeur du piti
			$miniHauteur = round($hauteur / $coeff);	// calcul de la hauteur du piti
		} else {
			$coeff = $hauteur / $maxHauteur;			// cacul du coefficient de reduction
			$miniLargeur = round($largeur / $coeff);	// calcul de la largeur du piti
			$miniHauteur = round($hauteur / $coeff);	// calcul de la hauteur du piti
		}
		
		
		
		$miniatureGD = @imagecreatetruecolor($miniLargeur, $miniHauteur);	// on cree une image vide
		if(!is_resource($miniatureGD)) $miniatureGD = imagecreate($miniLargeur, $miniHauteur);	// on cree une image vide
		$pathInfo=pathinfo(strtolower($image));
		$ext=$pathInfo['extension'];
		$miniPath=$dest.'/'.md5_file($image).'-'.$miniLargeur.'x'.$miniHauteur.'.'.$ext;
		if(file_exists($miniPath)) return $miniPath;
		if($ext=='gif') $imageGD = imagecreatefromgif($image);							// on cree une image GD  partir d'un Gif
		else if($ext=='jpg' || $ext=='jpeg') $imageGD = imagecreatefromjpeg($image);	// on cree une image GD  partir d'un Jpeg
		else if($ext=='png') $imageGD = imagecreatefrompng($image);						// on cree une image GD  partir d'un Png
		else return false;
		$youpi=@imagecopyresampled($miniatureGD, $imageGD, 0,0,0,0, $miniLargeur,$miniHauteur, $largeur,$hauteur);
		if(!$youpi) imagecopyresized($miniatureGD, $imageGD, 0,0,0,0, $miniLargeur,$miniHauteur, $largeur,$hauteur);

		/*if($ext=='gif' && function_exists('imagegif')) imagegif($miniatureGD,$miniPath);
		else */
		if($ext=='jpg' || $ext=='jpeg') imagejpeg($miniatureGD,$miniPath,$quality);
		else if($ext=='gif' || $ext=='png') imagepng($miniatureGD,$miniPath);
		imagedestroy($imageGD);	// destruction de l'image cree par GD
		imagedestroy($miniatureGD);
		return $miniPath;
	}
	
	/**
	 * Convertit une valeur en octet en valeur plus lisible (Ko,Mo,etc...)
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	integer	Valeur  convertir
	 * @param	boolean	True si on doit afficher l'unite, false par dfaut
	 * @param	integer	Nombre de chiffres aprs la virgule
	 * @return	string	Valeur convertie
	 */ 
	public function size($num,$unit=false,$prec=2) {
		$tab=array('Octets','Ko','Mo','Go','To','Po');
		for($i=0;$num>1024;$i++) $num=$num/1024;
		if($unit==false) return round($num,$prec);
		else return round($num,$prec)." ".$tab[$i];
	}

}
?>
