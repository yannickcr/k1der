// remote scripting library
// (c) copyright 2005 modernmethod, inc
var sajax_debug_mode = false;
var sajax_request_type = "POST";
var sajax_target_id = "";
var sajax_failure_redirect = "";

function sajax_debug(text) {
	if (sajax_debug_mode)
		alert(text);
}

function sajax_init_object() {
	sajax_debug("sajax_init_object() called..");
	
	var A;
	
	var msxmlhttp = new Array(
		'Msxml2.XMLHTTP.5.0',
		'Msxml2.XMLHTTP.4.0',
		'Msxml2.XMLHTTP.3.0',
		'Msxml2.XMLHTTP',
		'Microsoft.XMLHTTP');
	for (var i = 0; i < msxmlhttp.length; i++) {
		try {
			A = new ActiveXObject(msxmlhttp[i]);
		} catch (e) {
			A = null;
		}
	}
	
	if(!A && typeof XMLHttpRequest != "undefined")
		A = new XMLHttpRequest();
	if (!A)
		sajax_debug("Could not create connection object.");
	return A;
}

var sajax_requests = new Array();

function sajax_cancel() {
	for (var i = 0; i < sajax_requests.length; i++) 
		sajax_requests[i].abort();
}

function sajax_do_call(func_name, args) {
	var i, x, n;
	var uri;
	var post_data;
	var target_id;
	
	sajax_debug("in sajax_do_call().." + sajax_request_type + "/" + sajax_target_id);
	target_id = sajax_target_id;
	if (typeof(sajax_request_type) == "undefined" || sajax_request_type == "") 
		sajax_request_type = "GET";
	
//	uri = "/";
	uri = document.location.pathname;
	if (sajax_request_type == "GET") {
	
		if (uri.indexOf("?") == -1) 
			uri += "?rs=" + encodeURIComponent(func_name);
		else
			uri += "&rs=" + encodeURIComponent(func_name);
		uri += "&rst=" + encodeURIComponent(sajax_target_id);
		uri += "&rsrnd=" + new Date().getTime();
		
		for (i = 0; i < args.length-1; i++) 
			uri += "&rsargs[]=" + encodeURIComponent(args[i]);

		post_data = null;
	} 
	else if (sajax_request_type == "POST") {
		post_data = "rs=" + encodeURIComponent(func_name);
		post_data += "&rst=" + encodeURIComponent(sajax_target_id);
		post_data += "&rsrnd=" + new Date().getTime();
		
		for (i = 0; i < args.length-1; i++) 
			post_data = post_data + "&rsargs[]=" + encodeURIComponent(args[i]);
	}
	else {
		alert("Illegal request type: " + sajax_request_type);
	}
	
	x = sajax_init_object();
	if (x == null) {
		if (sajax_failure_redirect != "") {
			location.href = sajax_failure_redirect;
			return false;
		} else {
			sajax_debug("NULL sajax object for user agent:\n" + navigator.userAgent);
			return false;
		}
	} else {
		x.open(sajax_request_type, uri, true);
		// window.open(uri);
		
		sajax_requests[sajax_requests.length] = x;
		
		if (sajax_request_type == "POST") {
			x.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
			x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		}
	
		x.onreadystatechange = function() {
			if (x.readyState != 4) 
				return;

			sajax_debug("received " + x.responseText);
		
			var status;
			var data;
			var txt = x.responseText.replace(/^\s*|\s*$/g,"");
			status = txt.charAt(0);
			data = txt.substring(2);

			if (status == "") {
				// let's just assume this is a pre-response bailout and let it slide for now
			} else if (status == "-") 
				alert("Error: " + data);
			else {
				if (target_id != "") 
					document.getElementById(target_id).innerHTML = eval(data);
				else {
					try {
						var callback;
						var extra_data = false;
						if (typeof args[args.length-1] == "object") {
							callback = args[args.length-1].callback;
							extra_data = args[args.length-1].extra_data;
						} else {
							callback = args[args.length-1];
						}
						callback(eval(data), extra_data);
					} catch (e) {
						sajax_debug("Caught error " + e + ": Could not eval " + data );
					}
				}
			}
		}
	}
	
	sajax_debug(func_name + " uri = " + uri + "*/post = " + post_data);
	x.send(post_data);
	sajax_debug(func_name + " waiting..");
	delete x;
	return true;
}

		
// wrapper for cforms_submitcomment		
function x_cforms_submitcomment() {
	sajax_do_call("cforms_submitcomment",
		x_cforms_submitcomment.arguments);
}



//
// core cforms functions
//

function call_err(no,err,popFlag){

		//temp. turn send button back on
		document.getElementById('sendbutton'+no).style.cursor = "auto";
		document.getElementById('sendbutton'+no).disabled = false;

		err = unescape(decodeURI( err.value ));
		//err = err.value;

		stringDOM = '<root>' + err.replace(/(.*)(\r\n|$)/g, '<text>$1</text>') + '</root>';
		stringXHTML = err.replace(/(\r\n)/g, '<br/>');

		msgbox = 'usermessage'+no;
		if( document.getElementById(msgbox+'a') )
			document.getElementById(msgbox+'a').className = "info failure";
		if( document.getElementById(msgbox+'b') )
			document.getElementById(msgbox+'b').className = "info failure";

		doInnerXHTML(msgbox, stringXHTML.replace(/\\/g,""), stringDOM.replace(/\\/g,""));

		//popup error
    err = err.replace(/\\/g,"");
		if ( document.getElementById('cf_popup'+no).value.charAt(popFlag) == 'y')
					alert( err );
}


function clearField(thefield) {
  if ( thefield.defaultValue == thefield.value ) 
  		thefield.value = '';
};

function setField(thefield) {
  if ( thefield.value == '' ) 
  		thefield.value = thefield.defaultValue;
};
  
  
function cforms_validate(no, upload) {

	if (!no) no='';

	msgbox = 'usermessage'+no;
	if( document.getElementById(msgbox+'a') )
		document.getElementById(msgbox+'a').className = "info";
	if( document.getElementById(msgbox+'b') )
		document.getElementById(msgbox+'b').className = "info";
		
	waiting = decodeURI(document.getElementById('cf_working'+no).value);
	waiting = waiting.replace(/\\/g,"");

	if( doInnerXHTML(msgbox, waiting) ) {

		var all_valid = true;
		var code_err  = false;

		var regexp_e = new RegExp('^[\\w-_\.]+@[\\w-_]+[\.][\\w-_\.]+$');  // email regexp

		objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('*');

		for (var i = 0, j = objColl.length; i < j; i++) {

				temp = objColl[i].className;

				if ( temp.match(/secinput/) )
					newclass = 'secinput';
				else if ( c=temp.match(/cf-box-./) )
					newclass = c;
				else if ( temp.match(/cformselect/) )
					newclass = 'cformselect';
				else
					newclass = 'default';


				fld = objColl[i].nodeName.toLowerCase();
				typ = objColl[i].type;

				if ( (fld == "input" || fld == "textarea" || fld == "select") && !( typ=="hidden" || typ=="radio" || typ=="submit") ) {

				    if ( temp.match(/required/) ) {

								newclass = newclass + ' fldrequired';

								n = objColl[i].nextSibling;
								p = objColl[i].previousSibling;

								//if regexp provided use it!
								regexp = 1;
								obj_regexp = document.getElementById(objColl[i].id+'_regexp');

								if ( obj_regexp && obj_regexp.value != '' ) {
										regexp = new RegExp(obj_regexp.value);
										regexp = objColl[i].value.match(regexp);
								}


								if ( temp.match(/cf-box-./) ) {

											if ( objColl[i].checked==false ) {
														newclass = newclass + ' error';

														if ( all_valid )
														    objColl[i].focus();
													// we can't change the checkbox much but the text on the side!
													if( n && n.nodeName.toLowerCase()=="label" && !n.className.match(/errortxt/) )
															n.className = n.className + " errortxt";
													else if ( p && p.nodeName.toLowerCase()=="label" && !p.className.match(/errortxt/) )
															p.className = p.className + " errortxt";


														all_valid=false;
											}else{

													// we can't change the checkbox much but the text on the side!
													if( n && n.nodeName.toLowerCase()=="label" && n.className.match(/errortxt/) )
															n.className = n.className.substr(0,n.className.search(/ errortxt/));
													else if ( p && p.nodeName.toLowerCase()=="label" && p.className.match(/errortxt/) )
															p.className = p.className.substr(0,p.className.search(/ errortxt/));

											}


								} else if ( temp.match(/cformselect/) ) {

											if ( objColl[i].value=='' || objColl[i].value=='-' ){
														newclass = newclass + ' error';
														all_valid=false;
											}

								} else if ( objColl[i].value=='' || regexp==null ) {

											newclass = newclass + ' error';
											all_valid=false;

								}

					}
					else if ( temp.match(/email/) ) {
								newclass = newclass + ' fldemail';
								if (objColl[i].value=='' || !objColl[i].value.match(regexp_e)) {
										newclass = newclass + ' error';
										all_valid=false;
					 			}
					} //else

					objColl[i].className = newclass;
				} // if fields

		} // for


		//normal visitor verification turned on?
		if ( document.getElementById('cforms_q'+no) && (document.getElementById('cforms_a'+no).value != hex_md5(encodeURI(document.getElementById('cforms_q'+no).value.toLowerCase()) )) ) {
			document.getElementById('cforms_q'+no).className = "secinput error";
			if ( all_valid ) {
				all_valid = false;
				code_err = true;
			}
		}

		//captcha verification turned on?
		if ( document.getElementById('cforms_captcha'+no) && (document.getElementById('cforms_cap'+no).value != hex_md5(document.getElementById('cforms_captcha'+no).value.toLowerCase() )) ) {
			document.getElementById('cforms_captcha'+no).className = "secinput error";
			if ( all_valid ) {
				all_valid = false;
				code_err = true;
			}
		}

		//all good?  if "upload file" field included, don't do ajax
		if ( all_valid && upload ){
			document.getElementById('sendbutton'+no).style.cursor = "progress";
			return true;
		}
		else if ( all_valid ) {
			document.getElementById('sendbutton'+no).style.cursor = "progress";
			document.getElementById('sendbutton'+no).disabled = true;
			cforms_submitcomment(no);
			}

		if ( !all_valid && !code_err ){
			call_err(no,document.getElementById('cf_failure'+no),1);
			return false
		}

		if ( !all_valid ){
			call_err(no,document.getElementById('cf_codeerr'+no),1);
			return false
		}



		return false;

	} else	// if do_inner
		return true;
}



function doInnerXHTML(elementId, stringXHTML, stringDOM) {
	try {
		var elem = document.getElementById(123);  //test manual error
		if ( !stringDOM )
		    stringDOM = '<root><text>'+stringXHTML+'</text></root>';


  		stringDOM = html_escape(stringDOM);

	  	if ( document.getElementById(elementId+'a') ) {
			var elem = document.getElementById(elementId+'a');

			var children =  elem.childNodes;
	
			for (var i = 0; i < children.length; i++) {
				elem.removeChild(children[i]);
			}
	
			if (window.ActiveXObject) {
					var nodes = new ActiveXObject("Microsoft.XMLDOM");
					nodes.loadXML(stringDOM);
			} else {
					var nodes = new DOMParser().parseFromString(stringDOM, 'text/xml');
			}
	
			var ergebnisse = nodes.getElementsByTagName("text");
			var span = document.createElement("span");
			// alert("made it"); //debug
	
			elem.appendChild(span);
	
			for (var i = 0; i < ergebnisse.length; i++) {
				span.appendChild(document.createTextNode(ergebnisse[i].firstChild.nodeValue));
				if (i < ergebnisse.length) span.appendChild(document.createElement("br"));
			}
		}
		

	  	if ( document.getElementById(elementId+'b') ) {
			var elem = document.getElementById(elementId+'b');

			var children =  elem.childNodes;
	
			for (var i = 0; i < children.length; i++) {
				elem.removeChild(children[i]);
			}
	
			if (window.ActiveXObject) {
					var nodes = new ActiveXObject("Microsoft.XMLDOM");
					nodes.loadXML(stringDOM);
			} else {
					var nodes = new DOMParser().parseFromString(stringDOM, 'text/xml');
			}
	
			var ergebnisse = nodes.getElementsByTagName("text");
			var span = document.createElement("span");
			// alert("made it"); //debug
	
			elem.appendChild(span);
	
			for (var i = 0; i < ergebnisse.length; i++) {
				span.appendChild(document.createTextNode(ergebnisse[i].firstChild.nodeValue));
				if (i < ergebnisse.length) span.appendChild(document.createElement("br"));
			}
		}
					
		return true;

	} catch (e) {

		try {
		 	 //alert("debug");  //debug
		  	if ( document.getElementById(elementId+'a') )
				document.getElementById(elementId+'a').innerHTML = stringXHTML;
		  	if ( document.getElementById(elementId+'b') )
				document.getElementById(elementId+'b').innerHTML = stringXHTML;
			return true;
		}
		catch(ee) {
			return false;
		}
	}
}



function cforms_submitcomment(no) {
		var regexp = new RegExp('[$][#][$]', ['g']);
		var prefix = '$#$';

		if ( no=='' ) params = '1'; else params = no;

		objColl = document.getElementById('cforms'+no+'form').getElementsByTagName('*');

		for (var i = 0, j = objColl.length; i < j; i++) {

		    fld = objColl[i].nodeName.toLowerCase();
 				typ = objColl[i].type;

				if ( fld == "input" || fld == "textarea" || fld == "select" ) {

						if ( typ == "checkbox" ) {

							if ( objColl[i].name.match(/\[\]/) ){
								group='';
								
								while ( !(objColl[i].tagName.toLowerCase()!='span' && objColl[i].nextSibling==null) && !(objColl[i].textContent && objColl[i].textContent.match(/\n/)) && i < j ){

									if ( objColl[i].type == 'checkbox' && objColl[i].name.match(/\[\]/) && objColl[i].checked ) {
										group = group + objColl[i].value + ',';
									}
									i++;
								}
									
								params = params + prefix + group.substring(0,group.length-1);
								i=i-1;
							}
							else
								params = params + prefix + (objColl[i].checked?"X":"-");
								
				 		} else
						if ( typ == "radio" && objColl[i].checked ) {
								params = params + prefix + objColl[i].value;
					 	} else
						if ( typ == "select-multiple" ) {
        						all_child_obj='';
						        for (z=0;z<objColl[i].length; z++) {
						              if (objColl[i].childNodes[z].selected) {
            						        all_child_obj = all_child_obj + objColl[i].childNodes[z].value.replace(regexp, '$') + ','
            						  }
								}
						        params = params + prefix + all_child_obj.substring(0,all_child_obj.length-1);
								
					 	} else
						if ( typ != "hidden" && typ != "submit" && typ != "radio") {
								params = params + prefix + objColl[i].value.replace(regexp, '$');
					 	}

		 		}
		}
		x_cforms_submitcomment(params, cforms_setsuccessmessage);
}



function cforms_setsuccessmessage(message) {

		result="success";

		var offset = message.indexOf('*$#');
		var no = message.substring(0,offset);
		var pop    = message.charAt(offset+3); // check with return val from php call!

		if ( no == '1' ) no='';

		document.getElementById('cforms'+no+'form').reset();
		document.getElementById('sendbutton'+no).style.cursor = "auto";
		document.getElementById('sendbutton'+no).disabled = false;

		if ( !message.match(/<root>/) && message.match(/http:/))
			location.href = message;
		else if ( message.match(/!!!/) )
			result = " mailerr";


		stringXHTML = message.substring(offset+4,message.indexOf('|'));
		stringDOM   = message.substring(message.indexOf('|')+1);

		// for both message boxes		
	  	if ( document.getElementById('usermessage'+no+'a') )
			document.getElementById('usermessage'+no+'a').className = "info "+result;
	  	if ( document.getElementById('usermessage'+no+'b') )
			document.getElementById('usermessage'+no+'b').className = "info "+result;

		doInnerXHTML('usermessage'+no, stringXHTML, stringDOM);

		if (pop == 'y')
				alert( stringXHTML.replace(/<br\/>/g,'\r\n') );  //debug
}



/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */
/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */
/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }
/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test()
{
  return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
}
/*
 * Calculate the MD5 of an array of little-endian words, and a bit length
 */
function core_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;
  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;
  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;
    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);
    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);
    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);
    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);
    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);
}
/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}
/*
 * Calculate the HMAC-MD5, of a key and some data
 */
function core_hmac_md5(key, data)
{
  var bkey = str2binl(key);
  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);
  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }
  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
  return core_md5(opad.concat(hash), 512 + 128);
}
/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}
/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}
/*
 * Convert a string to an array of little-endian words
 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
 */
function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}
/*
 * Convert an array of little-endian words to a string
 */
function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}
/*
 * Convert an array of little-endian words to a hex string.
 */
function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}
/*
 * Convert an array of little-endian words to a base-64 string
 */
function binl2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}
