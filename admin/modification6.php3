<?
include "secu.php";?><script language="Javascript">
function SuppComment()
 {
  result = confirm('Voulez-vous ré-initialiser les résultats du sondage ?');
  if(result==1)
    {
     window.location='poll2.php';
    }
    else
    {
     alert('Résultats du sondage conservés');
	 window.location='../index.php?page=admin';
    }
 }

function SearchComment()
{
 window.open("admin/search_comment.php3","","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=1,resizable=1,width=500,height=400,left=0,top=0");
}
</script>
<?
$description = str_replace("
", "<br>", $description);
$jeux = str_replace("
", "<br>", $jeux);
$competitions = str_replace("
", "<br>", $competitions);
$lan = str_replace("
", "<br>", $lan);
$joueurs = str_replace("
", "<br>", $joueurs);
$phrases = str_replace("
", "<br>", $phrases);
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$titre       = addslashes(stripslashes(trim($titre)));

$rqt = Mysql_Query("UPDATE poll SET valeur='$titre' WHERE nom='titre'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l'inscription à échouée !";
}
else
{
 $ALERT = "Inscription réalisée avec succès !";
}

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$r1       = addslashes(stripslashes(trim($r1)));

$rqt = Mysql_Query("UPDATE poll SET valeur='$r1' WHERE nom='r1'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l'inscription à échouée !";
}
else
{
 $ALERT = "Inscription réalisée avec succès !";
}

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$r2       = addslashes(stripslashes(trim($r2)));

$rqt = Mysql_Query("UPDATE poll SET valeur='$r2' WHERE nom='r2'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l'inscription à échouée !";
}
else
{
 $ALERT = "Inscription réalisée avec succès !";
}

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$r3       = addslashes(stripslashes(trim($r3)));

$rqt = Mysql_Query("UPDATE poll SET valeur='$r3' WHERE nom='r3'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l'inscription à échouée !";
}
else
{
 $ALERT = "Inscription réalisée avec succès !";
}

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$r4       = addslashes(stripslashes(trim($r4)));

$rqt = Mysql_Query("UPDATE poll SET valeur='$r4' WHERE nom='r4'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, l'inscription à échouée !";
}
else
{
 $ALERT = "Inscription réalisée avec succès !";
}

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$r5       = addslashes(stripslashes(trim($r5)));

$rqt = Mysql_Query("UPDATE poll SET valeur='$r5' WHERE nom='r5'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la mise à jour à échouée !";
}
else
{
 $ALERT = "Sondage mis à jour avec succès !";
}
include "poll.php";
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
SuppComment();
</script>
