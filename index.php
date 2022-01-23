<?php
/**
 * Détection du mode du client
 */

// iMode
//if(stristr($_SERVER["HTTP_USER_AGENT"],"portalmmm")) header('location:http://imode.k1der.net');
// WAP
//if(stristr($_SERVER["HTTP_ACCEPT"],"text/vnd.wap.wml")) header('location:http://wap.k1der.net');

// Normal
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('session.use_trans_sid',false);
ini_set('default_charset','UTF-8');

session_save_path('tmp');
session_start();

// Constantes
define('TITLE','K1der.net');
define('CHARSET','UTF-8');

if(!isset($_SERVER["HTTP_USER_AGENT"])) $_SERVER["HTTP_USER_AGENT"]='';
define('PSP',stristr($_SERVER["HTTP_USER_AGENT"],"PlayStation Portable")?true:false);

ob_start("ob_gzhandler");

require_once('include/config.php');		// Chargement du fichier de configuration
require_once('include/autoload.php');	// Fonction d'autoload

$string	= new string();														// Création de l'objet string
$sql	= new mysql($bdd['Host'], $bdd['User'], $bdd['Pass'], $bdd['Base']);// Création de l'objet sql
$module	= new modules();													// Création de l'objet module
$site	= new site();														// Création de l'objet site
$membres= new membres();

//require_once('include/librairies/error_reporter.php');

define('THEME',$site->config('theme'));

$template = new template('templates/'.THEME.'/');

$template->setVar('header.title','K1der');
$m=array('a','b','c','d','e','f','g','h','i','j','k','l');
$template->setVar('ver','.'.$m[date('m')-1].'.'.date('d'));

// Oui ? Non ?  méditer
//if(!isset($_SERVER['REDIRECT_URL'])) $site->error('Accs incorrecte au site');


$module->page();
$site->makePage();
$template->p('parse');
ob_flush();
?>
