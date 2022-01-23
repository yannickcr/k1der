/**
 * Galeries Start
 */
function galeriesStart() {
	window.onresize=galeriesSetPadding;
	galeriesSetPadding();
}

function galeriesSetPadding() {
	var width=document.getElementById('photos').offsetWidth;
	var padding=Math.round((width-Math.floor(width/170)*170)/2);
	document.getElementById('photos').style.paddingLeft=padding+'px';
}

addToStart(galeriesStart);