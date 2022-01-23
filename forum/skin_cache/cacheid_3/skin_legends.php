<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:42 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_legends {

//===========================================================================
// bbcode_header
//===========================================================================
function bbcode_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div>{$ibforums->lang['bbc_intro']}</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// bbcode_row
//===========================================================================
function bbcode_row($before="",$after="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row1" valign="middle">$before</td>
	<td class="row1" valign="middle">$after</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// bbcode_row_footer
//===========================================================================
function bbcode_row_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
	</div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// bbcode_row_header
//===========================================================================
function bbcode_row_header($title="",$desc="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div>
	<div class="borderwrap">
		<div class="maintitle">$title</div>
		<div class="row2">$desc</div>
		<table cellspacing="1">
			<tr>
				<td width="50%" align="center" class="row1" valign="middle">{$ibforums->lang['bbc_before']}</td>
				<td width="50%" align="center" class="row1" valign="middle">{$ibforums->lang['bbc_after']}</td>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// emoticon_javascript
//===========================================================================
function emoticon_javascript() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language='javascript'>
<!--
	function add_smilie(code)
	{
		opener.document.REPLIER.Post.value += ' ' + code + ' ';
		//return true;
	}
//-->
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// emoticons_row
//===========================================================================
function emoticons_row($code="",$image="",$in="'",$out="'") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td align="center" class="row1" valign="middle"><a href={$out}javascript:add_smilie({$in}$code{$in}){$out}>$code</a></td>
	<td align="center" class="row1" valign="middle"><a href={$out}javascript:add_smilie({$in}$code{$in}){$out}><img src="{$ibforums->vars['EMOTICONS_URL']}/$image" valign="absmiddle" alt="$image" /></a></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// find_user_error
//===========================================================================
function find_user_error($msg="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form name="finduser" method="post" action="{$ibforums->base_url}entry=$entry&amp;name=$name&amp;sep=$sep&amp;CODE=finduser_two">
	<table cellspacing="1">
		<tr>
			<td class="pagetitle">{$ibforums->lang['fu_error']}<hr noshade></td>
		</tr>
		<tr>
			<td align="center" valign="middle">$msg</td>
		</tr>
		<tr>
			<td class="pcen"><a href="javascript:history.go(-1);">{$ibforums->lang['fu_back']}</a> :: <a href="javascript:window.close();">{$ibforums->lang['fu_close_win']}</a></td>
		</tr>
	</table>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// find_user_final
//===========================================================================
function find_user_final($names="",$entry="",$name="",$sep="line") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script type="text/javascript">
<!--
var separator = '$sep';
var entry     = '$entry';
var in_name   = '$name';
//-->
</script>
<script type="text/javascript" src="jscripts/ipb_finduser.js"></script>
<form name="finduser">
	<table cellspacing="1">
		<tr>
			<td class="pagetitle">{$ibforums->lang['fu_title']}<hr noshade></td>
		</tr>
		<tr>
			<td align="center" valign="middle">{$ibforums->lang['fu_add_desc']}<br /><br /><select name="username" class="forminput">$names</select><br /><br /><input type="button" name="add" onClick="add_to_form()" value="{$ibforums->lang['fu_add_mem']}"></td>
		</tr>
		<tr>
			<td class="pcen"><a href="javascript:history.go(-1);">{$ibforums->lang['fu_back']}</a> :: <a href="javascript:window.close();">{$ibforums->lang['fu_close_win']}</a></td>
		</tr>
	</table>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// find_user_one
//===========================================================================
function find_user_one($entry="",$name="",$sep="comma") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form name="finduser" method="post" action="{$ibforums->base_url}act=legends&amp;entry=$entry&amp;name=$name&amp;sep=$sep&amp;CODE=finduser_two">
	<table cellspacing="1">
		<tr>
			<td class="pagetitle">{$ibforums->lang['fu_title']}<hr noshade></td>
		</tr>
		<tr>
			<td align="center" valign="middle"><b>{$ibforums->lang['fu_enter_name']}</b><br /><br /><input type="text" size="40" name="username" class="forminput" /><br /><br /><input type="submit" value="{$ibforums->lang['fu_search_but']}" /></td>
		</tr>
		<tr>
			<td class="pcen"><a href="javascript:window.close();">{$ibforums->lang['fu_close_win']}</a></td>
		</tr>
	</table>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// page_footer
//===========================================================================
function page_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
	</div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// page_header
//===========================================================================
function page_header($title="",$row1="",$row2="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div>
	<div class="borderwrap">
		<div class="maintitle">$title</div>
		<table cellspacing="1">
			<tr>
				<td width="50%" align="center" class="formsubtitle" valign="middle">$row1</td>
				<td width="50%" align="center" class="formsubtitle" valign="middle">$row2</td>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// wrap_tag
//===========================================================================
function wrap_tag($tag="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div><b>$tag<b></div>
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