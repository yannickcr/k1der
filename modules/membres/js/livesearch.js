function liveSearchGoGoGo() {
	var champ = document.getElementById('to');
	champ.setAttribute("autocomplete","off");
			champ.onkeyup = function()  {
				var reg = RegExp("[^a-zA-Z0-1 ]");
				var data = champ.value.replace(reg,'').replace('#','');
				sendData(data, 'membres/livesearch-', 'GET', 'LSResult','innerHTML',true);
			};
			champ.onblur = function()  {
				setTimeout("document.getElementById('LSResult').style.display='none';",250);
			};
}

function choosePseudo(pseudo) {
	document.getElementById('to').value=pseudo;
}

addToStart(liveSearchGoGoGo);