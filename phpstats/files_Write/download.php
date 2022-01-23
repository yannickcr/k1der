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


// SECURITY ISSUES
define('IN_PHPSTATS',true);
define('__OPTIONS_FILE__','option/php-stats-options.php');
define('__LOCK_FILE__','option/options_lock.php');

// Richiamo variabili esterne
                       if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
              if(isset($_REQUEST['id'])) $id=$_REQUEST['id']; else $id='';
     if(isset($_SERVER['HTTP_REFERER'])) $HTTP_REFERER=$_SERVER['HTTP_REFERER'];
      if(isset($_SERVER['REMOTE_ADDR'])) $ip=(isset($_SERVER['HTTP_PC_REMOTE_ADDR']) ? $_SERVER['HTTP_PC_REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR']);
                if(isset($_GET['mode'])) $mode=$_GET['mode']; else $mode='';


// inclusione delle principali funzioni esterne
if (file_exists(__LOCK_FILE__)) sleep(2);
if (!@include(__OPTIONS_FILE__)) die("<b>ERRORE</b>: File di config non accessibile.");

if(!@include('inc/main_func.inc.php')) die('<b>ERRORE</b>: File main_func.inc.php non accessibile.');
if(!@include('inc/admin_func.inc.php')) die('<b>ERRORE</b>: File admin_func.inc.php non accessibile.');

if($option['prefix']=='') $option['prefix']='php_stats';
if(ini_get('zlib.output_compression'))
ini_set('zlib.output_compression', 'Off');

// Connessione a MySQL e selezione database
db_connect();

$downloads_withinterface='';
$errorDownload=false;
$result=sql_query("SELECT nome,descrizione,type,home,size,downloads,withinterface FROM $option[prefix]_downloads WHERE id='$id'");
if(@mysql_num_rows($result)==0) $errorDownload=true;
else list($downloads_nome,$downloads_descrizione,$downloads_type,$downloads_home,$downloads_size,$downloads_downloads,$downloads_withinterface)=@mysql_fetch_row($result);

if($option['template']=='') $option['template']='default';
  $template_path='templates/'.$option['template'];

  // Inclusioni secondarie: template della pagina e language pack.
  if(!@include("lang/$option[language]/main_lang.inc")) die("<b>ERRORE</b>: File $option[language]/main_lang.inc non accessibile."); // Language file
  if(!@include("$template_path/def.php")) die("<b>ERRORE</b>: File $template_path/def.php non accessibile.");                // Template defs

  // Titolo pagina
  $phpstats_title=@$string['down_title'];

// VERIFICO SE DEVO EFFETTUARE IL DOWNLOAD TRAMITE INTERFACCIA O NO
if(($mode!='download' && $downloads_withinterface=='YES') || $errorDownload===true)
  {
  if($errorDownload===true) $page=info_box($string['error'],$error['down_noid']);
  else
    {
    $phpstats_title=$phpstats_title.' '.stripslashes($downloads_nome);
    $page=
    "\n<br>".
    "\n<form action=\"download.php?mode=download\" method=\"post\">".
    "\n<TABLE $style[table_header] width=\"90%\">".
    "\n\t<TR><TD bgcolor=$style[table_title_bgcolor] colspan=\"2\"><span class=\"tabletitle\"><center>$string[sommario]</center></span></TD></TR>".
    "\n\t<TR><TD align=right width=\"40%\" bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$string[down_name]</span></TD><TD bgcolor=$style[table_bgcolor]><span class=\"tabletextB\">".stripslashes($downloads_nome)."</span></TD>".
    "\n\t<TR><TD align=right width=\"40%\" bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$string[down_desc]</span></TD><TD bgcolor=$style[table_bgcolor]><span class=\"tabletextB\">".stripslashes($downloads_descrizione)."</span></TD>".
    "\n\t<TR><TD align=right width=\"40%\" bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$string[down_type]</span></TD><TD bgcolor=$style[table_bgcolor]><span class=\"tabletextB\">$downloads_type</span></TD>".
    "\n\t<TR><TD align=right width=\"40%\" bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$string[down_home]</span></TD><TD bgcolor=$style[table_bgcolor]><span class=\"tabletextB\"><a href=\"$downloads_home\">$downloads_home</a></span></TD>".
    "\n\t<TR><TD align=right width=\"40%\" bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$string[down_size]</span></TD><TD bgcolor=$style[table_bgcolor]><span class=\"tabletextB\">$downloads_size</span></TD></TR>".
    "\n\t<TR><TD align=right width=\"40%\" bgcolor=$style[table_bgcolor]><span class=\"tabletextA\">$string[down_count]</span></TD><TD bgcolor=$style[table_bgcolor]><span class=\"tabletextB\">$downloads_downloads</span></TD></TR>".
    "\n\t<tr><td bgcolor=$style[table_bgcolor] colspan=\"2\"><INPUT TYPE=\"hidden\" name=\"id\" value=\"$id\"><center><input type=\"Submit\" value=\"$string[down_download]\"></center></td></tr>".
    "\n</table>\n</form>";
    }
  include("$template_path/templdetails.php");
  echo $template;
  }
else
  {
  if($id!='')
    {
    $result=sql_query("SELECT url FROM $option[prefix]_downloads WHERE id='$id' LIMIT 1");
    if(@mysql_affected_rows()>0) list($get)=@mysql_fetch_row($result);
    else
      {
      $page=info_box($string['error'],$error['down_noid']);
      include("$template_path/templdetails.php");
      echo $template;
      exit();
      }
    }
  if($get!='')
    {
    $get=str_replace(' ','%20',$get);
    $check=($option['check_links'] ? @fopen($get,'r') : true);
    if($check!=false)
      {
      sql_query("UPDATE $option[prefix]_downloads SET downloads=downloads+1 WHERE id='$id'");

      if($modulo[0])
        {
        // INSERISCO NEI DETTAGLI IL DOWNLOAD DEL FILE
        $result=sql_query("SELECT visitor_id FROM $option[prefix]_cache WHERE user_id='$ip' LIMIT 1");
        if(@mysql_num_rows($result)>0)
          {
          list($visitor_id)=@mysql_fetch_row($result);
          $date=time()-$option['timezone']*3600;
          $loaded="dwn|$id";
          sql_query("INSERT INTO $option[prefix]_details VALUES ('$visitor_id','$ip','','','','','$date','','$loaded','','','')");
          }
       }

      if($option['down_mode']==0) header("location: $get");
      else $filename=($option['down_mode']==1 ? $get : relative_path($get,$_SERVER['PHP_SELF']));

      $ext=substr($filename,-3);
      if($filename=='')
        {
        $page=info_box($string['error'],$error['down_noid']);
        include("$template_path/templdetails.php");
        echo $template;
        exit();
        }

      switch($ext)
        {
        case 'pdf': $ctype='application/pdf'; break;
        case 'exe': $ctype='application/octet-stream'; break;
        case 'zip': $ctype='application/zip'; break;
        case 'doc': $ctype='application/msword'; break;
        case 'xls': $ctype='application/vnd.ms-excel'; break;
        case 'ppt': $ctype='application/vnd.ms-powerpoint'; break;
        case 'gif': $ctype='image/gif'; break;
        case 'png': $ctype='image/png'; break;
        case 'jpg': $ctype='image/jpg'; break;
        default: $ctype='application/force-download';
        }

        header('Content-Type: application/x-download');
        $user_agent = strtolower ($_SERVER['HTTP_USER_AGENT']);
        if ((is_integer (strpos($user_agent, 'msie'))) && (is_integer (strpos($user_agent, 'win')))) //da migliorare con riconoscimento
        {
          header('Content-Disposition: filename="'.basename($filename).'"' );
        } else {
          header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        }
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public');
        header('Content-Transfer-Encoding: binary');
        //header('Content-Length: '.filesize($filename));
        @readfile($filename);
        exit();
      }
    }
  else
    {
    $page=info_box($string['error'],$error['file_down']);
    include("$template_path/templdetails.php");
    echo $template;
    exit();
    }
  if($modulo[0])
    {
    // INSERISCO NEI DETTAGLI IL DOWNLOAD DEL FILE
    $result=sql_query("SELECT visitor_id FROM $option[prefix]_cache WHERE user_id='$ip' LIMIT 1");
    if(@mysql_num_rows($result)>0)
      {
      list($visitor_id)=@mysql_fetch_row($result);
      $date=time()-$option['timezone']*3600;
      $loaded="dwn|$id";
      sql_query("INSERT INTO $option[prefix]_details VALUES ('$visitor_id','$ip','','','','','$date','','$loaded','','','')");
      }
    }
  }

// Chiusura connessione a MySQL se necessario.
if($option['persistent_conn']!=1) mysql_close();

?>