<?php
/*
Plugin Name: NextGEN Gallery
Plugin URI: http://alexrabe.boelinger.com/?page_id=80
Description: A NextGENeration Photo gallery
Author: NextGEN DEV-Team
Version: 0.99.1

Author URI: http://alexrabe.boelinger.com/

Copyright 2007-2008 by Alex Rabe & NextGEN DEV-Team

The NextGEN button is taken from the Silk set of FamFamFam. See more at 
http://www.famfamfam.com/lab/icons/silk/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Please note  :

The JW Image Rotator (Slideshow) is not part of this license and is available
under a Creative Commons License, which allowing you to use, modify and redistribute 
them for noncommercial purposes. 

For commercial use please look at the Jeroen's homepage : http://www.jeroenwijering.com/ 

*/ 

//#################################################################
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

//#################################################################
// Let's Go
	
global $wpdb, $wp_version, $wpmu_version, $wp_roles;

// ini_set('display_errors', '1');
// ini_set('error_reporting', E_ALL);

// Check for WPMU installation
define('IS_WPMU', version_compare($wpmu_version, '1.3', '>=') );
// Check for WP2.1 or higher
define('IS_WP21_COMPATIBLE', version_compare($wp_version, '2.1', '>=') );
// Check for WP2.5 installation
define('IS_WP25', version_compare($wp_version, '2.4', '>=') );

//This works only in WP2.1 or higher
if ( (IS_WP21_COMPATIBLE == FALSE) and (IS_WPMU != TRUE) ){
	add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, NextGEN Gallery works only under WordPress 2.1 or higher',"nggallery") . '</strong></p></div>\';'));
	return;
}

$memory_limit = (int) substr( ini_get('memory_limit'), 0, -1);
//This works only with enough memory, 8MB is silly, wordpress requires already 7.9999
if ( ($memory_limit != 0) && ($memory_limit < 12 ) ) {
	add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, NextGEN Gallery works only with a Memory Limit of 16 MB higher',"nggallery") . '</strong></p></div>\';'));
	return;
}

// Version and path to check version
define('NGGVERSION', "0.99.1");
// Minimum required database version
define('NGG_DBVERSION', "0.84");
define('NGGURL', "http://nextgen.boelinger.com/version.php");

// required for Windows & XAMPP
$myabspath = str_replace("\\","/",ABSPATH);  
define('WINABSPATH', $myabspath);

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', WINABSPATH . 'wp-content' );

define('NGGFOLDER', plugin_basename( dirname(__FILE__)) );
define('NGGALLERY_ABSPATH', WP_CONTENT_DIR.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );
define('NGGALLERY_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );

// look for imagerotator
define('NGGALLERY_IREXIST', file_exists(NGGALLERY_ABSPATH.'imagerotator.swf'));

// get value for safe mode
if ((gettype(ini_get('safe_mode')) == 'string')) {
	// if sever did in in a other way
	if (ini_get('safe_mode') == 'off') define('SAFE_MODE', FALSE);
	else define('SAFE_MODE', ini_get('safe_mode'));
} else
define('SAFE_MODE', ini_get('safe_mode'));

//pass the init check or show a message
if (get_option( "ngg_init_check" ) != false )
	add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . get_option( "ngg_init_check" ) . '</strong></p></div>\';'));

//read the options
$ngg_options = get_option('ngg_options');

// add database pointer 
$wpdb->nggpictures					= $wpdb->prefix . 'ngg_pictures';
$wpdb->nggallery					= $wpdb->prefix . 'ngg_gallery';
$wpdb->nggalbum						= $wpdb->prefix . 'ngg_album';
$wpdb->nggtags						= $wpdb->prefix . 'ngg_tags';
$wpdb->nggpic2tags					= $wpdb->prefix . 'ngg_pic2tags';

// Load language
function nggallery_init ()
{
	if (function_exists('load_plugin_textdomain')) {
		if ( !defined('WP_PLUGIN_DIR') ) {
			load_plugin_textdomain('nggallery','wp-content/plugins/' . NGGFOLDER . '/lang');
			//load_plugin_textdomain('nggallery', str_replace( ABSPATH, '', dirname(__FILE__) ) . '/lang');
		} else {
			load_plugin_textdomain('nggallery', false, dirname(plugin_basename(__FILE__)) . '/lang');
		}
	}
}

// Load the admin panel
if (is_admin()) {
	
	include_once (dirname (__FILE__)."/ngginstall.php");

	// check for upgrade
	if( get_option( "ngg_db_version" ) != NGG_DBVERSION ) 
		ngg_upgrade();
	
	if (IS_WP25)
		include_once (dirname (__FILE__)."/admin/wp25/admin.php");
	else
		include_once (dirname (__FILE__)."/admin/admin.php");
		
} else {
	
	// Load the gallery generator
	include_once (dirname (__FILE__)."/nggfunctions.php");
	
	// required in WP 2.5, NextGEN should have higher priority than the shortcode
	// see also http://trac.wordpress.org/ticket/6436 	
	if (IS_WP25) {	
		remove_filter('the_content', 'do_shortcode', 9);
		add_filter('the_content', 'do_shortcode', 11);
	}
	
	// Action calls for all functions 
	add_filter('the_content', 'searchnggallerytags', 10);
	add_filter('the_excerpt', 'searchnggallerytags', 10);
}

// Load tinymce button 
if (IS_WP25)
	include_once (dirname (__FILE__)."/tinymce3/tinymce.php");
else
	include_once (dirname (__FILE__)."/tinymce/tinymce.php");
	
// Load gallery class
require_once (dirname (__FILE__).'/lib/nggallery.lib.php');

// Init the gallery class
$nggallery = new nggallery();

// Add rewrite rules
$nggRewrite = new nggRewrite();

// add javascript to header
add_action('wp_head', 'ngg_addjs', 1);
function ngg_addjs() {
    global $wp_version, $ngg_options;
    
	echo "<meta name='NextGEN' content='".NGGVERSION."' />\n";
	if ($ngg_options['activateCSS']) 
		echo "\n".'<style type="text/css" media="screen">@import "'.NGGALLERY_URLPATH.'css/'.$ngg_options['CSSfile'].'";</style>';
	if ($ngg_options['thumbEffect'] == "thickbox") {
		echo "\n".'<script type="text/javascript"> var tb_pathToImage = "'.NGGALLERY_URLPATH.'thickbox/'.$ngg_options['thickboxImage'].'";</script>';
		echo "\n".'<style type="text/css" media="screen">@import "'.NGGALLERY_URLPATH.'thickbox/thickbox.css";</style>'."\n";
	    if ($wp_version < "2.5") {
	    	if ($wp_version > "2.1.3") wp_deregister_script('jquery'); 
	    	wp_enqueue_script('jquery', NGGALLERY_URLPATH .'admin/js/jquery.js', FALSE, '1.2.2');
		} 
			if (IS_WP25)
	    		wp_enqueue_script('ngg-thickbox', NGGALLERY_URLPATH .'thickbox/thickbox-pack.js', array('jquery'), '3.1.1');
	    	else
	    		wp_enqueue_script('thickbox', NGGALLERY_URLPATH .'thickbox/thickbox-pack.js', array('jquery'), '3.1.1');
	    }
	    
	// test for wordTube function
	if (!function_exists('integrate_swfobject')) {
		wp_enqueue_script('swfobject', NGGALLERY_URLPATH .'admin/js/swfobject.js', FALSE, '2.1');
	}
}

// load language file
add_action('init', 'nggallery_init');

// Init options & tables during activation 
// add_action('activate_' . NGGFOLDER.'/nggallery.php', 'ngg_install');
// WP recommended function, not used until 2.2.3
register_activation_hook(NGGFOLDER.'/nggallery.php','ngg_install');
register_deactivation_hook(NGGFOLDER.'/nggallery.php','ngg_deinstall');

// init tables in wp-database if plugin is activated
function ngg_install() {
	// Check for tables
	nggallery_install();
}

function ngg_deinstall() {
	// remove & reset the init check option
	delete_option( "ngg_init_check" );
}

// Content Filters
add_filter('ngg_gallery_name', 'sanitize_title');

?>