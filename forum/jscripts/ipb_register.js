//------------------------------------------
// Invision Power Board v2.0
// Register JS File
// (c) 2004 Invision Power Services, Inc.
//
// http://www.invisionboard.com
//------------------------------------------



function Validate()
{
	//------------------------------------------
	// Check for Empty fields
	//------------------------------------------
	
	if (document.REG.UserName.value == "" || document.REG.PassWord.value == "" || document.REG.PassWord_Check.value == "" || document.REG.EmailAddress.value == "")
	{
		alert ( ipb_lang_js_blanks );
		return false;
	}
}

function get_more_info()
{
	var chosenid = document.REG.subspackage.options[document.REG.subspackage.selectedIndex].value;
	
	if ( ! chosenid )
	{
		chosenid = 0;
	}
	
	//------------------------------------------
	// Toggle view...
	//------------------------------------------
	
	eval("document.REG.pkdesc.value=subsdesc_"+chosenid);
	
	toggleview('subspkdiv');
}