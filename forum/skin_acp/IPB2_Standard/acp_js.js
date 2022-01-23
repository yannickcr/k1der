//------------------------------------------
// Invision Power Board v2.0
// ACP Specific JS File
// (c) 2003 Invision Power Services, Inc.
//
// http://www.invisionboard.com
//------------------------------------------

//==========================================
// POP UP YA WINDA
//==========================================


function pop_win(theUrl, winName, theWidth, theHeight)
{
	if (winName == '') { winName = 'Preview'; }
	if (theHeight == '') { theHeight = 400; }
	if (theWidth == '') { theWidth = 400; }
	
	window.open(ipb_var_base_url+'&'+theUrl,winName,'width='+theWidth+',height='+theHeight+',resizable=yes,scrollbars=yes');
}

//==========================================
// Toggle div
//==========================================

function togglediv( did, show )
{
	//-----------------------------------
	// Add?
	//-----------------------------------
	
	if ( show )
	{
		my_show_div( my_getbyid( did ) );
		
	}
	else
	{
		my_hide_div( my_getbyid( did ) );
	}
	
	return false;
	
}


//==========================================
// Toggle menu categories
//==========================================

function togglemenucategory( fid, add )
{
	saved = new Array();
	clean = new Array();

	//-----------------------------------
	// Get any saved info
	//-----------------------------------
	
	if ( tmp = my_getcookie('acpcollapseprefs') )
	{
		saved = tmp.split(",");
	}
	
	//-----------------------------------
	// Remove bit if exists
	//-----------------------------------
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != fid && saved[i] != "" )
		{
			clean[clean.length] = saved[i];
		}
	}
	
	//-----------------------------------
	// Add?
	//-----------------------------------
	
	if ( add )
	{
		clean[ clean.length ] = fid;
		my_show_div( my_getbyid( 'fo_'+fid  ) );
		my_hide_div( my_getbyid( 'fc_'+fid  ) );
	}
	else
	{
		my_show_div( my_getbyid( 'fc_'+fid  ) );
		my_hide_div( my_getbyid( 'fo_'+fid  ) );
	}
	
	my_setcookie( 'acpcollapseprefs', clean.join(','), 1 );
	
	tmp = clean.join(',');
}

//==========================================
// Expand all (remove cookie)
//==========================================

function expandmenu()
{
	my_setcookie( 'acpcollapseprefs', menu_ids, 1 );
	window.location=window.location;
}

//==========================================
// Expand all (remove cookie)
//==========================================

function collapsemenu()
{
	my_setcookie( 'acpcollapseprefs', '', 1 );
	window.location=window.location;
}

//==========================================
// Change text editor size
//==========================================

function changefont()
{
	savearray   = new Array();
	idarray     = new Array();
	
	if ( template_bit_ids )
	{
		idarray  = template_bit_ids.split(",");
	}
	
	if ( tmp = my_getcookie('acpeditorprefs') )
	{
		savearray = tmp.split(",");
	}
	
	chosenfont  = document.theform.fontchange.options[document.theform.fontchange.selectedIndex].value;
	chosensize  = document.theform.sizechange.options[document.theform.sizechange.selectedIndex].value;
	chosenback  = document.theform.backchange.options[document.theform.backchange.selectedIndex].value;
	fontcolor   = document.theform.fontcolor.options[document.theform.fontcolor.selectedIndex].value;
	widthchange = document.theform.widthchange.options[document.theform.widthchange.selectedIndex].value;
	highchange  = document.theform.highchange.options[document.theform.highchange.selectedIndex].value;
	
	if ( idarray.length )
	{
		for (i = 0 ; i < idarray.length; i++ )
		{
			id = idarray[i];
				
			itm = my_getbyid(id);
			
			if ( chosenfont != '-' )
			{
				itm.style.fontFamily = chosenfont;
				savearray[0]         = chosenfont;
			}
			if ( chosensize != '-' )
			{
				itm.style.fontSize   = chosensize;
				savearray[1]         = chosensize;
			}
			if ( chosenback != '-' )
			{
				itm.style.backgroundColor = chosenback;
				savearray[2]              = chosenback;
			}
			if ( fontcolor != '-' )
			{
				itm.style.color = fontcolor;
				savearray[3]    = fontcolor;
			}
			if ( widthchange != '-' )
			{
				itm.style.width = widthchange;
				savearray[4]    = widthchange;
			}
			if ( highchange != '-' )
			{
				itm.style.height = highchange;
				savearray[5]     = highchange;
			}
		}
	}
	
	my_setcookie( 'acpeditorprefs', savearray.join(','), 1 );
}

//==========================================
// Auto jump menu
//==========================================

function autojumpmenu(fobj)
{
	urljump = fobj.options[fobj.selectedIndex].value;
	
	if ( urljump != "" && urljump != "-" )
	{
		window.location = urljump;
	}
}

function maincheckdelete(url, msg)
{
	if ( ! msg )
	{
		msg = 'PLEASE CONFIRM:\nOK to proceed with delete?';
	}
	
	if (confirm( msg ))
	{
		window.location.href = url;
	}
	else
	{
		alert ( 'OK, action cancelled!' );
	} 
}


