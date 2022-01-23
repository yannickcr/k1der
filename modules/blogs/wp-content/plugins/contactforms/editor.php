<?php
function cforms_addbuttons() {

		global $wp_db_version;
		global $cforms_root;

		// Check for WordPress 2.1+ and activated RTE
		if ( 3664 <= $wp_db_version && 'true' == get_user_option('rich_editing') ) {
				// add the button for wp21 in a new way
				add_filter("mce_plugins", "cforms_button_plugin", 0);
				add_filter('mce_buttons', 'cforms_button', 0);
				add_action('tinymce_before_init','cforms_button_script');
		}
		else {
				// Do it in the old way with buttonsnap
				$button_image_url = $cforms_root . '/images/buttonpic.gif';
				buttonsnap_separator();
				buttonsnap_jsbutton($button_image_url, __('cforms', 'cforms'), 'cforms_buttonscript();');

		}

}

// used to insert button in wordpress 2.1x editor
function cforms_button($buttons) {
		array_push($buttons, "separator", "cforms");
		return $buttons;
}

// Tell TinyMCE that there is a plugin (wp2.1)
function cforms_button_plugin($plugins) {
		array_push($plugins, "-cforms");
		return $plugins;
}


// Load the TinyMCE plugin : editor_plugin.js (wp2.1)
function cforms_button_script() {
		global $cforms_root;
		$pluginURL = $cforms_root .'/js/';
		echo 'tinyMCE.loadPlugin("cforms", "'.$pluginURL.'");'."\n";
		return;
}

// Load the Script for the Button(wp2.1)
function insert_cforms_script() {
		global $cforms_root;
		echo "\n".'<script type="text/javascript">
		          var globalPURL = "'.$cforms_root.'";
		          
							function cforms_buttonscript() {
							
								function edInsertContent(myField, myValue) {
									//IE support
									if (document.selection) {
										myField.focus();
										sel = document.selection.createRange();
										sel.text = myValue;
										myField.focus();
									}
									//MOZILLA/NETSCAPE support
									else if (myField.selectionStart || myField.selectionStart == \'0\') {
										var startPos = myField.selectionStart;
										var endPos = myField.selectionEnd;
										myField.value = myField.value.substring(0, startPos)
										              + myValue
								                      + myField.value.substring(endPos, myField.value.length);
										myField.focus();
										myField.selectionStart = startPos + myValue.length;
										myField.selectionEnd = startPos + myValue.length;
									} else {
										myField.value += myValue;
										myField.focus();
									}
								}

								var myValue = prompt(\'Please enter form # or leave blank for form #1\', \'\');
								if (myValue == null) return false;
								
								if (myValue==\'1\') myValue=\'\';

								myValue = \'<!--cforms\' + myValue + \'-->\';
								edInsertContent(edCanvas, myValue);
							}
				</script>';
		return;
}

//
// only insert buttons if enabled!
//
if(get_option('cforms_show_quicktag') == true) {
		add_action('init', 'cforms_addbuttons');
		add_action('edit_page_form', 'insert_cforms_script');
		add_action('edit_form_advanced', 'insert_cforms_script');
}
?>
