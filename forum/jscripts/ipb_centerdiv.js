resetposition = function()
{
	if ( document.getElementById )
	{
		var div = document.getElementById( divToCenter );
		var divWidth  = div.offsetWidth  ? div.offsetWidth  : div.style.width   ? parseInt( div.style.width )  : 0;
		var divHeight = div.offsetHeight ? div.offsetHeight :  div.style.height ? parseInt( div.style.height ) : 0;
		
		var sx = getwidth();
		var sy = getheight();
		
		var scrolly = getYscroll();
		
		var setX = ( sx - divWidth ) / 2;
		var setY = ( sy - divHeight) / 2 + scrolly;
		
		if( setX < 0 ) setX = 0;
		if( setY < 0 ) setY = 0;
		
		div.style.left = setX + "px";
		div.style.top  = setY + "px";
		div.style.visibility = "visible";
	}
};


function getwidth()
{
	var width = 0;
	
	if( document.documentElement && document.documentElement.clientWidth )
	{
		width = document.documentElement.clientWidth;
	}
	else if( document.body && document.body.clientWidth )
	{
		width = document.body.clientWidth;
	}
	else if( window.innerWidth )
	{
		width = window.innerWidth - 18;
	}
	
	return width;
}

function getheight()
{
	var height = 0;
	if( document.documentElement && document.documentElement.clientHeight )
	{
		height = document.documentElement.clientHeight;
	}
	else if( document.body && document.body.clientHeight )
	{
		height = document.body.clientHeight;
	}
	else if( window.innerHeight )
	{
		height = window.innerHeight - 18;
	}
	
	return height;
}

function getXscroll()
{
	var scrollX = 0;
	if( document.documentElement && document.documentElement.scrollLeft )
	{
		scrollX = document.documentElement.scrollLeft;
	}
	else if( document.body && document.body.scrollLeft )
	{
		scrollX = document.body.scrollLeft;
	}
	else if( window.pageXOffset )
	{
		scrollX = window.pageXOffset;
	}
	else if( window.scrollX )
	{
		scrollX = window.scrollX;
	}
	return scrollX;
}

function getYscroll()
{
	var scrollY = 0;
	if( document.documentElement && document.documentElement.scrollTop )
	{
		scrollY = document.documentElement.scrollTop;
	}
	else if( document.body && document.body.scrollTop )
	{
		scrollY = document.body.scrollTop;
	}
	else if( window.pageYOffset )
	{
		scrollY = window.pageYOffset;
	}
	else if( window.scrollY )
	{
		scrollY = window.scrollY;
	}
	return scrollY;
}