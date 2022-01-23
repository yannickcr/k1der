function confDel() {
	var liens = document.getElementsByTagName('a');
	for (var i = 0 ; i < liens.length ; ++i)  {
		if (liens[i].className == 'confdel')  {
			liens[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir effacer ce message ?');
				if(res) window.location=this.href;
				return false;
			};
		}
	}
}

addToStart(confDel);