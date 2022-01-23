<?php
/**
 * Header de la page.
 * Doctype suivant le type de navigateur (IE 6 non compatible application/xhtml+xml).
 * Ajout des CSS par défaut.
 * Le style CSS du body est diffrent suivant que l'on utilise le bloc de gauche ou pas.
 *
 * @author    Yannick Croissant
 * @package   K1der
 */
$template->setFile('header','header.html'); 

$template->setBlock('header','doctype1.0');
$template->setBlock('header','doctype1.1');

if(CONTENTTYPE=='application/xhtml+xml') $template->parse('doctype1.1');
else $template->parse('doctype1.0');

$template->setVar('header.contentType',CONTENTTYPE.'; charset='.CHARSET);
	
$this->addCss($template->root.'style.css','screen',true);

$this->addJs('include/js/scripts.inc.js',true);
$this->addJs('include/js/lightbox.inc.js');
$template->setVar('header.base',dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/');
$this->toparse[]='header';

// PHP-Stats
//define('__PHP_STATS_PATH__','include/scripts/phpstats/');
//include(__PHP_STATS_PATH__.'php-stats.redir.php');
//$this->addJs('stats/php-stats.js.php',true);
?>