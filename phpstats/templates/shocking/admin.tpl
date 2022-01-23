<html>
<head>
<title>$phpstats_title</title>
$meta
<META http-equiv=Page-Enter content=blendTrans(duration=0.5)>
<META http-equiv=page-exit content=blendtrans(duration=0.5)>
<link rel='stylesheet' href='./templates/shocking/styles.css' type='text/css'>
<script src='templates/shocking/functions.js' type='text/javascript' language='javascript'></script>
$autorefresh
</head>
<body>

<!-- header -->
<table align="center" width="100%">
  <tr><td class="credits">
    <a class="creditslink" href='http://www.php-stats.com'>Php-Stats © Webmaster76</a> - $generation_time - $server_time - <a class="creditslink" href="mailto:mindlab@mindlab.it">Shocking Template by Mind Lab Solutions</a>
  </td></tr>
  <tr><td class="indexcubes"></td></tr>
  <tr>
    <td class="indexheader"><img src="templates/shocking/images/logo.gif"></td>
  </tr>
  <tr><td class="indexcubes"></td></tr>
  <tr><td class="indexsite"><span class='nomesito'>$option[nomesito]</span></td></tr>
  <tr><td class="indexcubes"></td></tr>
</table>

<!-- menu & page -->
<table width="100%" align="center" cellspacing="0">
<tr>
 <td width="180" valign="top" class="menumenu">
        <table width="180" width="100%">
        
        <tr><td class="headerblu">Statistiche</td></tr>
        
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=main">$admin_menu[main]</a></p></td></tr>

        <!--Begin details-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=details">$admin_menu[details]</a></p></td></tr>
        <!--End details-->

        <!--Begin systems-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=os_browser">$admin_menu[os_browser]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=reso">$admin_menu[reso]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=systems">$admin_menu[systems]</a></p></td></tr>
        <!--End sytems-->

        <!--Begin pages_time-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=pages">$admin_menu[pages]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=percorsi">$admin_menu[percorsi]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=time_pages">$admin_menu[time_pages]</a></p></td></tr>
        <!--End pages_time-->

        <!--Begin referer_engines-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=referer">$admin_menu[referer]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=engines">$admin_menu[engines]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=query">$admin_menu[query]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=searched_words">$admin_menu[searched_words]</a></p></td></tr>
        <!--End referer_engines-->

        <!--Begin hourly-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=hourly">$admin_menu[hourly]</a></p></td></tr>
        <!--End hourly-->

        <!--Begin daily_monthly-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=daily">$admin_menu[daily]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=weekly">$admin_menu[weekly]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=monthly">$admin_menu[monthly]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=calendar">$admin_menu[calendar]</a></p></td></tr>
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=compare">$admin_menu[compare]</a></p></td></tr>
        <!--End daily_monthly-->

        <!--Begin ip-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=ip">$admin_menu[ip]</a></p></td></tr>
        <!--End ip-->

        <!--Begin country-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=country">$admin_menu[country]</a></p></td></tr>
        <!--End country-->

        <!--Begin bw_lang-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=bw_lang">$admin_menu[bw_lang]</a></p></td></tr>
        <!--End bw_lang-->

        <!--Begin downloads-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=downloads">$admin_menu[downloads]</a></p></td></tr>
        <!--End downloads-->

        <!--Begin clicks-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=clicks">$admin_menu[clicks]</a></p></td></tr>
        <!--End clicks-->

	     <!--Begin daily_monthly-->
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=trend">$admin_menu[trend]</a></p></td></tr>
	     <!--End daily_monthly-->
        <tr><td height="10"></td></tr>

        <tr><td class="headerblu">Amministrazione</td></tr>
        <!--Begin is_loged_in-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=preferenze">$admin_menu[options]</a></p></td></tr>
		  <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=esclusioni">$admin_menu[esclusioni]</a></p></td></tr>
		  <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=optimize_tables">$admin_menu[optimize_tables]</a></p></td></tr>
	     <!--Begin downloads-->
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=downadmin">$admin_menu[downadmin]</a></p></td></tr>
	     <!--End downloads-->
	     <!--Begin clicks-->
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=clicksadmin">$admin_menu[clicksadmin]</a></p></td></tr>
	     <!--End clicks-->
        <!--Begin modify config-->
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=modify_config">$admin_menu[modifyconfig]</a></p></td></tr>
	     <!--End modify config-->
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=backup">$admin_menu[backup]</a></p></td></tr>
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=resett">$admin_menu[reset]</a></p></td></tr>
        <!--Begin errorlogviewer-->
	     <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=viewerrorlog">$admin_menu[errorlogviewer]</a></p></td></tr>
        <!--End errorlogviewer-->
        <!--End is_loged_in-->
        <tr onMouseOver="this.style.backgroundColor='#EEEEEE';" onMouseOut="this.style.backgroundColor='#FFFFFF';"><td class="menutd"><p class="menu"><img align="absmiddle" src="templates/shocking/images/bullet.gif"> <a href="admin.php?action=$admin_menu[status_rev]">$admin_menu[status]</a></p></td></tr>
        </table>
  </font>
  </td>
  <td valign="top">
  $action
  </td>
  </tr>
</table>
</body>
</html>