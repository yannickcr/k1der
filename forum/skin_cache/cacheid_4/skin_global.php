<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 4                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:47 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_global {

//===========================================================================
// admin_link
//===========================================================================
function admin_link() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;| <b><a href='{$ibforums->vars['board_url']}/admin.{$ibforums->vars['php_ext']}' target='_blank'>{$ibforums->lang['admin_cp']}</a></b>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// board_offline
//===========================================================================
function board_offline($message="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table width='<{tbl_width}>' border='0' align='center' cellpadding='0' cellspacing='1' bgcolor='<{tbl_border}>'>
  <tr> 
    <td >
			<table width='100%' border='0' cellspacing='0' cellpadding='3'>
        <tr> 
          <td><img src='{$ibforums->vars['img_url']}/nav_m.gif' alt='' width='8' height='8'></td>
          <td width='100%' class='titlemedium'>{$ibforums->lang['offline_title']}</td>
        </tr>
      </table>
		</td>
  </tr>
  <tr> 
    <td class='mainbg'>
			<table width='100%' border='0' cellspacing='1' cellpadding='4'>
				<tr> 
          <td colspan='2' valign='top' class='post1'> <p>$message</p></td>
        </tr>
        <tr> 
          <td colspan='2' valign='top' class='posthead'>{$ibforums->lang['offline_login']}</td>
        </tr>
        <tr>
					<form action='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}' method='post'>
					<input type='hidden' name='act' value='Login'>
					<input type='hidden' name='CODE' value='01'>
					<input type='hidden' name='s' value='{$ibforums->session_id}'>
					<input type='hidden' name='referer' value=''>
					<input type='hidden' name='CookieDate' value='1'>
          <td class='row1'>{$ibforums->lang['erl_enter_name']}<br><img src='{$ibforums->vars['img_url']}/spacer.gif' alt='' width='180' height='1'></td>
          <td width='100%' class='row1'><input type='text' size='20' maxlength='64' name='UserName' class='forminput'></td>
        </tr>
        <tr> 
          <td class='row1'>{$ibforums->lang['erl_enter_pass']}</td>
          <td width='100%' class='row1'><input type='password' size='20' name='PassWord' class='forminput'></td>
        </tr>
        <tr> 
          <td colspan='2' align='center' class='titlefoot'><input type='submit' name='submit' value='{$ibforums->lang['erl_log_in_submit']}' class='forminput'></td>
					</form>
        </tr>
      </table></td>
  </tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// css_external
//===========================================================================
function css_external($css="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<style type="text/css" media="all">
	@import url(style_images/css_{$css}.css);
</style>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// css_inline
//===========================================================================
function css_inline($css="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<style type="text/css">
	{$css}
</style>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end_nav
//===========================================================================
function end_nav() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
	</td>
	<td align='right' valign='middle' nowrap>
	<table cellpadding='5' cellspacing='0' border='0' style='border:1px solid <{tbl_border}>' class='row2'>
	 <tr>
	   <td nowrap><a href='{$ibforums->base_url}&act=Help'>{$ibforums->lang['tb_help']}</a>
	   <span style='color:<{tbl_border}>'>|</span> <a href='{$ibforums->base_url}&act=Search&f={$ibforums->input['f']}'>{$ibforums->lang['tb_search']}</a>
	   <span style='color:<{tbl_border}>'>|</span> <a href='{$ibforums->base_url}&act=Members'>{$ibforums->lang['tb_mlist']}</a>
	   <span style='color:<{tbl_border}>'>|</span> <a href='../cal/index.php'>{$ibforums->lang['tb_calendar']}</a>
	   <!--IBF.CHATLINK-->
	   </td>
	 </tr>
	</table>
	
	</td>
  </tr>
</table>
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Error
//===========================================================================
function Error($message="",$ad_email_one="",$ad_email_two="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
	<script language='javascript'>
	<!--
	  function contact_admin() {
	  
	  	// Very basic spam bot stopper
	  		
	  	admin_email_one = '$ad_email_one';
	  	admin_email_two = '$ad_email_two';
	  	
	  	window.location = 'mailto:'+admin_email_one+'@'+admin_email_two+'?subject=Error on the forums';
	  	
	  }
	  
	  //-->
	  </script>
<table width='<{tbl_width}>' border='0' cellspacing='1' align='center' cellpadding='0' bgcolor='<{tbl_border}>'>
  <tr> 
    <td class='maintitle' > 
      <table width='100%' border='0' cellspacing='0' cellpadding='3'>
        <tr> 
          <td><img src='{$ibforums->vars['img_url']}/nav_m.gif' alt='' width='8' height='8'></td>
          <td width='100%' class='maintitle'><b>{$ibforums->lang['error_title']}</b></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td class='mainbg'> 
      <table width='100%' border='0' cellspacing='1' cellpadding='4'>
        <tr> 
          <td class='row1' valign='top'>
							{$ibforums->lang['exp_text']}<br><br>
						  <b>{$ibforums->lang['msg_head']}</b>
							<br><br>
							<span class='highlight'>$message</span>
							<br><br>
							<!-- IBF.LOG_IN_TABLE -->
							<br><br>
							<b>Useful Links:</b>
							<br><br>
			 &#149; <a href='{$ibforums->base_url}&act=Reg&CODE=10'>{$ibforums->lang['er_lost_pass']}</a><br>
              &#149; <a href='{$ibforums->base_url}&act=Reg&CODE=00'>{$ibforums->lang['er_register']}</a><br>
              &#149; <a href='{$ibforums->base_url}&act=Help&CODE=00'>{$ibforums->lang['er_help_files']}</a><br>
              &#149; <a href='javascript:contact_admin();'>{$ibforums->lang['er_contact_admin']}</a></p>
          </td>
        </tr>
        <tr> 
          <td class='titlefoot' align='center'>&lt; <a href='javascript:history.go(-1)'>{$ibforums->lang['error_back']}</a></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// error_log_in
//===========================================================================
function error_log_in($q_string="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}' method='post'>
     <input type='hidden' name='act' value='Login'>
     <input type='hidden' name='CODE' value='01'>
     <input type='hidden' name='s' value='{$ibforums->session_id}'>
     <input type='hidden' name='referer' value='$q_string'>
     <input type='hidden' name='CookieDate' value='1'>
     <table cellpadding='0' cellspacing='0' border='0' width='80%' bgcolor='<{tbl_border}>' align='center'>
        <tr>
            <td>
                <table cellpadding='3' cellspacing='1' border='0' width='100%'>
                <tr>
                <td align='left' colspan='2' class='titlemedium'>{$ibforums->lang['er_log_in_title']}</td>
                </tr>
                <tr>
                <td class='row1' width='40%'>{$ibforums->lang['erl_enter_name']}</td>
                <td class='row1'><input type='text' size='20' maxlength='64' name='UserName' class='forminput'></td>
                </tr>
                <tr>
                <td class='row1' width='40%'>{$ibforums->lang['erl_enter_pass']}</td>
                <td class='row1'><input type='password' size='20' name='PassWord' class='forminput'></td>
                </tr>
                <tr>
                <td class='row2' align='center' colspan='2'>
                <input type='submit' name='submit' value='{$ibforums->lang['erl_log_in_submit']}' class='forminput'>
                </td>
                </table>
             </td>
         </tr>
     </table>
   </form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// error_post_textarea
//===========================================================================
function error_post_textarea($post="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<h4>{$ibforums->lang['err_title']}</h4>
<p>{$ibforums->lang['err_expl']}</p>
<div class="fieldwrap">
	<h4>{$ibforums->lang['err_title']}</h4>
	<form name="mehform">
		<textarea cols="70" rows="5" name="saved" tabindex="2">$post</textarea>
	</form>
	<p class="formbuttonrow1"><input class="button" type="button" tabindex="1" value="{$ibforums->lang['err_select']}" onclick="document.mehform.saved.select()" /></p>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_show_rules_full
//===========================================================================
function forum_show_rules_full($rules="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Show FAQ/Forum Rules -->
<div class="borderwrap">
	<h3><{CAT_IMG}>&nbsp;{$rules['title']}</h3>
	<p>{$rules['body']}</p>
</div>
<!-- End FAQ/Forum Rules -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// forum_show_rules_link
//===========================================================================
function forum_show_rules_link($rules="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!-- Show FAQ/Forum Rules -->
<div class="ruleswrap">
	&nbsp;<{F_RULES}>&nbsp;<b><a href="{$ibforums->base_url}act=SR&amp;f={$rules['fid']}">{$rules['title']}</a></b>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// global_board_footer
//===========================================================================
function global_board_footer($time="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table cellspacing="0" id="gfooter">
	<tr>
		<td width="45%"><% SKINCHOOSER %> <% LANGCHOOSER %></td>
		<td width="10%" align="center" nowrap="nowrap"><a href="lofiversion/index.php<% LOFIVERSION %>"><b>{$ibforums->lang['global_lofi']}</b></a></td>
		<td width="45%" align="right" nowrap="nowrap"><% QUICKSTATS %>{$ibforums->lang['global_timeisnow']}: {$time}</td>
	</tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// global_board_header
//===========================================================================
function global_board_header($time="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
 <script language='JavaScript'>
     <!--
      function buddy_pop()
      {
           window.open('index.{$ibforums->vars['php_ext']}?act=buddy&s={$ibforums->session_id}','BrowserBuddy','width=200,height=450,resizable=yes,scrollbars=yes'); 
      }
     //-->
 </script>
<table width='<{tbl_width}>' border='0' align='center' cellpadding='0' cellspacing='1' bgcolor='<{tbl_border}>'>
  <tr> 
    <td align='left'>
     <table width='100%' border='0' cellspacing='0' cellpadding='0' background='{$ibforums->vars['img_url']}/header_tile.gif'>
      <tr>
       <td align='left'><a href='{$ibforums->base_url}' title='Board Home'><img src='{$ibforums->vars['img_url']}/logo.jpg' alt='-=K1der=- The Chocolat Effect' border='0'><img src='{$ibforums->vars['img_url']}/logo2.gif' alt='-=K1der=- The Chocolat Effect' border='0'></a></td>
       <td align='right' valign='middle' background='{$ibforums->vars['img_url']}/header_tile.gif'><!--IBF.BANNER--></td>
  	  </tr>
  	 </table>
  	</td>
  </tr>
  <tr> 
    <td class='row1'>
      <table width='100%' border='0' cellspacing='0' cellpadding='8'>
        <tr>
          <td width='100%' valign='middle' align='center'> <% MEMBER BAR %>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</td>
</table>
<br>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// global_lang_chooser
//===========================================================================
function global_lang_chooser($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}setlanguage=1" name="langselectorbox" method="post">
	<input type="hidden" name="langurlbits" value="act={$ibforums->input['act']}&CODE={$ibforums->input['CODE']}&t={$ibforums->input['t']}&f={$ibforums->input['f']}&st={$ibforums->input['st']}" />
	<select name="langid" onchange="chooselang(this)">
		<optgroup label="{$ibforums->lang['global_language']}">
			$data
		</optgroup>
	</select>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// global_quick_stats
//===========================================================================
function global_quick_stats($time="",$gzip="",$load="",$sql="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<img src='{$ibforums->vars['img_url']}/stat_time.gif' border='0' style='vertical-align:middle' /> {$time}sec
&nbsp;&nbsp;<img src='{$ibforums->vars['img_url']}/stat_load.gif' border='0' style='vertical-align:middle' /> $load
&nbsp;&nbsp;<img src='{$ibforums->vars['img_url']}/stat_sql.gif' border='0' style='vertical-align:middle' /> $sql <a href='{$ibforums->base_url}{$ibforums->query_string_safe}&amp;debug=1' style='color:white;font-size:10px'>queries</a>
&nbsp;&nbsp;<img src='{$ibforums->vars['img_url']}/stat_gzip.gif' border='0' style='vertical-align:middle' /> $gzip
<br />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// global_skin_chooser
//===========================================================================
function global_skin_chooser($data="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<form action="{$ibforums->base_url}setskin=1" name="skinselectorbox" method="post">
	<input type="hidden" name="skinurlbits" value="act={$ibforums->input['act']}&CODE={$ibforums->input['CODE']}&id={$ibforums->input['id']}&t={$ibforums->input['t']}&f={$ibforums->input['f']}&st={$ibforums->input['st']}" />
	<select name="skinid" onchange="chooseskin(this)">
		<optgroup label="{$ibforums->lang['global_skinselector']}">
			$data
		</optgroup>
	</select>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Guest_bar
//===========================================================================
function Guest_bar() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;{$ibforums->lang['guest_stuff']} ( <a href='{$ibforums->base_url}&act=Login&CODE=00'>{$ibforums->lang['log_in']}</a> | <a href='{$ibforums->base_url}&act=Reg&CODE=00'>{$ibforums->lang['register']}</a> )
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// ib_banner
//===========================================================================
function ib_banner() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href='http://www.ipshosting.com' target='_blank'><img src='html/sys-img/ipshosting.gif' border='0' alt='IPS Hosting'></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Member_bar
//===========================================================================
function Member_bar($msg="",$ad_link="",$mod_link="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table width='100%' cellpadding='0' cellspacing='0' border='0' align='center'>
<tr>
 <td align='left' valign='middle'><b>{$ibforums->lang['logged_in_as']} {$ibforums->member['name']}</b> ( <a href='{$ibforums->base_url}&act=Login&CODE=03'>{$ibforums->lang['log_out']}</a>$ad_link $mod_link )</td>
 <td align='right' valign='middle'>
   <b><a href='{$ibforums->base_url}&act=UserCP&CODE=00' title='{$ibforums->lang['cp_tool_tip']}'>{$ibforums->lang['your_cp']}</a></b> | <a href='{$ibforums->base_url}&act=Msg&CODE=01'>{$msg[TEXT]}</a>
   | <a href='{$ibforums->base_url}&act=Search&CODE=getnew'>{$ibforums->lang['view_new_posts']}</a> | <a href='javascript:buddy_pop();' title='{$ibforums->lang['bb_tool_tip']}'>{$ibforums->lang['l_qb']}</a>
 </td>
</tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// member_bar_disabled
//===========================================================================
function member_bar_disabled() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div id="userlinksguest">
	<p class="pcen">{$ibforums->lang['mb_disabled']}</b></p>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Member_no_usepm_bar
//===========================================================================
function Member_no_usepm_bar($ad_link="",$mod_link="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table width='100%' cellpadding='0' cellspacing='0' border='0' align='center'>
<tr>
 <td align='left' valign='middle'><b>{$ibforums->lang['logged_in_as']} {$ibforums->member['name']}</b> ( <a href='{$ibforums->base_url}&act=Login&CODE=03'>{$ibforums->lang['log_out']}</a>$ad_link $mod_link )</td>
 <td align='right' valign='middle'>
   <b><a href='{$ibforums->base_url}&act=UserCP&CODE=00' title='{$ibforums->lang['cp_tool_tip']}'>{$ibforums->lang['your_cp']}</a></b>
   | <a href='{$ibforums->base_url}&act=Search&CODE=getnew'>{$ibforums->lang['view_new_posts']}</a> | <a href='javascript:buddy_pop();' title='{$ibforums->lang['bb_tool_tip']}'>{$ibforums->lang['l_qb']}</a>
 </td>
</tr>
</table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// mod_link
//===========================================================================
function mod_link() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;| <b><a href='{$ibforums->base_url}&act=modcp'>{$ibforums->lang['mod_cp']}</a></b>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// msg_get_new_pm_notification
//===========================================================================
function msg_get_new_pm_notification($msg="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div id="pmnotewrap">
	<h4>{$ibforums->lang['pmp_title']} {$msg['name']}</h4>
	<div class="pminfo">
		<p class="pmavatar">{$msg['avatar']}</p>
		<p><b>{$msg['mt_title']}</b><br />{$msg['msg_post']}<br /><br /><i>{$msg['name']} {$ibforums->lang['pmp_part1']} {$msg['g_title']} {$ibforums->lang['pmp_part2']} {$msg['posts']} {$ibforums->lang['pmp_part3']}</i></p>
		<div class="cleared"><!-- float cleared --></div>
	</div>
	<p class="pmnotefoot"><a href='{$ibforums->base_url}act=Msg&amp;CODE=03&amp;MSID={$msg['mt_id']}&amp;VID=in'>{$ibforums->lang['pmp_read_in_window']}</a> / <a href='{$ibforums->base_url}act=Msg&amp;CODE=03&amp;MSID={$msg['mt_id']}&amp;VID=in' target='_blank'>{$ibforums->lang['pmp_read_new_window']}</a></p>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_compile
//===========================================================================
function pagination_compile($start="",$previous_link="",$start_dots="",$pages="",$end_dots="",$next_link="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
{$start}{$start_dots}{$previous_link}{$pages}{$next_link}{$end_dots}
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_current_page
//===========================================================================
function pagination_current_page($page="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;<span class="pagecurrent">{$page}</span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_end_dots
//===========================================================================
function pagination_end_dots($url="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;<span class="pagelinklast"><a href="$url" title="{$ibforums->lang['tpl_gotolast']}">&raquo;</a></span>&nbsp;
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_make_jump
//===========================================================================
function pagination_make_jump($tp="",$pp="",$ub="",$pages=1) {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<span class="pagelink"><a title="{$ibforums->lang['tpl_jump']}" href="javascript:multi_page_jump('$ub',$tp,$pp);">$pages {$ibforums->lang['tpl_pages']}</a></span>&nbsp;
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_next_link
//===========================================================================
function pagination_next_link($url="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;<span class="pagelink"><a href="$url" title="{$ibforums->lang['tpl_next']}">&gt;</a></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_page_link
//===========================================================================
function pagination_page_link($url="",$page="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;<span class="pagelink"><a href="$url" title="$page">$page</a></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_previous_link
//===========================================================================
function pagination_previous_link($url="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<span class="pagelink"><a href="$url" title="{$ibforums->lang['tpl_previous']}">&lt;</a></span>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pagination_start_dots
//===========================================================================
function pagination_start_dots($url="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<span class="pagelinklast"><a href="$url" title="{$ibforums->lang['tpl_gotofirst']}">&laquo;</a></span>&nbsp;
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// PM_popup
//===========================================================================
function PM_popup() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
     <script language='JavaScript'>
     <!--
       window.open('index.{$ibforums->vars['php_ext']}?act=Msg&CODE=99&s={$ibforums->session_id}','NewPM','width=500,height=250,resizable=yes,scrollbars=yes'); 
     //-->
     </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pop_up_window
//===========================================================================
function pop_up_window($title="",$css="",$text="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" /> 
		<title>$title</title>
		$css
	</head>
	<script type="text/javascript">
	  <!--
	   var ipb_var_st       = "{$ibforums->input['st']}";
	   var ipb_lang_tpl_q1  = "{$ibforums->lang['tpl_q1']}";
	   var ipb_var_s        = "{$ibforums->session_id}";
	   var ipb_var_phpext   = "{$ibforums->vars['php_ext']}";
	   var ipb_var_base_url = "{$ibforums->base_url}";
	   var ipb_input_f      = "{$ibforums->input['f']}";
	   var ipb_input_t      = "{$ibforums->input['t']}";
	   var ipb_input_p      = "{$ibforums->input['p']}";
	   var ipb_var_cookieid = "{$ibforums->vars['cookie_id']}";
	   var ipb_var_cookie_domain = "{$ibforums->vars['cookie_domain']}";
	   //-->
	</script>
	<script type="text/javascript" src="jscripts/ipb_global.js"></script>
	<body>
		<div> 
			$text
		</div>
	</body>
</html>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Redirect
//===========================================================================
function Redirect($Text="",$Url="",$css="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<html>
<head>
<title>{$ibforums->lang['stand_by']}</title>
<meta http-equiv='refresh' content='2; url={$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}$Url'>
$css
</head>
<body class='mainbg'>
<table width='<{tbl_width}>' height='85%' align='center'>
<tr>
	<td valign='middle'>
		<table align='center' border="0" cellspacing="1" cellpadding="0" bgcolor="#000000">
		<tr> 
			<td class='mainbg'>
				<table width="100%" border="0" cellspacing="1" cellpadding="12">
					<tr> 
						<td width="100%" align="center" class='row1'>
							{$ibforums->lang['thanks']}, 
							$Text<br><br>
							{$ibforums->lang['transfer_you']}<br><br>
							(<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}$Url'>{$ibforums->lang['dont_wait']}</a>)</td>
					</tr>
				</table>
			</td>
		</tr>
	  </table>
	</td>
</tr>
</table>
</body>
</html>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// rules_link
//===========================================================================
function rules_link($url="",$title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
&nbsp;<a href="$url">$title</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_chat_link_inline
//===========================================================================
function show_chat_link_inline() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<span style='color:<{tbl_border}>'>|</span> <a href='{$ibforums->base_url}&act=chat'>{$ibforums->lang['live_chat']}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_chat_link_popup
//===========================================================================
function show_chat_link_popup() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
     <script language='JavaScript'>
     <!--
        function chat_pop()
        {
			window.open('index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}&act=chat&pop=1','Chat','width={$ibforums->vars['chat_width']},height={$ibforums->vars['chat_height']},resizable=yes,scrollbars=yes');
     	}
     //-->
     </script>
<span style='color:<{tbl_border}>'>|</span> <a href="javascript:chat_pop();">{$ibforums->lang['live_chat']}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_tsl_link_inline
//===========================================================================
function show_tsl_link_inline() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}act=module&amp;module=toplist">{$ibforums->lang['tb_toplist']}</a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// signature_separator
//===========================================================================
function signature_separator($sig="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<br /><br />--------------------<br />
<div class="signature">$sig</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// start_nav
//===========================================================================
function start_nav() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<table width='<{tbl_width}>' align='center' border="0" cellspacing="0" cellpadding="2">
<tr> 
    <td width='1%' valign='middle'><{F_NAV}></td>
    <td width="100%" align='left' valign='middle' class="nav">
EOF;

//--endhtml--//
return $IPBHTML;
}



}

/*--------------------------------------------------*/
/*<changed bits>
admin_link,board_offline,end_nav,Error,error_log_in,global_board_header,Guest_bar,ib_banner,Member_bar,Member_no_usepm_bar,mod_link,PM_popup,Redirect,show_chat_link_inline,show_chat_link_popup,start_nav
</changed bits>*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>