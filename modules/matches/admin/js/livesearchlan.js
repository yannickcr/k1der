function liveSearchLan() {
	var champ = document.getElementById('lanparty');
	champ.setAttribute("autocomplete","off");
			champ.onkeyup = function()  {
				/*var reg = RegExp("[^a-zA-Z0-1 ]");
				var data = champ.value.replace(reg,'').replace('#','');*/
				sendData(champ.value, 'matches/admin/livesearchlan-', 'GET', 'livesearchlan','innerHTML',true);
			};
			champ.onblur = function()  {
				setTimeout("document.getElementById('livesearchlan').style.display='none';",250);
			};
}

function chooseLan(lan,jourdebut,moisdebut,anneedebut,jourfin,moisfin,anneefin) {
	document.getElementById('lanparty').value=lan;
	document.getElementById('jour').value=jourdebut;
	document.getElementById('mois').value=moisdebut;
	document.getElementById('annee').value=anneedebut;
	document.getElementById('jourdebut').value=jourdebut;
	document.getElementById('moisdebut').value=moisdebut;
	document.getElementById('anneedebut').value=anneedebut;
	document.getElementById('jourfin').value=jourfin;
	document.getElementById('moisfin').value=moisfin;
	document.getElementById('anneefin').value=anneefin;
}

addToStart(liveSearchLan);