<?

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

$nom = addslashes($nom);
$caca = addslashes($caca);
$dur = addslashes($dur);
$adresse = addslashes($adresse);
$dep = addslashes($dep);
$site = addslashes($site);
$adresse = addslashes($adresse);
$mail = addslashes($mail);
$places = addslashes($places);
$prix = addslashes($prix);
$tournois1 = addslashes($tournois1);
$tournois2 = addslashes($tournois2);
$tournois3 = addslashes($tournois3);
$tournois4 = addslashes($tournois4);
$tournois5 = addslashes($tournois5);
$tournois6 = addslashes($tournois6);
$tournois7 = addslashes($tournois7);
$tournois8 = addslashes($tournois8);
$lots = addslashes($lots);
$ville = addslashes($ville);
$infos = addslashes($infos);
$requete2  = "INSERT INTO calendrier VALUES('','$nom','$orderdate','$caca','$dur','$ville','$adresse','$dep','$site','$mail','$places','$prix','$tournois1','$tournois2','$tournois3','$tournois4','$tournois5','$tournois6','$tournois7','$tournois8','$lots','$infos','','0')";
$req2 = mysql_query($requete2) or die('Erreur SQL !<br/>'.$requete2.'<br/>'.mysql_error());
?>
<script language="Javascript">
alert('LAN ajoutée avec Succès\n Elle Apparaitera dès quelle aura été confirmée par un Admin');
window.location='index.php';
</script>
