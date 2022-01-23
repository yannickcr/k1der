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

define('IN_PHPSTATS',true);
define ('__OPTIONS_FILE__','option/php-stats-options.php');
define ('__STATICJS_FILE__','php-stats.js');
define ('__LOCK_FILE__','option/options_lock.php');

if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
if(!isset($_POST)) $_POST=$HTTP_POST_VARS;
if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie='';
if(isset($_POST['pswd'])) $pswd=addslashes($_POST['pswd']); else $pswd='';

@require('config.php');
@require('inc/main_func.inc.php');

if($option['prefix']=='') $option['prefix']='php_stats';

db_connect();
$result=sql_query("SELECT value FROM $option[prefix]_config WHERE name='admin_pass'");
list($admin_pass)=@mysql_fetch_row($result);
if($pass_cookie==md5($admin_pass) || $pswd==$admin_pass) create_options();
else{
    $return=
    '<html><title>:: Php-Stats - Create Option ::</title><body>'.
    '<center><br><br>'.
    '<form action="create-options.php" method="post">'.
    'Php-Stats Password: <input name="pswd" type="password" value=""><br><br>'.
    '<input type="submit" value="Invia - Send">'.
    '</center>'.
    '</body></html>';
    echo $return;
    }

if($option['persistent_conn']!=1) mysql_close();

function create_options(){
global $db,$option,$default_pages;

// ARRAY ORDINATA DEI VALORI CHE NON DEVONO ESSERE SCRITTI
$noWrite=Array('inadm_last_update','instat_report_w','instat_max_online','inadm_upd_available');

// Valori da memorizzare in formato stringa
$stringValue=Array('host','database','user_db','pass_db','script_url','exc_pass','prefix','ext_whois','language','server_url','admin_pass','template','nomesito','user_mail','user_pass_new','user_pass_key','phpstats_ver','exc_fol','exc_sip','exc_dip');

$options_text='<?php
if(!defined(\'IN_PHPSTATS\')) die("Php-Stats internal file.");
ignore_user_abort(true);

$option=Array(
';

// Scrivo le variabili presenti in config.php
while (list ($key, $value) = each ($option))
   {
   switch ($key)
     {
     case 'script_url':
             if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' && substr($value,0,5)==='http:') $value='https:'.substr($value,5);
            if(substr($value,-1)==='/') $value=substr($value,0,-1);
        $options_text.="'$key'=>'$value',\n";
        break;
     default:
        $options_text.=(in_array($key,$stringValue) ? "'$key'=>'".addslashes($value)."',\n" : "'$key'=>$value,\n");
        break;
     }
    }
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_row($result))
  {
  if (!(in_array($row[0],$noWrite)))
    {
    switch ($row[0])
     {
     case 'server_url':
        $tmpServerUrl=explode("\n",$row[1]);
        $options_text.="'$row[0]'=>'$row[1]',\n";
        break;
     case 'unlock_pages':
        $tmpUnlockPages=explode('|',$row[1]);
        break;
     case 'moduli':
        $tmpModuli=explode('|',$row[1]);
        break;
     case 'exc_fol':
        $options_text.="'$row[0]'=>'$row[1]',\n";
        $tmpExc_fol=explode("\n",$row[1]);
        break;
     case 'exc_sip':
        $options_text.="'$row[0]'=>'$row[1]',\n";
        $tmpExc_sip=explode("\n",$row[1]);
        break;
     case 'exc_dip':
        $options_text.="'$row[0]'=>'$row[1]',\n";
        $tmpExc_dip=explode("\n",$row[1]);
        break;
     default:
        $options_text.=(in_array($row[0],$stringValue) ? "'$row[0]'=>'$row[1]',\n" : "'$row[0]'=>$row[1],\n");
        break;
     }
    }
  else array_shift($noWrite); // CON ARRAY_SHIFT AUMENTO LA VELOCITA' DI CREAZIONE DEL FILE
  }
$options_text=substr($options_text, 0, -2)."\n);\n\n";

$options_text.="\$modulo=Array(";
for($i=0,$tot=count($tmpModuli);$i<$tot-1;++$i) $options_text.="$tmpModuli[$i],";
$options_text=substr($options_text, 0, -1).");\n\n";

$page_list=Array('main','details','os_browser','reso','systems','pages','percorsi','time_pages','referer','engines','query','searched_words','hourly','daily','weekly','monthly','calendar','compare','ip','country','bw_lang','downloads','clicks','trend');

$options_text.="\$unlockedPages=Array(\n";
  if(in_array(1,$tmpUnlockPages)){
  for($i=0,$tot=count($tmpUnlockPages);$i<$tot-1;++$i) $options_text.=($tmpUnlockPages[$i]==1 ? "'$page_list[$i]',\n" : '');
  $options_text=substr($options_text, 0, -2)."\n);\n\n";
  }
  else $options_text.="''\n);\n\n";

$tot=count($tmpServerUrl);
if (($tot===1) && ($tmpServerUrl[0]=='')) $options_text.="\$countServerUrl=0;\n\n";
else {
     $options_text.="\$serverUrl=Array(\n";
     for($i=0;$i<$tot;++$i) $options_text.="'".($option['www_trunc'] ? str_replace('://www.','://',$tmpServerUrl[$i]) : $tmpServerUrl[$i])."',\n";
     $options_text=substr($options_text, 0, -2)."\n);\n\$countServerUrl=$tot;\n\n";
     }

$tot=count($tmpExc_fol);
if (($tot===1) && ($tmpExc_fol[0]=='')) $options_text.="\$countExcFol=0;\n\n";
else {
     $options_text.="\$excf=Array(\n";
     for($i=0;$i<$tot;++$i) $options_text.="'$tmpExc_fol[$i]',\n";
     $options_text=substr($options_text, 0, -1)."\n);\n\$countExcFol=$tot;\n\n";
     }

$tot=count($tmpExc_sip);
if (($tot===1) && ($tmpExc_sip[0]=='')) $options_text.="\$countExcSip=0;\n\n";
else {
     $options_text.="\$excsips=Array(\n";
     for($i=0,$tot=count($tmpExc_sip);$i<$tot;++$i) $options_text.="'$tmpExc_sip[$i]',\n";
     $options_text=substr($options_text, 0, -1)."\n);\n\$countExcSip=$tot;\n\n";
     }

$tot=count($tmpExc_dip);
if (($tot===1) && ($tmpExc_dip[0]=='')) $options_text.="\$countExcDip=0;\n\n";
else {
     $options_text.="\$excdips=Array(\n";
     for($i=0,$tot=count($tmpExc_dip);$i<$tot;++$i) $options_text.="'$tmpExc_dip[$i]',\n";
     $options_text=substr($options_text, 0, -1)."\n);\n\$countExcDip=$tot;\n\n";
     }

unset($tmpModuli,$tmpServerUrl,$tmpExc_fol,$tmpExc_sip,$tmpExc_dip);

// SCRIVO L'ARRAY DEFAULT PAGE
$options_text.="\$default_pages=Array(\n";
while (list ($key, $value) = each ($default_pages)) $options_text.="'$value',\n";
$options_text=substr($options_text, 0, -2)."\n);\n?>";

// CREO IL FILE DI LOCK E FACCIO UNO SLEEP DI 1 SEC ALTRIMENTI NON VIENE MAI RILEVATO
@touch(__LOCK_FILE__);
sleep(1);

// CREAZIONE FILE OPTIONS.PHP
$optionsFile=fopen(__OPTIONS_FILE__, 'w+');
fwrite($optionsFile,$options_text);
fclose($optionsFile);

$ok=@unlink(__LOCK_FILE__);

$createFile=TRUE;

if(file_exists(__STATICJS_FILE__))
  {
  $staticJSFile=@fopen(__STATICJS_FILE__,'r');
  if($staticJSFile)
    {
    $tmp=@fread($staticJSFile,5);
    fclose($staticJSFile);
    if(($tmp=='//cvi' && $option['callviaimg']) || ($tmp!='//cvi' && !$option['callviaimg'])) $createFile=FALSE;
    }
  }

if($createFile){
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
$jsstatic_text=
'//cvi
'.$jsstatic_text.'
var sc1="<img src=\''.$option['script_url'].'/php-stats.php?w="+w+"&amp;h="+h+"&amp;c="+c+"&amp;f="+f+"&amp;NS_url="+NS_url+"&amp;t="+t+"\' border=\'0\' alt=\'\' width=\'1\' height=\'1\'>";';
}
else
{
$jsstatic_text.=
'
sc1="<scr"+"ipt type=\'text/javascript\' src=\''.$option['script_url'].'/php-stats.php?w="+w+"&amp;h="+h+"&amp;c="+c+"&amp;f="+f+"&amp;NS_url="+NS_url+"&amp;t="+t+"\'></scr"+"ipt>";';
}

$jsstatic_text.='
document.write(sc1);';

$staticJSFile=@fopen(__STATICJS_FILE__,'w');
if($staticJSFile)
  {
  fwrite($staticJSFile,$jsstatic_text);
  fclose($staticJSFile);
  }
}

if ($ok) echo'<center>.::OK - FILE OPTION::.</center>'; else echo '<center>ERRORE - ERROR</center>';
}
?>