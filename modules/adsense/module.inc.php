<?php
$sub_template = new Template("templates/".THEME."/");
$sub_template->setFile('module','adsense/module.html');
$this->addCss('templates/'.THEME.'/adsense/style_mod.css');

$sub_template->setBlock('module','xml');
$sub_template->setBlock('module','text');

if(CONTENTTYPE=='application/xhtml+xml') $sub_template->parse('xml');
else $sub_template->parse('text');
?>