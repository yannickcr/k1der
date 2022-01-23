var remove=Array();
var date;
var mouseX;
var mouseY;
var exec=0;
var activity;

/**
 * Shoutbox Start
 */
function shoutboxStart() {
	makeActivity();
	submitForm();
	mouseXY();
	liveEdit();
	var reloadlink=document.getElementById('shoutbox_reload');
	reloadlink.onclick=function() {
		switchActivity('on');
		update(1);
		return false;
	}
}

function makeActivity() {
	var reloadButton=document.getElementById('shoutbox_reload');
	activity=document.createElement('img');
	activity.style.visibility='hidden';
	activity.id='shoutbox_activity';
	activity.src='templates/'+THEME+'/images/activity-white-on-red.gif';
	activity.alt='loading';
	reloadButton.parentNode.insertBefore(activity,reloadButton);
}

function switchActivity(onoff) {
	if(onoff=='on') activity.style.visibility='visible';
	else activity.style.visibility='hidden';
}

function submitForm() {
	var form=document.getElementById('shoutbox_form');
	if(!form) return false;
	form.onsubmit=function() {
		var message=document.getElementById('shoutbox_message').value;
		if(message.length==0) return false;
		switchActivity('on');
		sendData('message='+message,'shoutbox/liveedit.html','POST',false,'none',false,update);
		return false;
	}
}

function update(keepmess) {
	sendData('update=1','shoutbox/liveedit.html','POST','shoutbox_messages','innerHTML',false,liveEdit);
	if(!keepmess) document.getElementById('shoutbox_message').value='';
}

function niceTitleLite() {
	exec++;
	var a=document.getElementById('shoutbox_messages').getElementsByTagName('a');
	for(var i=0;i<a.length;i++) {
		if(a[i].title && a[i].className=='auteur') {
			a[i].onmouseover=function() {
				var shoutbox=document.getElementById('shoutbox');
				var bulle=document.createElement('div');
				var bulletxt=document.createTextNode(this.title);
				bulle.appendChild(bulletxt);
				bulle.id='bulle';
				bulle.style.top=(mouseY-30)+'px';
				bulle.style.left=(mouseX+5)+'px';
				bulle.setAttribute('class','bulle');
				this.title='';
				shoutbox.appendChild(bulle);
			}
			a[i].onmouseout=function() {
				var shoutbox=document.getElementById('shoutbox');
				var bulle=document.getElementById('bulle');
				this.title=bulle.firstChild.nodeValue;
				shoutbox.removeChild(bulle);
			}
		}
	}
}

function mouseXY() {
	document.onmousemove=function(e) {
		if(!e) var e=event;
		var docbody=document.body;
		if(e.clientX) {
			mouseX=e.clientX + docbody.parentNode.scrollLeft;
			mouseY=e.clientY + docbody.parentNode.scrollTop;
		} else if(e.pageX) {
			mouseX=e.pageX + docbody.scrollLeft;
			mouseY=e.pageY + docbody.scrollTop;
		}
		var bulle=document.getElementById('bulle');
		if(bulle) {
			bulle.style.top=(mouseY-30)+'px';
			bulle.style.left=(mouseX+5)+'px';
		}
	}
}

/**
 * Assigne les actions aux champs du tableau
 */
function liveEdit() {
	switchActivity('off');
	var dd=document.getElementsByTagName('dd');
	var reg = RegExp("^message([0-9]+)$");
	for(var i=0;i<dd.length;i++) {
		if(dd[i].id.match(reg)!=null) {
			//alert(dd[i].id);
			var dl=dd[i].parentNode;	
			remove['edit'+dd[i].id]=1;
			dd[i].onmouseover=function() {
				var dl=this.parentNode;	
				var ddId=this.id;
				remove['edit'+this.id]=0;
				if(!document.getElementById('edit'+this.id)) {
					var edit=document.createElement('div');
					edit.id='edit'+this.id;
					edit.setAttribute('class','edit');
					// Image Edit
					var editImg=document.createElement('img');
					editImg.src='templates/'+THEME+'/images/icons/pencil.png';
					editImg.title='Editer';
					editImg.onclick=function() { goEdit(ddId); }
					edit.appendChild(editImg);
					// Image Del
					var delImg=document.createElement('img');
					delImg.src='templates/'+THEME+'/images/icons/cancel.png';
					delImg.title='Supprimer';
					delImg.onclick=function() { goDel(ddId); }
					edit.appendChild(delImg);
					this.insertBefore(edit,this.firstChild);
					edit.onmouseover=function() {
						var dd=this.parentNode;	
						remove['edit'+dd.id]=0;
						removeOtherEdit(this.id);
					}
					edit.onmouseout=function() {
						var dd=this.parentNode;	
						remove['edit'+dd.id]=1;
						setTimeout("removeEdit('"+dd.id+"')",10);
					}
				}
			}
			dd[i].onmouseout=function() {
				remove['edit'+this.id]=1;
				setTimeout("removeEdit('"+this.id+"')",10);
			}
		}
	}
	niceTitleLite();
}

/**
 * Supprime le menu d'édition courant
 */
function removeEdit(id) {
	var dd=document.getElementById(id);
	if(!id) return false;
	if(remove['edit'+dd.id]==1 && document.getElementById('edit'+id)) dd.removeChild(document.getElementById('edit'+id));
}

/**
 * Supprime tous les menus d'édition, sauf le menu courant
 */
function removeOtherEdit(id) {
	var div=document.getElementsByTagName('div');
	var reg = RegExp("^edittr([0-9]+)$");
	for(var i=0;i<div.length;i++) {
		if(div[i].id.match(reg)!=null && div[i].id!=id) {
			var dd=div[i].parentNode;
			dd.removeChild(div[i]);
		}
	}
}

/**
 * Dbut de l'édition d'un message
 * Action excute lors du clic sur le bouton éditer
 */
function goEdit(id) {
	// Récupère l'objet
	var dd=document.getElementById(id);
	// créer le textarea
	var txtEdit=document.createElement('textarea');
	txtEdit.id='liveedit'+dd.id;
	txtEdit.style.height=dd.offsetHeight+'px';
	// Message d'attente
	txtEdit.value='Chargement...';
	// Insert le textarea avant le div
	dd.innerHTML='';
	dd.appendChild(txtEdit,dd);
	
	// Place les informations du div dans le textarea
	sendData('id='+id,'shoutbox/liveedit.html','POST',txtEdit.id,'value');
	// Met le focus sur le texarea
	txtEdit.focus();
	
	// Supprime les évènements ainsi que le menu d'édition actif
	var tr=dd.parentNode;
	//td.removeChild(document.getElementById('edit'+tr.id));
	
	var dds=document.getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].onmouseover=null;
		dds[i].onmouseout=null;
	}
	
	// Ajoute le menu de validation des modifications
	var edit=document.createElement('div');
	edit.id='valid'+tr.id;
	edit.setAttribute('class','valid');
	// Image Valid
	var validImg=document.createElement('img');
	validImg.src='templates/'+THEME+'/images/icons/accept.png';
	validImg.title='Valider';
	validImg.onclick=function() { finishEdit(dd); }
	edit.appendChild(validImg);
	dd.insertBefore(edit,dd.firstChild);
}

/**
 * Fin de l'édition d'un message, et enregistrement de celui-ci
 * Action excute lors du clic sur le bouton valider
 */
function finishEdit(dd) {
	// Place les informations du div dans le textarea
	var message=document.getElementById('liveedit'+dd.id);
	var tr=dd.parentNode;
	dd.removeChild(document.getElementById('valid'+tr.id));
	switchActivity('on');
	sendData('id='+dd.id+'&new_message='+encodeURI(message.value.replace('&','%26')),'shoutbox/liveedit.html','POST',dd.id,'innerHTML',false,liveEdit);
}

/**
 * Demande de confirmation, puis suppression du message de la shoutbox
 * Action excute lors du clic sur le bouton supprimer
 */
function goDel(id) {
	var res=confirm('Etes vous certain de vouloir effacer ce message ?');
	if(res) {
		var dd=document.getElementById(id);
		switchActivity('on');
		sendData('id='+id+'&del=1','shoutbox/liveedit.html','POST',id,null,false,update);
		/*var td=document.getElementById(id);
		var parent=td.parentNode.parentNode;
		parent.removeChild(td.parentNode);*/
	}
	return false;
}

addToStart(shoutboxStart);