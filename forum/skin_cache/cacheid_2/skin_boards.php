<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 2                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:38 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_boards {

//===========================================================================
// active_user_links
//===========================================================================
function active_user_links() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<b>{$ibforums->lang['oul_show_more']}</b> <a href="{$ibforums->base_url}act=Online&amp;CODE=listall&amp;sort_key=click">{$ibforums->lang['oul_click']}</a>, <a href="{$ibforums->base_url}act=Online&amp;CODE=listall&amp;sort_key=name&amp;sort_order=asc&amp;show_mem=reg">{$ibforums->lang['oul_name']}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// ActiveUsers
//===========================================================================
function ActiveUsers($active="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="formsubtitle" colspan="2">$active[TOTAL] {$ibforums->lang['active_users']}</td>
		</tr>
		<tr>
			<td class="row1" width="1%"><{F_ACTIVE}></td>
			<td class="row2">
				<b>{$active[GUESTS]}</b> {$ibforums->lang['guests']}, <b>{$active[MEMBERS]}</b> {$ibforums->lang['public_members']} <b>{$active[ANON]}</b> {$ibforums->lang['anon_members']}
				<div class="thin">{$active[NAMES]}</div>
				{$active['links']}
			</td>
		</tr>
		<!--IBF.WHOSCHATTING-->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// birthdays
//===========================================================================
function birthdays($birthusers="",$total="",$birth_lang="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="formsubtitle" colspan="2">{$ibforums->lang['birthday_header']}</td>
		</tr>
		<tr>
			<td class="row1" width="1%"><{F_CALEN}></td>
			<td class="row2"><b>$total</b> $birth_lang<br />$birthusers</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// bottom_links
//===========================================================================
function bottom_links() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- no content -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// calendar_events
//===========================================================================
function calendar_events($events="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="formsubtitle" colspan="2">{$ibforums->lang['calender_f_title']}</td>
		</tr>
		<tr>
			<td class="row1" width="1%"><{F_CALEN}></td>
			<td class="row2">$events</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// catheader_expanded
//===========================================================================
function catheader_expanded($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap" style="display:{$data['div_fc']}" id="fc_{$data['id']}">
	<div class="maintitlecollapse">
		<p class="expand"><a href="javascript:togglecategory({$data['id']}, 0);"><{E_PLUS}></a></p>
		<p><{CAT_IMG}>&nbsp;<a href="{$ibforums->base_url}showforum={$data['id']}">{$data['name']}</a></p>
	</div>
</div>
<div class="borderwrap" style="display:{$data['div_fo']}" id="fo_{$data['id']}">
	<div class="maintitle">
		<p class="expand"><a href="javascript:togglecategory({$data['id']}, 1);"><{E_MINUS}></a></p>
		<p><{CAT_IMG}>&nbsp;<a href="{$ibforums->base_url}showforum={$data['id']}">{$data['name']}</a></p>
	</div>
	<table cellspacing="1">
		<tr> 
			<th colspan="2" width="66%">{$ibforums->lang['cat_name']}</th>
			<th align="center" width="7%">{$ibforums->lang['topics']}</th>
			<th align="center" width="7%">{$ibforums->lang['replies']}</th>
			<th width="35%">{$ibforums->lang['last_post_info']}</th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_all_cats
//===========================================================================
function end_all_cats() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_this_cat
//===========================================================================
function end_this_cat() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr> 
			<td class="catend" colspan="5"><!-- no content --></td>
		</tr>
	</table>
</div>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_img_with_link
//===========================================================================
function forum_img_with_link($img="",$id="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}act=Login&amp;CODE=04&amp;f={$id}" title="{$ibforums->lang['bi_markread']}">{$img}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_redirect_row
//===========================================================================
function forum_redirect_row($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Forum {$data['id']} entry -->
		<tr> 
			<td align="center" class="row2" width="1%"><{BR_REDIRECT}></td>
			<td class="row2"><b><a href="{$ibforums->base_url}showforum={$data['id']}" {$data['redirect_target']}>{$data['name']}</a></b><br /><span class="forumdesc">{$data['description']}</span></td>
			<td align="center" class="row1">--</td>
			<td align="center" class="row1">--</td>
			<td class="row1"><b>{$ibforums->lang['rd_hits']}:</b> {$data['redirect_hits']}</td>
		</tr>
<!-- End of Forum {$data['id']} entry -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forumrow
//===========================================================================
function forumrow($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr> 
			<td align="center" class="row2" width="1%">{$data['img_new_post']}</td>
			<td class="row2">{$data['_queued_img']}<b><a href="{$ibforums->base_url}showforum={$data['id']}">{$data['name']}</a></b><br /><span class="forumdesc">{$data['description']}{$data['show_subforums']}<br /><i>{$data['moderator']}</i></span>{$data['_queued_info']}</td>
			<td align="center" class="row1">{$data['topics']}</td>
			<td align="center" class="row1">{$data['posts']}</td>
			<td class="row1" nowrap="nowrap">{$data['last_unread']} <span>{$data['last_post']}<br /><b>{$ibforums->lang['in']}:</b>&nbsp;{$data['last_topic']}<br /><b>{$ibforums->lang['by']}:</b> {$data['last_poster']}</span></td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forumrow_lastunread_link
//===========================================================================
function forumrow_lastunread_link($fid="",$tid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}showtopic=$tid&amp;view=getlastpost" title="{$ibforums->lang['tt_golast']}"><{LAST_POST}></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// newslink
//===========================================================================
function newslink($fid="",$title="",$tid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br /><b>{$ibforums->vars['board_name']} {$ibforums->lang['newslink']}</b> <i><a href="{$ibforums->base_url}showtopic=$tid">$title</a></i>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagetop
//===========================================================================
function pagetop($lastvisit="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script type="text/javascript" src="jscripts/ipb_board.js"></script>
<table cellspacing="0" class="newslink">
	<tr>
		<td><b>{$ibforums->lang['welcome_back_text']}: <span>$lastvisit</span></b><!-- IBF.NEWSLINK --></td>
		<td align="right" valign="middle">
EOF;
//startif
if ( $ibforums->member['id'] == 0 )
{
$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}act=Login&amp;CODE=01&amp;CookieDate=1" method="post">
				<input type="text" size="20" name="UserName" onfocus="this.value=''" value="{$ibforums->lang['qli_name']}" />
				<input type="password" size="20" name="PassWord" onfocus="this.value=''" value="ibfrules" />
				<input class="button" type="image" src="{$ibforums->vars['img_url']}/login-button.gif" />
			</form>
EOF;
}//endif
else
{
$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}act=Search&amp;CODE=01&amp;forums=all" method="post">
				<input type="text" size="30" name="keywords"  onfocus="this.value=''" value="{$ibforums->lang['enter_search_words']}" />
				<input class="button" type="image" src="{$ibforums->vars['img_url']}/login-button.gif" />
			</form>
EOF;
}//endelse
$IPBHTML .= <<<EOF
		</td>
	</tr>
</table>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_queued_img
//===========================================================================
function show_queued_img($id="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}showforum={$id}&amp;modfilter=all"><{BC_QUEUED_POSTS}></a>&nbsp;
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_queued_info
//===========================================================================
function show_queued_info($posts=0,$topics=0) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br /><span class="desc"><b>Queued topics: $topics, posts: $posts</b></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_subforum_all_links
//===========================================================================
function show_subforum_all_links($links="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br /><b>{$ibforums->lang['sub_forum_title']}:</b> $links
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_subforum_link
//===========================================================================
function show_subforum_link($id="",$name="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}showforum={$id}">{$name}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// ShowStats
//===========================================================================
function ShowStats($text="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="formsubtitle" colspan="2">{$ibforums->lang['board_stats']}</td>
		</tr>
		<tr>
			<td class="row1" width="1%"><{F_STATS}></td>
			<td class="row2">$text<br />{$ibforums->lang['most_online']}</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// stats_footer
//===========================================================================
function stats_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr> 
			<td class="catend" colspan="2"><!-- no content --></td>
		</tr>
	</table>
</div>
<!-- Board Stats -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// stats_header
//===========================================================================
function stats_header($active="",$posts="",$members="",$show="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Board Stats -->
<div class="toplinks"><span><a href="{$ibforums->base_url}act=Login&amp;CODE=06">{$ibforums->lang['d_delete_cookies']}</a> &middot; <a href="{$ibforums->base_url}act=Login&amp;CODE=05">{$ibforums->lang['d_post_read']}</a></span></div>
<div class="borderwrap" style="display:{$show['div_fc']}" id="fc_stat">
	<div class="maintitle">
		<p class="expand"><a href="javascript:togglecategory('stat', 0);"><{E_PLUS}></a></p>
		<p><{CAT_IMG}>&nbsp;{$ibforums->lang['board_stats']}</p>
	</div>
	<div class="subtitlediv"><a href="{$ibforums->base_url}act=search&amp;CODE=getnew&amp;active=1&amp;lastdate=86400">{$ibforums->lang['sm_todays_posts']}</a> &middot; <a href="{$ibforums->base_url}act=Stats&amp;CODE=leaders">{$ibforums->lang['sm_forum_leaders']}</a> &middot; <a href="{$ibforums->base_url}act=Stats">{$ibforums->lang['sm_today_posters']}</a> &middot; <a href="{$ibforums->base_url}act=Members&amp;max_results=10&amp;sort_key=posts&amp;sort_order=desc">{$ibforums->lang['sm_all_posters']}</a></div>
	<div class="formsubtitle">
EOF;
//startif
if ( $ibforums->vars['show_totals'] == 1 )
{
$IPBHTML .= <<<EOF
<p class="members">$posts posts &#0124; $members members</p>
EOF;
}//endif
$IPBHTML .= <<<EOF
		<p>$active users online</p>
	</div>
</div>
<div class="borderwrap" style="display:{$show['div_fo']}" id="fo_stat">
	<div class="maintitle">
		<p class="expand"><a href="javascript:togglecategory('stat', 1);"><{E_MINUS}></a></p>
		<p><{CAT_IMG}>&nbsp;{$ibforums->lang['board_stats']}</p>
	</div>
	<table cellspacing="1">
		<tr>
			<th align="right" colspan="2"><a href="{$ibforums->base_url}act=search&amp;CODE=getnew&amp;active=1&amp;lastdate=86400">{$ibforums->lang['sm_todays_posts']}</a> &middot; <a href="{$ibforums->base_url}act=Stats&amp;CODE=leaders">{$ibforums->lang['sm_forum_leaders']}</a> &middot; <a href="{$ibforums->base_url}act=Stats">{$ibforums->lang['sm_today_posters']}</a> &middot; <a href="{$ibforums->base_url}act=Members&amp;max_results=10&amp;sort_key=posts&amp;sort_order=desc">{$ibforums->lang['sm_all_posters']}</a>
			</th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// subforum_img_with_link
//===========================================================================
function subforum_img_with_link($img="",$id="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}act=Login&amp;CODE=04&amp;f={$id}&amp;i=1" title="{$ibforums->lang['bi_markallread']}">{$img}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// subheader
//===========================================================================
function subheader($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
<div class="borderwrap" style="display:{$data['div_fc']}" id="fc_{$data['id']}">
	<div class="maintitlecollapse">
		<p class="expand"><a href="javascript:togglecategory({$data['id']}, 0);"><{E_PLUS}></a></p>
		<p><{CAT_IMG}>&nbsp;<a href="{$ibforums->base_url}showforum={$data['id']}">{$data['name']} {$ibforums->lang['sub_forum_title']}</a></p>
	</div>
</div>
<div class="borderwrap" style="display:{$data['div_fo']}" id="fo_{$data['id']}">
	<div class="maintitle">
		<p class="expand"><a href="javascript:togglecategory({$data['id']}, 1);"><{E_MINUS}></a></p>
		<p><{CAT_IMG}>&nbsp;<a href="{$ibforums->base_url}showforum={$data['id']}">{$data['name']} {$ibforums->lang['sub_forum_title']}</a></p>
	</div>
	<table cellspacing="1">
		<tr> 
			<th colspan="2" width="66%">{$ibforums->lang['cat_name']}</th>
			<th align="center" width="7%">{$ibforums->lang['topics']}</th>
			<th align="center" width="7%">{$ibforums->lang['replies']}</th>
			<th width="35%">{$ibforums->lang['last_post_info']}</th>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// whoschatting_empty
//===========================================================================
function whoschatting_empty($link="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="formsubtitle" colspan="2">{$ibforums->lang['whoschatting_total']} <a href="$link">{$ibforums->lang['whoschatting_loadchat']}</a></td>
		</tr>
		<tr>
			<td class="row1" width="1%"><{F_ACTIVE}></td>
			<td class="row2">
				<i>{$ibforums->lang['whoschatting_none']}</i>
			</td>
		</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// whoschatting_inline_link
//===========================================================================
function whoschatting_inline_link() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
{$ibforums->base_url}act=chat
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// whoschatting_popup_link
//===========================================================================
function whoschatting_popup_link() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
javascript:chat_pop({$ibforums->vars['chat_width']}, {$ibforums->vars['chat_height']});
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// whoschatting_show
//===========================================================================
function whoschatting_show($total="",$names="",$link="",$txt="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="formsubtitle" colspan="2">{$total} {$ibforums->lang['whoschatting_total']} <a href="$link">{$ibforums->lang['whoschatting_loadchat']}</a></td>
		</tr>
		<tr>
			<td class="row1" width="1%"><{F_ACTIVE}></td>
			<td class="row2">
				{$names}<div class="desc">$txt</div>
			</td>
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