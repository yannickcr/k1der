<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

$date=time()-$option['timezone']*3600;
list($mese,$anno)=explode('-',date('m-Y',$date));

if(isset($_POST['sel_mese'])) $sel_mese=addslashes($_POST['sel_mese']); else $sel_mese=$mese;
if(isset($_POST['sel_anno'])) $sel_anno=addslashes($_POST['sel_anno']); else $sel_anno=$anno;
    if(isset($_GET['start'])) $start=addslashes($_GET['start']); else $start=0;
     if(isset($_GET['sort'])) $sort=addslashes($_GET['sort']); else $sort=1; // Default sort
    if(isset($_GET['order'])) $order=addslashes($_GET['order']); else $order=0; // Default order
        if(isset($_GET['q'])) $q=addslashes($_GET['q']); else { if(isset($_POST['q'])) $q=addslashes($_POST['q']); else $q=''; }
     if(isset($_GET['mese'])) list($sel_anno,$sel_mese)=explode('-',addslashes($_GET['mese']));
     if(isset($_GET['mode'])) $mode=addslashes($_GET['mode']); else if($modulo[4]<2) $mode=1; else $mode=0;
    if(isset($_GET['group'])) $group=addslashes($_GET['group']); else $group=0;
  if(isset($_GET['delpage'])) $delpage=$_GET['delpage']; else $delpage='';

function referer() {
global $db,$string,$error,$varie,$style,$option,$start,$q,$pref,$sort,$order,$group,$mode,$mese,$anno,$sel_anno,$sel_mese,$phpstats_title,$pass_cookie,$delpage;
$return='';

// Cancello la pagina se richiesto
do{
if($delpage=='' || $pass_cookie!=md5($option['admin_pass'])) break;
$$delpage=rawurldecode($delpage);
$delpage2=rawurldecode($delpage);
$delpage3=urldecode($delpage);
$delValuepage=Array($delpage,addslashes($delpage),stripslashes($delpage),$delpage2,addslashes($delpage2),stripslashes($delpage2),$delpage3,addslashes($delpage3),stripslashes($delpage3));
for($i=0,$tot=count($delValuepage);$i<$tot;++$i){
   sql_query("DELETE FROM $option[prefix]_referer WHERE data='$delValuepage[$i]' LIMIT 1");
   if(@mysql_affected_rows()>0) break(2);
   }
}while(FALSE);

// Costruisco la parte di query per i mesi e/o la ricerca
if(strlen("$sel_mese")<2) $sel_mese='0'.$sel_mese;
if($q=='') $q_append=($mode==0 ? " WHERE mese='$sel_anno-$sel_mese'" : '');
else $q_append=($mode==0 ? " WHERE mese='$sel_anno-$sel_mese' AND data LIKE '%$q%'" : " WHERE data LIKE '%$q%'");

if($group==0) {
// MODALITA' NORMALE, NON RAGGRUPPATA PER DOMINIO

// Titolo pagina (riportata anche nell'admin)
if($mode==0) $phpstats_title=str_replace(Array('%MESE%','%ANNO%'),Array(formatmount($sel_mese),$sel_anno),$string['refers_title_2']);
else $phpstats_title=$string['refers_title'];

$return.=
"\n<script>\n".
"function popup(url) {\n".
"test=window.open(url,'nome','SCROLLBARS=1,STATUS=NO,TOOLBAR=NO,RESIZABLE=YES,LOCATION=NO,MENU=NO,WIDTH=360,HEIGHT=480,LEFT=0,TOP=0');\n".
"}\n".
"</script>\n";

// ORDINAMENTO MENU e QUERY
$tables=Array('pagina'=>'data','visits'=>'sumvisits','date'=>'date');
$modes=Array('0'=>'DESC','1'=>'ASC');
$q_sort=(isset($tables[$sort]) ? $tables[$sort] : 'sumvisits');
$q_order=(isset($modes[$order]) ? $modes[$order] : 'DESC');
$q_append2="$q_sort $q_order";
$rec_pag=100; // risultati visualizzayi per pagina
$query_tot=sql_query("SELECT count(DISTINCT data) FROM $option[prefix]_referer $q_append");
list($num_totale)=@mysql_fetch_row($query_tot);
$numero_pagine=ceil($num_totale/$rec_pag);
$pagina_corrente=ceil(($start/$rec_pag)+1);
$result=sql_query("SELECT data,SUM(visits) as sumvisits,date FROM $option[prefix]_referer $q_append GROUP BY data ORDER BY $q_append2 LIMIT $start,$rec_pag");
$righe=@mysql_num_rows($result);
if($righe>0)
  {
  $return.="<span class=\"pagetitle\">$phpstats_title</span><br>";
  if($q!='')
    {
    $searchresult=sql_query("SELECT SUM(visits) as sumvisits FROM $option[prefix]_referer $q_append LIMIT $start,$rec_pag");
    list($searchvisits)=@mysql_fetch_row($searchresult);
    $return.='<br>'.str_replace(Array('%query%','%trovati%','%hits%'),Array($q,$righe,$searchvisits),$string['pages_results']).'<br>';
    }
  if($numero_pagine>1)
    {
    $tmp=str_replace(Array('%current%','%total%'),Array($pagina_corrente,$numero_pagine),$varie['pag_x_y']);
    $return.="<div align=\"right\"><span class=\"testo\">$tmp&nbsp;&nbsp;</span></div>";
    }
  $return.=
  "<br><table $style[table_header] width=\"95%\" align=\"center\"><tr>".
  draw_table_title($string['refers_url'],'pagina',"admin.php?action=referer&q=$q&group=$group&mode=$mode&mese=$sel_anno-$sel_mese",$tables,$q_sort,$q_order).
  draw_table_title($string['refers_date'],'date',"admin.php?action=referer&q=$q&group=$group&mode=$mode&mese=$sel_anno-$sel_mese",$tables,$q_sort,$q_order).
  draw_table_title($string['refers_hits'],'visits',"admin.php?action=referer&q=$q&group=$group&mode=$mode&mese=$sel_anno-$sel_mese",$tables,$q_sort,$q_order).
  draw_table_title($string['refers_tracking']).
  draw_table_title($string['refers_delete']).
  '</tr>';

  $numer_of=(1+(($pagina_corrente-1)*$rec_pag));
  while($row=@mysql_fetch_row($result))
    {
    list($referer_data,$referer_sumvisits,$referer_date)=$row;
    $referer_data_urlenc=urlencode($referer_data);
    $referer_data=htmlspecialchars($referer_data);

    $return.=
    "<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">".
    "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".formaturl($referer_data, '', 60, 25, -30).'</span></td>'.
    "<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\">".formatdate($referer_date,3).' - '.formattime($referer_date).'</span></td>'.
    "<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\"><b>$referer_sumvisits</b></span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\"><a href=\"javascript:popup('tracking.php?what=referer&page=$referer_data_urlenc');\"><img src=\"templates/$option[template]/images/icon_tracking.gif\" border=0 title=\"$string[refers_alt_1]\"></a></td>".
    "<td bgcolor=$style[table_bgcolor] width=\"11\"><a href=\"admin.php?action=referer&mese=$sel_anno-$sel_mese&q=$q&sort=$sort&order=$order&group=$group&mode=$mode&start=$start&delpage=$referer_data_urlenc\" onclick=\"return confirmLink(this,'$string[refers_delete_confirm]')\"><img src=\"templates/$option[template]/images/icon_delete.gif\" title=\"$string[refers_delete_alt]\" border=0></a></td></tr>";
    '</tr>';
    ++$numer_of;
    }
  $return.= "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"5\" nowrap></td></tr>";
  if($numero_pagine>1)
    {
    $return.=
    "<tr><td bgcolor=$style[table_bgcolor] colspan=\"5\" height=\"20\" nowrap>".
    pag_bar("admin.php?action=referer&mese=$sel_anno-$sel_mese&q=$q&sort=$sort&order=$order&group=$group&mode=$mode",$pagina_corrente,$numero_pagine,$rec_pag).
    "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"5\" nowrap></td></tr>";
    }
  $return.='</table>';

  // RICERCA
  if($righe>0) $return.=write_monthly();
  }
else
  {
  if($q!='')
    {
    $body="$string[no_pages]<br><br><br><a href=\"javascript:history.back();\"><-- $pref[back]</a>";
    $return.=info_box($string['information'],$body);
    }
  else
    {
    if($mode==1) $return.=info_box($string['information'],$error['referer']);
    else
      {
      $tmp=str_replace(Array('%MESE%','%ANNO%'),Array(formatmount($sel_mese),$sel_anno),$error['referer_2']);
      $return.=info_box($string['information'],$tmp);
      }
    }
  $return.=write_monthly_of();
  }
} // FINE MODALITA' NORMALE
else
{
// INZIO MODALITA' RAGGRUPPATA PER DOMINIO

// Titolo pagina (riportata anche nell'admin)
if($mode==0) $phpstats_title=str_replace(Array('%MESE%','%ANNO%'),Array(formatmount($sel_mese),$sel_anno),$string['refers_group_title_2']);
else $phpstats_title=$string['refers_group_title'];

$return.=
"\n<script>\n".
"function popup(url) {\n".
"test=window.open(url,'nome','SCROLLBARS=1,STATUS=NO,TOOLBAR=NO,RESIZABLE=YES,LOCATION=NO,MENU=NO,WIDTH=570,HEIGHT=340,LEFT=0,TOP=0');\n".
"}\n".
"</script>\n";
//$return.="<span class=\"tabletextA\">$string[refers_title]</span><br>";

// ORDINAMENTO MENU e QUERY
$tables=Array('pagina'=>'dom','visits'=>'sumvisits','date'=>'last');
$modes=Array('0'=>'DESC','1'=>'ASC');
$q_sort=(isset($tables[$sort]) ? $tables[$sort] : 'sumvisits');
$q_order=(isset($modes[$order]) ? $modes[$order] : 'DESC');
$q_append2="$q_sort $q_order";
$rec_pag=100; // risultati visualizzayi per pagina

$query_tot=sql_query("SELECT count(SUBSTRING_INDEX(data,'/',3)) as dom FROM $option[prefix]_referer $q_append");
$num_totale=@mysql_num_rows($query_tot);
$numero_pagine=ceil($num_totale/$rec_pag);
$pagina_corrente=ceil(($start/$rec_pag)+1);
$result=sql_query("SELECT SUBSTRING_INDEX(data,'/',3) as dom, SUM(visits) as sumvisits, MAX(date) as last FROM $option[prefix]_referer $q_append GROUP BY dom ORDER BY $q_append2 LIMIT $start,$rec_pag");
$righe=@mysql_num_rows($result);
if($righe>0)
  {
  $return.="<span class=\"pagetitle\">$phpstats_title</span><br>";
  if($q!='')
    {
    $searchresult=sql_query("SELECT SUM(visits) as sumvisits FROM $option[prefix]_referer $q_append LIMIT $start,$rec_pag");
    list($searchvisits)=@mysql_fetch_row($searchresult);
    $return.='<br>'.str_replace(Array('%query%','%trovati%','%hits%'),Array($q,$righe,$searchvisits),$string['pages_results']).'<br>';
    }
  if($numero_pagine>1)
    {
    $tmp=str_replace(Array('%current%','%total%'),Array($pagina_corrente,$numero_pagine),$varie['pag_x_y']);
    $return.="<div align=\"right\"><span class=\"testo\">$tmp&nbsp;&nbsp;</span></div>";
    }
  $return.=
  "<br><table $style[table_header] width=\"95%\" align=\"center\"><tr>".
  draw_table_title($string['refers_url_1'],'pagina',"admin.php?action=referer&group=1&q=$q&group=$group&mode=$mode&mese=$sel_anno-$sel_mese",$tables,$q_sort,$q_order).
  draw_table_title($string['refers_date_1'],'date',"admin.php?action=referer&group=1&q=$q&group=$group&mode=$mode&mese=$sel_anno-$sel_mese",$tables,$q_sort,$q_order).
  draw_table_title($string['refers_hits_1'],'visits',"admin.php?action=referer&group=1&q=$q&group=$group&mode=$mode&mese=$sel_anno-$sel_mese",$tables,$q_sort,$q_order).
  draw_table_title($string['refers_tracking']).
  '</tr>';
  $numer_of=(1+(($pagina_corrente-1)*$rec_pag));
  while($row=@mysql_fetch_row($result))
    {
    list($referer_dom,$referer_sumvisits,$referer_last)=$row;
    $referer_dom=htmlspecialchars($referer_dom);
    $referer_dom_enc=str_replace('&','§§',$referer_dom);
    $return.=
    "<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">".
    "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".formaturl($referer_dom, '', 60, 25, -30)."</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\">".formatdate($referer_last,3)." - ".formattime($referer_last)."</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\"><b>$referer_sumvisits</b></span></td>";
    if($mode==0) $return.="<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\"><a href=\"javascript:popup('tracking.php?what=referer_domain&domain=$referer_dom_enc&mese=$sel_anno-$sel_mese')\"><img src=\"templates/$option[template]/images/icon_tracking.gif\" border=0 title=\"$string[refers_alt_2]\"></a></td>";
    else $return.="<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap><span class=\"tabletextA\"><a href=\"javascript:popup('tracking.php?what=referer_domain&domain=$referer_dom_enc')\"><img src=\"templates/$option[template]/images/icon_tracking.gif\" border=0 title=\"$string[refers_alt_2]\"></a></td>";
    $return.="</tr>";
    ++$numer_of;
    }
  $return.= "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"5\" nowrap></td></tr>";
  if($numero_pagine>1)
    {
    $return.=
    "<tr><td bgcolor=$style[table_bgcolor] colspan=\"5\" height=\"20\" nowrap>".
    pag_bar("admin.php?action=referer&mese=$sel_anno-$sel_mese&q=$q&sort=$sort&order=$order&group=1&mode=$mode",$pagina_corrente,$numero_pagine,$rec_pag).
    "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"5\" nowrap></td></tr>";
    }
  $return.="</table>";

  // RICERCA
  if($righe>0) $return.=write_monthly();
  }
else
  {
  if($q!='')
    {
    $body="$string[no_pages]<br><br><br><a href=\"javascript:history.back();\"><-- $pref[back]</a>";
    $return.=info_box($string['information'],$body);
    }
  else $return.=info_box($string['information'],$error['referer']);
  $return.=write_monthly_of();
  }
}
return($return);
}

function write_monthly() {
global $db,$string,$error,$varie,$style,$option,$start,$q,$pref,$sort,$order,$group,$mode,$mese,$anno,$sel_anno,$sel_mese;
global $modulo;
$return='';
if($group==0) { $new_group=1; $string_group=$string['refers_mode_1']; }
else { $new_group=0; $string_group=$string['refers_mode_0']; }
$return.=
"<br><br><center><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">".
'<tr><td colspan="2"><span class="testo">'.
"<form action='./admin.php?action=referer&group=$group&mode=$mode&mese=$sel_anno-$sel_mese' method='POST' name='form1'>".
"<span class=\"testo\">$string[search]:".
"<input name=\"q\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"$q\">".
"<input type=\"submit\" value=\"$string[go]\">".
'</span></form>';
'</td></tr>';
if($modulo[4]==2)
  {
  if($mode==0)
    {
    // SELEZIONE MESE DA VISUALIZZARE
    $return.=
    "<tr><td colspan=\"2\"><span class=\"testo\">".
    "<form action='./admin.php?action=referer' method='POST' name=form1><span class=\"tabletextA\">$string[calendar_view]</span>".
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
    '</FORM>'.
    '</td></tr>'.
    "<tr><td><span class=\"testo\"><a href=\"admin.php?action=referer&group=$group&mode=1\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string[os_vis_glob]</a></span></td></tr>".
    "<tr><td><span class=\"testo\"><a href=\"admin.php?action=referer&q=$q&sort=$sort&order=$order&group=$new_group&mode=$mode&mese=$sel_anno-$sel_mese\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string_group</a></span></td></tr>";
    }
  else
    {
    $return.=
    "<tr><td><span class=\"testo\"><a href=\"admin.php?action=referer&group=$group&mode=0\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string[os_vis_mens]</a></span></td></tr>".
    "<tr><td><span class=\"testo\"><a href=\"admin.php?action=referer&q=$q&sort=$sort&order=$order&group=$new_group&mode=$mode\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string_group</a></span></td></tr>";
    }
  }
$return.='</table></center>';
return($return);
}

function write_monthly_of() {
global $db,$string,$error,$varie,$style,$option,$start,$q,$pref,$sort,$order,$group,$mode,$mese,$anno,$sel_anno,$sel_mese;
global $modulo;
$return='';
if($modulo[4]==2)
  {
  $return.="<br><br><center><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  if($mode==0)
    {
    // SELEZIONE MESE DA VISUALIZZARE
    $return.=
    "<tr><td colspan=\"2\"><span class=\"testo\">".
    "<form action='./admin.php?action=referer' method='POST' name=form1><span class=\"tabletextA\">$string[calendar_view]</span>".
    "<SELECT name=sel_mese>";
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
    '</FORM>'.
    '</td></tr>'.
    "<tr><td><span class=\"testo\"><a href=\"admin.php?action=referer&mode=1\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string[os_vis_glob]</a></span></td></tr>";
    }
  else $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=referer&mode=0\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string[os_vis_mens]</a></span></td></tr>";
  $return.='</table></center>';
  }
return($return);
}
?>