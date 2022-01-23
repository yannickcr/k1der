<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:42 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_mod {

//===========================================================================
// delete_js
//===========================================================================
function delete_js() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language="JavaScript" type="text/javascript">
	<!--
	function ValidateForm() {
		document.REPLIER.submit.disabled = true;
		return true;
	}
	-->
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// edit_user_form
//===========================================================================
function edit_user_form($profile="",$custom_fields="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['cp_em_title']}: {$profile['name']}</div>
		<table cellspacing="0">
			<tr>
				<td valign="top" width="50%">
					<fieldset>
					<legend><b>{$ibforums->lang['cp_em_sub_main']}</b></legend>
						<table cellspacing="0">
							<tr>
								<td width="40%" class="row1">{$ibforums->lang['cp_remove_av']}</td>
								<td width="60%" class="row1">
									<select name="avatar">
										<option value="0">{$ibforums->lang['no']}</option>
										<option value="1">{$ibforums->lang['yes']}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="row1">{$ibforums->lang['cp_remove_photo']}</td>
								<td class="row1">
									<select name="photo">
										<option value="0">{$ibforums->lang['no']}</option>
										<option value="1">{$ibforums->lang['yes']}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="row1">{$ibforums->lang['cp_edit_website']}</td>
								<td class="row1"><input type="text" size="35" name="website" value="{$profile['website']}" /></td>
							</tr>
							<tr>
								<td class="row1">{$ibforums->lang['cp_edit_location']}</td>
								<td class="row1"><input type="text" size="35" name="location" value="{$profile['location']}" /></td>
							</tr>
							<tr>
								<td class="row1" valign="top">{$ibforums->lang['cp_edit_interests']}</td>
								<td class="row1"><textarea cols="35" rows="3" name="interests">{$profile['interests']}</textarea></td>
							</tr>
							<tr>
								<td class="row1" valign="top">{$ibforums->lang['cp_edit_signature']}</td>
								<td class="row1"><textarea cols="35" rows="5" name="signature">{$profile['signature']}</textarea></td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td valign="top" width="50%">
					<fieldset>
					<legend><b>{$ibforums->lang['cp_em_sub_other']}</b></legend>
						<table cellspacing="0">
							<tr>
								<td class="row1" width="40%">{$ibforums->lang['cp_em_yahoo']}</td>
								<td class="row1" width="60%"><input type="text" size="35" name="yahoo" value="{$profile['yahoo']}" /></td>
							</tr>
							<tr>
								<td class="row1">{$ibforums->lang['cp_em_aim']}</td>
								<td class="row1"><input type="text" size="35" name="aim_name" value="{$profile['aim_name']}" /></td>
							</tr>
							<tr>
								<td class="row1">{$ibforums->lang['cp_em_icq']}</td>
								<td class="row1"><input type="text" size="35" name="icq_number" value="{$profile['icq_number']}" /></td>
							</tr>
							<tr>
								<td class="row1">{$ibforums->lang['cp_em_msn']}</td>
								<td class="row1"><input type="text" size="35" name="msnname" value="{$profile['msnname']}" /></td>
							</tr>
EOF;
//startif
if ( $custom_fields != "" )
{
$IPBHTML .= <<<EOF
$custom_fields
EOF;
}//endif
$IPBHTML .= <<<EOF
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['cp_em_submit']}" /></div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_form
//===========================================================================
function end_form($action="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle" align="center"><input type="submit" name="submit" value="$action" /></div>
	</div>
</form>
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
<tr>
		<td class="row1" valign="top"><b>$title</b><br />$desc</td>
		<td class="row1">$content</td>
	</tr>
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
<textarea cols="60" rows="5" wrap="soft" name="$name">$value</textarea>
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
<input type="text" size="35" name="$name" value="$value" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_jump
//===========================================================================
function forum_jump($data="",$menu_extra="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
	<div align="right">{$data}</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// merge_body
//===========================================================================
function merge_body($title="",$desc="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="1">
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang['mt_new_title']}</b></td>
			<td class="pformright"><input type="text" size="40" maxlength="50" name="title" value="$title" /></td>
		</tr>
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang['mt_new_desc']}</b></td>
			<td class="pformright"><input type="text" size="40" maxlength="40" name="desc" value="$desc" /></td>
		</tr>
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang['mt_tid']}</b></td>
			<td class="pformright"><input type="text" size="50" name="topic_url" value="" /></td>
		</tr>
	</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// merge_post_form
//===========================================================================
function merge_post_form($post="",$pid="",$author="",$auth_key="",$upload="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}act=mod&amp;CODE=postchoice&amp;tact=merge&amp;checked=1" method="post">
	<input type="hidden" name="act" value="mod" />
	<input type="hidden" name="selectedpids" value="{$ibforums->input['selectedpids']}" />
	<input type="hidden" name="auth_key" value="{$auth_key}" />
	<input type="hidden" name="t" value="{$ibforums->input['t']}" />
	<input type="hidden" name="f" value="{$ibforums->input['f']}" />
	<input type="hidden" name="st" value="{$ibforums->input['st']}" />
	
		<div class="borderwrap">
		<div class="maintitle">{$ibforums->lang['cm_title']}</div>
			<table cellspacing="1">
				<tr>
					<td width="35%" class="row2"><b>{$ibforums->lang['cm_post']}</b></td>
					<td width="65%" class="row2"><select name="postdate" class="dropdown">{$pid}</select></td>
				</tr>
				<tr>
					<td class="row2"><b>{$ibforums->lang['cm_author']}</b></td>
					<td class="row2"><select name="postauthor" class="dropdown">{$author}</select></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['cm_text']}</b></td>
					<td class="row2"><textarea cols="70" rows="20" name="Post" class="textarea">$post</textarea></td>
				</tr>
EOF;
//startif
if ( $upload != "" )
{
$IPBHTML .= <<<EOF
<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['cm_attach']}</b><div class="desc">{$ibforums->lang['cm_attach2']}</div></td>
					<td class="row2">
						<table cellspacing="1">
							$upload
						</table>
					</td>
				</tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
			</table>
			<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['cm_submit']}" /></div>
		</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_exp
//===========================================================================
function mod_exp($words="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">$words</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_log_end
//===========================================================================
function mod_log_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_log_none
//===========================================================================
function mod_log_none() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="pformright" colspan="3" align="center"><i>{$ibforums->lang['ml_none']}</i></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_log_row
//===========================================================================
function mod_log_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="pformright">{$data['member']}</td>
	<td class="pformright">{$data['action']}</td>
	<td class="pformright">{$data['date']}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_log_start
//===========================================================================
function mod_log_start() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
	<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['ml_title']}</div>
		<table cellspacing="1">
			<tr>
				<td class="formsubtitle" width="30%"><b>{$ibforums->lang['ml_name']}</b></td>
				<td class="formsubtitle" width="50%"><b>{$ibforums->lang['ml_desc']}</b></td>
				<td class="formsubtitle" width="20%"><b>{$ibforums->lang['ml_date']}</b></td>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_simple_page
//===========================================================================
function mod_simple_page($title="",$msg="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">$title</div>
		<div class="tablepad">
			$msg
			<br /><br />
			<div align="center"><a href="#" onclick="window.close();">{$ibforums->lang['cpp_close']}</a></div>
		</div>
	</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// move_checked_form_end
//===========================================================================
function move_checked_form_end($jump_html="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
			<div align="center" class="tablepad"><b>{$ibforums->lang['cp_tmove_to']}</b>&nbsp;&nbsp;<select name="df">$jump_html</select></div>
			<div align="center" class="formsubtitle"><input type="submit" value="{$ibforums->lang['cp_tmove_end']}" /></div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// move_checked_form_entry
//===========================================================================
function move_checked_form_entry($tid="",$title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row1" width="40%" align="right"><input type="checkbox" name="TID_$tid" value="1" checked="checked" /></td>
	<td class="row1" width="60%"><b>$title</b></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// move_checked_form_start
//===========================================================================
function move_checked_form_start($forum_name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['cp_tmove_start']} $forum_name</div>
		<table cellspacing="0">
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// move_form
//===========================================================================
function move_form($jhtml="",$forum_name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="1">
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang[move_from]} <b>$forum_name</b> {$ibforums->lang[to]}</b></td>
			<td class="pformright"><select name="move_id">$jhtml</select></td>
		</tr>
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang['leave_link']}</b></td>
			<td class="pformright"><select name="leave"><option value="y" selected="selected">{$ibforums->lang['yes']}</option><option value="n">{$ibforums->lang['no']}</option></select></td>
		</tr>
	</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// move_post_body
//===========================================================================
function move_post_body() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="1">
		<tr>
			<td class="row2" width="40%"><b>{$ibforums->lang['cmp_topic']}</b><div class="desc">{$ibforums->lang['cmp_topic2']}</div></td>
			<td class="row2" width="60%"><input type="text" size="50" name="topic_url" value="" /></td>
		</tr>
	</table>
</div>
<br />
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['st_post']}</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// poll_edit_new_entry
//===========================================================================
function poll_edit_new_entry($id="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
		<td class="row1"><b>{$ibforums->lang['pe_option']} $id</b> <i>( {$ibforums->lang['pe_unused']} )</i></td>
		<td class="row1"><input type="text" size="60" maxlength="250" name="POLL_$id" value="" /></td>
		<td class="row1"><input type="text" size="7" maxlength="250" name="VOTES_$id" value="0" /></td>
	</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// poll_edit_top
//===========================================================================
function poll_edit_top() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="0">
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// poll_entry
//===========================================================================
function poll_entry($id="",$entry="",$votes="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
		<td class="row1"><b>{$ibforums->lang['pe_option']} $id</b></td>
		<td class="row1"><input type="text" size="60" maxlength="250" name="POLL_$id" value="$entry" /></td>
		<td class="row1"><input type="text" size="7" maxlength="250" name="VOTES_$id" value="$votes" /></td>
	</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// poll_select_form
//===========================================================================
function poll_select_form($poll_question="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
		<td class="row1"><b>{$ibforums->lang['pe_question']}</b></td>
		<td class="row1"><input type="text" size="60" maxlength="250" name="poll_question" value="$poll_question" /></td>
	</tr>
	<tr>
		<td class="row1"><b>{$ibforums->lang['pe_pollonly']}</b></td>
		<td class="row1">
			<select name="pollonly">
				<option value="0">{$ibforums->lang['pe_no']}</option>
				<option value="1">{$ibforums->lang['pe_yes']}</option>
			</select>
		</td>
	</tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// prune_splash
//===========================================================================
function prune_splash($forum="",$forums_html="",$select="",$auth_key="",$confirm_data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['cp_prune']} {$forum['name']}</div>
EOF;
//startif
if ( $confirm_data['show'] == 1 )
{
$IPBHTML .= <<<EOF
<div>
		<div>
			<div class="borderwrap" id="maxibox">
				<div class="maintitle">
					<div><input type="button" value=" X " class="button" style="color:red" onclick="my_hide_div( my_getbyid('maxibox') );my_show_div( my_getbyid('minibox') );return false;" /></div>
					<div>{$ibforums->lang['mpt_confirm']}</div>
				</div>
					
				<div class="formsubtitle">{$ibforums->lang['cp_check_result']}</div>
					<table cellspacing="0">
						<tr>
							<td class="pformleftw"><b>{$ibforums->lang['cp_total_topics']}</b></td>
							<td class="pformright">{$confirm_data['tcount']}</td>
						</tr>
						<tr>
							<td class="pformleftw"><span>{$ibforums->lang['cp_total_match']}</span></td>
							<td class="pformright"><span>{$confirm_data['count']}</span></td>
						</tr>
					</table>
					
					<form action="{$ibforums->base_url}{$confirm_data['link']}" method="post">
						<input type="hidden" name="auth_key" value="$auth_key" />
						<div class="formsubtitle" align="center"><input type="submit" value="{$confirm_data['link_text']}" /></div>
					</form>
				</div>
				
				<div class="borderwrap" style="display:none" id="minibox">
				<div class="maintitle">
					<div><input type="button" value=" + " class="button" style="color: red;" onclick="my_hide_div( my_getbyid('minibox') );my_show_div( my_getbyid('maxibox') );return false;" /></div>
					<div>{$ibforums->lang['mpt_confirm']}</div>
				</div>
			</div>
		</div>
	</div>
EOF;
}//endif
$IPBHTML .= <<<EOF
	<div class="formsubtitle">{$ibforums->lang['mpt_help']}</div>
	<div class="tablepad">{$ibforums->lang['cp_prune_text']}</div>
		<form name="ibform" action="{$ibforums->base_url}" method="POST">
			<input type="hidden" name="s" value="{$ibforums->session_id}" />
			<input type="hidden" name="act" value="mod" />
			<input type="hidden" name="CODE" value="prune_start" />
			<input type="hidden" name="f" value="{$forum['id']}" />
			<input type="hidden" name="auth_key" value="$auth_key" />
			<input type="hidden" name="check" value="1" />
			
				<div class="formsubtitle">{$ibforums->lang['mpt_title']}</div>
					<table cellspacing="0">
						<tr>
							<td class="pformleftw">{$ibforums->lang['cp_action']}</td>
							<td class="pformright"><select name="df">$forums_html</select></td>
						</tr>
						<tr>
							<td class="pformleftw">{$ibforums->lang['cp_prune_days']}</td>
							<td class="pformright"><input type="text" size="40" name="dateline" value="{$ibforums->input['dateline']}" /></td>
						</tr>
						<tr>
							<td class="pformleftw">{$ibforums->lang['cp_prune_type']}</td>
							<td class="pformright">$select<br /><input type="checkbox" id="cbox" name="ignore_pin" value="1" checked="checked" class="checkbox" />&nbsp;<label for="cbox">{$ibforums->lang['mps_ignorepin']}</label></td>
						</tr>
						<tr>
							<td class="pformleftw">{$ibforums->lang['cp_prune_replies']}</td>
							<td class="pformright"><input type="text" size="40" name="posts" value="{$ibforums->input['posts']}" /></td>
						</tr>
						<tr>
							<td class="pformleftw">{$ibforums->lang['cp_prune_member']}</td>
							<td class="pformright"><input type="text" size="40" name="member" value="{$ibforums->input['member']}" /></td>
						</tr>
					</table>
				<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['cp_prune_sub1']}" class="button" /></div>
			</div>
		</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// split_body
//===========================================================================
function split_body($jump="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="row2">{$ibforums->lang['st_explain']}</div>
		<table cellspacing="1">
			<tr>
				<td class="row1"><b>{$ibforums->lang['mt_new_title']}</b></td>
				<td class="row2"><input type="text" size="40"  maxlength="100" name="title" value="" /></td>
			</tr>
			<tr>
				<td class="row1"><b>{$ibforums->lang['mt_new_desc']}</b></td>
				<td class="row2"><input type="text" size="40"  maxlength="100" name="desc" value="" /></td>
			</tr>
			<tr>
				<td class="row1"><b>{$ibforums->lang['st_forum']}</b></td>
				<td class="row2"><select name="fid">$jump</select></td>
			</tr>
		</table>
	</div>
<br />
	<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['st_post']}</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// split_end_form
//===========================================================================
function split_end_form($action="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle" align="center">
			<input type="submit" name="submit" value="$action" />
		</div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// split_row
//===========================================================================
function split_row($row="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$row['st_top_bit']}</div>
	<div class="tablepad">
		{$row['post']}
		<div align="right"><b>{$ibforums->lang['st_split']}</b>&nbsp;&nbsp;<input type="checkbox" name="post_{$row['pid']}" value="1" checked="checked" /></div>
	</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// table_top
//===========================================================================
function table_top($posting_title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle">$posting_title</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// topic_history
//===========================================================================
function topic_history($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
		<div class="maintitle">{$ibforums->lang['th_title']}</div>
		<table cellspacing="1">
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_topic']}</b></td>
				<td class="pformright">{$data['th_topic']}</td>
			</tr>
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_desc']}</b></td>
				<td class="pformright">{$data['th_desc']}&nbsp;</td>
			</tr>
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_start_date']}</b></td>
				<td class="pformright">{$data['th_start_date']}</td>
			</tr>
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_start_name']}</b></td>
				<td class="pformright">{$data['th_start_name']}</td>
			</tr>
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_last_date']}</b></td>
				<td class="pformright">{$data['th_last_date']}</td>
			</tr>
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_last_name']}</b></td>
				<td class="pformright">{$data['th_last_name']}</td>
			</tr>
			<tr>
				<td class="pformleftw"><b>{$ibforums->lang['th_avg_post']}</b></td>
				<td class="pformright">{$data['th_avg_post']}</td>
			</tr>
		</table>
	</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// topictitle_fields
//===========================================================================
function topictitle_fields($title="",$desc="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="1">
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang[edit_f_title]}</b></td>
			<td class="pformright"><input type="text" size="40" maxlength="50" name="TopicTitle" value="$title" /></td>
		</tr>
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang[edit_f_desc]}</b></td>
			<td class="pformright"><input type="text" size="40" maxlength="40" name="TopicDesc" value="$desc" /></td>
		</tr>
	</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// uploadbox_entry
//===========================================================================
function uploadbox_entry($attach="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td width="1%" align="center"><select name="attach_{$attach['attach_id']}" class="dropdown"><option value="keep">{$ibforums->lang['cm_keep']}</option><option value="delete">{$ibforums->lang['cm_delete']}</option></select></td>
			<td width="1%"><img src="{$ibforums->vars['mime_img']}/{$attach['image']}" alt="" /></td>
			<td width="15%" nowrap="nowrap">{$attach['size']}</td>
			<td width="95%"><a href="{$ibforums->base_url}act=Attach&amp;type=post&amp;id={$attach['attach_id']}" target="_blank"><b>{$attach['attach_file']}</b></a> #{$attach['attach_pid']}</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_errors
//===========================================================================
function warn_errors($data="") {
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
// warn_footer
//===========================================================================
function warn_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="pformleftw" valign="top"><b>{$ibforums->lang['w_reason']}</b><br />{$ibforums->lang['w_reason2']}</td>
			<td class="pformright"><textarea rows="6" cols="70" class="textinput" name="reason">{$ibforums->input['reason']}</textarea></td>
		</tr>
		<tr>
			<td class="pformleftw"><b>{$ibforums->lang['w_c_subj']}</b></td>
			<td class="pformright"><input type="input" name="subject" value="{$ibforums->input['subject']}" size="30" /></td>
		</tr>
		<tr>
			<td class="pformleftw" valign="top"><b>{$ibforums->lang['w_contact']}</b><br />{$ibforums->lang['w_contact2']}</td>
			<td class="pformright">
				{$ibforums->lang['w_c']}&nbsp;
				<select name="contactmethod">
					<option value="pm">{$ibforums->lang['w_c_p']}</option>
					<option value="email">{$ibforums->lang['w_c_e']}</option>
				</select>
					<br />
				<textarea rows="6" cols="70" class="textinput" name="contact">{$ibforums->input['contact']}</textarea>
			</td>
		</tr>
	</table>
		<div align="center" class="formsubtitle"><input type="submit" value="{$ibforums->lang['w_submit']}" /></div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_header
//===========================================================================
function warn_header($mid="",$name="",$cur=0,$min=0,$max=10,$key="",$tid="",$st="",$type="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form method="post" action="{$ibforums->base_url}&amp;act=warn&amp;CODE=dowarn&amp;mid=$mid&amp;t=$tid&amp;st=$st&amp;type={$ibforums->input['type']}">
	<input type="hidden" name="key" value="$key" />
		<div class="borderwrap">
		<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['w_warnfor']} <a href="{$ibforums->base_url}showuser=$mid">$name</a> ( $min &lt; $cur &gt; $max )</div>
			<div class="formsubtitle">{$ibforums->lang['w_complete']}</div>
				<table cellspacing="0">
					<tr>
						<td class="pformleftw"><b>{$ibforums->lang['w_adjust_level']}</b></td>
						<td class="pformright">
							<input type="radio" name="level" id="add" class="radiobutton" value="add" {$type['add']} /><label for="add" class="warnbad"><b>{$ibforums->lang['w_add']}</b></label>
								<br />
							<input type="radio" name="level" id="minus" class="radiobutton" value="remove" {$type['minus']} /><label for="minus" class="warngood"><b>{$ibforums->lang['w_remove']}</b></label>
						</td>
					</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_mod_posts
//===========================================================================
function warn_mod_posts($mod_tick="",$mod_array="",$mod_extra="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
						<td class="pformleftw"><b>{$ibforums->lang['w_modq']}</b></td>
						<td class="pformright">
							<input type="checkbox" name="mod_indef" value="1" $mod_tick /> {$ibforums->lang['w_modq_i']}
								<br />
							<b>{$ibforums->lang['w_orfor']}</b>
							<input type="input" name="mod_value" value="{$mod_array['timespan']}" size="5" />&nbsp;
							<select name="mod_unit">
								<option value="d" {$mod_array['days']}>{$ibforums->lang['w_day']}</option>
								<option value="h" {$mod_array['hours']}>{$ibforums->lang['w_hour']}</option>
							</select>
							$mod_extra
					</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_rem_posts
//===========================================================================
function warn_rem_posts($post_tick="",$post_array="",$post_extra="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
						<td class="pformleftw"><b>{$ibforums->lang['w_resposts']}</b></td>
						<td class="pformright">
							<input type="checkbox" name="post_indef" value="1" $post_tick /> {$ibforums->lang['w_resposts_i']}
								<br />
							<b>{$ibforums->lang['w_orfor']}</b>
							<input type="input" name="post_value" value="{$post_array['timespan']}" size="5" />&nbsp;
							<select name="post_unit">
								<option value="d" {$post_array['days']}>{$ibforums->lang['w_day']}</option>
								<option value="h" {$post_array['hours']}>{$ibforums->lang['w_hour']}</option>
							</select>
							$post_extra
					</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_restricition_in_place
//===========================================================================
function warn_restricition_in_place() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
		<b>{$ibforums->lang['w_restricted']}</b>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_success
//===========================================================================
function warn_success() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['w_done_t']}</div>
	<div class="formsubtitle">&nbsp;</div>
		<div class="tablepad">
			{$ibforums->lang['w_done_te']}
			<ul>
				<li><a href="{$ibforums->base_url}">{$ibforums->lang['w_done_home']}</a></li>
				<!--IBF.FORUM_TOPIC-->
			</ul>
		</div>
	</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_success_forum
//===========================================================================
function warn_success_forum($fid="",$fname="",$tid="",$tname="",$st=0) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<li><a href="{$ibforums->base_url}showforum=$fid">{$ibforums->lang['w_done_forum']} <b>$fname</b></a></li>
				<li><a href="{$ibforums->base_url}showtopic=$tid&amp;st=$st">{$ibforums->lang['w_done_topic']} <b>$tname</b></a></li>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_suspend
//===========================================================================
function warn_suspend($susp_array="",$susp_extra="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
		<td class="pformleftw"><b>{$ibforums->lang['w_suspend']}</b></td>
		<td class="pformright">
			{$ibforums->lang['w_susfor']}  <input type="input" name="susp_value" value="{$susp_array['timespan']}" size="5" />&nbsp;
			<select name="susp_unit">
				<option value="d" {$susp_array['days']}>{$ibforums->lang['w_day']}</option>
				<option value="h" {$susp_array['hours']}>{$ibforums->lang['w_hour']}</option>
			</select>
			$susp_extra
	</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_view_footer
//===========================================================================
function warn_view_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_view_header
//===========================================================================
function warn_view_header($id="",$name="",$links="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="0">
		<tr>
			<td><span id="phototitle">$name</span></td>
			<td align="right">$links</td>
		</tr>
	</table>
	<div class="borderwrap">
		<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['w_v_title']}: <a href="{$ibforums->base_url}showuser=$id">$name</a></div>
	</div>
	<table cellspacing="1">
		<tr>
			<th class="formsubtitle" width="30%">{$ibforums->lang['w_v_warnby']}</th>
			<th class="formsubtitle" width="70%">{$ibforums->lang['w_v_notes']}</th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_view_negative_row
//===========================================================================
function warn_view_negative_row($date="",$content="",$puni_name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row2" valign="top"><b>$puni_name</b></td>
			<td class="row2" valign="top">{$ibforums->lang['w_v_warned_on']} <b>$date</b></td>
		</tr>
		<tr>
			<td class="row1" valign="middle"><span class="warnbad">{$ibforums->lang['w_v_add']}</span></td>
			<td class="row1" valign="top"><span class="postcolor">$content</span></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_view_none
//===========================================================================
function warn_view_none() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row1" colspan="2" align="center"><b>{$ibforums->lang['w_v_none']}</b></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_view_positive_row
//===========================================================================
function warn_view_positive_row($date="",$content="",$puni_name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row2" valign="top"><b>$puni_name</b></td>
			<td class="row2" valign="top">{$ibforums->lang['w_v_warned_on']} <b>$date</b></td>
		</tr>
		<tr>
			<td class="row1" valign="middle"><span class="warngood">{$ibforums->lang['w_v_minus']}</span></td>
			<td class="row1" valign="top"><span class="postcolor">$content</span></td>
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