<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 2                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:38 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_login {

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
// ShowForm
//===========================================================================
function ShowForm($message="",$referer="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language='JavaScript' type="text/javascript">
<!--
function ValidateForm() {
	var Check = 0;
	if (document.LOGIN.UserName.value == '') { Check = 1; }
	if (document.LOGIN.PassWord.value == '') { Check = 1; }
	if (Check == 1) {
		alert("{$ibforums->lang['blank_fields']}");
		return false;
	} else {
		document.LOGIN.submit.disabled = true;
		return true;
	}
}
//-->
</script> 
<form action="{$ibforums->base_url}act=Login&amp;CODE=01" method="post" name="LOGIN" onsubmit="return ValidateForm()">
	<input type="hidden" name="referer" value="$referer" />
	<div class="borderwrap">
		<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['log_in']}</div>
		<div class="formsubtitle">$message</div>
		<div class="errorwrap">
			<h4>Attention!</h4>
			<p>{$ibforums->lang['login_text']}</p>
			<p><b>{$ibforums->lang['forgot_pass']} <a href="{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?act=Reg&amp;CODE=10">{$ibforums->lang['pass_link']}</a></b></p>
		</div>
		<table cellspacing="1">
			<tr>
				<td width="60%" valign="top">
					<fieldset>
						<legend><b>{$ibforums->lang['log_in']}</b></legend>
						<table cellspacing="1">
							<tr>
EOF;
//startif
if ( $ibforums->vars['converge_login_method'] == 'username' )
{
$IPBHTML .= <<<EOF
<td width="50%"><b>{$ibforums->lang['enter_name']}</b></td>
								<td width="50%"><input type="text" size="25" maxlength="64" name="UserName" class="forminput" /></td>
EOF;
}//endif
else
{
$IPBHTML .= <<<EOF
<td width="50%"><b>{$ibforums->lang['enter_email']}</b></td>
								<td width="50%"><input type="text" size="25" value="{$ibforums->input['UserName']}" maxlength="64" name="UserName" class="forminput" /></td>
EOF;
}//endelse
$IPBHTML .= <<<EOF
							</tr>
							<tr>
								<td width="50%"><b>{$ibforums->lang['enter_pass']}</b></td>
								<td width="50%"><input type="password" size="25" name="PassWord" class="forminput" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td width="40%" valign="top">
					<fieldset>
						<legend><b>{$ibforums->lang['options']}</b></legend>
						<table cellspacing="1">
							<tr>
								<td width="10%"><input type="checkbox" name="CookieDate" value="1" checked="checked" /></td>
								<td width="90%"><b>{$ibforums->lang['rememberme']}</b><br /><span class="desc">{$ibforums->lang['notrecommended']}</span></td>
							</tr>
							<tr>
								<td width="10%"><input type="checkbox" name="Privacy" value="1" /></td>
								<td width="90%"><b>{$ibforums->lang['form_invisible']}</b><br /><span class="desc">{$ibforums->lang['anon_name']}</span></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td class="formbuttonrow" colspan="2"><input class="button" type="submit" name="submit" value="{$ibforums->lang['log_in_submit']}" /></td>
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



}

/*--------------------------------------------------*/
/*<changed bits>

</changed bits>*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>