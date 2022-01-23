//------------------------------------------
// Invision Power Board v2.0
// PM JS File
// (c) 2003 Invision Power Services, Inc.
//
// http://www.invisionboard.com
//------------------------------------------

//==========================================
// Check All boxes
//==========================================

function CheckAll(fmobj)
{
	for (var i=0;i<fmobj.elements.length;i++)
	{
		var e = fmobj.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox') && (!e.disabled))
		{
			e.checked = fmobj.allbox.checked;
		}
	}
}

//==========================================
// Check all or uncheck all?
//==========================================

function CheckCheckAll(fmobj)
{	
	var TotalBoxes = 0;
	var TotalOn = 0;
	for (var i=0;i<fmobj.elements.length;i++)
	{
		var e = fmobj.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox'))
		{
			TotalBoxes++;
			if (e.checked)
			{
				TotalOn++;
			}
		}
	}
	
	if (TotalBoxes==TotalOn)
	{
		fmobj.allbox.checked=true;
	}
	else
	{
		fmobj.allbox.checked=false;
	}
}

//==========================================
// INBOX FUNCTIONS
//==========================================

var ie  = document.all  ? 1 : 0;
//var ns4 = document.layers ? 1 : 0;

//==========================================
// highlite
//==========================================

function hl(cb)
{
	if (ie)
	{ 
		while (cb.tagName != "TR")
		{
			cb = cb.parentElement;
		}
	}
	else
	{
		 while (cb.tagName != "TD")
		 {
			 cb = cb.parentNode;
		 }
	}
		 
	cb.className = 'row1';
   
}

//==========================================
// down-lite
//==========================================

function dl(cb)
{
   if (ie)
   {
	   while (cb.tagName != "TR")
	   {
		   cb = cb.parentElement;
	   }
   }
   else
   {
	   while (cb.tagName != "TD")
	   {
		   cb = cb.parentNode;
	   }
   }
   cb.className = 'row2';
}

//==========================================
// Boxes checked?
//==========================================

function cca(cb)
{
   if (cb.checked)
   {
	   hl(cb);
   }
   else
   {
	   dl(cb);
   }
}

//==========================================
// Inbox check all
//==========================================

function InboxCheckAll(cb)
{
	var fmobj = document.mutliact;
	for (var i=0;i<fmobj.elements.length;i++)
	{
		var e = fmobj.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox') && (!e.disabled))
		{
			e.checked = fmobj.allbox.checked;
			if (fmobj.allbox.checked)
			{
			   hl(e);
			}
			else
			{
			   dl(e);
			}
		}
	}
}

function select_read()
{	
	var fmobj = document.mutliact;
	for (var i=0;i<fmobj.elements.length;i++)
	{
		var e = fmobj.elements[i];
		if ((e.type=='hidden') && (e.value == 1) && (! isNaN(e.name) ))
		{
			eval("fmobj.msgid_" + e.name + ".checked=true;");
			hl(e);
		}
	}
}

function unselect_all()
{	
	var fmobj = document.mutliact;
	for (var i=0;i<fmobj.elements.length;i++)
	{
		var e = fmobj.elements[i];
		if (e.type=='checkbox')
		{
			e.checked=false;
			dl(e);
		}
	}
}
