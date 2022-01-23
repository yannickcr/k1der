function liveSearchPlayer() {
	var champ = document.getElementsByTagName('input');
	var mode = document.getElementById('mode').value;
	var rech = RegExp("^joueur([0-9]+)$");
	var rech2 = RegExp("^joueur");
	for(var i=0;i<champ.length;i++) {
		if(champ[i].id.match(rech)!=null) {
			champ[i].setAttribute("autocomplete","off");
			champ[i].onkeyup = function()  {
				var id=this.id.replace(rech2,'');
				sendData(this.value, 'matches/admin/livesearchplayer-'+id+'-', 'GET', 'choixjoueur'+id,'innerHTML',true);
				if(mode=='Deathmatch') dmSpecial(this.id);
			};
			champ[i].onblur = function()  {
				var id=this.id.replace(rech2,'');
				setTimeout("hideLive('choixjoueur"+id+"')",250);
			};
		}
	}
	
	var rech3 = RegExp("^adv([0-9]+)$");
	var rech4 = RegExp("^adv");
	for(var i=0;i<champ.length;i++) {
		if(champ[i].id.match(rech3)!=null) {
			champ[i].setAttribute("autocomplete","off");
			champ[i].onkeyup = function()  {
				var id=this.id.replace(rech4,'');
				sendData(this.value, 'matches/admin/livesearchadv-'+id+'-', 'GET', 'choixadv'+id,'innerHTML',true);
				if(mode=='Deathmatch') dmSpecial(this.id);
			};
			champ[i].onblur = function()  {
				var id=this.id.replace(rech4,'');
				setTimeout("hideLive('choixadv"+id+"')",250);
			};
		}
	}
}

function hideLive(id) {
	if(document.getElementById(id)) document.getElementById(id).style.display='none';
}

function chooseAdv(id,adv) {
	var mode = document.getElementById('mode').value;
	document.getElementById('adv'+id).value=adv;
	if(mode=='Deathmatch') dmSpecial('adv'+id);
}

function choosePlayer(id,joueur) {
	var mode = document.getElementById('mode').value;
	document.getElementById('joueur'+id).value=joueur;
	if(mode=='Deathmatch') dmSpecial('joueur'+id);
}

function dmSpecial(id) {
	var td=document.getElementById('tdnbscores1');
	if(document.getElementById('score'+id) && document.getElementById(id).value=='') {
		td.removeChild(document.getElementById('score'+id));
		td.removeChild(document.getElementById('score'+id+'Carte1'));
		td.removeChild(document.getElementById('br'+id));
	} else if(document.getElementById('score'+id)) document.getElementById('score'+id).innerHTML=document.getElementById(id).value+' :';
	else {
		var label=document.createElement('label');
		label.id='score'+id;
		label.setAttribute('for','score'+id+'Carte1');
		var text=document.createTextNode(document.getElementById(id).value+' :');
		label.appendChild(text);
		td.appendChild(label);
		var input=document.createElement('input');
		input.type='text';
		input.id='score'+id+'Carte1';
		input.name='score'+id+'Carte1';
		td.appendChild(input);
		var br=document.createElement('br');
		br.id='br'+id;
		td.appendChild(br);
	}
}