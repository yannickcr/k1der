<?php

// DEFINIZIONE VARIABILI PRINCIPALI
define('IN_PHPSTATS',true);

if(isset($_SERVER['REMOTE_ADDR'])) $ip=(isset($_SERVER['HTTP_PC_REMOTE_ADDR']) ? $_SERVER['HTTP_PC_REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR']);
                 if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
            if(isset($_GET['id'])) $id=$_GET['id']; else $id='';
            if(isset($_GET['psw'])) $psw=$_GET['psw']; else $psw='';

// Inclusioni necessarie
include('config.php');
include('inc/admin_func.inc.php');
include('inc/main_func.inc.php');

if($id==='') die('Id non settato');

if($psw==$option['exc_pass']) {
  $id-=0;
  // Controllo la validità dell'id (Per evitare SQL injection!)
  if(!ereg('(^[0-9][0-9]?$)',$id)) die('Id non valido'); //$error[down_errs_id]

  // Definizione Variabili
  $id=($id<10 ? '0'.$id : "$id");
  $ip=str_pad(sprintf('%u',ip2long($ip)),10,'0',STR_PAD_LEFT);
  $newentry=$id.$ip;

  // Connessione a MySQL e selezione database
  db_connect();

  $result=sql_query("SELECT name,value FROM $option[prefix]_config WHERE name='exc_dip' OR name='server_url'");
  while($row=@mysql_fetch_row($result)) $tmp[$row[0]]=$row[1];

  $found=FALSE;
  $changed=FALSE;

  $tmp['exc_dip']=@trim($tmp['exc_dip']);

  if($tmp['exc_dip']==='') $excdips=Array();
  else if(strpos($tmp['exc_dip'],"\n")===FALSE){ $excdips=Array(); $excdips[]=$tmp['exc_dip']; }
  else $excdips=@explode("\n",$tmp['exc_dip']);

  for($i=0,$tot=count($excdips);$i<$tot;++$i)
    {
    if(substr($excdips[$i],0,2)===$id) //controllo se è lo stesso id
      {
      $found=TRUE;
      if($excdips[$i]===$newentry) break;
      $changed=TRUE;
      $excdips[$i]=$newentry; //modifico l'entry
      break;
      }
    }

    if(!$found){ $excdips[]=$newentry; $changed=TRUE; }

  if($changed)
    {
    $excdips=implode("\n",$excdips); //ip din

    sql_query("UPDATE $option[prefix]_config SET value='$excdips' WHERE name='exc_dip'");

    $createDone=create_option_file();
    }

  if($option['persistent_conn']!=1) mysql_close();

  $url=explode("\n",$tmp['server_url']);

  header('Cache-Control: no-store, no-cache, must-revalidate');
  header('Location: '.$url[0]);
}

else die('Password non valida');

?>