function addfile() {
	var image=document.getElementById('image');
	image.onchange=function() {
		var apercu=document.getElementById('apercu');
		if(apercu) {
			apercu.setAttribute('src',this.value);
			apercu.setAttribute('alt',this.value);
		} else {
			var img=document.createElement('img');
			img.id='apercu';
			img.src=this.value;
			img.alt=this.value;
			img.setAttribute('style','margin:0 10px; vertical-align:top; max-width:80%;max-height:200px;');
			img.onmouseover=function() {
				this.setAttribute('style','position:absolute; margin:0 10px;');
			}
			img.onmouseout=function() {
				this.setAttribute('style','margin:0 10px; vertical-align:top; max-width:70%;max-height:200px;');
			}
			this.parentNode.insertBefore(img,this.nextSibling);
		}
	}
	for(var i=1;document.getElementById('miroir'+i);i++) {}
	actionMiroir(i-1);
}

function actionMiroir(i) {
	var miroir=document.getElementById('miroir'+i);
	miroir.onkeyup=function() {
		if(miroir.value=='') return false;
		var tr=document.createElement('tr');
		var td=document.createElement('td');
		var label=document.createElement('label');
		var labeltxt=document.createTextNode('Lien du miroir '+(++i));
		label.setAttribute('for','miroir'+i);
		label.appendChild(labeltxt);
		td.setAttribute('style','width:30%;font-weight:bold;');
		td.appendChild(label);
		tr.appendChild(td);
		var td=document.createElement('td');
		var input=document.createElement('input');
		input.type='text';
		input.id='miroir'+i;
		input.name='miroir[]';
		td.setAttribute('style','width:70%;');
		td.appendChild(input);
		tr.appendChild(td);
		this.parentNode.parentNode.parentNode.insertBefore(tr,this.parentNode.parentNode.nextSibling);
		actionMiroir(i);
		this.onkeyup=null;
	}
}

addToStart(addfile);