<?php
// Nettoye le texte recherch (ne garde que les caractères alphanumriques)
$_GET['q']=eregi_replace('[^a-z0-9]','',$_GET['q']);

// Si il ne reste plus rien, on stop tout
if(empty($_GET['q'])) exit();

// Recherche dans la base
$res=$sql->query('SELECT pseudo FROM mod_membres WHERE pseudo LIKE "'.$_GET['q'].'%" ORDER BY pseudo LIMIT 0,10');

// Si aucun rsultat, on stop tout
if($sql->numRows($res)==0) exit();

// Boucle d'affichage des rsultats
while($info=$sql->fetchAssoc($res)) {
?>
	<a class="livesearch" href="javascript:choosePseudo('<?php echo $info['pseudo']; ?>');"><?php echo $info['pseudo']; ?></a>
<?php
}
exit();
?>