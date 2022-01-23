function openScript(url, width, height) {
        var Win = window.open(url,"openScript",'width=' + width + ',height=' + height + ',resizable=no,scrollbars=yes,menubar=no,status=no' );
}
var codesHexa="0123456789ABCDEF";
function codeHexa (octetDec) // octet décimal ==> hexadécimal
	{
	return (codesHexa.charAt(octetDec>>>4)+codesHexa.charAt(octetDec&15));
	}
function decodeHexa (octetHex) // octet hexadécimal ==> décimal
	{
	return ( (codesHexa.indexOf(octetHex.charAt(0))<<4) + codesHexa.indexOf(octetHex.charAt(1)) );
	}
// FONCTIONS DE CRYPTAGE
var clef="9Ajf0kDhD4GBwnuis1ys1d45br7uyZ"; // Vous pouvez modifier cette clef
function crypte (texte) // texte en clair ==> texte crypté
	{
	resultat="";
	l=texte.length;
	lc=clef.length;
	m=0;
	for (n=0;n<l;n++)
		{
		c=texte.charCodeAt(n);
		if (c<256) // Uniquement les caractères ASCII
			{
     			resultat+=codeHexa( c ^ clef.charCodeAt(m%lc) );
			m++;
			}
		}
	return resultat;
	}
function decrypte (texte) // texte crypté ==> texte en clair
	{
	resultat="";
	l=texte.length;
	lc=clef.length;
	m=0;
	for (n=0;n<l;n+=2)
		{
		c=decodeHexa(texte.substr(n,2));
		resultat+=String.fromCharCode( c ^ clef.charCodeAt(m%lc) );
		m++;
		}
		return resultat;
	}
function rech(){

  with(document.rechform){

    if( texte.value == '' ){
      alert("Tu n'as pas rentr&eacute; de texte à rechercher\nalors forc&eacute;ment la recherche risque d'être difficile...");
      return false;
    }
  }
  return true;
}
if (navigator.appVersion.substring(0,1)>=3)
{
img1=new Image;
img1.src='images/fond.gif';
}
function Message(message,champ)
  {
  if(document.getElementById)
   // document.getElementById("ejs_texte").style.display="block";
   // document.getElementById("def").style.display="none";
    document.getElementById(champ).innerHTML=message;
  }
function Message2(message,champ)
  {
  if(document.getElementById)
   // document.getElementById("ejs_texte").style.display="none";
   // document.getElementById("def").style.display="block";
    document.getElementById(champ).innerHTML ="&nbsp;";
  }
function ChangeMessage(champ)
  {
  if(document.getElementById(champ).gro != 1)
  {
  if(document.getElementById)
    document.getElementById(champ).style.display="block";
	document.getElementById(champ).gro = 1;
  }
  else
  {
  if(document.getElementById)
    document.getElementById(champ).style.display="none";
	document.getElementById(champ).gro = 0;
  }
  }
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

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
	
	document.getElementById("bulle").style.top = mouseY+20+"px";
	document.getElementById("bulle").style.left = mouseX-40+"px";
	document.getElementById("bulle").style.visibility = voir;
	document.getElementById("bulle").style.display = display; 
	}
	init();
	function cache(){
		document.getElementById("bulle").style.visibility = "hidden";
	}
}