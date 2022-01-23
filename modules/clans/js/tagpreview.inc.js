function tagpreview() {
	var tag=document.getElementById('tag');
	
	var before=document.getElementById('before');
	var after=document.getElementById('after');
	var tagpreview=document.getElementById('tagpreview');
	
	before.onchange=function() {
		if(before.checked==true) tagbefore(before,tag,tagpreview);
	}
	after.onchange=function() {
		if(after.checked==true) tagafter(after,tag,tagpreview);
	}
	
	tag.onkeyup=function() {
		if(before.checked==true) tagbefore(before,tag,tagpreview);
		if(after.checked==true) tagafter(after,tag,tagpreview);
	}
}

function tagbefore(before,tag,tagpreview) {
	var text=document.createTextNode('exemple : '+tag.value+'Joueur');
	tagpreview.removeChild(tagpreview.childNodes[0]);
	tagpreview.appendChild(text);
}

function tagafter(after,tag,tagpreview) {
	var text=document.createTextNode('exemple : Joueur'+tag.value);
	tagpreview.removeChild(tagpreview.childNodes[0]);
	tagpreview.appendChild(text);
}

addToStart(tagpreview);