<?php
$sub_template = new template("templates/".THEME."/");
$sub_template->setFile('module','donate/module.html');
$this->addCss('templates/'.THEME.'/donate/style_mod.css');
$this->addJs('include/js/interstitiel.inc.js');
$this->addJs('modules/donate/js/donate.js');
?>