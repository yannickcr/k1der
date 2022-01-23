<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 5                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:51 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_msg {

//===========================================================================
// address_add
//===========================================================================
function address_add($mem_to_add="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
<input type="hidden" name="act" value="Msg" />
<input type="hidden" name="CODE" value="09" />
	<div class="formsubtitle">{$ibforums->lang['member_add']}</div>
	<table cellspacing="0">
		<tr>
			<td valign="middle">{$ibforums->lang['enter_a_name']}<br /><input type="text" name="mem_name" size="20" maxlength="40" value="$mem_to_add" /></td>
			<td valign="middle">{$ibforums->lang['enter_desc']}<br /><input type="text" name="mem_desc" size="30" maxlength="60" value="" /></td>
			<td valign="middle">{$ibforums->lang['allow_msg']}<br /><select name="allow_msg"><option value="yes" selected="selected">{$ibforums->lang['yes']}</option><option value="no">{$ibforums->lang['no']}</option></select></td>
		</tr>
	</table>
	<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['submit_address']}" /></div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// address_edit
//===========================================================================
function address_edit($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
<input type="hidden" name="act" value="Msg" />
<input type="hidden" name="CODE" value="12" />
<input type="hidden" name="MID" value="{$data[MEMBER]['contact_id']}"  />
	<div class="formsubtitle">{$ibforums->lang['member_edit']}</div>
	<table cellspacing="0">
		<tr>
			<td valign="middle"><b>{$data[MEMBER]['contact_name']}</b></td>
			<td valign="middle">{$ibforums->lang['enter_desc']}<br /><input type="text" name="mem_desc" size="30" maxlength="60" value="{$data[MEMBER]['contact_desc']}" /></td>
			<td valign="middle">{$ibforums->lang['allow_msg']}<br />{$data[SELECT]}</td>
		</tr>
	</table>
	<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['submit_address_edit']}" /></div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Address_header
//===========================================================================
function Address_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$ibforums->lang['address_current']}</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Address_none
//===========================================================================
function Address_none() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<p class="pcen">{$ibforums->lang['address_none']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Address_table_header
//===========================================================================
function Address_table_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
	<div class="borderwrapm">
		<table cellspacing="1">
			<tr>
				<th><b>{$ibforums->lang['member_name']}</b></th>
				<th width="60%"><b>{$ibforums->lang['enter_block']}</b></th>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// archive_complete
//===========================================================================
function archive_complete() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$ibforums->lang['arc_comp_title']}</div>
	<p>{$ibforums->lang['arc_complete']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// archive_form
//===========================================================================
function archive_form($jump_html="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
<input type="hidden" name="act" value="Msg" />
<input type="hidden" name="CODE" value="15" />
	
<div class="formsubtitle">{$ibforums->lang['archive_title']}</div>
	<p>{$ibforums->lang['archive_text']}</p>
		<table cellspacing="0">
			<tr>
				<td><b>{$ibforums->lang['arc_folders']}</b></td>
				<td>$jump_html</td>
			</tr>
			<tr>
				<td><b>{$ibforums->lang['arc_dateline']}</b></td>
				<td valign="middle">
					<select name="dateline">
						<option value="1">1</option>
						<option value="7">7</option>
						<option value="30" selected="selected">30</option>
						<option value="90">90</option>
						<option value="365">365</option>
						<option value="all">{$ibforums->lang['arc_alldays']}</option>
					</select>
						&nbsp;&nbsp;{$ibforums->lang['arc_days']}
					<select name="oldnew">
						<option value="newer" selected="selected">{$ibforums->lang['arch_new']}</option>
						<option value="older">{$ibforums->lang['arch_old']}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>{$ibforums->lang['arc_max']}</b></td>
				<td valign="middle"><select name="number"><option value="5">5</option><option value="10">10</option><option value="20" selected="selected">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option></select></td>
			</tr>
			<tr>
				<td><b>{$ibforums->lang['arc_delete']}</b></td>
				<td valign="middle"><select name="delete"><option value="yes">{$ibforums->lang['arc_yes']}</option><option value="no" selected="selected">{$ibforums->lang['arc_no']}</option></select></td>
			</tr>
			<tr>
				<td><b>{$ibforums->lang['arc_type']}</b></td>
				<td valign="middle"><select name="type"><option value="xls" selected="selected">{$ibforums->lang['arc_xls']}</option><option value="html">{$ibforums->lang['arc_html']}</option></select></td>
			</tr>
		</table>
		<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['arc_submit']}" /></div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// archive_html_entry
//===========================================================================
function archive_html_entry($info="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrapm">
	<div class="maintitle">PM: {$info['msg_title']}</div>
	<div class="tablefill"><div class="postcolor">{$info['msg_content']}</div></div>
	<div class="formsubtitle">Sent by <b>{$info['msg_sender']}</b> on {$info['msg_date']}</div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// archive_html_entry_sent
//===========================================================================
function archive_html_entry_sent($info="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrapm">
	<div class="maintitle">PM: {$info['msg_title']}</div>
	<div class="tablefill"><div class="postcolor">{$info['msg_content']}</div></div>
	<div class="formsubtitle">Sent to <b>{$info['msg_sender']}</b> on {$info['msg_date']}</div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// archive_html_footer
//===========================================================================
function archive_html_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</div>
</body>
</html>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// archive_html_header
//===========================================================================
function archive_html_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<html>
<head>
	<title>{$ibforums->lang['pma_title']}</title>
	<style type="text/css">
		html{
			overflow-x: auto;
		}
		
		body{
			background-color: #fff;
			color: #000;
			font-family: Verdana, Tahoma, Arial, sans-serif;
			font-size: 11px;
			margin:0px;
			padding:0px;
			text-align:center;
		   }
		   
		a:link, a:visited, a:active{
			color: #000;
			text-decoration: underline;
		}
		
		a:hover{
			color: #465584;
			text-decoration:underline;
		}
		
		img{
			border: 0;
			vertical-align: middle;
		}
				
		#ipbwrapper{
			margin: 0 auto 0 auto;
			text-align: left;
			width: 95%;
		}
		
		.post1{
			background-color: #F5F9FD;
		}
		
		.post2{
			background-color: #EEF2F7;
		}
	
		/* Common elements */
		.row1{
			background-color: #F5F9FD;
		}
		
		.row1{
			background-color: #DFE6EF;
		}
		
		.row3{
			background-color: #EEF2F7;
		}
		
		.row2{
			background-color: #E4EAF2;
		}
	
		/* tableborders gives the white column / row lines effect */
		.plainborder{
			background-color: #F5F9FD
			border: 1px solid #345487;
		}
		
		.tableborder{
			background-color: #FFF;
			border: 1px solid #345487;
			margin: 0;
			padding: 0;
		}
		
		.tablefill{
			background-color: #F5F9FD;
			border: 1px solid #345487;
			padding: 6px;
		}
		
		.tablepad{
			background-color: #F5F9FD;
			padding:6px;
		}
		
		.tablebasic{
			border: 0;
			margin: 0;
			padding: 0;
			width:100%;
		}
	
		.pformstrip{
			background-color: #D1DCEB;
			color: #3A4F6C;
			font-weight: bold;
			margin-top:1px
			padding:7px;
		}
		
		#QUOTE{
			background-color: #FAFCFE;
			border: 1px solid #000;
			color: #465584;
			font-family: Verdana, Arial;
			font-size: 11px;
			padding: 2px;
		}
		
		#CODE{
			background-color: #FAFCFE;
			border: 1px solid #000;
			color: #465584;
			font-family: Courier, Courier New, Verdana, Arial;
			font-size: 11px;
			padding: 2px;
		}
		/* Main table top (dark blue gradient by default) */
		.maintitle{
			background-color: #D1DCEB;
			background-image: url({$ibforums->vars['board_url']}/style_images/<#IMG_DIR#>/tile_back.gif);
			color: #FFF;
			font-weight: bold;
			padding:8px 0px 8px 5px;
			vertical-align:middle;
		}
		
		.maintitle a:link, .maintitle  a:visited, .maintitle  a:active{
			color: #fff;
			text-decoration: none;
		}
		
		.maintitle a:hover{
			text-decoration: underline;
		}
		
		/* Topic View elements */
		.signature{
			color: #339;
			font-size: 10px;
			line-height:150%;
		}
		
		.postdetails{
			font-size: 10px;
		}
		
		.postcolor{
			font-size: 12px;
			line-height: 160%;
		}
	</style>
</head>
<body>
 <div id="ipbwrapper">
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// empty_folder_footer
//===========================================================================
function empty_folder_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
			<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['fd_continue']}" /></div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// empty_folder_header
//===========================================================================
function empty_folder_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
<input type="hidden" name="act" value="Msg" />
<input type="hidden" name="CODE" value="dofolderdelete" />
	<div class="formsubtitle">{$ibforums->lang['mi_prune_msg']}</div>
	<p>{$ibforums->lang['fd_text']}</p>
	<div class="borderwrapm">
		<table cellspacing="1">
			<tr>
				<th>{$ibforums->lang['fd_name']}</th>
				<th>{$ibforums->lang['fd_count']}</th>
				<th>{$ibforums->lang['fd_empty']}</th>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// empty_folder_row
//===========================================================================
function empty_folder_row($real="",$id="",$cnt="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
				<td class="row1"><b>$real</b></td>
				<td class="row1" align="center">$cnt</td>
				<td class="row1" align="center"><input type="checkbox" class="checkbox" name="its_$id" value="1" /></td>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// empty_folder_save_unread
//===========================================================================
function empty_folder_save_unread() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
				<td class="row1" colspan="3" align="center"><input type="checkbox" class="checkbox" name="save_unread" value="1" checked="checked" /> <b>{$ibforums->lang['fd_save_unread']}</b></td>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_address_table
//===========================================================================
function end_address_table() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
</table>
	</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_inbox
//===========================================================================
function end_inbox($vdi_html="",$amount_info="",$pages="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
				<td align="right" colspan="5" class="formbuttonrow">
					<input type="submit" name="move" value="{$ibforums->lang['move_button']}" /> $vdi_html {$ibforums->lang['move_or_delete']} <input type="submit" name="delete" value="{$ibforums->lang['delete_button']}" /> {$ibforums->lang['selected_msg']}
				</td>
			</tr>
		</table>
	</div>
</form>
<div style="padding: 5px;">
	<div class="wrapmini"><{M_READ}>&nbsp;{$ibforums->lang['icon_read']}<br /><{M_UNREAD}>&nbsp;{$ibforums->lang['icon_unread']}</div>
	<div align="right">$pages<br /><i>$amount_info</i></div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// inbox_row
//===========================================================================
function inbox_row($msg="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr id="{$msg['mt_id']}">
			<td align="center" valign="middle" class="row1">{$msg['icon']}</td>
			<td class="row1">{$msg['attach_img']}&nbsp;<a href="{$ibforums->base_url}act=Msg&amp;CODE=03&amp;VID={$msg['mt_vid_folder']}&amp;MSID={$msg['mt_id']}">{$msg['mt_title']}</a></td>
			<td class="row1"><a href="{$ibforums->base_url}showuser={$msg['from_id']}">{$msg['from_name']}</a> {$msg['add_to_contacts']}</td>
			<td class="row1">{$msg['date']}</td>
			<td align="center" class="row1"><input type="hidden" name="{$msg['mt_id']}" value="{$msg['mt_read']}" /><input type="checkbox" name="msgid_{$msg['mt_id']}" value="yes" onclick="cca(this);" /></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// inbox_table_header
//===========================================================================
function inbox_table_header($dirname="",$info="",$vdi_html="",$pages="",$curvid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- inbox folder -->
<div class="formsubtitle">$dirname</div>
	<table cellspacing="0">
		<tr>
			<td valign="middle">
				<!-- LEFT -->
				<table cellspacing="1" style="width:250px" class="inbox">
					<tr>
						<td class="row1" colspan="3">{$info['full_messenger']}</td>
					</tr>
					<tr>
						<td valign="middle" class="row1" colspan="3"><{BAR_LEFT}><img src="{$ibforums->vars['img_url']}/bar.gif" width="{$info['img_width']}" height="11" align="middle" alt="" /><{BAR_RIGHT}></td>
					</tr>
					<tr>
						<td class="row1" width="33%" valign="middle">0%</td>
						<td class="row1" width="33%" align="center" valign="middle">50%</td>
						<td class="row1" width="33%" align="right" valign="middle">100%</td>
					</tr>
				</table>
			</td>
			
			<!-- RIGHT -->
			<td align="right" valign="bottom">
				$pages<br /><br />
				<a href="javascript:select_read()">{$ibforums->lang['pmpc_mark_read']}</a> :: <a href="javascript:unselect_all()">{$ibforums->lang['pmpc_unmark_all']}</a><br /><br />
				<form action="{$ibforums->base_url}CODE=01&amp;act=Msg" name="jump" method="post">
					<b>{$ibforums->lang['goto_folder']}: </b>&nbsp; $vdi_html 
					<input type="submit" name="submit" value="{$ibforums->lang['goto_submit']}" />
				</form>
			</td>
		</tr>
	</table>
<!-- INBOX TABLE -->
  
<form action="{$ibforums->base_url}CODE=06&amp;act=Msg" name="mutliact" method="post">
<input type="hidden" name="curvid" value="$curvid" />
<div class="borderwrapm">
	<table cellspacing="1">
		<tr>
			<th width="5%">&nbsp;</th>
			<th width="35%"><a href="{$ibforums->base_url}act=Msg&amp;CODE=01&amp;VID={$info['vid']}&amp;sort=title&amp;st={$ibforums->input['st']}"><b>{$ibforums->lang['message_title']}</b></a></th>
			<th width="30%"><a href="{$ibforums->base_url}act=Msg&amp;CODE=01&amp;VID={$info['vid']}&amp;sort=name&amp;st={$ibforums->input['st']}"><b>{$ibforums->lang['message_from']}</b></a></th>
			<th width="25%"><a href="{$ibforums->base_url}act=Msg&amp;CODE=01&amp;VID={$info['vid']}&amp;sort={$info['date_order']}&amp;st={$ibforums->input['st']}"><b>{$ibforums->lang['message_date']}</b></a></th>
			<th align="center" width="5%"><input name="allbox" type="checkbox" value="Check All" onclick="InboxCheckAll();" /></th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mass_pm_box
//===========================================================================
function mass_pm_box($names="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td colspan="2" class="formsubtitle">{$ibforums->lang['carbon_copy_title']}</td>
		</tr>
		<tr>
			<td class="pformleft">{$ibforums->lang['carbon_copy_desc']}</td>
			<td class="pformright">
				<textarea name="carbon_copy" rows="5" cols="40">$names</textarea><br />
				<input type="button" name="findusers" onclick="find_users()" value="{$ibforums->lang['find_user_names']}" />
				<br /><input type="checkbox" name="mt_hide_cc" value="1" />&nbsp;<b>{$ibforums->lang['cc_hide_users']}</b>
			</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// No_msg_inbox
//===========================================================================
function No_msg_inbox() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row1" colspan="5" align="center"><b>{$ibforums->lang['inbox_no_msg']}</b></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pm_errors
//===========================================================================
function pm_errors($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$ibforums->lang['err_errors']}</div>
	<span class="postcolor"><p>$data<br /><br />{$ibforums->lang['pme_none_sent']}</p></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// prefs_add_dirs
//===========================================================================
function prefs_add_dirs() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$ibforums->lang['prefs_new']}</div>
	<p>{$ibforums->lang['prefs_text_b']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// prefs_footer
//===========================================================================
function prefs_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle" align="center"><input type="submit" value="{$ibforums->lang['prefs_submit']}" /></div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// prefs_header
//===========================================================================
function prefs_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
<input type="hidden" name="act" value="Msg" />
<input type="hidden" name="CODE" value="08" />
	<div class="formsubtitle">{$ibforums->lang['prefs_current']}</div>
	<p>{$ibforums->lang['prefs_text_a']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// prefs_row
//===========================================================================
function prefs_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<p><input type="text" name="{$data[ID]}" value="{$data[REAL]}" />{$data[EXTRA]}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// preview
//===========================================================================
function preview($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$ibforums->lang['pm_preview']}</div>
	<p>$data</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// render_address_row
//===========================================================================
function render_address_row($entry="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row1" valign="middle"><a href="{$ibforums->base_url}act=Profile&amp;CODE=03&amp;MID={$entry['contact_id']}"><b>{$entry['contact_name']}</b></a> &nbsp; &nbsp;[ {$entry['contact_desc']} ]</td>
			<td class="row1" valign="middle">[ <a href="{$ibforums->base_url}act=Msg&amp;CODE=11&amp;MID={$entry['contact_id']}">{$ibforums->lang['edit']}</a> ] :: [ <a href="{$ibforums->base_url}act=Msg&amp;CODE=10&amp;MID={$entry['contact_id']}">{$ibforums->lang['delete']}</a> ]&nbsp;&nbsp;( {$entry['text']} )</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Render_msg
//===========================================================================
function Render_msg($post="",$author="",$jump="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="formsubtitle">{$post['mt_title']}</div>
	<div align="right" style='padding:2px;'>[ <a href="{$ibforums->base_url}CODE=04&amp;act=Msg&amp;MSID={$post['mt_id']}&amp;MID={$author['id']}&amp;fwd=1">{$ibforums->lang['vm_forward_pm']}</a> | <a href="{$ibforums->base_url}CODE=04&amp;act=Msg&amp;MID={$author['id']}&amp;MSID={$post['mt_id']}">{$ibforums->lang['pm_reply_link']}</a> ]</div>
	
	<div class="borderwrapm">
		<div class="subtitle">{$ibforums->lang['m_pmessage']}</div>
			<table cellspacing="1">
				<tr>
					<td valign="middle" class="row2"><span class="normalname"><a href="{$ibforums->base_url}showuser={$author['id']}">{$author['name']}</a></span></td>
					<td class="row2" valign="top">
						<!-- POSTED DATE DIV -->
						<div class="row2" style='float:left'>
							{$data[POST]['post_icon']}<span class="postdetails"><b>{$post['mt_title']}</b>, {$post['msg_date']}</span>
						</div>
						<!-- DELETE  DIV -->
						<div align="right"><a href="{$ibforums->base_url}CODE=05&amp;act=Msg&amp;MSID={$post['mt_id']}&amp;VID={$author['VID']}"><{P_DELETE}></a>&nbsp;<a href="{$ibforums->base_url}CODE=04&amp;act=Msg&amp;MID={$author['id']}&amp;MSID={$post['mt_id']}"><{P_QUOTE}></a></div>
					</td>
				</tr>
				<tr>
					<td valign="top" class="post1">
						<span class="postdetails">
							{$author['avatar']}<br /><br />
							{$author['title']}<br />
							{$author['member_rank_img']}<br /><br />
							{$author['member_group']}<br />
							{$author['member_posts']}<br />
							{$author['member_number']}<br />
							{$author['member_joined']}<br /><br />
							{$author['warn_text']} {$author['warn_minus']}{$author['warn_img']}{$author['warn_add']}
						</span>
						<br />
						<!--$ author[field_1]-->
						<img src="{$ibforums->vars['img_url']}/spacer.gif" alt="" width="160" height="1" /><br /> 
					</td>
					<td width="100%" valign="top" class="post1">
						{$post['show_cc_users']}
						<div class="postcolor">{$post['msg_post']} <!--IBF.ATTACHMENT_{$post['msg_id']}--></div>
						{$author['signature']}
					</td>
				</tr>
				<tr>
					<td class="darkrow3">[ <a href="{$ibforums->base_url}CODE=02&amp;act=Msg&amp;MID={$author['id']}">{$ibforums->lang['add_to_book']}</a> ]</td>
					<td class="darkrow3" nowrap="nowrap">
						<!-- EMAIL / WWW / MSGR -->
						<div class="darkrow3" style='float:left'>
							{$author['addresscard']}{$author['message_icon']}{$author['email_icon']}{$author['website_icon']}{$author['icq_icon']}{$author['aol_icon']}{$author['yahoo_icon']}{$author['msn_icon']}
						</div>
						<!-- UP -->
						<div align="right"><a href="javascript:scroll(0,0);"><{P_UP}></a></div>
					</td>
				</tr>
			</table>
		</div>
		
		<div style='padding:4px;float:left'>
			<form action="{$ibforums->base_url}" name="jump" method="post">
			<input type="hidden" name="act" value="Msg" />
			<input type="hidden" name="CODE" value="01" />
				{$ibforums->lang['goto_folder']}:&nbsp; {$jump}
			<input type="submit" name="submit" value="{$ibforums->lang[goto_submit]}" />
			</form>
		</div>
		<div align="right" style='padding:4px'>[ <a href="{$ibforums->base_url}CODE=04&amp;act=Msg&amp;MSID={$post['mt_id']}&amp;MID={$author['id']}&amp;fwd=1">{$ibforums->lang['vm_forward_pm']}</a> | <a href="{$ibforums->base_url}CODE=04&amp;act=Msg&amp;MID={$author['id']}&amp;MSID={$post['mt_id']}">{$ibforums->lang['pm_reply_link']}</a> ]</div>
		<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// render_msg_show_cc
//===========================================================================
function render_msg_show_cc($cc_users="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<b>CC:</b> $cc_users
		<hr noshade="noshade" size="1" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Send_form
//===========================================================================
function Send_form($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script type="text/javascript">
<!--
	function find_users(){
		url = "index.{$ibforums->vars['php_ext']}?act=legends&CODE=finduser_one&s={$ibforums->session_id}&entry=textarea&name=carbon_copy&sep=line";
		window.open(url,"FindUsers","width=400,height=250,resizable=yes,scrollbars=yes"); 
	}
-->
</script>
<form action="{$ibforums->base_url}" method="post" name="REPLIER" {$data['form_extra']} onsubmit="return ValidateForm(1)">
<input type="hidden" name="act" value="Msg" />
<input type="hidden" name="CODE" value="04" />
<input type="hidden" name="MODE" value="01" />
<input type="hidden" name="OID"  value="{$data['OID']}" />
<input type="hidden" name="removeattachid" value="0" />
<input type="hidden" name="post_key" value="{$data['post_key']}" />
{$data['upload']}
<table cellspacing="0">
	<tr>
		<td colspan="2" class="formsubtitle">{$ibforums->lang['to_whom']}</td>
	</tr>
	<tr>
		<td class="pformleft">{$ibforums->lang['address_list']}</td>
		<td class="pformright">{$data[CONTACTS]}&nbsp;</td>
	</tr>
	<tr>
		<td class="pformleft">{$ibforums->lang['enter_name']}</td>
		<td class="pformright"><input type="text" name="entered_name" size="50" value="{$data[N_ENTER]}" tabindex="1" /></td>
	</tr>
		<!--IBF.MASS_PM_BOX-->
	<tr>
		<td colspan="2" class="formsubtitle">{$ibforums->lang['enter_message']}</td>
	</tr>
	<tr>
		<td class="pformleft">{$ibforums->lang['msg_title']}</td>
		<td class="pformright"><input type="text" name="msg_title" size="40" tabindex="2" maxlength="40" value="{$data[O_TITLE]}" /></td>
	</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// send_form_footer
//===========================================================================
function send_form_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td colspan="2" class="formsubtitle">{$ibforums->lang['msg_options']}</td>
		</tr>
		<tr>
			<td class="pformleft">&nbsp;</td>
			<td class="pformright">
				<input type="checkbox" name="add_sent" value="yes" />&nbsp;<b>{$ibforums->lang['auto_sent_add']}</b>
					<br />
				<input type="checkbox" name="add_tracking" value="1" />&nbsp;<b>{$ibforums->lang['vm_track_msg']}</b>
			</td>
		</tr>
		<tr>
			<td class="formbuttonrow" align="center" colspan="2">
				<input type="submit" value="{$ibforums->lang['submit_send']}" tabindex="4" accesskey="s" name="submit" />
				<input type="submit" value="{$ibforums->lang['pm_pre_button']}" tabindex="5" name="preview" />
				<input type="submit" value="{$ibforums->lang['pms_send_later']}" tabindex="6" name="save" />
			</td>
		</tr>
	</table>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// trackread_end
//===========================================================================
function trackread_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td align="right" colspan="5" class="formbuttonrow"><input type="submit" name="endtrack" value="{$ibforums->lang['tk_untrack_button']}" /> {$ibforums->lang['selected_msg']}</td>
		</tr>
	</table>
</div>
</form>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// trackread_row
//===========================================================================
function trackread_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row1" valign="middle">{$data['icon']}</td>
	<td class="row1">{$data['mt_title']}</td>
	<td class="row1"><a href="{$ibforums->base_url}showuser={$data['memid']}">{$data['to_name']}</a></td>
	<td class="row1">{$data['date']}</td>
	<td class="row1"><input type="checkbox" name="msgid_{$data['mt_id']}" value="yes" onclick="CheckCheckAll(document.trackread);" /></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// trackread_table_header
//===========================================================================
function trackread_table_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}CODE=31&amp;act=Msg" name="trackread" method="post">
<div class="formsubtitle">{$ibforums->lang['tk_read_messages']}</div>
<p>{$ibforums->lang['tk_read_desc']}</p>
<div class="borderwrapm">
	<table cellspacing="1">
		<tr>
			<th width="5%">&nbsp;</th>
			<th width="30%"><b>{$ibforums->lang['message_title']}</b></th>
			<th width="30%"><b>{$ibforums->lang['pms_message_to']}</b></th>
			<th width="20%"><b>{$ibforums->lang['tk_read_date']}</b></th>
			<th align="center" width="5%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.trackread);" /></th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// trackUNread_end
//===========================================================================
function trackUNread_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td align="right" colspan="5" class="formbuttonrow"><input type="submit" name="delete" value="{$ibforums->lang['delete_button']}" /> {$ibforums->lang['selected_msg']}</td>
		</tr>
	</table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// trackUNread_row
//===========================================================================
function trackUNread_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row1" valign="middle">{$data['icon']}</td>
			<td class="row1">{$data['mt_title']}</td>
			<td class="row1"><a href="{$ibforums->base_url}showuser={$data['memid']}">{$data['to_name']}</a></td>
			<td class="row1">{$data['date']}</td>
			<td class="row1"><input type="checkbox" name="msgid_{$data['mt_id']}" value="yes" onclick="CheckCheckAll(document.trackunread);" /></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// trackUNread_table_header
//===========================================================================
function trackUNread_table_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}CODE=32&amp;act=Msg" name="trackunread" method="post">
<div class="formsubtitle">{$ibforums->lang['tk_unread_messages']}</div>
<p>{$ibforums->lang['tk_unread_desc']}</p>
<div class="borderwrapm">
	<table cellspacing="1">
		<tr>
			<th width="5%">&nbsp;</th>
			<th width="30%"><b>{$ibforums->lang['message_title']}</b></th>
			<th width="30%"><b>{$ibforums->lang['pms_message_to']}</b></th>
			<th width="20%"><b>{$ibforums->lang['tk_unread_date']}</b></th>
			<th align="center" width="5%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.trackunread);" /></th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// unsent_end
//===========================================================================
function unsent_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td align="center" nowrap="nowrap" colspan="6" class="formbuttonrow"><input type="submit" name="delete" value="{$ibforums->lang['delete_button']}" /> {$ibforums->lang['selected_msg']}</td>
		</tr>
	</table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// unsent_row
//===========================================================================
function unsent_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="row1" valign="middle">{$data['msg']['icon']}</td>
			<td class="row1">{$data['msg']['attach_img']}&nbsp;<a href="{$ibforums->base_url}act=Msg&amp;CODE=21&amp;MSID={$data['msg']['mt_id']}">{$data['msg']['mt_title']}</a></td>
			<td class="row1"><a href="{$ibforums->base_url}showuser={$data['msg']['from_id']}">{$data['msg']['from_name']}</a></td>
			<td class="row1">{$data['msg']['date']}</td>
			<td class="row1" align="center">{$data['msg']['cc_users']}</td>
			<td class="row1"><input type="checkbox" name="msgid_{$data['msg']['mt_id']}" value="yes" /></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// unsent_table_header
//===========================================================================
function unsent_table_header() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}CODE=06&amp;act=Msg&amp;saved=1" name="mutliact" method="post">
<div class="formsubtitle">{$ibforums->lang['pms_saved_title']}</div>
<br />
<div class="borderwrapm">
	<table cellspacing="1">
		<tr>
			<th width="5%">&nbsp;</th>
			<th width="30%"><b>{$ibforums->lang['message_title']}</b></th>
			<th width="30%"><b>{$ibforums->lang['pms_message_to']}</b></th>
			<th width="20%"><b>{$ibforums->lang['pms_saved_date']}</b></th>
			<th width="10%"><b>{$ibforums->lang['pms_cc_users']}</b></th>
			<th align="center" width="5%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.mutliact);" /></th>
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