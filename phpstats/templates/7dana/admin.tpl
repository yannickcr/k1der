<html>
<head>
<title>$phpstats_title</title>
$meta
<META http-equiv=Page-Enter content=blendTrans(duration=0.5)>
<META http-equiv=page-exit content=blendtrans(duration=0.5)>
<link rel='stylesheet' href='./templates/7dana/styles.css' type='text/css'>
<script src='./templates/7dana/functions.js' type='text/javascript' language='javascript'></script>
</head>
<body>
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
         <tr>
           <td>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
         <tr>
           <td width="21"><img src="./templates/7dana/images/top_tab_left.gif" border="0" /></td>
           <td align="middle" width="100%" background="./templates/7dana/images/top_tab_bg.gif"></td>
           <td width="21"><img src="./templates/7dana/images/top_tab_right.gif" border="0" /></td>
         </tr>
      </table>
<table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#EEEFF0">
  <tr bgcolor="#464A51">
   <td height="30" colspan="2">
      <table width="100%">
	    <tr>
		  <td><img src="templates/7dana/images/logo.gif" alt="$option[nomesito]"></td>
		</tr>
      </table>
          <table width="100%" cellpadding="0" cellspacing="0">
   <TR>
  <td class='subhead' align='right'>$option[nomesito]&nbsp;
  </td>
  </TR>
</table>
	</td>
  </tr>
<tr>
  <td width='150' valign="top" class='left'>
<div id="masterdiv">
	<div class="dhtml_menutitle" onclick="SwitchMenu('sub1')">&raquo;&nbsp;Main</div>
	<span class="dhtml_submenu" id="sub1">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=main">$admin_menu[main]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=details">$admin_menu[details]</a>
	</span>
	<div class="dhtml_menutitle" onclick="SwitchMenu('sub2')">&raquo;&nbsp;Systems</div>
	<span class="dhtml_submenu" id="sub2">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=os_browser">$admin_menu[os_browser]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=reso">$admin_menu[reso]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=systems">$admin_menu[systems]</a>
	</span>
       <div class="dhtml_menutitle" onclick="SwitchMenu('sub3')">&raquo;&nbsp;Pages/Time</div>
		<span class="dhtml_submenu" id="sub3">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=pages">$admin_menu[pages]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=percorsi">$admin_menu[percorsi]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=time_pages">$admin_menu[time_pages]</a>
	</span>
       <div class="dhtml_menutitle" onclick="SwitchMenu('sub4')">&raquo;&nbsp;Referer Stats</div>
		<span class="dhtml_submenu" id="sub4">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=referer">$admin_menu[referer]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=engines">$admin_menu[engines]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=query">$admin_menu[query]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=searched_words">$admin_menu[searched_words]</a>
	</span>	
	<div class="dhtml_menutitle" onclick="SwitchMenu('sub5')">&raquo;&nbsp;Hourly/Daily/Monthly</div>
		<span class="dhtml_submenu" id="sub5">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=hourly">$admin_menu[hourly]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=daily">$admin_menu[daily]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=weekly">$admin_menu[weekly]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=monthly">$admin_menu[monthly]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=calendar">$admin_menu[calendar]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=compare">$admin_menu[compare]</a>
	</span>
	<div class="dhtml_menutitle" onclick="SwitchMenu('sub6')">&raquo;&nbsp;Other Stats</div>
		<span class="dhtml_submenu" id="sub6">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=ip">$admin_menu[ip]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=country">$admin_menu[country]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=bw_lang">$admin_menu[lang]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=downloads">$admin_menu[downloads]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=clicks">$admin_menu[clicks]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=trend">$admin_menu[trend]</a>
	</span>
	<div class="dhtml_menutitle" onclick="SwitchMenu('sub7')">&raquo;&nbsp;Admin</div>
		<span class="dhtml_submenu" id="sub7">
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=preferenze">$admin_menu[options]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=esclusioni">$admin_menu[esclusioni]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=optimize_tables">$admin_menu[optimize_tables]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=downadmin">$admin_menu[downadmin]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=clicksadmin">$admin_menu[clicksadmin]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=backup">$admin_menu[backup]</a><br>
		&nbsp;&raquo;&nbsp;<a href="admin.php?action=resett">$admin_menu[reset]</a>
	</span>	
</div>
<br><br>
       <!--Begin errorlogviewer-->
	        <p class="menu">&nbsp;&raquo;&nbsp;<a href="admin.php?action=viewerrorlog">$admin_menu[errorlogviewer]</a></p>
       <!--End errorlogviewer-->  
       <!--End is_loged_in-->
        <p class="menu">&nbsp;&raquo;&nbsp;<a href="admin.php?action=$admin_menu[status_rev]">$admin_menu[status]</a></p>
  </font>  </td>
  <td width="610" valign="top" bgcolor="#DBDDDF">
  $action
  <br>
   </td>
   </tr>
   <TR>
   <td class='copyright' colspan='2'><a href='http://www.php-stats.com'>Php-Stats ©&nbsp;Webmaster76</a>&nbsp; - $generation_time - $server_time&nbsp;&nbsp; | &nbsp;&nbsp;Skin design by <a href='http://www.7dana.com' target="_blank">7dana.com</a>&nbsp;</a></td>
   </TR>
</table>
   <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
         <tr>
           <td><img src="templates/7dana/images/bot_tab_left.gif" border="0" /></td>
           <td align="middle" width="100%" background="templates/7dana/images/bot_tab_bg.gif"></td>
           <td><img src="templates/7dana/images/bot_tab_right.gif" border="0" /></td>
         </tr>
      </table>
      </td>
         </tr>
      </table>
</body>
</html>