<?php
/*********************************************************************/
/*		 DVconnectés - Script qui compte le nombre de connectés		 */
/*				 © Suprem ( suprem@free.fr ) - 2001					 */
/*********************************************************************/

include "dv_config.php";

$db = mysql_connect("$dv_host", "$dv_login", "$dv_pass") or die("Connexion impossible");
mysql_select_db("$dv_base",$db);

$ipAppelant = $REMOTE_ADDR;
$heureAppelant = time();

$query = "SELECT * FROM DVconnectes";
$result = mysql_query($query);

while ($row = mysql_fetch_array($result)) {

	if ($row[dateFin] < $heureAppelant) {

		$query_plus_la = "DELETE FROM DVconnectes WHERE ip='$row[ip]'";
		$result_plus_la = mysql_query($query_plus_la);

	}


}

$query_appelant = "SELECT * FROM DVconnectes where ip='$ipAppelant'";
$result_appelant = mysql_query($query_appelant);
$nb3 = mysql_num_rows($result_appelant);

if ($nb3 != 0) {}

else {

	$dateDebut = time();
	$dateFin = time()+300;
		
	$query_inc = "INSERT INTO DVconnectes VALUES('$dateDebut','$dateFin','$ipAppelant')";
	$result_inc = mysql_query($query_inc) or die ("");
}

$query_connectes = "SELECT * FROM DVconnectes";
$result_connectes =  mysql_query($query_connectes);
$count = mysql_num_rows($result_connectes);

if ($count == 1)	$txt = "";
else				$txt = "";

echo $count." ".$txt."\n";
?>