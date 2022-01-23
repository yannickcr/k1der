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
if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
$s=urlencode("§§");
require("config.php");
$return="
if(document.referrer){
  var f=document.referrer;
}else{
  var f=top.document.referrer;
}
f=escape(f);
f=f.replace(/&/g,'$s');
if((f=='null') || (f=='unknown') || (f=='undefined')) f='';
var w=screen.width;
var h=screen.height;
var rand=Math.round(100000*Math.random());
var browser=navigator.appName;
var t=escape(document.title);
var NS_url=\"\";
if(browser!=\"Netscape\") c=screen.colorDepth; else c=screen.pixelDepth;
NS_url=document.URL;
NS_url=escape(NS_url);
NS_url=NS_url.replace(/&/g,'$s');";
if($option['callviaimg'])
  $return.="\nvar sc1=\"<img src='$option[script_url]/php-stats.php?w=\"+w+\"&amp;h=\"+h +\"&amp;c=\"+c+\"&amp;f=\"+f+\"&amp;NS_url=\"+NS_url+\"&amp;t=\"+t+\"' style='display:none;' border='0' alt='' width='0' height='0'>\";";
   else
  $return.="\nsc1=\"<scr\"+\"ipt language='javascript' src='$option[script_url]/php-stats.php?w=\"+w+\"&amp;h=\"+h+\"&amp;c=\"+c+\"&amp;f=\"+f+\"&amp;NS_url=\"+NS_url+\"&amp;t=\"+t+\"'></scr\"+\"ipt>\";";
$return.="\ndocument.write(sc1);";
echo $return;
?>