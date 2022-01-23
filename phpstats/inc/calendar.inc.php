<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');
//
if(isset($_POST['viewcalendar'])) $viewcalendar=addslashes($_POST['viewcalendar']); else $viewcalendar='last';
if(isset($_POST['calendarmode'])) $calendarmode=addslashes($_POST['calendarmode']); else $calendarmode=1;

function calendar() {
global $db,$option,$string,$error,$varie,$style,$viewcalendar,$modulo,$calendarmode,$phpstats_title;
if (!isset($modulo)) $modulo=explode('|',$option['moduli']);
// Titolo pagina (riportata anche nell'admin)
$phpstats_title=$string['calendar_title'];
// Titolo
$return="<span class=\"pagetitle\">$phpstats_title<br><br></span>";
//
$giorni=Array(null,31,28,31,30,31,30,31,31,30,31,30,31);
list($date_m,$date_Y)=explode('-',date('m-Y'));
$result=sql_query("SELECT min(data) FROM $option[prefix]_daily");
$row=@mysql_fetch_row($result);
$ini_y=substr($row[0],0,4);
if($ini_y=='') $ini_y=$date_Y;
$return.=
"<table border=\"0\" $style[table_header] width=\"95%\" align=\"center\" ><tr>".
"<td bgcolor=$style[table_title_bgcolor] nowrap  class=\"tabletitle\"></td>";
if($viewcalendar=='last'){
	$anno=($date_m<12 ? $date_Y-1 : $date_Y);
	$y=$date_m+1;
}
else{
	$anno=$viewcalendar;
	$y=1;
}
for($i=0;$i<12;++$i){
	if($y>12){
		++$anno;
		$y=1;
	}
	$return.="<td bgcolor=$style[table_title_bgcolor] nowrap class=\"tabletitle\"><center>".formatmount($y,1)."</center></td>";
	++$y;
}
$return.='</tr>';

$totale=Array(null,'-','-','-','-','-','-','-','-','-','-','-','-');
$min_accessi=Array(null,'-','-','-','-','-','-','-','-','-','-','-','-');
$max_accessi=Array(null,'-','-','-','-','-','-','-','-','-','-','-','-');
$giorni_utili=Array(null,'-','-','-','-','-','-','-','-','-','-','-','-');
for($k=1;$k<=31;++$k){
	$k=str_pad($k,2,'0',STR_PAD_LEFT);
	$return.="<tr><td bgcolor=$style[table_title_bgcolor] width=\"10\" nowrap class=\"tabletitle\">$k</td>";
	if($viewcalendar=='last'){
		if($date_m<12){
			$anno=$date_Y-1;
			$y=$date_m+1;
		}
		else{
			$anno=$date_Y;
			$y=1;
		}
	}
	else $y=1;

	$conto=1;
	while($conto<=12){
		if($y>12){
			$y=1;
			++$anno;
		}
		$i=1;
		$max=$giorni[$y]; //uso l'indice numerico per l'accesso più veloce
		$y=str_pad($y,2,'0',STR_PAD_LEFT);
		$return.="<td align='right' class='text' bgcolor=$style[table_bgcolor] >";
		//VERIFICO CHE LA QUERY ABBIA SENSO PRIMA DI EFFETTUARLA ;)
		if(($anno % 4)==0) $giorni[2]=29; // Anno bisestile????
		if($k<=$max){
			$result=sql_query("SELECT * FROM $option[prefix]_daily WHERE data='$anno-$y-$k'");
			$i=1;
			//echo"$k - $y<br>";
			$return.=(date('w',mktime(0,0,0,$y,$k,$anno))==0 ? '<span class="tabletextB">' : '<span class="tabletextA">');
			if(@mysql_num_rows($result)>0){
				while($row=@mysql_fetch_row($result)){
					list($lista_data,$lista_accessi,$lista_visite,$lista_accessi_no_count,$lista_visite_no_count)=$row;
                    switch($calendarmode){
                      case 0: $what=$lista_accessi; break;
                      case 1: $what=$lista_visite; break;
                      case 2: $what=($lista_accessi-$lista_accessi_no_count); break;
                      case 3: $what=($lista_visite-$lista_visite_no_count); break;
                      case 4: $what=$lista_accessi_no_count; break;
                      case 5: $what=$lista_visite_no_count; break;
                      }

					if($min_accessi[$conto]==='-' || $min_accessi[$conto]>$what) $min_accessi[$conto]=$what;

					if($max_accessi[$conto]<=$what) $max_accessi[$conto]=$what;

					$giorni_utili[$conto]+=1;
				}
                switch($calendarmode){
                      case 0:
                            $return.=$lista_accessi;
                            $totale[$conto]+=$lista_accessi;
                            break;
                      case 1:
                            $return.=$lista_visite;
                            $totale[$conto]+=$lista_visite;
                            break;
                      case 2:
                            $return.=($lista_accessi-$lista_accessi_no_count);
                            $totale[$conto]+=($lista_accessi-$lista_accessi_no_count);
                            break;
                      case 3:
                            $return.=($lista_visite-$lista_visite_no_count);
                            $totale[$conto]+=($lista_visite-$lista_visite_no_count);
                            break;
                      case 4:
                            $return.=$lista_accessi_no_count;
                            $totale[$conto]+=$lista_accessi_no_count;
                            break;
                      case 5:
                            $return.=$lista_visite_no_count;
                            $totale[$conto]+=$lista_visite_no_count;
                            break;
                      }
			}
			else $return.='-';
			$return.='</span>';
		}
		else $return.='<center></center>'; // fine verifica query

		$return.='</td>';
		++$y;
		++$conto;
	}
}
$return.=
'</tr>'.
"<tr><td bgcolor=$style[table_title_bgcolor] colspan=\"13\" height=\"1\" nowrap></td></tr>". // Separatore
"<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">". // TOTALI
"<td bgcolor=$style[table_bgcolor] width=\"10\" nowrap><span class=\"tabletextA\">".$string['calendar_day_total']."</span></td>";
for($i=1;$i<13;++$i) $return.="<td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\"><b>".$totale[$i]."</b></span></td>";

$return.=
'</tr>'.
"<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">". // MINIMI
"<td bgcolor=$style[table_bgcolor] width=\"10\" nowrap><span class=\"tabletextA\">".$string['calendar_day_worst']."</span></td>";
for($i=1;$i<13;++$i) $return.="<td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\">".$min_accessi[$i]."</span></td>";

$return.=
'</tr>'.
"<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">". // MASSIMI
"<td bgcolor=$style[table_bgcolor] width=\"10\" nowrap><span class=\"tabletextA\">".$string['calendar_day_best']."</span></td>";
for($i=1;$i<13;++$i) $return.="<td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\">".$max_accessi[$i]."</span></td>";

$return.=
'</tr>'.
"<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">". // MEDIA
"<td bgcolor=$style[table_bgcolor]  width=\"10\" nowrap><span class=\"tabletextA\">".$string['calendar_day_average']."</span></td>";
for($i=1;$i<13;++$i) $return.="<td bgcolor=$style[table_bgcolor] align=\"right\"><span class=\"tabletextA\">".($giorni_utili[$i]==='-' ? '-' : round((($totale[$i])/$giorni_utili[$i]),1))."</span></td>";

$return.=
'</tr>'.
"<tr><td bgcolor=$style[table_title_bgcolor] colspan=\"13\" height=\"1\" nowrap></td></tr>". // Separatore
'</table>';

// FORM CON LA SELEZIONE DELLE OPZIONI CALENDARIO
$return.=
"<br><br><center><form action='./admin.php?action=calendar' method='POST' name=form1><span class=\"tabletextA\">$string[calendar_view]</span>".
'<SELECT name=calendarmode>'.
"<OPTION value='0'".($calendarmode==0 ? ' SELECTED' : '').">$string[hits]</OPTION>".
"<OPTION value='1'".($calendarmode==1 ? ' SELECTED' : '').">$string[visite]</OPTION>";
if($modulo[11]){
$return.=
"<OPTION value='2'".($calendarmode==2 ? ' SELECTED' : '').">$string[hits_no_spider]</OPTION>".
"<OPTION value='3'".($calendarmode==3 ? ' SELECTED' : '').">$string[visite_no_spider]</OPTION>".
"<OPTION value='4'".($calendarmode==4 ? ' SELECTED' : '').">$string[hits_spider]</OPTION>".
"<OPTION value='5'".($calendarmode==5 ? ' SELECTED' : '').">$string[visite_spider]</OPTION>";
}
$return.=
'</SELECT>'.
'<SELECT name=viewcalendar>';
for($i=$ini_y;$i<=$date_Y;++$i) $return.="<OPTION value='$i'".($viewcalendar==$i ? ' SELECTED' : '').">$i</OPTION>";
$return.=
"<OPTION value='last'".($viewcalendar=='last' ? ' SELECTED' : '').">$string[calendar_last]</OPTION>".
'</SELECT>'.
"<input type=\"submit\" value=\"$string[go]\">".
'</FORM>'.
'</center>';
return($return);
}
?>