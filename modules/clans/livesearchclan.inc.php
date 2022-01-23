<?php
// Nettoye le texte recherch (ne garde que les caractères alphanumriques)
$_GET['q']=eregi_replace('[^a-z0-9 ]','',$_GET['q']);

// Si il ne reste plus rien, on stop tout
if(empty($_GET['q'])) exit();

// Recherche dans la base
$res=$sql->query('SELECT nom FROM mod_membres_clans WHERE nom LIKE "'.$_GET['q'].'%" ORDER BY nom LIMIT 0,10');

// Si aucun rsultat, on stop tout
if($sql->numRows($res)==0) exit();

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
// Boucle d'affichage des rsultats
while($info=$sql->fetchAssoc($res)) {
?>
	<a class="livesearch" href="javascript:chooseClan('<?php echo $info['nom']; ?>');"><?php echo $info['nom']; ?> </a>
<?php
}
exit();
?>