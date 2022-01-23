<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');
if(isset($_GET['mode'])) $mode=addslashes($_GET['mode'])-0; else $mode=0;

function percorsi(){
global $db,$option,$string,$error,$varie,$style,$phpstats_title,$mode;

// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['percorsi_title'];
$limit=10;
$what=Array('lev_1','lev_2','lev_3','lev_4','lev_5','lev_6','outs');
$found=0;
$page='';

foreach($what as $key)
  {
  $result=sql_query("SELECT SUM($key) from $option[prefix]_pages");
  list($total)=mysql_fetch_row($result);
  $result=sql_query("SELECT data,$key,titlePage from $option[prefix]_pages WHERE $key>0 ORDER BY $key DESC LIMIT 0,$limit");
  if($total>0)
    {
    $found=1;
    if($key=='outs') $page.="<center><span class=\"tabletextA\">$string[percorsi_altre]</span></center>";
    $page.=
    "\n<TABLE $style[table_header] width=\"90%\">".
    "\n\t<tr>".
    "\n\t".draw_table_title($string['percorsi_'.$key]).
    "\n\t".draw_table_title($string['percorsi_hits']).
    "\n\t".draw_table_title($string['percorsi_prob']).
    "\n\t</tr>";
    while($row=mysql_fetch_row($result))
      {
      list($pages_data,$pages_key,$pages_title)=$row;
      $page.=
      "<tr>".
      "<td align=left bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".formaturl($pages_data,$pages_title,55,22,-25,$pages_title,$mode)."</span></td>".
      "<td align=right bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$pages_key</span></td>".
      "<td align=right bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">".round($pages_key/$total*100,2)."%</span></td>".
      "</tr>";
      }
    $page.="</table><span class=\"tabletextA\"><br></span>";
    }
  }
if($found==0) $return=info_box($string['information'],$string['percorsi_noresult']);
else $return="<span class=\"pagetitle\">$phpstats_title<br><br></span>".$page;

// SELEZIONE MODALITA'
$return.="<br><center><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
if($mode!=0) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=percorsi&mode=0\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_0]</span></a></td></tr>";
if($mode!=1) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=percorsi&mode=1\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_1]</span></a></td></tr>";
if($mode!=2) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=percorsi&mode=2\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[pages_mode_2]</span></a></td></tr>";
$return.="</table></center>";
return $return;
}
?>