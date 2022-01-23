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
$version='0.1.9.1'; // Versione attuale di Php-Stats
$error='';
$php_stats_error='';
$page='';
$style=''; // In caso di register globals=on
$err_status=0;

// Per compatibilità php > 4.1.X
if(!isset($_POST)) $_POST=$HTTP_POST_VARS;
if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
if(!isset($_SERVER)) $_SERVER=$HTTP_SERVER_VARS;
if(isset($_POST['password'])) $password=$_POST['password']; else $password='';
if(isset($_POST['action'])) $action=$_POST['action']; else $action='';
if(isset($_POST['lang'])) $lang=$_POST['lang']; else $lang='';
if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie='';
if(isset($_POST['license'])) $license=$_POST['license']; else $license=0;
if($action!='') if($license!=1) { header("location: http://www.php-stats.com"); exit(); }

// inclusione delle principali funzioni esterne
if(!@include('config.php')) die('<b>ERRORE</b>: File config.php non accessibile.');
if(!@include('inc/main_func.inc.php')) die('<b>ERRORE</b>: File main_func.inc.php non accessibile.');
if(!@include('inc/admin_func.inc.php')) die('<b>ERRORE</b>: File admin_func.inc.php non accessibile.');

if($option['out_compress']==1) ob_start('ob_gzhandler');
if($option['prefix']=='') $option['prefix']='php_stats';
// Connessione a MySQL e selezione database
db_connect();

// Lettura variabili
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_array($result))
  {
  $option[$row[0]]=$row[1];
  }

// Inclusione file linguaggio (definito dalle preferenze
if(!@include("update_files/lang/$option[language]/update_lang.inc")) die("<b>ERRORE</b>: File update_files/lang/$option[language]/update_lang.inc non accessibile."); // Language file

// Controllo versione di php-stats e determino lo stato di errore
if(isset($option['phpstats_ver']))
  {
  if($err_status==0 && $option['phpstats_ver']<"0.1.3")
    {
    // La versione è antecedente alla 0.1.3
    $error=$error['no_min_ver'];
    $err_status=1;
    }
  if($err_status==0 && $option['phpstats_ver']>="$version")
    {
    // Nessun update necessario, è già la versione corrente
    $error=$error['noupdateneed'];
    $err_status=1;
    }
  }
  else
  {
  // Impossibile determinare la versione corrente
  $error=$error['no_ver_detect'];
  $err_status=1;
  }

if($err_status!=0)
  {
  // Visualizzo l'errore
  $page.=
  "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\" width=\"300\">".
  "<tr><td bgcolor=\"#707888\" nowrap><span class=\"tabletitle\">".$string['box_title']."</span></td>".
  "<tr><td align=\"center\" valign=\"middle\" bgcolor=\"#EEEEEE\" nowrap>".
  "<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"5\" border=\"0\"><tr><td align=\"center\" valign=\"middle\"  nowrap>".
  "<span class=\"testo\">$error</span></td></tr>".
  "</td></tr></table>".
  "</table><br><br><br>";
  }
  else
  {
  if($action=="apply")
    {
        $ok=False;
    // DETERMINO DA QUALE VERSIONE AGGIORNARE
    if($option['phpstats_ver']=='0.1.3') {
                                         $sql='update_files/sql/0.1.3_to_0.1.4.sql';
                                         $ok=@exec_sql_lines($sql);
                                         if($ok==true) $option['phpstats_ver']='0.1.4';
                                         }
    if($option['phpstats_ver']=='0.1.4') {
                                         $sql='update_files/sql/0.1.4_to_0.1.5.sql';
                                         $ok=@exec_sql_lines($sql);
                                         include('update_files/sql/0.1.4_to_0.1.5.php');
                                         upgrade_014_to_015();
                                         if($ok==true) $option['phpstats_ver']='0.1.5';
                                         }
    if($option['phpstats_ver']=='0.1.5') {
                                         $sql='update_files/sql/0.1.5_to_0.1.6.sql';
                                         $ok=@exec_sql_lines($sql);
                                         if($ok==true) $option['phpstats_ver']='0.1.6';
                                         }
    if($option['phpstats_ver']=='0.1.6') {
                                         $sql='update_files/sql/0.1.6_to_0.1.7.sql';
                                         $ok=@exec_sql_lines($sql);
                                         if($ok==true) $option['phpstats_ver']='0.1.7';
                                         }
    if($option['phpstats_ver']=='0.1.7') {
                                         $sql='update_files/sql/0.1.7_to_0.1.8.sql';
                                         $ok=@exec_sql_lines($sql);
                                         if($ok==true) $option['phpstats_ver']='0.1.8';
                                         }
    if($option['phpstats_ver']=='0.1.8') {
                                         $sql='update_files/sql/0.1.8_to_0.1.9.sql';
                                         $ok=@exec_sql_lines($sql);
                                         include('update_files/sql/0.1.8_to_0.1.9.php');
                                         upgrade_018_to_019();
                                         if($ok==true) $option['phpstats_ver']='0.1.9';
                                         }
    if($option['phpstats_ver']=='0.1.9') {
                                         $sql='update_files/sql/0.1.9_to_0.1.9.1.sql';
                                         $ok=@exec_sql_lines($sql);
                                         if($ok==true) $option['phpstats_ver']='0.1.9.1';
                                         }
                           if($ok==true) {
                                         @chmod('option/', 0777);
                                         $writeServer=@touch('option/php-stats-options.php');
                                         if($writeServer){
                                           @chmod('config.php', 0666);
                                           $writeFilesList=Array('admin.php','click.php','download.php','escludi.php','php-stats.php','php-stats.recjs.php','php-stats.recphp.php','tracking.php','view_stats.js.php');
                                           for($i=0,$tot=count($writeFilesList);$i<$tot;++$i){
                                               $okcopy=copy('files_Write/'.$writeFilesList[$i],$writeFilesList[$i]);
                                               if(!$okcopy) { $writeServer=FALSE; break; }
                                           }
                                           $page_list=Array('main','details','os_browser','reso','systems','pages','percorsi','time_pages','referer','engines','query','searched_words','hourly','daily','weekly','monthly','calendar','compare','ip','country','bw_lang','downloads','clicks','trend');
                                           $ok=create_option_file(1);
                                           }
                                         }

    if($ok==true)
      {
      // Tutti gli aggiornamenti sono stati effettuati correttamente
      $page.=
      "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\" width=\"300\">".
      "<tr><td bgcolor=\"#707888\" nowrap><span class=\"tabletitle\">".$string['box_title_ok']."</span></td>".
      "<tr><td align=\"center\" valign=\"middle\" bgcolor=\"#EEEEEE\" nowrap>".
      "<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"5\" border=\"0\"><tr><td align=\"center\" valign=\"middle\"  nowrap>".
      (!$writeServer ? "<span class=\"testo\">$error[nowrite_server]</span><br>" : '').
      "<span class=\"testo\">".$string['done']."</span></td></tr>".
      "</td></tr></table>".
      "</table><br>".
      "<div align=\"center\"><form action=\"admin.php?action=preferenze\" method=\"post\">\n".
      "<input type=\"submit\" name=\"op\" value=\"$string[end]\">\n".
      "</form></div>\n";
      }
      else
      {
      // Si è verificato un errore in fase di aggiornamento
      $page.=
      "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\" width=\"300\">".
      "<tr><td bgcolor=\"#707888\" nowrap><span class=\"tabletitle\">".$string['box_title']."</span></td>".
      "<tr><td align=\"center\" valign=\"middle\" bgcolor=\"#EEEEEE\" nowrap>".
      "<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"5\" border=\"0\"><tr><td align=\"center\" valign=\"middle\"  nowrap>".
      "<span class=\"testo\">".$error['error_updating']."</span><br><br>$php_stats_error</td></tr>".
      "</td></tr></table>".
      "</table><br><br><br>";
            }
    }
    else
    {
    // Se l'azione non è "apply" faccio vedere la licenza e chiedo di aggiornarla
    $page.=
    "<table border=\"1\" bordercolor=#000000 cellpadding=\"0\" cellspacing=\"0\" width=\"400\"><tr><td bordercolor=#EEEEEE>".
    "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"100%\">".
    "<tr><td bgcolor=#EEEEEE align=\"center\">".
    "<br><span class=\"testo\">$string[license]</span>".
    "<div align=\"center\"><form action=\"update.php\" method=\"post\">\n".
    "<textarea name=\"text\" readonly cols=\"80\" rows=\"15\">\n";
    $fp=fopen("update_files/lang/$option[language]/license","r");
    while(!feof($fp)) $page.=fgets($fp,1024);
        fclose($fp);
        $page.=
        "</textarea><br><br>".
        "<input type=\"radio\" name=\"license\" value=\"1\" class=\"radio\"><span class=\"testo\">$string[license_ok]</span>     <input type=\"radio\" name=\"license\" value=\"0\" class=\"radio\" checked><span class=\"testo\">$string[license_no]</span>".
        "<br><br>".
        "<input type=\"hidden\" name=\"action\" value=\"apply\">".
        "<input type=\"submit\" name=\"op\" value=\"$string[next]\"><br><br>";
        $writeServer=false;
        @chmod("option", 0777);
        $writeServer=@touch('option/php-stats-options.php');
        $page.=
        (!$writeServer ? "<span class=\"testo\"><font color=#FF0000>$error[nowrite_server_1]</font></span>" : '').
        "<br></td></tr>".
        "</table>";
        "</td></tr>".
        "</table>".
        "</form></div>\n";
    }
  }

///////////////
// HTML PAGE //
///////////////
$html =
"\n<html>".
"\n<head>".
"\n<title>".str_replace("%php-stats-ver%",$version,$string['install'])."</title>".
"\n<style type=\"text/css\">".
"\n<!--".
"\nselect, option, textarea, input { BORDER-RIGHT: #9DBECD 1px solid; BORDER-TOP: #9DBECD 1px solid; BORDER-BOTTOM: #9DBECD 1px solid; BORDER-LEFT: #9DBECD 1px solid; COLOR: #000000; FONT-SIZE: 10px; FONT-FAMILY: Verdana; BACKGROUND-COLOR: #ffffff; }".
"\n.radio { BORDER-RIGHT: #9DBECD 0px solid; BORDER-TOP: #9DBECD 0px solid; BORDER-BOTTOM: #9DBECD 0px solid; BORDER-LEFT: #9DBECD 0px solid; COLOR: #000000; FONT-SIZE: 10px; FONT-FAMILY: Verdana; BACKGROUND-COLOR: #EEEEEE }".
"\na:visited,a:active {text-decoration:none; color:#000000; font-weight:plain;}".
"\na:hover {text-decoration:none; color:#AA0000; font-weight:plain;}".
"\na:link { text-decoration:none; color:#000000; font-weight:plain; }".
"\n.testo {  font-family: Verdana; font-size: xx-small; color:#000000; text-align: justify; }".
"\n.tabletitle { font-family: Verdana; font-size: xx-small; color: #FFFFFF ; font-weight: bold}".
"\n-->".
"\n</style>".
"\n</head>".
"\n<body>".
"\n<TABLE cellSpacing=\"1\" cellPadding=\"1\" width=\"760\" height=\"100%\" align=\"center\" bgColor=\"black\">".
"  <TBODY>".
"    <TR>".
"      <TD  height=30 bgColor=#c1c1c1>".
"            <TABLE width=740 cellSpacing=0 cellPadding=0>".
"            <TR>".
"              <TD><IMG alt=\"\" src=\"setup_files/logo.gif\"></TD>".
"              <TD vAlign=bottom align=right><FONT face=verdana size=1>PHP-STATS UPDATER (v1.3)</FONT></TD>".
"            </TR>".
"        </TABLE>".
"          </TD>".
"    </TR>".
"    <TR>".
"      <TD vAlign=top bgColor=#d9dbe9><CENTER><br><br>".
"\n<h3>".str_replace("%php-stats-ver%",$version,$string['install'])."</h3>".
"\n\n".$page;
"        <br><br></CENTER>".
"    <TR>".
"      <TD height=10 bgColor=#c1c1c1><CENTER><FONT face=verdana size=1>© <A href=\"http://www.php-stats.com/\">Webmaster76</A></FONT></CENTER></TD>".
"    </TR>".
"  </TBODY>".
"\n</TABLE>".
"\n</body>".
"\n</html>";

// Restituisco la pagina!
echo"$html";

/*--------------------------------------------------
  remove_remarks()
  Remove # type remarks from large sql files
  --------------------------------------------------*/
function remove_remarks($sql) {
        $i=0;
        while($i < strlen($sql)) {
                if($sql[$i] == "#" and ($i==0 or $sql[$i-1] == "\n")) {
                        $j=1;
                        while($sql[$i+$j] != "\n") {
                                ++$j;
                                if($j+$i > strlen($sql)) break;
                        }
                        $sql=substr($sql,0,$i) . substr($sql,$i+$j);
                }
                ++$i;
        }
        return($sql);
}

/*--------------------------------------------------
  split_sql_file()
  Split up a large sql file into individual queries
  --------------------------------------------------*/
function split_sql_file($sql, $delimiter) {
        global $option;
        // BEGIN Cambio prefisso della tabella!!!!
        $sql=str_replace("php_stats","$option[prefix]",$sql);
        // END Cambio prefisso della tabella!!!!
        $sql=trim($sql);
        $char="";
        $last_char="";
        $ret=array();
        $in_string=true;
        for($i=0; $i<strlen($sql); ++$i) {
                $char=$sql[$i];

                /* if delimiter found, add the parsed part to the returned array */
                if($char == $delimiter && !$in_string) {
                        $ret[]=substr($sql, 0, $i);
                        $sql=substr($sql, $i + 1);
                        $i=0;
                        $last_char="";
                }

                if($last_char == $in_string && $char == ")")  $in_string=false;
                if($char == $in_string && $last_char != "\\") $in_string=false;
                elseif(!$in_string && ($char == "\"" || $char == "'") && ($last_char != "\\")) $in_string=$char;
                $last_char=$char;
        }

        if(!empty($sql)) $ret[]=$sql;
        return($ret);
}




/*--------------------------------------------------
  exec_sql_lines()
  takes a file and executes all its sql-queries
  uses remove_remark() and split_sql_file()
  --------------------------------------------------*/
function exec_sql_lines($sql_file, $old_string='', $new_string='') {
global $php_stats_error;
        $error_lev=0;
        $sql_query=isset($sql_query) ? $sql_query : "";

        if(!empty($sql_file) && $sql_file != "none") {
                $sql_query=fread(fopen($sql_file, "r"), filesize($sql_file));
                /* If magic_quotes_runtime is enabled, most functions that return data from any sort of external source
                   including databases and text files will have quotes escaped with a backslash.
                */
                if(get_magic_quotes_runtime() == 1) $sql_query=stripslashes($sql_query);
                /* replace old_string with new_string if they are set */
                if($old_string != '') {
                        $sql_query=ereg_replace($old_string,$new_string,$sql_query);
                }
        }
        $sql_query=trim($sql_query);

        if($sql_query != "") {
                $sql_query  =remove_remarks($sql_query);
                $pieces     =split_sql_file($sql_query,";");
                $cnt_pieces =count($pieces);
                /* run multiple queries */
                for ($i=0; $i<$cnt_pieces; ++$i) {
                        $sql=trim($pieces[$i]);
                        if(!empty($sql) and $sql[0] != "#")
                                                  {
                                                  $result=sql_query($sql);
                                                  if($result==false)
                                                    {
                                                        $php_stats_error.="<font color=\"#FF0000\" size=\"1\">Error executing: <b>$sql</b><br>Error string: <b>".mysql_error()."</b></font><br><br>";
                                                        $error_lev=1;
                                                        }
                                                  }
                }
        }
        if($error_lev==0)
                return true;
                else
                return false;
}
?>