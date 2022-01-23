function checker() {
	var inputs = document.getElementsByTagName('input');
	for (var i = 0 ; i < inputs.length ; ++i)  {
		if (inputs[i].className == 'confdelall')  {
			inputs[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir effacer ces messages ?');
				if(!res) return false;
			};
		}
		if (inputs[i].className == 'confmarkall')  {
			inputs[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir marquer ces messages comme lu ?');
				if(!res) return false;
			};
		}
	}
	var check=true;
	var checker=document.getElementById('checker');
	var rech = RegExp("^message");
	checker.onclick=function() {
		var checkboxes = document.getElementsByTagName('input');
		for (var i = 0 ; i < checkboxes.length ; ++i)  {
			if (checkboxes[i].id.match(rech)!=null)  checkboxes[i].checked=check;
		}
		if(check==true) check=false;
		else check=true;
	}
}
addToStart(checker);