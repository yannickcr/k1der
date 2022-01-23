<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:42 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_forum {

//===========================================================================
// announcement_row
//===========================================================================
function announcement_row($data="",$inforum=1) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row1"><{B_PIN}></td>
	<td class="row1">&nbsp;</td>
	<td class="row1"><b>{$ibforums->lang['announce_row']}: <a href="{$ibforums->base_url}act=announce&amp;f={$data['forum_id']}&amp;id={$data['announce_id']}">{$data['announce_title']}</a></b></td>
	<td class="row1" align="center">-</td>
	<td class="row1" align="center"><a href="{$ibforums->base_url}showuser={$data['member_id']}">{$data['member_name']}</a></td>
	<td class="row1" align="center">{$data['announce_views']}</td>
	<td class="row1"><span class="desc">{$data['announce_start']}
	<br /><a href="{$ibforums->base_url}act=announce&amp;f={$data['forum_id']}&amp;id={$data['announce_id']}">{$ibforums->lang['last_post_by']}</a> <b><a href="{$ibforums->base_url}showuser={$data['member_id']}">{$data['member_name']}</a></b></span>
	</td>
EOF;
//startif
if ( $ibforums->member['is_mod'] and $inforum == 1 )
{
$IPBHTML .= <<<EOF
<td align="center" class="row1">&nbsp;</td>
EOF;
}//endif
$IPBHTML .= <<<EOF
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// announcement_wrap
//===========================================================================
function announcement_wrap($announce="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="darkrow1" colspan="8"><b>{$ibforums->lang['announce_start']}</b></td>
</tr>
$announce
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_active_users
//===========================================================================
function forum_active_users($active=array()) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="formsubtitle" style="padding: 4px;">{$ibforums->lang['active_users_title']} ({$ibforums->lang['active_users_detail']})</div>
	<div class="row1" style="padding: 4px;">{$ibforums->lang['active_users_members']} {$active['names']}</div>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_password_log_in
//===========================================================================
function forum_password_log_in($fid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}" method="post">
	<input type="hidden" name="act" value="SF" />
	<input type="hidden" name="f" value="$fid" />
	<input type="hidden" name="L" value="1" />
	<div class="borderwrap">
		<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['need_password']}</div>
		<div class="tablepad">{$ibforums->lang['need_password_txt']}</div>
		<div class="tablepad">
			<b>{$ibforums->lang['enter_pass']}</b>
			<input type="password" size="20" name="f_password" />
		</div>
		<div class="pformstrip" align="center"><input type="submit" value="{$ibforums->lang['f_pass_submit']}" class="forminput" /></div>
	</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forums_attachments_bottom
//===========================================================================
function forums_attachments_bottom() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- inbox folder -->
	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forums_attachments_row
//===========================================================================
function forums_attachments_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr id="{$data['attach_id']}">
	<td align="center" class="row1"><img src="{$ibforums->vars['mime_img']}/{$data['image']}" alt="{$ibforums->lang['attached_file']}" /></td>
	<td class="row2">
		<a href="{$ibforums->base_url}act=Attach&amp;type=post&amp;id={$data['attach_id']}" title="{$data['attach_file']}" target="_blank">{$data['short_name']}</a>
		<div class="desc">( {$ibforums->lang['attach_hits']}: {$data['attach_hits']} )<br />( {$ibforums->lang['attach_post_date']} {$data['attach_date']} )</div>
	</td>
	<td align="center" class="row1">{$data['real_size']}</td>
	<td class="row2" align="center"><a href="#" onclick="opener.location='{$ibforums->base_url}showtopic={$data['tid']}&amp;view=findpost&amp;p={$data['pid']}';">{$data['pid']}</a></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forums_attachments_top
//===========================================================================
function forums_attachments_top($title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- inbox folder -->
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['attach_page_title']}: $title</div>
	<table cellspacing="1">
		<tr>
			<th width="2%">&nbsp;</th>
			<th width="73%"><b>{$ibforums->lang['attach_title']}</b></th>
			<th width="5%">{$ibforums->lang['attach_size']}</b></a></th>
			<th width="15%"><b>{$ibforums->lang['attach_post']}</b></th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mm_end
//===========================================================================
function mm_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- end multimod -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mm_entry
//===========================================================================
function mm_entry($id="",$title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<option value="t_{$id}">--  $title</option>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mm_start
//===========================================================================
function mm_start() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<option value="-1">------------------------------</option>
<option value="-1">{$ibforums->lang['mm_title']}</option>
<option value="-1">------------------------------</option>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// OLD_render_pinned_row
//===========================================================================
function OLD_render_pinned_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Begin Pinned Topic Entry {$data['tid']} -->
<tr> 
	<td align="center" class="row2">{$data['folder_img']}</td>
	<td align="center" class="row2">{$data['topic_icon']}</td>
	<td class="row2">
	<div>
		{$data['go_new_post']}{$data['prefix']} {$data['attach_img']}<a href="{$ibforums->base_url}showtopic={$data['tid']}" title="{$ibforums->lang['topic_started_on']} {$data['start_date']}">{$data['title']}</a> {$data[PAGES]}
		<div class="desc">{$data['description']}</div>
	</div>
	<td align="center" class="row2">{$data['posts']}</td>
	<td align="center" class="row2">{$data['starter']}</td>
	<td align="center" class="row2">{$data['views']}</td>
	<td class="row2"><span class="desc">{$data['last_post']}<br /><a href="{$ibforums->base_url}showtopic={$data['tid']}&amp;view=getlastpost">{$data['last_text']}</a> <b>{$data['last_poster']}</b></span></td>
</tr>
<!-- End Pinned Topic Entry {$data['tid']} -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// page_title
//===========================================================================
function page_title($title="",$pages="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div><span class="pagetitle">$title</span>$pages</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagetop
//===========================================================================
function pagetop($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language="javascript" type="text/javascript">
<!--
var unselectedbutton = "{$ibforums->vars['img_url']}/topic_unselected.gif";
var selectedbutton   = "{$ibforums->vars['img_url']}/topic_selected.gif";
var lang_gobutton    = "{$ibforums->lang['f_go']}";
var lang_suredelete  = "{$ibforums->lang['cp_js_delete']}";
//-->
</script>
<script type="text/javascript" src="jscripts/ipb_forum.js"></script>   
<!--IBF.SUBFORUMS-->
<table cellspacing="0">
	<tr>
		<td style='padding-left:0px' width="60%">{$data['SHOW_PAGES']}</td>
		<td class='nopad' style='padding:0px 0px 5px 0px' align="right" nowrap="nowrap"><a href="{$ibforums->base_url}act=Post&amp;CODE=00&amp;f={$data['id']}"><{A_POST}></a>{$data[POLL_BUTTON]}</td>
	</tr>
</table>
<div class="borderwrap">
	<div class="maintitle">
	<p class="expand"><a href="{$ibforums->base_url}act=Login&amp;CODE=04&amp;f={$data['id']}">{$ibforums->lang['mark_as_read']}</a> &middot; <a href="{$ibforums->base_url}act=usercp&amp;CODE=start_subs&amp;method=forum&amp;fid={$data['id']}">{$ibforums->lang['ft_title']}</a>
	</p>
	<p><{CAT_IMG}>&nbsp;{$data['name']}</p>
</div>
<table cellspacing="1">
	<tr> 
		<th align="center">&nbsp;</th>
		<th align="center">&nbsp;</th>
		<th width="50%" nowrap="nowrap">{$ibforums->lang['h_topic_title']}</th>
		<th width="7%" align="center" nowrap="nowrap">{$ibforums->lang['h_replies']}</th>
		<th width="14%" align="center" nowrap="nowrap">{$ibforums->lang['h_topic_starter']}</th>
		<th width="7%" align="center" nowrap="nowrap">{$ibforums->lang['h_hits']}</th>
		<th width="22%" nowrap="nowrap">{$ibforums->lang['h_last_action']}</th>
EOF;
//startif
if ( $ibforums->member['is_mod'] )
{
$IPBHTML .= <<<EOF
<th width="1%" align="center">&nbsp;</th>
EOF;
}//endif
$IPBHTML .= <<<EOF
	</tr>
	<!-- Forum page unique top -->
	<!--IBF.ANNOUNCEMENTS-->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_show_lastpage
//===========================================================================
function pagination_show_lastpage($tid="",$st="",$page="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<span class="minipagelinklast"><a href="{$ibforums->base_url}showtopic={$tid}&amp;st=$st">&raquo; $page</a></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_show_page
//===========================================================================
function pagination_show_page($tid="",$st="",$page="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<span class="minipagelink"><a href="{$ibforums->base_url}showtopic={$tid}&amp;st=$st">$page</a></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_wrap_pages
//===========================================================================
function pagination_wrap_pages($tid="",$pages="",$posts="",$perpage="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;<a href="javascript:multi_page_jump('{$ibforums->base_url}showtopic={$tid}', $posts, $perpage );" title="multipage jump"><img src='{$ibforums->vars['img_url']}/pages_icon.gif' alt='*' border='0' /></a> $pages
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// render_forum_row
//===========================================================================
function render_forum_row($data="",$class1='row2',$class2='row1',$classposts='row2',$inforum=0) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Begin Topic Entry {$data['tid']} -->
<tr> 
	<td align="center" class="$class2">{$data['folder_img']}</td>
	<td align="center" class="$class2">{$data['topic_icon']}</td>
	<td class="$class2" valign="middle">
		<div>
			{$data['go_new_post']}{$data['prefix']} {$data['attach_img']}<a href="{$ibforums->base_url}showtopic={$data['tid']}" title="{$ibforums->lang['topic_started_on']} {$data['start_date']}">{$data['title']}</a> {$data[PAGES]}
			<div class="desc">{$data['description']}</div>
		</div>
	</td>
	<td align='center' class="$classposts">
     {$data['posts']}
EOF;
//startif
if ( $data['_hasqueued'] and $inforum == 1 )
{
$IPBHTML .= <<<EOF
&nbsp;<a href="{$ibforums->base_url}showtopic={$data['tid']}&amp;modfilter=invisible_posts"><{BC_QUEUED_POSTS}></a>
EOF;
}//endif

$IPBHTML .= <<<EOF
    </td>
	<td align="center" class="$class1">{$data['starter']}</td>
	<td align="center" class="$class1">{$data['views']}</td>
	<td class="$class1"><span class="lastaction">{$data['last_post']}<br /><a href="{$ibforums->base_url}showtopic={$data['tid']}&amp;view=getlastpost">{$data['last_text']}</a> <b>{$data['last_poster']}</b></span></td>
EOF;
//startif
if ( $ibforums->member['is_mod'] and $inforum == 1 and $data['tidon'] == 1 )
{
$IPBHTML .= <<<EOF
<td align="center" class="$class1"><a href="#" title="{$ibforums->lang['click_for_mod']}" onclick="forum_toggle_tid('{$data['real_tid']}'); return false;"><img name="img{$data['real_tid']}" src="{$ibforums->vars['img_url']}/topic_selected.gif" /></a></td>
EOF;
}//endif
//startif
if ( $ibforums->member['is_mod'] and $inforum == 1 and $data['tidon'] == 0 )
{
$IPBHTML .= <<<EOF
<td align="center" class="$class1"><a href="#" title="{$ibforums->lang['click_for_mod']}" onclick="forum_toggle_tid('{$data['real_tid']}'); return false;"><img name="img{$data['real_tid']}" src="{$ibforums->vars['img_url']}/topic_unselected.gif" /></a></td>
EOF;
}//endif
$IPBHTML .= <<<EOF
</tr>
    <!-- End Topic Entry {$data['tid']} -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// render_pinned_end
//===========================================================================
function render_pinned_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- END PINNED -->
<tr>
   <td class="darkrow1" colspan="8"><b>{$ibforums->lang['regular_topics']}</b></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// render_pinned_start
//===========================================================================
function render_pinned_start($show=0) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!--PINNED-->
EOF;
//startif
if ( $show == 1 )
{
$IPBHTML .= <<<EOF
<tr>
	<td class="darkrow1" colspan="8"><b>{$ibforums->lang['pinned_start']}</b></td>
</tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
    <!-- END PINNED -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_no_matches
//===========================================================================
function show_no_matches() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr> 
	<td class="row2" colspan="8" align="center">
		<br />
		<b>{$ibforums->lang['no_topics']}</b>
		<br /><br />
	</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_page_jump
//===========================================================================
function show_page_jump($total="",$pp="",$qe="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="javascript:multi_page_jump( $total, $pp, '$qe' )" title="{$ibforums->lang['tpl_jump']}">{$ibforums->lang['multi_page_forum']}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_rules
//===========================================================================
function show_rules($rules="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<h3><{CAT_IMG}>&nbsp;{$rules['title']}, <a href="{$ibforums->base_url}act=SF&amp;f={$rules['fid']}">{$ibforums->lang['back_to_forum']}</a></h3>
	<p>{$rules['body']}</p>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// tableend
//===========================================================================
function tableend($data="",$auth_key="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td colspan="8" class="darkrow1">
				<table cellspacing="0">
					<tr>
						<td width="50%" class="nopad">
							<form action="{$ibforums->base_url}" method="post" name="search">
								<input type="hidden" name="forums" value="{$data['id']}" />
								<input type="hidden" name="cat_forum" value="forum" />
								<input type="hidden" name="act" value="Search" />
								<input type="hidden" name="joinname" value="1" />
								<input type="hidden" name="CODE" value="01" />
								<input type="text" size="30" name="keywords" value="{$ibforums->lang['enter_keywords']}" onfocus="this.value = '';" /> <input type="submit" value="{$ibforums->lang['search_forum']}" class="button" />
							</form>
						</td>
EOF;
//startif
if ( $ibforums->member['is_mod'] )
{
$IPBHTML .= <<<EOF
<td width="50%" align="right" nowrap="nowrap" class="nopad">
							<form name="modform" method="post" action="{$ibforums->base_url}act=mod&CODE=topicchoice&f={$data['id']}" onsubmit="return checkdelete();">
								<input type="hidden" name="act" value="mod" />
								<input type="hidden" name="auth_key" value="{$auth_key}" />
								<input type="hidden" name="modfilter" value="{$ibforums->input['modfilter']}" />
								<input type="hidden" value="{$ibforums->input['selectedtids']}" name="selectedtids"" />
								<select name="tact">
									<option value="close">{$ibforums->lang['cpt_close']}</option>
									<option value="open">{$ibforums->lang['cpt_open']}</option>
									<option value="pin">{$ibforums->lang['cpt_pin']}</option>
									<option value="unpin">{$ibforums->lang['cpt_unpin']}</option>
									<option value="move">{$ibforums->lang['cpt_move']}</option>
									<option value="merge">{$ibforums->lang['cpt_merge']}</option>
									<option value="delete">{$ibforums->lang['cpt_delete']}</option>
									<option value="approve">{$ibforums->lang['cpt_approve']}</option>
									<option value="unapprove">{$ibforums->lang['cpt_unapprove']}</option>
									<!--IBF.MMOD-->
								</select>&nbsp;
								<input type="submit" name="gobutton" value="{$ibforums->lang['f_go']}" class="button" />
							</form>
						</td>
EOF;
}//endif

$IPBHTML .= <<<EOF
					</tr>
				</table>
			</td>
		</tr>
		<tr> 
			<td class="catend" colspan="8"><!-- no content --></td>
		</tr>
	</table>
</div>
<table cellspacing="0">
	<tr>
		<td style='padding-left:0px' width="50%" nowrap="nowrap">{$data['SHOW_PAGES']}</td>
		<td class='nopad' style='padding:5px 0px 5px 0px' align="right" width="50%"><a href="{$ibforums->base_url}act=Post&amp;CODE=00&amp;f={$data['id']}"><{A_POST}></a>{$data[POLL_BUTTON]}</td>
	</tr>
</table>
<!--IBF.FORUM_ACTIVE-->
<div class="activeusers">
	<div class="row2">
		<table cellspacing="0">
			<tr>
				<td width="5%" nowrap="nowrap">
					<{B_NEW}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_open_new']}</span>
					<br /><{B_NORM}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_open_no']}</span>
					<br /><{B_HOT}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_hot_new']}</span>
					<br /><{B_HOT_NN}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_hot_no']}</span>&nbsp;
				</td>
				<td width="5%" nowrap="nowrap">
					<{B_POLL}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_poll']}</span>
					<br /><{B_POLL_NN}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_poll_no']}</span>
					<br /><{B_LOCKED}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_locked']}</span>
					<br /><{B_MOVED}>&nbsp;&nbsp;<span class="desc">{$ibforums->lang['pm_moved']}</span>
				</td>
				<td align="right" width="90%">
					{$data[FORUM_JUMP]}<br /><br />
					<form action="{$ibforums->base_url}act=SF&amp;f={$data['id']}&amp;st={$ibforums->input['st']}" method="post">
						<select name="sort_key">{$ibforums->show['sort_by']}</select>
						<select name="sort_by">{$ibforums->show['sort_order']}</select>
						<select name="prune_day">{$ibforums->show['sort_prune']}</select>
						<select name="topicfilter">{$ibforums->show['topic_filter']}</select>
						<input type="submit" value="{$ibforums->lang['sort_submit']}" class="button" />
					</form>
				</td>
			</tr>
		</table>
	</div>
</div>
EOF;
//startif
if ( $ibforums->member['is_mod'] )
{
$IPBHTML .= <<<EOF
<br />
<div align="center">
	<a href="{$ibforums->base_url}showforum={$data['id']}&amp;modfilter=invisible_topics">{$ibforums->lang['mod_showallinvisible']}</a>
	&middot;
	<a href="{$ibforums->base_url}showforum={$data['id']}&amp;modfilter=invisible_posts">{$ibforums->lang['mod_showallposts']}</a>
	&middot;
	<a href="{$ibforums->base_url}act=mod&amp;CODE=resync&amp;f={$data['id']}&amp;auth_key={$auth_key}">{$ibforums->lang['mod_resync']}</a>
	&middot;
	<a href="javascript:PopUp('{$ibforums->base_url}act=mod&amp;CODE=prune_start&amp;f={$data['id']}&amp;auth_key={$auth_key}', 'PRUNE', 600,500)">{$ibforums->lang['mod_prune']}</a>
</div>
EOF;
}//endif
$IPBHTML .= <<<EOF
<br clear="all" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// topic_attach_icon
//===========================================================================
function topic_attach_icon($tid="",$count=0) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="#" onclick="PopUp('{$ibforums->base_url}act=attach&amp;code=showtopic&amp;tid={$tid}', 'Attach{$tid}', 500,400); return false;" title="{$count} {$ibforums->lang['topic_attach']}"><{ATTACH_ICON}></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// who_link
//===========================================================================
function who_link($tid="",$posts="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="javascript:who_posted($tid);">$posts</a>
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