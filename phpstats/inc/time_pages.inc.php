<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

if(isset($_GET['start'])) $start=addslashes($_GET['start']); else $start=0;
 if(isset($_GET['sort'])) $sort=addslashes($_GET['sort']); else $sort=2; // Default sort
if(isset($_GET['order'])) $order=addslashes($_GET['order']); else $order=0; // Default order
 if(isset($_GET['mode'])) $mode=addslashes($_GET['mode'])-0; else $mode=0;
function time_pages() {
global $db,$string,$error,$varie,$style,$option,$start,$sort,$order,$phpstats_title,$mode;
// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['time_pages_title'];
$return='';
// ORDINAMENTO MENU e QUERY
$tables=Array('pagina'=>'data','permanenza'=>'count','totale'=>'presence');
$modes=Array('0'=>'DESC','1'=>'ASC');
$q_sort=(isset($tables[$sort]) ? $tables[$sort] : 'presence');
$q_order=(isset($modes[$order]) ? $modes[$order] : 'DESC');
$q_append="$q_sort $q_order";
$rec_pag=50; // risultati visualizzayi per pagina
$query_tot=sql_query("SELECT count(1) FROM $option[prefix]_pages WHERE tocount>0");
list($num_totale)=@mysql_fetch_row($query_tot);
if($num_totale>0)
  {
  $numero_pagine=ceil($num_totale/$rec_pag);
  $pagina_corrente=ceil(($start/$rec_pag)+1);
  // Titolo
  $return.="<span class=\"pagetitle\">$phpstats_title</span><br>";
  //
  if($numero_pagine>1) $return.="<div align=\"right\"><span class=\"testo\">".str_replace(Array('%current%','%total%'),Array($pagina_corrente,$numero_pagine),$varie['pag_x_y'])."&nbsp;&nbsp;</span></div>";
  $return.=
  "<br><table border=\"0\" $style[table_header] width=\"90%\" align=\"center\"><tr>".
  draw_table_title($string['time_pages_url'],'pagina','admin.php?action=time_pages',$tables,$q_sort,$q_order).
  draw_table_title($string['time_pages_perm'],'permanenza','admin.php?action=time_pages',$tables,$q_sort,$q_order).
  draw_table_title($string['time_pages_tot'],'totale','admin.php?action=time_pages',$tables,$q_sort,$q_order).
  "</tr>";

  $result=sql_query("SELECT data,(presence/tocount) as count,presence,titlePage FROM $option[prefix]_pages WHERE tocount>0 ORDER BY $q_append LIMIT $start,$rec_pag");
  while($row=@mysql_fetch_row($result))
    {
    list($pages_url,$pages_count,$pages_presence,$pages_title)=$row;
    $pages_title=stripslashes(trim($pages_title));
    $return.=
    "<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">".
    "<td bgcolor=$style[table_bgcolor] align=\"left\"><span class=\"tabletextA\">".formaturl($pages_url,$pages_title,60,25,-25,$pages_title,$mode)."</span></td>".
    "<td bgcolor=$style[table_bgcolor] align=\"left\"><span class=\"tabletextA\"><b>".formatperm($pages_count)."</b></span></td>".
    "<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".formatperm($pages_presence,2)."</span></td>".
    "</tr>";
    }
  $return.= "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"4\" nowrap></td></tr>";
  if($numero_pagine>1) $return.=
                       "<tr><td bgcolor=$style[table_bgcolor] colspan=\"4\" height=\"20\" nowrap>".
                       pag_bar("admin.php?action=time_pages&sort=$sort&order=$order&mode=$mode",$pagina_corrente,$numero_pagine,$rec_pag).
                       "<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"4\" nowrap></td></tr>";
  $return.="</table>";
  // SELEZIONE MODALITA'
  $return.="<br><center><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  if($mode!=0) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=time_pages&sort=$sort&order=$order&mode=0\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_0]</span></a></td></tr>";
  if($mode!=1) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=time_pages&sort=$sort&order=$order&mode=1\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_1]</span></a></td></tr>";
  if($mode!=2) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=time_pages&sort=$sort&order=$order&mode=2\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_2]</span></a></td></tr>";
  $return.="</table></center>";
  }
else $return.=info_box($string['information'],$error['pages']);
return($return);
}
?>
