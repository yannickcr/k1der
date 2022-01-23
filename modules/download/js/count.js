function dlCount() {
	var liens = document.getElementsByTagName('a');
	for (var i = 0 ; i < liens.length ; ++i)  {
		if (liens[i].className == 'dlcount')  {
			liens[i].onclick = function()  {
				var id= this.id.replace('dl','');
				sendData('id='+id, 'download/count.html', 'POST',null);
				window.location=this.href;
				return false;
			};
		}
	}
}

addToStart(dlCount);