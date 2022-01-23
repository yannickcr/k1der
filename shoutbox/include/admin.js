/*----------------------------------------
            K1der Shoutbox 1.7 Beta7
               par Country
              www.k1der.net
----------------------------------------*/
/* Description : Scripts javascript de l'administration du shoutbox (partie apparence) */
/* Global */
function fond() {
	var couleur=document.getElementById("ifond").value;
	document.getElementById("apercu").style.backgroundColor="#"+couleur;
	document.getElementById("tab1").style.backgroundColor="#"+couleur;
	document.getElementById("tab2").style.backgroundColor="#"+couleur;
}
function cfont() {
	var couleur=document.getElementById("icfont").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("tab1").style.color="#"+couleur;
	document.getElementById("tab2").style.color="#"+couleur;
}
function tfont() {
	var taille=document.getElementById("itfont").value;
	document.getElementById("tab1").style.fontSize=taille;
	document.getElementById("tab2").style.fontSize=taille;
}
function police() {
	var police=document.getElementById("ipolice").value;
	document.getElementById("tab1").style.fontFamily=police;
	document.getElementById("tab2").style.fontFamily=police;
}
/* Bulle */
function bulle_fond() {
	var couleur=document.getElementById("ibulle_fond").value;
	document.getElementById("bulle").style.backgroundColor="#"+couleur;
}
function bulle_cfont() {
	var couleur=document.getElementById("ibulle_cfont").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("bulle").style.color="#"+couleur;
}
function bulle_tfont() {
	var taille=document.getElementById("ibulle_tfont").value;
	document.getElementById("bulle").style.fontSize=taille;
}
function bulle_police() {
	var police=document.getElementById("ibulle_police").value;
	document.getElementById("bulle").style.fontFamily=police;
}
function bulle_border() {
	var couleur=document.getElementById("ibulle_border").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("bulle").style.borderColor="#"+couleur;
}
function bulle_borders() {
	var style=document.getElementById("ibulle_borders").value;
	document.getElementById("bulle").style.borderStyle=style;
}
function bulle_bordert() {
	var taille=document.getElementById("ibulle_bordert").value;
	document.getElementById("bulle").style.borderWidth=taille;
}
/* Champs de formulaire */
function champs_bg() {
	var couleur=document.getElementById("ichamps_bg").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("pseudo").style.backgroundColor="#"+couleur;
	document.getElementById("message").style.backgroundColor="#"+couleur;
}
function champs_border() {
	var couleur=document.getElementById("ichamps_border").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("pseudo").style.borderColor="#"+couleur;
	document.getElementById("message").style.borderColor="#"+couleur;
}
function champs_borders() {
	var style=document.getElementById("ichamps_borders").value;
	document.getElementById("pseudo").style.borderStyle=style;
	document.getElementById("message").style.borderStyle=style;
}
function champs_bordert() {
	var taille=document.getElementById("ichamps_bordert").value;
	document.getElementById("pseudo").style.borderWidth=taille;
	document.getElementById("message").style.borderWidth=taille;
}
function champs_cfont() {
	var couleur=document.getElementById("ichamps_cfont").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("pseudo").style.color="#"+couleur;
	document.getElementById("message").style.color="#"+couleur;
}
function champs_tfont() {
	var taille=document.getElementById("ichamps_tfont").value;
	document.getElementById("pseudo").style.fontSize=taille;
	document.getElementById("message").style.fontSize=taille;
}
function champs_police() {
	var police=document.getElementById("ichamps_police").value;
	document.getElementById("pseudo").style.fontFamily=police;
	document.getElementById("message").style.fontFamily=police;
}
/* Boutons */
function boutons_bg() {
	var couleur=document.getElementById("iboutons_bg").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("valider").style.backgroundColor="#"+couleur;
}
function boutons_border() {
	var couleur=document.getElementById("iboutons_border").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("valider").style.borderColor="#"+couleur;
}
function boutons_borders() {
	var style=document.getElementById("iboutons_borders").value;
	document.getElementById("valider").style.borderStyle=style;
}
function boutons_bordert() {
	var taille=document.getElementById("iboutons_bordert").value;
	document.getElementById("valider").style.borderWidth=taille;
}
function boutons_cfont() {
	var couleur=document.getElementById("iboutons_cfont").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("valider").style.color="#"+couleur;
}
function boutons_tfont() {
	var taille=document.getElementById("iboutons_tfont").value;
	document.getElementById("valider").style.fontSize=taille;
}
function boutons_police() {
	var police=document.getElementById("iboutons_police").value;
	document.getElementById("valider").style.fontFamily=police;
}
/* Liens */
function liens_police() {
	var police=document.getElementById("iliens_police").value;
	document.getElementById("lien").style.fontFamily=police;
	document.getElementById("mail").style.fontFamily=police;
	document.getElementById("histo").style.fontFamily=police;
	document.getElementById("aide").style.fontFamily=police;
	document.getElementById("actu").style.fontFamily=police;
}
function liens_c() {
	var couleur=document.getElementById("iliens_c").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("lien").style.color="#"+couleur;
	document.getElementById("mail").style.color="#"+couleur;
	document.getElementById("histo").style.color="#"+couleur;
	document.getElementById("aide").style.color="#"+couleur;
	document.getElementById("actu").style.color="#"+couleur;;
}
function liens_tfont() {
	var taille=document.getElementById("iliens_tfont").value;
	document.getElementById("lien").style.fontSize=taille;
	document.getElementById("mail").style.fontSize=taille;
	document.getElementById("histo").style.fontSize=taille;
	document.getElementById("aide").style.fontSize=taille;
	document.getElementById("actu").style.fontSize=taille;
}
function liens_deco() {
	var deco=document.getElementById("iliens_deco").value;
	document.getElementById("lien").style.textDecoration=deco;
	document.getElementById("mail").style.textDecoration=deco;
	document.getElementById("histo").style.textDecoration=deco;
	document.getElementById("aide").style.textDecoration=deco;
	document.getElementById("actu").style.textDecoration=deco;
}
function liste_bg1() {
	var couleur=document.getElementById("iliste_bg1").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("td1").style.backgroundColor="#"+couleur;
	document.getElementById("td3").style.backgroundColor="#"+couleur;
}
function liste_bg2() {
	var couleur=document.getElementById("iliste_bg2").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("td2").style.backgroundColor="#"+couleur;
}
function liste_border() {
	var couleur=document.getElementById("iliste_border").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("liste").style.backgroundColor="#"+couleur;
}
function liste_c() {
	var couleur=document.getElementById("iliste_c").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("td1").style.color="#"+couleur;
	document.getElementById("td2").style.color="#"+couleur;
	document.getElementById("td3").style.color="#"+couleur;
}
function liste_tfont() {
	var taille=document.getElementById("iliste_tfont").value;
	document.getElementById("td1").style.fontSize=taille;
	document.getElementById("td2").style.fontSize=taille;
	document.getElementById("td3").style.fontSize=taille;
}
function liste_police() {
	var police=document.getElementById("iliste_police").value;
	document.getElementById("td1").style.fontFamily=police;
	document.getElementById("td2").style.fontFamily=police;
	document.getElementById("td3").style.fontFamily=police;
}
function liste_cadmin() {
	var couleur=document.getElementById("iliste_cadmin").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("admin").style.color="#"+couleur;
}
function liste_cmodo() {
	var couleur=document.getElementById("iliste_cmodo").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("modo").style.color="#"+couleur;
}
function liste_cvisit() {
	var couleur=document.getElementById("iliste_cvisit").value;
	while(couleur.length<6) {
		couleur=couleur+"0";
	}
	document.getElementById("visit").style.color="#"+couleur;
}