<?php
// Vérification des droits d'accès
if($membres->infos('groupe')!=4) $site->error(1);

$admin = new admin();

$template->setFile('centre','admin/index.html');  

$this->noUse(array('gauche','droite'));

$site->addToTitle(' - Administration');
$site->addCss('templates/'.THEME.'/admin/style.css');

$menu=$admin->genMenu();
$template->setVar('menu',$menu);

$sub_template = new template("templates/".THEME."/");
$submodule='';
$action='';
if (isset($_GET['submodule'])) $submodule=$_GET['submodule'].'/';
if (isset($_GET['action'])) $action=$_GET['action'];
else $action='index';
$file='modules/'.$submodule.'admin/'.$action.'.inc.php';
if(file_exists($file)) include_once $file;
$file='modules/'.$submodule.'admin/js/messages.js';
if(file_exists($file)) $site->addJs($file);
$sub_template->setVar("THEME",THEME);
$template->setVar('res_centredroite',$sub_template->globalParse('parse','centredroite',true));
?>
