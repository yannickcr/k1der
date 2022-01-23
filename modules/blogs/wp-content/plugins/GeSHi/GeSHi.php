<?php
/*
Plugin Name: GeSHi
Plugin URI: http://www.arno-box.net/plugins/geshi
Description: GeSHi is a Free WordPress Plugin, it aims to be a simple but powerful highlighting class.
Version: 1.0
Author: Arno's Toolbox
Author URI: http://www.arno-box.net/

This program is free software; you can redistribute it and/or
modify it under the terms of the CC-GNU GPL.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
CC-GNU GPL for more details.

The license is available at http://creativecommons.org/licenses/GPL/2.0/
*/

include_once('libgeshi/geshi.php');

function GeSHi_Callback($matched) {
	$lang = $matched[1];
	$code = $matched[2];

	/* On remplace les double quote */
	$code = str_replace("&#8221;","\"", $code);
	$code = str_replace("&#8220;","\"", $code);
	
	/* Instantiation de la classe */
	$geshi =& new GeSHi($code, $lang);
	
	/* Remplacement des tabulations par 4 espaces */
	$geshi->set_tab_width(4);
	
	/* On numérote les lignes */
	$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
	
	/* Définition du cadre et de la couleur de fond */
	$geshi->set_overall_style('color: #000066; border-left: 5px solid rgb(195, 215, 234); background-color: rgb(240, 240, 240); padding:1px;', true);

	/* Style à appliquer aux lignes */
	$geshi->set_line_style('font: normal normal 8pt \'Courier New\', Courier, monospace; color: #003030;', 'font-weight: bold; color: #006060;', true);

	/* On défini de faire un retour dans un div pour remplacer les tabulations */
	$geshi->set_header_type(GESHI_HEADER_DIV);

	/* On retourne le code modifié */
	$parsed_code = $geshi->parse_code();
	$nb_line = count(split("\n", $parsed_code));
	if ($nb_line > 225) 
		return "<div style=\"overflow: auto; width: 478px; height: 300px; padding-bottom: 15px;\">".$parsed_code."</div>";
	return "<div style=\"overflow: auto; width: 478px;padding-bottom: 15px;\">".$parsed_code."</div>";
}

function GeSHi($content) {
	$pattern = '/<pre\s*lang="([^"]*)">(.*)<\/pre>/Us';
	$content = preg_replace_callback($pattern, 'GeSHi_Callback', $content);
	return $content;
}

add_filter('the_content', 'GeSHi');

?>
