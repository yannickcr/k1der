<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:42 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_online {

//===========================================================================
// Page_end
//===========================================================================
function Page_end($show_mem="",$sort_order="",$sort_key="",$links="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- End content Table -->
		<tr>
			<td colspan="4" class="formbuttonrow" align="center" valign="middle">
				<form method="post" action="{$ibforums->base_url}act=Online&amp;CODE=listall">
					<b>{$ibforums->lang['s_by']}&nbsp;</b>
					<select class="forminput" name="sort_key">{$sort_key}</select>
					<select class="forminput" name="show_mem">&nbsp;{$show_mem}</select>
					<select class="forminput" name="sort_order">&nbsp;{$sort_order}</select>
					<input type="submit" value="{$ibforums->lang['s_go']}" class="forminput" />
				</form>
			</td>
		</tr>
	</table>
</div>
<br />
<div>$links</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Page_header
//===========================================================================
function Page_header($links="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language='Javascript' type='text/javascript'>
		<!--
		function PopUp(url, name, width,height,center,resize,scroll,posleft,postop) {
			if (posleft != 0) { x = posleft }
			if (postop  != 0) { y = postop  }
		
			if (!scroll) { scroll = 1 }
			if (!resize) { resize = 1 }
		
			if ((parseInt (navigator.appVersion) >= 4 ) && (center)) {
			  X = (screen.width  - width ) / 2;
			  Y = (screen.height - height) / 2;
			}
			if (scroll != 0) { scroll = 1 }
		
			var Win = window.open( url, name, 'width='+width+',height='+height+',top='+Y+',left='+X+',resizable='+resize+',scrollbars='+scroll+',location=no,directories=no,status=no,menubar=no,toolbar=no');
	     }
		//-->
	</script>
<div align='left'>$links</div>
<br />
<div class="borderwrap">
	<div class="maintitle">&nbsp;&nbsp;{$ibforums->lang['page_title']}</div>
		<table cellspacing="1">
			<tr>
				<th width="30%">{$ibforums->lang['member_name']}</th>
				<th width="30%">{$ibforums->lang['where']}</th>
				<th align="center" width="20%">{$ibforums->lang['time']}</th>
				<th width="10%">&nbsp;</th>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_row
//===========================================================================
function show_row($session="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Entry for {$session['member_id']} -->
			<tr>
				<td class="row1">{$session['member_name']}</td>
				<td class="row1">{$session['where_line']}</td>
				<td class="row1" align="center">{$session['running_time']}</td>
				<td class="row1" align="center">{$session['msg_icon']}</td>
			</tr>
<!-- End of Entry -->
EOF;

//--endhtml--//
return $IPBHTML;
}



}

/*--------------------------------------------------*/
/*<changed bits>

</changed bits>*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>