<?
include "secu.php";

if ($type == "mechant")
{
$message = "Haha !<br><br>Tu croyait vraiment pouvoir rentrer chez les K1der ?<br>Tu t'est pas regardé ou koi ? Tu sais pas jouer ! T'est une kiche ! T'arrivera a rien dans la vie !<br><br>Nan, sérieux trouve un autre passe temps. Je sais pas moi, essaye le trico.<br><br>Voila, au plaisir de plus te revoir.<br><br>Team K1der";
}
if ($type == "gentil")
{
$raison = str_replace("
","<br>",$raison);
$message = "Salut !<br><br>Je suis désolé mais tu ne correspond pas au profil de joueur que nous recherchons.<br>Raison:<br>$raison<br><br>Voila, nous espérons cependant te revoir sur notre chan (#k1der sur Qnet) ou en LAN.<br><br>Team K1der";
}
if ($type == "chan")
{
$message = "Salut<br>Nous avons bien reçu ta demande de recrutement et Nous sommes intéressé par ta candidature<br>Donc sa serai bien que tu passe sur IRC ( #k1der sur Qnet) un soir et que tu pv un @ , histoire qu'on puisse parler et voir ton Level :)<br>Si tu n'a pas mIRC tu peut t'y connecter à partir de cette page: <a href=http://www.k1der.net/index.php?page=irc>http://www.k1der.net/index.php?page=irc</a><br><br>Voila,++<br><br>Team K1der";
}

$date = date("d/m/Y"); 
$heure = date("H:i");
$from="From:k1der_bot@k1der.net\n"; 
$from.="MIME-version: 1.0\n"; 
$from.="Content-type: text/html; charset= iso-8859-1\n"; 
$sujet = 'Recrutement dans le clan K1der'; 
$message = str_replace("
","<br>",$message);
$message = "<font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$message<br><br>---------------------------------------<br>@+ sur <a target=_blank href=http://www.k1der.net>-=K1der=- The Chocolat Effect</a><br></font>"; 
$message = stripslashes($message);
mail ($mail,$sujet,$message,$from);

$req = MYSQL_QUERY("SELECT * FROM recrut_comm WHERE id_recrut='$id' && nom='$nom'");
$res = MYSQL_NUM_ROWS($req);

if ($res != 0)
{
mysql_query("UPDATE recrut_comm SET mail='$type' WHERE id_recrut='$id' && nom='$nom'");
}
else
{
mysql_query("INSERT INTO recrut_comm VALUES('','$id','$nom','','$type')");
}
?>
<script language="Javascript">
alert('Message envoyé avec Succès !');
window.location='../index.php?page=visu_details_recrut&id=<? echo $id; ?>';
</script>