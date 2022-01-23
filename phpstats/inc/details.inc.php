<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');
// Variabili esterne
if(isset($_GET['start'])) $start=addslashes($_GET['start']); else $start=0;
if(isset($_GET['mode'])) $mode=addslashes($_GET['mode'])-0; else $mode=0;
$filter_number=
''.
($_GET['show_bw']=='1' ? '1' : '0').
($_GET['show_gr']=='1' ? '1' : '0').
($_GET['show_sp']=='1' ? '1' : '0');
if($filter_number==='000') $filter_number='111';

function details(){
global $db,$string,$error,$style,$option,$varie,$start,$modulo,$phpstats_title,$mode,$pref,$filter_number;

switch($filter_number)
  {
  case '100': $filter_agents="WHERE os NOT REGEXP 'Spider|Grabber' AND os!=''"; break;
  case '010': $filter_agents="WHERE os='Grabber'"; break;
  case '001': $filter_agents="WHERE os='Spider'"; break;
  case '110': $filter_agents="WHERE os!='Spider'"; break;
  case '101': $filter_agents="WHERE os!='Grabber'"; break;
  case '011': $filter_agents="WHERE os REGEXP 'Spider|Grabber'"; break;
  default: $filter_agents='';
  }
$filter_params=($filter_number{0}==='1' ? '&show_bw=1' : '').($filter_number{1}==='1' ? '&show_gr=1' : '').($filter_number{2}==='1' ? '&show_sp=1' : '');

//Array_Connessioni
$arrayTypeConn=Array(
'0'=>'',
'1'=>'Dial-Up',
'2'=>'ADSL',
'3'=>'Lan',
'4'=>'Wireline',
'5'=>'GPRS',
'6'=>'UMTS',
'7'=>'WI-FI');

$elenco0='';
//Cerco gli isp disponibili
$location='isp_db/';
$hook=opendir($location);
while(($file=readdir($hook)) !== false){
        if($file != '.' && $file != '..'){
      $path=$location . '/' . $file;
          if(is_dir($path)) $elenco0[]=$file;
        }
}
closedir($hook);
// Fine lettura directory isp-db
$range_isp='|';
if(is_array($elenco0)){
  @natsort($elenco0);
  while(list($key, $val)=each($elenco0)){
         $val=chop($val);
     $range_isp.=$val.'|';
  }
}
include("lang/$option[language]/bw_lang.php");
// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['details_title'];

$rec_pag=10; // risultati visualizzati per pagina

include("lang/$option[language]/domains_lang.php");

$last_visitor_id='';
$return=
"<script>
function whois(url) {
        test=window.open(url,'nome','SCROLLBARS=1,STATUS=NO,TOOLBAR=NO,RESIZABLE=YES,LOCATION=NO,MENU=NO,WIDTH=450,HEIGHT=600,LEFT=0,TOP=0');
}
</script>\n";

$query_tot=sql_query("SELECT count(DISTINCT visitor_id) FROM $option[prefix]_details $filter_agents");
list($num_totale)=@mysql_fetch_row($query_tot);

if($num_totale>0)
  {
  // Titolo
  $return.="<span class=\"pagetitle\">$phpstats_title</span>";

  $numero_pagine=ceil($num_totale/$rec_pag);
  $pagina_corrente=ceil(($start/$rec_pag)+1);
  if($numero_pagine>1) $return.=
                       "<div align=\"right\"><span class=\"testo\">".
                       str_replace(Array('%current%','%total%'),Array($pagina_corrente,$numero_pagine),$varie['pag_x_y']).
                       "&nbsp;&nbsp;</span></div>";
  $return.="\n\n<ol start=".(1+(($pagina_corrente-1)*$rec_pag)).">";
  $result=sql_query("SELECT visitor_id FROM $option[prefix]_details $filter_agents GROUP BY visitor_id ORDER BY date DESC LIMIT $start,$rec_pag");
  $closed=1;
  while($row=@mysql_fetch_row($result))
    {
    $count=0;
    list($details_id)=$row;
    $result2=sql_query("SELECT ip,host,os,bw,lang,date,referer,currentPage,reso,colo,titlePage FROM $option[prefix]_details WHERE visitor_id='$details_id' ORDER BY date ASC");
    $visitor_pages=@mysql_num_rows($result2);
    while($row2=@mysql_fetch_row($result2))
      {
      list($details_ip,$details_host,$details_os,$details_bw,$details_language,$details_date,$details_referer,$details_currentPage,$details_reso,$details_colo,$details_titlePage)=$row2;
      if($count==0)
        {
        if($closed==0)
          {
          $return.="\n\t\t</table>\n\t\t</td>\n\t</tr>\n</table>";
          $closed=1;
          }
        $return.=
        "\n\n<!--  NEW VISITOR-->".
        "\n<br>\n<li class=\"testo\"><span class=\"testo\">".formatdate($details_date)." - ".formattime($details_date)."</span><br>".
        "\n<table width=\"90%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\" bordercolor=\"#000000\">\n\t<tr>\n\t\t<td bordercolor=\"#d9dbe9\">".
        "\n\t\t<table $style[table_header] width=\"100%\">".
        "\n\t\t\t<tr>".
        "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[details_os]</center></span></td>".
        "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[details_browser]</center></span></td>".
        "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[details_reso]</center></span></td>".
        "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[details_ip]</center></span></td>".
        "</tr>";

        // Corpo della tabella
        $return.=
        "\n\t\t\t<tr bgcolor=\"#B3C0D7\" onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">".
        "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">";

        $viewReso=1;
        $rangeMacro='-Spider,Grabber-';
        $nome_bw=$details_bw;
        if(@strpos($rangeMacro,$details_os))
          {
          $nome_os=$details_os;
          $viewReso=0;
          }
        else $nome_os=chop($details_os);

        $return.=$nome_os."</span></td>".
            "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".$nome_bw."</span></td>";

        $reso='';
        if($viewReso)
          {
          $reso=($details_reso=='' ? '?' : $details_reso);
          if($details_colo!='' && $details_colo!='?') $reso.=" $details_colo bit";
          }
        else $reso='N/A';
        $return.=
        "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$reso</span></td>".
        "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">";

        if($option['ext_whois']=='') $return.="<a href=\"javascript:whois('whois.php?IP=$details_ip');\">$details_ip</a>";
              else $return.="<a href=\"".str_replace('%IP%',$details_ip,$option['ext_whois'])."\" target=\"_BLANK\">$details_ip</a>";

        $return.=
        "</span></td>".
        "</tr>".
        "\n\t\t</table>";

        $count=0;
        $table='';

        // LINGUA DEL BROWSER
        if($bw_lang!='' && isset($bw_lang[$details_language])) // Non visualizzo se non è settata la lingua
          {
          $table.=
          "\n\t\t\t<tr>".
          "<td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_lang]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".$lang=$bw_lang[$details_language]."</span></td>".
          "</tr>";
          ++$count;
          }

        // PAESE
        if($option['ip-zone'] && $modulo[7])
              {
          $ip_number=sprintf('%u',ip2long($details_ip))-0;
          if(
            ($ip_number>=3232235520 && $ip_number<=3232301055) || //192.168.0.0 ... 192.168.255.255
            ($ip_number>=167772160 && $ip_number<=184549375) || //10.0.0.0 ... 10.255.255.255
            ($ip_number>=2886729728 && $ip_number<=2887778303) || //172.16.0.0 ... 172.31.255.255
            ($ip_number>=0 && $ip_number<=16777215) || //0.0.0.0 ... 0.255.255.255
            ($ip_number>=4026531840 && $ip_number<=4294967295) //240.0.0.0 ... 255.255.255.255
            ) $domain='lan';
          else if($option['ip-zone']==1)
            {
            $result3=sql_query("SELECT tld FROM $option[prefix]_ip_zone WHERE $ip_number BETWEEN ip_from AND ip_to");
            if(@mysql_affected_rows()>0) list($domain)=@mysql_fetch_row($result3); else $domain='unknown';
            }
          else if($option['ip-zone']==2) $domain=getIP($ip_number,23,'ip-to-country.db','ip-to-country.idx',2);
          $domain2=$domain;
          $domain=$domain_name[$domain];
          $table.=
              "\n\t\t\t<tr>".
          "<td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_country]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".$domain."</span></td>".
          "</tr>";
          if(strpos($range_isp,$domain2))
            {
            $isp_recognize=getIP($ip_number,148,$location.$domain2."/$domain2.db",$location.$domain2."/$domain2.idx",127);
            if($isp_recognize!='unknown')
              {
              $isp_recognize=rtrim($isp_recognize);
              list($conn_type,$isp_string,$isp_descr)=explode('|',$isp_recognize);
              $table.=
              "\n\t\t\t<tr>".
              "<td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_isp]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$isp_string</span></td>".
              "</tr>";
              if($conn_type!=0) $table.="\n\t\t\t<tr><td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_connection]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$arrayTypeConn[$conn_type]</span></td></tr>";
              if($isp_descr!='') $table.="\n\t\t\t<tr><td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_descr]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$isp_descr</span></td></tr>";
              }
            }
          }

        // HOST
        if($details_host!='' && $details_host!=$details_ip) // Non visualizzo se HOST è vuoto
          {
          $table.=
          "\n\t\t\t<tr>".
          "<td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_server]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$details_host</span></td>".
          "</tr>";
          ++$count;
          }

        // REFERER o MOTORE DI RICERCA
        if($details_referer!='')
          {
          $table.="\n\t\t\t<tr>";
          if(substr($details_referer,0,4)==='http')
            {
            $table.="<td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_referer]&nbsp;</center></span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">";
            $details_referer=htmlspecialchars($details_referer);
            $table.=
            formaturl($details_referer,'',70,35,-30).
            "</span></td></tr>";
            }
          else
            {
            list($nome_motore,$domain,$query,$resultPage,$engineUrl)=explode('|',$details_referer);
            $engineUrl=htmlspecialchars($engineUrl);
            $query=stripslashes($query); // \' -> '
            $image='images/engines.php?q='.str_replace(' ','-',$nome_motore.'');
            $motore_string=$nome_motore.' ('.$domain_name[$domain].')';
            if($resultPage>0) $motore_string.=', '.$string['se_page'].': '.$resultPage;
            $table.="<td bgcolor=$style[table_title_bgcolor] width=\"5%\" nowrap><span class=\"tabletitle\"><center>&nbsp;$string[details_referer]&nbsp;</center></span></td>".
            "<td bgcolor=$style[table_bgcolor]>".
            "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">".
            "<tr>".
            "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\"><img src=\"$image\" align=\"absmiddle\"> $motore_string</span></td>".
            "<td bgcolor=$style[table_title_bgcolor] width=\"50\" nowrap><span class=\"tabletitle\"><center>$string[details_query]</center></span></td>".
            "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$query</span></td>".
            "<td bgcolor=$style[table_bgcolor] width=\"11\"><a href=\"$engineUrl\" target=\"_BLANK\"><img src=\"templates/$option[template]/images/icon_viewlink.gif\" border=0 ALT=\"$string[alt_visitlink]\"></a></td>".
            "</tr>".
            "</table>".
            "</span></td></tr>";
            }

          ++$count;
          }
        if($count) $return.=
                   "\n\t\t<table $style[table_header] width=\"100%\">".
                   $table.
                   "\n\t\t</table>";

        $details_titlePage=stripslashes(trim($details_titlePage));
        $return.=
        "\n\t\t<table border=\"0\" $style[table_header] width=\"100%\">".
        "\n\t\t\t<tr><td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[details_ora]</center></span></td><td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>".str_replace("%VISITEDPAGES%",$visitor_pages,$string['details_pageviewed'])."</center></span></td></tr>";
        if($option['page_title'] && $option['refresh_page_title']==0 && $details_titlePage=='?')
              {
          $result4=sql_query("SELECT titlePage FROM $option[prefix]_pages WHERE data='$details_currentPage'");
          if(@mysql_affected_rows()>0) list($details_titlePage)=@mysql_fetch_row($result4);
          }
        $return.=
        "\n\t\t\t<tr bgcolor=\"#B3C0D7\" onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this,'$style[table_bgcolor]', '$style[table_bgcolor]')\"><td bgcolor=$style[table_bgcolor] width=\"10%\"><span class=\"tabletextA\">&nbsp;".formattime($details_date)."&nbsp;</span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".formaturl($details_currentPage,$details_titlePage,70,35,-30,$details_titlePage,$mode)."</span></td></tr>";
        $closed=0;
        }
      else
        {
        $return.=check_details($details_date,$details_currentPage,$details_titlePage,$mode);
        $closed=0;
        }
        ++$count;
      }
    }
  if($closed==0)
    {
    $return.="</table></td></tr></table>";
    $closed=1;
    }
  $return.="</ol>";

  // EXPORT EXCEL
  $result=sql_query("SELECT data FROM $option[prefix]_daily ORDER BY data ASC LIMIT 0,1");
  $year_now=date('Y');
  if(mysql_affected_rows()>0) while($row=@mysql_fetch_row($result)) list($anno_y,$mese_y,$giorno_y)=explode('-',$row[0]);
  else $anno_y=$year_now;

  $return.=
  "<br><form name=\"export\" action=\"./export.php\" method=\"POST\">".
  "<table border=\"0\" $style[table_header] width=\"416\" align=\"center\">".
  "<tr><td bgcolor=$style[table_title_bgcolor]><span class=\"tabletitle\">$string[export_title]</span></td></tr>".
  (extension_loaded('zlib') ? "<tr><td bgcolor=$style[table_bgcolor]><center><span class=\"tabletextA\">$string[backup_cmp]</span>&nbsp<select name=\"compress\"><option value=\"1\" selected>$pref[si]</option><option value=\"0\">$pref[no]</option></select></center></td></tr>" : '').
  "<tr><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\"><center>$string[export_text_01]</center></td></tr>".
  "<tr><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\"><center>$string[export_from]<SELECT name=data_1[0]><OPTION></OPTION>";
  for($i=1;$i<=31;++$i) $return.="<OPTION value='$i'>$i</OPTION>";
  $return.="</SELECT><SELECT name=data_1[1]><OPTION></OPTION>";
  for($i=1;$i<=12;++$i) $return.="<OPTION value='$i'>".$varie['mounts_1'][$i-1]."</OPTION>";
  $return.="</SELECT><SELECT name=data_1[2]><OPTION></OPTION>";
  for($i=$anno_y;$i<=$year_now;++$i) $return.="<OPTION value='$i'>$i</OPTION>";
  $return.="</SELECT>$string[export_to] <SELECT name=data_2[0]><OPTION></OPTION>";
  for($i=1;$i<=31;++$i) $return.="<OPTION value='$i'>$i</OPTION>";
  $return.="</SELECT><SELECT name=data_2[1]><OPTION></OPTION>";
  for($i=1;$i<=12;++$i) $return.="<OPTION value='$i'>".$varie['mounts_1'][$i-1]."</OPTION>";
  $return.="</SELECT><SELECT name=data_2[2]><OPTION></OPTION>";
  for($i=$anno_y;$i<=$year_now;++$i) $return.="<OPTION value='$i'>$i</OPTION>";
  $return.=
  "</center></span></td></tr>".
  "<tr><td bgcolor=$style[table_bgcolor]><input name=\"mode\" type=\"hidden\" value=$mode><input name=\"export_choice\" type=\"hidden\" value=1><center><input type=\"submit\" value=\"$pref[start_export]\"></center></td></tr>".
  "</table>".
  "</form>";

  if($numero_pagine>1) $return.=
                       "<br><table border=\"0\" $style[table_header] width=\"90%\" align=\"center\">".
                       "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] nowrap></td></tr>".
                       "<tr><td bgcolor=$style[table_bgcolor] height=\"20\" nowrap>".
                       pag_bar('admin.php?action=details&mode='.$mode.$filter_params,$pagina_corrente,$numero_pagine,$rec_pag).
                       "</td></tr>".
                       "<tr><td height=\"1\"bgcolor=$style[table_title_bgcolor] nowrap></td></tr>".
                       "</table>";
  }
else $return.=info_box($string['information'],$error['details']);
// SELEZIONE MODALITA'
$return.="<br><center><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
if($mode!=0) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=details&mode=0{$filter_params}\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_0]</span></a></td></tr>";
if($mode!=1) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=details&mode=1{$filter_params}\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_1]</span></a></td></tr>";
if($mode!=2) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=details&mode=2{$filter_params}\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_2]</span></a></td></tr>";
$return.=
"</table></center>";
if($modulo[11]){
$return.="<form name=\"filter_agents\" action=\"admin.php\" method=\"GET\">".
"<input type=\"hidden\" name=\"action\" value=\"details\">".
"<input type=\"hidden\" name=\"mode\" value=\"$mode\">".
"<br><table border=\"0\" $style[table_header] width=\"416\" align=\"center\">".
"<tr><td bgcolor=$style[table_title_bgcolor]><span class=\"tabletitle\">$string[rf_title]</span></td></tr>".
"<tr><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\"><center>".
"<input type=\"checkbox\" name=\"show_bw\" value=\"1\"".($filter_number{0}==='1' ? ' checked' : '').">&nbsp;<span class='testo'>$string[rf_browsers]&nbsp;&nbsp;&nbsp;&nbsp;".
"<input type=\"checkbox\" name=\"show_gr\" value=\"1\"".($filter_number{1}==='1' ? ' checked' : '').">&nbsp;<span class='testo'>$string[rf_grabbers]&nbsp;&nbsp;&nbsp;&nbsp;".
"<input type=\"checkbox\" name=\"show_sp\" value=\"1\"".($filter_number{2}==='1' ? ' checked' : '').">&nbsp;<span class='testo'>$string[rf_spiders]</center></span></td></tr>".
"<tr><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\"><center><input type=\"submit\" value=\"$string[rf_submit]\"></center></span></td></tr>".
"</table>".
"</form>";
}
return($return);
}


function check_details($time,$page,$title,$mode)
  {
  global $option,$db,$style,$string;
  $return="\n\t\t\t<tr bgcolor=\"#B3C0D7\" onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this,'$style[table_bgcolor]','$style[table_bgcolor]')\"><td bgcolor=$style[table_bgcolor] width=\"10%\"><span class=\"tabletextA\">&nbsp;".formattime($time)."&nbsp;</span></td><td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">";
  $tmp=substr($page,0,3);
  if($tmp==='dwn')
    {
    list($dummy,$id)=explode('|',$page);
    $res_name=sql_query("SELECT nome FROM $option[prefix]_downloads WHERE id='$id' LIMIT 1");
    if(@mysql_num_rows($res_name)>0)
      {
      list($name)=@mysql_fetch_row($res_name);
      $name=@stripslashes($name);
      $return.=str_replace('%NAME%',$name,$string['details_down']);
      }
    else $return.=str_replace('%ID%',$id,$string['details_down']);
    }
  else if($tmp==='clk')
    {
    list($dummy,$id)=explode('|',$page);
    $res_name=sql_query("SELECT nome FROM $option[prefix]_clicks WHERE id='$id' LIMIT 1");
    if(@mysql_num_rows($res_name)>0)
      {
      list($name)=@mysql_fetch_row($res_name);
      $return.=str_replace('%NAME%',$name,$string['details_click']);
      }
    else $return.=str_replace('%ID%',$id,$string['details_click']);
    }
  else
    {
    if($option['page_title'] && $option['refresh_page_title']==0 && $title=='?')
      {
      $result4=sql_query("SELECT titlePage FROM $option[prefix]_pages WHERE data='$page'");
      if(@mysql_affected_rows()>0) list($title)=@mysql_fetch_row($result4);
      }
    $return.=formaturl($page,$title,70,35,-30,$title,$mode); // VISUALIZZO IL TITOLO DELLE PAGINE SE PRESENTE E SE ATTIVO
    }
  $return.="</span></td></tr>";
  return($return);
}
?>