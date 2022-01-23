<?
function get_meteo ($sortie)
{

//Récupération du nom des servers

//if (eregi("<!-- gametiger results -->(.*)<!-- /gametiger results -->", $sortie, $servers))
if (eregi("href=/search(.*)&nbsp;</a></td><td align=right>", $sortie, $servers))

list($server1,$server2,$server3,$server4,$server5,$server6,$server7,$server8,$server9,$server10,$server11,$server12,$server13,$server14,$server15)= split ("</a></td><td align=right>", $servers[0]);

$server2 = strstr ($server2, "<a href=/search?address=");
$server3 = strstr ($server3, "<a href=/search?address=");
$server4 = strstr ($server4, "<a href=/search?address=");
$server5 = strstr ($server5, "<a href=/search?address=");
$server6 = strstr ($server6, "<a href=/search?address=");
$server7 = strstr ($server7, "<a href=/search?address=");
$server8 = strstr ($server8, "<a href=/search?address=");
$server9 = strstr ($server9, "<a href=/search?address=");
$server10 = strstr ($server10, "<a href=/search?address=");
$server11 = strstr ($server11, "<a href=/search?address=");
$server12 = strstr ($server12, "<a href=/search?address=");
$server13 = strstr ($server13, "<a href=/search?address=");
$server14 = strstr ($server14, "<a href=/search?address=");
$server15 = strstr ($server15, "<a href=/search?address=");

$server1 = strstr ($server1, ">");
$server2 = strstr ($server2, ">");
$server3 = strstr ($server3, ">");
$server4 = strstr ($server4, ">");
$server5 = strstr ($server5, ">");
$server6 = strstr ($server6, ">");
$server7 = strstr ($server7, ">");
$server8 = strstr ($server8, ">");
$server9 = strstr ($server9, ">");
$server10 = strstr ($server10, ">");
$server11 = strstr ($server11, ">");
$server12 = strstr ($server12, ">");
$server13 = strstr ($server13, ">");
$server14 = strstr ($server14, ">");
$server15 = strstr ($server15, ">");

$server1 = str_replace (">","",$server1);
$server2 = str_replace (">","",$server2);
$server3 = str_replace (">","",$server3);
$server4 = str_replace (">","",$server4);
$server5 = str_replace (">","",$server5);
$server6 = str_replace (">","",$server6);
$server7 = str_replace (">","",$server7);
$server8 = str_replace (">","",$server8);
$server9 = str_replace (">","",$server9);
$server10 = str_replace (">","",$server10);
$server11 = str_replace (">","",$server11);
$server12 = str_replace (">","",$server12);
$server13 = str_replace (">","",$server13);
$server14 = str_replace (">","",$server14);
$server15 = str_replace (">","",$server15);

//Récupération du nom des joueurs

eregi("<font face=\"helvetica,arial,sans-serif\">(.*)</td><td><font face=\"helvetica,arial,sans-serif\">", $sortie, $joueurs);

list($joueur1,$joueur2,$joueur3,$joueur4,$joueur5,$joueur6,$joueur7,$joueur8,$joueur9,$joueur10,$joueur11,$joueur12,$joueur13,$joueur14,$joueur15)= split ("</td><td>", $joueurs[0]);

$joueur2 = strstr ($joueur2, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur3 = strstr ($joueur3, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur4 = strstr ($joueur4, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur5 = strstr ($joueur5, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur6 = strstr ($joueur6, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur7 = strstr ($joueur7, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur8 = strstr ($joueur8, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur9 = strstr ($joueur9, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur10 = strstr ($joueur10, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur11 = strstr ($joueur11, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur12 = strstr ($joueur12, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur13 = strstr ($joueur13, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur14 = strstr ($joueur14, "<td><font face=\"helvetica,arial,sans-serif\">");
$joueur15 = strstr ($joueur15, "<td><font face=\"helvetica,arial,sans-serif\">");

$joueur1 = str_replace ("<font face=\"helvetica,arial,sans-serif\">","",$joueur1);
$joueur2 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur2);
$joueur3 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur3);
$joueur4 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur4);
$joueur5 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur5);
$joueur6 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur6);
$joueur7 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur7);
$joueur8 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur8);
$joueur9 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur9);
$joueur10 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur10);
$joueur11 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur11);
$joueur12 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur12);
$joueur13 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur13);
$joueur14 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur14);
$joueur15 = str_replace ("<td><font face=\"helvetica,arial,sans-serif\">","",$joueur15);

//Récupération des IPs

eregi("<a href=/connect(.*)game=cstrike", $sortie, $ips);

list($ip1,$ip2,$ip3,$ip4,$ip5,$ip6,$ip7,$ip8,$ip9,$ip10,$ip11,$ip12,$ip13,$ip14,$ip15)= split ("&game=cstrike>", $ips[0]);

$ip2 = strstr ($ip2, "<a href=/connect?address=");
$ip3 = strstr ($ip3, "<a href=/connect?address=");
$ip4 = strstr ($ip4, "<a href=/connect?address=");
$ip5 = strstr ($ip5, "<a href=/connect?address=");
$ip6 = strstr ($ip6, "<a href=/connect?address=");
$ip7 = strstr ($ip7, "<a href=/connect?address=");
$ip8 = strstr ($ip8, "<a href=/connect?address=");
$ip9 = strstr ($ip9, "<a href=/connect?address=");
$ip10 = strstr ($ip10, "<a href=/connect?address=");
$ip11 = strstr ($ip11, "<a href=/connect?address=");
$ip12 = strstr ($ip12, "<a href=/connect?address=");
$ip13 = strstr ($ip13, "<a href=/connect?address=");
$ip14 = strstr ($ip14, "<a href=/connect?address=");
$ip15 = strstr ($ip15, "<a href=/connect?address=");

$ip1 = str_replace ("<a href=/connect?address=","",$ip1);
$ip2 = str_replace ("<a href=/connect?address=","",$ip2);
$ip3 = str_replace ("<a href=/connect?address=","",$ip3);
$ip4 = str_replace ("<a href=/connect?address=","",$ip4);
$ip5 = str_replace ("<a href=/connect?address=","",$ip5);
$ip6 = str_replace ("<a href=/connect?address=","",$ip6);
$ip7 = str_replace ("<a href=/connect?address=","",$ip7);
$ip8 = str_replace ("<a href=/connect?address=","",$ip8);
$ip9 = str_replace ("<a href=/connect?address=","",$ip9);
$ip10 = str_replace ("<a href=/connect?address=","",$ip10);
$ip11 = str_replace ("<a href=/connect?address=","",$ip11);
$ip12 = str_replace ("<a href=/connect?address=","",$ip12);
$ip13 = str_replace ("<a href=/connect?address=","",$ip13);
$ip14 = str_replace ("<a href=/connect?address=","",$ip14);
$ip15 = str_replace ("<a href=/connect?address=","",$ip15);

$ip1 = str_replace ("&game=cstrike","",$ip1);
$ip2 = str_replace ("&game=cstrike","",$ip2);
$ip3 = str_replace ("&game=cstrike","",$ip3);
$ip4 = str_replace ("&game=cstrike","",$ip4);
$ip5 = str_replace ("&game=cstrike","",$ip5);
$ip6 = str_replace ("&game=cstrike","",$ip6);
$ip7 = str_replace ("&game=cstrike","",$ip7);
$ip8 = str_replace ("&game=cstrike","",$ip8);
$ip9 = str_replace ("&game=cstrike","",$ip9);
$ip10 = str_replace ("&game=cstrike","",$ip10);
$ip11 = str_replace ("&game=cstrike","",$ip11);
$ip12 = str_replace ("&game=cstrike","",$ip12);
$ip13 = str_replace ("&game=cstrike","",$ip13);
$ip14 = str_replace ("&game=cstrike","",$ip14);
$ip15 = str_replace ("&game=cstrike","",$ip15);

if ($joueur1 != "") { $joueur1 = "&nbsp;<font title=\"$server1
IP: $ip1\">$joueur1</font><br>"; }
if ($joueur2 != "") { $joueur2 = "&nbsp;<font title=\"$server2
IP: $ip2\">$joueur2</font><br>"; }
if ($joueur3 != "") { $joueur3 = "&nbsp;<font title=\"$server3
IP: $ip3\">$joueur3</font><br>"; }
if ($joueur4 != "") { $joueur4 = "&nbsp;<font title=\"$server4
IP: $ip1\">$joueur4</font><br>"; }
if ($joueur5 != "") { $joueur5 = "&nbsp;<font title=\"$server5
IP: $ip1\">$joueur5</font><br>"; }
if ($joueur6 != "") { $joueur6 = "&nbsp;<font title=\"$server6
IP: $ip1\">$joueur6</font><br>"; }
if ($joueur7 != "") { $joueur7 = "&nbsp;<font title=\"$server7
IP: $ip1\">$joueur7</font><br>"; }
if ($joueur8 != "") { $joueur8 = "&nbsp;<font title=\"$server8
IP: $ip1\">$joueur8</font><br>"; }
if ($joueur9 != "") { $joueur9 = "&nbsp;<font title=\"$server9
IP: $ip1\">$joueur9</font><br>"; }
if ($joueur10 != "") { $joueur10 = "&nbsp;<font title=\"$server10
IP: $ip1\">$joueur10</font><br>"; }
if ($joueur11 != "") { $joueur11 = "&nbsp;<font title=\"$server11
IP: $ip1\">$joueur11</font><br>"; }
if ($joueur12 != "") { $joueur12 = "&nbsp;<font title=\"$server12
IP: $ip1\">$joueur12</font><br>"; }
if ($joueur13 != "") { $joueur13 = "&nbsp;<font title=\"$server13
IP: $ip1\">$joueur13</font><br>"; }
if ($joueur14 != "") { $joueur14 = "&nbsp;<font title=\"$server14
IP: $ip1\">$joueur14</font><br>"; }
if ($joueur15 != "") { $joueur15 = "&nbsp;<font title=\"$server15
IP: $ip1\">$joueur15</font><br>"; }

$joueurs = "$joueur1$joueur2$joueur3$joueur4$joueur5$joueur6$joueur7$joueur8$joueur9$joueur10$joueur11$joueur12$joueur13$joueur14$joueur15";

if ($joueurs == "")
{
$joueurs = "Aucun";
}

return "<center>$joueurs</center>";
}
ob_start("get_meteo");
readfile("http://www.gametiger.net/search?game=cstrike&player=k1der&submitButton=Player+Search&count=15");
ob_end_flush();
?>