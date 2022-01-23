/**
 * Textes des erreurs
 */
var text_enter_url      = "Entrez un lien";
var text_enter_url_name = "Entrez le texte du lien";

var text_enter_video_url = "Entrez le lien complet vers la vidéo (format flv)";
var text_enter_video_title = "Entrez le titre de la vidéo";
var text_enter_video_height = "Entrez la hauteur de la vidéo";
var text_enter_video_width = "Entrez la largeur de la vidéo";


var text_enter_image    = "Entrez l'URL complte de l'image";
var text_enter_email    = "Entrez l'adresse email";
var text_enter_flash    = "Entrer l'URL de l'Animation Flash.";
var error_no_url        = "Erreur ! Pas de lien rentr";
var error_no_height     = "Erreur ! Pas de hauteur rentre";
var error_no_width      = "Erreur ! Pas de largeur rentre";
var error_no_title      = "Erreur ! Pas de texte rentr";
var error_no_email      = "Vous devez entrer une adresse email";
var error_no_image      = "Vous devez entrer l'URL complte de l'image";
var list_prompt         = "Entrez un objet de liste. Cliquez sur 'annuler' ou laissez vide pour terminer la liste";

/**
 * Fonction utilise pour les tags simples (B,I,U, etc...)
 */
function replaceSelection (replaceString,starttag,endtag) {
  var input=document.getElementById("message");
  input.focus();
  if(!starttag) starttag=replaceString;
  if(!endtag) endtag=replaceString;
  var butval=document.getElementById(replaceString).value;
  if (input.setSelectionRange) { // Firefox qui est beau
    var selectionStart = input.selectionStart;
    var selectionEnd = input.selectionEnd;
	var selection = input.value.substring(selectionStart, selectionEnd);
    if(selection==0) {
        if(butval.search(/\*/i)==-1) {
            document.getElementById(replaceString).value=butval+"*";
            input.value = input.value+"["+starttag+"]";
        } else {
            document.getElementById(replaceString).value=butval.replace(/\*/i,"");
            input.value = input.value+"[/"+endtag+"]";
        }
    } else {
            input.value = input.value.substring(0, selectionStart)
						  + "["+starttag+"]"+selection+"[/"+endtag+"]"
						  + input.value.substring(selectionEnd);
    }
  }
  else if (document.selection) { // IE qui est moche
    var range = document.selection.createRange();
    if (range.parentElement() == input) {
      var isCollapsed = range.text == '';
	  var selection = range.text;
      if(selection==0) {
		  if(butval.search(/\*/i)==-1) {
			  document.getElementById(replaceString).value=butval+"*";
			  range.text = "["+replaceString+"]";
		  } else {
			  document.getElementById(replaceString).value=butval.replace(/\*/i,"");
			  range.text = "[/"+replaceString+"]";
		  }
	  } else range.text = "["+replaceString+"]"+selection+"[/"+replaceString+"]";
      if (!isCollapsed)  {
        range.moveStart('character', -replaceString.length);
        range.select();
      }
    }
  }
}

/**
 * Ajout d'une URL dans le message
 */
function add_url() {
	var input=document.getElementById("message");
    input.focus();
	var FoundErrors='';
	var enterURL=prompt(text_enter_url, "http://");
	
	if (input.setSelectionRange) { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		var selection = input.value.substring(selectionStart, selectionEnd);
		
		if(selection==0) {
			var enterTITLE = prompt(text_enter_url_name, "My Webpage");
		} else enterTITLE = selection;
		
		if (!enterURL) FoundErrors += " " + error_no_url;
		if (!enterTITLE) FoundErrors += " " + error_no_title;
		if (FoundErrors) {
			alert("Erreur!"+FoundErrors);
			return;
		}
		
		input.value = input.value.substring(0, selectionStart)
					+ "[URL="+enterURL+"]"+enterTITLE+"[/URL]"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection) { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			var selection = range.text;
			if(selection==0) {
				var enterTITLE = prompt(text_enter_url_name, "My Webpage");
			} else enterTITLE = selection;
			
			if (!enterURL) FoundErrors += " " + error_no_url;
			if (!enterTITLE) FoundErrors += " " + error_no_title;
			if (FoundErrors) {
				alert("Erreur!"+FoundErrors);
				return;
			}
			
			range.text = "[URL="+enterURL+"]"+enterTITLE+"[/URL]";
		}
	}
}

/**
 * Ajout d'un e-mail dans le message
 */
function add_email() {
	var input=document.getElementById("message");
    input.focus();
    var emailAddress=prompt(text_enter_email, "");

	if (input.setSelectionRange) { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		
		if (!emailAddress) { 
			alert(error_no_email); 
			return; 
		}
		
		input.value = input.value.substring(0, selectionStart)
					+ "[EMAIL]"+emailAddress+"[/EMAIL]"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection) { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			
			if (!emailAddress) { 
				alert(error_no_email); 
				return; 
			}
			
			range.text = "[EMAIL]"+emailAddress+"[/EMAIL]";
		}
	}
}

/**
 * Ajout d'une image dans le message
 */
function add_image() {
	var input=document.getElementById("message");
    input.focus();
    var enterURL=prompt(text_enter_image, "http://");

	if (input.setSelectionRange) { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		
		if (!enterURL) {
			alert(error_no_image);
			return;
		}
		
		input.value = input.value.substring(0, selectionStart)
					+ "[IMG]"+enterURL+"[/IMG]"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection) { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			
			if (!enterURL) { 
				alert(error_no_image); 
				return; 
			}
			
			range.text = "[IMG]"+enterURL+"[/IMG]";
		}
	}
}

/**
 * Ajout d'une VIDEO dans le message
 */
function add_video() {
	var input=document.getElementById("message");
    input.focus();
	var FoundErrors='';
	var enterURL=prompt(text_enter_video_url, "http://");
	
	if (input.setSelectionRange) { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		var selection = input.value.substring(selectionStart, selectionEnd);
		
		if(selection==0) {
			var enterTITLE = prompt(text_enter_video_title, "");
		} else enterTITLE = selection;
		
		var enterWIDTH = prompt(text_enter_video_width, "");
		var enterHEIGHT = prompt(text_enter_video_height, "");
		
		if (!enterURL) FoundErrors += " " + error_no_url;
		//if (!enterTITLE) FoundErrors += " " + error_no_title;
		if (!enterWIDTH) FoundErrors += " " + error_no_width;
		if (!enterHEIGHT) FoundErrors += " " + error_no_height;
		if (FoundErrors) {
			alert("Erreur!"+FoundErrors);
			return;
		}
		
		input.value = input.value.substring(0, selectionStart)
					+ "[VIDEO="+enterURL+" TITLE="+enterTITLE+" SIZE="+enterWIDTH+"_"+enterHEIGHT+" /]"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection) { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			var selection = range.text;
			if(selection==0) {
				var enterTITLE = prompt(text_enter_url_name, "My Webpage");
			} else enterTITLE = selection;
			
			var enterWIDTH = prompt(text_enter_video_width, "");
			var enterHEIGHT = prompt(text_enter_video_height, "");

			if (!enterURL) FoundErrors += " " + error_no_url;
			//if (!enterTITLE) FoundErrors += " " + error_no_title;
			if (!enterWIDTH) FoundErrors += " " + error_no_width;
			if (!enterHEIGHT) FoundErrors += " " + error_no_height;
			if (FoundErrors) {
				alert("Erreur!"+FoundErrors);
				return;
			}
			
			range.text = "[VIDEO="+enterURL+" TITLE="+enterTITLE+" SIZE="+enterWIDTH+"_"+enterHEIGHT+" /]";
		}
	}
}


/**
 * Ajout d'une liste dans le message
 */
function add_list() {
	var input=document.getElementById("message");
    input.focus();
	var listvalue="init";
	var thelist="";
	
	while ( (listvalue != "") && (listvalue != null) )
	{
		listvalue = prompt(list_prompt, "");
		if ( (listvalue != "") && (listvalue != null) )
		{
			thelist = thelist+"[*]"+listvalue+"\n";
		}
	}

	if (input.setSelectionRange && thelist!="") { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		
		input.value = input.value.substring(0, selectionStart)
					+ "[LIST]\n"+thelist+"[/LIST]\n"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection && thelist!="") { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			
			range.text = "[LIST]\n"+thelist+"[/LIST]\n";
		}
	}
}

/**
 * Ajout d'un classement dans le message
 */
function add_classement() {
	var input=document.getElementById("message");
    input.focus();
	var listvalue="init";
	var thelist="";
	var i=1;
	var ext="er";
	while ( (listvalue != "") && (listvalue != null) )
	{
		listvalue = prompt(list_prompt, "");
		if ( (listvalue != "") && (listvalue != null) )
		{
			thelist = thelist+"["+i+ext+"]"+listvalue+"\n";
		}
		i++;
		ext="me";
	}

	if (input.setSelectionRange && thelist!="") { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		
		input.value = input.value.substring(0, selectionStart)
					+ "[CLASSEMENT]\n"+thelist+"[/CLASSEMENT]\n"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection && thelist!="") { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			
			range.text = "[CLASSEMENT]\n"+thelist+"[/CLASSEMENT]\n";
		}
	}
}

/**
 * Ajout d'un attibut au texte (taille,couleur) dans le message
 */
function add_attribut(replaceString) {
  var input=document.getElementById("message");
  input.focus();
  var butval = document.getElementById(replaceString).value;
  if (input.setSelectionRange) { // Firefox qui est beau
    var selectionStart = input.selectionStart;
    var selectionEnd = input.selectionEnd;
	var selection = input.value.substring(selectionStart, selectionEnd);
    if(selection==0) {
    	  input.value = input.value+"["+replaceString+"="+butval+"][/"+replaceString+"]";
    } else {
		  input.value = input.value.substring(0, selectionStart)
					  + "["+replaceString+"="+butval+"]"+selection+"[/"+replaceString+"]"
					  + input.value.substring(selectionEnd);
    }
  }
  else if (document.selection) { // IE qui est moche
    var range = document.selection.createRange();
    if (range.parentElement() == input) {
      var isCollapsed = range.text == '';
	  var selection = range.text;
      if(selection==0) {
    	  range.text = "["+replaceString+"="+butval+"][/"+replaceString+"]";
	  } else range.text = "["+replaceString+"="+butval+"]"+selection+"[/"+replaceString+"]";
    }
  }
  document.getElementById(replaceString).value=0;
}

/**
 * Ajout d'un smiley dans le message
 */
function add_smiley(emocode) {
	var input=document.getElementById("message");
    input.focus();
	if (input.setSelectionRange) { // Firefox qui est beau
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;
		input.value = input.value.substring(0, selectionStart)
					+ " "+emocode+" "
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection) { // IE qui est moche
	
		var range = document.selection.createRange();
		if (range.parentElement().name == input.name) {
		  var isCollapsed = range.text == '';
		  var selection = range.text;
		  if(selection==0) {
			  range.text = " "+emocode+" ";
			} else range.text = " "+emocode+" ";
		}
	}
}

/**
 * Agrandissement de la zone de message
 */
function expand() {
	var message=document.getElementById("message");
	var height=message.style.height.replace('px','')*1;
	if(height==0) height=250;
	message.style.height=(height+100)+"px";
}

/**
 * Diminution de la zone de message
 */
function decrease() {
	var message=document.getElementById("message");
	var height=message.style.height.replace('px','')*1;
	if(height==0) height=250;
	message.style.height=(height-100)+"px";
}