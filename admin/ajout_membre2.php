<? $level = "10"; include "secu.php";

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$rnd1 = rand (0,9);
$rnd2 = rand (0,9);
$rnd3 = rand (0,9);
$rnd4 = rand (0,9);
$rnd5 = rand (0,9);
$pass2 = "$rnd1$rnd2$rnd3$rnd4$rnd5";

$thedate = date("Ymd");

$requete  = "INSERT INTO equipe VALUES('','','','','','','$mail','','','$kinder','','','','','','','','','','','','','','','','','','','','','','','','','$pass2','pas','','1','0','$thedate')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

$message  = "Salut ma couille !\n\n";
$message .= "Tu as la chance, l'honneur et le privil�ge de faire parti des -=K1der=-\n";
$message .= "Voici tes infos pour te logguer sur le site:\n\n";
$message .= "Login: $kinder";
$message .= "\nPass: $pass2 (tu peut le changer sur le site)";
$message .= "\n\nVoila, oubli pas de passer r�guli�rement sur le channel #k1der sur Quakenet pour rester o courant de tout ce kon pr�voi (matches, LANs, etc...)";
$message .= "\n\nA bient�t sur http://www.k1der.net";

mail("$mail","Bienvenue chez les -=K1der=-","$message","From: k1der_bot@k1der.net\nReply-To: k1der_bot@k1der.net");


?>
<script language="Javascript">
alert('Membre ajout� avec Succ�s, un mail lui a �t� envoy�');
window.location='../index.php?page=admin';
</script>
