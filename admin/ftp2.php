<?
include "../config.inc.php3";
$source = "ftp_temp/";

//echo $text;


//$text = str_replace("\n","",$text);
//$text = trim($text);
$text = stripslashes($text);

//$text = strip_tags($text);

// ID du message, nom de fichier.
//$index = ($nbmes + 1);
$nomf = "tmp.txt";

// Creer le fichier.
$nfichier = fopen("ftp_temp/tmp.txt", "w");
fclose($nfichier);

// Enregistrer le texte.
$nfichier = fopen("ftp_temp/tmp.txt", "w+");
fputs($nfichier, "$text");
fclose($nfichier);

//ftp_copy("ftp_temp/tmp.txt","ftp://".$ftp_user_name.":".$ftp_user_pass."@".$ftp_server."/".$thefichier);


// Création de la connexion
$conn_id = ftp_connect("$ftp_server");

// Authentification avec nom de compte et mot de passe
$login_result = ftp_login($conn_id, "$ftp_user_name", "$ftp_user_pass");

// Vérification de la connexion
if ((!$conn_id) || (!$login_result)) {
     //   echo "La connexion FTP a échoué!";
    //    echo "Tentative de connexion à $ftp_server avec $ftp_user_name";
     //   die;
    } else {
     //   echo "Connecté à $ftp_server, avec $ftp_user_name";
    }
ftp_pasv ($conn_id,1);

ftp_put($conn_id,$thefichier,"ftp_temp/tmp.txt",FTP_BINARY);
//ftp_delete ($conn_id,$thefichier);

// Fermeture de la connexion FTP.
ftp_quit($conn_id);
unlink("ftp_temp/tmp.txt");

//$file = fopen("ftp://".$ftp_user_name.":".$ftp_user_pass."@".$ftp_server."/".$thefichier, "w+");
//fwrite($file, $text);
//fclose($file);

?>
<script language="Javascript">
alert('Fichier mis à jour avec succès');
window.location='../index.php?page=ger_server';
</script>