<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

$date=time()-$option['timezone']*3600;
list($mese,$anno)=explode('-',date('m-Y',$date));

if(isset($_POST['sel_mese'])) $sel_mese=addslashes($_POST['sel_mese']); else $sel_mese=$mese;
if(isset($_POST['sel_anno'])) $sel_anno=addslashes($_POST['sel_anno']); else $sel_anno=$anno;
     if(isset($_GET['mode'])) $mode=addslashes($_GET['mode']); else if($modulo[4]<2) $mode=1; else $mode=0;
     if(isset($_GET['mese'])) list($sel_anno,$sel_mese)=explode('-',addslashes($_GET['mese']));

function searched_words() {
global $db,$option,$style,$string,$varie,$error,$modulo,$mode,$phpstats_title;
global $mese,$anno,$sel_anno,$sel_mese;

$search=Array('"','\'','+',' AND ',' OR ','+','(',')',':','.','[',']','\\','/');
$replace=Array('','',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
	
if(strlen($sel_mese)<2) $sel_mese='0'.$sel_mese;

$return='';

if($mode==0) $clause="WHERE mese='$sel_anno-$sel_mese'";
else $clause='';
// Titolo pagina (riportata anche nell'admin)
if($mode==0) $phpstats_title=str_replace(Array('%MESE%','%ANNO%'),Array(formatmount($sel_mese),$sel_anno),$string['searched_words_title_2']);
else $phpstats_title=$string['searched_words_title'];
//
$result=sql_query("SELECT data,visits FROM $option[prefix]_query $clause");
$num_totale=@mysql_num_rows($result);

$word_list=Array();
$word_list_2=Array();
if($num_totale>0){
	while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
		// ELIMINO CARATTERI NON UTILI
		$row['data']=str_replace($search,$replace,$row['data']);
		$row['data']=eregi_replace('( ){2,}',' ',$row['data']);
		$this_query_words=explode(' ',$row['data']);
		for($i=0,$tot=count($this_query_words);$i<$tot;++$i){
			$word=$this_query_words[$i];
			if(strlen($word)>2){
				if(isset($word_list[$word])) $word_list[$word]+=$row['visits'];
				else $word_list[$word]=$row['visits'];
			}
		}
	}
	arsort($word_list);
	
	if($mode==0){
		// MEMORIZZO LE QUERY DEL MESE PRECEDENTE PER I CONFRONTI
		$mese_prec=date('Y-m',mktime(0,0,0,$sel_mese-1,1,$sel_anno));
		$result=sql_query("SELECT data,visits FROM $option[prefix]_query WHERE mese='$mese_prec'");
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			// ELIMINO CARATTERI NON UTILI
			$row['data']=str_replace($search,$replace,$row['data']);
			$row['data']=eregi_replace('( ){2,}',' ',$row['data']);
			$this_query_words=explode(' ',$row['data']);
			for($i=0,$tot=count($this_query_words);$i<$tot;++$i){
				$word=$this_query_words[$i];
				if(strlen($word)>2){
					if(isset($word_list_2[$word])) $word_list_2[$word]+=$row['visits'];
					else $word_list_2[$word]=$row['visits'];
				}
			}
		}
	}
	// Titolo pagina
	$return.="<span class=\"pagetitle\">$phpstats_title</span><br><br>";
	// Inizio tabella risultati
	$return.="\n<table border=\"0\" $style[table_header] width=\"90%\" align=\"center\">";
	if($mode==0) $return.="<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"4\" nowrap></td></tr>";
	else $return.="<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"3\" nowrap></td></tr>";
	
	$i=1;
	foreach($word_list as $key => $value){
		$return.=
		"\n<tr onmouseover=\"setPointer(this, '$style[table_hitlight]', '$style[table_bgcolor]')\" onmouseout=\"setPointer(this, '$style[table_bgcolor]', '$style[table_bgcolor]')\">".
		"\n\t<td bgcolor=$style[table_bgcolor] width=\"30\" align=\"right\" nowrap><span class=\"tabletextA\">$i</span></td>".
		"\n\t<td bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$key</span></td>".
		"\n\t<td bgcolor=$style[table_bgcolor] width=\"30\" nowrap><span class=\"tabletextA\">$value</span></td>";
		
		// VARIAZIONI RISPETTO AL MESE PRECEDENTE
		
		if($mode==0){
			if(isset($word_list_2[$key])){
				$prec=$word_list_2[$key];
				$variazione=round(($value-$prec)/$value*100,1);
				if($variazione<-15) $level='1';
				elseif($variazione<-5) $level='2';
				elseif($variazione<5) $level='3';
				elseif($variazione<15) $level='4';
				else $level='5';
				if($variazione>0) $variazione='+'.$variazione;
				$variazione.=' %';
				$alt_img=str_replace('%HITS%',$prec,$string['searched_words_last_m']);
				$alt_img.="\n".str_replace('%VARIAZIONE%',$variazione,$string['searched_words_last_v']);
			}
			else{
				$level='new';
				$alt_img='';
			}
			$return.="<td bgcolor=$style[table_bgcolor] nowrap=\"1\" width=\"16\"><span class=\"tabletextA\"><img src=\"templates/$option[template]/images/icon_level_{$level}.gif\" title=\"$alt_img\"></span></td>";
		}
		
		$return.="</tr>";
		++$i;
		if($i>100) break;
	}
	if($mode==0) $return.="<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"4\" nowrap></td></tr>";
	else $return.="<tr><td height=\"1\" bgcolor=$style[table_title_bgcolor] colspan=\"3\" nowrap></td></tr>";
	$return.="</table>";
}
else{
	// NESSUN RISULTATO
	if($mode==1) $return.=info_box($string['information'],$error['searched_words']);
	else{
		$tmp=str_replace(Array('%MESE%','%ANNO%'),Array(formatmount($sel_mese),$sel_anno),$error['searched_words_2']);
		$return.=info_box($string['information'],$tmp);
	}
}
if($modulo[4]==2){
	$new_mode1=($mode==0 ? 1 : 0);
	if($mode==0){
		$return.="<br><center>";
		$return.="<FORM action='./admin.php?action=searched_words&mode=$mode' method='POST' name=form1>";
		// SELEZIONE MESE DA VISUALIZZARE
		$return.="&nbsp;<span class=\"tabletextA\">$string[calendar_view]</span><SELECT name=sel_mese>";
		for($i=1;$i<13;++$i) $return.="<OPTION value='$i'".($sel_mese==$i ? ' SELECTED' : '').">".$varie['mounts'][$i-1]."</OPTION>";
		$return.=
		"</SELECT>".
		"<SELECT name=sel_anno>";
		
		$result=sql_query("SELECT min(data) FROM $option[prefix]_daily");
		$row=@mysql_fetch_row($result);
		$ini_y=substr($row[0],0,4);
		if($ini_y=='') $ini_y=$anno;
		for($i=$ini_y;$i<=$anno;++$i) $return.="<OPTION value='$i'".($sel_anno==$i ? ' SELECTED' : '').">$i</OPTION>";
		$return.=
		"</SELECT>".
		"&nbsp;<input type=\"submit\" value=\"$string[go]\">".
		"</FORM>".
		"<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">".
		"<tr><td><span class=\"testo\"><a href=\"admin.php?action=searched_words&mode=1\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string[searched_words_query_vis_glob]</a></span></td></tr>";
	}
	else{
		$return.=
		"<br><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">".
		"<tr><td><span class=\"testo\"><a href=\"admin.php?action=searched_words&mode=0\"><img src=templates/$option[template]/images/icon_change.gif border=\"0\" align=\"absmiddle\" hspace=\"1\" vspace=\"1\"> $string[searched_words_query_vis_mens]</a></span></td></tr>";
	}
	$return.="</table></center>";
}

// RESTITUISCO OUTPUT
return($return);
}

?>