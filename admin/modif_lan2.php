<?
include "secu.php";?><?

function effacer($fichier) {
  if (file_exists($fichier)) {
    chmod($fichier,0777);
    if (is_dir($fichier)) {
	  $REP = opendir($fichier);
	  while($entree = readdir($REP))
	  {
	  if ($entree != "." && $entree != "..")
	  {
	  unlink($fichier."/".$entree);
	  }
	  }
	  closedir($REP);
      rmdir($fichier);
	}
    else unlink($fichier);
  }
}

function diff_date($jour2 , $mois2 , $an2, $dur){ 
  $timestamp2 = mktime(0, 0, 0, $mois2, $jour2, $an2); 
  $timestamp = floor( $timestamp2 + ($dur * 3600 * 24)); 
  return $timestamp; 
}

require("../config.inc.php3");

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

$ville = addslashes($ville);
$infos = addslashes($infos);

$caca = diff_date($jour, $mois, $annee, $dur-1);
$caca = date("Ymd", $caca);
$hohoho = $caca;

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

//le 2ème truc

if (($k1der == 'oui') && ($prevk1der != 'oui'))
{
$requete  = "INSERT INTO lan_party VALUES('','$nom','$jour','$mois2','$annee','$ville','$site','','$orderdate')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

$requete  = "SELECT * FROM lan_party ORDER BY id DESC";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$disp = mysql_fetch_array($req);

mkdir("../lan/$disp[id]", 0777);
mkdir("../lan/$disp[id]/photos", 0777);
mkdir("../lan/$disp[id]/fichiers", 0777);

include "allthelan.php";

$cucu = "_".$disp[id]."_";
$date666 = date("U");

$requete3  = "INSERT INTO ib_topics VALUES ('', '$nom', '', 'open','0', '0','$date666', '0','$date666', 0, 'Site -=K1der=-', 'Site -=K1der=-','0','0','0','4','1','0','_$disp[id]_', '', '','0');";
$req3 = mysql_query($requete3) or die('Erreur SQL !<br>'.$requete3.'<br>'.mysql_error());
$req666 = MYSQL_QUERY("SELECT * FROM ib_topics ORDER BY tid DESC LIMIT 0, 1");
$disp666 = mysql_fetch_array($req666);

$allthelan = addslashes($allthelan);

$requete2  = "INSERT INTO ib_posts VALUES('0','','','0','Site -=K1der=-','0','1','127.0.0.1','$date666','','$allthelan','0','$disp666[tid]','4','','_$disp[id]_','','','','1','')";

$req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='4'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]+1;

mysql_query("UPDATE ib_forums SET last_title='$nom' ,topics='$topics' ,last_post='$date666' , last_poster_name='Site -=K1der=-' WHERE id='$disp[id]'");

}
else if (($k1der == 'oui') && ($prevk1der == 'oui'))
{
mysql_query("UPDATE lan_party SET nom='$nom', jour='$jour', mois='$mois2', annee='$annee', loc='$ville', url='$site', orderdate='$orderdate' WHERE id='$id_lan'");

include "allthelan.php";

$cucu = "_".$id_lan."_";

$requete3  = "UPDATE ib_topics SET title='$nom' WHERE pinned='$cucu' && forum_id='4'";
mysql_query($requete3) or die('Erreur SQL !<br>'.$requete3.'<br>'.mysql_error());
$allthelan = addslashes($allthelan);
$req666  = "UPDATE ib_posts SET post='$allthelan' WHERE attach_hits='$cucu' && forum_id='4'";
mysql_query($req666) or die('Erreur SQL !<br>'.$req666.'<br>'.mysql_error());

}
else if (($k1der != 'oui') && ($prevk1der == 'oui'))
{
mysql_query("DELETE FROM lan_party WHERE id='$id_lan'");

$repertoire = "../lan/$id_lan/fichiers";
effacer($repertoire);
$repertoire = "../lan/$id_lan/photos/piti";
effacer($repertoire);
$repertoire = "../lan/$id_lan/photos";
effacer($repertoire);
$repertoire = "../lan/$id_lan";
effacer($repertoire);

$caca = "_".$id_lan."_";

$req = MYSQL_QUERY("SELECT * FROM ib_topics WHERE pinned='$caca' && forum_id='4'");
$disp = mysql_fetch_array($req);

mysql_query("DELETE FROM ib_topics WHERE pinned='$caca' && forum_id='4'");

mysql_query("DELETE FROM ib_posts WHERE attach_hits='$caca' && topic_id='$disp[tid]'");

$req = MYSQL_QUERY("SELECT * FROM ib_forums WHERE id='4'");
$disp = mysql_fetch_array($req);
$topics = $disp[topics]-1;

mysql_query("UPDATE ib_forums SET topics='$topics' WHERE id='$disp[id]'");

}

$nom = addslashes($nom);
$orderdate = addslashes($orderdate);
$hohoho = addslashes($hohoho);
$dur = addslashes($dur);
$ville = addslashes($ville);
$adresse= addslashes($adresse);
$dep = addslashes($dep);
$site = addslashes($site);
$mail = addslashes($mail);
$places = addslashes($places);
$prix = addslashes($prix);
$tournois1= addslashes($tournois1);

$tournois2 = addslashes($tournois2);
$tournois3 = addslashes($tournois3);
$tournois4 = addslashes($tournois4);
$tournois5 = addslashes($tournois5);
$tournois6 = addslashes($tournois6);
$tournois7 = addslashes($tournois7);
$tournois8 = addslashes($tournois8);
$lots = addslashes($lots);
$infos = addslashes($infos);
$k1der = addslashes($k1der);

$rqt = Mysql_Query("UPDATE calendrier SET nom='$nom', debut='$orderdate', fin='$hohoho', dur='$dur', ville='$ville' ,adresse='$adresse', dep='$dep', site='$site', mail='$mail', places='$places', prix='$prix', tournois1='$tournois1', tournois2='$tournois2', tournois3='$tournois3', tournois4='$tournois4', tournois5='$tournois5', tournois6='$tournois6', tournois7='$tournois7', tournois8='$tournois8', lots='$lots', infos='$infos', k1der='$k1der' WHERE id='$id'");

if(!$rqt)
{
 $ALERT = "Une erreur s\'est produite, la LAN n\'a pas été mise à jour !";
}
else
{
 $ALERT = "La LAN a été mise à jour avec succès !";
}
?>

<script language="Javascript">
alert('<? echo $ALERT ?>');
window.location='../index.php?page=admin';
</script>