<?php
$titrepage = "-=K1der=- The Chocolat Effect";
switch($galerie)
{
case "cs_italy":
	$page_a_inclure = "galerie/cs_italy.htm";
	$back_color = "#FFFF99";
	break;
	
case "cs_militia":
	$page_a_inclure = "galerie/cs_militia.htm";
	$back_color = "#66CC33";
	break;

case "de_dust":
	$page_a_inclure = "galerie/de_dust.htm";
	$back_color = "#FF9999";
	break;

case "de_dust2":
	$page_a_inclure = "galerie/de_dust2.htm";
	$back_color = "#66CCFF";
	break;
	
case "de_inferno":
	$page_a_inclure = "galerie/de_inferno.htm";
	$back_color = "#CC66CC";
	break;
	
case "de_nuke":
	$page_a_inclure = "galerie/de_nuke.htm";
	$back_color = "#FF9933";
	break;

case "de_prodigy":
	$page_a_inclure = "galerie/de_prodigy.htm";
	$back_color = "#FFFF99";
	break;

case "de_rotterdam":
	$page_a_inclure = "galerie/de_rotterdam.htm";
	$back_color = "#66CC33";
	break;
	
case "de_survivor":
	$page_a_inclure = "galerie/de_survivor.htm";
	$back_color = "#FF9999";
	break;
	
case "de_train":
	$page_a_inclure = "galerie/de_train.htm";
	$back_color = "#66CCFF";
	break;

case "de_vertigo":
	$page_a_inclure = "galerie/de_vertigo.htm";
	$back_color = "#CC66CC";
	break;
}

echo "<html>";
include "script.htm";
echo "<body bgcolor=#FFFFFF text=#000000 link=#CC0000 vlink=#CC0000 alink=#CC0000>";
echo "<server>";
echo "<STYLE TYPE='text/css'>";
echo "<!--";
echo "A { text-decoration: none; }";
echo "-->";
echo "</STYLE>";
echo "</server>";
echo "<style>BODY {";
echo "SCROLLBAR-FACE-COLOR: #CC0000; SCROLLBAR-HIGHLIGHT-COLOR: #000000; SCROLLBAR-SHADOW-COLOR: #000000; SCROLLBAR-3DLIGHT-COLOR: #FFFFFF; SCROLLBAR-ARROW-COLOR: #000000; SCROLLBAR-TRACK-COLOR: #FFFFFF; SCROLLBAR-DARKSHADOW-COLOR: #FFFFFF";
echo "}";
echo "</style>";
include "navigation.htm";
include "bot_page.htm";
echo "</body></html>";

?>