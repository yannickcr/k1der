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

// Per ragioni di sicurezza i file inclusi avranno un controllo di provenienza
define('IN_PHPSTATS', true);

                if(!isset($_POST)) $_POST=$HTTP_POST_VARS;
              if(!isset($_COOKIE)) $_COOKIE=$HTTP_COOKIE_VARS;
               if(!isset($_FILES)) $_FILES=$HTTP_POST_FILES;
if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie='';
    if(isset($_POST['operation'])) $operation=addslashes($_POST['operation']); else $operation='';
if(isset($_COOKIE['pass_cookie'])) $pass_cookie=$_COOKIE['pass_cookie']; else $pass_cookie='';
     if(isset($_POST['compress'])) $compress=addslashes($_POST['compress']); else $compress=0;
         if(isset($_POST['data'])) $data=addslashes($_POST['data']); else $data='NO';
 if(isset($_POST['selected_tbl'])) $selected_tbl=$_POST['selected_tbl'];
$primary="";
$return="";
// inclusione delle principali funzioni esterne
include("../config.php");
include("./main_func.inc.php");
include("./admin_func.inc.php");
if($option['prefix']=='') $option['prefix']='php_stats';

// Connessione a MySQL e selezione database
db_connect();

// Leggo le variabili
$result=sql_query("SELECT name,value FROM $option[prefix]_config");
while($row=@mysql_fetch_array($result))
  {
  $option[$row[0]]=$row[1];
  }
// Controllo che l'utente abbia i permessi necessari altrimenti LOGIN
if($pass_cookie!=md5($option['admin_pass'])){
   if($option['persistent_conn']!=1) mysql_close();
   header("Location: $option[script_url]/admin.php?action=login"); exit();
}
// Inclusioni
include("../lang/$option[language]/main_lang.inc");
// Per evitare il timeout dello script
@set_time_limit(1200);

$memory_limit=trim(@ini_get('memory_limit'));
// Setto 3 MB di base
if(empty($memory_limit)) $memory_limit=3*1024*1024;

    if(strtolower(substr($memory_limit,-1))=='m') $memory_limit=(int)substr($memory_limit,0,-1)*1024*1024;
    elseif(strtolower(substr($memory_limit,-1))=='k') $memory_limit=(int)substr($memory_limit,0,-1)*1024;
    elseif(strtolower(substr($memory_limit,-1))=='g') $memory_limit=(int)substr($memory_limit,0,-1)*1024*1024*1024;
    else $memory_limit=(int)$memory_limit;
    if($memory_limit>1500000) $memory_limit-=1500000;
    $memory_limit*=2/3;

// Imposto il limite di guardia di 1/2 MB sotto il buffer
 $limit_memory_guard=$memory_limit-floor((1*1024*1024)/2);
 $data_buffer='';
 $data_buffer_lenght=0;
 $str='';

$date=date("Y-m-d");
if($compress==1) {
// @ob_start();
// @ob_implicit_flush(0);
header("Content-Type: application/x-gzip; name=\"php-stats[$date].sql.gz\"");
header("Content-disposition: attachment; filename=php-stat[$date].sql.gz");
}
else { if($compress!=0) exit(); else {
header("Content-Type: text/x-delimtext; name=\"php-stats[$date].sql\"");
header("Content-disposition: attachment; filename=php-stats[$date].sql");
}}

// define("VERBOSE", 0);
        //mysql_select_db($option['database'],$db);
        $sql="select version() as version";
        $result=sql_query($sql,$db);
        $statrow=@mysql_fetch_array($result);
        $version=$statrow["version"];
if($data!="YES") $data="No";
$dump_code=md5("code:$option[phpstats_ver]");
$str.="
#---------------------------------------------------------
#
# Php-Stats Dump
# Author: Matrix (original code: Kill-9)
#
# Host: $option[host]   Database: $option[database]
#---------------------------------------------------------
# Server version        $version
# Dumping data $data
#---------------------------------------------------------
# Dump code: $dump_code
#---------------------------------------------------------

";

foreach($selected_tbl as $val)
 {
 $table_name=$val;
$str.="
#
# Table structure for table '$table_name'
#

DROP TABLE /*!32300 IF EXISTS*/ $table_name;

CREATE TABLE $table_name (
";
$testcreate=0;

        $sql_desc="desc $table_name";
        $result_desc=sql_query($sql_desc,$db);
        $rows=0;
        while($statrow=@mysql_fetch_array($result_desc))
                {
                $field=$statrow["Field"];
                $type=$statrow["Type"];
                $null=$statrow["Null"];
                $type_for_test=substr($type,0,3);
                if(!$null) $null="NOT NULL"; else $null="";
                $key=$statrow["Key"];
                if($key=="PRI") $primary="$field"; else $index="yes";
                $default=$statrow["Default"];
                if(($type_for_test=="int") && ($null="NOT NULL") && (!$default)) $default=0;
                $extra =$statrow["Extra"];
if($testcreate==0) {
$str.="$field $type DEFAULT '$default' $null  $extra
";
$testcreate=1; }
else {
$str.=",$field $type DEFAULT '$default' $null  $extra
"; }

                }
                if($index=="yes")
                {
                $sql_index="show index from $table_name";
                $result_index=sql_query($sql_index,$db);
                $num=0;
                while($statrow=@mysql_fetch_array($result_index))
                        {
                        $non=$statrow["Non_unique"];
                        if($non==1)
                                {
                                $name[$num]=$statrow["Key_name"];
                                $col_name[$num]=$statrow["Column_name"];
                                $num=$num + 1;
                                }
                        }
                if($num!=0)
                {
                $str ="Key $name[0] (";

                for ($i=0;$i<$num;++$i)
                        {
                        if($i==0)
                                {
                        $str.= "$col_name[$i]";

                                }
                        else
                                {
                                $str.= ",$col_name[$i]";

                                }
                        }
                        $str.= ")";

                        if($primary)
                        {
                        $str.= ",
                        ";

                        }
                        else
                        {
                        $str.= "
                        ";

                        }
                }
                }
                if($primary)
                {
                $str.= ",Primary key($primary)";

                }
        $str.= ");
";

$key="";
$primary="";
        // DONE DUMPING TABLE DESC

if($data=="YES")
        {
        $str.="#
# Dumping data for table '$table_name'
#

";

        $sql_select="select * from $table_name";
        $result_select=sql_query($sql_select,$db);
        $tot_fields=mysql_num_fields($result_select);
        while($statrow=@mysql_fetch_array($result_select))
                {
                $i=0;
                $str.= "INSERT INTO $table_name VALUES (";

                while($i<$tot_fields)
                        {
                        $type=mysql_field_type($result_select,$i);
                        if($i==0)
                                {
                                if($statrow[$i] || $statrow[$i]==0) $hold=$statrow[$i]; else $hold="NULL";
                                if($type=="int" || $hold=="NULL")
                                        {
                                        $str.=addslashes($hold);

                                        }
                                else
                                        {
                              $hold=addslashes($hold);
                                        $str.= "'$hold'";

                                        }
                                }
                        else
                                {
                                if($statrow[$i] || $statrow[$i]==0) $hold=$statrow[$i]; else $hold="NULL";
                                if($type=="int" || $hold=="NULL")
                                        {
                              $hold=addslashes($hold);
                                        $str.= ",'$hold'";

                                        }
                                else
                                        {
                              $hold=addslashes($hold);
                                        $str.= ",'$hold'";

                                        }
                                }
                        $i=$i + 1;
                        }
                        $str.= ");
";

                }
        }
write_file_dump($str);
$str='';
        }
empty_buffer();

function write_file_dump($line)
{
 global $data_buffer,$limit_memory_guard,$compress;
 $data_buffer.=$line;
 $data_buffer_lenght=strlen($data_buffer);
 if($data_buffer_lenght>$limit_memory_guard) {
   if($compress==1) $data_buffer=gzencode($data_buffer);
 echo $data_buffer;
 $data_buffer='';
 $data_buffer_lenght=0;
}
 return;
}

function empty_buffer()
{
 global $data_buffer,$compress;
  if($compress==1) $data_buffer=gzencode($data_buffer);
 echo $data_buffer;
 return;
}

// Chiusura connessione a MySQL se necessario.
if($option['persistent_conn']!=1) mysql_close();
?>