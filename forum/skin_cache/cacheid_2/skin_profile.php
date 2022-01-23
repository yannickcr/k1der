<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 2                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:38 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_profile {

//===========================================================================
// custom_field
//===========================================================================
function custom_field($title="",$value="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row2" valign="top"><b>$title</b></td>
	<td class="row1">$value</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// get_photo
//===========================================================================
function get_photo($show_photo="",$show_width="",$show_height="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<img src="$show_photo" alt="User Photo" $show_width $show_height />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// no_custom_information
//===========================================================================
function no_custom_information() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td colspan="2" align="center" class="row2">{$ibforums->lang['no_info']}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_card
//===========================================================================
function show_card($name="",$photo="",$info="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language="javascript" type="text/javascript">
<!--
 function redirect_to(where, closewin)
 {
 	opener.location= '{$ibforums->base_url}' + where;
 	
 	if (closewin == 1)
 	{
 		self.close();
 	}
 }
//-->
</script>
<div class="borderwrap" style="text-align: left;">
	<div class="maintitle">$name</div>
	<table cellspacing="1" class='row1'>
		<tr>
			<th><b>{$ibforums->lang['head_contact']}</b></th>
			<th><b>{$ibforums->lang['photo_title']}</b></th>
		</tr>
		<tr>
			<td valign="middle" class="nopad">
				<table cellspacing="1">
EOF;
//startif
if ( $ibforums->vars['blog_default_view'] != "" and $info['has_blog'] == 1 )
{
$IPBHTML .= <<<EOF
<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['myblog']}</td>
						<td width="100%" class="row1"><b><a href='javascript:redirect_to("&amp;automodule=blog&amp;cmd=showblog&amp;mid={$info['mid']}",1);'>{$ibforums->lang['click_here']}</a></b></td>
					</tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['email']}</td>
						<td width="100%" class="row1"><b>{$info['email']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['website_addr']}</td>
						<td width="100%" class="row1"><b>{$info['website']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['aim']}</td>
						<td width="100%" class="row1"><{PRO_AIM}> <b>{$info['aim_name']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['icq']}</td>
						<td width="100%" class="row1"><{PRO_ICQ}> <b>{$info['icq_number']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['yahoo']}</td>
							<td width="100%" class="row1"><{PRO_YIM}> <b>{$info['yahoo']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['msn']}</td>
						<td width="100%" class="row1"><{PRO_MSN}> <b>{$info['msn_name']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['pm']}</b></td>
						<td class="row1"><b><a href="javascript:redirect_to('&act=Msg&CODE=4&MID={$info['mid']}', 1);">{$ibforums->lang['click_here']}</a></b></td>
					</tr>
				</table>
			</td>
			<td valign="middle" class="row1" align="center">$photo</td>
		</tr>
		<tr>
			<td class="formbuttonrow" colspan="2"><a href="{$ibforums->base_url}act=Profile&amp;CODE=showcard&amp;MID={$info['mid']}&amp;download=1">{$ibforums->lang['ac_download']}</a> &middot; <a href="javascript:self.close();">{$ibforums->lang['ac_close']}</a></td>
		</tr>
		<tr>
			<td class="catend" colspan="2"><!-- no content --></td>
		</tr>
	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_card_download
//===========================================================================
function show_card_download($name="",$photo="",$info="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<html>
	<head>
		<title>$name</title>
		<style type="text/css">
			<!--CSS-->
		</style>
		<script language="javascript" type="text/javascript">
		<!--
		function redirect_to(where, closewin)
		{
		  document.location= "{$ibforums->base_url}" + where;
		  
		  if (closewin == 1)
		  {
			  self.close();
		  }
		}
		//-->
		</script>
	</head>
	<body>
	<div class="borderwrap" style="text-align: left;">
	<div class="maintitle">$name</div>
	<table cellspacing="1" class='row1'>
		<tr>
			<th><b>{$ibforums->lang['head_contact']}</b></th>
			<th><b>{$ibforums->lang['photo_title']}</b></th>
		</tr>
		<tr>
			<td valign="middle" class="nopad">
				<table cellspacing="1">
EOF;
//startif
if ( $ibforums->vars['blog_default_view'] != "" and $info['has_blog'] == 1 )
{
$IPBHTML .= <<<EOF
<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['myblog']}</td>
						<td width="100%" class="row1"><b><a href='javascript:redirect_to("&amp;automodule=blog&amp;cmd=showblog&amp;mid={$info['mid']}",1);'>{$ibforums->lang['click_here']}</a></b></td>
					</tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['email']}</td>
						<td width="100%" class="row1"><b>{$info['email']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['website_addr']}</td>
						<td width="100%" class="row1"><b>{$info['website']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['aim']}</td>
						<td width="100%" class="row1"><{PRO_AIM}> <b>{$info['aim_name']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['icq']}</td>
						<td width="100%" class="row1"><{PRO_ICQ}> <b>{$info['icq_number']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['yahoo']}</td>
							<td width="100%" class="row1"><{PRO_YIM}> <b>{$info['yahoo']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['msn']}</td>
						<td width="100%" class="row1"><{PRO_MSN}> <b>{$info['msn_name']}</b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="row1">{$ibforums->lang['pm']}</b></td>
						<td class="row1"><b><a href="javascript:redirect_to('&act=Msg&CODE=4&MID={$info['mid']}', 1);">{$ibforums->lang['click_here']}</a></b></td>
					</tr>
				</table>
			</td>
			<td valign="middle" class="row1" align="center">$photo</td>
		</tr>
		<tr>
			<td class="catend" colspan="2"><!-- no content --></td>
		</tr>
	</table>
   </div>
	</body>
</html>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_photo
//===========================================================================
function show_photo($name="",$photo="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div id="photowrap">
	<div id="phototitle">$name</div>
	<div id="photoimg">$photo</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_profile
//===========================================================================
function show_profile($info="",$auth_key="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script language="Javascript" type="text/javascript">
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
	
		var Win = window.open( url, name, "width="+width+",height="+height+",top="+Y+",left="+X+",resizable="+resize+",scrollbars="+scroll+",location=no,directories=no,status=no,menubar=no,toolbar=no");
	 }
	//-->
</script>
<div class="borderwrap">
	<div class="maintitle">{$ibforums->lang['head_title']}: {$info['name']}</div>
	<table cellspacing="1">
		<tr>
			<td width="1%" nowrap="nowrap" valign="top" class="row1">
				<div id="profilename">{$info['name']}</div>
				<br />
				<div>{$info['avatar']}</div>
				<div>{$info['member_title']}</div>
				<div>{$info['member_rank_img']}</div>
				<br />
				<div class="postdetails">
					{$ibforums->lang['mgroup']}: {$info['group_title']}<br />
					{$ibforums->lang['joined']}: {$info['joined']}
				</div>
				<!--{WARN_LEVEL}-->
			</td>
			<td width="30%" align="center" nowrap="nowrap" valign="top" class="row1">
				<fieldset>
					<legend><b>{$ibforums->lang['profile_options']}</b></legend>
					<table cellspacing="0">
						<tr>
							<td width="1%"><{PRO_ITEM}></td>
							<td width="99%"><a href="{$ibforums->base_url}act=Msg&amp;CODE=02&amp;MID={$info['mid']}">{$ibforums->lang['add_to_contact']}</a></td>
						</tr>
						<tr>
							<td width="1%"><{PRO_ITEM}></td>
							<td width="99%"><a href="{$ibforums->base_url}act=Search&amp;CODE=getalluser&amp;mid={$info['mid']}">{$ibforums->lang['find_posts']}</a></td>
						</tr>
						<tr>
							<td width="1%"><{PRO_ITEM}></td>
							<td width="99%"><a href="{$ibforums->base_url}act=Search&amp;CODE=gettopicsuser&amp;mid={$info['mid']}">{$ibforums->lang['find_member_topics']}</a></td>
						</tr>
EOF;
//startif
if ( $ibforums->member['id'] != 0 )
{
$IPBHTML .= <<<EOF
<tr>
							<td width="1%"><{PRO_ITEM}></td>
							<td width="99%"><a href="{$ibforums->base_url}act=usercp&amp;CODE=ignore&amp;uid={$info['mid']}">{$ibforums->lang['ignore_user']}</a></td>
						</tr>
EOF;
}//endif
//startif
if ( $ibforums->member['g_is_supmod'] == 1 )
{
$IPBHTML .= <<<EOF
<tr>
							<td width="1%"><{PRO_ITEM}></td>
							<td width="99%"><a href="{$ibforums->base_url}act=mod&amp;CODE=editmember&amp;auth_key={$auth_key}&amp;mid={$info['mid']}">{$ibforums->lang['supmod_edit_member']}</a></td>
						</tr>
EOF;
}//endif

$IPBHTML .= <<<EOF
					</table>
				</fieldset>
			</td>
			<td width="1%" align="right" class="row1">{$info['photo']}</td>
		</tr>
	</table>
</div>
<br />
<table cellspacing="1">
	<tr>
		<!-- STATS -->
		<td width="50%" valign="top" style="padding-left: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle">{$ibforums->lang['active_stats']}</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['user_local_time']}</b></td>
					<td class="row1">{$info['local_time']}</td>
				</tr>
				<tr>
					<td class="row2" width="30%" valign="top"><b>{$ibforums->lang['total_posts']}</b></td>
					<td width="70%" class="row1"><b>{$info['posts']}</b>
					<br />( {$info['posts_day']} {$ibforums->lang['posts_per_day']} / {$info['total_pct']}% {$ibforums->lang['total_percent']} )
					</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['fav_forum']}</b></td>
					<td class="row1"><a href="{$info['base_url']}act=SF&amp;f={$info['fav_id']}"><b>{$info['fav_forum']}</b></a><br />( {$info['fav_posts']} {$ibforums->lang['fav_posts']} / {$info['percent']}% {$ibforums->lang['fav_percent']} )</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['last_active']}</b></td>
					<td class="row1">{$info['last_active']}</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['last_click']}</b></td>
					<td class="row1">{$info['online_status_indicator']} {$info['online_extra']}</td>
				</tr>
			</table>
		</td>
		<!-- Communication -->
		<td width="50%" valign="top" style="padding-right: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle">{$ibforums->lang['communicate']}</td>
				</tr>
				<tr>
					<td width="1%" class="row1"><{PRO_AIM}></td>
					<td width="99%" class="row2">{$info['aim_name']}</td>
				</tr>
				<tr>
					<td width="1%" class="row1"><{PRO_YIM}></td>
					<td width="99%" class="row2">{$info['yahoo']}</td>
				</tr>
				<tr>
					<td width="1%" class="row1"><{PRO_ICQ}></td>
					<td width="99%" class="row2">{$info['icq_number']}</td>
				</tr>
				<tr>
					<td width="1%" class="row1"><{PRO_MSN}></td>
					<td width="99%" class="row2">{$info['msn_name']}</td>
				</tr>
				<tr>
					<td width="1%" class="row1"><{PRO_CONTACT}></td>
					<td width="99%" class="row2"><a href="{$info['base_url']}act=Msg&amp;CODE=4&amp;MID={$info['mid']}">{$ibforums->lang['pm']}</a></td>
				</tr>
				<tr>
					<td width="1%" class="row1"><{PRO_CONTACT}></td>
					<td width="99%" class="row2">{$info['email']}</td>
				</tr>
			</table>
		</td>
		<!-- END CONTENT ROW 1 -->
		<!-- information -->
	</tr>
	<tr>
		<td width="50%" valign="top" style="padding-left: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle">{$ibforums->lang['info']}</td>
				</tr>
EOF;
//startif
if ( $ibforums->vars['blog_default_view'] != "" and $info['has_blog'] == 1 )
{
$IPBHTML .= <<<EOF
<tr>
						<td class="row2" width="30%" valign="top"><b>{$ibforums->lang['myblog']}</b></td>
						<td width="70%" class="row1"><a href='{$ibforums->base_url}automodule=blog&amp;cmd=showblog&amp;mid={$info['mid']}'>{$ibforums->lang['click_here']}</a></td>
					</tr>
EOF;
}//endif

$IPBHTML .= <<<EOF
				<tr>
					<td class="row2" width="30%" valign="top"><b>{$ibforums->lang['homepage']}</b></td>
					<td width="70%" class="row1">{$info['homepage']}</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['birthday']}</b></td>
					<td class="row1">{$info['birthday']}</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['location']}</b></td>
					<td class="row1">{$info['location']}</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>{$ibforums->lang['interests']}</b></td>
					<td class="row1">{$info['interests']}</td>
				</tr>
			</table>
		</td>
		<!-- Profile -->
		<td width="50%" valign="top" style="padding-right: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle">{$ibforums->lang['head_addit']}</td>
				</tr>
				<!--{CUSTOM.FIELDS}-->
			</table>
		</td>
	</tr>
</table>
EOF;
//startif
if ( $info['signature'] != "" )
{
$IPBHTML .= <<<EOF
<br />
<div class="borderwrap">
<table cellspacing="1">
	<tr>
		<td class="maintitle">{$ibforums->lang['siggie']}</td>
	</tr>
	<tr>
		<td class="row2">
			<div class="signature">{$info['signature']}</div>
		</td>
	</tr>
</table>
</div>
EOF;
}//endif
$IPBHTML .= <<<EOF
<!--MEM OPTIONS-->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// user_edit
//===========================================================================
function user_edit($info="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br />
<div class="borderwrap">
	<div class="formsubtitle" align="center" style="padding: 5px;">
		<a href="{$info['base_url']}act=UserCP&amp;CODE=22">{$ibforums->lang['edit_my_sig']}</a> &middot;
		<a href="{$info['base_url']}act=UserCP&amp;CODE=24">{$ibforums->lang['edit_avatar']}</a> &middot;
		<a href="{$info['base_url']}act=UserCP&amp;CODE=01">{$ibforums->lang['edit_profile']}</a>
	</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_level
//===========================================================================
function warn_level($mid="",$img="",$percent="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
Warn: (<a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$percent}</a>%) <a href="{$ibforums->base_url}act=warn&amp;type=minus&amp;mid={$mid}" title="{$ibforums->lang['tt_warn_minus']}"><{WARN_MINUS}></a>{$img}<a href="{$ibforums->base_url}act=warn&amp;type=add&amp;mid={$mid}" title="{$ibforums->lang['tt_warn_add']}"><{WARN_ADD}></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_level_no_mod
//===========================================================================
function warn_level_no_mod($mid="",$img="",$percent="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
{$ibforums->lang['warn_level']}: (<a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$percent}</a>%) {$img}
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_level_rating
//===========================================================================
function warn_level_rating($mid="",$level="",$min=0,$max=10) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
{$ibforums->lang['warn_level']}: <a href="{$ibforums->base_url}act=warn&amp;type=minus&amp;mid={$mid}" title="{$ibforums->lang['tt_warn_minus']}"><{WARN_MINUS}></a> &lt;&nbsp;$min ( <a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$level}</a> ) $max&nbsp;&gt; <a href="{$ibforums->base_url}act=warn&amp;type=add&amp;mid={$mid}" title="{$ibforums->lang['tt_warn_add']}"><{WARN_ADD}></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// warn_level_rating_no_mod
//===========================================================================
function warn_level_rating_no_mod($mid="",$level="",$min=0,$max=10) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&lt;&nbsp;$min ( <a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$level}</a> ) $max&nbsp;&gt;
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