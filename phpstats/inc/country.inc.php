<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');
//
if(isset($_GET['mode'])) $mode=addslashes($_GET['mode']); else $mode='?';

function country() {
global $db,$mode,$option,$string,$style,$error,$phpstats_title;
include("lang/$option[language]/domains_lang.php");
// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['country_title'];
$return='';
if($mode==='hits') { $mode='hits'; $img="templates/$option[template]/images/style_bar_1.gif"; } else {$mode='visits'; $img="templates/$option[template]/images/style_bar_2.gif"; }
$total=0;
$result=sql_query("select SUM($mode) from $option[prefix]_domains");
list($total)=@mysql_fetch_row($result);
if($total>0)
  {
  ////////////////////////
  // CARTINA CONTINENTI //
  ////////////////////////
  $result=sql_query("SELECT SUM($mode),area FROM $option[prefix]_domains GROUP BY area");
  while($row=@mysql_fetch_row($result)) $area[$row[1]]=$row[0];
  // Titolo
  $return.=
'<span class="pagetitle">'.$string['continent_title'].'<br><br></span>
<table border="0" '.$style['table_header'].' width="482" height="259" align="center">
<tr><td height="1" bgcolor="'.$style['table_title_bgcolor'].'" nowrap></td></tr>
<tr>
<td bgcolor="'.$style['table_bgcolor'].'">
<table width="482" height="259" border="0" cellpadding="0" cellspacing="0" background="templates/'.$option['template'].'/images/continent_map.gif">
<tr>
<td width="163" rowspan="5" align="center" valign="middle"><span class="tabletextA"><b>'.$domain_name['area_AM'].' '.$area['AM'].'<br>('.round($area['AM']/$total*100,2).'%)</b></span></td>
<td width="162" align="center" valign="bottom"><span class="tabletextA"><b>'.$domain_name['area_EU'].' '.$area['EU'].'<br>('.round($area['EU']/$total*100,2).'%)</b></span></td>
<td width="116"></td>
<td width="41"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td align="left" valign="top"><span class="tabletextA"><b>'.$domain_name['area_AS'].' '.($area['AS']+$area['GUS']).'<br>('.round(($area['AS']+$area['GUS'])/$total*100,2).'%)</b></span></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align="center"><span class="tabletextA"><b>'.$domain_name['area_AF'].' '.$area['AF'].'<br>('.round($area['AF']/$total*100,2).'%)</b></span></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td colspan="2" align="center"><span class="tabletextA"><b>'.$domain_name['area_OZ'].' '.$area['OZ'].'<br>('.round($area['OZ']/$total*100,2).'%)</b></span></td>
</tr>
<tr>
<td colspan="3">&nbsp;</td>
</tr>
</table>
</tr>
<tr><td height="1" bgcolor="'.$style['table_title_bgcolor'].'" nowrap></td></tr>
</td></table>';
  if($mode==='hits') { $tipo=$string['visite']; $new_mode='visits';} else { $tipo=$string['hits']; $new_mode='hits'; }
  $return.="<br><center><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class=\"testo\"><a href=\"admin.php?action=country&mode=$new_mode\">".str_replace('%tipo%',$tipo,$string['mode'])."</a></span></center>\n";
  /////////////////////
  // DETTAGLI DOMINI //
  /////////////////////
  $return.=
'<br><br>
<span class="pagetitle">'.$string['country_title'].'<br><br></span>
<table border="0" '.$style['table_header'].' width="90%" align="center"><tr>
'.draw_table_title('').'
'.draw_table_title($string['country']).'
'.draw_table_title($mode==='hits' ? $string['country_hits'] : $string['country_visits']).'
'.draw_table_title('').'
</tr>';
  $result=sql_query("select tld,$mode AS value from $option[prefix]_domains WHERE $mode>0 ORDER BY $mode DESC");
  while($row=@mysql_fetch_array($result,MYSQL_ASSOC))
    {
    $return.=
"<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">
<td bgcolor=$style[table_bgcolor] nowrap width=\"14\"><img src=\"images/flags.php?q=$row[tld]\" align=\"absmiddle\"></td>
<td bgcolor=$style[table_bgcolor] align=\"right\" nowrap ><span class=\"tabletextA\">
".$domain_name[$row['tld']].($row['tld']==='unknown' ? '' : " (.$row[tld])")."
</span></td>
<td align=\"right\" bgcolor=$style[table_bgcolor] nowrap><span class=\"tabletextA\"><b>$row[value]</b></span></td>
<td bgcolor=$style[table_bgcolor] nowrap><span class=\"tabletextA\"><img src=\"$img\" width=\"".($row['value']/$total*200)."\" height=\"7\"> (".round($row['value']*100/$total,1)."%)</span></td>
</tr>";
    }
  $return.=
"<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"4\" nowrap></td></tr>
</table>
<br><center><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class=\"testo\"><a href=\"admin.php?action=country&mode=$new_mode\">".str_replace("%tipo%",$tipo,$string['mode']).'</a></span></center>';
  }
  else $return.=info_box($string['information'],$error['country']);
return($return);
}
?>
