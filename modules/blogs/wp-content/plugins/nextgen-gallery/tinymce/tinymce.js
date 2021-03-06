function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}


function insertNGGLink() {
	
	var tagtext;
	
	var gallery = document.getElementById('gallery_panel');
	var album = document.getElementById('album_panel');
	var singlepic = document.getElementById('singlepic_panel');
	
	// who is active ?
	if (gallery.className.indexOf('current') != -1) {
		var galleryid = document.getElementById('gallerytag').value;
		var showtype = getCheckedValue(document.getElementsByName('showtype'));
		if (galleryid != 0 )
			tagtext = "["+ showtype + "=" + galleryid + "]";
		else
			tinyMCEPopup.close();
	}

	if (album.className.indexOf('current') != -1) {
		var albumid = document.getElementById('albumtag').value;
		var showtype = getCheckedValue(document.getElementsByName('albumtype'));
		if (albumid != 0 )
			tagtext = "[album=" + albumid + "," + showtype + "]";
		else
			tinyMCEPopup.close();
	}

	if (singlepic.className.indexOf('current') != -1) {
		var singlepicid = document.getElementById('singlepictag').value;
		var imgWidth = document.getElementById('imgWidth').value;
		var imgHeight = document.getElementById('imgHeight').value;
		var imgeffect = document.getElementById('imgeffect').value;
		var imgfloat = document.getElementById('imgfloat').value;

		if (singlepicid != 0 ) {
			if (imgeffect == "none")
				tagtext = "[singlepic=" + singlepicid + "," + imgWidth + "," + imgHeight + ",," + imgfloat + "]";
			else
				tagtext = "[singlepic=" + singlepicid + "," + imgWidth + "," + imgHeight + "," + imgeffect + "," + imgfloat + "]";
		} else {
			tinyMCEPopup.close();
		}
	}
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
//		tinyMCE.execCommand("mceCleanup");
 		tinyMCE.selectedInstance.repaint();
	} else {
		edCanvas = mceWindow.document.getElementById('content');
		window.edInsertContent(edCanvas, tagtext);
	}
	tinyMCEPopup.close();
}
