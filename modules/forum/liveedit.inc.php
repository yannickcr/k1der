<?php
/**
 * Vérification des accès (si le post appartient bien au visiteur ou si il est admin)
 */
$info=$sql->fetchAssoc($sql->query('SELECT auteur_id,forum_id FROM mod_forum_posts WHERE id="'.$_POST['id'].'"'));
if($membres->infos('id')!=$info['auteur_id'] && !$membres->verifAcces('forum_'.$info['forum_id'].'_edit')) exit();

// Encodage
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);

/**
 * Action 1 : Récupération du message dans la base de donne
 */
if(isset($_POST['id']) && !isset($_POST['texte'])) {
	$res=$sql->query('SELECT post FROM mod_forum_posts WHERE id="'.$_POST['id'].'"');
	$info=$sql->fetchAssoc($res);
	$texte=$string->unhtmlentities($info['post']);
	$texte=str_replace('&euro;','',$texte);
	echo $texte;

/**
 * Action 2 : Placement du message modifi dans la base de donne
 */
} else if(isset($_POST['id']) && isset($_POST['texte'])) {
	include 'include/librairies/bbcode.class.php';
	$bbcode= new BBCode();
	$texte=$_POST['texte'];
	$texte=str_replace(array('€','%26'),array('','&'),$texte);
	$texte=$site->clear4Sql($texte,false,false,true);
	if(!empty($texte)) {
		$sql->query('UPDATE mod_forum_posts SET post="'.$texte.'" WHERE id="'.$_POST['id'].'"');
		$texte=$bbcode->BBCodeToHtml(stripslashes($texte));
	} else {
		$res=$sql->query('SELECT * FROM mod_forum_posts WHERE id="'.$_POST['id'].'"');
		$info=$sql->fetchAssoc($res);
		$texte=$bbcode->BBCodeToHtml($info['post']);
	}
	echo $texte;
}
exit(); // On arrte de chargement de la page
?>