<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 5                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:50 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_buddy {

//===========================================================================
// append_view
//===========================================================================
function append_view($url="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
( <b><a href="javascript:redirect_to('$url', 0)">{$ibforums->lang['view_link']}</a></b> )
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// buddy_js
//===========================================================================
function buddy_js() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language='javascript' type="text/javascript">
<!--
 function redirect_to(where, closewin)
 {
 	opener.location= '{$ibforums->base_url}' + where;
 	
 	if (closewin == 1)
 	{
 		self.close();
 	}
 }
 
 function check_form(helpform)
 {
 	opener.name = "ibfmain";
 
 	if (helpform == 1) {
 		document.theForm2.target = 'ibfmain';
 	} else {
 		document.theForm.target = 'ibfmain';
 	}
 	
 	return true;
 }
 
 function shrink()
 {
 	window.resizeTo('200','75');
 }
 
 function expand()
 {
 	window.resizeTo('200','450');
 }
 
 
 //-->
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// build_away_msg
//===========================================================================
function build_away_msg() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
{$ibforums->lang['new_posts']}
<br />
{$ibforums->lang['my_replies']}
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// closelink
//===========================================================================
function closelink() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<p><a href="javascript:window.location=window.location;">{$ibforums->lang['refresh']}</a> / <a href="javascript:self.close();">{$ibforums->lang['close_win']}</a></p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// login
//===========================================================================
function login() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}act=Login&amp;CODE=01&amp;CookieDate=1&amp;buddy=1" method="post" name="theForm" onSubmit="return check_form();">
	<div class="borderwrap">
		<h3>{$ibforums->lang['page_title']}</h3>
		<div class="formsubtitle" align="center">{$ibforums->lang['log_in_needed']}</div>
			<p>{$ibforums->lang['no_guests']}</p>
			<div class="formsubtitle" align="center">{$ibforums->lang['log_in']}</div>
			<div class="fieldwrap">
				<h4>{$ibforums->lang['lin_name']}</h4>
				<input type="text" name="UserName" />
				<h4>{$ibforums->lang['lin_pass']}</h4>
				<input type="password" name="PassWord" />
				<input type="submit" value="{$ibforums->lang['log_in']}" class="button" />
			</div>
			<p>{$ibforums->lang['reg_text']}<br /><a href="javascript:redirect_to('&amp;act=Reg', 1);">{$ibforums->lang['reg_link']}</a></p>
	<div align="center"><!--CLOSE.LINK--></div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// main
//===========================================================================
function main($away_text="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<h3>{$ibforums->lang['page_title']}</h3>
	<div class="formsubtitle">{$ibforums->lang['while_away']}</div>
	<p>{$away_text}</p>
	<div class="formsubtitle">{$ibforums->lang['show_me']}</div>
	<p><a href="javascript:redirect_to('&amp;act=Stats&amp;CODE=leaders',0)">{$ibforums->lang['sm_forum_leaders']}</a><br /><a href="javascript:redirect_to('&amp;act=Stats',0)">{$ibforums->lang['sm_today_posters']}</a><br /><a href="javascript:redirect_to('&amp;act=Members&amp;max_results=10&amp;sort_key=posts&amp;sort_order=desc',0)">{$ibforums->lang['sm_all_posters']}</a><br /><a href="javascript:redirect_to('&amp;act=Search&amp;CODE=lastten',0)">{$ibforums->lang['sm_my_last_posts']}</a></p>
	<div class="formsubtitle" align="center">{$ibforums->lang['search_forums']}</div>
	<div align="center">
		<form action="{$ibforums->base_url}act=Search&amp;CODE=01&amp;forums=all&amp;cat_forum=forum&amp;joinname=1&amp;search_in=posts&amp;result_type=topics" method="post" name="theForm" onsubmit="return check_form();">
			<input type="text" size="17" name="keywords" />&nbsp;<input class="gobutton" type="image" src="{$ibforums->vars['img_url']}/login-button.gif" value="{$ibforums->lang['go']}" />
		</form>
	</div>
	<div class="formsubtitle" align="center">{$ibforums->lang['search_help']}</div>
	<div align="center">
		<form action="{$ibforums->base_url}act=Help&amp;CODE=02" method="post" name="theForm2" onsubmit="return check_form(1);">
			<input type="text" size="17" name="search_q" />&nbsp;<input class="gobutton" type="image" src="{$ibforums->vars['img_url']}/login-button.gif" value="{$ibforums->lang['go']}" />
		</form>
	</div>
	<div><!--CLOSE.LINK--></div>
</div>
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