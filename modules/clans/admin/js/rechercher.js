function rechercheOptions() {
	
	/**
	 * Cochage/Décochage des cases lors de modifications et lancement de la recherche
	 */
	var inputs = document.getElementsByTagName('input');
	for (var i = 0 ; i < inputs.length ; i++)  {
		if (inputs[i].type=='text' && inputs[i].id.search(/rech/i)!=-1)  {
			inputs[i].onkeyup = function()  {
				var id=this.id.replace('champ1','').replace('champ2','').replace('champ','');
				var id2=this.id.replace('champ1','champb').replace('champ2','champ1').replace('champb','champ2');
				if(this.value!='') document.getElementById(id).checked=true;
				else if(document.getElementById(id2).value=='') document.getElementById(id).checked=false;
				
				var data='nom='+document.getElementById('rechnomchamp').value;
				data+='&tag='+document.getElementById('rechtagchamp').value;
				data+='&leader='+document.getElementById('rechleadchamp').value;
				data+='&create='+document.getElementById('rechdatecreatechamp').value;
				data+='&checkednom='+document.getElementById('rechnom').checked;
				data+='&checkedtag='+document.getElementById('rechtag').checked;
				data+='&checkedleader='+document.getElementById('rechlead').checked;
				data+='&checkedcreate='+document.getElementById('rechdatecreate').checked;

				sendData(data,'admin/clans/recherche.html','POST','reponses','innerHTML',false,confDel);
			}
		}
		if (inputs[i].type=='checkbox' && inputs[i].id.search(/rech/i)!=-1)  {
			inputs[i].onchange = function()  {
				
				var data='nom='+document.getElementById('rechnomchamp').value;
				data+='&tag='+document.getElementById('rechtagchamp').value;
				data+='&leader='+document.getElementById('rechleadchamp').value;
				data+='&create='+document.getElementById('rechdatecreatechamp').value;
				data+='&checkednom='+document.getElementById('rechnom').checked;
				data+='&checkedtag='+document.getElementById('rechtag').checked;
				data+='&checkedleader='+document.getElementById('rechlead').checked;
				data+='&checkedcreate='+document.getElementById('rechdatecreate').checked;
				
				sendData(data,'admin/clans/recherche.html','POST','reponses','innerHTML',false,confDel);
			}
		}

	}
	var selects = document.getElementsByTagName('select');
	for (var i = 0 ; i < selects.length ; i++)  {
		if (selects[i].id.search(/rech/i)!=-1)  {
			selects[i].onchange = function()  {
				var id=this.id.replace('champ','');
				if(this.value!='') document.getElementById(id).checked=true;
				else document.getElementById(id).checked=false;
				
					var data='nom='+document.getElementById('rechnomchamp').value;
					data+='&tag='+document.getElementById('rechtagchamp').value;
					data+='&leader='+document.getElementById('rechleadchamp').value;
					data+='&create='+document.getElementById('rechdatecreatechamp').value;
					data+='&checkednom='+document.getElementById('rechnom').checked;
					data+='&checkedtag='+document.getElementById('rechtag').checked;
					data+='&checkedleader='+document.getElementById('rechlead').checked;
					data+='&checkedcreate='+document.getElementById('rechdatecreate').checked;
				
					sendData(data,'admin/clans/recherche.html','POST','reponses','innerHTML',false,confDel);
			}
		}
	}
}

addToStart(rechercheOptions);