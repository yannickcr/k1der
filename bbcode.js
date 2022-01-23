var text_enter_url      = "Entrez un lien";
var text_enter_url_name = "Entrez le texte du lien";
var text_enter_image    = "Entrez l'URL complète de l'image";
var text_enter_email    = "Entrez l'adresse email";
var text_enter_flash    = "Entrer l'URL de l'Animation Flash.";
var error_no_url        = "Erreur ! Pas de lien rentré";
var error_no_title      = "Erreur ! Pas de texte rentré";
var error_no_email      = "Vous devez entrer une adresse email";
var error_no_image      = "Vous devez entrer l'URL complète de l'image";
var list_prompt         = "Entrez un objet de liste. Cliquez sur 'annuler' ou laissez vide pour terminer la liste";

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
					+ "[IMG="+enterURL+"]"
					+ input.value.substring(selectionEnd);
		
	} else if (document.selection) { // IE qui est moche
		var range = document.selection.createRange();
		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			
			if (!enterURL) { 
				alert(error_no_image); 
				return; 
			}
			
			range.text = "[IMG="+enterURL+"]";
		}
	}
}
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

function show_hide(id) {
	var show=document.getElementById(id).style.display;
	if(show=='none') document.getElementById(id).style.display='';
	else document.getElementById(id).style.display='none';
}