<?php
//
// Copyright God`
// god@godserv.net
//

$date = date("U");

include "irc_time.php";

$thedate = $date-$olddate;

if ($thedate >= "60")
{

$serv = "irc.quakenet.org";  //server
$port = "6667";  //port du server
$nick = "Kbot";  //nom utilisé lors de la connection
$chan = "#k1der";  //channel à traiter

//echo "connect to $serv...<br>"; 
$fp=fsockopen("$serv","$port"); 
//echo "Ident...<br>"; 
fputs($fp,"nick $nick\n"); 
fputs($fp,"user Godphpscript \"localhost\" \"$serv\" :$nick\n");
while($new=fgets($fp,1024)){ 
	
$new = str_replace("\r", "", $new); 
$new = str_replace("\n", "", $new); 

$tab_temp0=explode(":",$new); 
$msg_void=$tab_temp0[0];
$msg_info=$tab_temp0[1];
$msg_user=$tab_temp0[2]; 
$msg_void1=$tab_temp0[3]; 

$tab_temp3=explode(" ",$msg_info); 
$msg_info_type=$tab_temp3[1]; 


if($msg_info_type=="433"){ 
	$randnick++; 
	$mennick.=$randnick; 
	fputs($fp,"nick $mennick\n"); 
} 

if(eregi("PING",$tab_temp0[0])){ 
	fputs($fp,"PONG $tab_temp0[1]\n"); 
	sleep(1);
} 

$ligneesp = explode(" ",$new);

if ($ligneesp[1] == "322"){
	$chanuser = $ligneesp[3];
	$nbruser = $ligneesp[4];
	fputs($fp,"quit byebye\n");
			}
			
if ($ligneesp[1] == "376"){
fputs($fp,"list $chan\n"); 
}


//echo "$new <br>"; 

}
echo "<b>$nbruser</b>";

$source = "";

$src = "<?
\$olddate = \"$date\";
\$oldusers = \"$nbruser\";
?>";

// ID du message, nom de fichier.
$nomf = "irc_time.php";

// Creer le fichier.
$nfichier = fopen($source."$nomf", "w");
fclose($nfichier);

// Enregistrer le texte.
$nfichier = fopen($source."$nomf", "w+");
fputs($nfichier, "$src");
fclose($nfichier);
}
else
{
echo "<b>$oldusers</b>";
}
?>
