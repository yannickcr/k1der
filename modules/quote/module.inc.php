<?php
$sub_template = new Template("templates/".THEME."/");
$sub_template->setFile('module','quote/module.html');
$this->addJs('modules/quote/js/quote.js');

$this->addCss('templates/'.THEME.'/quote/style.css');

$info=$sql->fetchArray($sql->query('SELECT phrase FROM mod_quote ORDER BY rand() LIMIT 1'));

$template->setVar('phrase',$info['phrase']);
?>