<?php
/*header('Content-Type: text/html; charset='.CHARSET);
$res=$sql->query('SELECT id,titre,descr FROM mod_forum_topics');

echo $sql->numRows($res).'<br /><br /><br />';

$bbcode = new bbcode;


while($info=$sql->fetchAssoc($res)) {
	//echo $info['post'];
	$info['titre']=utf8_decode($string->unhtmlentities($info['titre']));
	$info['descr']=utf8_decode($string->unhtmlentities($info['descr']));
	//echo '<hr />';
	//$info['post']=$bbcode->htmlToBBCode($info['post']);
	//echo $info['post'];
$sql->query('UPDATE mod_forum_topics SET titre="'.$site->clear4SQL($info['titre']).'", descr="'.$site->clear4SQL($info['descr']).'" WHERE id='.$info['id']);
	//echo '<hr />';
//	echo '<hr />';
}
exit();*/
/**
 * 1. Instanciation d'objets
 */

/**
 * 2. Actions
 */

/**
 * 3. Récupération des données
 */

/**
 * 4. Initialisation de la page
 */
$template->setFile('centre','imode/index.html');
$this->noUse(array('header','titre','gauche','droite','bas','footer'));
header('Content-Type: text/html; charset='.CHARSET);
$site->addToTitle(' - iMode');

/**
 * 5. Déclaration des blocs
 */

/**
 * 6. Construction de la page
 */

/**
 * 7. Affichage des erreurs
 */
?>