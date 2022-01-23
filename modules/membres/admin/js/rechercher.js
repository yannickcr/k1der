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
				
				var data='pseudo='+document.getElementById('rechpseudochamp').value;
				data+='&nbpartmin='+document.getElementById('rechnbpartchamp1').value;
				data+='&nbpartmax='+document.getElementById('rechnbpartchamp2').value;
				data+='&ins='+document.getElementById('rechdateinschamp').value;
				data+='&visit='+document.getElementById('rechdatevisitchamp').value;
				data+='&groupe='+document.getElementById('rechgroupechamp').value;
				data+='&checkedpseudo='+document.getElementById('rechpseudo').checked;
				data+='&checkednbpart='+document.getElementById('rechnbpart').checked;
				data+='&checkedins='+document.getElementById('rechdateins').checked;
				data+='&checkedvisit='+document.getElementById('rechdatevisit').checked;
				data+='&checkedgroupe='+document.getElementById('rechgroupe').checked;

				sendData(data,'admin/membres/recherche.html','POST','reponses','innerHTML',false,confDel);
			}
		}
		if (inputs[i].type=='checkbox' && inputs[i].id.search(/rech/i)!=-1)  {
			inputs[i].onchange = function()  {
				
				var data='pseudo='+document.getElementById('rechpseudochamp').value;
				data+='&nbpartmin='+document.getElementById('rechnbpartchamp1').value;
				data+='&nbpartmax='+document.getElementById('rechnbpartchamp2').value;
				data+='&ins='+document.getElementById('rechdateinschamp').value;
				data+='&visit='+document.getElementById('rechdatevisitchamp').value;
				data+='&groupe='+document.getElementById('rechgroupechamp').value;
				data+='&checkedpseudo='+document.getElementById('rechpseudo').checked;
				data+='&checkednbpart='+document.getElementById('rechnbpart').checked;
				data+='&checkedins='+document.getElementById('rechdateins').checked;
				data+='&checkedvisit='+document.getElementById('rechdatevisit').checked;
				data+='&checkedgroupe='+document.getElementById('rechgroupe').checked;
				
				sendData(data,'admin/membres/recherche.html','POST','reponses','innerHTML',false,confDel);
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
				
					var data='pseudo='+document.getElementById('rechpseudochamp').value;
					data+='&nbpartmin='+document.getElementById('rechnbpartchamp1').value;
					data+='&nbpartmax='+document.getElementById('rechnbpartchamp2').value;
					data+='&ins='+document.getElementById('rechdateinschamp').value;
					data+='&visit='+document.getElementById('rechdatevisitchamp').value;
					data+='&groupe='+document.getElementById('rechgroupechamp').value;
					data+='&checkedpseudo='+document.getElementById('rechpseudo').checked;
					data+='&checkednbpart='+document.getElementById('rechnbpart').checked;
					data+='&checkedins='+document.getElementById('rechdateins').checked;
					data+='&checkedvisit='+document.getElementById('rechdatevisit').checked;
					data+='&checkedgroupe='+document.getElementById('rechgroupe').checked;
				
					sendData(data,'admin/membres/recherche.html','POST','reponses','innerHTML',false,confDel);
			}
		}
	}
}

addToStart(rechercheOptions);