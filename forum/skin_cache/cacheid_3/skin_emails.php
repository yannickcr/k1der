<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:42 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_emails {

//===========================================================================
// aol_body
//===========================================================================
function aol_body($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Begin AIM Remote -->
<table cellspacing="0" class="aoltable">
	<tr align="right">
		<td>
			<a href="http://www.aol.co.uk/aim/index.html"><img src="http://www.aol.co.uk/aim/remote/gr/aimver_man.gif" width="44" height="55"  alt="Download AIM" /></a>
			<img src="http://www.aol.co.uk/aim/remote/gr/aimver_topsm.gif" width="73" height="55" alt="AIM Remote" />
			<br />
			<a href="aim:goim?screenname={$data['AOLNAME']}&amp;message=Hi.+Are+you+there?"><img src="http://www.aol.co.uk/aim/remote/gr/aimver_im.gif" width="117" height="39" alt="Send me an Instant Message" /></a>
			<br />
			<a href="aim:addbuddy?screenname={$data['AOLNAME']}"><img src="http://www.aol.co.uk/aim/remote/gr/aimver_bud.gif" width="117" height="39" alt="Add me to Your Buddy List" /></a>
			<br />
			<a href="http://www.aol.co.uk/aim/remote.html"><img src="http://www.aol.co.uk/aim/remote/gr/aimver_botadd.gif" width="117" height="23" alt="Add Remote to Your Page" /></a>
			<br />
			<a href="http://www.aol.co.uk/aim/index.html"><img src="http://www.aol.co.uk/aim/remote/gr/aimver_botdow.gif" width="117" height="29" alt="Download AOL Instant Messenger" /></a>
			<br /><br />
		</td>
	</tr>
</table>
<!-- End AIM Remote -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// board_rules
//===========================================================================
function board_rules($title="",$body="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">$title</div>
	<div class="tablepad">$body</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// chat04_inline
//===========================================================================
function chat04_inline($server="",$group="",$room="",$w="",$h="",$lang="",$textmode="",$style="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['chat_title']}</div>
	<div class="tablepad" align="center">
	 <div style='border:8px outset #BCD0ED;width:{$w}px;'>
		<applet codebase="$server" archive="pchat.zip" code="ParaChat.class" width="$w" height="$h"> 
			<param name=cabbase value="pchat.cab"> 
			<param name=roam.Group value="$group"> 
			<param name=Channel value="$room">
			<param name="ctrl.Language" value="$lang">
			<param name="ui.ChatWindow" value="$textmode">
			<param name="ui.BgColor" value="{$style['applet_bg']}">
			<param name="ui.FgColor" value="{$style['applet_fg']}">
			<param name="cmd.ChatBg" value="{$style['window_bg']}">
			<param name="cmd.ChatFg" value="{$style['window_fg']}">
			<param name="cmd.FontSize" value="{$style['font_size']}">
			<!--CUSTOMPARAM-->
			<!--AUTOLOGIN-->
			Sorry, your browser is not Java enabled, please visit <a href="http://chathelp.invisionsitetools.com/java.html">our java support pages</a>
		</applet>
	  </div>
	</div>
</div>
<br />
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['chat_help']}</div>
	<div class="tablepad">
		{$ibforums->lang['chat_help_text']}
	</div>
</div>
<iframe src='{$ibforums->base_url}act=chat&CODE=update' width='1' height='1' marginwidth='0' marginheight='0' frameborder='0' name='chatimg' />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// chat04_pop
//===========================================================================
function chat04_pop($server="",$group="",$room="",$w="",$h="",$lang="",$textmode="",$style="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div align="center">
	<applet codebase="$server" archive="pchat.zip" code="ParaChat.class" width="$w" height="$h"> 
		<param name=cabbase value="pchat.cab"> 
		<param name=roam.Group value="$group"> 
		<param name=Channel value="$room">
		<param name="ctrl.Language" value="$lang">
		<param name="ui.ChatWindow" value="$textmode">
		<param name="ui.BgColor" value="{$style['applet_bg']}">
		<param name="ui.FgColor" value="{$style['applet_fg']}">
		<param name="cmd.ChatBg" value="{$style['window_bg']}">
		<param name="cmd.ChatFg" value="{$style['window_fg']}">
		<param name="cmd.FontSize" value="{$style['font_size']}">
		<!--CUSTOMPARAM-->
		<!--AUTOLOGIN-->
		Sorry, your browser is not Java enabled, please visit <a href="http://chathelp.invisionsitetools.com/java.html">our java support pages</a>
	</applet>
  </div>
  <iframe src='{$ibforums->base_url}act=chat&CODE=update' width='1' height='1' marginwidth='0' marginheight='0' frameborder='0' name='chatimg' />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// chat_inline
//===========================================================================
function chat_inline($server="",$acc_no="",$lang="",$w="",$h="",$user="",$pass="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['chat_title']}</div>
	<div class="tablepad" align="center">
		<applet codebase="http://{$server}/current/" code="Client.class" archive="scclient_$lang.zip" width="$w" height="$h">
			<param name="room" value="$acc_no">
			<param name="cabbase" value="scclient_$lang.cab">
			<param name="username" value="$user">
			<param name="password" value="$pass">
			<param name="autologin" value="yes">
		</applet>
	</div>
</div>
<br />
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['chat_help']}</div>
	<div class="tablepad">
		{$ibforums->lang['chat_help_text']}
	</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// chat_pop
//===========================================================================
function chat_pop($server="",$acc_no="",$lang="",$w="",$h="",$user="",$pass="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div align="center">
	<applet codebase="http://{$server}/current/" code="Client.class" archive="scclient_$lang.zip" width="$w" height="$h">
		<param name="room" value="$acc_no">
		<param name="cabbase" value="scclient_$lang.cab">
		<param name="username" value="$user">
		<param name="password" value="$pass">
		<param name="autologin" value="yes">
	</applet>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_table
//===========================================================================
function end_table() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- End content Table -->
			</table>
		</td>
	</tr>
	<tr>
		<td class="darkrow1" colspan="2">&nbsp;</td>
	</tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// errors
//===========================================================================
function errors($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['errors_found']}</div>
	<div class="tablepad"><span class="postcolor">$data</span></div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_jump
//===========================================================================
function forum_jump($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="1">
	<tr>
		<td align="right">$data</td>
	</tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forward_form
//===========================================================================
function forward_form($title="",$text="",$lang="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post" name='REPLIER'>
	<input type="hidden" name="act"  value="Forward" />
	<input type="hidden" name="CODE" value="01" />
	<input type="hidden" name="s"    value="{$ibforums->session_id}" />
	<input type="hidden" name="st"   value="{$ibforums->input['st']}" />
	<input type="hidden" name="f"    value="{$ibforums->input['f']}" />
	<input type="hidden" name="t"    value="{$ibforums->input['t']}" />
	<div class="borderwrap">
		<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['title']}</div>
		<table cellspacing="0">
			<tr>
				<td class="row1"  width="30%" valign="top"><b>{$ibforums->lang['send_lang']}</b></td>
				<td class="row1" width="80%">$lang</td>
			</tr>
			<tr>
				<td class="row1"  width="30%" valign="top"><b>{$ibforums->lang['to_name']}</b></td>
				<td class="row1" width="80%"><input type="text" name="to_name" value="" size="30" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="row1"  width="30%" valign="top"><b>{$ibforums->lang['to_email']}</b></td>
				<td class="row1" width="80%"><input type="text" name="to_email" value="" size="30" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="row1"  width="30%" valign="top"><b>{$ibforums->lang['subject']}</b></td>
				<td class="row1" width="80%"><input type="text" name="subject" value="{$title}" size="30" maxlength="120" /></td>
			</tr>
			<tr>
				<td class="row1"  width="30%" valign="top"><b>{$ibforums->lang['message']}</b></td>
				<td class="row1" width="80%"><textarea cols="60" rows="12" wrap="soft" name="message" class="textinput">{$text}</textarea>
				</td>
			</tr>
			<tr>
				<td class="formbuttonrow" colspan="2"><input type="submit" value="{$ibforums->lang['submit_send']}" /></td>
			</tr>
		</table>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// icq_body
//===========================================================================
function icq_body($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="http://msg.mirabilis.com/scripts/WWPMsg.dll" method="POST" name="frmPager">
	<input type="hidden" name="subject" value="From WebPager Panel" />
	<input type="hidden" name="to" value="{$data[UIN]}" />
	<tr>
		<td class="row1"><b>{$ibforums->lang['name']}</b></td>
		<td class="row1"><input type="text" name="from" value="{$ibforums->member['name']}" size="40" onMouseOver="this.focus()" onFocus="this.select()" /></td>
	</tr>
	<tr>
		<td class="row1"><b>{$ibforums->lang['email']}</b></td>
		<td class="row1"><input type="text" name="fromemail" value="{$ibforums->member['email']}" size="40" onMouseOver="this.focus()" onFocus="this.select()" /></td>
	</tr>
	<tr>
		<td class="row1" valign="top"><b>{$ibforums->lang['msg']}</b></td>
		<td class="row1"><textarea wrap="virtual" cols="50" rows="12" wrap="soft" name="body" class="textinput" onMouseOver="this.focus()" onFocus="this.select()"></textarea></td>
	</tr>
	<tr>
		<td class="row1" align="center" colspan="2"><input type="submit" value="{$ibforums->lang['submit']}" /></td>
	</tr>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// msn_body
//===========================================================================
function msn_body($msnname="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<object classid="clsid:F3A614DC-ABE0-11d2-A441-00C04F795683" codebase="#Version=2,0,0,83" codetype="application/x-oleobject" id="MsgrObj" name="MsgrApp" width="0" height="0"></object>
<object classid="clsid:FB7199AB-79BF-11d2-8D94-0000F875C541" codetype="application/x-oleobject" id="MsgrApp" name="MsgrApp" width="0" height="0"></object>
<tr>
	<td class="row1"><b>{$ibforums->lang['msn_name']}</b></td>
	<td class="row1"><input type="text" name="msnname" value="$msnname" size="40" onMouseOver="this.focus()" onFocus="this.select()" /></td>
</tr>
<tr>
	<td class="row1" align="center" colspan="2"><a href="javascript:MsgrApp.LaunchIMUI('$msnname');">{$ibforums->lang['msn_send_msg']}</a></td>
</tr>
<tr>
	<td class="row1" align="center" colspan="2"><a href="javascript:MsgrApp.LaunchAddContactUI('$msnname');">{$ibforums->lang['msn_add_contact']}</a></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pager_header
//===========================================================================
function pager_header($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="0">
	<tr>
		<td>
			<table cellspacing="0">
				<tr>
					<th colspan="2" align="center">{$data[TITLE]}</th>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// report_form
//===========================================================================
function report_form($tid="",$pid="",$st="",$topic_title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}act=report&amp;send=1&amp;t=$tid&amp;p=$pid&amp;st=$st" method="post" name="REPLIER">
	<div class="borderwrap">
		<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['report_title']}</div>
		<table cellspacing="1">
			<tr>
				<td class="row1"  width="30%" valign="top"><b>{$ibforums->lang['report_topic']}</b></td>
				<td class="row1" width="80%"><a href="{$ibforums->base_url}showtopic=$tid&amp;st=$st&amp;&#35;entry$pid">$topic_title</a>
				</td>
			</tr>
			<tr>
				<td class="row1"  width="30%" valign="top">{$ibforums->lang['report_message']}</td>
				<td class="row1" width="80%"><textarea cols="60" rows="12" wrap="soft" name="message" class="textinput"></textarea>
				</td>
			</tr>
			<tr>
				<td class="formbuttonrow" colspan="2"><input type="submit" value="{$ibforums->lang['report_submit']}" /></td>
			</tr>
		</table>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// send_form
//===========================================================================
function send_form($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post" name="REPLIER">
	<input type="hidden" name="act" value="Mail" />
	<input type="hidden" name="CODE" value="01" />
	<input type="hidden" name="to" value="{$data['TO']}" />
	<div><b>{$ibforums->lang['imp_text']}</b></div>
	<br />
	<div class="borderwrap">
		<div class="maintitle">{$ibforums->lang['send_title']}</div>
		<div class="formsubtitle">{$ibforums->lang['send_email_to']} {$data['NAME']}</div>
		<table cellspacing="1">
			<tr>
				<td class="pformleftw" valign="top"><b>{$ibforums->lang['subject']}</b></td>
				<td class="pformright"><input type="text" name="subject" value="{$data['subject']}" size="50" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="pformleftw" valign="top"><b>{$ibforums->lang['message']}</b><br /><br />{$ibforums->lang['msg_txt']}</td>
				<td class="pformright"><textarea cols="60" rows="12" wrap="soft" name="message" class="textinput">{$data['content']}</textarea></td>
			</tr>
			<tr>
				<td class="formbuttonrow" colspan="2"><input type="submit" value="{$ibforums->lang['submit_send']}" /></td>
			</tr>
		</table>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// sent_screen
//===========================================================================
function sent_screen($member_name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['email_sent']}</div>
	<div class="tablepad">{$ibforums->lang['email_sent_txt']} $member_name</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_address
//===========================================================================
function show_address($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['send_email_to']} {$data[NAME]}</div>
	<div class="tablepad">
		{$ibforums->lang['show_address_text']}
		<br />
		&gt;&gt;<b><a href="mailto:{$data[ADDRESS]}" class="misc">{$ibforums->lang['send_email_to']} {$data[NAME]}</a></b>
	</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// yahoo_body
//===========================================================================
function yahoo_body($yahoo="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row1"><b>{$ibforums->lang['yahoo_name']}</b></td>
	<td class="row1"><input type="text" name="msnname" value="$yahoo" size="40" onMouseOver="this.focus()" onFocus="this.select()" /></td>
</tr>
<tr>
	<td class="row1"><b>{$ibforums->lang['yahoo_status']}</b></td>
	<td class="row1"><img src="http://opi.yahoo.com/online?u=$yahoo&amp;m=g&amp;t=2" /></td>
</tr>
<tr>
	<td class="row1" align="center" colspan="2"><a href="http://edit.yahoo.com/config/send_webmesg?.target=$yahoo&amp;.src=pg">{$ibforums->lang['yahoo_send_msg']}</a></td>
</tr>
<tr>
	<td class="row1" align="center" colspan="2"><a href="http://members.yahoo.com/interests?.oc=t&amp;.kw=$yahoo&amp;.sb=1">{$ibforums->lang['yahoo_view_profile']}</a></td>
</tr>
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