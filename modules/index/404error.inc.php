<?php
/**
 * Page d'erreur 404 intelligente
 */
$template->setFile('centre','index/404error.html');
$template->setBlock('centre','plan');

if(isset($_SERVER['HTTP_REFERER'])) $referer='[url='.$_SERVER['HTTP_REFERER'].']'.$_SERVER['HTTP_REFERER'].'[/url]';
else $referer='Aucune';

if($membres->infos('id')) $profil='[url=membres/'.$string->clean($membres->infos('pseudo')).']'.$membres->infos('pseudo').'[/url]';
else $profil='Visiteur non identifi';

$message='[b][u]Rapport d\'une Erreur 404[/u][/b]'."\r\n\r\n";
$message.='Date et Heure de l\'erreur : '.$string->formatDate('%A %d %B %Y',date('U'),true).'  '.$string->formatDate('%H:%M',date('U'))."\r\n";
$message.='Adresse de la page introuvable : '.$_SERVER['REQUEST_URI']."\r\n";
$message.='Adresse d\'o provient le visiteur : '.$referer."\r\n";
$message.='Adresse IP du visiteur : [url=admin/whois-'.str_replace('.','-',$_SERVER['REMOTE_ADDR']).'.html]'.$_SERVER['REMOTE_ADDR'].'[/url]'."\r\n";
$message.='Profil du visiteur : '.$profil."\r\n";

//$membres->sendMessage(1,'Country','Erreur 404',$message,1);
?>
