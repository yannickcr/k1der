/**
 * Permet de cacher le forumaire de rponse rapide
 * (ainsi pour les navigateur avec javascript dsactiv il restera visible)
 */
function reprap() {
	if(document.getElementById('rep-rap')) document.getElementById('rep-rap').style.display='none';
}

addToStart(reprap);

/**
 * Affichage/Masquage du formulaire de rponse rapide
 */
function show_hide(id) {
	var show=document.getElementById(id).style.display;
	if(show=='none') document.getElementById(id).style.display='';
	else document.getElementById(id).style.display='none';
}

/**
 * "Convertit" le texte de la case du tableau en textarea
 * En ralit : créer un texarea avec pour valeur le contenu de la case et le place dans la case
 */
function edit() {
	var td=document.getElementsByTagName('td');
	for(var i=0;i<td.length;i++) {
		if(td[i].id.search(/message/i)!=-1) {
			td[i].ondblclick= function() {
				// Récupère le parent
				var parent=this.parentNode;	
				// Récupère l'id
				var id=this.id.replace('message','');
				
				// créer le textarea
				var edit=document.createElement('textarea');
				edit.id='liveedit';
				edit.style.width='100%';
				edit.style.height=this.offsetHeight+'px';
				// Message d'attente
				edit.value='Chargement du message...';
				// Insert le textarea avant le div
				this.innerHTML='';
				this.appendChild(edit,this);
				
				// Place les informations du div dans le textarea
				sendData('id='+id,'forum/liveedit.html','POST','liveedit','value');
				// Met le focus sur le texarea
				edit.focus();
				// Affecte les venements au textarea
				getTextarea();
				this.onclick=null;
			}
		}
	}
}
/**
 * "Convertit" le textarea en texte
 * En ralit : supprime le textarea et re-affiche le nouveau texte dans la case
 */
function getTextarea() {
	var textarea=document.getElementById('liveedit');
	textarea.onblur= function() {
		// Récupère le parent
		var parent=this.parentNode;
		// Récupère l'id et la valeur
		var id=parent.id.replace('message','');
		var value=this.value;
		// Supprime le textarea
		parent.removeChild(this);
		// Message d'attente
		parent.innerHTML='Mise  jour...';
		// Place les informations du textarea dans le div
		if(value!='Chargement du message...' && value!='') sendData('texte='+encodeURI(value.replace('&','%26'))+'&id='+id,'forum/liveedit.html','POST',parent.id,'innerHTML');
		else sendData('id='+id,'forum/liveedit.html','POST',parent.id,'innerHTML');
		this.onblur=null;
		edit();
	}
}

addToStart(edit);
