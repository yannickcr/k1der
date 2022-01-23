function liveSearchGoGoGo() {
	var champ = document.getElementById('clan');
	champ.setAttribute("autocomplete","off");
			champ.onkeyup = function()  {
				var reg = RegExp("[^a-zA-Z0-1 ]");
				var data = champ.value.replace(reg,'').replace('#','');
				sendData(data, 'clans/livesearchclan-', 'GET', 'LSResult','innerHTML',true);
			};
			champ.onblur = function()  {
				setTimeout("document.getElementById('LSResult').style.display='none';",250);
			};
}

function chooseClan(clan) {
	document.getElementById('clan').value=clan;
}

addToStart(liveSearchGoGoGo);