<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

$date=time()-$option['timezone']*3600;
list($mese,$anno)=explode('-',date('m-Y',$date));

    if(isset($_GET['debug'])) $debug=addslashes($_GET['debug']); else $debug=0;
if(isset($_GET['sel_mese'])) $sel_mese=addslashes($_GET['sel_mese']); else $sel_mese=$mese;
if(isset($_GET['sel_anno'])) $sel_anno=addslashes($_GET['sel_anno']); else $sel_anno=$anno;
     if(isset($_GET['mode'])) $mode=addslashes($_GET['mode'])-0; else if($modulo[1]<2) $mode=1; else $mode=0;
$filter_number=
''.
($_GET['show_bw']=='1' ? '1' : '0').
($_GET['show_gr']=='1' ? '1' : '0').
($_GET['show_sp']=='1' ? '1' : '0');
if($filter_number==='000') $filter_number='111';

function systems() {
global $db,$string,$error,$style,$option,$varie,$modulo,$phpstats_title,$filter_number,$mode,$mese,$anno,$sel_anno,$sel_mese,$filter_number;

switch($filter_number)
  {
  case '100': $filter_agents=" AND os NOT REGEXP 'Spider|Grabber'"; break;
  case '010': $filter_agents=" AND os='Grabber'"; break;
  case '001': $filter_agents=" AND os='Spider'"; break;
  case '110': $filter_agents=" AND os!='Spider'"; break;
  case '101': $filter_agents=" AND os!='Grabber'"; break;
  case '011': $filter_agents=" AND os REGEXP 'Spider|Grabber'"; break;
  default: $filter_agents='';
  }
$filter_params=($filter_number{0}==='1' ? '&show_bw=1' : '').($filter_number{1}==='1' ? '&show_gr=1' : '').($filter_number{2}==='1' ? '&show_sp=1' : '');

$limite=50;
$return='';
if(strlen("$sel_mese")<2) $sel_mese='0'.$sel_mese;
$clause=($mode==0 ? "WHERE mese='$sel_anno-$sel_mese' AND os<>''" : "WHERE os<>''");
$query_bas=sql_query("SELECT sum(hits),sum(visits) FROM $option[prefix]_systems $clause{$filter_agents}");
list($total_hits,$total_accessi)=@mysql_fetch_row($query_bas);
$query_tot=sql_query("SELECT os,hits,visits FROM $option[prefix]_systems $clause{$filter_agents}");
$num_totale=@mysql_num_rows($query_tot);
// Titolo pagina (riportata anche nell'admin)
if($mode==0) $phpstats_title=str_replace(Array('%MESE%','%ANNO%'),Array(formatmount($sel_mese),$sel_anno),$string['systems_title_2']);
else $phpstats_title=$string['systems_title'];
//
if($num_totale>0)
  {
  $count=0;
  $result=sql_query("SELECT os,bw,reso,colo,SUM(hits) AS sumhits,SUM(visits) AS sumvisits FROM $option[prefix]_systems $clause GROUP BY os,bw,reso,colo ORDER BY sumhits DESC");
  $return.=
  "<span class=\"pagetitle\">$phpstats_title</span>".
  "<br><br><table border=\"0\" $style[table_header] width=\"90%\" align=\"center\">".
  '<tr>'.
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center></center></span></td>".
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[systems_os]</center></span></td>".
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[systems_bw]</center></span></td>".
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[systems_reso]</center></span></td>".
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[systems_colo]</center></span></td>".
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>$string[os_hits]</center></span></td>".
  "<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center></center></span></td>".
  '</tr>';
  while(($row=@mysql_fetch_array($result,MYSQL_ASSOC)) && $count<$limite)
    {
    ++$count;
    $image1='images/os.php?q='.($row['os']==='?' ? 'unknown' : str_replace(' ','-',$row['os']));
    $image2='images/browsers.php?q='.($row['bw']==='?' ? 'unknown' : str_replace(' ','-',$row['bw']));
    $return.=
    "<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">".
    "<td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\">$count</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"left\"><span class=\"tabletextA\"><img src=\"$image1\" align=\"absmiddle\"> $row[os]</span></td>";

    $rangeMacro='-Spider,Grabber-';
    if(strpos($rangeMacro,$row['os']) || $row['colo']=='?') $row['colo']=''; else $row['colo'].=' bit';

    $return.=
    "<td bgcolor=$style[table_bgcolor] align=\"left\"><span class=\"tabletextA\"><img src=\"$image2\" align=\"absmiddle\"> $row[bw]</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"left\"><span class=\"tabletextA\">$row[reso]</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"left\"><span class=\"tabletextA\">$row[colo]</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\"><b>$row[sumhits]</b></span><br><span class=\"tabletextA\"><b>$row[sumvisits]</b></span></td>".
    "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/style_bar_1.gif\" width=\"".($row['sumhits']/MAX($total_hits,1)*180)."\" height=\"7\"> (".round($row['sumhits']*100/MAX($total_hits,1),1)."%)</span><br><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/style_bar_2.gif\" width=\"".($row['sumvisits']/MAX($total_accessi,1)*180)."\" height=\"7\"> (".round($row['sumvisits']*100/MAX($total_accessi,1),1)."%)</span></td>".
    '</tr>';
    }
  $return.=
  "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"7\" nowrap></td></tr>".
  "<tr><td bgcolor=$style[table_bgcolor] colspan=\"7\" nowrap><span class=\"tabletextA\"><center><img src=\"templates/$option[template]/images/style_bar_1.gif\" width=\"7\" height=\"7\"> $string[hits] <img src=\"templates/$option[template]/images/style_bar_2.gif\" width=\"7\" height=\"7\"> $string[visite]</span></center></td></tr>".
  "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"7\" nowrap></td></tr>".
  '</table>';
  }
else
  {
  $return.=info_box($string['information'],$error['os_bw']);
  }
if($modulo[1]==2)
  {
  $return.='<br><br><center>';
  if($mode==0)
    {
    // SELEZIONE MESE DA VISUALIZZARE
    $return.=
    "<form action='./admin.php' method='GET' name=form1><span class=\"tabletextA\">$string[calendar_view]</span>".
    "<input type=\"hidden\" name=\"action\" value=\"systems\">".
    "<input type=\"hidden\" name=\"mode\" value=\"$mode\">".
    "<input type=\"hidden\" name=\"show_bw\" value=\"".$filter_number{0}."\">".
    "<input type=\"hidden\" name=\"show_gr\" value=\"".$filter_number{1}."\">".
    "<input type=\"hidden\" name=\"show_sp\" value=\"".$filter_number{2}."\">".
    '<SELECT name=sel_mese>';
    for($i=1;$i<13;++$i) $return.="<OPTION value='$i'".($sel_mese==$i ? ' SELECTED' : '').'>'.$varie['mounts'][$i-1].'</OPTION>';
    $return.=
    '</SELECT>'.
    '<SELECT name=sel_anno>';
    $result=sql_query("SELECT min(data) FROM $option[prefix]_daily");
    $row=@mysql_fetch_row($result);
    $ini_y=substr($row[0],0,4);
    if($ini_y=='') $ini_y=$anno;
    for($i=$ini_y;$i<=$anno;++$i) $return.="<OPTION value='$i'".($sel_anno==$i ? ' SELECTED' : '').">$i</OPTION>";
    $return.=
    '</SELECT>'.
    "<input type=\"submit\" value=\"$string[go]\">".
    "<br><br><a href=\"admin.php?action=systems&mode=1{$filter_params}\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[os_vis_glob]</span></a>".
    '</FORM>';
    }
    else $return.="<a href=\"admin.php?action=systems&mode=0{$filter_params}\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[os_vis_mens]</span></a>";
  $return.='</center>';
  }
  if($modulo[11]){
$return.=
"<form name=\"filter_agents\" action=\"admin.php\" method=\"GET\">".
"<input type=\"hidden\" name=\"action\" value=\"systems\">".
"<input type=\"hidden\" name=\"mode\" value=\"$mode\">".
"<input type=\"hidden\" name=\"sel_anno\" value=\"$sel_anno\">".
"<input type=\"hidden\" name=\"sel_mese\" value=\"$sel_mese\">".
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