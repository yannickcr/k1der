<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 2                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:38 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_poll {

//===========================================================================
// button_null_vote
//===========================================================================
function button_null_vote() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<input type="submit" name="nullvote" value="{$ibforums->lang['poll_null_vote']}" title="{$ibforums->lang['tt_poll_null']}" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// button_show_results
//===========================================================================
function button_show_results() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<input type="button" value="{$ibforums->lang['pl_show_results']}" title="{$ibforums->lang['tt_poll_show']}" onclick="go_gadget_show()" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// button_show_voteable
//===========================================================================
function button_show_voteable() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<input type="button" name="viewresult" value="{$ibforums->lang['pl_show_vote']}"  title="{$ibforums->lang['tt_poll_svote']}" onclick="go_gadget_vote()" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// button_vote
//===========================================================================
function button_vote() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<input type="submit" name="submit" value="{$ibforums->lang['poll_add_vote']}" title="{$ibforums->lang['tt_poll_vote']}" />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// delete_link
//===========================================================================
function delete_link($tid="",$fid="",$key="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}act=Mod&amp;CODE=22&amp;f=$fid&amp;t=$tid&amp;auth_key=$key"><{P_DELETE}></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// edit_link
//===========================================================================
function edit_link($tid="",$fid="",$key="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<a href="{$ibforums->base_url}act=Mod&amp;CODE=20&amp;f=$fid&amp;t=$tid&amp;auth_key=$key"><{P_EDIT}></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// poll_header
//===========================================================================
function poll_header($tid="",$poll_q="",$edit="",$delete="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!--IBF.POLL_JS-->
<form action="{$ibforums->base_url}act=Poll&amp;t=$tid&amp;st={$ibforums->input['st']}" method="post">
		<table cellspacing="1">
			<tr>
				<th colspan="3" align="center"><b>$poll_q</b></th>
			</tr>
			<tr>
				<td colspan="3" class="formsubtitle" align="right">$edit&nbsp;$delete</td>
			</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// poll_javascript
//===========================================================================
function poll_javascript($tid="",$fid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<script type="text/javascript">
function go_gadget_show()
{
	window.location = "{$ibforums->base_url}&act=ST&f=$fid&t=$tid&mode=show&st={$ibforums->input['start']}";
}
function go_gadget_vote()
{
	window.location = "{$ibforums->base_url}&act=ST&f=$fid&t=$tid&st={$ibforums->input['start']}";
}
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Render_row_form
//===========================================================================
function Render_row_form($votes="",$id="",$answer="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
    <td class="row1" colspan="3"><input type="radio" name="poll_vote" value="$id" class="radiobutton" />&nbsp;<b>$answer</b></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Render_row_results
//===========================================================================
function Render_row_results($votes="",$id="",$answer="",$percentage="",$width="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
	<td class="row1">$answer</td>
	<td class="row1"> [ <b>$votes</b> ] </td>
	<td class="row1"><{BAR_LEFT}><img src="{$ibforums->vars['img_url']}/bar.gif" width="$width" height="11" align="middle" alt="" /><{BAR_RIGHT}>&nbsp;[$percentage%]</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// show_total_votes
//===========================================================================
function show_total_votes($total_votes="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
    <td class="row1" colspan="3" align="center"><b>{$ibforums->lang['pv_total_votes']}: $total_votes</b></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// ShowPoll_footer
//===========================================================================
function ShowPoll_footer() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td colspan="3" class="formbuttonrow"><!--IBF.VOTE-->&nbsp;<!--IBF.SHOW--></td>
		</tr>
		<tr>
			<td class="catend" colspan="3"><!-- no content --></td>
		</tr>
		</table>
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