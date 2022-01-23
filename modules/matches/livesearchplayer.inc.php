<?php
// Nettoye le texte recherch (ne garde que les caractères alphanumriques)
//$_GET['q']=eregi_replace('','',$_GET['q']);

// Si il ne reste plus rien, on stop tout
if(empty($_GET['q'])) exit();
// Recherche dans la base
$res=$sql->query('SELECT lineup2 FROM mod_matches WHERE lineup2 LIKE "%:\"'.$site->clear4Sql($_GET['q']).'%\";%" ORDER BY lineup2 LIMIT 0,10');
$res2=$sql->query('SELECT pseudo FROM mod_membres WHERE pseudo LIKE "'.$site->clear4Sql($_GET['q']).'%" ORDER BY pseudo LIMIT 0,10');

// Si aucun rsultat, on stop tout
if(($sql->numRows($res)+$sql->numRows($res2))==0) exit();

// Sinon, on crit le style CSS
// NB : Le mettre ailleurs peut tre ?
?>
<style type="text/css">
a.livesearch:link,a.livesearch:visited,a.livesearch:hover,a.livesearch:active {
	display:block;
	padding:3px;
}
a.livesearch:link,a.livesearch:visited {
	color:#000;
	text-decoration:none;
}
a.livesearch:hover,a.livesearch:active {
	background-color:#36C;
	color:#FFF;
	text-decoration:none;
}

</style>
<?php
$liste=array();
// Boucle des adversaires
while($info=$sql->fetchAssoc($res)) {
	$pseudos=unserialize($info['lineup2']);
	foreach($pseudos as $var) if(eregi("^".$_GET['q'],$var)) $liste[]=$var;
}
// Boucles des membres
while($info=$sql->fetchAssoc($res2)) {
	$liste[]=$info['pseudo'];
}
$liste=array_unique($liste);
// Boucle d'affichage des rsultats
foreach($liste as $var) {
?>
	<a class="livesearch" href="javascript:choosePlayer('<?php echo $_GET['id']; ?>','<?php echo $var; ?>');"><?php echo $var; ?></a>
<?php
}
exit();
?>