<?php
$date = date("m");
$an = date("Y");

// Avancer de X mois
if(isset($_GET["plus"])) {
	$plus=$_GET["plus"];
	while($plus!=0) {
		$date++;
		if ($date>12) {
			$an++;
			$date="01";
		}
		$plus--;
	}
}
get_lans($date,$an);
function get_lan_infos($lien) {
	$fp = fopen("http://www.lan-fr.com/".$lien,"r"); //lecture du fichier
	$record=0;
	$i=0;
	unset($lan);
	while(!feof($fp)) { //on parcourt toutes les lignes
		$tmp=fgets($fp,4096); // lecture du contenu de la ligne
		if(ereg("titre1_orange",$tmp)) $record=1;
		if(ereg("Vous devez être connecté",$tmp)) $record=0;
		$tmp=strip_tags(str_replace("\n","",$tmp));
		$tmp2=str_replace(" ","",$tmp);
		if($record==1) {
			$texte[$i]=$tmp;
			$i++;
		}
	}
	
	fclose($fp);//on ferme
	$record_tour=0;
	$record_infos=0;
	for($i=1;$i<count($texte);$i++) {
		if($texte[$i]=="Tarifs") $record_tour=0;
		if($texte[$i]=="Site Web") $record_infos=0;
		
		if($i==1) $lan["nom"]=$texte[$i];
		if($i==2) {
			$tab1=array("Du "," au");
			$tab2=array("");
			$tmp=explode(" ",str_replace($tab1,$tab2,$texte[$i]));
			$tmp2=explode("/",$tmp[0]);
			$tmp3=explode("/",$tmp[1]);
			$lan["debut"]=$tmp2[2].$tmp2[1].$tmp2[0];
			$lan["fin"]=$tmp3[2].$tmp3[1].$tmp3[0];
			$lan["dur"]=diff_date($tmp3[0],$tmp3[1],$tmp3[2],$tmp2[0],$tmp2[1],$tmp2[2]);
		}
		if($i==3) {
			$lan["ville"]=eregi_replace("(.*) \((.*)\)","\\1",$texte[$i]);
			$lan["dep"]=eregi_replace("(.*) \((.*)\)","\\2",$texte[$i]);
		}
		if($texte[$i]=="Adresse") $lan["adresse"]=$texte[$i+1];
		if($texte[$i]=="Nombre de places") $lan["places"]=$texte[$i+1];
		if($record_tour==1 && isset($lan["tournois1"])) $lan["tournois1"].=$texte[$i];
		else if($record_tour==1) $lan["tournois1"]=$texte[$i];
		if($record_infos==1 && isset($lan["infos"])) $lan["infos"].=$texte[$i];
		else if($record_infos==1) $lan["infos"]=$texte[$i];
		if($texte[$i]=="Tarifs") $lan["prix"]=$texte[$i+1];
		if($texte[$i]=="Site Web") $lan["site"]=$texte[$i+1];
		
		if($texte[$i]=="Tournois prévus") $record_tour=1;
		if($texte[$i]=="Autres infos") $record_infos=1;
	}
	unset($texte);
	foreach($lan as $i=>$var) $lan[$i]=addslashes($var);
	//foreach($lan as $i=>$var) echo "\$lan[$i]=>$var<br>";
	//echo "<br><br><br>";
	$num=mysql_num_rows(sql("SELECT id FROM calendrier WHERE nom=\"".$lan["nom"]."\" && debut=\"".$lan["debut"]."\""));
	if($num<1) {
		sql("INSERT INTO calendrier (nom,debut,fin,dur,ville,adresse,dep,site,places,prix,tournois1,infos,conf)VALUES (
		\"".$lan["nom"]."\",
		\"".$lan["debut"]."\",
		\"".$lan["fin"]."\",
		\"".$lan["dur"]."\",
		\"".$lan["ville"]."\",
		\"".$lan["adresse"]."\",
		\"".$lan["dep"]."\",
		\"".$lan["site"]."\",
		\"".$lan["places"]."\",
		\"".$lan["prix"]."\",
		\"".$lan["tournois1"]."\",
		\"".$lan["infos"]."\",
		\"1\")
		");
	}
}

function get_lans($date,$an) {
	$fp = fopen("http://www.lan-fr.com/index.php?op=lan&ac=calen&a=".$an."&m=".$date."&region=all","r"); //lecture du fichier
	$texte='';
	while (!feof($fp)) { //on parcourt toutes les lignes
		$texte.= fgets($fp, 4096); // lecture du contenu de la ligne
	}
	
	fclose($fp);//on ferme
	$texte=strtolower($texte);// on passe tout en miniscule 
	//on récupere que ce qui est le meilleur
	$texte=strip_tags($texte,'<title></title><script></script><a></a><head></head><style></style>'); 
	//on trie (saut de ligne, blanc, title, head, style, script, inter lien)
	$texte = str_replace("\n"," ",$texte); 
	$texte = str_replace("&nbsp;"," ",$texte); 
	$texte = preg_replace('`<head.*?/head>`', '', $texte); 
	$texte = preg_replace('`<title.*?/title>`', '', $texte);
	$texte = preg_replace('`<script.*?/script>`', '', $texte); 
	$texte = preg_replace('`<style.*?/style>`', '', $texte); 
	$texte = preg_replace('`/a>.*?<a`', '/a><br><a', $texte);
	$texte = str_replace("  "," ",$texte);
		
	$texte = preg_replace('`&lt;br&gt;`', '<br>', $texte);
	$ligne=explode("<br>",$texte);
	$texte='';
	for($i=0;$i<count($ligne);$i++) {
		if(ereg("ac=detail",$ligne[$i])) $texte.=get_lan_infos(replace($ligne[$i]))."<br/>";
	}
	//echo $texte;
	?>
	<?php
	//$text="<a href=\"index.php?op=lan&ac=detail&id=2171\" class=\"lienclair\" onmouseover=\"poplink('blanalyon for téléthon/bbr /st boonet de mure (69)br /120 places - 3 jours');\" onmouseout=\"killlink()\"> lanalyon ..</a>";
	//echo htmlentities($text)."<br>";
	//echo htmlentities(replace($text));
}

function replace($text) {
	return eregi_replace(
		"<a href=\"([^[:space:]]*[[:alnum:]#?/&=])\" class=\"lien([^[:space:]]*[[:alnum:]])\" onmouseover=\"poplink\(\'(.*)\'\);\" onmouseout=\"killlink\(\)\">(.*)</a>",
		"\\1"
	,$text);

}

function connection() {
	include "../config.inc.php3";
	$db = @mysql_connect($dbhost,$dblogi,$dbpass) or die(mysql_error());
	@mysql_select_db($dbbase,$db) or die(mysql_error());
}

function close() {
	include "../config.inc.php3";
	@mysql_close() or die(mysql_error());
}

//Exécution d'une requete MySql
function sql($requete,$die=1) {
	global $k1der;
	extract($_POST,EXTR_OVERWRITE);
	extract($_GET,EXTR_OVERWRITE);
	connection();
	if ($die==1) $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	else $die = mysql_query($requete) or $sqlerror=mysql_error();
	close();
	$k1der["nb_req"]++;
	if (!empty($sqlerror)) return $sqlerror;
	else return $req;
}

function diff_date($jour , $mois , $an , $jour2 , $mois2 , $an2){ 
if ($an == 1970)
{
$mois = 0;
$jour = 0;
$an = 0;
}

if ($an2 == 1970)
{
$mois2 = 0;
$jour2 = 0;
$an2 = 0;
}
 $timestamp = mktime(0, 0, 0, $mois, $jour, $an); 
  $timestamp2 = mktime(0, 0, 0, $mois2, $jour2, $an2); 
  
  $diff = floor(($timestamp - $timestamp2) / (3600 * 24))+1; 
  return $diff; 
}
?>