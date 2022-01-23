var IE = (document.all && !window.opera) ? true : false;
var OPERA = window.opera ? true : false;

/**
 * Permet l'affichage d'un message aprs un vnement
 * On passe en paramètres un tableau contenants les messages.
 */
function message(messages) {
	var url = window.location.href;
	var rech = RegExp("^(.*)#mess([0-9]+)$");
	if(url.match(rech)!=null) {
		var reg = RegExp("^(.*)#mess");
		id = url.replace(reg,'');
		// Création du message
		var div = document.createElement('div');
		div.id='messageok';
		var titre = document.createElement('h6');
		var titreTxt = document.createTextNode('Message');
		titre.appendChild(titreTxt);
		div.appendChild(titre);
		var divTxt = document.createTextNode(messages[id]);
		div.appendChild(divTxt);
		var button = document.createElement('input');
		button.type='button';
		button.value='Ok';
		div.appendChild(button);
		var bodys=document.getElementsByTagName('body');
		bodys[0].appendChild(div);
		
		// Met le focus sur le bouton
		button.focus();
		// Lui assigne l'action de supprimer le message lors du clique
		button.onclick = function()  {
			bodys[0].removeChild(div);
			window.location.hash='ok';
		};
	}
}

/**
* Blogix : Permet d'envoyer des données en GET ou POST en utilisant les XmlHttpRequest 
* http://qwix.media-box.net/index.php/2005/01/21/45-XmlhttprequestEtPhp
*/
function sendData(data, page, method,reponse,type,hide,todo,end) {
	
	if(!type) var type='innerHTML';
	if(!hide) var hide=false;
	
    // Internet Explorer c'est mal
    if(window.ActiveXObject) var XhrObj = new ActiveXObject("Microsoft.XMLHTTP") ;
    // Mozilla c'est bien
    else var XhrObj = new XMLHttpRequest();
    
    var content = document.getElementById(reponse);
    
	if(method == "GET" && data=='' && hide==true) {
		content.style.display='none';
		return false;
	} else if(method == "GET") XhrObj.open("GET", page+data+".html", true);
    else if(method == "POST") XhrObj.open("POST", page, true);

    XhrObj.onreadystatechange = function() {
        if (XhrObj.readyState == 4 && XhrObj.status == 200) {
			if(XhrObj.responseText.search(RegExp(/<body>/))!=-1) {
				sendData(data, page, method,reponse,type,hide,todo);
				return false;
			}
			var response=XhrObj.responseText.replace('<data>','').replace('</data>','');
			
			if(response!='' && hide==true && content) content.style.display='block';
			else if(hide==true && content) content.style.display='none';
            if(type=='value' && content) content.value = response;
			else if(type=='innerHTML' && content) content.innerHTML = response;
			if(todo && typeof(todo)=='function') todo();
		}
    }    

    if(method == "GET") XhrObj.send(null);
    else if(method == "POST") {
        XhrObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        XhrObj.send(data);
    }
}

/**
 * Ibilab.net : Evenements multiples au chargement d'une page
 * http://www.ibilab.net/webdev/articles/Javascript/evenements-multiples-chargement-page-7.htm
 */
function addToStart(fnc) {
	if(!window.listStart) window.listStart = new Array();
	window.listStart.push(fnc);
}

function start() {
	if (arguments.callee.done) return;
	arguments.callee.done = true;	
	
	var ls = window.listStart;
	if (ls) {
		for (var i=0;i<ls.length;i++) {
			var fnc = ls[i];
			if(typeof(fnc) == 'function') fnc();
			else eval(fnc);
		}
	}
	 xiti();
	 lightboxInit();	 
}

/**
 * Correctly handle PNG transparency in Win IE 5.5 & 6.
 * http://homepage.ntlworld.com/bobosola. Updated 18-Jan-2006.
 */
function pngFix() {
	var arVersion = navigator.appVersion.split("MSIE")
	var version = parseFloat(arVersion[1])
	
	if (version>=5.5 && version<7 && (document.body.filters)) {
		for(var i=0; i<document.images.length; i++) {
			var img = document.images[i]
			var imgName = img.src.toUpperCase()
			if (imgName.substring(imgName.length-3, imgName.length) == "PNG") {
				var imgID = (img.id) ? "id='" + img.id + "' " : ""
				var imgClass = (img.className) ? "class='" + img.className + "' " : ""
				var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
				var imgStyle = "display:inline-block;" + img.style.cssText 
				if (img.align == "left") imgStyle = "float:left;" + imgStyle
				if (img.align == "right") imgStyle = "float:right;" + imgStyle
				if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle
				var strNewHTML = "<span " + imgID + imgClass + imgTitle
				+ " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
				+ "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
				+ "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>" 
				img.outerHTML = strNewHTML
				i = i-1
			}
		}
	}
}


/**
 * domEl() function - painless DOM manipulation
 * written by Pawel Knapik  //  pawel.saikko.com
 */
function domEl(e,c,a,p,x)
{
	if(e||c) {
		// c=(typeof c=='string'||(typeof c=='object'&&!c.length))?[c]:c;
		if (typeof c=='string') c = c.length ? [c] : [];
		else if (typeof c=='object' && !c.length) c = [c];  
		e=(!e&&c.length==1)?document.createTextNode(c[0]):e;
		var n = (typeof e=='string')?document.createElement(e) :
		!(e&&e===c[0])?e.cloneNode(false):e.cloneNode(true);
		if(e.nodeType!=3) {
			if(!c) c='';
			c[0]===e?c[0]='':'';
			for(var i=0,j=c.length;i<j;i++) typeof c[i]=='string'?
			n.appendChild(document.createTextNode(c[i])):
			n.appendChild(c[i].cloneNode(true));
			if(a){for (var i in a) { i=='className'?n.className=a[i]:n.setAttribute(i,a[i]);}}
		}
	}
	if(!p)return n;
	p=(typeof p=='object'&&!p.length)?[p]:p;
	for(var i=(p.length-1);i>=0;i--) {
		if(x){while(p[i].firstChild)p[i].removeChild(p[i].firstChild);
			if(!e&&!c&&p[i].parentNode)p[i].parentNode.removeChild(p[i]);}
		if(n) p[i].appendChild(n.cloneNode(true));
	}
}

function $$(selector)
{
	// Attempt to fail gracefully in lesser browsers
	if (!document.getElementsByTagName) return new Array();
	// Split list
	if(selector.indexOf(',') > -1) {
		var list = selector.split(',');
		var el = new Array();
		for(var i=0;i<list.length;i++) el = el.concat($$(list[i].trim()));
		return el;
	}
	// Split selector in to tokens
	var tokens = selector.split(' ');
	var currentContext = new Array(document);
	for (var i = 0; i < tokens.length; i++) {
		token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');;
		if (token.indexOf('#') > -1) {
			// Token is an ID selector
			var bits = token.split('#');
			var tagName = bits[0];
			var id = bits[1];
			var element = document.getElementById(id);
			if ( ! element ) return new Array();
			// tag with that ID not found, return false
			if (tagName && element.nodeName.toLowerCase() != tagName) return new Array();
			// Set currentContext to contain just this element
			currentContext = new Array(element);
			continue; // Skip to next token
		}
		if (token.indexOf('.') > -1) {
			// Token contains a class selector
			var bits = token.split('.');
			var tagName = bits[0];
			var className = bits[1];
			if (!tagName) {
				tagName = '*';
			}
			// Get elements matching tag, filter them for class selector
			var found = new Array;
			var foundCount = 0;
			for (var h = 0; h < currentContext.length; h++) {
				var elements;
				if (tagName == '*') elements = getAllChildren(currentContext[h]);
				else elements = currentContext[h].getElementsByTagName(tagName);
				for (var j = 0; j < elements.length; j++) found[foundCount++] = elements[j];
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			for (var k = 0; k < found.length; k++) {
				if (found[k].className && found[k].className.match(new RegExp('\\b'+className+'\\b'))) {
					currentContext[currentContextIndex++] = found[k];
				}
			}
			continue; // Skip to next token
		}
		// Code to deal with attribute selectors
		if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/)) {
			var tagName = RegExp.$1;
			var attrName = RegExp.$2;
			var attrOperator = RegExp.$3;
			var attrValue = RegExp.$4;
			if (!tagName) tagName = '*';
			// Grab all of the tagName elements within current context
			var found = new Array;
			var foundCount = 0;
			for (var h = 0; h < currentContext.length; h++) {
				var elements;
				if (tagName == '*') elements = getAllChildren(currentContext[h]);
				else elements = currentContext[h].getElementsByTagName(tagName);
				for (var j = 0; j < elements.length; j++) found[foundCount++] = elements[j];
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			var checkFunction; // This function will be used to filter the elements
			switch (attrOperator) {
				case '=': // Equality
				checkFunction = function(e) { return (e.getAttribute(attrName) == attrValue); };
				break;
				case '~': // Match one of space seperated words 
				checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('\\b'+attrValue+'\\b'))); };
				break;
				case '|': // Match start with value followed by optional hyphen
				checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('^'+attrValue+'-?'))); };
				break;
				case '^': // Match starts with value
				checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) == 0); };
				break;
				case '$': // Match ends with value - fails with "Warning" in Opera 7
				checkFunction = function(e) { return (e.getAttribute(attrName).lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length); };
				break;
				case '*': // Match ends with value
				checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) > -1); };
				break;
				default :
				// Just test for existence of attribute
				checkFunction = function(e) { return e.getAttribute(attrName); };
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			for (var k = 0; k < found.length; k++) {
				if(!found[k].getAttribute(attrName)) continue;
				if(checkFunction(found[k])) currentContext[currentContextIndex++] = found[k];
			}
			// alert('Attribute Selector: '+tagName+' '+attrName+' '+attrOperator+' '+attrValue);
			continue; // Skip to next token
		}
		if (!currentContext[0]) return;
		
		// If we get here, token is JUST an element (not a class or ID selector)
		tagName = token;
		var found = new Array;
		var foundCount = 0;
		for (var h = 0; h < currentContext.length; h++) {
			var elements = currentContext[h].getElementsByTagName(tagName);
			for (var j = 0; j < elements.length; j++) found[foundCount++] = elements[j];
		}
		currentContext = found;
	}
	return currentContext;
}


/**
 * getElementsByClass - algorithm by Dustin Diaz, shortened by Pawel Knapik
 */
function getElementsByClass(s,n,t) {
	var c=[], e=(n?n:document).getElementsByTagName(t?t:'*'),r=new RegExp("(^|\\s)"+s+"(\\s|$)");
	for (var i=0,j=e.length;i<j;i++) r.test(e[i].className)?c.push(e[i]):''; return c }
	
/**
 * $() based on prototype.js dollar function idea, optimized by Pawel Knapik.
 */
function $(){var r=[],a=arguments;for(var i=0,j=a.length;i<j;i++){(typeof a[i]=='string')?(r.push(document.getElementById(a[i]))):(r.push(a[i]))}
return(r.length==1)?r[0]:r}

String.prototype.trim = function() { 
	return this.replace(/^\s+|\s+$/g, "");
};

function hasClass(elt, className)
{
	return ( (elt.className && elt.className.match( new RegExp( "\\b"+className+"\\b" ) ) )  ? true : false );
}

function addClass(elt, className)
{
	if ( ! hasClass(elt, className) ) {
		if (elt.className.length > 0 ) elt.className += ' ' + className;
		else elt.className = className;
	}
}

function removeClass(elt, className)
{
	if ( hasClass(elt, className) ) {
		elt.className = elt.className.replace(new RegExp( "\\b"+className+"\\b" ), "");
	}
}

Object.extend = function(destination, source) {
	for (var property in source) {
		destination[property] = source[property];
	}
	return destination;
}

Function.prototype.bind = function(object) {
	var __method = this;
	return function() {
		return __method.apply(object, arguments);
	}
}

function insertAfter(element, insert) {
	var next = getNextSibling(insert);
	if ( next )
		insert.parentNode.insertBefore(element, next);
	else
		insert.parentNode.appendChild(element);
}

function getPreviousSibling(elt)
{
	var sibling = elt.previousSibling;
	while (sibling != null) {
		if (sibling.nodeName == elt.nodeName)
			return sibling;
		sibling = sibling.previousSibling;
	}
	return null;
}

function getNextSibling(elt)
{
	var sibling = elt.nextSibling;
	while (sibling != null) {
		if (sibling.nodeName == elt.nodeName) return sibling;
		sibling = sibling.nextSibling;
	}
	return null;
}

function getTarget(e,tag) {
	if(e.srcElement) var el=e.srcElement;
	else var el=e.target;
	if(tag) while(el && el.nodeName.toUpperCase()!=tag.toUpperCase()) el=el.parentNode;
	return el;
}

function getPageHeight() {
	return (document.body.offsetHeight<document.documentElement.clientHeight)?document.documentElement.clientHeight:(document.body.offsetHeight+30);
}
function getPageWidth() {
	return document.documentElement.clientWidth;
}

function getTopPosition(o,m) {
	if(m=='height') var o=o.height;
	else var o=o.offsetHeight;
	if (IE) {
		return document.documentElement.scrollTop
		    + ( document.documentElement.clientHeight / 2 )
		    - ( o / 2 );
	} else {
		return window.pageYOffset
		    + ( window.innerHeight / 2 )
		    - ( o / 2);
	}
}

function getStyle(oElm, strCssRule){
    var strValue = "";
    if(document.defaultView && document.defaultView.getComputedStyle){
        strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
    }
    else if(oElm.currentStyle){
        strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
            return p1.toUpperCase();
        });
        strValue = oElm.currentStyle[strCssRule];
    }
    return strValue;
}

var fx = new Object();
fx.fade = function(d,e,o,t,s,c){
	if(!this.e) var obj = new Object({d:d,e:e,o:o,t:(t?t:20),s:(s?s:0.1),c:(c?c:'')});
	else var obj = this;
	var e = obj.e.style; // Les raccourcis c'est bien
	if(!IE) e.opacity=getStyle(obj.e,'opacity');
	else e.opacity=!obj.e.filters.length?1:(obj.e.filters.item('alpha').opacity/100);
	if((obj.d=='in'  && e.opacity>=obj.o) || 
	   (obj.d=='out' && e.opacity<=obj.o)) return typeof obj.c=='function'?obj.c():true;
	e.opacity = e.opacity*1+obj.s*(obj.d=='in'?1:-1);
	if(IE) e.filter = 'alpha(opacity='+(100*e.opacity)+')';
	setTimeout(fx.fade.bind(obj),obj.t);
};

function xiti() {
	Xt_param = 's=273865&amp;p='+TITLE;
	try {Xt_r = top.document.referrer;}
	catch(e) {Xt_r = document.referrer; }
	Xt_h = new Date();
	Xt_i = '<img style="visibility:hidden;" ';
	Xt_i += 'src="http://logv32.xiti.com/hit.xiti?'+Xt_param;
	Xt_i += '&amp;hl='+Xt_h.getHours()+'x'+Xt_h.getMinutes()+'x'+Xt_h.getSeconds();
	if(parseFloat(navigator.appVersion)>=4)
	{Xt_s=screen;Xt_i+='&amp;r='+Xt_s.width+'x'+Xt_s.height+'x'+Xt_s.pixelDepth+'x'+Xt_s.colorDepth;}
	$('xiti-logo').innerHTML=Xt_i+'&amp;ref='+Xt_r.replace(/[<>"]/g, '').replace(/&/g, '$')+'" alt="Xiti" />';
}

function lightboxInit() {
	var links= $$('a[href$="jpg"]');
	for(var i=0;i<links.length;i++) {
		if(!links[i].href.match(RegExp(/^http:\/\/www.k1der.net\/galeries/))) addClass(links[i], 'lightbox')
	}
	
	lightbox = new Lightbox({
		targets: '.lightbox',
		loadImage : 'templates/'+THEME+'/images/loading.gif',
		closeButton : 'templates/'+THEME+'/images/close.gif',
		speed: 20,
		opacity : 0.8
	});	
}

function startIE() {
	 xiti();
}

if (document.addEventListener) {
	/* for Mozilla */
	document.addEventListener("DOMContentLoaded", start, false);
	/* for other good browsers */
	document.addEventListener("load", start, false);
/* for other browsers */
} else window.onload = startIE;
