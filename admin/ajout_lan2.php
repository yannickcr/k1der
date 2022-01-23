<? include "secu.php";

function diff_date($jour2 , $mois2 , $an2, $dur){ 
  $timestamp2 = mktime(0, 0, 0, $mois2, $jour2, $an2); 
  $timestamp = floor( $timestamp2 + ($dur * 3600 * 24)); 
  return $timestamp; 
}

require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
$mois2 = $mois;

if ($mois =="Janvier")
	{
	$mois = '01';
	}
if ($mois =="Février")
	{
	$mois = '02';
	}
if ($mois =="Mars")
	{
	$mois = '03';
	}
if ($mois =="Avril")
	{
	$mois = '04';
	}
if ($mois =="Mai")
	{
	$mois = '05';
	}
if ($mois =="Juin")
	{
	$mois = '06';
	}
if ($mois =="Juillet")
	{
	$mois = '07';
	}
if ($mois =="Août")
	{
	$mois = '08';
	}
if ($mois =="Septembre")
	{
	$mois = '09';
	}
if ($mois =="Octobre")
	{
	$mois = '10';
	}
if ($mois =="Novembre")
	{
	$mois = '11';
	}
if ($mois =="Décembre")
	{
	$mois = '12';
	}

$caca = diff_date($jour, $mois, $annee, $dur-1);
$caca = date("Ymd", $caca);

$res = strlen("$jour");
if ($res == '1')
{
$jour2 = "0$jour";
}
else
{
$jour2 = "$jour";
}

$orderdate = "$annee$mois$jour2";

//le 2ème truc

if ($k1der == 'oui')
{
$requete  = "INSERT INTO lan_party VALUES('','$nom','$jour','$mois2','$annee','$ville','$site','','$orderdate')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

$requete  = "SELECT * FROM lan_party ORDER BY id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

mkdir("../lan/$disp[id]", 0777);
mkdir("../lan/$disp[id]/photos", 0777);
mkdir("../lan/$disp[id]/fichiers", 0777);

$date666 = date("U");

include "allthelan.php";

$requete3  = "INSERT INTO ib_topics VALUES ('', '$nom', '', 'open','0', '0','$date666', '0','$date666', 0, 'Site -=K1der=-', 'Site -=K1der=-','0','0','0','4','1','0','_$disp[id]_', '', '','0');";
$req3 = mysql_query($requete3) or die('Erreur SQL !<br>'.$requete3.'<br>'.mysql_error());
$req666 = MYSQL_QUERY("SELECT * FROM ib_topics ORDER BY tid DESC LIMIT 0, 1");
$disp666 = mysql_fetch_array($req666);

$requete2  = "INSERT INTO ib_posts VALUES('0','','','0','Site -=K1der=-','0','1','127.0.0.1','$date666','','$allthelan','0','$disp666[tid]','4','','_$disp[id]_','','','','1','')";

$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='4'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]+1;

mysql_query("UPDATE ib_forums SET last_title='$nom' ,topics='$topics' ,last_post='$date666' , last_poster_name='Site -=K1der=-' WHERE id='$disp[id]'");

}

$requete2  = "INSERT INTO calendrier VALUES('','$nom','$orderdate','$caca','$dur','$ville','$adresse','$dep','$site','$mail','$places','$prix','$tournois1','$tournois2','$tournois3','$tournois4','$tournois5','$tournois6','$tournois7','$tournois8','$lots','$infos','$k1der','1')";
$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());
?>
<script language="Javascript">
alert('LAN ajoutée avec Succès');
window.location='../index.php?page=admin';
</script>
