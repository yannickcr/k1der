// Load the language file
tinyMCE.importPluginLanguagePack('cforms', 'en');

var TinyMCE_cformsscript = {

	getInfo : function() {
			return {
				longname : 'cforms',
				author : 'Oliver Seidel',
				authorurl : 'http://www.deliciousdays.com',
				infourl : 'http://www.deliciousdays.com',
				version : "3.5"
			};
	},
		
		
		
	getControlHTML : function(cn) {
				switch (cn) {
							case "cforms":
								return tinyMCE.getButtonHTML(cn, 'lang_cforms_desc', '{$pluginurl}/../images/button.gif', 'mcecforms');
							}
				return "";
	},
		
		
		
	execCommand : function(editor_id, element, command, user_interface, value) {
		
		var inst = tinyMCE.getInstanceById(editor_id);
		var focusElm = inst.getFocusElement();
		var doc = inst.getDoc();

		function getAttrib(elm, name) {
				return elm.getAttribute(name) ? elm.getAttribute(name) : "";
		}

		switch (command) {
		
		case "mcecforms":

			var flag = "";
			var template = new Array();

			// Is selection a image?
			if (focusElm != null && focusElm.nodeName.toLowerCase() == "img") {
					flag = getAttrib(focusElm, 'class');
					flagIE = getAttrib(focusElm, 'className');

					if ( flag == 'mce_plugin_cforms_img' || flagIE == 'mce_plugin_cforms_img' ) // Not a wordpress
							alert("Placeholder for: " + getAttrib(focusElm,'moretext') );
					
					return true;
			}
			
			var myValue = prompt('Please enter form # or leave blank for form #1', '');
			if (myValue == null) return false;

			if (myValue=='1') myValue='';

			altMore = "Placeholder for: cforms"+myValue;
			cssstyle = 'background:url('+globalPURL+'/images/cformsmce.gif) no-repeat 5px 5px; border-top: 1px dotted #cccccc; border-bottom: 1px dotted #cccccc;';

			html = ''
				+ '<img src="'+globalPURL+'/images/spacer.gif" '
				+ 'width="100%" height="30px" moretext="cforms'+myValue+'" '
				+ 'alt="'+altMore+'" title="'+altMore+'" style="'+cssstyle+'" class="mce_plugin_cforms_img" />';

			tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, html);
			tinyMCE.selectedInstance.repaint();

//								tinyMCE.triggerNodeChange(true);
			return true;
	}
	return false;
	},
		
		
		
	cleanup : function(type, content) {
		switch (type) {

			case "insert_to_editor":
				var startPos = 0;

				// Parse all <!--more--> tags and replace them with images
				while ((startPos = content.indexOf('<!--cforms', startPos)) != -1) {
					var endPos = content.indexOf('-->', startPos) + 3;
					// Insert image
					var moreText = content.substring(startPos + 4, endPos - 3);
					altMore = "Placeholder for: "+moreText;
					cssstyle = 'background:url('+globalPURL+'/images/cformsmce.gif) no-repeat 5px 5px; border-top: 1px dotted #cccccc; border-bottom: 1px dotted #cccccc;';

					var contentAfter = content.substring(endPos);
					content = content.substring(0, startPos);
					content += '<img src="'+globalPURL+'/images/spacer.gif" ';
					content += ' width="100%" height="30px" moretext="'+moreText+'" ';
					content += 'alt="'+altMore+'" title="'+altMore+'" style="'+cssstyle+'" class="mce_plugin_cforms_img" />';
					content += contentAfter;

					startPos++;
				}
				break;

			case "get_from_editor":
				// Parse all img tags and replace them with <!--more-->
				var startPos = -1;
				while ((startPos = content.indexOf('<img', startPos+1)) != -1) {
					var endPos = content.indexOf('/>', startPos);
					var attribs = this._parseAttributes(content.substring(startPos + 4, endPos));

					if (attribs['class'] == "mce_plugin_cforms_img") {
						endPos += 2;

						var moreText = attribs['moretext'] ? attribs['moretext'] : '';
						var embedHTML = '<!--'+moreText+'-->';

						// Insert embed/object chunk
						chunkBefore = content.substring(0, startPos);
						chunkAfter = content.substring(endPos);
						content = chunkBefore + embedHTML + chunkAfter;
					}
				}
				break;
		}

		// Pass through to next handler in chain
		return content;
	},
	
	
	
	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {

		tinyMCE.switchClass(editor_id + '_cforms', 'mceButtonNormal');

		if (node == null)
			return;

		do {
			if (node.nodeName.toLowerCase() == "img" && tinyMCE.getAttrib(node, 'class').indexOf('mce_plugin_cforms_img') == 0)
				tinyMCE.switchClass(editor_id + '_cforms', 'mceButtonSelected');
		} while ((node = node.parentNode));

		return true;
	},
	
	
	
	_parseAttributes : function(attribute_string) {
		var attributeName = "";
		var attributeValue = "";
		var withInName;
		var withInValue;
		var attributes = new Array();
		var whiteSpaceRegExp = new RegExp('^[ \n\r\t]+', 'g');

		if (attribute_string == null || attribute_string.length < 2)
			return null;

		withInName = withInValue = false;

		for (var i=0; i<attribute_string.length; i++) {
			var chr = attribute_string.charAt(i);

			if ((chr == '"' || chr == "'") && !withInValue)
				withInValue = true;
			else if ((chr == '"' || chr == "'") && withInValue) {
				withInValue = false;

				var pos = attributeName.lastIndexOf(' ');
				if (pos != -1)
					attributeName = attributeName.substring(pos+1);

				attributes[attributeName.toLowerCase()] = attributeValue.substring(1);

				attributeName = "";
				attributeValue = "";
			} else if (!whiteSpaceRegExp.test(chr) && !withInName && !withInValue)
				withInName = true;

			if (chr == '=' && withInName)
				withInName = false;

			if (withInName)
				attributeName += chr;

			if (withInValue)
				attributeValue += chr;
		}

		return attributes;
	}

	
};
// Adds the plugin class to the list of available TinyMCE plugins
tinyMCE.addPlugin("cforms", TinyMCE_cformsscript );

