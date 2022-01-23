function donateInit() {
	interstitiel = new Interstitiel({
		targets:'#donatelink',
		classShow : 'donate',
		classHide : 'donatehide'
	});
}
addToStart(donateInit);