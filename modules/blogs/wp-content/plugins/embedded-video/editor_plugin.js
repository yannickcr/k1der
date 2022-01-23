if (tinyMCE.settings['language'] != 'en' && tinyMCE.settings['language'] != 'de_de') {
	var temp = tinyMCE.settings['language'];
	tinyMCE.settings['language'] = 'en';
	tinyMCE.importPluginLanguagePack('embeddedvideo','en');
	tinyMCE.settings['language'] = temp;
} else {
	tinyMCE.importPluginLanguagePack('embeddedvideo','en, de_de');
}

tinyMCE.settings['language'] = temp;

var TinyMCE_embeddedvideoPlugin = {
	getInfo : function() {
		return {
			longname : 'Embedded Video with Link',
			author : 'Stefan He&szlig;',
			authorurl : 'http://www.jovelstefan.de',
			infourl : 'http://www.jovelstefan.de/embedded-video/',
			version : "1.0"
		};
	},
	getControlHTML : function(cn) {
		switch (cn) {
			case "embeddedvideo":
				return tinyMCE.getButtonHTML(cn, 'lang_embeddedvideo_title', '{$pluginurl}/embeddedvideo-button.png', 'mce_embeddedvideo');
		}
		return "";
	},
	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mce_embeddedvideo":
				embeddedvideo_insert('wp21');
				return true;
		}
		return false;
	}
};

tinyMCE.addPlugin('embeddedvideo', TinyMCE_embeddedvideoPlugin);