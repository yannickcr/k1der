function partScroll() {
	var partScrolling=setInterval('partScrollIt()',20);
	var partenaires=document.getElementById('partenaires2');
	var j=partenaires.childNodes.length;
	for(var i=0;i<j;i++) {
		var partclone=partenaires.childNodes[i].cloneNode(true);
		partenaires.appendChild(partclone);
	}
	partenaires.onmouseover=function() {
		clearInterval(partScrolling);
	}
	partenaires.onmouseout=function() {
		partScrolling=setInterval('partScrollIt()',20);
	}
}
function partScrollIt(partenaires) {
	var partenaires=document.getElementById('partenaires2');
	var height=partenaires.offsetHeight/2;
	var top=1*partenaires.style.marginTop.replace('px','');
	if(top<-height) top=0;
	partenaires.style.marginTop=(top-1)+'px';
}

addToStart(partScroll);