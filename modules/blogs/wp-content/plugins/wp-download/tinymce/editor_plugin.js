tinyMCE.importPluginLanguagePack('wpdownload','fr');
//This var is called at bottom by tinyMCE.addPlugin
var TinyMCE_ExternalPluginPlugin = {
	//Woteva info you need
	getInfo : function() {
		return {
			longname : 'WP-Download WYSIWYG',
			author : 'Arno\'s Toolbox',
			authorurl : 'http://www.arno-box.net',
			infourl : 'http://www.arno-box.net/wordpress/12/wordpress-plugin-wp-download/',
			version : tinyMCE.majorVersion + '.' + tinyMCE.minorVersion
		};
	},
	//This adds the button image itself and its command
	getControlHTML : function(cn) {
		switch (cn) {
			case "wpdownload":
				return tinyMCE.getButtonHTML(cn, 'lang_wpdownload_desc', '{$pluginurl}/images/image.gif', 'mceWPDownloadInsert');
		}
		return "";
	},
	//This executes the button call to an external javascript
	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceWPDownloadInsert":
				WPDownloadOpenDialog();
			return true;
		}
		return false;
	}
};
//Registers the plugin name and the functions above with tinyMCE. 'externalplugin' is the plugin name called in mybuttons.php
tinyMCE.addPlugin("wpdownload", TinyMCE_ExternalPluginPlugin);