<?php
function upgrade_018_to_019(){
global $db,$option,$append;
$result=sql_query("SELECT * FROM $option[prefix]_cache");
while($row=@mysql_fetch_row($result))
  {
  $nome_bw=chop(getbrowser($row[8]));
  if(strpos(__RANGE_MACRO__,$GLOBALS['cat_macro'])) $nome_os=$GLOBALS['cat_macro']; else $nome_os=chop(getos($row[8]));
  sql_query("UPDATE $option[prefix]_cache SET os='$nome_os',bw='$nome_bw' WHERE user_id='$row[0]'");
  }
  sql_query("ALTER TABLE $option[prefix]_cache DROP COLUMN agent");
  $category='-Spider|Grabber-';
  $result=sql_query("SELECT ip,agent,referer FROM $option[prefix]_details WHERE agent<>''");
  while($row=@mysql_fetch_row($result)){
     $nome_bw=@getbrowser($row[1]);
     if(strpos($category,$GLOBALS['cat_macro'])) $nome_os=$GLOBALS['cat_macro']; else $nome_os=getos($row[1]);
     $details_referer='';
     if($row[2]!=''){
       $reffer=$row[2];
       $engineResult=getengine($reffer);
       if($engineResult!==FALSE){
         list($nome_motore,$domain,$query,$resultPage)=$engineResult;
         $details_referer=implode('|',$engineResult).'|'.addslashes(urldecode($reffer));
       }
     }
     sql_query("UPDATE $option[prefix]_details SET os='$nome_os',bw='$nome_bw',referer='$details_referer' WHERE ip='$row[0]' AND agent='$row[1]' AND referer='$row[2]'");
     }
  sql_query("ALTER TABLE $option[prefix]_details DROP COLUMN agent");
  $result=sql_query("SELECT sum(hits),sum(visits) FROM $option[prefix]_systems WHERE os REGEXP 'Spider|Grabber'");
  list($total_spider_hits,$total_spider_visits)=@mysql_fetch_row($result);
  sql_query("UPDATE $option[prefix]_counters SET no_count_hits='$total_spider_hits',no_count_visits='$total_spider_visits'");
  return true;
}
?>