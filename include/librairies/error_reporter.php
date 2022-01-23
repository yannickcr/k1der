<?php
function errorReporter($errno , $errstr , $errfile , $errline) {
	global $membres,$string;
	// Variables nécessaires au traitement des erreurs
	// Boolen permettant d'indiquer si on doit stopper ou non l'excution du script
	// Par dfaut on stop
	$stopper = false;
	
	// Boolen permettant d'indiquer si on doit ou non afficher le message d'erreur
	// Par dfaut on masque
	$afficher = false;
	
	// On dtermine le type d'erreur et on affecte les variables et le cas chant
	switch ($errno) {
		case E_USER_NOTICE :
		case E_NOTICE : {
			$stopper = false;
			$type = "Notification";
			break;
		}
		
		case E_COMPILE_WARNING :
		case E_CORE_WARNING :
		case E_USER_WARNING :
		case E_WARNING : {
			$stopper = false;
			$type = "Avertissement";
			break;
		}
		
		case E_PARSE : {
			$afficher = false;
			$type = "Syntaxe";
		}
		
		case E_COMPILE_ERROR :
		case E_CORE_ERROR :
		case E_USER_ERROR :
		case E_ERROR : {
			$afficher = false;
			$type = "Erreur";
			break;
		}
		
		default : {
			//echo "Erreur inconnue : [" . $errno . "] => " . $errstr . "<br>";
			$afficher = false;
			$type = "Erreur inconnue";
			break;
		}
	}
	
	// Messages  ignorer
	if($type=='Erreur inconnue') return true;
	
	// Construction du message d'erreur

	if(isset($_SERVER['HTTP_REFERER'])) $referer='[url='.$_SERVER['HTTP_REFERER'].']'.$_SERVER['HTTP_REFERER'].'[/url]';
	else $referer='Aucune';
	
	if($membres->infos('id')) $profil='[url=membres/'.$string->clean($membres->infos('pseudo')).']'.$membres->infos('pseudo').'[/url]';
	else $profil='Visiteur non identifi';
	
	$message='[b][u]Rapport d\'une Erreur PHP[/u][/b]'."\r\n\r\n";
	$message.='Date et Heure de l\'erreur : '.$string->formatDate('%A %d %B %Y',date('U'),true).'  '.$string->formatDate('%H:%M',date('U'))."\r\n";
	$message.='Adresse de la page : '.$_SERVER['REQUEST_URI']."\r\n";
	$message.='Adresse d\'o provient le visiteur : '.$referer."\r\n";
	$message.='Adresse IP du visiteur : [url=admin/whois-'.str_replace('.','-',$_SERVER['REMOTE_ADDR']).'.html]'.$_SERVER['REMOTE_ADDR'].'[/url]'."\r\n";
	$message.='Profil du visiteur : '.$profil."\r\n\r\n";
	$message.='[b]Rsum de l\'erreur[/b]'."\r\n\r\n";
	$message.= 'Type : '.$type."\r\n";
	$message.= 'Rsum : '.$errstr."\r\n";
	$message.= 'Fichier : '.$errfile."\r\n";
	$message.= 'Ligne : '.$errline."\r\n";
	
	$membres->sendMessage(1,'Country','Erreur PHP',$message,1);
	
	// On teste la valeur de la variable
	if ($afficher == true) echo $message;
	
	// On enregistre l'erreur dans le fichier '/var/php/erreurs.log'
	//error_log ($errstr.' : '.$errfile.' on line '.$errline."\r\n" , 3 , "erreurs.log");
	// On teste la valeur de la variable $stopper
	if ($stopper == true) exit ();
}

error_reporting(E_ALL);
//set_error_handler("errorReporter");
?>