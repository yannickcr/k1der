<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

if(isset($_GET['mode'])) $mode=addslashes($_GET['mode'])-0; else $mode=0;

function daily() {
global $db,$option,$string,$error,$varie,$style,$mode,$modulo,$phpstats_title;
if (!isset($modulo)) $modulo=explode('|',$option['moduli']);
// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['daily_title'];
// Titolo
$return="<span class=\"pagetitle\">$phpstats_title<br><br></span>";

$day=0;
$total_visits=0;
$max=0;
list($date_G,$date_i,$date_m,$date_d,$date_Y)=explode('-',date('G-i-m-d-Y'));
$date_G-=0;//conversione in numeri
$date_i-=0;
$date_m-=0;
$date_d-=0;
$date_Y-=0;

switch($mode){
         case 0:
          if($modulo[11])
            {
            $string['hits'].=$string['daily_string_mode_0'];
            $string['visite'].=$string['daily_string_mode_0'];
            }
          break;
         case 1:
          $string['hits'].=$string['daily_string_mode_1'];
          $string['visite'].=$string['daily_string_mode_1'];
          break;
         case 2:
          $string['hits'].=$string['daily_string_mode_2'];
          $string['visite']=$string['daily_string_mode_2'];
          break;
   }

for($i=0;$i<=30;++$i)
  {
  $lista_accessi[$i]=0;
  $lista_visite[$i]=0;
  $giorno=date('Y-m-d',mktime($date_G-$option['timezone'],$date_i,0,$date_m,$date_d-$i,$date_Y));
  $lista_giorni[$i]=$giorno;
  //$sql="select * from $option[prefix]_daily where to_days(now( )) - to_days(data) < 32 order by 'data' desc";
  $result=sql_query("select hits,visits,no_count_hits,no_count_visits from $option[prefix]_daily where data='$giorno'");
  while($row=@mysql_fetch_array($result,MYSQL_ASSOC))
    {
    switch($mode){
         case 0:
          $lista_accessi[$i]=$row['hits'];
          $lista_visite[$i]=$row['visits'];
          if($row['hits']>$max) $max=$row['hits'];
          break;
         case 1:
          $lista_accessi[$i]=$row['hits']-$row['no_count_hits'];
          $lista_visite[$i]=$row['visits']-$row['no_count_visits'];
          if(($row['hits']-$row['no_count_hits'])>$max) $max=($row['hits']-$row['no_count_hits']);
          break;
         case 2:
          $lista_accessi[$i]=$row['no_count_hits'];
          $lista_visite[$i]=$row['no_count_visits'];
          if($row['no_count_hits']>$max) $max=$row['no_count_hits'];
          break;
   }
  }
  }
////////////////////////////////////
// GENERO IL GRAFICO IN VERTICALE //
////////////////////////////////////
$max_v=($max<30 ? 30 : $max);
$tmp=round($max_v/6,0);
$max_v=$tmp*6;
$return.=
"<table bgcolor=$style[table_bgcolor] border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\">".
"<tr><td><table bgcolor=$style[table_bgcolor] border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">".
'<tr><td height="30"><span class="testo">'.($tmp*5).'</span></td></tr>'.
'<tr><td height="30"><span class="testo">'.($tmp*4).'</span></td></tr>'.
'<tr><td height="30"><span class="testo">'.($tmp*3).'</span></td></tr>'.
'<tr><td height="30"><span class="testo">'.($tmp*2).'</span></td></tr>'.
'<tr><td height="30"><span class="testo">'.($tmp*1).'</span></td></tr>'.
'</table></td>';
for($i=29;$i>=0;$i--) $return.="<td height=\"200\" width=\"15\" valign=\"bottom\" align=\"center\" background=\"templates/$option[template]/images/table_grid.gif\"><img src=\"templates/$option[template]/images/style_bar_3.gif\"\" width=\"5\" height=\"".($lista_accessi[$i]/$max_v*187)."\"  title=\"$lista_accessi[$i]\"><img src=\"templates/$option[template]/images/style_bar_4.gif\"\" width=\"5\" height=\"".($lista_visite[$i]/$max_v*187)."\" title=\"$lista_visite[$i]\"></td>";

$return.=
"<td height=\"200\" width=\"1\" valign=\"bottom\" align=\"center\" background=\"templates/$option[template]/images/table_grid.gif\"></td>".
"</td></tr><tr><td></td>";
for($i=29;$i>=0;$i--)
  {
  list($giorno,$weekday)=explode('-',date('d-w',mktime($date_G-$option['timezone'],$date_i,0,$date_m,$date_d-$i,$date_Y)));
  $return.=($weekday==0 ? "<td><span class=\"tabletextB\">$giorno</span></td>" : "<td><span class=\"tabletextA\">$giorno</span></td>");
  }
$return.=
'</tr>'.
"<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"32\" nowrap></td></tr>".
"<tr><td bgcolor=$style[table_bgcolor] colspan=\"32\" nowrap><span class=\"tabletextA\"><center><img src=\"templates/$option[template]/images/style_bar_1.gif\" width=\"7\" height=\"7\"> $string[hits] <img src=\"templates/$option[template]/images/style_bar_2.gif\" width=\"7\" height=\"7\"> $string[visite]</span></center></td></tr>".
"<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"32\" nowrap></td></tr>".
'</table>';

//////////////////////////////////////
// GENERO IL GRAFICO IN ORIZZONTALE //
//////////////////////////////////////
$return.=
"<br><br><table border=\"0\" width=\"90%\" $style[table_header]>".
'<tr>'.
"<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>Data</center></span></td>".
"<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center>Accessi</center></span></td>".
"<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center></center></span></td>".
"<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center></center></span></td>".
"<td bgcolor=$style[table_title_bgcolor] nowrap><span class=\"tabletitle\"><center></center></span></td>".
'</tr>';

for($i=0;$i<=29;++$i)
  {
  if($lista_visite[$i+1]>0)
    {
    //$img="templates/$option[template]/images/icon_level_1.gif";

    $variazione=round(($lista_visite[$i]-$lista_visite[$i+1])/$lista_visite[$i+1]*100,1);
    if($variazione<-15)    $level='1';
    elseif($variazione<-5) $level='2';
    elseif($variazione<5)  $level='3';
    elseif($variazione<15) $level='4';
    else                   $level='5';
    if($variazione>0) $variazione='+'.$variazione;
    $variazione.=' %';
    }
  else
    {
    $variazione='-';
    $level=($lista_visite[$i]>0 ? '5' : 'unkn');
    }
  // Variazione accessi
  //$variazione2=round(($lista_accessi[$i]-$lista_accessi[$i+1])/$lista_accessi[$i+1]*100,1);
  //if($variazione2>0) $variazione2="+".$variazione2;
  //  $variazione2.=" %";
  $data=explode('-',$lista_giorni[$i]);
  $giorno=str_replace(Array('%mount%','%day%','%year%'),Array(formatmount($data[1]),$data[2],$data[0]),$varie['date_format']);
  $mese_tmp[0]=$data[1];
  if(!isset($mese_tmp[1])) $mese_tmp[1]='';
  if($mese_tmp[1]=='') $mese_tmp[1]=$mese_tmp[0];
  if($mese_tmp[0]!=$mese_tmp[1]) $return.="<tr><td  bgcolor=$style[table_bgcolor] height=\"1\" colspan=\"5\"></td></tr>";
  $return.= "<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\"><td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"";
  $return.=(date('w',mktime($date_G-$option['timezone'],$date_i,0,$date_m,$date_d-$i,$date_Y))==0 ? 'tabletextB' : 'tabletextA');
  $max=max($max,1);
  $img="templates/$option[template]/images/icon_level_{$level}.gif";
  //$return.="\">$giorno</span></td><td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\"><b>$lista_accessi[$i]</b></span><br><span class=\"tabletextA\"><b>$lista_visite[$i]</b></span></td><td bgcolor=$style[table_bgcolor] width=\"300\"><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/style_bar_1.gif\" width=\"".($lista_accessi[$i]/$max*250)."\" height=\"7\"></span><br><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/style_bar_2.gif\" width=\"".($lista_visite[$i]/$max * 250)."\" height=\"7\"></span></td><td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\">".$variazione2."<br>".$variazione."</span></td><td bgcolor=$style[table_bgcolor] width=\"16\"><img src=\"$img\"></td></tr>";
  $return.="\">$giorno</span></td><td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\"><b>$lista_accessi[$i]</b></span><br><span class=\"tabletextA\"><b>$lista_visite[$i]</b></span></td><td bgcolor=$style[table_bgcolor] width=\"300\"><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/style_bar_1.gif\" width=\"".($lista_accessi[$i]/$max*250)."\" height=\"7\"></span><br><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/style_bar_2.gif\" width=\"".($lista_visite[$i]/$max * 250)."\" height=\"7\"></span></td><td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\">".$variazione."</span></td><td bgcolor=$style[table_bgcolor] width=\"16\"><img src=\"$img\"></td></tr>";
  $mese_tmp[1]=$mese_tmp[0];
  }
$return.=
"<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"5\" nowrap></td></tr>".
"<tr><td bgcolor=$style[table_bgcolor] colspan=\"5\" nowrap><span class=\"tabletextA\"><center><img src=\"templates/$option[template]/images/style_bar_1.gif\" width=\"7\" height=\"7\"> $string[hits] <img src=\"templates/$option[template]/images/style_bar_2.gif\" width=\"7\" height=\"7\"> $string[visite]</span></center></td></tr>".
"<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"5\" nowrap></td></tr>".
"</table>";
if($modulo[11]){
        // SELEZIONE MODALITA'
        $return.="<br><center><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
        if($mode!=0) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=daily&mode=0\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[daily_mode_0]</span></a></td></tr>";
        if($mode!=1) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=daily&mode=1\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[daily_mode_1]</span></a></td></tr>";
        if($mode!=2) $return.="<tr><td><span class=\"testo\"><a href=\"admin.php?action=daily&mode=2\"><img src=templates/$option[template]/images/icon_changevis.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"><span class='testo'> $string[daily_mode_2]</span></a></td></tr>";
        $return.="</table></center>";
    }
return($return);
}
?>