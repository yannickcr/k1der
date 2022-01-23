//--------------------------------------------
// Set up our simple tag open values
//--------------------------------------------

var B_open = 0;
var I_open = 0;
var U_open = 0;
var QUOTE_open = 0;
var CODE_open = 0;
var SQL_open = 0;
var HTML_open = 0;

var bbtags   = new Array();

var fombj    = document.REPLIER;

//==========================================
// prep and set up
//==========================================

prep_mode();

function prep_mode()
{
	cvalue = my_getcookie( "bbmode" );
	
	if (cvalue == 'ezmode')
	{
		fombj.bbmode[0].checked = true;
	}
	else
	{
		fombj.bbmode[1].checked = true;
	}
}

//==========================================
// Set BBCode mode
//==========================================

function setmode(mVal)
{
	my_setcookie( 'bbmode', mVal, 1 );
}


function get_easy_mode_state()
{
	//--------------------------------------------
	// Returns true if we've chosen easy mode
	//--------------------------------------------
	
	if ( fombj.bbmode[0].checked )
	{
		return true;
	}
	else
	{
		return false;
	}
}

//==========================================
// Set the help bar status
//==========================================

function hstat(msg)
{
	fombj.helpbox.value = eval( "help_" + msg );
}

//==========================================
// Set the number of tags open box
//==========================================

function cstat()
{
	var c = stacksize(bbtags);
	
	if ( (c < 1) || (c == null) ) {
		c = 0;
	}
	
	if ( ! bbtags[0] ) {
		c = 0;
	}
	
	fombj.tagcount.value = c;
}


//==========================================
// Close all tags
//==========================================

function closeall()
{
	if (bbtags[0])
	{
		while (bbtags[0])
		{
			tagRemove = popstack(bbtags)
			fombj.Post.value += "[/" + tagRemove + "]";
			
			//--------------------------------------------
			// Change the button status
			// Ensure we're not looking for FONT, SIZE or COLOR as these
			// buttons don't exist, they are select lists instead.
			//--------------------------------------------
			
			if ( (tagRemove != 'FONT') && (tagRemove != 'SIZE') && (tagRemove != 'COLOR') )
			{
				eval("fombj." + tagRemove + ".value = ' " + tagRemove + " '");
				eval(tagRemove + "_open = 0");
			}
		}
	}
	
	//--------------------------------------------
	// Ensure we got them all
	//--------------------------------------------
	
	fombj.tagcount.value = 0;
	bbtags = new Array();
	fombj.Post.focus();
}

//==========================================
// EMOTICONS
//==========================================

function emoticon(theSmilie)
{
	doInsert(" " + theSmilie + " ", "", false);
}

//==========================================
// ADD CODE
//==========================================

function add_code(NewCode)
{
    fombj.Post.value += NewCode;
    fombj.Post.focus();
}

//==========================================
// ALTER FONT
//==========================================

function alterfont(theval, thetag)
{
    if (theval == 0)
    	return;
	
	if(doInsert("[" + thetag + "=" + theval + "]", "[/" + thetag + "]", true))
		pushstack(bbtags, thetag);
	
    fombj.ffont.selectedIndex  = 0;
    fombj.fsize.selectedIndex  = 0;
    fombj.fcolor.selectedIndex = 0;
    
    cstat();
	
}


//==========================================
// SIMPLE TAGS (such as B, I U, etc)
//==========================================

function simpletag(thetag)
{
	var tagOpen = eval(thetag + "_open");
	
	if ( get_easy_mode_state() )
	{
		inserttext = prompt(prompt_start + "\n[" + thetag + "]xxx[/" + thetag + "]");
		if ( (inserttext != null) && (inserttext != "") )
		{
			doInsert("[" + thetag + "]" + inserttext + "[/" + thetag + "] ", "", false);
		}
	}
	else
	{
		if (tagOpen == 0)
		{
			if(doInsert("[" + thetag + "]", "[/" + thetag + "]", true))
			{
				eval(thetag + "_open = 1");
				
				//--------------------------------------------
				// Change the button status
				//--------------------------------------------
				
				eval("fombj." + thetag + ".value += '*'");
		
				pushstack(bbtags, thetag);
				cstat();
				hstat('click_close');
			}
		}
		else
		{
			//--------------------------------------------
			// Find the last occurance of the opened tag
			//--------------------------------------------
			lastindex = 0;
			
			for (i = 0 ; i < bbtags.length; i++ )
			{
				if ( bbtags[i] == thetag )
				{
					lastindex = i;
				}
			}
			
			//--------------------------------------------
			// Close all tags opened up to that tag was opened
			//--------------------------------------------
			
			while (bbtags[lastindex])
			{
				tagRemove = popstack(bbtags);
				doInsert("[/" + tagRemove + "]", "", false)
				
				//--------------------------------------------
				// Change the button status
				//--------------------------------------------
				
				if ( (tagRemove != 'FONT') && (tagRemove != 'SIZE') && (tagRemove != 'COLOR') )
				{
					eval("fombj." + tagRemove + ".value = ' " + tagRemove + " '");
					eval(tagRemove + "_open = 0");
				}
			}
			
			cstat();
		}
	}
}

//==========================================
// List tag
//==========================================

function tag_list()
{
	var listvalue = "init";
	var thelist = "";
	
	while ( (listvalue != "") && (listvalue != null) )
	{
		listvalue = prompt(list_prompt, "");
		if ( (listvalue != "") && (listvalue != null) )
		{
			thelist = thelist+"[*]"+listvalue+"\n";
		}
	}
	
	if ( thelist != "" )
	{
		doInsert( "[LIST]\n" + thelist + "[/LIST]\n", "", false);
	}
}

//==========================================
// URL tag
//==========================================

function tag_url()
{
    var FoundErrors = '';
    var enterURL   = prompt(text_enter_url, "http://");
    var enterTITLE = prompt(text_enter_url_name, "My Webpage");

    if (!enterURL) {
        FoundErrors += " " + error_no_url;
    }
    if (!enterTITLE) {
        FoundErrors += " " + error_no_title;
    }

    if (FoundErrors) {
        alert("Error!"+FoundErrors);
        return;
    }

	doInsert("[URL="+enterURL+"]"+enterTITLE+"[/URL]", "", false);
}

//==========================================
// Insert attachment tag
//==========================================

function insert_attach_to_textarea(aid)
{
	doInsert( "[attachmentid="+aid+"]" );
}

//==========================================
// Image tag
//==========================================

function tag_image()
{
    var FoundErrors = '';
    var enterURL   = prompt(text_enter_image, "http://");

    if (!enterURL) {
        FoundErrors += " " + error_no_url;
    }

    if (FoundErrors) {
        alert("Error!"+FoundErrors);
        return;
    }

	doInsert("[IMG]"+enterURL+"[/IMG]", "", false);
}

function tag_email()
{
    var emailAddress = prompt(text_enter_email, "");

    if (!emailAddress) { 
		alert(error_no_email); 
		return; 
	}

	doInsert("[EMAIL]"+emailAddress+"[/EMAIL]", "", false);
}

//--------------------------------------------
// GENERAL INSERT FUNCTION
//--------------------------------------------
// ibTag: opening tag
// ibClsTag: closing tag, used if we have selected text
// isSingle: true if we do not close the tag right now
// return value: true if the tag needs to be closed later

//

function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = fombj.Post;
	
	//----------------------------------------
	// It's IE!
	//----------------------------------------
	if ( (ua_vers >= 4) && is_ie && is_win)
	{
		if (obj_ta.isTextEdit)
		{
			obj_ta.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if(ibClsTag != "" && rng.text.length > 0)
					ibTag += rng.text + ibClsTag;
				else if(isSingle)
					isClose = true;
	
				rng.text = ibTag;
			}
		}
		else
		{
			if(isSingle)
			{
				isClose = true;
			}
			
			obj_ta.value += ibTag;
		}
	}
	//----------------------------------------
	// It's MOZZY!
	//----------------------------------------
	
	else if ( obj_ta.selectionEnd )
	{ 
		var ss = obj_ta.selectionStart;
		var st = obj_ta.scrollTop;
		var es = obj_ta.selectionEnd;
		
		if (es <= 2)
		{
			es = obj_ta.textLength;
		}
		
		var start  = (obj_ta.value).substring(0, ss);
		var middle = (obj_ta.value).substring(ss, es);
		var end    = (obj_ta.value).substring(es, obj_ta.textLength);
		
		//-----------------------------------
		// text range?
		//-----------------------------------
		
		if (obj_ta.selectionEnd - obj_ta.selectionStart > 0)
		{
			middle = ibTag + middle + ibClsTag;
		}
		else
		{
			middle = ibTag + middle;
			
			if (isSingle)
			{
				isClose = true;
			}
		}
		
		obj_ta.value = start + middle + end;
		
		var cpos = ss + (middle.length);
		
		obj_ta.selectionStart = cpos;
		obj_ta.selectionEnd   = cpos;
		obj_ta.scrollTop      = st;


	}
	//----------------------------------------
	// It's CRAPPY!
	//----------------------------------------
	else
	{
		if (isSingle)
		{
			isClose = true;
		}
		
		obj_ta.value += ibTag;
	}
	
	obj_ta.focus();

	return isClose;
}	
