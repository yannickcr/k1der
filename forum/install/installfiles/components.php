<?php

$WRAPPER = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head> 
<title><% TITLE %></title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" /> 
<% GENERATOR %> 
<% CSS %> 
<% JAVASCRIPT %> 
</head> 
<body>
<div id="ipbwrapper">
<% BOARD HEADER %>
<% MEMBER BAR %>
<% NAVIGATION %>
<!--IBF.NEWPMBOX-->
<% BOARD %>
<% BOARD FOOTER %>
<% STATS %> 
<% COPYRIGHT %>
</div>
</body> 
</html>
EOF;


$CSS = <<<EOF
/* 
 * Cascading Style Sheet(CSS 467), for Invision Power Board 2.0.0
 * Author: James A. Mathias, admin@leihu.com, http://www.1lotus.com 
 * Copyright: 2004 Invision Power Services, all rights reserved 
 *
 * All style attributes in alpha-numeric order starting from 0
 *
 */

/* 
 * ========================================
 * global element styles 
 * ========================================
 */

html{
	overflow-x: auto; /* fixes MSIE scrollbar bug DO NOT REMOVE, has no effect in Mozilla, or Opera */
}

body{ 
	background: #FFF;
	color: #222;
	font-family: Verdana, Tahoma, Arial, Trebuchet MS, Sans-Serif, Georgia, Courier, Times New Roman, Serif;
	font-size: 11px;
	line-height: 135%;
	margin: 0;
	padding: 0; /* required for Opera to have 0 margin */
	text-align: center; /* centers board in MSIE */
}

table,
tr,
td{ /* required for text in tables, because tables do not inherit from body */
	background: transparent;
	color: #222;
	font-size: 11px;
	line-height: 135%;
}

table{ /* makes all tables 100% wide */
	width: 100%;
}

td,
.divpad{ /* gives all tables faux cellpadding of 5px */
	padding: 5px;
}

td.nopad{ /* allows some tables to have no padding on the td */
	padding: 0;
}

form{
	display: inline;
	margin: 0; /* removes mystery form tag gapping */
	padding: 0; /* removes mystery form tag gapping */
}

img{
	border: 0; /* makes sure linked images do not have a border */
	vertical-align: middle;
}

/* 
 * ========================================
 * global hyperlink styles 
 * ========================================
 */

a:link,
a:visited,
a:active{
	background: transparent;
	color: #222;
	text-decoration: underline;
}

a:hover{
	background: transparent;
	color: #34498B;
}

/* 
 * ========================================
 * Main wrapper, this controls the overall width of the board in you browser view window. 
 * ========================================
 */

#ipbwrapper{ 
	margin: 20px auto 20px auto; /* centers the box, no matter the overall width, also applies a 20px gap at the top and bottom of the board */
	text-align: left; /* re_aligns text to left second part of two part MSIE centering workaround */
	/* EDIT THIS TO CHANGE THE WIDTH OF THE BOARD -> 750px is a common fixed resolution size */
	width: 98%;
}

/* 
 * ========================================
 * styles for pagination links 
 * ========================================
 */
 

.pagelink,
.pagelinklast,
.pagecurrent,
.minipagelink,
.minipagelinklast{
	background: #F0F5FA;
	border: 1px solid #072A66;
	padding: 1px 3px 1px 3px;
}

.pagelinklast,
.minipagelinklast{
	background: #DFE6EF;
}


.pagecurrent{
	background: #FFC9A5;
}

.minipagelink,
.minipagelinklast{
	border: 1px solid #C2CFDF;
	font-size: 10px;
	margin: 0 1px 0 0;
}

.pagelink a:active,
.pagelink a:visited,
.pagelink a:link,
.pagelinklast a:active,
.pagelinklast a:visited,
.pagelinklast a:link,
.pagecurrent a:active,
.pagecurrent a:visited,
.pagecurrent a:link,
.minipagelink a:active,
.minipagelink a:visited,
.minipagelink a:link,
.minipagelinklast a:active,
.minipagelinklast a:visited,
.minipagelinklast a:link{
	text-decoration: none;
}

/* fake button effect for some links */
.fauxbutton{
	background: #BFCDE0;
	border: 1px solid #072A66;
	font-size: 11px;
	font-weight: bold;
	padding: 4px;
}

.fauxbutton a:link,
.fauxbutton a:visited,
.fauxbutton a:active{
	color: #222 !important;
	text-decoration: none;
}

.forumdesc,
.forumdesc a:link,
.forumdesc a:visited,
.forumdesc a:active{ 
	background: transparent;
	font-size: 10px; 
	color: #666;
	line-height: 135%;
	margin: 2px 0 0 0;
	padding: 0;
}

/* =================================================================================== */
/* =================================================================================== */
/* =================================================================================== */

.searchlite {
	background-color:yellow;
	font-weight:bold;
	color: red;
}

.activeusers{
	background: #FFF;
	border: 1px solid #072A66;
	color: #000;
	margin: 0px;
	padding: 1px;
}

.activeuserposting a:link,
.activeuserposting a:visited,
.activeuserposting a:active,
.activeuserposting
{
	font-style:italic;
	text-decoration: none;
	border-bottom:1px dotted black;
}

fieldset.search{ 
	line-height: 150%;
	padding: 6px; 
}

label{ 
	cursor: pointer; 
}

img.attach{ 
	background: #808080 url(<#IMG_DIR#>/click2enlarge.gif) no-repeat top right;
	border: 1px solid #808080;
	margin: 0 2px 0 0;
	padding: 11px 2px 2px 2px;
}

.thumbwrap,
.thumbwrapp,
.fullimagewrap{
	border: 1px solid #072A66;
	margin: 2px;
}

.thumbwrapp{
	border: 2px solid #660707;
}

.fullimagewrap{
	background: #F5F9FD;
	text-align: center;
	margin: 5px 0 5px 0;
	padding: 5px;
}

.thumbwrap h4,
.thumbwrapp h4{	
	background: #DDE6F2;
	border: 0 !important;
	border-bottom: 1px solid #5176B5 !important;
	color: #5176B5; 
	font-size: 12px;
	font-weight: bold; 
	margin: 0;
	padding: 5px;
}

.thumbwrap p,
.thumbwrapp p{
	background: #EEF2F7 !important;
	border: 0 !important;
	border-top: 1px solid #5176B5 !important;
	margin: 0 !important;
	padding: 5px !important;
	text-align: left;
}

.thumbwrap p.alt,
.thumbwrapp p.alt{
	background: #DFE6EF !important;
	margin: 0 !important;
	padding: 5px !important;
	text-align: left;
}

.thumbwrapp p.pin{
	background: #EFDFDF !important;
	text-align: center !important;
}
	
.thumbwrap img.galattach,
.thumbwrapp img.galattach{
	background: #FFF url(<#IMG_DIR#>/img_larger.gif) no-repeat bottom right;
	border: 1px solid #072A66;
	margin: 5px;
	padding: 2px 2px 10px 2px;
}

li.helprow{ 
	margin: 0 0 10px 0;
	padding: 0; 
}

ul#help{ 
	padding: 0 0 0 15px; 
}

.warngood,
.warnbad{ 
	color: #0B9500;
	font-weight: bold;
}

.warnbad{ 
	color: #DD0000;
}

#padandcenter{ 
	margin: 0 auto 0 auto;
	padding: 14px 0 14px 0;
	text-align: center;
}

#profilename{ 
	font-size: 28px; 
	font-weight: bold; 
}

#photowrap{ 
	padding: 6px; 
}

#phototitle{ 
	border-bottom: 1px solid #000; 
	font-size: 24px; 
}

#photoimg{ 
	margin: 15px 0 0 0;
	text-align: center; 
} 

#ucpmenu,
#ucpcontent{ 
	background: #F5F9FD;
	border: 1px solid #345487;
	line-height: 150%;
}

#ucpmenu p{ 
	margin: 0; 
	padding: 2px 5px 6px 9px;
}

#ucpmenu a:link, 
#ucpmenu a:active, 
#ucpmenu a:visited{ 
	text-decoration: none; 
}

#ucpcontent{ 
	width: auto;
}

#ucpcontent p{ 
	margin: 0;
	padding: 10px;
}

.activeuserstrip{ 
	background: #BCD0ED;
	padding: 6px;
}

/* Topic View elements */
.signature{  
	background: transparent;
	color: #339; 
	font-size: 10px;
	line-height: 150%;
}

.postdetails{ 
	font-size: 10px;
	line-height:140%;
}

.postcolor{ 
	font-size: 12px; 
	line-height: 160%;
}

.normalname{ 
	color: #003;
	font-size: 12px; 
	font-weight: bold; 
}

.normalname a:link, 
.normalname a:visited, 
.normalname a:active{ 
	font-size: 12px;
}

.post1,
.bg1{ 
	background: #F5F9FD;
}

.post2,
.bg3{ 
	background: #EEF2F7;
}

.row2shaded,
.post1shaded { background-color: #DEDBE4 }
.row4shaded,
.post2shaded { background-color: #E3DFE7 }

.row1{ 
	background: #DFE6EF; 
}

.row2{ 
	background: #E4EAF2; 
}

.darkrow1{ 
	background: #BCD0ED;
	color: #3A4F6C; 
}

.darkrow3{ 
	background: #D1DCEB; 
	color: #3A4F6C; 
}

/* tableborders gives the white column / row lines effect */
.plainborder,
.tablefill,
.tablepad{ 
	background: #F5F9FD;
	border: 1px solid #345487;
}

.tablefill,
.tablepad{ 
	padding: 6px;  
}

.tablepad{ 
	border: 0 !important;
}

.wrapmini{ 
	float: left;
	line-height: 1.5em;
	width: 25%;
}

.pagelinks{
	float: left;
	line-height: 1.2em;
	width: 35%;
}

.desc{ 
	font-size: 11px; 
	color: #434951;
}

.lastaction
{
	font-size: 10px; 
	color: #434951;
}

.edit{ 
	font-size: 9px;
}

.thin{ 
	border: 1px solid #FFF;
	border-left: 0;
	border-right: 0;
	line-height: 150%;
	margin: 2px 0 2px 0;
	padding: 6px 0 6px 0;
}

/* =================================================================================== */
/* =================================================================================== */
/* =================================================================================== */

/* 
 * ========================================
 * calendar styles 
 * ========================================
 */
	
.calmonths{ 
	background: #F0F5FA;
	border: 1px solid #C2CFDF;
	font-size: 18px; 
	font-weight: bold; 
	margin: 5px 0 5px 0;
	padding: 8px;
	text-align: center;
}

.weekday{
	font-size: 14px;
	font-weight: bold;
}

.calmonths a{
	text-decoration: none;
}

.calday,
.calweekday{ 
	background: #DFE6EF;
	color: #666;
	font-size: 11px;
	font-weight: bold;
	margin: 0;
	padding: 4px;
	text-align: right;
}

.calweekday{
	border-right: 1px solid #AAA;
	color: #222;
	font-size: 14px;
	padding: 6px;
	text-align: center;
}

.cellblank,
.celldate,
.celltoday,
.mcellblank,
.mcelldate,
.mcelltoday{
	background: #EEF2F7;
	height: 100px;
	margin: 0;
	padding: 0;
	vertical-align: top;
}

.mcellblank,
.mcelldate,
.mcelltoday{
	height: auto;
}

.cellblank,
.mcellblank{
	background: #C2CFDF;
}

.celltoday,
.mcelltoday{
	border: 2px solid #8B0000;
}

/* 
 * ========================================
 * form styles 
 * ========================================
 */

input,
textarea,
select{
	background: #FFF;
	border: 1px solid #4C77B6;
	color: #000;
	font-family: verdana, helvetica, sans-serif;
	font-size: 11px;
	margin: 5px;
	padding: 2px;
	vertical-align: middle;
}

select{
	border: 0;
	font-family: verdana, helvetica, sans-serif;
	font-size: 12px;
	margin: 0;
	padding: 0;
}

input.button{
	margin: 0;
	width: auto;
}

optgroup option{
	font-family: verdana, helvetica, sans-serif;
	font-size: 12px;
}

.codebuttons{ 
	font-family: Verdana, Helvetica, Sans-Serif; 
	font-size: 10px; 
	vertical-align: middle;
	margin:2px;
}

.textarea,
.searchinput,
.button,
.gobutton{
	background: #FFF;
	border: 1px solid #4C77B6;
	color: #000;
	font-family: Verdana, Helvetica, Sans-Serif;
	font-size: 11px;
	padding: 2px;
	vertical-align: middle;
}
	
.button{
	background: #DFE6EF;
}

.gobutton{
	background: transparent;
	border: 0;
	color: #072A66;
	margin: 0;
	vertical-align: middle;
}

.radiobutton,
.checkbox,
.helpbox { 
	border: 0;
	vertical-align: middle;
}

/* 
 * class.formtable 
 *
 * used for tabled forms 
 * technically tables should not be used for form display 
 * but, in the case of IPB a table is easier to work with
 * for the average webmaster, who has little to no CSS knowledge.
 *
 */

.formtable{
	background: transparent;
}

.formtable td,
.pformleft,
.pformleftw,
.pformright{
	background:#F5F9FD;
	border: 1px solid #C2CFDF;
	border-bottom: 0;
	border-left: 0;
	font-weight: bold;
	margin: 1px 0 0 0;
	padding: 6px;
	width: 25%;
} 

.formtable td.wider,
.pformleftw,
.pformright{
	width: 40%;
}

.formtable td.formright,
.pformright{
	border-right: 0;
	font-weight: normal;
	width: auto;
} 

.formtable td.formtitle,
.formsubtitle{
	background: #D1DCEB;
	border: 1px solid #9FB9D4; 
	border-bottom: 0;
	border-left: 0;
	border-right: 0;
	font-weight: normal;
}

.formsubtitle{ 
	border: 0;
	color: #3A4F6C;
	font-weight: bold;
	padding: 5px;
}

.formtable td.formstrip{
	background: #DDE8F2;
	border: 1px solid #9FB9D4;
	border-left: 0;
	border-right: 0;
	font-weight: normal;
}

/* 
 * ========================================
 * new style quote and code wrappers MATT's DESIGN 
 * ========================================
 */

.quotetop{
	background: #E4EAF2 url(<#IMG_DIR#>/css_img_quote.gif) no-repeat right;
	border: 1px dotted #000;
	border-bottom: 0;
	border-left: 4px solid #8394B2;
	color: #000;
	font-weight: bold;
	font-size: 10px;
	margin: 8px auto 0 auto;
	padding: 3px;
}

.quotemain{
	background: #FAFCFE;
	border: 1px dotted #000;
	border-left: 4px solid #8394B2;
	border-top: 0;
	color: #465584;
	padding: 4px;
	margin: 0 auto 8px auto;
}

.codetop,
.sqltop,
.htmltop{
	background: #FDDBCC url(<#IMG_DIR#>/css_img_code.gif) no-repeat right;
	color: #000;
	font-weight: bold;
	margin: 0 auto 0 auto;
	padding: 3px;
	width: 98%;
}

.codemain,
.sqlmain,
.htmlmain{
	background: #FAFCFE;
	border: 1px dotted #000;
	color: #465584;
	font-family: Courier, Courier New, Verdana, Arial;
	margin: 0 auto 0 auto;
	padding: 2px;
	width: 98%;
}

/* 
 * ========================================
 * old school quote and code styles - backwards compatibility 
 * ========================================
 */

#QUOTE,
#CODE{  
	background: #FAFCFE; 
	border: 1px solid #000; 
	color: #465584; 
	font-family: Verdana, Arial; 
	font-size: 11px; 
	padding: 2px; 
	white-space: normal;
}

#CODE{ 
	font-family: Courier, Courier New, Verdana, Arial;
}

/* 
 * ========================================
 * All New Styles 
 * ========================================
 */
.cleared{
	clear: both;
}

.borderwrap,
.borderwrapm{ /* this will affect the outlining border of all the tables and boxes through-out the skin. */
	background: #FFF; 
	border: 1px solid #072A66;
	padding: 0; 
	margin: 0; 
}

.borderwrapm{
	margin: 5px;
}

.borderwrap h3,
.maintitle,
.maintitlecollapse{
	background: transparent url(<#IMG_DIR#>/tile_cat.gif);
	border: 1px solid #FFF;
	border-bottom: 1px solid #5176B5;
	color: #FFF; 
	font-size: 12px;
	font-weight: bold; 
	margin: 0;
	padding: 8px;
}

.maintitlecollapse{
	border: 1px solid #FFF;
}

.maintitle p,
.maintitlecollapse p,
.formsubtitle p{
	background: transparent !important;
	border: 0 !important;
	margin: 0 !important;
	padding: 0 !important;
}

.maintitle p.expand,
.maintitle p.goto,
.maintitlecollapse p.expand,
.formsubtitle p.members{
	float: right;
	width: auto !important;
}

.maintitle a:link, 
.maintitle a:visited,
.maintitlecollapse a:link, 
.maintitlecollapse a:visited{ 
	background: transparent;
	color: #FFF;
	text-decoration: none; 
}

.maintitle a:hover, 
.maintitle a:active,
.maintitlecollapse a:hover, 
.maintitlecollapse a:active{ 
	background: transparent;
	color: #F1F1F1;
}

table th,
.borderwrap table th,
.subtitle,
.subtitlediv,
.postlinksbar{ 
	background: transparent url(<#IMG_DIR#>/tile_sub.gif);
	border-bottom: 1px solid #5176B5;
	color: #3A4F6C; 
	font-size: 10px;
	font-weight: bold; 
	letter-spacing: 1px;
	margin: 0; 
	padding: 5px; 
}

.subtitlediv{
	border: 1px solid #FFF;
	border-bottom: 1px solid #5176B5;
	text-align: right;
}

.borderwrap table th a:link,
.subtitle a:link,
.subtitlediv a:link,
.borderwrap table th a:visited,
.subtitle a:visited, 
.subtitlediv a:visited, 
.borderwrap table th a:active,
.subtitle a:active,
.subtitlediv a:active,
.borderwrap table th a:hover,
.subtitle a:hover,
.subtitlediv a:hover{ 
	background: transparent;
	color: #3A4F6C;
	text-decoration: none; 
}

.borderwrap h4{
	background: #DDE6F2;
	border: 1px solid #FFF;
	border-bottom: 1px solid #5176B5;
	border-top: 1px solid #5176B5;
	color: #5176B5; 
	font-size: 12px;
	font-weight: bold; 
	margin: 0;
	padding: 5px;
}

.borderwrap p{
	background: #F9F9F9;
	border: 1px solid #CCC;
	margin: 5px;
	padding: 10px;
	text-align: left;
}

td.formbuttonrow,
.borderwrap p.formbuttonrow,
.borderwrap p.formbuttonrow1{
	background: #D1DCEB !important; 
	border: 1px solid #FFF;
	border-top: 1px solid #5176B5;
	margin: 0px !important;
	padding: 5px !important;
	text-align: center;
}

td.formbuttonrow{
	border-bottom: 0;
	border-left: 0;
	border-right: 0;
}

.borderwrap p.formbuttonrow1{
	background: #F9F9F9 !important;
	border: 0;
	border-top: 1px solid #CCC;
}

.bar,
.barb,
.barc{
	background: #DFE6EF;
	border: 1px solid #FFF;
}

.barc{
	border-bottom: 0;
}

.bar p,
.barb p,
.barc p{
	background: transparent;
	border: 0;
	color: #222;
	font-size: 11px;
	margin: 0;
	padding: 5px;
	text-align: left;
}

.barb p{
	text-align: right;
}

.bar p.over,
.bar p.overs,
.barc p.over,
.barc p.overs{
	float: right;
}

.barb p.over,
.barb p.overs{
	float: left;
}

.bar p.overs,
.barb p.overs,
.barc p.overs{
	position: relative;
	top: 5px;
}

.catend{
	background: #8394B2;
	color: #000;
	font-size: 1px;
	height: 5px;
}

.newslink{
	background: #F0F5FA;
	border: 1px solid #C2CFDF;
	margin: 0;
	width: 100%;
}

.newslink td{
	color: #222;
	font-size: 10px;
	padding: 5px 5px 5px 10px;
}

.newslink span{
	background: transparent;
	color: #072A66;
	font-style: italic;
	font-weight: normal;
}

.newslink input{
	background: #FFF;
	border: 1px solid #999;
	color: #072A66;
	font-size: 10px;
	padding: 3px;
	vertical-align: middle;
	width: auto;
}

.newslink input.button{
	background: transparent;
	border: 0;
	color: #072A66;
	vertical-align: middle;
}

.fieldwrap{
	background: #F9F9F9;
	border: 1px solid #CCC;
	border-top: 0;
	margin: 5px;
	padding: 0;
	text-align: left;
}

.fieldwrap h4{
	background: #EEE;
	border: 1px solid #CCC;
	border-left: 0;
	border-right: 0;
	color: #444; 
	font-size: 12px;
	font-weight: bold; 
	margin: 0;
	padding: 5px;
}

.errorwrap,
#pmnotewrap{
	background: #F2DDDD;
	border: 1px solid #992A2A;
	border-top: 0;
	margin: 5px;
	padding: 0;
}

#pmnotewrap{
	line-height: 135%;
	margin: 0 0 5px 0;
}

.errorwrap h4,
#pmnotewrap h4{
	background: #E3C0C0;
	border: 1px solid #992A2A;
	border-left: 0;
	border-right: 0;
	color: #992A2A; 
	font-size: 12px;
	font-weight: bold; 
	margin: 0;
	padding: 5px;
}

.errorwrap p,
#pmnotewrap p{
	background: transparent;
	border: 0;
	color: #992A2A;
	margin: 0;
	padding: 8px;
}

#pmnotewrap p.pmavatar{
	float: left;
}

#pmnotewrap p.pmnotefoot{
	background: #E3C0C0;
	border-top: 1px solid #992A2A;
	text-align: right;
}

#pmnotewrap a:link, 
#pmnotewrap  a:visited{ 
	background: transparent; 
	color: #992A2A; 
	text-decoration: underline;
}

#pmnotewrap a:hover, 
#pmnotewrap a:active{
	background: transparent; 
	color: #992A2A; 
	text-decoration: none;
}

.ruleswrap{
	background: #F2DDDD;
	border: 1px solid #992A2A;
	color: #992A2A; 
	margin: 5px 0 5px 0;
	padding: 5px;
}

#redirectwrap{
	background: #F0F5FA;
	border: 1px solid #C2CFDF;
	margin: 200px auto 0 auto;
	text-align: left;
	width: 500px;
}

#redirectwrap h4{
	background: #D0DDEA;
	border-bottom: 1px solid #C2CFDF;
	color: #3A4F6C;
	font-size: 14px;
	margin: 0;
	padding: 5px;
}

#redirectwrap p{
	margin: 0;
	padding: 5px;
}

#redirectwrap p.redirectfoot{
	background: #E3EBF4;
	border-top: 1px solid #C2CFDF;
	text-align: center;
}


#gfooter{
	background: #8394B2;
	margin: 5px 0 5px 0;
	padding: 0;
	width: 100%;
}

#gfooter td{
	color: #FFF;
	font-size: 10px;
	padding: 4px;
}

#gfooter a:link,
#gfooter a:visited{
	color: #FFF;
}

#logostrip{ 
	background: #3860BB url(<#IMG_DIR#>/tile_back.gif);
	border: 1px solid #FFF;
	height: 68px;
	margin: 0;
	padding: 0;
}

#submenu{ 
	background: transparent url(<#IMG_DIR#>/tile_sub.gif);
	border: 1px solid #FFF;
	border-top: 0;
	color: #3A4F6C; 
	margin: 0; 
}

#userlinks,
#userlinksguest{ 
	background: #F0F5FA;
	border: 1px solid #C2CFDF;
	margin: 5px 0 5px 0;
	padding: 0 5px 0 5px;
}

#userlinksguest{ 
	background: #F4E7EA;
	border: 1px solid #986265;
}

#submenu p,
#userlinks p,
#userlinksguest p{
	background: transparent !important;
	border: 0 !important;
	font-size: 10px;
	font-weight: bold; 
	letter-spacing: 1px;
	margin: 0 !important;
	padding: 7px 0 7px 0; 
	text-align: right;
}

#userlinks p,
#userlinksguest p{
	font-weight: normal;
	letter-spacing: 0;
}

#submenu p.home,
#userlinks p.home,
#userlinksguest p.home{
	float: left;
}

#userlinksguest p.pcen{
	text-align: center;
}

#submenu a:link, 
#submenu  a:visited{ 
	background: transparent; 
	color: #3A4F6C; 
	padding: 0 6px 0 6px;
	text-decoration: none;
}

#submenu a:hover, 
#submenu a:active{
	background: transparent; 
	color: #5176B5; 
}
#navstrip{ 
	background: transparent;
	color: #999;
	font-size: 12px;
	font-weight: bold;
	margin: 0 0 5px 0;
	padding: 8px 0 8px 0px; 
}

#navstrip a:link, 
#navstrip  a:visited{ 
	background: transparent; 
	color: #222; 
	text-decoration: none;
}

#navstrip a:hover, 
#navstrip a:active{
	background: transparent; 
	color: #5176B5; 
}

.toplinks{
	background: transparent;
	color: #000;
	margin: 0;
	padding: 0 0 5px 0;
	text-align: right;
}

.toplinks span{
	background: #F0F5FA;
	border: 1px solid #C2CFDF;
	border-bottom: 0;
	color: #000;
	font-size: 10px;
	font-weight: bold;
	margin: 0 10px 0 0;
	padding: 5px;
}

.copyright{ 
	background: #EEE;
	font-size: 11px; 
	margin: 0 0 5px 0;
	padding: 8px;
}

/* 
 * ========================================
 * print page styles 
 * ========================================
 */

#print{
	margin: 20px auto 20px auto;
	padding: 0;
	text-align: left;
	width: 85%;
}

#print h1,
#print h2,
#print h3,
#print h4,
#print p{
	color: #036;
	font-size: 18px;
	font-weight: bold;
	margin: 0;
	padding: 8px;
}

#print h2,
#print h3,
#print p{
	border-bottom: 1px solid #999;
	font-size: 11px;
	font-weight: normal;
}

#print h3{
	background: #F5F5F5;
	font-size: 12px;
	font-weight: bold;
	margin: 0 0 10px 0;
}

#print h4{
	background: #F9F9F9;
	font-size: 11px;
}

#print p{
	margin: 0 0 5px 0;
	padding: 10px;
}

#print p.printcopy{
	border: 0;
	color: #000;
	text-align: center;
}

EOF;

?>