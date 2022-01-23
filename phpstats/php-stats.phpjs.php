<?php   ////////////////////////////////////////////////
        //   ___ _  _ ___     ___ _____ _ _____ ___   //
        //  | _ \ || | _ \___/ __|_   _/_\_   _/ __|  //
        //  |  _/ __ |  _/___\__ \ | |/ _ \| | \__ \  //
        //  |_| |_||_|_|0.1.9|___/ |_/_/ \_\_| |___/  //
        //                                            //
   /////////////////////////////////////////////////////////
   //       Author: Roberto Valsania (Webmaster76)        //
   //   Staff: Matrix, Viewsource, PaoDJ, Fabry, theCAS   //
   //          Homepage: www.php-stats.com,               //
   //                    www.php-stats.it,                //
   //                    www.php-stat.com                 //
   /////////////////////////////////////////////////////////

define('IN_PHPSTATS', true);
$loaded=$colres=$titlepage=FALSE;

@require("config.php");
if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
if(isset($_GET['ip'])) $ip=$_GET['ip']; else exit();
if(isset($_GET['visitor_id'])) $visitor_id=$_GET['visitor_id']; else exit();
if(isset($_GET['referer'])) if(($_GET['referer']==1) || $option['full_recn']) $referer=TRUE;
if(isset($_GET['loaded'])) if($_GET['loaded']==1) $loaded=TRUE;
if(isset($_GET['colres'])) if($_GET['colres']==1) $colres=TRUE;
if(isset($_GET['titlepage'])) { if(($_GET['titlepage']==1) && $option['page_title']) $titlepage=TRUE; }
if(isset($_GET['date'])) $date=$_GET['date']; else exit();

// IN QUESTA BETA NON FACCIO CONTROLLI SULLA COERENZA DELL'IP E VISITOR_ID
$appendjs="ip=$ip&amp;visitor_id=$visitor_id";
$errorjs='inutil';
$s=urlencode("§§");
$return='var rand=Math.round(100000*Math.random());
var inutil=\'\';
';
if($option['full_recn'] || ($referer===TRUE))
  $return.="if(document.referrer){
  var f=document.referrer;
}else{
  var f=top.document.referrer;
}
f=escape(f);
f=f.replace(/&/g,'$s');
if((f=='null') || (f=='unknown') || (f=='undefined')) f='';";
if($colres===TRUE)
  {
$return.='
var w=screen.width;
var h=screen.height;
var browser=navigator.appName;
if(browser!="Netscape") c=screen.colorDepth; else c=screen.pixelDepth;';
$appendjs.='&amp;w="+w+"&amp;h="+h+"&amp;c="+c+"';
$errorjs.=',w,h,c';
  }
if($option['full_recn'] || ($referer===TRUE)) {
  $appendjs.='&amp;f="+f+"';
  $errorjs.=',f'; }
if ($loaded===TRUE)
  {
  $return.="\nvar NS_url=\"\";
NS_url=document.URL;
NS_url=escape(NS_url);
NS_url=NS_url.replace(/&/g,'$s');";
  $appendjs.='&amp;NS_url="+NS_url+"';
  $errorjs.=',NS_url';
  }
if ($titlepage===TRUE)
  {
  $return.="\nvar t=escape(document.title);";
  $appendjs.='&amp;t="+t+"';
  $errorjs.=',t';
  }
if($option['callviaimg'])
   $return.="\nvar sc1=\"<img src='$option[script_url]/php-stats.recjs.php?$appendjs&amp;date=$date' border='0' alt='' width='1' height='1'>\";";
  else
  $return.="\nvar sc1=\"<scr\"+\"ipt type='text/javascript' src='$option[script_url]/php-stats.recjs.php?$appendjs&amp;date=$date'></scr\"+\"ipt>\";";
$return.="\ndocument.write(sc1);";
echo $return;
?>