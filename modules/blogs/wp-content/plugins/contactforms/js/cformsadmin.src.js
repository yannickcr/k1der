window.onload = function()
{
	if ( !document.getElementById('cformsfieldsbox') ) return;
	
	var now = new Date();
	now.setTime(now.getTime() + (365*24*60*60*1000));

	order = "";
	allNodes = getElementsByClassName('dbx-cforms','ul',document.getElementById('cformsfieldsbox'));

	for(i = 0; i < allNodes.length; i++)
			order = order + i + "+|";

	order = order.substring(0,order.length-1);
//	alert(order);

	document.cookie = 'dbx-cforms=cformsfieldsbox:'+order+'; expires='+now.toGMTString()+'; path=/';

	var manager = new dbxManager('cforms');

	var meta = new dbxGroup(
		'cformsfieldsbox', 		// container ID [/-_a-zA-Z0-9/]
		'vertical', 	// orientation ['vertical'|'horizontal']
		'6', 			// drag threshold ['n' pixels]
		'yes',			// restrict drag movement to container axis ['yes'|'no']
		'15', 			// animate re-ordering [frames per transition, or '0' for no effect]
		'no', 			// include open/close toggle buttons ['yes'|'no']
		'', 		// default state ['open'|'closed']
		'', 		// word for "open", as in "open this box"
		'', 		// word for "close", as in "close this box"
		'click-down and drag to move this box', // sentence for "move this box" by mouse
		'', // pattern-match sentence for "(open|close) this box" by mouse
		'use the arrow keys to move this box', // sentence for "move this box" by keyboard
		'',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
		);

	manager.onstatechange = function()
	{
		//the box order and state string for all groups
		//alert( this.state );

		//return value determines whether cookie is set
		document.getElementById('cformswarning').innerHTML = "Please save (Update Settings) your new order of fields.";
		document.mainform.field_order.value = this.state;
		return true;
	};
	
		function getElementsByClassName(strClass, strTag, objContElm){
		strTag = strTag || "*";
	 	objContElm = objContElm || document;
		var objColl = objContElm.getElementsByTagName(strTag);
		if (!objColl.length && strTag == "*" && objContElm.all) objColl = objContElm.all;

		var arr = new Array();
		var delim = strClass.indexOf('|') != -1 ? '|' : ' ';
		var arrClass = strClass.split(delim);

		for (var i = 0, j = objColl.length; i < j; i++) {
			var arrObjClass = objColl[i].className.split(' ');
			if (delim == ' ' && arrClass.length > arrObjClass.length) continue;
			var c = 0;
			comparisonLoop:
			for (var k = 0, l = arrObjClass.length;	k < l; k++) {
				for (var m = 0, n = arrClass.length;	m < n;	m++) {
					if (arrClass[m] == arrObjClass[k]) c++;
					if (( delim == '|' && c == 1) || (delim == ' ' && c == arrClass.length)) {
						arr.push(objColl[i]);
						break comparisonLoop;
					}
				}
			}
		}
		return arr;
	}

};

function newcformsversion() {
	pluginname = 'cforms';
	allNodes = getElementsByClassName('name','*');
	for(i = 0; i < allNodes.length; i++) {
			var regExp=/<\S[^>]*>/g;
	    temp = allNodes[i].innerHTML;
	    if (temp.replace(regExp,'') == pluginname) {
		    allNodes[i].getElementsByTagName('a')[0].parentNode.parentNode.style.background="#FE2E33";
		    allNodes[i].getElementsByTagName('a')[0].innerHTML = 'cforms<br/><small>there is a new update available!</small>';
	  	}
	}
}

function getElementsByClassName(strClass, strTag, objContElm){
	strTag = strTag || "*";
 	objContElm = objContElm || document;
	var objColl = objContElm.getElementsByTagName(strTag);
	if (!objColl.length && strTag == "*" && objContElm.all) objColl = objContElm.all;

	var arr = new Array();
	var delim = strClass.indexOf('|') != -1 ? '|' : ' ';
	var arrClass = strClass.split(delim);

	for (var i = 0, j = objColl.length; i < j; i++) {
		var arrObjClass = objColl[i].className.split(' ');
		if (delim == ' ' && arrClass.length > arrObjClass.length) continue;
		var c = 0;
		comparisonLoop:
		for (var k = 0, l = arrObjClass.length;	k < l; k++) {
			for (var m = 0, n = arrClass.length;	m < n;	m++) {
				if (arrClass[m] == arrObjClass[k]) c++;
				if (( delim == '|' && c == 1) || (delim == ' ' && c == arrClass.length)) {
					arr.push(objColl[i]);
					break comparisonLoop;
				}
			}
		}
	}
	return arr;
}

function checkentry(el) {
  if ( document.getElementById(el).checked == 0 )
	document.getElementById(el).checked = 1;
  else
	document.getElementById(el).checked = 0;
};

function checkonoff(formno,chkName) {
  if ( document.forms[formno].checkflag.value == 0 ) {
    document.forms[formno].checkflag.value =1;
    document.forms[formno].allchktop.checked = 1;
    document.forms[formno].allchkbottom.checked = 1;
    SetChecked (formno,1,chkName);
  }
  else {
    document.forms[formno].checkflag.value =0;
    document.forms[formno].allchktop.checked = 0;
    document.forms[formno].allchkbottom.checked = 0;
    SetChecked (formno,0,chkName);
  }
}

function SetChecked(formno,val,chkName) {
  dml=document.forms[formno];
  len = dml.elements.length;
  var i=0;
  for( i=0 ; i<len ; i++) {
    if (dml.elements[i].name==chkName) {
      dml.elements[i].checked=val;
    }
  }
}

function sort_entries(field) {
	if( document.form.order.value==field ) {
		if ( document.form.orderdir.value=='DESC' ) 
			document.form.orderdir.value='ASC';
		else
			document.form.orderdir.value='DESC';
	}
	document.form.order.value=field;
	document.form.submit();
}

