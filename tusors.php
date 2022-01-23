<?php
if(!isset($_GET['url'])) $_GET['url']='http://www.k1der.net';
include ('compt_sponsor.php');
$ip=$_SERVER["REMOTE_ADDR"];
if($ip!=$lastip) {
	$fp=fopen('compt_sponsor.php','w');
	$texte='<?php
$clicks='.++$clicks.';
$lastip=\''.$ip.'\';
?>';
	fwrite($fp,$texte);
	fclose($fp);
}
header('location:'.$_GET['url']);
?>