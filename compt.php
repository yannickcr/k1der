<?
/*
Fichier de l'arhive :

compt.php3 : fichier de compteur
comptsql   : fichier mysql
readme.txt : explication
*.jpg      : images

------------------------------------------------------------------------

Script :  super4compt
Prix : Gratuit
Licence : Si vous modifiez ce script, merci de m'en informer : ggreg4@caramail.com
Site : http://www.multimania.com/ouf2dingue

------------------------------------------------------------------------

1. Ouvrir le fichier compt.php3 et ins�rer les variable.
2. Ensuite installer la table compt ce trouvant dans le fichier comptsql.
3. et inserer un include("compt.php3") ou vous shouaiter l'inserer.

------------------------------------------------------------------------*/
/*###############variable a modifier###############*/
//Nom de la base
$database = "k1der1";

//Nom d'utilisateur
$user = "root";

//Mot de passe
$password = "";

//Chemin sql bd
$host = "localhost";

//nombres de temps qu'une ip reste blocker(en segonde)
$nbtemps=7200;

//activer le mode image(1->mode image : 0->mode text)
$modeimg=0;

/*###############fin de variable a modifier###############*/
mysql_connect($host, $user, $password);
mysql_select_db($database);

$heurec=date("U");
$ipc=$REMOTE_ADDR;
$result=mysql_query("SELECT * FROM compt");
$nb=mysql_result($result,0,'nb');
$nb++;

$res=mysql_query("SELECT * FROM compt_secu WHERE ip='$ipc'");
if (mysql_num_rows($res))
	{
	mysql_query("UPDATE compt_secu SET time='$heurec' WHERE ip='$ipc'");
	}
else
	{
	mysql_query("INSERT INTO compt_secu (ip, time) VALUES ('$ipc', '$heurec')");
	mysql_query("UPDATE compt SET nb='$nb'");
	}

$heuremax=$heurec-$nbtemps;
mysql_query("DELETE FROM compt_secu WHERE time<'$heuremax'");

function replace(&$inputString)
	{
	$inputString = ereg_replace("0", "<img src='images/0.jpg' border=0>", $inputString);
	$inputString = ereg_replace("1", "<img src='images/1.jpg' border=0>", $inputString);
	$inputString = ereg_replace("2", "<img src='images/2.jpg' border=0>", $inputString);
	$inputString = ereg_replace("3", "<img src='images/3.jpg' border=0>", $inputString);
	$inputString = ereg_replace("4", "<img src='images/4.jpg' border=0>", $inputString);
	$inputString = ereg_replace("5", "<img src='images/5.jpg' border=0>", $inputString);
	$inputString = ereg_replace("6", "<img src='images/6.jpg' border=0>", $inputString);
	$inputString = ereg_replace("7", "<img src='images/7.jpg' border=0>", $inputString);
	$inputString = ereg_replace("8", "<img src='images/8.jpg' border=0>", $inputString);
	$inputString = ereg_replace("9", "<img src='images/9.jpg' border=0>", $inputString);
	}
if ($modeimg==1){replace($nb);}
echo "$nb";
?>