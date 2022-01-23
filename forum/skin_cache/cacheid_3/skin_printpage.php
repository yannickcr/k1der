<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:42 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_printpage {

//===========================================================================
// choose_form
//===========================================================================
function choose_form($fid="",$tid="",$title="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="borderwrap">
	<div class="maintitle"><{CAT_IMG}>&nbsp;{$ibforums->lang['tvo_title']}&nbsp;$title</div>
	<table cellspacing="1">
		<tr>
			<th>Please choose an option below</th>
		</tr>
		<tr>
			<td class="row1"><b><a href="{$ibforums->base_url}act=Print&amp;client=printer&amp;f=$fid&amp;t=$tid">{$ibforums->lang['o_print_title']}</a></b><br />{$ibforums->lang['o_print_desc']}</td>
		</tr>
		<tr>
			<td class="row2"><b><a href="{$ibforums->base_url}act=Print&amp;client=html&amp;f=$fid&amp;t=$tid">{$ibforums->lang['o_html_title']}</a></b><br />{$ibforums->lang['o_html_desc']}</td>
		</tr>
		<tr>
			<td class="row1"><b><a href="{$ibforums->base_url}act=Print&amp;client=wordr&amp;f=$fid&amp;t=$tid">{$ibforums->lang['o_word_title']}</a></b><br />{$ibforums->lang['o_word_desc']}</td>
		</tr>
		<tr>
			<td class="formbuttonrow"><a href="{$ibforums->base_url}showtopic=$tid">{$ibforums->lang['back_topic']}</a></td>
		</tr>
		<tr>
			<td class="catend"><!-- no content --></td>
		</tr>
	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pp_end
//===========================================================================
function pp_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<!--Copyright-->
EOF;
//startif
if ( $ibforums->vars['ipb_copy_number'] == '' )
{
$IPBHTML .= <<<EOF
<p class="printcopy">Powered by Invision Power Board (http://www.invisionboard.com)<br />&copy; Invision Power Services (http://www.invisionpower.com)</p>
EOF;
}//endif
$IPBHTML .= <<<EOF
	</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pp_header
//===========================================================================
function pp_header($forum_name="",$topic_title="",$topic_starter="",$fid="",$tid="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<html>
	<head>
		<title>{$ibforums->vars['board_name']} [Powered by Invision Power Board]</title>
		<!--IPB.CSS-->
	</head>
	<body>
	<div id="print">
		<h1>{$ibforums->lang['title']}</h1>
		<h2><a href="{$ibforums->base_url}act=ST&amp;f=$fid&amp;t=$tid" title="Click to view Topic">{$ibforums->lang['topic_here']}</a></h2>
		<h3>{$ibforums->vars['board_name']} _ $forum_name _ $topic_title</h3>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// pp_postentry
//===========================================================================
function pp_postentry($poster="",$entry="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<div class="printpost">
			<h4>{$ibforums->lang['by']}: {$entry['author_name']}</b> {$ibforums->lang['on']} {$entry['post_date']}</h4>
			<p>{$entry['post']}
			<br />
			<!--IBF.ATTACHMENT_{$entry['pid']}-->
			</p>
		</div>
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