
<script language="Javascript">
function Open()
	{
	window.open("irc/web_irc.php","K1der","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=0,width=600,height=501")
//	window.open'administ/modifier.php3?id='+data;
}
</script>
  
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="434" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="31" height="42" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
  </tr>
  <tr> 
    <td width="434" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>K1der</b></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">=-</font></b></font><font color="#FFFFFF"><b> 
      Server</b></font></b></font></td>
  </tr>
    <tr> 
      
    <td height="105" colspan="2">
	<?php
/* ------------------------------------------------------------- */
// Module CounterStrike Status
// Auteur : Boz (boz@gorets.com)
// D'après les travaux de Henrik Schack Jensen (henrik@schack.dk)
/* ------------------------------------------------------------- */
/*    ATTENTION ! NE MODIFIEZ RIEN EN DESSOUS DE CETTE LIGNE !   */
/* ------------------------------------------------------------- */
//require("live/serveur.php");
//require("live/counterstrike.php");
if(!$HTTP_POST_VARS["serveradr"] AND !$HTTP_POST_VARS["serverport"]){
	$serveradr = $ip;
	$serverport= $port;
}
else {
	$serveradr = str_replace(".","",$HTTP_POST_VARS["serveradr"]);
	if(is_numeric($serveradr) AND is_numeric($HTTP_POST_VARS["serverport"])){
    	$serveradr = trim($HTTP_POST_VARS["serveradr"]);
    	$serverport= trim($HTTP_POST_VARS["serverport"]);
	}
	else {
    	$serveradr = $ip;
    	$serverport= $port;
	}
}
$csinfo = new CounterStrike;
$status = $csinfo->getServerInfo($serveradr,$serverport,1000);
if ($status) {
    $status = $csinfo->getServerPlayers($serveradr,$serverport,1000);
    $status = $csinfo->getServerRules($serveradr,$serverport,1000);
    $rules = $csinfo->m_serverrules;
?>
      <TABLE width="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" CLASS="tblg">
        <TR> 
          <TD COLSPAN="2" CLASS="serveur">&nbsp;</TD>
        </TR>
        <TR> 
          <TD COLSPAN="2" CLASS="serveur"> <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $csinfo->m_servervars["servername"] ?></font></strong></TD>
        </TR>
        <TR> 
          <TD CLASS="map">&nbsp;</TD>
          <TD CLASS="data">&nbsp;</TD>
        </TR>
        <TR> 
          <TD CLASS="map"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Map 
              en cours</font></div></TD>
          <TD CLASS="data"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Infos 
            serveur</strong></font></TD>
        </TR>
        <TR> 
          <TD ALIGN="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><IMG SRC="images/mappics/<?php if (@filesize("images/mappics/".$csinfo->m_servervars["mapname"].".jpg")!="") echo $csinfo->m_servervars["mapname"]; else echo "nomap"; ?>.jpg" BORDER="0" WIDTH="160" HEIGHT="120" ALT="<?php echo $csinfo->m_servervars["mapname"]?>" TITLE="<?php echo $csinfo->m_servervars["mapname"]?>"><br>
            <?php echo $csinfo->m_servervars["mapname"]?></font></TD>
          <TD valign="top"><p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><br>
              I</b><strong>P :</strong> <?php echo $serveradr ?> <b>Por</b><strong>t 
              :</strong> <?php echo $serverport ?><BR>
              <b>Joueur</b><strong>s :</strong> <?php echo $csinfo->m_servervars["currentplayers"] ?> 
              sur <?php echo $csinfo->m_servervars["maxplayers"]?><BR>
              <b>Friendly Fir</b><strong>e :</strong> 
              <?php if($rules["mp_friendlyfire"]==0) echo "Off"; else echo "On"; ?>
              <BR>
              <strong>Accès </strong><strong>:</strong> 
              <?php if($rules["sv_password"]==0) echo "Public"; else echo "Privé"; ?>
              </font></p>
            <p>&nbsp;</p></TD>
        </TR>
      </TABLE>
      <DIV CLASS="titreblocg"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
        <br>
        <strong>Joueurs en ligne<br>
        </strong></font></DIV> 
      <TABLE width="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" CLASS="tblg">
        <?php
if (is_array($csinfo->m_playerinfo)) {
?>
        <TR> 
          <TH><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pseudo</font></TH>
          <TH ALIGN="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Frags</font></TH>
          <TH ALIGN="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Durée</font></TH>
        </TR>
        <?php
	while (list(,$player) = each ($csinfo->m_playerinfo)) {
?>
        <TR> 
          <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  <?
		  if(ereg('-=K1der=-',$player["name"]))
		  {
		  $player['name'] = str_replace("-=K1der=-","-=<font color=black>K</font>1der=-",$player['name']);
		  echo "<font color=#CC0000><b>"
		  ?>
		  <?=$player["name"]?>
		  <?
		  echo "</b></font>";
		  }
		  else
		  {
		  ?>
		  <?=$player["name"]?>
		  <?
		  }
		  ?>
            &nbsp;</font></TD>
          <TD ALIGN="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
            <?=$player["frags"]?>
            </font></TD>
          <TD ALIGN="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?=$player["time"]?>
            </font></TD>
        </TR>
        <?php
	}
}
else {
?>
        <TR> 
          <TD ALIGN="center"><P><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><B><I>Le 
              serveur est vide</I></B></font> 
            <P></TD>
        </TR>
        <?php
}
?>
      </TABLE>
      <?php
	} 
	else {
	echo "<B>Erreur de communication avec le serveur</B>\n";
} 
?>
      <br>
      <br>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="100%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Configuration 
            du server</strong></font></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <?php
$ij = 0;
reset ($rules);
ksort ($rules);
while (list($name,$value) = each ($rules)) { 
?>
          <td width="50%"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
			echo $name." = ".$value."<br>";
			?>
            </font></td>
          <?php
$ij++;
if ($ij == 2)
{
echo "<tr></tr>";
$ij = 0;
} 
}
?>
          <td width="50%">&nbsp;</td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
  <H2 class=news style="MARGIN-TOP: 0px; MARGIN-BOTTOM: 0.2em">&nbsp;</H2>
