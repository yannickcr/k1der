//------------------------------------------
// Invision Power Board v2.0
// Find User JS File
// (c) 2004 Invision Power Services, Inc.
//
// http://www.invisionboard.com
//------------------------------------------



function add_to_form()
{
	var name = document.finduser.username.options[document.finduser.username.selectedIndex].value;
	
	if (separator == 'line')
	{
		separator = '\n';
	}
	
	if (separator == 'none')
	{
		separator = '';
	}
	
	if (entry == 'textarea')
	{
		// Where shall we put the separator?
		
		var tbox     = eval('opener.document.REPLIER.'+in_name+'.value');
		var tboxSize = eval('opener.document.REPLIER.'+in_name+'.value.length');
		
		// Remove leading spaces...
		
		while ( tbox.slice(0,1) == " " )
		{
			tbox        = tbox.substr(1, tboxSize - 1);
			tboxSize = tboxSize;
		}
		
		// Remove trailing spaces...
		
		while ( tbox.slice(tboxSize - 1, tboxSize) == " " )
		{
			tbox = tbox.substr(0, tboxSize - 1);
			tboxSize = tboxSize;
		}
		
		// Do we have a leading comma?
		
		while ( tbox.slice(0,1) == "\n" )
		{
			tbox = tbox.substr(1, tboxSize - 1);
			tboxSize = tboxSize;
		}
		
		// Do we have a trailing comma?...
		
		while ( tbox.slice(tboxSize - 1, tboxSize) == "\n" )
		{
			tbox = tbox.substr(0, tboxSize - 1);
			tboxSize = tboxSize;
		}
		
		// First in box?
		
		if ( tboxSize == 0)
		{
			eval('opener.document.REPLIER.'+in_name+'.value += name');
		}
		else
		{
			eval('opener.document.REPLIER.'+in_name+'.value += separator + name');
		}
	}
}