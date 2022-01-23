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
// Vars declaration

               if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
 if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie="";

@require('../config.php');
@require('main_func.inc.php');
if(!isset($option['prefix'])) $option['prefix']='php_stats';
db_connect();

$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_row($result)) $option[$row[0]]=$row[1];

// Controllo che l'utente abbia i permessi necessari altrimenti LOGIN
if($pass_cookie!=md5($option['admin_pass'])) { header("Location: $option[script_url]/admin.php?action=login"); die(); }

// CREATE JS
$jsstatic_text='
if(document.referrer) var f=document.referrer;
else var f=top.document.referrer;
f=escape(f);
f=f.replace(/&/g,"%A7%A7");
if((f=="null") || (f=="unknown") || (f=="undefined")) f="";
var w=screen.width;
var h=screen.height;
var rand=Math.round(100000*Math.random());
var browser=navigator.appName;
var t=escape(document.title);
var NS_url="";
if(browser!="Netscape") c=screen.colorDepth; else c=screen.pixelDepth;
NS_url=document.URL;
NS_url=escape(NS_url);
NS_url=NS_url.replace(/&/g,"%A7%A7");';

if($option['callviaimg'])
{
$jsstatic_text.=
'//cvi
var sc1="<img src=\''.$option['script_url'].'/php-stats.php?w="+w+"&h="+h+"&c="+c+"&f="+f+"&NS_url="+NS_url+"&t="+t+"\' border=\'0\' alt=\'\' width=\'1\' height=\'1\' onE"+"rror=\'reca"+"ll_stat(w,h,c,rand,f,NS_url,t)\' onA"+"bort=\'reca"+"ll_stat(w,h,c,rand,f,NS_url,t)\'>";';
}
else
{
$jsstatic_text.=
'
sc1="<scr"+"ipt language=\'javascript\' src=\''.$option['script_url'].'/php-stats.php?w="+w+"&h="+h+"&c="+c+"&f="+f+"&NS_url="+NS_url+"&t="+t+"\'></scr"+"ipt>";';
}

$jsstatic_text.='
document.write(sc1);';

// DOWNLOAD START
header('Content-disposition: filename="php-stats.js"');
header('Content-type: application/octetstream');
header('Pragma: no-cache');
header('Expires: 0');

echo $jsstatic_text;
?>