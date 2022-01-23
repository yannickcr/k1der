function embeddedvideo_insert(postid) {
	
	if(window.tinyMCE) {
		var postnumber;
		
		if (postid == 'wp21') postnumber = document.getElementById('post_ID').value;
		else postnumber = (postid == 0) ? document.getElementsByName('temp_ID')[0].value : postid;
		
		var template = new Array();

		template['file'] = tinyMCE.baseURL + '/../../../wp-content/plugins/embedded-video/embedded-video-popup.php?post='+postnumber;
		template['width'] = 440;
		template['height'] = 220;

		args = {
			resizable : 'no',
			scrollbars : 'no',
			inline : 'yes'
		};

		tinyMCE.openWindow(template, args);
		return true;
	} else {
		window.alert('This function is only available in the WYSIWYG editor');
		return true;
	}
}

function ev_insertVideoCode(portal, vid, linktext) {
	var text = (linktext == '') ? ('['+ portal + ' ' + vid + ']') : ('['+ portal + ' ' + vid + ' ' + linktext + ']');
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, text);
		tinyMCE.execCommand("mceCleanup");
		tinyMCE.selectedInstance.repaint();
	} else {
		edInsertContent(edCanvas, text);
	}
	return true;
}

function ev_checkData(formObj) {	
	if (formObj.vid.value != '') ev_insertCode(formObj);
}

function ev_insertCode(formObj) {
	var portal = formObj.portal.value;
	var vid = formObj.vid.value;
	var linktext = (formObj.nolink.checked) ? 'nolink' : formObj.linktext.value;

	ev_insertVideoCode(portal, vid, linktext);
	tinyMCEPopup.close();
}

function disable_enable(objCheckbox, objTextfield) {
	objTextfield.disabled = (objCheckbox.checked) ? true : false;
	objTextfield.value = '';
	objTextfield.style.backgroundColor = (objTextfield.disabled) ? '#ccc' : '#fff';
}

function dailymotion(objSelectBox, objTextfield, objCheckbox) {
	if (objSelectBox.value=='dailymotion' || objSelectBox.value=='garagetv') {
		objCheckbox.checked = true;
		objTextfield.disabled = true;
		objTextfield.style.backgroundColor = '#ccc';
		objTextfield.value = '';
	}
	objCheckbox.disabled = (objSelectBox.value=='dailymotion') ? true : false;
}

function init() {
	tinyMCEPopup.resizeToInnerSize();
}