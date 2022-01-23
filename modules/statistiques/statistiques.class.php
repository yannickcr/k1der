<?php
/**
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class statistiques {

	/*
		Auteur : Sébastien Guillon
			URL : http://sebastienguillon.com
			Article : http://sebastienguillon.com/journal/2005/10/script-php-pour-dechiffrer-les-chaines-user-agent
	
		Droits :
			copyright (c) 2005, Sébastien Guillon
			Utilisation libre pour les usages non commerciaux.
			Pour tout autre usage ou pour plus de précisions, contactez-moi via le formulaire sur :
			http://sebastienguillon.com/contact
	
		Créé : 12 octobre 2005
		Dernière modification : 22 janvier 2006
	*/
	
	/*
		Resources :
		http://www.pgts.com.au/pgtsj/pgtsj0212d.html
		http://www.zytrax.com/tech/web/browser_ids.htm
	*/
	function decodeUA($ua_string, $detailed=1) {
		$ua['name'] = '';						// Nom générique ou nom du produit
		if($detailed)	$ua['ua_version'] = '';	// Version déduite de la chaîne agent utilisateur
		$ua['ua_class'] = 'Unknown';			// Classe qui pourra être utilisée dans CSS
		$ua['ua_type'] = 'UnknownUA';			// Type d'AU : Browser / Robot / FeedReader / Validator / MobileDevice (Ajouter CMS, Aggregateur, Aspirateur)
		$ua['os'] = '';							// Nom générique de la plateforme ou de l'OS
		if($detailed)	$ua['os_version'] = '';	// Version de la plateforme ou de l'OS
		$ua['os_class'] = 'Unknown';			// Classe qui pourra être utilisée dans CSS
	
		// Agents utilisateur
		if(ereg('Opera', $ua_string)) {
			$ua['name'] = 'Opera';
			$ua['ua_class'] = 'Opera';
			$ua['ua_type'] = 'Browser';
			if($detailed) {
				if(ereg('Mozilla', $ua_string)) {
					// Lorsqu'Opera s'identifie comme un autre AU, le nom Opera et la version sont séparés par un espace (et placés à la fin)
					$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Opera', ' ');
	
					if(ereg('MSIE', $ua_string)) {
						// Les version récentes d'Opera n'offrent le choix qu'entre MSIE et Mozilla
						$ua['ua_version'] = $ua['ua_version'].' (identifié comme MSIE)';
					} else {
						// Plus rare car Opera, avant 8.5, était configuré par défaut pour s'identifier comme MSIE
						$ua['ua_version'] = $ua['ua_version'].' (identifié comme Mozilla)'; // Plus un résultat par défaut qu'une identification précise (plus précis pour les version 7+)
					}
				} else {
					// Opera est identifié comme tout navigateur devrait le faire : avec son nom en premier
					$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Opera');
				}
			} //detailed
		} elseif(ereg('Yahoo-Blogs', $ua_string)) {
			$ua['name'] = 'Yahoo-Blogs';
			$ua['ua_class'] = 'Yahoo';
			$ua['ua_type'] = 'Robot';
		} elseif(ereg('VoilaBot', $ua_string)) {
			$ua['name'] = 'Voila';
			$ua['ua_class'] = 'Voila';
			$ua['ua_type'] = 'Robot';
			
			if($detailed) {
				if(eregi('VoilaBot BETA 1.2', $ua_string)) $ua['ua_version'] = 'BETA 1.2';
			} //detailed
		} elseif(ereg('Maxthon', $ua_string)) {
			$ua['name'] = 'Maxthon (MSIE)';
			$ua['ua_class'] = 'Maxthon';
			$ua['ua_type'] = 'Browser';
		} elseif(ereg('MyIE2', $ua_string)) {
			$ua['name'] = 'MyIE2 (MSIE)';
			$ua['ua_class'] = 'Maxthon';
			$ua['ua_type'] = 'Browser';
		} elseif(ereg('MSIE', $ua_string)) {
			$ua['name'] = 'Internet Explorer';
			$ua['ua_class'] = 'InternetExplorer';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				//http://msdn.microsoft.com/workshop/author/dhtml/overview/aboutuseragent.asp
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'MSIE', ' ');
				if(eregi('MSIE 7', $ua_string)) {$ua['ua_class'] = 'InternetExplorer7';} // !!! À améliorer
			} //detailed
		} elseif (ereg('Firefox', $ua_string)) {
			$ua['name'] = 'Firefox';
			$ua['ua_class'] = 'Firefox';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Firefox');
			} //detailed
		} elseif(ereg('OmniWeb', $ua_string)) {
			// la chaîne AU d'Omniweb peut contenir les mots Safari, KHTML et Gecko, il faut donc le détecter avant,
			// bien que ce soit un navigateur rare.
			$ua['name'] = 'OmniWeb';
			$ua['ua_class'] = 'OmniWeb';
			$ua['ua_type'] = 'Browser';
			if($detailed) {
				if(eregi('OmniWeb/v5', $ua_string)) $ua['ua_version'] = '5';
				elseif(eregi('OmniWeb/v496', $ua_string)) $ua['ua_version'] = '4.5';
				elseif(eregi('OmniWeb/v4', $ua_string)) $ua['ua_version'] = '4';
				elseif(eregi('OmniWeb/4.2', $ua_string)) $ua['ua_version'] = '4.2';
				else $ua['ua_version'] = $this->decodeUAVersion($ua_string, 'OmniWeb');
			} //detailed
		} elseif(ereg('Shiira', $ua_string)) {
			// la chaîne AU de Shiira peut contenir les mots Safari, KHTML et Gecko, il faut donc le détecter avant,
			// bien que ce soit un navigateur rare. Déjà rencontré : Shiira/1.2, Shiira/0.9.5.1
			$ua['name'] = 'Shiira';
			$ua['ua_class'] = 'Shiira';
			$ua['ua_type'] = 'Browser';
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Shiira');
			} //detailed
		} elseif(ereg('Safari', $ua_string)) {
			$ua['name'] = 'Safari';
			$ua['ua_class'] = 'Safari';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				//http://developer.apple.com/internet/safari/uamatrix.html
				//http://homepage.mac.com/jprince/designSandbox/web/safari-agents/
				if(eregi('Safari/417.8', $ua_string)) $ua['ua_version'] = '2.0.3';
				elseif(eregi('Safari/416.13', $ua_string)) $ua['ua_version'] = '2.0.2';
				elseif(eregi('Safari/416.12', $ua_string)) $ua['ua_version'] = '2.0.2';
				elseif(eregi('Safari/412.5', $ua_string)) $ua['ua_version'] = '2.0.1';
				//elseif(eregi('Safari/412.2.2', $ua_string)) $ua['ua_version'] = '2.0';
				//elseif(eregi('Safari/412.2', $ua_string)) $ua['ua_version'] = '2.0';
				elseif(eregi('Safari/412', $ua_string)) $ua['ua_version'] = '2.0';
				elseif(eregi('Safari/312.3', $ua_string)) $ua['ua_version'] = '1.3.1';
				elseif(eregi('Safari/312', $ua_string)) $ua['ua_version'] = '1.3';
				elseif(eregi('Safari/125.12', $ua_string)) $ua['ua_version'] = '1.2.4';
				elseif(eregi('Safari/125.11', $ua_string)) $ua['ua_version'] = '1.2.4';
				elseif(eregi('Safari/125.9', $ua_string)) $ua['ua_version'] = '1.2.3';
				elseif(eregi('Safari/125.8', $ua_string)) $ua['ua_version'] = '1.2.2';
				elseif(eregi('Safari/125.7', $ua_string)) $ua['ua_version'] = '1.2.2';
				elseif(eregi('Safari/125.1', $ua_string)) $ua['ua_version'] = '1.2.1';
				elseif(eregi('Safari/125', $ua_string)) $ua['ua_version'] = '1.2';
				elseif(eregi('Safari/100.1', $ua_string)) $ua['ua_version'] = '1.1.1';
				elseif(eregi('Safari/100', $ua_string)) $ua['ua_version'] = '1.1';
				elseif(eregi('Safari/85.8', $ua_string)) $ua['ua_version'] = '1.0.3';
				elseif(eregi('Safari/85.7', $ua_string)) $ua['ua_version'] = '1.0.2';
				elseif(eregi('Safari/85', $ua_string)) $ua['ua_version'] = '1.0';
				elseif(eregi('Safari/74', $ua_string)) $ua['ua_version'] = '1.0b2v74';
				elseif(eregi('Safari/73', $ua_string)) $ua['ua_version'] = '0.9';
				elseif(eregi('Safari/60', $ua_string)) $ua['ua_version'] = '0.8.2';
				elseif(eregi('Safari/51', $ua_string)) $ua['ua_version'] = '0.8.1';
				elseif(eregi('Safari/48', $ua_string)) $ua['ua_version'] = '0.8';
				else $ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Safari');
			} //detailed
		} elseif(ereg('Camino', $ua_string)) {
			$ua['name'] = 'Camino';
			$ua['ua_class'] = 'Camino';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Camino');
			} //detailed
		} elseif(ereg('Konqueror', $ua_string)) {
			$ua['name'] = 'Konqueror';
			$ua['ua_class'] = 'Konqueror';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Konqueror');
			} //detailed
		} elseif(ereg('Lynx', $ua_string)) {
			$ua['name'] = 'Lynx';
			$ua['ua_class'] = 'Lynx';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Lynx');
			} //detailed
		} elseif(ereg('HTTrack', $ua_string)) {
			$ua['name'] = 'HTTrack';
			$ua['ua_class'] = 'HTTrack';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				if(eregi('HTTrack 3.0x', $ua_string)) $ua['ua_version'] = '3.0x';
			} //detailed
		} elseif(ereg('NokiaN70', $ua_string)) {
			$ua['name'] = 'NokiaN70';
			$ua['ua_class'] = 'NokiaN70';
			$ua['ua_type'] = 'MobileDevice';
		} elseif(ereg('iCab', $ua_string)) {
			$ua['name'] = 'iCab';
			$ua['ua_class'] = 'iCab';
			$ua['ua_type'] = 'Browser';
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'iCab');
			} //detailed
		} elseif(ereg('Netscape', $ua_string)) {
			$ua['name'] = 'Netscape';
			$ua['ua_class'] = 'Netscape';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Netscape');
			} //detailed
		} elseif(ereg('Galeon', $ua_string)) {
			$ua['name'] = 'Galeon';
			$ua['ua_class'] = 'Galeon';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Galeon');
			} //detailed
		} elseif(ereg('Epiphany', $ua_string)) {
			$ua['name'] = 'Epiphany';
			$ua['ua_class'] = 'Epiphany';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Epiphany');
			} //detailed
		} elseif(ereg('Firebird', $ua_string)) {
			$ua['name'] = 'Firebird';
			$ua['ua_class'] = 'Firebird';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Firebird');
			} //detailed
		} elseif(ereg('K-Meleon', $ua_string)) {
			$ua['name'] = 'K-Meleon';
			$ua['ua_class'] = 'K-Meleon';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'K-Meleon');
			} //detailed
		}// ---------------------------------------------------------------------------- Robots, Crawlers, Spiders
		elseif(ereg('Googlebot', $ua_string)) {
			$ua['name'] = 'Googlebot';
			$ua['ua_class'] = 'Google';
			$ua['ua_type'] = 'Robot';
			
			if($detailed) {
				if(eregi('Googlebot/2.1', $ua_string)) $ua['ua_version'] = '2.1';
				elseif(eregi('Googlebot-Image/1.0', $ua_string)) $ua['ua_version'] = 'Image/1.0';
			} //detailed
		} elseif(ereg('msnbot', $ua_string)) {
			$ua['name'] = 'MSN bot';
			$ua['ua_class'] = 'MSN';
			$ua['ua_type'] = 'Robot';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'msnbot');
			} //detailed
		} elseif(ereg('Java/', $ua_string)) {
			$ua['name'] = 'Java';
			$ua['ua_class'] = 'Java';
			$ua['ua_type'] = 'Robot';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Java');
			} //detailed
		} elseif(ereg('Ask Jeeves/Teoma', $ua_string)) {
			$ua['name'] = 'Ask Jeeves';
			$ua['ua_class'] = 'AskJeeves';
			$ua['ua_type'] = 'Robot';
		} elseif(ereg('Slurp', $ua_string)) {
			$ua['name'] = 'Slurp';
			$ua['ua_class'] = 'Yahoo';
			$ua['ua_type'] = 'Robot';
		} elseif(ereg('OmniExplorer_Bot', $ua_string)) {
			$ua['name'] = 'OmniExplorer_Bot';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'Robot';
		} elseif(ereg('ia_archiver', $ua_string)) {
			$ua['name'] = 'Alexa';
			$ua['ua_class'] = 'Alexa';
			$ua['ua_type'] = 'Robot';
		} elseif(ereg('Liferea', $ua_string)) {
			// ------------------------------------------------------------------------------ Feed Readers / Aggregators
			$ua['name'] = 'Liferea';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Liferea');
			} //detailed
		} elseif(ereg('FeedFetcher-Google', $ua_string)) {
			$ua['name'] = 'Google Reader';
			$ua['ua_class'] = 'Google';
			$ua['ua_type'] = 'FeedReader';
		} elseif(ereg('Bloglines', $ua_string)) {
			$ua['name'] = 'Bloglines';
			$ua['ua_class'] = 'Bloglines';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Bloglines');
			} //detailed
		} elseif(ereg('FeedDemon', $ua_string)) {
			$ua['name'] = 'FeedDemon';
			$ua['ua_class'] = 'FeedDemon';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'FeedDemon');
			} //detailed
		} elseif(ereg('NetNewsWire', $ua_string)) {
			$ua['name'] = 'NetNewsWire';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'NetNewsWire');
			} //detailed
		} elseif(ereg('AppleSyndication', $ua_string)) {
			$ua['name'] = 'AppleSyndication (Safari RSS reader)';
			$ua['ua_class'] = 'Safari';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'AppleSyndication');
			} //detailed
		} elseif(ereg('NewsGatorOnline', $ua_string)) {
			$ua['name'] = 'NewsGator';
			$ua['ua_class'] = 'NewsGator';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'NewsGatorOnline');
			} //detailed
		} elseif(ereg('Feedreader', $ua_string)) {
			$ua['name'] = 'Feedreader';
			$ua['ua_class'] = 'Feedreader';
			$ua['ua_type'] = 'FeedReader';
		} elseif(ereg('Thunderbird', $ua_string)) {
			$ua['name'] = 'Thunderbird';
			$ua['ua_class'] = 'Thunderbird';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Thunderbird');
			} //detailed
		} elseif(ereg('MagpieRSS', $ua_string)) {
			$ua['name'] = 'MagpieRSS';
			$ua['ua_class'] = 'MagpieRSS';
			$ua['ua_type'] = 'FeedReader';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'MagpieRSS');
			} //detailed
		} elseif(ereg('Waggr_Fetcher', $ua_string)) {
			$ua['name'] = 'Waggr';
			$ua['ua_type'] = 'FeedReader';
		} elseif(ereg('UniversalFeedParser', $ua_string)) $ua['ua_type'] = 'FeedReader';
		elseif(ereg('PubSub-RSS-Reader', $ua_string)) $ua['ua_type'] = 'FeedReader';
		elseif(ereg('SharpReader', $ua_string)) $ua['ua_type'] = 'FeedReader';
		elseif(ereg('Technoratibot', $ua_string)) {
			$ua['name'] = 'Technoratibot';
			$ua['ua_class'] = 'Technorati';
			$ua['ua_type'] = 'Robot';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Technoratibot');
			} //detailed
		} elseif(ereg('iTunes', $ua_string)) {
			$ua['name'] = 'iTunes';
			$ua['ua_class'] = 'iTunes';
			$ua['ua_type'] = 'Browser';
		} elseif(ereg('W3C_Validator', $ua_string)) {
			// ---------------------------------------------------------------------------------------------- Validators
			$ua['name'] = 'W3C Validator';
			$ua['ua_class'] = 'W3C';
			$ua['ua_type'] = 'Validator';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'W3C_Validator');
			} //detailed
		} elseif(ereg('CSE HTML Validator', $ua_string)) {
			$ua['name'] = 'CSE HTML Validator';
			$ua['ua_type'] = 'Validator';
		} elseif(ereg('Jigsaw', $ua_string)) {// W3C_CSS_Validator
			$ua['name'] = 'Jigsaw (W3C CSS Validator)';
			$ua['ua_class'] = 'Jigsaw';
			$ua['ua_type'] = 'Validator';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Jigsaw');
			} //detailed
		} elseif(ereg('FeedValidator', $ua_string)) {
			$ua['name'] = 'FeedValidator';
			$ua['ua_class'] = 'FeedValidator';
			$ua['ua_type'] = 'Validator';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'FeedValidator');
			} //detailed
		} elseif(ereg('WDG_Validator', $ua_string)) {
			$ua['name'] = 'WDG_Validator';
			$ua['ua_class'] = 'WDGValidator';
			$ua['ua_type'] = 'Validator';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'WDG_Validator');
			} //detailed
		} elseif(ereg('Page Valet', $ua_string)) {
			$ua['name'] = 'Page Valet';
			$ua['ua_type'] = 'Validator';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Page Valet');
			} //detailed
		} elseif(ereg('NetMechanic', $ua_string)) {
			$ua['name'] = 'NetMechanic';
			$ua['ua_type'] = 'Validator';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'NetMechanic', ' ');
			} //detailed
		} elseif(eregi('amaya', $ua_string)) {
			$ua['name'] = 'Amaya';
			$ua['ua_class'] = 'Amaya';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'amaya');
			} //detailed
		} elseif(eregi('Incutio', $ua_string)) {
			$ua['name'] = 'Incutio';
			$ua['ua_class'] = 'Incutio';
			$ua['ua_type'] = 'Robot';
		} elseif(eregi('SeaMonkey', $ua_string)) {
			$ua['name'] = 'SeaMonkey';
			$ua['ua_class'] = 'SeaMonkey';
			$ua['ua_type'] = 'Browser';
			if($detailed) {
				if(eregi('SeaMonkey/1.0a', $ua_string)) {
					$ua['ua_version'] = '1.0a';
					$ua['ua_class'] = 'SeaMonkeyOld';
				} elseif(eregi('SeaMonkey/1.0b', $ua_string)) $ua['ua_version'] = '1.0b';
				else $ua['ua_version'] = $this->decodeUAVersion($ua_string, 'SeaMonkey');
			} //detailed
		} elseif(eregi('AvantGo', $ua_string)) {
			$ua['name'] = 'AvantGo';
			$ua['ua_class'] = 'AvantGo';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'AvantGo', ' ');
			} //detailed
		} elseif(ereg('Mozilla/4', $ua_string)) {
			$ua['name'] = 'Navigator';
			$ua['ua_class'] = 'Navigator';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'Mozilla');
			} //detailed
		} elseif(ereg('Mozilla/5.0', $ua_string)) {
			$ua['name'] = 'Mozilla';
			$ua['ua_class'] = 'Mozilla';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'rv', ':');
			} //detailed
		} elseif(ereg('Gecko', $ua_string)) {
			$ua['name'] = 'Gecko';
			$ua['ua_class'] = 'Gecko';
			$ua['ua_type'] = 'Browser';
		} elseif(eregi('NetPositive', $ua_string)) {
			$ua['name'] = 'NetPositive';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'Browser';
			
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'NetPositive');
			} //detailed
		} elseif(ereg('curl', $ua_string)) {
			$ua['name'] = 'cURL';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'Browser';
	
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'curl');
			} //detailed
		} elseif(ereg('ELinks', $ua_string)) {
			$ua['name'] = 'ELinks';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'Browser';
	
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'ELinks');
			} //detailed
		} elseif(ereg('AmigaVoyager', $ua_string)) {
			$ua['name'] = 'AmigaVoyager';
			$ua['ua_class'] = 'Default';
			$ua['ua_type'] = 'Browser';
	
			if($detailed) {
				$ua['ua_version'] = $this->decodeUAVersion($ua_string, 'AmigaVoyager');
			} //detailed
		}
	
		// La détection des robots vient après la détection des navigateurs, car certains se font passer
		// pour des navigateurs. On conservera donc le nom du navigateur mais en sachant qu'il s'agit d'un robot.
	
		$robots = array('crawler', 'Freedom2Cache', 'polybot', 'Chilkat', 'LocalcomBot', 'larbin', 'Minuteman', 'Openfind', 
	'Python-urllib', 'Snoopy', 'Onet.pl', 'Ultraknowledge', 'FyberSpider', 'A2B', 'ping.blo.gs', 'Baiduspider', 'w3m',
	'Syndic8', 'BecomeBot', 'Pompos', 'nicebot', 'MovableType', 'Crawler', 'ichiro', 'Combine', 'Findexa', 'ELinks',
	'NutchCVS', 'MSRBOT', 'MJ12bot', 'appie', 'arameda.com', 'IconSurf', 'Sqworm', 'Gigabot', 'LinkWalker', 'AlbertBot',
	'Yahoo-MMCrawler', 'EverbeeCrawler', 'ISC Systems iRc Search', 'libwww-perl', 'RssBandit', 'SBIder', 'Wget', 'Arachmo',
	'SVSearchRobot', 'HTML Tidy fetchbot', 'RSS-SPIDER', 'htdig', 'RSSOwl', 'SVSpider', 'Mozdex', 'W3C-checklink',
	'InternetArchive', 'HenryTheMiragoRobot', 'HenriLeRobotMirago', 'lmspider', 'rssImagesBot', 'yacy', 'EARTHCOM.info',
	'Missigua Locator', 'Twisted PageGetter', 'get-lang parser', 'TrackBackBot', 'BlogPulse', 'always.hiding-out.com',
	'MojeekBot', 'aipbot', 'UofTDB_experiment', 'Oracle Ultra Search', 'pipeLiner', 'CydralSpider', 'BlogsNowBot',
	'robot@mycatalog.ru', 'AIBOT', 'NG/2.0', 'WebIndexer', 'searchbot', 'Lonopono', 'Jakarta', 'Blogslive', 'topicblogs',
	'Gaisbot', 'Kronenbourg 1664', 'tvholbot', 'OpenIntelligenceData', 'www.petitsage.fr', 'testbot', 'mod_accessibility',
	'gsa-crawler', 'almaden.ibm.com', 'Xenu Link Sleuth', 'BruinBot', 'vspider', 'My Spider', 'TopSecretAgent',
	'ZeBot_www.ze.bz', 'updated/0.1beta', 'messor.redants.net', 'QweeryBot', 'SurveyBot', 'geniebot', 'Blogshares',
	'Dumbot', 'HooWWWer', 'noxtrumbot', 'Omnipelagos', 'Mediapartners-Google', 'PsSpider', 'VoilaBot', 'Septera',
	'WordPress', 'ZyBorg', 'Port Huron Labs', 'WebRobot', 'Nutch', 'axod', 'URI::Fetch', 'BlogsNowBot', 'geourl',
	'psbot', 'Calif Univ Tools', 'edgeio-retriever', 'WebZIP', 'HTTP::Lite', 'Everest-Vulcan', 'cfetch', 'Frontier', 
	'WEP Search', 'simon', 'Mo College', 'xirq', 'W3CRobot', 'Webdup', 'SearchIt-Bot', 'vivisimo', 'findlinks',
	'Blogpulse', 'Mizzu', 'GT::WWW', 'EARTHCOM.info', 'OutfoxBot', 'EmeraldShield', 'Ipselonbot', 'findlinks',
	'Bitacle', 'CFNetwork', 'Baldric', 'intraVnews', 'pavuk', 'ActiveLink', 'oBot', 'Prodiance', 'Custo',
	'EmailSiphon', 'RufusBot', 'Opquast Test', 'ProxOper', 'iSiloX', 'ASPseek', 'sohu agent', 'Java1.4.0',
	'Strategic Board Bot', 'findlinks', 'WebMiner', 'g2Crawler', 'k2spider', 'DataSpider', 'voyager', 'one0.com',  'http://herbert.groot.jebbink.nl/?app=ImagesHereImagesThereImagesEverywhere', 'Twiceler', 'MSFrontPage/4.0', 
	'Zeusbot', 'Ultraseek', 'Indy Library', 'BlogSearch', 'Web Downloader', 'Misterbot', 'EbiNess', 'Teleport Pro',
	'gozilla', 'Exabot', 'Blogobot', 'SiteSucker', 'ImagesHereImagesThereImagesEverywhere', 'Pockey',
	'Program Shareware', 'PerMan Surfer', 'genieBot', 'LetsCrawl.com', 'sohu-search');
	
		foreach($robots as $bot)
		{
			if(ereg($bot, $ua_string)) {
				$ua['ua_type'] = 'Robot';
				break;
			}
		}
	
	
		// OS / plateforme
	
		if (ereg('Win' ,$ua_string)) {	//http://msdn.microsoft.com/workshop/author/dhtml/overview/aboutuseragent.asp
			$ua['os'] = 'Windows';
			$ua['os_class'] = 'Windows';
			
			if($detailed) {
				if(eregi('Windows NT 5.1', $ua_string)) {
					$ua['os_version'] = 'XP';
					$ua['os_class'] = 'WinXP';
	
					if(eregi('Media Center PC 2.8', $ua_string)) {
						$ua['os_version'] = 'Media Center 2004';
						$ua['os_class'] = 'WinXP';
					} elseif(eregi('Media Center PC 3.0', $ua_string)) {
						$ua['os_version'] = 'Media Center 2005';
						$ua['os_class'] = 'WinXP';
					} elseif(eregi('Media Center PC 3.1', $ua_string)) {
						$ua['os_version'] = 'Media Center 2005 + Update 1';
						$ua['os_class'] = 'WinXP';
					} elseif(eregi('Media Center PC 4.0', $ua_string)) {
						$ua['os_version'] = 'Media Center 2005 + Update 2';
						$ua['os_class'] = 'WinXP';
					} elseif(eregi('SV1', $ua_string)) {
						$ua['os_version'] = 'XP (SP2)';
						$ua['os_class'] = 'WinXP';
					}
				} elseif(eregi('Win 9x 4.90', $ua_string)) {
					$ua['os_version'] = 'Me';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows ME', $ua_string)) {
					$ua['os_version'] = 'Me';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows 98', $ua_string)) {
					$ua['os_version'] = '98';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows 95', $ua_string)) {
					$ua['os_version'] = '95';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows NT 5.01', $ua_string)) {
					$ua['os_version'] = '2000 (SP1)';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows NT 5.0', $ua_string)) {
					$ua['os_version'] = '2000';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Win95', $ua_string)) {
					$ua['os_version'] = '95';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Win98', $ua_string)) {
					$ua['os_version'] = '98';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows NT 4.0', $ua_string)) {
					$ua['os_version'] = 'NT 4.0';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('WinNT', $ua_string)) {
					$ua['os_version'] = 'NT';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows NT 5.2', $ua_string)) {
					$ua['os_version'] = 'Server 2003';
					$ua['os_class'] = 'WinXP';
				} elseif(eregi('Windows NT 6.0', $ua_string)) {
					$ua['os_version'] = 'Vista';
					$ua['os_class'] = 'WindowsVista';
				} elseif(eregi('Windows NT', $ua_string)) {
					$ua['os_version'] = 'NT';
					$ua['os_class'] = 'Win9x';
				} elseif(eregi('Windows XP', $ua_string)) {
					$ua['os_version'] = 'XP';
					$ua['os_class'] = 'WinXP';
				} elseif(eregi('Windows CE', $ua_string)) {
					$ua['os_version'] = 'CE';
					$ua['os_class'] = 'WindowsCE';
				}
			} //detailed
		} elseif(eregi('Linux',$ua_string)) {
			$ua['os'] = 'Linux';
			$ua['os_class'] = 'Linux';
			
			if($detailed) {
				if(eregi('Ubuntu', $ua_string)) {
					$ua['os_version'] = 'Ubuntu';
					$ua['os_class'] = 'Ubuntu';
				} elseif(eregi('Debian', $ua_string)) {
					$ua['os_version'] = 'Debian';
					$ua['os_class'] = 'Debian';
				} elseif(eregi('Fedora', $ua_string)) {
					$ua['os_version'] = 'Fedora';
					$ua['os_class'] = 'Fedora';
				} elseif(eregi('Red Hat', $ua_string)) {
					$ua['os_version'] = 'Red Hat';
					$ua['os_class'] = 'RedHat';
				} elseif(eregi('redhat', $ua_string)) {
					$ua['os_version'] = 'Red Hat';
					$ua['os_class'] = 'RedHat';
				} elseif(eregi('slackware', $ua_string)) {
					$ua['os_version'] = 'Slackware';
					$ua['os_class'] = 'Slackware';
				} elseif(eregi('Mandriva', $ua_string)) {
					$ua['os_version'] = 'Mandriva';
					$ua['os_class'] = 'Mandriva';
				} elseif(eregi('mdk', $ua_string)) {
					$ua['os_version'] = 'Mandrake';
					$ua['os_class'] = 'Mandriva';
				} elseif(eregi('SUSE', $ua_string)) {
					$ua['os_version'] = 'SUSE';
					$ua['os_class'] = 'SUSE';
				} elseif(eregi('Gentoo', $ua_string)) {
					$ua['os_version'] = 'Gentoo';
					$ua['os_class'] = 'Gentoo';
				} elseif(eregi('XandrOS', $ua_string)) {
					$ua['os_version'] = 'XandrOS';
					$ua['os_class'] = 'XandrOS';
				} elseif(eregi('gnu', $ua_string)) {
					$ua['os_version'] = 'GNU';
					$ua['os_class'] = 'GNU';
				}
			} //detailed
		} elseif(ereg('Mac',$ua_string)) {
			$ua['os'] = 'Mac';
			$ua['os_class'] = 'MacOS9';
			
			if($detailed) {
				if(eregi('Mac OS X', $ua_string)) {
					$ua['os_version'] = 'OS X';
					$ua['os_class'] = 'MacOSX';
				} elseif(eregi('PPC', $ua_string)) {
					$ua['os_version'] = 'OS 9';
					$ua['os_class'] = 'MacOS9';
				} elseif(eregi('Mac_PowerPC', $ua_string)) {
					$ua['os_version'] = 'OS 9';
					$ua['os_class'] = 'MacOS9';
				}
			} //detailed
		} elseif(ereg('FreeBSD',$ua_string)) {
			$ua['os'] = 'FreeBSD';
			$ua['os_class'] = 'FreeBSD';
		} elseif(ereg('SunOS',$ua_string)) {
			$ua['os'] = 'SunOS';
			$ua['os_class'] = 'SunOS';
			
			if($detailed) {
				if(eregi('SunOS 5.8 sun4u', $ua_string)) $ua['os_version'] = '5.8';
				elseif(eregi('SunOS sun4u', $ua_string)) $ua['os_version'] = 'sun4u';
			} //detailed
		} elseif(ereg('DragonFly',$ua_string)) {
			$ua['os'] = 'DragonFlyBSD';
			$ua['os_class'] = 'DragonFly';
		} elseif(ereg('Unix',$ua_string)) {
			$ua['os'] = 'Unix';
			$ua['os_class'] = 'Unix';
		} elseif(ereg('BeOS',$ua_string)) {
			$ua['os'] = 'BeOS';
			$ua['os_class'] = 'BeOS';
		} elseif(ereg('RISC OS',$ua_string)) {
			$ua['os'] = 'RISC OS';
			$ua['os_class'] = 'RISCOS';
	
			if($detailed) {
				/*if(eregi('RISC OS 4.39', $ua_string)) $ua['os_version'] = '4.39';
				elseif(eregi('RISC OS 3.70', $ua_string)) $ua['os_version'] = '3.70';*/
				$ua['os_version'] = $this->decodeUAVersion($ua_string, 'RISC OS', ' ');
			} //detailed
		} elseif(ereg('AmigaOS',$ua_string)) {
			$ua['os'] = 'AmigaOS';
			$ua['os_class'] = 'Default';
	
			if($detailed) {
				$ua['os_version'] = $this->decodeUAVersion($ua_string, 'AmigaOS');
			} //detailed
		}
		return $ua;
	}
		
	function decodeUAVersion($ua_string, $ua_name, $ua_version_delimiter = '/') {
		switch($ua_version_delimiter) {
			case '/':
			$pattern = "/$ua_name\/[a-zA-Z0-9\.]*/i";break;
			
			default:
			$pattern = "/".$ua_name.$ua_version_delimiter."[a-zA-Z0-9\.]*/i";
		}
		
		if(preg_match($pattern, $ua_string, $matches)) {
			$matches_array = explode($ua_version_delimiter, $matches[0]);
			return $matches_array[1];
		}
	}
}
?>