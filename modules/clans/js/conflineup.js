function confDelLineUp() {
	var liens = document.getElementsByTagName('a');
	// On rcupre tous les liens (<a>) du document dans une variable (un array), ici liens.
	// Une boucle qui parcourt le tableau (array) liens du début  la fin.
	for (var i = 0 ; i < liens.length ; ++i)  {
		// Si les liens ont un nom de class gal  confdel, alors on agit.
		if (liens[i].className == 'confdel')  {
			// Au clique de la souris.
			liens[i].onclick = function()  {
				var res=confirm('Etes vous certain de vouloir effacer cette Line up ?');
				if(res) window.location(this.href);
				return false; // On ouvre la page ayant pour URL le href du lien cliqu et on inhibe le lien rel.
			};
		}
	}
}

addToStart(confDelLineUp);