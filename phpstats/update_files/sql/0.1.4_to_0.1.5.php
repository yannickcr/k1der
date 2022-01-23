<?
// AGGIORNAMENTO DA 0.1.4 A 0.1.5
function upgrade_014_to_015() {
global $db,$option;
// SPOSTO LE RISOLUZIONI NELLA TABELLA SYSTEMS
$result=sql_query("SELECT * FROM $option[prefix]_reso");
while($row=@mysql_fetch_array($result))
  {
  sql_query("INSERT INTO $option[prefix]_systems VALUES('','','$row[0]','','$row[1]','$row[2]','')");
  }

// SPOSTO I COLORI NELLA TABELLA SYSTEMS
$result=sql_query("SELECT * FROM $option[prefix]_colo");
while($row=@mysql_fetch_array($result))
  {
  sql_query("INSERT INTO $option[prefix]_systems VALUES('','','','$row[0]','$row[1]','$row[2]','')");
  }

// SPOSTO I SISTEMI OPERATIVI E I BROWSER NELLA TABELLA SYSTEMS
$result=sql_query("SELECT * FROM $option[prefix]_agent");
while($row=@mysql_fetch_array($result))
  {
  $nome_bw=chop(getbrowser($row[0]));
  $nome_os=chop(getos($row[0]));
  echo"$nome_os - $nome_bw<br>";
  sql_query("INSERT INTO $option[prefix]_systems VALUES('$nome_os','$nome_bw','','','$row[1]','$row[2]','')");
  }

// CANCELLO LE TABELLE OBSOLETE  
sql_query("DROP TABLE $option[prefix]_agent");
sql_query("DROP TABLE $option[prefix]_reso");
sql_query("DROP TABLE $option[prefix]_colo");
sql_query("DROP TABLE $option[prefix]_tmp");        
}
?>