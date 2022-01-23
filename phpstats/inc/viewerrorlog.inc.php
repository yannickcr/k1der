<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

if(isset($_POST['mode'])) $mode=addslashes($_POST['mode']); else $mode='';

function viewerrorlog() {
global $option,$mode,$string,$opzioni,$style,$phpstats_title;
$phpstats_title=$string['viewerrlog_title'];
$return='';
$log='';
if($mode=='reset')
  {
  // Reset del file
  $fp=@fopen('php-stats.log','w');
  if($fp) $return=info_box($string['information'],$string['viewerrlog_reset_done']);
  else $return=info_box($string['error'],$string['viewerrlog_reset_error']);
  }
else
  {
  // Visualizzazione del file
  if(!is_readable('php-stats.log')) $return=info_box($string['error'],$string['viewerrlog_nr']);
  elseif(!is_writable('php-stats.log')) $return=info_box($string['error'],$string['viewerrlog_nw']);
  else
    {
    $fp=fopen('php-stats.log','r');
    while(!feof($fp)) $log.=fgets($fp,1024);
    fclose($fp);
    if(trim($log)!='')
      {
      $return.=
      "<span class=\"pagetitle\">$phpstats_title</span><br><br>".
      "\n<center><textarea name=\"text\" wrap=\"OFF\" readonly cols=\"100\" rows=\"15\">\n".$log."\n</textarea><br><br>".
      "<form action=\"admin.php?action=viewerrorlog\" method=\"post\"><input type=\"hidden\" name=\"mode\" value=\"reset\"><input type=\"submit\" value=\"".$string['viewerrlog_reset']."\"></center></form>";
      }
    else $return=info_box($string['information'],$string['viewerrlog_void']);
    }
  }
return($return);
}
?>
