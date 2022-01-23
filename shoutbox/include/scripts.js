/*----------------------------------------
            K1der Shoutbox 1.7 Beta7
               par Country
              www.k1der.net
----------------------------------------*/
/* Description : Scripts javascript du shoutbox */
/*
Script pour afficher les bulles contenant la date et heure du message
*/
bname=navigator.appName;
function affiche(action,contenu){
	var voir;
	var display;
	if (action == "cache"){
		voir = "hidden";
		display = "none";
	}
	else {
		voir = "visible";
		display = "block";
	}	
	document.getElementById("bulle").innerHTML = contenu;
	function init() {
		document.onmousemove=mousemove;
	}
	function mousemove(e) {
		if (navigator.appName.indexOf("Explorer") > -1) {
			var mouseX=event.clientX + document.body.parentNode.scrollLeft;
			var mouseY=event.clientY + document.body.parentNode.scrollTop;
		}
		else {
			var mouseX=e.pageX + document.body.scrollLeft;
			var mouseY=e.pageY + document.body.scrollTop;
		}
	
	document.getElementById("bulle").style.top = mouseY-40+"px";
	document.getElementById("bulle").style.left = mouseX+10+"px";
	document.getElementById("bulle").style.visibility = voir;
	document.getElementById("bulle").style.display = display; 
	}
	init();
	function cache(){
		document.getElementById("bulle").style.visibility = "hidden";
	}
}
/*
Script ouvrant une pop-up pour l'aide ou l'historique
*/
function openscript(url, width, height) {
        var Win = window.open(url,'','width=' + width + ',height=' + height + ',resizable=no,scrollbars=yes,menubar=no,status=no' );
}
/*
Demande de confirmation pour la suppression d'un message
*/
function supprimer(id,auteur)
{
 var phrase = 'Voulez-vous vraiment supprimer le message de '+auteur+' ?';
 var redir = '?action=suppr&id='+id;
 resultat = confirm(phrase);
 if(resultat==1) {
 	window.location=redir;
 }
}/*
Demande de confirmation pour la suppression d'un modérateur
*/
function supprimer_user(user)
{
 var phrase = 'Voulez-vous vraiment supprimer le modérateur '+user+' ?';
 var redir = '?action=suppr&user='+user;
 resultat = confirm(phrase);
 if(resultat==1) {
 	window.location=redir;
 }
}