<?php
// Nettoye le texte recherch (ne garde que les caractères alphanumriques)
//$_GET['q']=eregi_replace('','',$_GET['q']);

// Si il ne reste plus rien, on stop tout
if(empty($_GET['q'])) exit();

// Recherche dans la base
$res=$sql->query('SELECT nom,date_debut,date_fin FROM mod_lanparty WHERE nom LIKE "'.$site->clear4Sql($_GET['q']).'%" ORDER BY nom LIMIT 0,10');

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
	<a class="livesearch" href="javascript:chooseLan('<?php echo $info['nom']."',".date('j',$info['date_debut']).",".date('n',$info['date_debut']).",".date('Y',$info['date_debut']).",".date('j',$info['date_fin']).",".date('n',$info['date_fin']).",".date('Y',$info['date_fin']); ?>)"><?php echo $info['nom']; ?></a>
<?php
}
exit();
?>