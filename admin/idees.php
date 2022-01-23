<?
include "secu.php";
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM idees ORDER by id");
$nbre =mysql_num_rows($req);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $nbre." idées dans la base"; ?></b></font></td>
  </tr>

  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
	  <?
  $i=1;
  while($disp = mysql_fetch_array($req))
  {
  ?><?
	echo $i.". <b> ".$disp[pseudo]." </b>: ".$disp[idee]."<br><br>";
	?><?
$i++;
}
?></font></td>
  </tr>
</table>
