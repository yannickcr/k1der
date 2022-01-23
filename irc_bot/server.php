News Archive
<?php
/* ------------------------------------------------------------- */
// Module CounterStrike Status
// Auteur : Boz (boz@gorets.com)
// D'après les travaux de Henrik Schack Jensen (henrik@schack.dk)
/* ------------------------------------------------------------- */
/*    ATTENTION ! NE MODIFIEZ RIEN EN DESSOUS DE CETTE LIGNE !   */
/* ------------------------------------------------------------- */
require("../live/serveur.php");
require("../live/counterstrike.php");
if(!$HTTP_POST_VARS["serveradr"] AND !$HTTP_POST_VARS["serverport"]){
	$serveradr = $ip;
	$serverport= $port;
}
else {
	$serveradr = str_replace(".","",$HTTP_POST_VARS["serveradr"]);
	if(is_numeric($serveradr) AND is_numeric($HTTP_POST_VARS["serverport"])){
    	$serveradr = trim($HTTP_POST_VARS["serveradr"]);
    	$serverport= trim($HTTP_POST_VARS["serverport"]);
	}
	else {
    	$serveradr = $ip;
    	$serverport= $port;
	}
}
$csinfo = new CounterStrike;
$status = $csinfo->getServerInfo($serveradr,$serverport,1000);
if ($status) {
    $status = $csinfo->getServerPlayers($serveradr,$serverport,1000);
    $status = $csinfo->getServerRules($serveradr,$serverport,1000);
    $status = $csinfo->getServerStatus($serveradr,$serverport,1000,$rcon);
    $rules = $csinfo->m_serverrules;
	$type = $csinfo->m_serverstatus;
	
echo $csinfo->m_servervars["servername"]." - 
";
echo $serveradr.":".$serverport."
";
echo " - ".$csinfo->m_servervars["mapname"]." - ".$csinfo->m_servervars["currentplayers"]." sur ".$csinfo->m_servervars["maxplayers"]." - 
";
echo "Status: ".$type."
";
} else {
echo "Serveur Down ! C'est abuzer...
";
echo "
";
/*echo "
";*/
}
?>
denotes services announcement