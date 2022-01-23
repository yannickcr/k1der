function confRenv() {
	var liens = document.getElementsByTagName('a');
	for (var i = 0 ; i < liens.length ; ++i)  {
		if (liens[i].className == 'confrenv')  {
			liens[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir renvoyer ce membre ?');
				if(res) window.location=this.href;
				return false;
			};
		}
	}
}
addToStart(confRenv);