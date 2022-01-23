function confDel() {
	var liens = document.getElementsByTagName('a');
	for (var i = 0 ; i < liens.length ; ++i)  {
		if (liens[i].className == 'confdelc')  {
			liens[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir effacer cette catégorie ?');
				if(res) window.location=this.href;
				return false;
			};
		} else if (liens[i].className == 'confdelf')  {
			liens[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir effacer ce fichier ?');
				if(res) window.location=this.href;
				return false;
			};
		}
	}
}

addToStart(confDel);