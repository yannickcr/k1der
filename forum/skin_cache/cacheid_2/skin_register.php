<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 2                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:38 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_register {

//===========================================================================
// bot_antispam
//===========================================================================
function bot_antispam($regid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
<input type="hidden" name="regid" value="$regid" />
	<fieldset class="row3">
	<legend><b>{$ibforums->lang['las_title']}</b></legend>
		<table cellspacing="0">
			<tr>
				<td width="1%">
					{$ibforums->lang['las_input']}<div class="desc">{$ibforums->lang['las_input_text']}</div>
					<input type="text" size="25" maxlength="32" name="reg_code" />
				</td>
				<td align="center">
					<img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}&amp;p=1" alt="Code Bit" />
					&nbsp;<img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}&amp;p=2" alt="Code Bit" />
					&nbsp;<img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}&amp;p=3" alt="Code Bit" />
					&nbsp;<img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}&amp;p=4" alt="Code Bit" />
					&nbsp;<img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}&amp;p=5" alt="Code Bit" />
					&nbsp;<img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}&amp;p=6" alt="Code Bit" />
				</td>
			</tr>
		</table>
	</fieldset>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// bot_antispam_gd
//===========================================================================
function bot_antispam_gd($regid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
<input type="hidden" name="regid" value="$regid" />
	<fieldset class="row3">
	<legend><b>{$ibforums->lang['las_title']}</b></legend>
		<table cellspacing="0">
			<tr>
				<td width="1%">
					{$ibforums->lang['las_input']}<div class="desc">{$ibforums->lang['las_input_text']}</div>
					<input type="text" size="25" maxlength="32" name="reg_code" />
				</td>
				<td align="center"><img src="{$ibforums->base_url}act=Reg&amp;CODE=image&amp;rc={$regid}" alt="Loading Image" /></td>
			</tr>
		</table>
	</fieldset>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// coppa_form
//===========================================================================
function coppa_form() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<html>
	<head>
		<title>{$ibforums->lang['cpf_title']}</title>
		<!--<link rel="stylesheet" href="style_sheets/stylesheet_<{css_id}>.css" type="text/css">-->
	</head>
	
	<body>
		<table cellspacing="1">
			<tr>
				<td valign="middle">
					<span class="pagetitle">{$ibforums->vars['board_name']}: {$ibforums->lang['cpf_title']}</span><br /><br />
					<b><span>{$ibforums->lang['cpf_perm_parent']}</span></b><br /><br />
					{$ibforums->lang['cpf_fax']} {$ibforums->vars['coppa_fax']}<br /><br />{$ibforums->lang['cpf_address']}<br />
					{$ibforums->vars['coppa_address']}
				</td>
			</tr>
		</table>
		<br />
		
		<table cellspacing="1">
			<tr>
				<td width="40%">{$ibforums->lang['user_name']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['pass_word']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['email_address']}</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<br />
		
		<table cellspacing="1">
			<tr>
				<td valign="middle"><b><span>{$ibforums->lang['cpf_sign']}</span></b></td>
			</tr>
		</table>
		<br />
		
		<table cellspacing="1">
			<tr>
				<td width="40%">{$ibforums->lang['cpf_name']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['cpf_relation']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['cpf_signature']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['cpf_email']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['cpf_phone']}</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="40%">{$ibforums->lang['cpf_date']}</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</body>
</html>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// coppa_start
//===========================================================================
function coppa_start($coppadate="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['registration_form']}</div>
	<div class="formsubtitle">{$ibforums->lang['coppa_info']}</div>
	<div class="tablepad"  align="center">
		<span class="postcolor">
			<b>{$ibforums->lang['coppa_link']}<br /><br /> 
			&lt; <a href="{$ibforums->base_url}act=Reg&amp;coppa_pass=1">{$ibforums->lang['coppa_date_before']} $coppadate</a>
			- <a href="{$ibforums->base_url}act=Reg&amp;CODE=coppa_two">{$ibforums->lang['coppa_date_after']} $coppadate</a> &gt;
			</b>
		</span>
	</div>
	<div class="formsubtitle">{$ibforums->lang['coppa_form']}</div>
	<div class="tablepad">{$ibforums->lang['coppa_form_text']} <a href="mailto:{$ibforums->vars['email_in']}">{$ibforums->vars['email_in']}</a></div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// coppa_two
//===========================================================================
function coppa_two() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['cp2_title']}</div>
	<table cellspacing="1">
		<tr>
			<td class="row1">
				{$ibforums->lang['cp2_text']}<br /><br />
				<div>
					<span>
						&lt;&lt; <a href="{$ibforums->base_url}">{$ibforums->lang['cp2_cancel']}</a> 
						- <a href="{$ibforums->base_url}act=Reg&amp;coppa_pass=1&amp;coppa_user=1">{$ibforums->lang['cp2_continue']}</a> &gt;&gt;
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<td class="formsubtitle">{$ibforums->lang['coppa_form']}</td>
		</tr>
		<tr>
			<td class="row1">{$ibforums->lang['coppa_form_text']} <a href="mailto:{$ibforums->vars['email_in']}">{$ibforums->vars['email_in']}</a></td>
		</tr>
	</table>
</div>
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
	<div class="formsubtitle">{$ibforums->lang['errors_found']}</div>
	<div class="tablepad"><span class="postcolor">$data</span></div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// field_dropdown
//===========================================================================
function field_dropdown($name="",$options="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<select name="$name">$options</select>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// field_entry
//===========================================================================
function field_entry($title="",$desc="",$content="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<fieldset>
	<legend>$title</legend>
		<div class="desc">$desc</div><br />
		$content
	</fieldset>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// field_textarea
//===========================================================================
function field_textarea($name="",$value="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<textarea cols="60" rows="5" name="$name">$value</textarea>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// field_textinput
//===========================================================================
function field_textinput($name="",$value="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<input type="text" size="30" name="$name" value="$value" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// lost_pass_form
//===========================================================================
function lost_pass_form($lasid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
<input type="hidden" name="act" value="Reg" />
<input type="hidden" name="CODE" value="11" />
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['lost_pass_form']}</div>
	<div class="formsubtitle">{$ibforums->lang['lp_header']}</div>
	<div class="tablepad"><span>
EOF;
//startif
if ( $ibforums->vars['converge_login_method'] == 'username' )
{
$IPBHTML .= <<<EOF
{$ibforums->lang['lp_text']}
EOF;
}//endif
else
{
$IPBHTML .= <<<EOF
{$ibforums->lang['lp_text_email']}
EOF;
}//endelse

$IPBHTML .= <<<EOF
	</span></div>
	<div class="formsubtitle">{$ibforums->lang['complete_form']}</div>
	<table cellspacing="0">
		<tr>
			<td class="pformleft">
EOF;
//startif
if ( $ibforums->vars['converge_login_method'] == 'username' )
{
$IPBHTML .= <<<EOF
<strong>{$ibforums->lang['lp_user_name']}</strong>
EOF;
}//endif
else
{
$IPBHTML .= <<<EOF
<strong>{$ibforums->lang['lp_email_address']}</strong>
EOF;
}//endelse
$IPBHTML .= <<<EOF
</td>
			<td class="pformright"><input type="text" size="32" maxlength="32" name="member_name" /></td>
		</tr>
	</table>
<!--{REG.ANTISPAM}--><br />
</div>
<br />
<div class="borderwrap">
	<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['lp_send']}" /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// optional_title
//===========================================================================
function optional_title() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!--
	<tr>
  		<td colspan="2" class="formsubtitle">{$ibforums->lang['cf_optional']}</td>
	</tr>
-->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_authorise
//===========================================================================
function show_authorise($member="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><!-- --></div>
	<div class="formsubtitle">{$ibforums->lang['registration_process']}</div>
	<div class="tablepad">{$ibforums->lang['thank_you']} {$member['name']}. {$ibforums->lang['auth_text']} {$member['email']}</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_dumb_form
//===========================================================================
function show_dumb_form($type="reg") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script type="text/javascript">
<!--
	function Validate(){
		// Check for Empty fields
		if (document.REG.uid.value == "" || document.REG.aid.value == ""){
			alert ("{$ibforums->lang['js_blanks']}");
			return false;
		}
	}
-->
</script>
<form action="{$ibforums->base_url}" method="post" name="REG" onsubmit="return Validate()">
<input type="hidden" name="act" value="Reg" />
<input type="hidden" name="CODE" value="03" />
<input type="hidden" name="type" value="$type" />
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['dumb_header']}</div>
	<div class="tablepad">{$ibforums->lang['dumb_text']}</div>
	<div class="formsubtitle">{$ibforums->lang['complete_form']}</div>
		<table cellspacing="0">
			<tr>
				<td class="pformleft">{$ibforums->lang['user_id']}</td>
				<td class="pformright"><input type="text" size="32" maxlength="32" name="uid" /></td>
			</tr>
			<tr>
				<td class="pformleft">{$ibforums->lang['val_key']}</td>
				<td class="pformright"><input type="text" size="32" maxlength="50" name="aid" /></td>
			</tr>
		</table>
	<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['dumb_submit']}" /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_lostpass_form
//===========================================================================
function show_lostpass_form() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language="javascript" type="text/javascript">
<!--
	function Validate(){
		// Check for Empty fields
		if (document.REG.uid.value == "" || document.REG.aid.value == ""){
			alert ("{$ibforums->lang['js_blanks']}");
			return false;
		}
	}
-->
</script>
<form action="{$ibforums->base_url}" method="post" name="REG" onsubmit="return Validate()">
<input type="hidden" name="act" value="Reg" />
<input type="hidden" name="CODE" value="03" />
<input type="hidden" name="type" value="lostpass" />
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['dumb_header']}</div>
	<div class="formsubtitle">{$ibforums->lang['lpf_title']}</div>
	<!--IBF.INPUT_TYPE-->
	<tr>
		<td class="pformleft"><b>{$ibforums->lang['lpf_pass1']}</b><br><i>{$ibforums->lang['lpf_pass11']}</i></td>
		<td class="pformright"><input type="password" size="32" maxlength="32" name="pass1" /></td>
	</tr>
	<tr>
		<td class="pformleft"><b>{$ibforums->lang['lpf_pass2']}</b><br><i>{$ibforums->lang['lpf_pass22']}</i></td>
		<td class="pformright"><input type="password" size="32" maxlength="32" name="pass2" /></td>
	</tr>
</table>
	<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['dumb_submit']}" /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_lostpass_form_auto
//===========================================================================
function show_lostpass_form_auto($aid="",$uid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<input type="hidden" name="uid" value="$uid" />
<input type="hidden" name="aid" value="$aid" />
<table cellspacing="0">
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_lostpass_form_manual
//===========================================================================
function show_lostpass_form_manual() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="tablepad">{$ibforums->lang['dumb_text']}</div>
<div class="formsubtitle">{$ibforums->lang['complete_form']}</div>
<table cellspacing="0">
	<tr>
		<td class="pformleft"><b>{$ibforums->lang['user_id']}</b></td>
		<td class="pformright"><input type="text" size="32" maxlength="32" name="uid" /></td>
	</tr>
	<tr>
		<td class="pformleft"><b>{$ibforums->lang['val_key']}</b></td>
		<td class="pformright"><input type="text" size="32" maxlength="50" name="aid" /></td>
	</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_lostpasswait
//===========================================================================
function show_lostpasswait($member="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['lpf_title']}</div>
	<div class="formsubtitle">{$ibforums->lang['registration_process']}</div>
	<div class="tablepad">{$ibforums->lang['lpass_text']}</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_preview
//===========================================================================
function show_preview($member="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><!-- --></div>
	<div class="formsubtitle">{$ibforums->lang['registration_process']}</div>
	<div class="tablepad">{$ibforums->lang['thank_you']} {$member['name']}. {$ibforums->lang['preview_reg_text']}</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_revalidate_form
//===========================================================================
function show_revalidate_form($name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post" name="REG">
<input type="hidden" name="act" value="Reg" />
<input type="hidden" name="CODE" value="reval2" />
<div>{$ibforums->lang['rv_ins']}</div><br />
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['rv_title']}</div>
	<table cellspacing="0">
		<tr>
			<td class="pformleft"><b>{$ibforums->lang['rv_name']}</b></td>
			<td class="pformright"><input type="text" size="32" maxlength="64" name="username" value="$name" /></td>
		</tr>
	</table>
	<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['rv_go']}" /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_revalidated
//===========================================================================
function show_revalidated() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['rv_title']}</div>
	<div class="formsubtitle">{$ibforums->lang['rv_process']}</div>
	<div class="tablepad">{$ibforums->lang['rv_done']}</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_terms
//===========================================================================
function show_terms($text="",$coppa_user="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}act=Reg&coppa_user={$coppa_user}&termsread=1&coppa_pass=1" method="post">
	<div class="borderwrap">
		<div class="maintitle">{$ibforums->lang['tc_title']}</div>
		<table cellspacing="1">
			<tr>
				<th>{$ibforums->lang['tc_text']}</th>
			</tr>
			<tr>
				<td class="row1">{$text}</td>
			</tr>
			<tr>
				<td class="row2"><label for="agree_cbox"><input type="checkbox" id="agree_cbox" name="agree_to_terms" value="1" /> <b>{$ibforums->lang['agree_submit']}</b></label></td>
			</tr>
			<tr>
				<td class="formbuttonrow"><input type="submit" value="{$ibforums->lang['tc_regbut']}" class="button"></td>
			</tr>
			<tr>
				<td class="catend" colspan="2"><!-- no content --></td>
			</tr>
		</table>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// ShowForm
//===========================================================================
function ShowForm($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script type="text/javascript">
<!--
var ipb_lang_js_blanks   = "{$ibforums->lang['js_blanks']}";
var ipb_lang_js_no_check = "{$ibforums->lang['js_no_check']}";
var subsdesc_0 = "{$ibforums->lang['subsm_no_desc']}";<!--{SUBS.JSCRIPT}-->
-->
</script>
<script type="text/javascript" src="jscripts/ipb_register.js"></script>
<form action="{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}" method="post" name="REG" onsubmit="return Validate()">
<input type="hidden" name="act" value="Reg" />
<input type="hidden" name="termsread" value="1" />
<input type="hidden" name="agree_to_terms" value="1" />
<input type="hidden" name="CODE" value="02" />
<input type="hidden" name="coppa_user" value="{$data['coppa_user']}" />
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['registration_form']}</div>
	<div class="formsubtitle">{$data['TEXT']}</div>
	<div class="tablepad">
		<table cellspacing="0">
			<tr>
				<td width="50%">
					<fieldset class="row3">
					<legend><b>{$ibforums->lang['user_name_title']}</b></legend>
						<table cellspacing="0">
							<tr>
								<td>{$ibforums->lang['user_name']} &nbsp;<span>(<a href="#" style="cursor: help; color: #555;" title="{$ibforums->lang['user_name_text']}">?</a>)</span></td>
							</tr>
							<tr>
								<td><input type="text" size="50" maxlength="64" value="{$ibforums->input['UserName']}" name="UserName" /></td>
							</tr>
						</table>
					</fieldset><br />
					
					<fieldset class="row3">
					<legend><b>{$ibforums->lang['password_title']}</b></legend>
						<table cellspacing="0">
							<tr>
								<td width="1%" nowrap="nowrap">{$ibforums->lang['password']} &nbsp;<span>(<a href="#" style="cursor: help; color: #555;" title="{$ibforums->lang['password_text']}">?</a>)</span></td>
								<td width="100%">{$ibforums->lang['password_confirm']} &nbsp;<span>(<a href="#" style="cursor: help; color: #555;" title="{$ibforums->lang['password_confirm_text']}">?</a>)</span></td>
							</tr>
							<tr>
								<td><input type="password" size="25" maxlength="32" value="{$ibforums->input['PassWord']}" name="PassWord" /></td>
								<td><input type="password" size="25" maxlength="32" value="{$ibforums->input['PassWord_Check']}"  name="PassWord_Check" /></td>
							</tr>
						</table>
					</fieldset><br />
				
					<fieldset class="row3">
					<legend><b>{$ibforums->lang['email_address_title']}</b></legend>
						<table cellspacing="0">
							<tr>
								<td width="1%" nowrap="nowrap">{$ibforums->lang['email_address']} &nbsp;<span>(<a href="#" style="cursor: help; color: #555;" title="{$ibforums->lang['email_address_text']}">?</a>)</span></td>
								<td width="100%">{$ibforums->lang['email_address_confirm']} &nbsp;<span>(<a href="#" style="cursor: help; color: #555;" title="{$ibforums->lang['email_address_confirm_text']}">?</a>)</span></td>
							</tr>
							<tr>
								<td><input type="text" size="25" maxlength="50" value="{$ibforums->input['EmailAddress']}"  name="EmailAddress" /></td>
								<td><input type="text" size="25" maxlength="50"  value="{$ibforums->input['EmailAddress_two']}" name="EmailAddress_two" /></td>
							</tr>
						</table>
					</fieldset>
					<!--{REQUIRED.FIELDS}-->
					<!--{SUBS.MANAGER}-->
					<!--IBF.MODULES.EXTRA-->
				</td>
				<td width="50%" valign="top">
					<div>
						<b>{$ibforums->lang['cf_optional']}</b><br /><br />
						<table cellspacing="0">
							<tr>
								<td>
									<fieldset>
									<legend>{$ibforums->lang['op_email_title']}</legend>
										<div class="desc">{$ibforums->lang['op_email_text']}</div><br />
										<input type="checkbox" name="allow_admin_mail" value="1" class="checkbox" <!--[admin.checked]--> /> {$ibforums->lang['op_email_ad']}<br />
										<input type="checkbox" name="allow_member_mail" value="1" class="checkbox" <!--[member.checked]--> /> {$ibforums->lang['op_email_mem']}
									</fieldset><br />
							
									<fieldset>
									<legend>{$ibforums->lang['op_tz_title']}</legend>
										<div class="desc">{$ibforums->lang['op_tz_text']}</div><br />
										<!--{TIME_ZONE}--><br /><br />
										<input type="checkbox" name="dst" value="1" class="checkbox" <!--[dst.checked]--> /> {$ibforums->lang['op_tz_dst']}<br />
									</fieldset>
									<!--{OPTIONAL.FIELDS}-->
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td><!--{REG.ANTISPAM}--></td>
				<td valign="middle" align="center">
					<div class="desc">{$ibforums->lang['submit_text']}</div><br />
						<input type="submit" value="{$ibforums->lang['submit_form']} &gt; &gt;" />
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// subsm_end
//===========================================================================
function subsm_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</select>
		</td>
		<td align="center">
			<input type="button" class="button" onclick="get_more_info()" value="{$ibforums->lang['subsm_more']}" />
		</td>
	</tr>
</table>
<div style="display: none; position: absolute; border: 3px outset #000;" class="tablepad" id="subspkdiv">
	<textarea readonly="readonly" name="pkdesc"></textarea>
	<div align="right"><a href="#" onclick="toggleview("subspkdiv"); return false;">{$ibforums->lang['subsm_close']}</a></div>
</div>
</fieldset>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// subsm_row
//===========================================================================
function subsm_row($id="",$title="",$cost="",$duration="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<option value="{$id}">$title ($duration) - $cost</option>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// subsm_start
//===========================================================================
function subsm_start($cur="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
<fieldset class="row3">
<legend><b>{$ibforums->lang['subsm_title']}</b></legend>
	<div class="desc"> &nbsp;{$ibforums->lang['cc_currency_in']} $cur</div>
	<table cellspacing="0">
		<tr>
			<td width="1%">
				<select class="dropdown" name="subspackage">
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// tmpl_form
//===========================================================================
function tmpl_form($action="",$hidden="",$title="",$content="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form method="post" action="$action">
$hidden
<div class="borderwrap">
	<div class="maintitle">$title</div>
	<table cellspacing="0">
		$content
	</table>
	<div class="formsubtitle" align="center"><input type="submit" /></div>
</div>
</form>
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