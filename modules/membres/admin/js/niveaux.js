var numLine;
function apercuNiveau() {

	/**
	 * Ajout d'une ligne virge  la table
	 */
	makeTableLine();
	
	/**
	 * Aperu des images
	 */
	var inputs = document.getElementsByTagName('input');
	for (var i = 0 ; i < inputs.length ; i++)  {
		if (inputs[i].type == 'button' && inputs[i].value=='+')  {
			inputs[i].onclick = function()  {
				var num=this.id.replace('plus','');

				// Récupération de l'image  utiliser
				var img=document.getElementById('image'+num).value;
				
				// Création de l'image
				var image=document.createElement('img');
					image.src='templates/'+THEME+'/images/membres/'+img;
					image.alt='.';
				
				// Placement de l'image
				document.getElementById('apercu'+num).appendChild(image);
				
				// MAJ des infos dans le champ cach
					document.getElementById('img'+num).value=document.getElementById('apercu'+num).childNodes.length-2;
			}
		}
		if(inputs[i].type == 'button' && inputs[i].value=='-') {
			inputs[i].onclick = function() {
				var num=this.id.replace('moins','');
				
				if(document.getElementById('apercu'+num).childNodes.length>2) {
					var noeud=document.getElementById('apercu'+num).lastChild;
					document.getElementById('apercu'+num).removeChild(noeud);
				}
				
				// MAJ des infos dans le champ cach
					document.getElementById('img'+num).value=document.getElementById('apercu'+num).childNodes.length-2;
			}
		}
		
	}

	/**
	 * Changement des images dans l'aperu
	 */
	var selects = document.getElementsByTagName('select');
	for (var i = 0 ; i < selects.length ; i++) {
		if (selects[i].className=='listeimages')  {

			selects[i].onchange = function()  {
				var num=this.id.replace('image','');
				// Récupération de l'image  utiliser
				var img=document.getElementById('image'+num).value;
				
				var imgs = document.getElementById('apercu'+num).getElementsByTagName("img");
				for(var j = 0 ; j < imgs.length ; j++) {
					imgs[j].setAttribute('src','templates/'+THEME+'/images/membres/'+img);
				}
			}
		}
	}

	/**
	 * Changement de la dsignation dans l'aperu et ajout d'une ligne si on utilise la dernire
	 */
	var texts = document.getElementsByTagName('input');
	for (var i = 0 ; i < texts.length ; i++) {
		if (texts[i].id.search(/design/i)!=-1)  {
			texts[i].onkeyup = function()  {
				var num=this.id.replace('design','');
				// Création du paragraphe
				var texte=document.createTextNode(this.value);

				// Placement du paragraphe
				if(document.getElementById('apercu'+num).getElementsByTagName('p')[0].hasChildNodes()) {
					var paragr=document.getElementById('apercu'+num).getElementsByTagName('p')[0].firstChild;
					paragr.replaceData(0,paragr.nodeValue.length,this.value);
				} else {
				   document.getElementById('apercu'+num).getElementsByTagName('p')[0].appendChild(texte);
				}
			}
		}
		
		if (texts[i].id=='points'+numLine)  {
			texts[i].onkeyup = function()  {
				if(this.value!='') apercuNiveau();
				this.onkeyup = null;
			}
		}
	}
}

function makeTableLine() {
	
	// On dtermine combien de ligne il y a dj dans le tableau
	numLine=1;
	var tbody=document.getElementsByTagName('tbody');
	for(var i=0;tbody[0].childNodes[i];i++) if(tbody[0].childNodes[i].nodeName=='TR') numLine++;
	
	// On créer la nouvelle ligne
	var tr=document.createElement('tr');
		tr.id='ligne'+numLine;
	
	// 1re colonne
	var input=document.getElementById('img'+(numLine-1)).cloneNode(true);
		input.id='img'+numLine;
		input.name='img'+numLine;
		input.value='';
	var p=document.createElement('p');
	var td=document.createElement('td');
		td.id='apercu'+numLine;
	td.appendChild(input);
	td.appendChild(p);
	tr.appendChild(td);
	
	// 2me colonne
	var input=document.getElementById('points'+(numLine-1)).cloneNode(true);
		input.id='points'+numLine;
		input.name='points'+numLine;
		input.value='';
	var td=document.createElement('td');
	td.appendChild(input);
	tr.appendChild(td);
	
	// 3me colonne
	var input=document.getElementById('design'+(numLine-1)).cloneNode(true);
		input.id='design'+numLine;
		input.name='design'+numLine;
		input.value='';
	var td=document.createElement('td');
	td.appendChild(input);
	tr.appendChild(td);
	
	// 4me colonne
	// Elment dynamique en vue
	// Tant pis, on va copier sur le voisin ;)
	var selectImg=document.getElementById('image'+(numLine-1)).cloneNode(true);
		selectImg.id='image'+numLine;
		selectImg.name='image'+numLine;
	var td=document.createElement('td');
	td.appendChild(selectImg);
	tr.appendChild(td);

	// 5me colonne
	var input1=document.createElement('input');
		input1.type='button';
		input1.id='plus'+numLine;
		input1.name='plus'+numLine;
		input1.value='+';
	var text=document.createTextNode(' ');
	var input2=document.createElement('input');
		input2.type='button';
		input2.id='moins'+numLine;
		input2.name='moins'+numLine;
		input2.value='-';
	var td=document.createElement('td');
		td.id='repetimg'+numLine;
	td.appendChild(input1);
	td.appendChild(text);
	td.appendChild(input2);
	tr.appendChild(td);

	// On fourre tout dans le tbody, voila une bonne chose de faite
	tbody[0].appendChild(tr);
}

addToStart(apercuNiveau);