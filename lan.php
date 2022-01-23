<?php
$titrepage = "-=K1der=- The Chocolat Effect";
switch($lan)
{
case "fragedition":
	$page_a_inclure = "lan/frag_edition.htm";
	break;
case "lananico":
	$page_a_inclure = "lan/lan_a_nico.htm";
	break;
case "landebriec2":
	$page_a_inclure = "lan/lan_de_briec2.htm";
	break;
case "landedaoulas":
	$page_a_inclure = "lan/lan_de_daoulas.htm";
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