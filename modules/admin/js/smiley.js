function addSmiley() {
	var inputs=document.getElementsByTagName('input');
	var lastInput;
	for(var i=0;i<inputs.length;i++) {
		var rech = RegExp("^smiley_([0-9]+)_txt$");
		if(inputs[i].id.match(rech)!=null) lastInput=inputs[i];
	}
	var reg = RegExp("^smiley_([0-9]+)_txt$");
	var id = lastInput.id.replace(reg,'$1')*2/2;
	lastInput.onkeyup=function() {
		var ligne=lastInput.parentNode.parentNode;
		var newligne=ligne.cloneNode(true);
		newligne.childNodes[1].childNodes[0].setAttribute('id','smiley_'+(id+1));
		newligne.childNodes[1].childNodes[0].setAttribute('name','smiley_'+(id+1));
		newligne.childNodes[1].childNodes[0].selected=false;
		newligne.childNodes[3].childNodes[0].setAttribute('id','smiley_'+(id+1)+'_txt');
		newligne.childNodes[3].childNodes[0].setAttribute('name','smiley_'+(id+1)+'_txt');
		newligne.childNodes[3].childNodes[0].value='';
		lastInput.parentNode.parentNode.parentNode.appendChild(newligne);
		this.onkeyup=false;
		addSmiley();
	}
}

function cloneSmileysList() {
	var source=document.getElementById('inputsource');
	var tds=document.getElementsByTagName('td');
	var lastInput;
	for(var i=0;i<tds.length;i++) {
		var list=source.cloneNode(true);
		var rech = RegExp("^smileytd_([0-9]+)$");
		if(tds[i].id.match(rech)!=null) {
			var id='smiley_'+tds[i].id.replace(rech,'$1');
			var input=document.getElementById(id);
			list.id=id;
			list.name=id;
			list.value=input.value;
			tds[i].removeChild(input);
			tds[i].appendChild(list);
		}
	}
}

addToStart(addSmiley);
addToStart(cloneSmileysList);