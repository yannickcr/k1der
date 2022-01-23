function serveurStart() {
	var h3=$('serveur').getElementsByTagName('h3');
	for(var i=1;i<=h3.length;i++) {	
		if(!$('serveur'+i+'activity')) {
			var img = domEl('img','',{id:'serveur'+i+'activity',className:'serveuractivity',src:'templates/'+THEME+'/images/activity-white-on-red.gif',title:'Loading...',alt:'loading'});
			h3[i-1].parentNode.insertBefore(img,h3[i-1].nextSibling);
		}
		
		serveurSwitchActivity(i,'off');
		var lien=$('serveur'+i+'_reload');
		if(!lien) var lien=domEl('a','Actualiser',{id:'serveur'+i+'_reload',className:'reload',title:'Rafraichir les informations du serveur',href:'#'});
		lien.onclick=function() {
			var rech = RegExp("^serveur([0-9]+)_reload$");
			var i=this.id.replace(rech,'$1');
			serveurSwitchActivity(i,'on');
			sendData('id='+i,'serveur/maj.html','POST','serveur'+i,'innerHTML',false,serveurStart);
			return false;
		}
		if($('serveur'+i+'_players').innerHTML.search(RegExp(/href/))==-1) {
			$('serveur'+i+'_players').innerHTML=$('serveur'+i+'_players').innerHTML+' <a href="javascript:serveurPlayers('+i+');">Liste</a>';
		}
		
		if(!$('serveur'+i+'_reload')) h3[i-1].parentNode.insertBefore(lien,h3[i-1].nextSibling);
	}
}

function serveurSwitchActivity(i,onoff) {
	if(onoff=='on') {
		$('serveur'+i+'map').style.display='none';
		$('serveur'+i+'activity').style.display='block';
	} else {
		$('serveur'+i+'map').style.display='block';
		$('serveur'+i+'activity').style.display='none';
	}
}

function serveurPlayers(i) {
	if($('serveur'+i+'_playerslist').style.display=='block') $('serveur'+i+'_playerslist').style.display='none';
	else {
		$('serveur'+i+'_playerslist').style.display='block';
		sendData('id='+i+'&cache=1','serveur/majplayers.html','POST','serveur'+i+'_playerslist','innerHTML',false,serveurStartPlayers);
	}
}

function serveurStartPlayers() {
	var rech = RegExp("^serveur([0-9]+)_reloadplayers$");
	var rech2 = RegExp("^serveur([0-9]+)_closeplayers$");
	for(var i=1;i<10;i++) {
		if($('serveur'+i+'_reloadplayers')) {
			$('serveur'+i+'_reloadplayers').onclick=function() {
				var i=this.id.replace(rech,'$1');
				serveurPlayersSwitchActivity(i,'on');
				sendData('id='+i,'serveur/majplayers.html','POST','serveur'+i+'_playerslist','innerHTML',false,serveurStartPlayers);
				return false;
			}
			$('serveur'+i+'_closeplayers').onclick=function() {
				var i=this.id.replace(rech2,'$1');
				$('serveur'+i+'_playerslist').style.display='none';
				return false;
			}
		}
	}
}

function serveurPlayersSwitchActivity(i) {
	var tbody=$('serveur'+i+'_playerslist').getElementsByTagName('tbody')[0];

	for(var j=0;j<tbody.childNodes.length;j++) {
		if(tbody.childNodes[j].nodeName.toLowerCase()=='tr') tbody.removeChild(tbody.childNodes[j]);
	}
	var img=domEl('img','',{src:'templates/'+THEME+'/images/activity-black-on-grey.gif',title:'loading...',alt:'loading...'});
	var td=domEl('td',img,{colspan:4});
	domEl('tr',td,{},tbody);
}


addToStart(serveurStart);