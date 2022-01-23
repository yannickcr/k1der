// Charge le fichier XML du jeu slectionn
function loadXml(data,change) {
	if(!data) var data=document.getElementById('jeu').value;
	else selectedGame=data;
	
	if(change) {
		sendData(data, 'matches/admin/loadxml-', 'GET', 'matchdetails','innerHTML',false,matchActions);
	} else {
		
		var id="id="+document.getElementById('id').value;
		sendData(id, 'matches/admin/loadxml-'+data+'.html', 'POST', 'matchdetails','innerHTML',false,matchActions);
	}
}

// Assigne les actions aux champs des infos générales
function generalActions() {
	
	// Jeu
	var jeu=document.getElementById('jeu');
	jeu.onchange=function() {
		loadXml(jeu.value,1);
	}

	// Lieu du match
	document.getElementById('lanpartyline').style.display='none';
	document.getElementById('sallejeuxline').style.display='none';
	document.getElementById('tournoiline').style.display='none';
	
	var lieu=document.getElementById('lieu');
	if(lieu.value!='internet') document.getElementById(lieu.value+'line').style.display=null;
	
	var newline;
	lieu.onchange=function() {
		var val=this.value;
		document.getElementById('lanpartyline').style.display='none';
		document.getElementById('sallejeuxline').style.display='none';
		document.getElementById('tournoiline').style.display='none';
		if(val=='lanparty') document.getElementById(val+'line').style.display=null;
		else if(val=='sallejeux') document.getElementById(val+'line').style.display=null;
		else if(val=='internet' && document.getElementById('type').value=='tournoi') document.getElementById('tournoiline').style.display=null;
	}

	// Type de match
	document.getElementById('tournoiline').style.display='none';
	document.getElementById('datedebutline').style.display='none';
	document.getElementById('datefinline').style.display='none';
	
	var type=document.getElementById('type');
	
	if(type.value!='unique') {
		document.getElementById('datedebutline').style.display=null;
		document.getElementById('datefinline').style.display=null;
		document.getElementById('dateline').style.display='none';
	}
	
	type.onchange=function() {
		document.getElementById('tournoiline').style.display='none';
		document.getElementById('datedebutline').style.display='none';
		document.getElementById('datefinline').style.display='none';
		document.getElementById('dateline').style.display='none';
		if(this.value=='tournoi' && document.getElementById('lieu').value=='internet') document.getElementById('tournoiline').style.display=null;
		if(this.value=='tournoi') {
			document.getElementById('datedebutline').style.display=null;
			document.getElementById('datefinline').style.display=null;
		} else document.getElementById('dateline').style.display=null;
	}
}

// Assigne les actions aux champs des infos du match
function matchActions() {
	
	// Changement du mode de jeu
	var mode=document.getElementById('mode');
	mode.onchange=function() {
		sendData(this.value, 'matches/admin/reloadxmlpart-'+selectedGame+'-maps-', 'GET', 'tdnbmaps','innerHTML',false,modeChangeNbMaps);
		sendData(this.value, 'matches/admin/reloadxmlpart-'+selectedGame+'-lineup1-', 'GET', 'tdlineup1','innerHTML',false);
		sendData(this.value, 'matches/admin/reloadxmlpart-'+selectedGame+'-lineup2-', 'GET', 'tdlineup2','innerHTML',false);
		sendData(this.value, 'matches/admin/reloadxmlpart-'+selectedGame+'-scores-', 'GET', 'tdnbscores1','innerHTML',false,liveSearchPlayer);
	}
	
	// Live Search des joueurs
	liveSearchPlayer();
	
	// Changement du nombre de cartes
	changeNbMaps();
	modeChangeNbMaps();
	
	// Images d'ajout/ suppression de joueurs/adversaires
	var addplayer=document.getElementById('addplayer');
	addplayer.onclick=function() {
		var td=document.getElementById('tdlineup1');
		var countInput=td.getElementsByTagName('input');
		var input=document.createElement('input');
		input.type='text';
		input.style.width='200px';
		input.id='joueur'+(countInput.length+1);
		input.name='joueur'+(countInput.length+1);
		td.appendChild(input);
		td.appendChild(document.createElement('br'));
		var div=document.createElement('div');
		div.id='choixjoueur'+(countInput.length);
		div.setAttribute('class','livesearch');
		td.appendChild(div);
		liveSearchPlayer(); // On rassigne les actions
	}
	
	var delplayer=document.getElementById('delplayer');
	delplayer.onclick=function() {
		var td=document.getElementById('tdlineup1');
		var countInput=td.getElementsByTagName('input');
		if(countInput.length==1) return false;
		var nbInput=countInput.length;
		
		var rech = RegExp("^joueur([0-9]+)$");
		var mode = document.getElementById('mode').value;
		
		while(countInput.length==nbInput) {
			var toDel=td.lastChild;
			
			// Suppression des scores
			if(mode=='Deathmatch' && toDel.id.match(rech)!=null && document.getElementById('br'+toDel.id)) {
				var td2=document.getElementById('tdnbscores1');
				td2.removeChild(document.getElementById('score'+toDel.id));
				td2.removeChild(document.getElementById('score'+toDel.id+'Carte1'));
				td2.removeChild(document.getElementById('br'+toDel.id));
			}
			
			td.removeChild(toDel);
		}
	}
	
	var addadv=document.getElementById('addadv');
	addadv.onclick=function() {
		var td=document.getElementById('tdlineup2');
		var countInput=td.getElementsByTagName('input');
		var input=document.createElement('input');
		input.type='text';
		input.style.width='200px';
		input.id='adv'+(countInput.length+1);
		input.name='adv'+(countInput.length+1);
		td.appendChild(input);
		var div=document.createElement('div');
		div.id='choixadv'+(countInput.length);
		div.setAttribute('class','livesearch');
		td.appendChild(div);
		liveSearchPlayer(); // On rassigne les actions
	}
	
	var deladv=document.getElementById('deladv');
	deladv.onclick=function() {
		var td=document.getElementById('tdlineup2');
		var countInput=td.getElementsByTagName('input');
		if(countInput.length==1) return false;
		var nbInput=countInput.length;
		
		var rech = RegExp("^adv([0-9]+)$");
		var mode = document.getElementById('mode').value;
		
		while(countInput.length==nbInput) {
			var toDel=td.lastChild;
			
			// Suppression des scores
			if(mode=='Deathmatch' && toDel.id && toDel.id.match(rech)!=null && document.getElementById('br'+toDel.id)) {
				var td2=document.getElementById('tdnbscores1');
				td2.removeChild(document.getElementById('score'+toDel.id));
				td2.removeChild(document.getElementById('score'+toDel.id+'Carte1'));
				td2.removeChild(document.getElementById('br'+toDel.id));
			}
			
			td.removeChild(toDel);
		}
	}
}

function changeNbMaps() {
	var nbmaps=document.getElementById('nbmaps');
	nbmaps.onchange=function() {
		onChangeNbMaps(this);
	}
}

function modeChangeNbMaps() {
	changeNbMaps();
	var nbmaps=document.getElementById('nbmaps');
	onChangeNbMaps(nbmaps);
	var mode=document.getElementById('mode').value;
	if(mode=="Deathmatch" || mode=="Duel") document.getElementById('tdadv').style.display='none';
	else document.getElementById('tdadv').style.display=null;
}

function onChangeNbMaps(champ) {
	for(var i=1;document.getElementById('carte'+i);i++) document.getElementById('carte'+i).style.display='none';
	for(var i=1;i<=champ.value;i++) document.getElementById('carte'+i).style.display=null;
}

var selectedGame='cs'

addToStart(loadXml);
addToStart(generalActions);