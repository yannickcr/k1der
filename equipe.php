<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?
require("config.inc.php3");
?>
<div align="center"><font size="7" face="Verdana, Arial, Helvetica, sans-serif"><strong>-=K<font color="#FF0000">1der</font>=- 
  T<font color="#FF0000">eam</font></strong></font><br>
  <br>
  <table width="9" border="0" cellpadding="0" cellspacing="0" bordercolor="#000000">
    <tr bordercolor="#FFFFFF"> 
	<?
	$i = 0;
	if ($section != '')
	{
	$req = MYSQL_QUERY("SELECT * FROM equipe WHERE $section='oui' order by kinder");
	}
	else
	{
	$req = MYSQL_QUERY("SELECT * FROM equipe order by kinder");
	}
	$nbre =mysql_num_rows($req);
	while($disp = mysql_fetch_array($req))
	{
	$k1der = str_replace("é","e",$disp[kinder]);
	?>
	  <td width="5"><a href="index.php?page=team&section=<? echo $section; ?>&id=<? echo $disp[id]; ?>#player"><img src="images/logos/<? echo $k1der; ?>.gif" alt="-=K1der=- <? echo $disp[kinder]; ?>" border="0"></a></td>
	<?
	$i++;
	if ($i == 6)
	{
	echo "</tr></table><table width=9 border=0 cellpadding=0 cellspacing=0 bordercolor=#000000><tr bordercolor=#FFFFFF>";
	$i = 0;
	}
	}
	?>
    </tr>
  </table>
  <br>
  <strong></strong><br>
</div>
<br>
<?
if ($id != '')
{
$req = MYSQL_QUERY("SELECT * FROM equipe WHERE id=$id");
  $nbre =mysql_num_rows($req);
  $disp = mysql_fetch_array($req);
$k1der = str_replace("é","e",$disp[kinder]);
?>
<a name="player"></a>
<table width="536" border="0" cellspacing="0" cellpadding="0" bordercolor="#000000" align="center">
  <tr> 
    <td width="598" valign="bottom" bordercolor="#FFFFFF"> 
      <table width="100" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td><table width="100" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
              <tr> 
                <td bordercolor="#000000"> <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="150" height="200">
                    <param name="movie" value="flash/<? echo $k1der; ?>.swf">
                    <param name="quality" value="high">
                    <embed src="flash/<? echo $k1der; ?>.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="150" height="200"></embed></object></td>
              </tr>
            </table></td>
          <td valign="bottom"><strong> 
            <?
				if ($disp[statu] == "Mort")
				{
				$color="red";
				}
				else
				{
				$color="green";
				}
				?>
            <font size="2" face="Verdana, Arial, Helvetica, sans-serif" color=<? echo $color; ?>><? echo $disp[statu] ?></font></strong></td>
        </tr>
      </table>
      <strong> </strong></td>
    <td width="598" bordercolor="#FFFFFF"><img src="images/logos/logo_<? echo $k1der; ?>.gif" align="absmiddle"><strong> 
      </strong></td>
  </tr>
  <tr> 
    <td width="598" colspan="2" bordercolor="#FFFFFF"> <table width="536" border="0" cellspacing="0" cellpadding="0" bordercolor="#DE0200" bgcolor="#DE0200">
        <tr> 
          <td width="60" height="124" valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/joueur.gif" width="23" height="114"></font></td>
          <td width="465" valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
              <tr> 
                <td width="190" valign="top"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Nom 
                    :</font></div></td>
                <td width="10" rowspan="7" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                  </font></td>
                <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                  <? echo $disp[nom] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Pr&eacute;nom 
                    :</font></div></td>
                <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[prenom] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Age 
                    :</font></div></td>
                <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                  <?
				$ziday = date("Ymd");
				$sonage = $ziday-$disp[age];
				$sonage = $sonage/10000;
				$sonage = (int)$sonage; 
				echo $sonage; 
				?>
                  ans</font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">MSN 
                    :</font></div></td>
                <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><?
				$mail_0 = strstr ($disp[icq] , "@");
				$mail_1 = str_replace ($mail_0,"",$disp[icq]);
				$mail_2 = str_replace ("@","",$mail_0);
				?>
				<script language="JavaScript">
				var un="<? echo $mail_1; ?>";
				var deux = "<? echo $mail_2; ?>";
				document.write("<a href="+"ma"+"ilto:"+un+"[AT]"+deux+"><font color=white>"+un+"@"+deux+"</font></a>");
				</script></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">E-Mail 
                    :</font></div></td>
                <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
				<?
				$mail_0 = strstr ($disp[e_mail] , "@");
				$mail_1 = str_replace ($mail_0,"",$disp[e_mail]);
				$mail_2 = str_replace ("@","",$mail_0);
				?>
				<script language="JavaScript">
				var un="<? echo $mail_1; ?>";
				var deux = "<? echo $mail_2; ?>";
				document.write("<a href="+"ma"+"ilto:"+un+"[AT]"+deux+" title='Ecrire à <? echo $k1der; ?>'><font color=white>"+un+"@"+deux+"</font></a>");
				</script></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Ville 
                    :</font></div></td>
                <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[ville] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">R&ocirc;le 
                    :</font></div></td>
                <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[role] ?></font></td>
              </tr>
            </table></td>
          <td width="11" height="124" align="right" valign="top"><img src="images/littlehautdroite.gif" width="10" height="10"></td>
        </tr>
        <tr> 
          <td colspan="3" valign="middle" height="4"><img src="images/ligne.gif" width="536" height="5"></td>
        </tr>
        <tr> 
          <td width="60" valign="middle"> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/connection.gif" width="23" height="56"></font></div></td>
          <td width="465" valign="top"> <div align="right"> 
              <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
                <tr> 
                  <td width="190" valign="top"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Connection 
                      :</font></div></td>
                  <td width="10" rowspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                    </font></td>
                  <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                    <? echo $disp[conn_type] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">FAI 
                      :</font></div></td>
                  <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[conn_fai] ?></font></td>
                </tr>
              </table>
            </div></td>
          <td width="11" valign="middle">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="3" height="2"><img src="images/ligne.gif" width="536" height="5"></td>
        </tr>
        <tr> 
          <td width="60" valign="middle"> <div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/hardware.gif" width="23" height="158"></font></div></td>
          <td width="465" valign="top"> <div align="right"> 
              <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
                <tr> 
                  <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Processeur 
                      :</font></div></td>
                  <td width="10" rowspan="8" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">&nbsp; 
                    </font></td>
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[proc] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                      Graphique :</font></div></td>
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[graph] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Ram 
                      :</font></div></td>
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[ram] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                      Son :</font></div></td>
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[son] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                      M&egrave;re :</font></div></td>
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[mere] ?></font></td>
                </tr>
                <tr> 
                  <td width="190" valign="top"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Souris 
                      :</font></div></td>
                  <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
                    <? echo $disp[souris] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Clavier 
                      :</font></div></td>
                  <td width="265" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[clavier] ?></font></td>
                </tr>
                <tr> 
                  <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Tapis 
                      :</font></div></td>
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><? echo $disp[tapis] ?></font></td>
                </tr>
              </table>
            </div></td>
          <td width="11">&nbsp;</td>
        </tr>
		<?
		if (($disp[cs] == 'oui') && ($section != 'war3'))
		{
		?>
        <tr> 
          <td colspan="3"><img src="images/ligne.gif" width="536" height="5"></td>
        </tr>
        <tr> 
          <td valign="middle"><img src="images/counter.gif" width="24" height="237"></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
              <tr> 
                <td colspan="3" valign="top"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Map 
                    pr&eacute;f&eacute;r&eacute;e</font></div></td>
              </tr>
              <tr> 
                <td colspan="3" valign="top"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/cartes/<? echo $disp[mapcs]; ?>.jpg" width="109" height="81"><br>
                    <? echo $disp[mapcs] ?></font></div></td>
              </tr>
              <tr> 
                <td colspan="3" valign="top">&nbsp;</td>
              </tr>
              <tr> 
                <td colspan="3" valign="top">&nbsp;</td>
              </tr>
              <tr> 
                <td colspan="3" valign="top"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Arme 
                    pr&eacute;f&eacute;r&eacute;e</font></div></td>
              </tr>
              <tr> 
                <?
			  $arme = str_replace("/","",$disp[armecs]);
			  ?>
                <td colspan="3" valign="top"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/armes/<? echo $arme; ?>.gif"><br>
                    <? echo $disp[armecs] ?></font></div></td>
              </tr>
              <tr> 
                <td valign="top">&nbsp;</td>
                <td valign="top">&nbsp;</td>
                <td valign="top">&nbsp;</td>
              </tr>
              <tr> 
                <td width="190" valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Resolution 
                    :</font></div></td>
                <td width="10" valign="top">&nbsp;</td>
                <td width="265" valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[resocs] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Sensitivity 
                    :</font></div></td>
                <td valign="top">&nbsp;</td>
                <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[senscs] ?></font></td>
              </tr>
              <tr>
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Zoom Sensitivity Ratio : </font></div></td>
                <td valign="top">&nbsp;</td>
                <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[sens2cs] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Config 
                    :</font></div></td>
                <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?
				$k2der = str_replace(" ","_",$disp[kinder]);
				$k2der = str_replace("é","e",$k2der);
				$k2der = strtolower($k2der);
				if (file_exists("down/conf/".$k2der."_conf.zip"))
				{
				echo "<a href=down/conf/".$k2der."_conf.zip><font color=white>Downloader</font></a>";
				}
				else if (file_exists("down/conf/".$k2der."_conf.cfg"))
				{
				echo "<a href=down/conf/".$k2der."_conf.cfg><font color=white>Downloader</font></a>";
				}
				else if (file_exists("down/conf/".$k2der."_conf.exe"))
				{
				echo "<a href=down/conf/".$k2der."_conf.exe><font color=white>Downloader</font></a>";
				}
				else if (file_exists("down/conf/".$k2der."_conf.txt"))
				{
				echo "<a href=down/conf/".$k2der."_conf.txt><font color=white>Downloader</font></a>";
				}
				else if (file_exists("down/conf/".$k2der."_conf.rar"))
				{
				echo "<a href=down/conf/".$k2der."_conf.rar><font color=white>Downloader</font></a>";
				}
				else if (file_exists("down/conf/".$k2der."_conf.cab"))
				{
				echo "<a href=down/conf/".$k2der."_conf.cab><font color=white>Downloader</font></a>";
				}
				else
				{
				echo "Non Disponible";
				}
				?>
                  </font></td>
              </tr>
            </table></td>
          <td valign="bottom">&nbsp;</td>
        </tr>
		<?
		}
		if (($disp[war3] == 'oui') && ($section != 'cs'))
		{
		?>
        <tr> 
          <td colspan="3" valign="bottom"><img src="images/ligne.gif" width="536" height="5"></td>
        </tr>
        <tr> 
          <td width="60" valign="middle"> <div align="left"><img src="images/war3.gif" width="23" height="200"></div></td>
          <td width="465" valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
              <tr> 
                <td colspan="3" valign="top"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">H&eacute;ro</font><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    pr&eacute;f&eacute;r&eacute;</font></div></td>
              </tr>
              <tr>
                <td colspan="3" valign="top"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/heros/<? echo $disp[herow3]; ?>.gif"><br>
                    <? echo $disp[herow3] ?></font></div></td>
              </tr>
              <tr> 
                <td valign="top">&nbsp;</td>
                <td valign="top">&nbsp;</td>
                <td valign="top">&nbsp;</td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Carte 
                    Pr&eacute;f&eacute;r&eacute;e :</font></div></td>
                <td valign="top">&nbsp;</td>
                <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[mapw3] ?></font></td>
              </tr>
              <tr> 
                <td width="190" valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Resolution 
                    :</font></div></td>
                <td width="10" valign="top">&nbsp;</td>
                <td width="265" valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[resow3] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Compte 
                    BattleNet :</font></div></td>
                <td valign="top">&nbsp;</td>
                <td valign="top"><a href="<? echo $disp[urlw3]; ?>"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Voir</font></a></td>
              </tr>
            </table></td>
          <td width="11" valign="bottom">&nbsp;</td>
        </tr>
		<?
		}
		?>
        <tr> 
          <td colspan="3" valign="bottom"><img src="images/ligne.gif" width="536" height="5"></td>
        </tr>
        <tr> 
          <td valign="bottom"><img src="images/os.gif" width="24" height="45"></td>
          <td valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#DE0200" bgcolor="#DE0200">
              <tr> 
                <td width="190" valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Syst&egrave;me 
                    d'exploitation :</font></div></td>
                <td width="10" valign="top">&nbsp;</td>
                <td width="265" valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[os] ?></font></td>
              </tr>
              <tr> 
                <td valign="top"><div align="right"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">R&eacute;solution 
                    :</font></div></td>
                <td valign="top">&nbsp;</td>
                <td valign="top"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[reso2] ?></font></td>
              </tr>
            </table></td>
          <td align="right" valign="bottom">&nbsp;</td>
        </tr>
        <tr> 
          <td width="60" valign="bottom"><img src="images/littlebasgauche.gif" width="10" height="10"></td>
          <td width="465">&nbsp;</td>
          <td width="11" align="right" valign="bottom"><img src="images/littlebasdroite.gif" width="10" height="10"></td>
        </tr>
      </table></td>
  </tr>
</table>
<?
}
?>
</body>
</html>
