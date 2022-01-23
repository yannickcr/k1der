<?php ob_start("ob_gzhandler");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once("inc/functions.php");

$nbr = vg_get('nbr');
$texte = vg_post('texte');
$dans = vg_post('dans');
$id = vg_get('id');
$la_lan = vg_get('la_lan');
$nbr_photos = vg_get('nbr_photos');
$start = vg_get('start');
$type = vg_get('type');
$lordre = vg_get('lordre');
$p = vg_get('p');
$pseudo = vg_post('pseudo');
$txt = vg_post('txt');
$Submit = vg_post('Submit');
$login = vg_post('login');
$pass = vg_post('pass');
$HTTP_COOKIE_VARS[gen] = vg_cookie('gen');

function date2timestamp($date,$format){
// Paramètres : 
   //    $date : date formatt&eacute;e comme renvoie date()
   //    $format : format de la date similire au paramètre de date()
/* exemple : date2timestamp("2001-07-11 16:00:00","Y-m-d h:i:s");
retourne 994860000
*/


   //jour
   $d = "([0-3][0-9])";
   $j = "([1-3]?[0-9])";
   // mois
   $m = "(0[0-9]|1[0-2])";
   $n = "([0-9]|1[0-2])";
   $F = "(January|February|March|April|May|June|July|August|September|October|November|December)";
   $M = "(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)";
   //annee
   $Y = "([0-9]{4,4})";
   $y = "([0-9]{2,2})";
   //heures
   $g = "([1]?[0-9])";
   $G = "([0-2]?[0-9])";
   $h = "([01][0-9])";
   $H = "([0-2][0-9])";
   //minutes
   $i = "([0-5][0-9])";
   //secondes
   $s = "([0-5][0-9])";
   
   $z = "([0-3]?[0-9]?[0-9])";
   $I = "[01]" ;
   
   $lesmois = array('January'=>1,'February'=>2,'March'=>3,'April'=>4,'May'=>5,'June'=>6,
   'July'=>7,'August'=>8,'September'=>9,'October'=>10,'November'=>11,'December'=>12,
   'Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,'Jul'=>7,'Aug'=>8,'Sep'=>9,
   'Oct'=>10,'Nov'=>11,'Dec'=>12);
   
   $ok = array('M','F','I','d','j','m','n','y','Y','g','G','h','H','i','s','z');
   $nok = array('a','A','L','B','D','S','t','T','w','Z');

   $form_m = preg_replace("/([\(\)\[\]\{\}\?\.\*\?\$\^\/\\\\])/","\\\\$1",$format);
   $len = strlen($form_m);
   $form="";
   for($count=0;$count<$len;$count++)
      {
      $chr = substr($form_m,$count,1);
      if ($chr == '\\' || substr($form,-1,1) == '\\')
         {
         $form .= substr($form_m,$count,2);
         $count++;
         continue;
         }
      if (in_array($chr,$ok))
         $form .= $$chr; 
      else      
         if (in_array($chr,$nok))
            $form .= ".+"; 
         else
            if ($chr == 'r')
               $form .= ", $d $M $Y $H:$i:$s [-+][0-9]{4,4}";
            else
               $form .= $chr;
      }
   
   $format = preg_replace("/(^|[^\\\\])(r)/","$1, d M Y H:i:s",$format);
   $form = preg_replace("/\\\\\\\\([a-zA-Z])/","$1",$form);
   preg_match("/$form/",$date,$reg);
  
   $len = strlen($format);
   $pos = 1;
   $annee = $mois = $jour = 0;

   for($count=0;$count<$len;$count++)
      {
      $chr = substr($format,$count,1);
      if ($chr == '\\')
         {
         $count++;
         continue;
         }
      if ($chr == 'd' || $chr == 'j')
         $jour = $reg[$pos++];
      if ($chr == 'm' || $chr == 'n')
         $mois = $reg[$pos++];
      if ($chr == 'M' || $chr == 'F')
         $mois = $lesmois[$reg[$pos++]];
      if ($chr == 'y'|| $chr == 'Y')
         $annee = $reg[$pos++];
      if ($chr == 'g' || $chr == 'h'||$chr == 'G' || $chr == 'H')
         $heure = $reg[$pos++];
      if ($chr == 'i')
         $min = $reg[$pos++];
      if ($chr == 's' || $chr == 'z')
         $sec = $reg[$pos++];
      if ($chr == 'I')
         $dst = $reg[$pos++];
      }

   if ($jour == 0)
      return "Pas de jour specifie";
   if ($mois == 0)
      return "Pas de mois specifie";
   if ($annee == 0)
      return "Pas d'annee specifiee";
   if (!isset($heure))
      $heure=0;
   if (!isset($min))
      $min=0;
   if (!isset($sec))
      $sec=0;
   if (!isset($dst))
      $dst=-1;
   $timestamp = mktime($heure, $min, $sec, $mois, $jour, $annee, $dst);
   return $timestamp;
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
  
  $diff = floor(($timestamp - $timestamp2) / (3600 * 24)); 
  return $diff; 
}

require_once("config.inc.php3");
$titropage = "-=K1der=- The Chocolat Effect || Clan Counter-Strike de Bretagne";
$page_a_inclure = "news.php";
switch(vg_get('page'))
{
case "news":
	$page_a_inclure = "news.php";
	break;
case "idee":
	$page_a_inclure = "idee.php";
	break;
case "irc":
	$page_a_inclure = "irc.php";
	break;
case "web_irc":
	$page_a_inclure = "web_irc.php";
	break;
case "je_suis_trop_con":
	$page_a_inclure = "pass.php";
	break;
case "team":
	$page_a_inclure = "equipe.php";
	break;
case "stats":
	$page_a_inclure = "stats.htm";
	break;
case "galerie":
	$page_a_inclure = "galerie.htm";
	break;
case "matches":
	$page_a_inclure = "matches.php";
	break;
case "phrases":
	$page_a_inclure = "phrases.htm";
	break;
case "dessins":
	$page_a_inclure = "dessins_view.php";
	break;
case "download":
	$page_a_inclure = "download.php";
	break;
case "sons":
	$page_a_inclure = "sons.htm";
	break;
case "recrutement":
	$page_a_inclure = "recrut.php";
	break;
case "forum":
	$page_a_inclure = "forum.htm";
	break;
case "chat":
	$page_a_inclure = "chat.htm";
	break;
case "archives":
	$page_a_inclure = "archives.php3";
	break;
case "admin":
	$page_a_inclure = "admin/index.php";
	break;
case "read_comment":
	$page_a_inclure = "read_comment.php3";
	break;
case "lan_photos":
	$page_a_inclure = "photos_lan.php";
	break;
case "ajout_lien":
	$page_a_inclure = "admin/aj_lien.php";
	break;
case "liens_list":
	$page_a_inclure = "admin/lien_liste.php";
	break;
case "k1der":
	$page_a_inclure = "k1der.htm";
	break;
case "dessins_gal":
	$page_a_inclure = "dessins_gal.php";
	break;
case "dessins_list":
	$page_a_inclure = "admin/dessins_list.php";
	break;
case "dessins_view":
	$page_a_inclure = "dessins_view.php";
	break;
case "ajout_phrase":
	$page_a_inclure = "admin/aj_phrase.php";
	break;
case "phrases_list":
	$page_a_inclure = "admin/phrases_liste.php";
	break;
case "ajout_dessin":
	$page_a_inclure = "admin/ajout_dessin.php";
	break;
case "del_comment":
	$page_a_inclure = "admin/del_comment.php";
	break;
case "ajoutnews":
	$page_a_inclure = "admin/ajout_news.php";
	break;
case "ajoutverif":
	$page_a_inclure = "admin/ajouter_verif.php3";
	break;
case "modiflistnews":
	$page_a_inclure = "admin/modifier_liste.php3";
	break;
case "del_comment":
	$page_a_inclure = "admin/delete_comment.php3";
	break;
case "activeuser":
	$page_a_inclure = "admin/activeuser.php";
	break;	
case "modifplayer":
	$page_a_inclure = "admin/modif_joueur.php";
	break;
case "modifpass":
	$page_a_inclure = "admin/modifier3.php3";
	break;
case "modiflan":
	$page_a_inclure = "admin/modifier4.php3";
	break;
case "modiflanstatut":
	$page_a_inclure = "admin/modifier5.php3";
	break;
case "modifpoll":
	$page_a_inclure = "admin/modifier6.php3";
	break;
case "add_photos":
	$page_a_inclure = "lan_photos.php";
	break;
case "up_photos":
	$page_a_inclure = "upload_photos.php";
	break;
case "matches_details":
	$page_a_inclure = "matches_details.php";
	break;
case "next_matches_details":
	$page_a_inclure = "next_matches_details.php";
	break;
case "ajout_matche":
	$page_a_inclure = "admin/ajout_matche.php";
	break;
case "ajout_next_matche":
	$page_a_inclure = "admin/ajout_next_matche.php";
	break;
case "matches_liste":
	$page_a_inclure = "admin/matches_liste.php";
	break;
case "next_matches_liste":
	$page_a_inclure = "admin/next_matches_liste.php";
	break;
case "modif_matche":
	$page_a_inclure = "admin/modif_matche.php";
	break;
case "modif_next_matche":
	$page_a_inclure = "admin/modif_next_matche.php";
	break;
case "lan_details":
	$page_a_inclure = "lan_details.php";
	break;
case "aj_cat":
	$page_a_inclure = "admin/aj_cat.php";
	break;
case "cat_liste":
	$page_a_inclure = "admin/cat_liste.php";
	break;
case "aj_down":
	$page_a_inclure = "admin/aj_down.php";
	break;
case "down_liste":
	$page_a_inclure = "admin/down_liste.php";
	break;
case "ajout_lan":
	$page_a_inclure = "admin/ajout_lan.php";
	break;
case "lan_liste":
	$page_a_inclure = "admin/lan_liste.php";
	break;
case "modif_lan":
	$page_a_inclure = "admin/modif_lan.php";
	break;
case "modif_news":
	$page_a_inclure = "admin/modif_news.php";
	break;
case "partenaires":
	$page_a_inclure = "partenaires.php";
	break;
case "conf_lien":
	$page_a_inclure = "admin/lien_liste_noconf.php";
	break;
case "prop_news":
	$page_a_inclure = "prop_news.php";
	break;
case "conf_news":
	$page_a_inclure = "admin/conf_news.php";
	break;
case "visu_news":
	$page_a_inclure = "admin/visu_news.php";
	break;
case "dessins_desc_list":
	$page_a_inclure = "admin/dessins_desc_list.php";
	break;
case "dessins_suppr_list":
	$page_a_inclure = "admin/dessins_suppr_list.php";
	break;
case "dessins_upload":
	$page_a_inclure = "admin/uploader.php";
	break;
case "ajouter_lan":
	$page_a_inclure = "cal/ajout_lan.php";
	break;
case "conf_lan":
	$page_a_inclure = "admin/conf_lan.php";
	break;
case "ajout_membre":
	$page_a_inclure = "admin/ajout_membre.php";
	break;
case "membres_liste":
	$page_a_inclure = "admin/membres_liste.php";
	break;
case "mailing":
	$page_a_inclure = "admin/mailing.php";
	break;
case "aj_dossier":
	$page_a_inclure = "admin/ajout_dossier.php";
	break;
case "modif_dossier":
	$page_a_inclure = "admin/modif_dossier.php";
	break;
case "dossiers_liste":
	$page_a_inclure = "admin/dossiers_liste.php";
	break;
case "dossiers":
	$page_a_inclure = "dossiers.php";
	break;
case "dossier":
	$page_a_inclure = "dossier.php";
	break;
case "down_comments":
	$page_a_inclure = "download_comments.php";
	break;
case "search":
	$page_a_inclure = "search.php";
	break;
case "config_upload":
	$page_a_inclure = "admin/uploader2.php";
	break;
case "ajout_event":
	$page_a_inclure = "admin/aj_event.php";
	break;
case "event_liste":
	$page_a_inclure = "admin/event_liste.php";
	break;
case "modif_event":
	$page_a_inclure = "admin/modif_event.php";
	break;
case "ger_dossiers":
	$page_a_inclure = "admin/ger_dossiers.php";
	break;
case "ger_dossier":
	$page_a_inclure = "admin/ger_dossier.php";
	break;
case "modif2_dossier":
	$page_a_inclure = "admin/modif2_dossier.php";
	break;
case "dossier_comments":
	$page_a_inclure = "dossier_comments.php";
	break;
case "admin_pages":
	$page_a_inclure = "admin/admin_pages.php";
	break;
case "admin_pages_levels":
	$page_a_inclure = "admin/admin_pages_levels.php";
	break;
case "modif_cat_admin":
	$page_a_inclure = "admin/modif_cat.php";
	break;
case "modif_page_admin":
	$page_a_inclure = "admin/modif_page.php";
	break;
case "membres_liste_levels":
	$page_a_inclure = "admin/membres_liste_levels.php";
	break;
case "ger_recrut":
	$page_a_inclure = "admin/ger_recrut.php";
	break;
case "visu_recrut":
	$page_a_inclure = "admin/visu_recrut.php";
	break;
case "visu_details_recrut":
	$page_a_inclure = "admin/visu_details_recrut.php";
	break;
case "visu_membres_levels":
	$page_a_inclure = "admin/visu_membres_levels.php";
	break;
case "ajout_matchew3":
	$page_a_inclure = "admin/ajout_matchew3.php";
	break;
case "matches_war3_details":
	$page_a_inclure = "matches_war3_details.php";
	break;
case "server":
	$page_a_inclure = "server.php";
	break;
case "ger_server":
	$page_a_inclure = "admin/ger_server.php";
	break;
case "ger_server_ftp":
	$page_a_inclure = "admin/ftp.php";
	break;
case "ger_server_aj_fichier":
	$page_a_inclure = "admin/ger_server_aj_fichier.php";
	break;
case "ger_server_list_fichier":
	$page_a_inclure = "admin/ger_server_list_fichier.php";
	break;
case "ger_server_modif_fichier":
	$page_a_inclure = "admin/ger_server_modif_fichier.php";
	break;
case "sondage":
	$page_a_inclure = "sondage.php";
	break;
case "aj_sondage":
	$page_a_inclure = "admin/aj_sondage.php";
	break;
case "modif_sondage":
	$page_a_inclure = "admin/modif_sondage.php";
	break;
case "sondage_liste":
	$page_a_inclure = "admin/sondage_liste.php";
	break;
case "ger_ep":
	$page_a_inclure = "admin/ger_ep.php";
	break;
case "matches_stats":
	$page_a_inclure = "matches_stats.php";
	break;
case "aj_anniv":
	$page_a_inclure = "admin/aj_anniv.php";
	break;
case "modif_anniv":
	$page_a_inclure = "admin/modif_anniv.php";
	break;
case "anniv_liste":
	$page_a_inclure = "admin/anniv_liste.php";
	break;
case "defier":
	$page_a_inclure = "defier.php";
	break;
case "defi_details":
	$page_a_inclure = "admin/defi_details.php";
	break;
case "create_mail":
	$page_a_inclure = "admin/create_mail.php";
	break;
case "suggest":
	$page_a_inclure = "suggest.php";
	break;
}

//$nom_page = $page_a_inclure; // optionnel, c'est pour sp&eacute;cifier le nom de la page
//require "stat/visiteur.php";
echo "<title>$titropage</title>";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name='title' content='-=K1der=- The Chocolat Effect'/>
<meta name='description' content='Site officiel du clan -=K1der=-'/>
<meta name='keywords' content='k1der, kinder, k1nder, clan, team, equipe, playmobil, lego, cs, hl, half-life, half, life, counter-strike, counter, strike, bzh, breton, bretagne, lan, poils, chocolat, country, surprise, bueno, maxi, pingui, sex, hack, warez, divx, mp3, lan, party, quimper, blobby, volley, multiplayer, multijoueur'/>
<meta name='author' content='Country'/>
<meta http-equiv='content-language' content='fr'/>
<meta name='copyright' content='Team K1der 2001-2003'/>
<meta name='robots' content='all'/>
<meta name='revisit-after' content='7 days'/>
<meta name='identifier-url' content='http://www.k1der.net'/>
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<meta name="DC.Language" scheme="RFC3066" content="fr-FR" />
<meta name="DC.Title" xml:lang="fr" content="-=K1der=- The Chocolat Effect" />
<meta name="DC.Date.created" scheme="W3CDTF" content="2001-05-16" />
<meta name="DC.Date.modified" scheme="W3CDTF" content="<?php echo date('Y-m-d'); ?>" />
<meta name="DC.Publisher" content="Clan K1der" />
<meta name="DC.Creator" content="Country" />
<meta name="DC.Description" xml:lang="fr" content="Site officiel du clan -=K1der=-" />
<meta name="DC.Subject" xml:lang="fr" content="k1der, kinder, k1nder, clan, team, equipe, playmobil, lego, cs, hl, half-life, half, life, counter-strike, counter, strike, bzh, breton, bretagne, lan, poils, chocolat, country, surprise, bueno, maxi, pingui, sex, hack, warez, divx, mp3, lan, party, quimper, blobby, volley, multiplayer, multijoueur" />
<meta name="DC.Audience" content="Joueurs de bretagne" />
<link rel="alternate" type="application/rss+xml" title="-=K1der=- The Chocolat Effect" href="http://www.k1der.net/rss.php" />
<link rel="shortcut icon" href="http://www.k1der.net/favicon.ico"/>
<link rel="stylesheet" type="text/css" href="styles.css" media="screen" />
<script language="javascript" src="scripts.js" type="text/javascript"></script>
<?
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
	  
$req = MYSQL_QUERY("SELECT * FROM config WHERE nom='theme'");
$disp = mysql_fetch_array($req);
$theme = "$disp[valeur]";
?>
<script type="text/javascript" language="javascript" src="http://www.k1der.net/phpstats/php-stats.js.php"></script>
</head>
<body>
<span id="dsl">Ce site n'est actuellement pas aux normes du W3C. Cependant une nouvelle version est prévue pour cet été. Cette dernière sera valide XHTML 1.1 et WCAG 1.0. Pour plus d'informations vous pouvez me contacter par email à country arobase k1der point net .</span>
<div id="bulle"></div>
<noscript><img src="http://www.k1der.net/phpstats/php-stats.php" border="0" alt="" /></noscript>
<table width="950" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td height="30" colspan="6"> <table width="950" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="217"></td>
          <td width="366"><img src="images/titre3<? echo $theme; ?>.gif" alt="" /></td>
          <td valign="bottom" width="116"><img src="images/titre4<? echo $theme; ?>.gif" alt="" /></td>
          <td valign="bottom" width="140"><img src="images/titre5<? echo $theme; ?>.gif" alt="" /></td>
          <td valign="bottom" width="111"><img src="images/titre6<? echo $theme; ?>.gif" alt="" /></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="93" colspan="6"><img src="images/titre2<? echo $theme; ?>.gif" width="950" height="95" alt="" /></td>
  </tr>
  <tr> 
    <td height="2" style="background-image:url(images/fond.gif);text-align:center;" width="950" colspan="6"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 

      <?
	  
	  $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
	  mysql_select_db("$dbbase",$db) or Die("Base Down !");
	  
	  $req = MYSQL_QUERY("SELECT * FROM phrases");
	  $res = MYSQL_NUM_ROWS($req);
	  $nbre =mysql_num_rows($req);
	  $rnd = rand (0,$nbre-1);
	  
	  $req = MYSQL_QUERY("SELECT * FROM phrases limit $rnd,1");
	  $res = MYSQL_NUM_ROWS($req);
	  $nbre =mysql_num_rows($req);
	  $disp = mysql_fetch_array($req);
	  ?>
	  
	<?=$disp[phrase];?>

      </font></td>
  </tr>
  <tr> 
    <td width="170" height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
    <td height="20" style="background-image:url(images/fond.gif);" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><img src="images/coin.gif" width="20" height="20" alt="" /></font></td>

    <td width="570" rowspan="2">    <?
	$cookie = vg_cookie('gen');
	if ($cookie)
	{
	?><table border="0" align="center" cellpadding="0" cellspacing="0">
        <?
	if ($HTTP_COOKIE_VARS[gen] != '')
	{
	?>
        <tr> 
          <td width="570"><div align="center"> <a href="index.php?page=admin" onMouseOver="Message('Accès à l\'administration','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/admin.gif" width="16" height="16" border="0" align="middle" alt="Accès à l'administration" /></a> 
              <?
		$req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf='0'");
		$nbre =mysql_num_rows($req);
		if ($nbre == '0') { ?>
              &nbsp;&nbsp;<a href="index.php?page=ajoutnews" onMouseOver="Message('Ajouter une News','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/news.gif" width="16" height="16" border="0" align="middle" alt="Ajouter une News" /></a> 
              <? } else {	?>
              &nbsp;&nbsp;<a href="index.php?page=conf_news" onMouseOver="Message('Confirmer les News (<b><font color=red><? echo $nbre; ?></font></b> en attente)','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/news.gif" width="16" height="16" border="0" align="middle" alt="Confirmer les News (<? echo $nbre; ?> en attente)" /></a> 
              <? }
		$req = MYSQL_QUERY("SELECT * FROM calendrier WHERE conf='0'");
		$nbre =mysql_num_rows($req);
		if ($nbre == '0') { ?>
              &nbsp;&nbsp;<a href="index.php?page=ajout_lan" onMouseOver="Message('Ajouter une LAN','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/lan.gif" width="16" height="16" border="0" align="middle" alt="Ajouter une LAN" /></a> 
              <? } else {	?>
              &nbsp;&nbsp;<a href="index.php?page=conf_lan" onMouseOver="Message('Confirmer les LAN Party (<b><font color=red><? echo $nbre; ?></font></b> en attente)','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/lan.gif" width="16" height="16" border="0" align="middle" alt="Confirmer les LAN Party (<? echo $nbre; ?> en attente)" /></a> 
              <? } ?>
              &nbsp;&nbsp;<a href="index.php?page=ajout_matche" onMouseOver="Message('Ajouter un Match','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/match.gif" width="16" height="16" border="0" align="middle" alt="Ajouter un Match" /></a> 
              &nbsp;&nbsp;<a href="index.php?page=ger_dossiers" onMouseOver="Message('Ajouter un Dossier','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/dossiers.gif" width="16" height="16" border="0" align="middle" alt="Ajouter un Dossier" /></a> 
              &nbsp;&nbsp;<a href="index.php?page=ajout_event" onMouseOver="Message('Ajouter un evènement','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/event.gif" width="16" height="16" border="0" align="middle" alt="Ajouter un evènement" /></a> 
              &nbsp;&nbsp;<a href="index.php?page=ajout_phrase" onMouseOver="Message('Ajouter une Phrase','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/phrases.gif" width="16" height="16" border="0" align="middle" alt="Ajouter une Phrase" /></a> 
              <?
		$req = MYSQL_QUERY("SELECT * FROM liens WHERE conf='0'");
		$nbre =mysql_num_rows($req);
		if ($nbre == '0') { ?>
              &nbsp;&nbsp;<a href="index.php?page=ajout_lien" onMouseOver="Message('Ajouter un lien','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/liens.gif" width="16" height="16" border="0" align="middle" alt="Ajouter un lien" /></a> 
              <? } else {	?>
              &nbsp;&nbsp;<a href="index.php?page=conf_lien" onMouseOver="Message('Confirmer les liens (<b><font color=red><? echo $nbre; ?></font></b> en attente)','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/liens.gif" width="16" height="16" border="0" align="middle" alt="Confirmer les liens (<? echo $nbre; ?> en attente)" /></a> 
              <? } ?>
              &nbsp;&nbsp;<a href="forum/index.php?act=SF&f=12" target="_blank" onMouseOver="Message('Accès à la partie admin du Forum','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/forum.gif" width="16" height="16" border="0" align="middle" alt="Accès à la partie admin du Forum" /></a> 
              <?
		$req = MYSQL_QUERY("SELECT * FROM recrutement WHERE lu='0'");
		$nbre =mysql_num_rows($req);
		if ($nbre == '0') { ?>
              &nbsp;&nbsp;<a href="index.php?page=visu_recrut" onMouseOver="Message('Voir les demandes de recrutement','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/gen.gif" width="16" height="16" border="0" align="middle" alt="Voir les demandes de recrutement" /></a> 
              <? } else {	?>
              &nbsp;&nbsp;<a href="index.php?page=visu_recrut" onMouseOver="Message('Voir les demandes de recrutement (<b><font color=red><? echo $nbre; ?></font></b> en attente)','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/gen.gif" width="16" height="16" border="0" align="middle"></a> 
              <? } ?>
              &nbsp;&nbsp;<a target="_blank" href="phpstats/admin.php" onMouseOver="Message('Voir les stats du site','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/stats.gif" width="16" height="16" border="0" align="middle" alt="Voir les stats du site" /></a> 
              &nbsp;&nbsp;<a href="index.php?page=modifplayer" onMouseOver="Message('Modifier ma fiche de joueur','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/info.gif" width="16" height="16" border="0" align="middle" alt="Modifier ma fiche de joueur" /></a> 
              &nbsp;&nbsp;<a href="index.php?page=admin&logout=1" onMouseOver="Message('D&eacute;connection','desc')" onMouseOut="Message2('','desc')"><img src="images/icones/deco.gif" width="16" height="16" border="0" align="middle" alt="D&eacute;connection" /></a></div></td>
        </tr>
        <?
	}
	else
	{
	?>
        <tr>
          <td width="570"></td>
        </tr>
        <?
	}
	?>
        <?
	if ($HTTP_COOKIE_VARS[gen] != '')
	{
	  $req = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$HTTP_COOKIE_VARS[gen]'");
	  $disp = mysql_fetch_array($req);
	  if ($disp[statut] == 'pas')
	  {
	  $contenu .= "
	  <font size=\"1\">Participe à la prochaine LAN : </font>
	  <select name='menu1' onchange=MM_jumpMenu('parent',this,0) style='font-size:9; font-family: Verdana, Arial, Helvetica, sans-serif;'>
      <option value='' selected>Je sais pas</option>
      <option value='admin/true2.php'>Oui</option\>
      <option value='admin/false2.php'>Non</option>
      </select>";
	  }
	  
	  $roq = MYSQL_QUERY("SELECT * FROM next_matches");
	  $nbro =mysql_num_rows($roq);

	  
	  if (($disp[next_match] == '') && ($nbro != 0))
	  {
	  $contenu .= "
	  <font size=\"1\">Participe au prochain Match : </font>
	  <select name='menu2' onchange=MM_jumpMenu('parent',this,0) style='font-size:9; font-family: Verdana, Arial, Helvetica, sans-serif;'>
	  <option value='' selected>Je sais pas</option>
	  <option value='admin/true.php'>Oui</option\>
	  <option value='admin/false.php'>Non</option>
	  </select>";
	  }
	?>
        <tr> 
          <td width="570" id="ejs_texte"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
            <div align="center" id="desc">&nbsp;</div>
            </font></td>
        </tr></table>
        <?
	  $req = MYSQL_QUERY("SELECT * FROM equipe WHERE kinder='$HTTP_COOKIE_VARS[gen]'");
	  $disp = mysql_fetch_array($req);
	  if ($disp[statut] == 'pas')
	  {
	  $contenu .= "
	  <font size=\"1\">Participe à la prochaine LAN : </font>
	  <select name='menu1' onchange=MM_jumpMenu('parent',this,0) style='font-size:9; font-family: Verdana, Arial, Helvetica, sans-serif;'>
      <option value='' selected>Je sais pas</option>
      <option value='admin/true2.php'>Oui</option\>
      <option value='admin/false2.php'>Non</option>
      </select>";
	  }
	  
	  $roq = MYSQL_QUERY("SELECT * FROM next_matches");
	  $nbro =mysql_num_rows($roq);

	  
	  if (($disp[next_match] == '') && ($nbro != 0))
	  {
	  $contenu .= "
	  <font size=\"1\">Participe au prochain Match : </font>
	  <select name='menu2' onchange=MM_jumpMenu('parent',this,0) style='font-size:9; font-family: Verdana, Arial, Helvetica, sans-serif;'>
	  <option value='' selected>Je sais pas</option>
	  <option value='admin/true.php'>Oui</option\>
	  <option value='admin/false.php'>Non</option>
	  </select>";
	  }
	}
	}
	?></td>
    <td height="20" style="background-image:url(images/fond.gif);" ><img src="images/coin3.gif" width="20" height="20" align="middle" alt="" /></td>
    <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
  </tr>
  <tr> 
    <td width="170" height="20" style="background-image:url(images/fond.gif);" > 
      <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><font color="#FFFFFF"><strong>Rechercher</strong></font></font></div></td>
    <td width="20" height="20"> <div align="right"></div></td>
    <td width="20" height="20"> <div align="center"></div></td>
    <td width="170" height="20" style="background-image:url(images/fond.gif);" > <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Dernier 
        Dessin</b></font></div></td>
  </tr>
  <tr> 
    <td colspan="6"> <table width="950" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td valign="top" width="170"> <table width="170" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td valign="bottom" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></font></td>
                <td style="background-image:url(images/fond.gif);" > <form method="post" name="rechform" id="rechform" action="index.php?page=search" onsubmit="return rech()">
                    <div align="center"> 
                      <input name="texte" type="text" id="texte" size="17" />
                      <br/>
                      <font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">dans 
                      </font> 
                      <select name="dans" id="dans">
                        <option value="News">News</option>
                        <option value="LAN">LAN</option>
                        <option value="Matches">Matches</option>
                        <option value="Forum">Forum</option>
                      </select>
                      <br/>
                      <input type="submit" name="Submit" value="Ok" />
                    </div>
                  </form></td>
                <td valign="bottom" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr> 
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);" > <div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Menu</strong></font></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" ><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=news"><font color="#FFFFFF">News</font></a></font></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond.gif);" > <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="index.php?page=lan_photos"><font color="#FFFFFF">LAN 
                    Party</font></a></font></div></td>
              </tr>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond.gif);" > <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="cal/index.php"><font color="#FFFFFF">Calendrier 
                    des LANs</font></a></font></div></td>
              </tr>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond.gif);" > <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=phrases"><font color="#FFFFFF">Phrases 
                    &agrave; la Con</font></a></font></div></td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" ><div align="center"><a href="index.php?page=dossiers"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Dossiers</font></a></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp; </td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="index.php?page=dessins"><font color="#FFFFFF">Dessins</font></a></font></div></td>
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp; </td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp; </td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="index.php?page=download"><font color="#FFFFFF">Download</font></a></font></div></td>
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp; </td>
              </tr>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond.gif);" > <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="index.php?page=recrutement"><font color="#FFFFFF">Recrutement</font></a></font></div></td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp; </td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="forum/" target="_blank"><font color="#FFFFFF">Forum</font></a></font></div></td>
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp; </td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td width="130" height="20" style="background-image:url(images/fond.gif);" > <div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=irc"><font color="#FFFFFF">IRC</font></a></font></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" > <div align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></font></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130"> <div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.ircstat.net/stat/quakenet/k1der.html" target="_blank"><font color="#FFFFFF">Stats 
                    IRC</font></a></font></div></td>
                <td width="20" height="20" style="background-image:url(images/fond.gif);" ><div align="left"> 
                    <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></font></div>
                  </div></td>
              </tr>
              <tr> 
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond_cs.gif);"><table width="170" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="20"><div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></div></td>
                    <td width="130"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Section CS</strong></font></div></td>
                    <td width="20"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
                  </tr>
                  <tr>
                    <td colspan="3">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3"><div align="center"><a href="index.php?page=team&amp;section=cs"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">&Eacute;quipe</font></a></div></td>
                  </tr>
                  <tr>
                    <td colspan="3"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=matches"><font color="#ffffff">Matches</font></a></font></div></td>
                  </tr>
                  <tr>
                    <td colspan="3"><div align="center"><a href="index.php?page=download&amp;type=cs"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Download</font></a></div></td>
                  </tr>
                  <tr>
                    <td colspan="3">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3">
					<?php
					include_once 'clteamstats.php'; 
					?>
					<div style="
					position:relative;
					height:10px;
					width:150px;
					background-image:url(images/cyberleagues.gif);
					background-repeat:no-repeat;
					background-position:top center;
					font-family:Verdana, Arial, Helvetica, sans-serif;
					color:#FFFFFF;
					font-weight:bold;
					font-size:1.1em;
					text-align:right;
					padding:35px 10px 0px 0px;
					"><?php echo $team['TeamPosition']; ?><span style="font-size:0.7em;">&egrave;me</span></div></td>
                  </tr>
                  <tr>
                    <td width="20"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                    <td width="130">&nbsp;</td>
                    <td width="20"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
                  </tr>
                </table>
                <table width="170" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td bgcolor="#FFFFFF">&nbsp;</td>
                      <td bgcolor="#FFFFFF">&nbsp;</td>
                      <td bgcolor="#FFFFFF">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td width="20"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></div></td>
                      <td width="130"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Serveur</strong></font></div></td>
                      <td width="20"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td colspan="3"><div align="center"><? include "live/csstatus.php";?></div></td>
                    </tr>
                    <tr> 
                      <td width="20"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                      <td width="130">&nbsp;</td>
                      <td width="20"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="7" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="7" style="background-image:url(images/fond.gif);" width="130"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Prochaine 
                    </b></font></div></td>
                <td height="7" style="background-image:url(images/fond.gif);" width="20"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr> 
                <td height="20" width="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" width="130" style="background-image:url(images/fond.gif);" > <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>LAN 
                    Party </b></font></div></td>
                <td height="20" width="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr> 
                <td colspan="3" height="7" style="background-image:url(images/fond.gif);" > <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"> 
                    <?
						
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
$date = date("Ymd");

$requete  = "SELECT * FROM calendrier WHERE debut>='$date' && k1der ='oui' ORDER by debut";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$disp = mysql_fetch_array($req);
$nbrplop =mysql_num_rows($req);
if ($nbrplop != 0)
{
$ruquete  = "SELECT * FROM config WHERE nom = 'prev_lan'";
$ruq = mysql_query($ruquete) or die('Erreur SQL !<br/>'.$ruquete.'<br/>'.mysql_error());  
$dusp = mysql_fetch_array($ruq);

if ("$disp[nom]" != "$dusp[valeur]")
{
mysql_query("UPDATE equipe SET statut='pas'");
mysql_query("UPDATE config SET valeur='$disp[nom]' WHERE nom='prev_lan'");
}

$date = date2timestamp("$disp[debut]", "Ymd");

$jour = date("d", $date);
$mois = date("m", $date);
$an = date("Y", $date);

$jour2 = date("d");
$mois2 = date("m");
$an2 = date("Y");

$nbre = diff_date($jour , $mois , $an , $jour2 , $mois2 , $an2);
if(!ereg("http://",$disp[site])) $disp[site]="http://".$disp[site];
if ($nbre == 2)
{
echo "<center>Plus que 2 jours avant la<br/><a target=\"_blank\" href=\"$disp[site]\"><font color=\"#ffffff\">$disp[nom]</font></a></center>";
}
else if ($nbre == 1)
{
echo "<center>Demain y a la<br/><a target=\"_blank\" href=\"$disp[site]\"><font color=\"#ffffff\">$disp[nom]</font></a> !</center>";
}
else if ($nbre == 0)
{
echo "<center>On est à la<br/><a target=\"_blank\" href=\"$disp[site]\"><font color=\"#ffffff\">$disp[nom]</font></a> !</center>";
}
else
{
echo "<center>$nbre jours avant la<br/><a target=\"_blank\" href=\"$disp[site]\"><font color=\"#ffffff\">$disp[nom]</font></a></center>";
}
}
else
{
echo "Aucune LAN de Pr&eacute;vue<br/><img src=\"shoutbox/smileys/sad.gif\" alt=\"\" />";
mysql_query("UPDATE equipe SET statut='non'");
}
?>
                    </font> </div></td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130">&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" width="20"> 
                </td>
              </tr>
                  <?
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");
$requete  = "SELECT * FROM equipe ORDER BY kinder";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
while($disp = mysql_fetch_array($req))
{
?>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" > <font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
                  </font></td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130"> <font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
                  <?
if ($disp[statut] == "oui")
	{
		$ico = "images/oui.gif";
	}
elseif ($disp[statut] == "non")
	{
		$ico = "images/non.gif";
	}
elseif ($disp[statut] == "pas")
	{
		$ico = "images/pasur.gif";
	}
?>
                  <img src="<? echo $ico ?>" width="7" height="7" alt="" /> <font color="#FFFFFF"><a href="index.php?page=team&amp;id=<? echo $disp[id]; ?>#player"><font color="#FFFFFF"><? echo $disp[kinder]; ?></font></a></font></font></td>
                <td height="20" style="background-image:url(images/fond.gif);" width="20">&nbsp;</td>
              </tr>
              <?
			  }
			  ?>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130">&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" width="20">&nbsp;</td>
              </tr>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond.gif);" > <div align="center"><font size="1" color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/oui.gif" width="7" height="7" alt="" /> 
                    Vient <img src="images/non.gif" width="7" height="7" alt="" /> Vient 
                    pas<br/>
                    <img src="images/pasur.gif" width="7" height="7" alt="" /> Sait pas</font></div></td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130">&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" width="20"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr> 
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);" > <div align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Forum 
                    Live </font></strong></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" > <table style="border:1px solid #000000" cellspacing="0" cellpadding="0" width="100%">
                    <tr> 
                                          <td style="border-color:#000000;" bgcolor="#FFFFFF"> <table cellspacing="1" cellpadding="2" width="100%" bgcolor="#CCCCCC" border="0">
      <?
				if ($HTTP_COOKIE_VARS[gen] != '')
				{				
				$ruquete  = "SELECT * FROM ib_topics ORDER BY last_post DESC LIMIT 0,5";
				}
				else
				{
				$ruquete  = "SELECT * FROM ib_topics WHERE forum_id != '26' && forum_id != '12' && forum_id != '14' && forum_id != '15' && forum_id != '16' && forum_id != '17' && forum_id != '18' && forum_id != '19' && forum_id != '20' && forum_id != '21' && forum_id != '22' ORDER BY last_post DESC LIMIT 0,5";
				}
				$ruq = mysql_query($ruquete) or die('Erreur SQL !<br/>'.$ruquete.'<br/>'.mysql_error()); 
				while($disp = mysql_fetch_array($ruq))
				{
				if ($bgcolor == "#FFFFFF")
				{
				$bgcolor = "#F7F7F7";
				}
				else
				{
				$bgcolor = "#FFFFFF";
				}				
				
				$roquete  = "SELECT * FROM ib_forums WHERE id='$disp[forum_id]'";
				$roq = mysql_query($roquete) or die('Erreur SQL !<br/>'.$roquete.'<br/>'.mysql_error()); 
				$dosp = mysql_fetch_array($roq);
				$fauxrum = $dosp[name];


				if ($disp[last_poster_id] == '0')
				{
				if ($disp[starter_id] == '0')
				{
				$hydi = "<font color=\"#000000\">$disp[last_poster_name]</font> <font color=\"#999999\">($disp[posts])</font>";
				}
				else
				{
				$hydi = "<a target=\"_blank\" href=\"forum/index.php?act=Profile&amp;CODE=03&amp;MID=$disp[starter_id]\"><font color=\"#000000\">$disp[last_poster_name]</font></a> <font color=\"#999999\">($disp[posts])</font>";
				}
				}
				else
				{
				$hydi = "<a target=\"_blank\" href=\"forum/index.php?act=Profile&amp;CODE=03&amp;MID=$disp[last_poster_id]\"><font color=\"#000000\">$disp[last_poster_name]</font></a> <font color=\"#999999\">($disp[posts])</font>";
				}
				echo "<tr><td style=\"border-color:#ffffff;background-color:$bgcolor;\"><font face='Verdana, Arial, Helvetica, sans-serif' size='1'><b>$fauxrum:<br/><a target=\"_blank\" href=\"forum/index.php?act=ST&amp;f=1&amp;t=$disp[tid]&amp;view=getnewpost\">$disp[title]</a></b><br/> par <b>$hydi</b></font><br/><br/></td></tr>";
				}
				?>
                        </table></td>
                    </tr>
                  </table></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
			 <?
function wpTeamStats($teamid,&$stats) {
	// Liste des champs
	$tags=array(
		"TeamName",
		"TeamDescription",
		"TeamMembers",
		"TeamKeys",
		"TeamClicks",
		"TeamRank",
		"TeamDateFormed",
		"TeamFounder",
		"GeneratedTime"
	);
	// Initialisation du parseur et lecture du fichier XML
	$data=@implode("",@file("http://whatpulse.org/api/teams/".$teamid.".xml"));
	$parser=xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parse_into_struct($parser,$data,$values,$index);
	xml_parser_free($parser);
	for ($i=0;$i<sizeof($tags);$i++) $stats[$tags[$i]]=$values[$index[$tags[$i]][0]]['value'];
}

// Exemple d'utilisation

wpTeamStats(748,$team);

?> 
              <tr>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" style="background-image:url(images/fond.gif);"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);"><div align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">WhatPulse</font></strong></div></td>
                <td height="20" style="background-image:url(images/fond.gif);"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr>
                <td height="20" colspan="3" style="padding-left:3px;padding-right:3px;background-image:url(images/fond.gif);">
				  <font color="#FFFFFF" size="1" face="Verdana, Arial, Helvetica, sans-serif">
				  <?
				echo "<br/>Stats de la team <b>".$team['TeamName']."</b> :<br/>";
				echo "Nous avons <b>".$team['TeamMembers']."</b> membres.<br/>";
				echo "Pour un total de :<br/><b>".$team['TeamKeys']."</b> touches pressées<br/>";
				echo "<b>".$team['TeamClicks']."</b> clics de souris.<br/>";
				echo "<br/>Classement: <b>".$team['TeamRank']."</b>èmes<br/>";
				echo "<br/>Stats d&eacute;taill&eacute;es : <b><a target=\"_blank\" href=\"http://whatpulse.org/stats/teams/748/\"><font color=\"#ffffff\">ici</font></a></b>";
				?>
			      </font></td>
              </tr>
              <tr>
                <td height="20" style="background-image:url(images/fond.gif);"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);">&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
                <td height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></font></td>
                <td height="20" style="background-image:url(images/fond.gif);" width="130"> <div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Liens</b></font></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" width="20"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></font></td>
              </tr>
              <tr> 
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td width="130" height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
            </table>
            <table width="170" border="0" cellspacing="0" cellpadding="0">
              <?
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM liens";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$nbre =mysql_num_rows($req);
$i = 1;
while($disp = mysql_fetch_array($req))
{
if ($disp[image] != "")
{
?>
              <?
		}
		else
		{
		?>
              <?
		}
		}
		?>
              <tr> 
                <td height="20" colspan="3" style="background-image:url(images/fond.gif);" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <?
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM liens");
$res = MYSQL_NUM_ROWS($req);
$nbre =mysql_num_rows($req);
$rnd = rand (0,$nbre-1);

$requete  = "SELECT * FROM liens WHERE conf = '1' limit $rnd,1";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$nbre =mysql_num_rows($req);
$i = 1;
while($disp = mysql_fetch_array($req))
{
if ($disp[image] != "")
{
if(ereg(".swf",$disp[image]))
{
?>
                    <tr> 
                      <td colspan="3">
						  <div align="center"> 
							<object type="application/x-shockwave-flash" data="<? echo $disp[image]; ?>" width="88" height="31">
									<param name="movie" value="<? echo $disp[image]; ?>" />
							</object>
						  </div>
					  </td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <?
}
else
{
?>
                    <tr> 
                      <td colspan="3"> <div align="center"><a href="<? echo $disp[lien]; ?>" target="_blank"><img src="<? echo $disp[image]; ?>" border="0" alt="<? echo $disp[nom]; ?>" /></a></div></td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <?
}
		}
		else
		{
		?>
                    <tr> 
                      <td colspan="3"><div align="center"><a class="type2" href="<? echo $disp[lien]; ?>" target="_blank"><font color="#ffffff" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[nom]; ?></font></a></div></td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <?
		}
		}
		?>
                    <tr> 
                      <td colspan="3"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.defrag-france.net" target="_blank"><img border="0" src="images/defrag.gif" alt="Defrag France" /></a></font></div></td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td colspan="3"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=partenaires"><font color="#FFFFFF">Nos Partenaires</font></a></font></div></td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td colspan="3"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Et 
                          Nous:</font></div></td>
                    </tr>
                    <tr> 
                      <td height="15" colspan="3"><div align="center">
						<object type="application/x-shockwave-flash" data="http://www.k1der.net/images/lienk1der.swf" width="120" height="40">
								<param name="movie" value="http://www.k1der.net/images/lienk1der.swf" />
						</object>
					  </div></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td style="background-image:url(images/fond.gif);">&nbsp;</td>
                <td width="130" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td style="background-image:url(images/fond.gif);" width="20"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td style="background-image:url(images/fond.gif);" width="130">&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
            </table></td>
          <td height="20" valign="top" colspan="3"> 
            <?
		  require($page_a_inclure);
		  ?>
          </td>
          <td height="40" valign="top" width="170"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="170"><table width="170" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td style="background-image:url(images/fond.gif);" width="20">&nbsp;</td>
                      <?
$requete  = "SELECT * FROM config WHERE nom = 'image'";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$disp = mysql_fetch_array($req);

$lien = str_replace("/piti/","&amp;image=",$disp[valeur]);

function faire_piti2($rep_pitis, $nom_image_photo) {
  include "config.photos_lan.php";
  @set_time_limit(86400); // pour eviter que sa mette une erreur de temps
  $salut = "$rep_pitis$nom_image_photo";
  $nopiti = str_replace("/piti/","/", $salut);
  $chemin_image = $nopiti;
  $chemin_piti = $salut;
  if(is_file($chemin_image)) {
    $gd_image = ImageCreateFromJPEG($chemin_image);				// on cree une image GD 
    $data = GetImageSize($chemin_image);					// on recupere la taille de l'image
    $largeur_photo = $data[0];
    $hauteur_photo = $data[1];
	if ($largeur_photo > $hauteur_photo)
	{
	$coeff_reduc = $largeur_photo / $hauteur_max;				// cacul du coefficient de reduction
    $largeur_piti = $largeur_photo / $coeff_reduc;				// calcul de la largeur du piti
    $hauteur_piti = $hauteur_photo / $coeff_reduc;				// calcul de la hauteur du piti
	}
	else
	{
    $coeff_reduc = $largeur_max / $hauteur_photo;				// cacul du coefficient de reduction
    $largeur_piti = $largeur_photo * $coeff_reduc;				// calcul de la largeur du piti
    $hauteur_piti = $hauteur_photo * $coeff_reduc;				// calcul de la hauteur du piti
	}
    $gd_piti = ImageCreateTrueColor($largeur_piti, $hauteur_piti);			// on cree une image vide
    imagecopyresampled($gd_piti, $gd_image, 0,0,0,0, $largeur_piti,$hauteur_piti, $largeur_photo,$hauteur_photo);
    ImageJPEG($gd_piti,$chemin_piti, $qualite_piti);				//ecriture de l'image
    ImageDestroy($gd_image);							// destruction de l'image cree par GD
    Imagedestroy($gd_piti);
  }
}
?>
                      <td width="130" height="98" style="background-image:url(images/fond.gif);" ><div align="center"><a href="dessins.php?rep=images/dessins/<? echo $lien; ?>" target="_blank"> 
                          <?
	  if(!file_exists("images/dessins/$disp[valeur]"))
	  {
	  $fichier2 = strstr ("$disp[valeur]", "/");
	  $fichier2 = str_replace("/piti/","", $fichier2);
	  $fichier = str_replace("$fichier2","", $disp[valeur]);
	  faire_piti2("images/dessins/$fichier", $fichier2);
	  }
	  else
	  {
    $data = GetImageSize("images/dessins/$disp[valeur]");					// on recupere la taille de l'image
    $largeur_photo = $data[0];
    $hauteur_photo = $data[1];
	if ($largeur_photo > "127")
	{
	$old = $largeur_photo;
	$largeur_photo = "127";
	$coef = $old/$largeur_photo;
	$hauteur_photo = $hauteur_photo/$coef;
	}
	}
	
?>
                          <img src="images/dessins/<? echo $disp[valeur]; ?>" width="<? echo $largeur_photo; ?>" height="<? echo $hauteur_photo; ?>" border="0" alt="" /></a> 
                        </div></td>
                      <td width="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                    </tr>
                    <tr> 
                      <td style="background-image:url(images/fond.gif);" width="20"><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                      <td width="130" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                      <td width="20" style="background-image:url(images/fond.gif);" ><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
                    </tr>
                  </table></td>
              </tr>
            </table>
            <table width="170" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="20" height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="2" style="background-image:url(images/fond.gif);" ><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></td>
                <td style="background-image:url(images/fond.gif);" height="2"> <div align="center"> 
                    <b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Derniers 
                    Matches</font></b></div></td>
                <td style="background-image:url(images/fond.gif);" height="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr align="center"> 
                <td colspan="3" style="background-image:url(images/fond.gif);" height="16"> <table border="1" cellspacing="0" cellpadding="0" style="border:1px solid #000000" width="130">
                    <?
					  $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
					  mysql_select_db("$dbbase",$db) or Die("Base Down !");
					  
					  $requete  = "SELECT * FROM matches order by orderdate DESC, id DESC LIMIT 0,5";
					  $req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
					  while($disp = mysql_fetch_array($req))
					  {
					  ?>
                    <tr> 
                      <td height="10" style="border:0px solid #FFFFFF;text-align:left;" bgcolor="#FFFFFF"><?
					  echo "<font size=\"1\"><b>&nbsp;<a onmouseover=\"
					  affiche('','&lt;font face=\'Verdana, Arial, Helvetica, sans-serif\' size=\'1\'&gt;&lt;b&gt;Score&lt;/b&gt;&lt;br/&gt; $disp[score_k1]/$disp[score_me]&lt;/font&gt;')\" 
					  onmouseout=\"affiche('cache')\" 
					  href='index.php?page=matches_details&amp;id=$disp[id]'><font face='Verdana, Arial, Helvetica, sans-serif' color='#000000'>".htmlentities($disp[mechants])."</font></a></b></font>";
					  if ($disp[score_k1] > $disp[score_me])
					  {
					  $txt = "Win";
					  $back = "#00FF00";
					  $font = "#000000";
					  }
					  else if ($disp[score_k1] < $disp[score_me])
					  {
					  $txt = "Lost";
					  $back = "#FF0000";
					  $font = "#000000";
					  }
					  else
					  {
					  $txt = "Draw";
					  $back = "#0000FF";
					  $font = "#FFFFFF";
					  }
					  ?></td>
                      <td width="30" height="10" style="border:1px solid #FFFFFF;font-weight:bold;text-align:center;" bgcolor="<? echo $back; ?>"> 
                       <font size="1" face="Verdana, Arial, Helvetica, sans-serif" color="<? echo $font; ?>"><? echo $txt; ?></font></div></td>
                      <td height="10" style="border:0px solid #FFFFFF" bgcolor="#FFFFFF"><font size="1">&nbsp;</font></td>
                    </tr>
                    <?
					 }
					  ?>
                  </table></td>
              </tr>
              <tr> 
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20"> <div align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Prochain 
                    Match</font></b></div></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr align="center"> 
                <td colspan="3" style="background-image:url(images/fond.gif);" > <table width="130" style="border:1px solid #000000" cellpadding="0" cellspacing="0">
                    <?
					  $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
					  mysql_select_db("$dbbase",$db) or Die("Base Down !");
					  
					  $requete  = "SELECT * FROM defi ORDER BY orderdate,heure";
					  $req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
					  $nbre =mysql_num_rows($req);
					  $disp = mysql_fetch_array($req);
					  if ($nbre > 1) { $leess = "s"; }
					  if ($nbre == 0) { $nbre = "Aucun"; }
					  if (($HTTP_COOKIE_VARS[gen] != "") && ($nbre != 0)) { $lien1 = "<a href=\"index.php?page=defi_details&amp;id=$disp[id]\">"; $lien2 = "</a>"; }
					  $plop = "<br/><font size=\"1\"><font face='Verdana, Arial, Helvetica, sans-serif' color='#000000'><b>$lien1$nbre d&eacute;fi$leess en attente$lien2</b><br/><br/><a href='index.php?page=defier'><b>Clique ici pour nous d&eacute;fier</b></a></font></font>";
					  
					  $notredate = date("U");
					  mysql_query("DELETE FROM next_matches WHERE date <='$notredate'");
					  
					  $requete  = "SELECT * FROM config WHERE nom = 'prev_match'";
					  $req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
					  $disp = mysql_fetch_array($req);
					  $idmatchavant = $disp[valeur];
					  $requete  = "SELECT * FROM next_matches ORDER BY date LIMIT 0,1";
					  $req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
					  $nbre =mysql_num_rows($req);
					  if ($nbre != 0)
					  {
					  while($disp = mysql_fetch_array($req))
					  {
					  if ($disp[id] != $idmatchavant)
					  {
					  mysql_query("UPDATE equipe SET next_match=''");
					  mysql_query("UPDATE config SET valeur='$disp[id]' WHERE nom='prev_match'");
					  }
					  ?>
                    <tr> 
                      <td bgcolor="#FFFFFF"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <?
						  
 $jour=date("l",$disp[date]); 
  $mois=date("m",$disp[date]); 
  $liste_jour_fr=array( 
  "Monday"=>"Lundi " , 
  "Tuesday"=>"Mardi" , 
  "Wednesday"=>"Mercredi" , 
  "Thursday"=>"Jeudi" , 
  "Friday"=>"Vendredi" , 
  "Saturday"=>"Samedi" , 
  "Sunday"=>"Dimanche" 
  ); 
  
  $liste_mois_fr=array( 
  "01 "=>"janvier" , 
  "02" =>"f&eacute;vrier" , 
  "03" =>"mars" , 
  "04" =>"avril" , 
  "05" =>"mai" , 
  "06" =>"juin" , 
  "07" =>"juillet" , 
  "08" =>"août" , 
  "09" =>"septembre" , 
  "10" =>"octobre" , 
  "11" =>"novembre" , 
  "12" =>"d&eacute;cembre" 
  ); 
  
  foreach($liste_jour_fr as $j_en => $j_fr ) 
  { 
  if($jour==$j_en) 
  { 
  $datefren .= $j_fr; 
  } 
  } 
  
  $datefren .= date(" d",$disp[date]); 
  
  foreach($liste_mois_fr as $m_en => $m_fr ) 
  { 
  if($mois==$m_en) 
  { 
  $datefren .= " $m_fr"; 
  } 
  } 
  //$datefren .= date(" Y");
  //return $datefren; 
						  
						  //$dute = date_en_francais();
						  $dute = $datefren;
						  echo "<font size=\"1\"><a href='index.php?page=next_matches_details&amp;id=$disp[id]'><font face='Verdana, Arial, Helvetica, sans-serif' color='#000000'>Contre les <b>$disp[mechants]</b><br/>le $dute à $disp[heure]</font></a></font>$plop";
						  ?>
                          </font></div></td>
                    </tr>
                    <?
					 }
					 }
					 else
					 {
					 ?>
                    <tr> 
                      <td style="border-color:#ffffff;" bgcolor="#FFFFFF"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <?
					 echo "<font size=\"1\"><font face='Verdana, Arial, Helvetica, sans-serif' color='#000000'>Pas de match pr&eacute;vu</font></font>$plop";
					 ?>
                          </font></div></td>
                    </tr>
                    <?
					 }
					 ?>
                  </table></td>
              </tr>
              <tr> 
                <td width="20" style="background-image:url(images/fond.gif);" ><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" ><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
              </tr>
              <tr> 
                <td height="2" colspan="3"><table  cellpadding="0" cellspacing="0" style="border:1px solid #000000" >
                    <tr> 
                      <td align="center" style="border-color:#ffffff;"> <div align="center"> 
                          <?
						$moa_d = date("m");
						$an_d = date("Y");
						if (strlen($moa) == '1')
						{
						$moa9 = "0".$moa;
						}
						else
						{
						$moa9 = $moa;
						}
						$limage = "images/calendrier/$moa9$ane.jpg";
						$limage2 = "images/calendrier/$moa_d$an_d.jpg";
						if (file_exists($limage))
						{
						?>
                          <img src="images/calendrier/<? echo "$moa9$ane"; ?>.jpg" width="166" height="125" alt="" /> 
                          <?
						  }
						  else if (file_exists($limage2))
						  {
						  ?>
                          <img src="images/calendrier/<? echo "$moa_d$an_d"; ?>.jpg" width="166" height="125" alt="" /> 
                          <?
						  } else {
						  ?>
                          <img src="images/retard.jpg" width="166" height="125" alt="Maxiiiiiii" /> 
                          <?
						  }
						  ?>
						  </div></td>
                    </tr>
                    <tr> 
                      <td align="center" style="border-color:#ffffff;"> 
                        <? include "calendrier.php"; ?>
                        <div align="center"></div></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td width="20" height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" style="background-image:url(images/fond.gif);" height="2"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></td>
                <td width="130" style="background-image:url(images/fond.gif);" height="2"> <div align="center"> 
                    <b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Sondage</font></b></div></td>
                <td width="20" style="background-image:url(images/fond.gif);" height="2"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr align="center"> 
                <td colspan="3" style="background-image:url(images/fond.gif);" height="16"> <table cellspacing="0" cellpadding="0" width="130">
                    <tr> 
                      <td style="border:1px solid #000000" bgcolor="#FFFFFF"> 
                        <? include "poll.php"; ?>
                      </td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td width="20" style="background-image:url(images/fond.gif);" ><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td width="130" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td width="20" style="background-image:url(images/fond.gif);" ><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
                <td height="2">&nbsp;</td>
              </tr>
              <tr> 
                <td height="2" valign="top" style="background-image:url(images/fond.gif);" ><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></td>
                <td width="130" height="2" style="background-image:url(images/fond.gif);" > 
                  <div align="center"> <b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">
                    <?
						  $req = MYSQL_QUERY("SELECT * FROM config WHERE nom='episode'");
						  $disp = mysql_fetch_array($req);
						  echo $disp[valeur];
						  ?>
                    </font></b></div>
</td>
                <td height="2" valign="top" style="background-image:url(images/fond.gif);" ><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr align="center"> 
                <td style="background-image:url(images/fond.gif);" height="16">&nbsp; </td>
                <td width="130" height="16" style="background-image:url(images/fond.gif);" >
<table style="border:1px solid #000000" cellspacing="0" cellpadding="0" width="130">
                    <tr> 
                      <td style="border-color:#000000;" bgcolor="#FFFFFF">
<!-- <div class="maxiabuze"></div> -->
					  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td style="height:19px;"></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;Sc&eacute;nario 
                              :</font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?
						$requete  = "SELECT * FROM config WHERE nom='ep_scen'";
						$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
						$disp = mysql_fetch_array($req);
						$scen = $disp[valeur];
						?><img src="images/ep_1.gif" width="1" height="9" alt="" /><?
						if ($disp[valeur] != 0)
						{
						?><img src="images/ep_2.gif" width="<? echo $disp[valeur]*0.80; ?>" height="9" alt="" /><?
						}
						$val2 = 100 - $disp[valeur];
						if ($val2 != 0)
						{
						?><img src="images/ep_3.gif" width="<? echo $val2*0.80; ?>" height="9" alt="" /><?
						}
						?><img src="images/ep_1.gif" width="1" height="9" alt="" />&nbsp;<? echo $disp[valeur]; ?>%</font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;Story 
                              Board :</font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?
						$requete  = "SELECT * FROM config WHERE nom='ep_story'";
						$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
						$disp = mysql_fetch_array($req);
						$story = $disp[valeur];
						?><img src="images/ep_1.gif" width="1" height="9" alt="" /><?
						if ($disp[valeur] != 0)
						{
						?><img src="images/ep_2.gif" width="<? echo $disp[valeur]*0.80; ?>" height="9" alt="" /><?
						}
						$val2 = 100 - $disp[valeur];
						if ($val2 != 0)
						{
						?><img src="images/ep_3.gif" width="<? echo $val2*0.80; ?>" height="9" alt="" /><?
						}
						?><img src="images/ep_1.gif" width="1" height="9" alt="" />&nbsp;<? echo $disp[valeur]; ?>%</font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;Flash 
                              :</font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?
						$requete  = "SELECT * FROM config WHERE nom='ep_flash'";
						$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
						$disp = mysql_fetch_array($req);
						$flash = $disp[valeur];
						?><img src="images/ep_1.gif" width="1" height="9" alt="" /><?
						if ($disp[valeur] != 0)
						{
						?><img src="images/ep_2.gif" width="<? echo $disp[valeur]*0.80; ?>" height="9" alt="" /><?
						}
						$val2 = 100 - $disp[valeur];
						if ($val2 != 0)
						{
						?><img src="images/ep_3.gif" width="<? echo $val2*0.80; ?>" height="9" alt="" /><?
						}
						?><img src="images/ep_1.gif" width="1" height="9" alt="" />&nbsp;<? echo $disp[valeur]; ?>%</font></td>
                          </tr>
                          <tr> 
                            <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>&nbsp;Avancement 
                              total :</strong></font></td>
                          </tr>
                          <tr> 
                            <td style="text-align:left;"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?
							  $total = round(($scen/20)+($story/6.6666666666666666666666667)+($flash/1.25),0);
						?><img src="images/ep_1.gif" width="1" height="9" alt="" /><?
						if ($total != 0)
						{
						?><img src="images/ep_2.gif" width="<? echo $total*0.80; ?>" height="9" alt="" /><?
						}
						$val2 = 100 - $total;
						if ($val2 != 0)
						{
						?><img src="images/ep_3.gif" width="<? echo $val2*0.80; ?>" height="9" alt="" /><?
						}
						?><img src="images/ep_1.gif" width="1" height="9" alt="" />&nbsp;<? echo $total; ?>%</font></td>
                          </tr>
                          <tr> 
                            <td>&nbsp;</td>
                          </tr>
                          <tr> 
                            <td><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Date 
                                de sortie :<br/>
                                <?
						$requete  = "SELECT * FROM config WHERE nom='ep_date'";
						$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
						$disp = mysql_fetch_array($req);
						$jour = date("d",$disp[valeur]);
						$mois = date("m",$disp[valeur]);
						$an =  date("Y",$disp[valeur]);
						if ($disp[valeur] != 0)
						{
						echo "$jour/$mois/$an";
						}
						else
						{
						echo $disp[valeur];
						}
						$jour2 = date("d");
						$mois2 = date("m");
						$an2 = date("Y");
						
						$nbre = diff_date($jour , $mois , $an , $jour2 , $mois2 , $an2);
						
						?>
                                </strong></font></div></td>
                          </tr>
                          <tr> 
                            <td><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? if ($disp[valeur] != 0) { echo "reste $nbre jour"; } ?><? if ($nbre > 1) { echo "s"; } ?></font></div></td>
                          </tr>
                          <tr> 
                            <td><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong><br/>Déjà <?=diff_date($jour2,$mois2,$an2,25,12,2003);?> jours de retard ! </strong></font></div></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                <td style="background-image:url(images/fond.gif);" height="16">&nbsp;</td>
              </tr>
              <tr> 
                <td style="background-image:url(images/fond.gif);" ><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td width="130" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" ><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td width="20">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" ><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></td>
                <td height="20" style="background-image:url(images/fond.gif);" valign="middle"> 
                  <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"> 
                    <b>Chat'Express</b></font></div></td>
                <td style="background-image:url(images/fond.gif);" height="20"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td rowspan="2" style="background-image:url(images/fond.gif);" > <div align="center"> 
                    <table style="border:1px solid #000000" cellspacing="0" cellpadding="0" width="126">
                      <tr> 
                        <td style="border-color:#000000;" bgcolor="#FFFFFF"><? include "http://www.k1der.net/shoutbox/index.php"; ?></td>
                      </tr>
                    </table>
                    <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> 
                    </font> </div></td>
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" ><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td height="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td style="background-image:url(images/fond.gif);" height="20"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td width="20">&nbsp;</td>
                <td width="130">&nbsp;</td>
                <td width="20">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" style="background-image:url(images/fond.gif);" ><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></td>
                <td width="130" style="background-image:url(images/fond.gif);" > <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Acc&egrave;s 
                    Membres</b></font></div></td>
                <td width="20" style="background-image:url(images/fond.gif);" ><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td width="20" style="background-image:url(images/fond.gif);" >&nbsp;</td>
                <td width="130" style="background-image:url(images/fond.gif);" rowspan="2"> <div align="center"><a href="index.php?page=admin"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><br/>
                    Acc&egrave;der &agrave; l'espace Membres</font></a><br/>
                    <br/>
                  </div></td>
                <td style="background-image:url(images/fond.gif);" >&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" height="20" valign="bottom" style="background-image:url(images/fond.gif);" ><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td style="background-image:url(images/fond.gif);" height="20" valign="bottom"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td width="20">&nbsp;</td>
                <td width="130">&nbsp;</td>
                <td width="20">&nbsp;</td>
              </tr>
              <tr> 
                <td width="20" style="background-image:url(images/fond.gif);" height="20"><img src="images/coinghautgauche.gif" width="20" height="20" alt="" /></td>
                <td height="20" style="background-image:url(images/fond.gif);" valign="middle"> 
                  <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"> 
                    <b>Compteurs </b></font></div></td>
                <td width="20" style="background-image:url(images/fond.gif);" height="20"><img src="images/coinghautdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr> 
                <td colspan="3" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                  </b></font>
                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                      <? require "compt.php"; ?>
</b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">Visiteurs</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?php
include "dv_connect.php";
?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">Connect&eacute;
                          <?
						if ($count > 1)
						{
						echo "s";
						}
						?>
sur le Site</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?
				 // $req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS ORDER BY id DESC");
				 // $res = MYSQL_NUM_ROWS($req);
				  //include "compt_irc.php";
				  $fichier = @fopen("http://searchirc.com/searchirc_chan_stats.php?n=44&c=I2sxZGVy&o=2","r");
				  if ($fichier) {
				  	$buffer = fgets($fichier);
				  	@fclose ($fichier);
					$buffer = explode("Current: ",$buffer);
					$buffer = explode(", ",$buffer[1]);
					echo $buffer[0];
				  } else echo "?";
				  ?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">Connect&eacute;s sur IRC</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?
				  $req = mysql_query("SELECT * FROM mynewsinfos ORDER BY id DESC");
				  $res = mysql_num_rows($req);
				  echo $res;
				  ?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">News</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?
						$nbrdl = 0;
						$reqCOMMENT = mysql_query("SELECT * FROM liens_down");
						$resCOMMENT = mysql_num_rows($reqCOMMENT);
						while($disp2 = mysql_fetch_array($reqCOMMENT))
						{
						$nbrdl = $disp2[taille] + $nbrdl;
						}
						echo $nbrdl;
						?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">T&eacute;l&eacute;chargements</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?
				  $req = @MYSQL_QUERY("SELECT * FROM ib_posts");
				  $res = @MYSQL_NUM_ROWS($req);
				  echo $res;
				  ?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">
                        <?
						/*
						$nbrdl = 0;
						$reqCOMMENT = mysql_query("SELECT * FROM liens_down");
						$resCOMMENT = mysql_num_rows($reqCOMMENT);
						while($disp2 = mysql_fetch_array($reqCOMMENT))
						{
						$reqCOMMENT2 = mysql_query("SELECT * FROM cats_down WHERE id='$disp2[cat]'");
						$disp3 = mysql_fetch_array($reqCOMMENT2);
						if ($disp3[type] == 'k1der')
						{
						$nbrdl = $disp2[taille] + $nbrdl;
						}
						}
						echo "&nbsp;<b>$nbrdl</b> trucs à Maxi<br/>";
						*/
						?>Posts sur le forum</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?
				  $req = MYSQL_QUERY("SELECT * FROM matches ORDER BY id DESC");
				  $res = MYSQL_NUM_ROWS($req);
				  echo $res;
				  ?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">Matches</font></td>
                    </tr>
                    <tr>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b>
                        <?
				  $req = MYSQL_QUERY("SELECT * FROM lan_party ORDER BY id DESC");
				  $res = MYSQL_NUM_ROWS($req);
				  echo $res;
				  ?>
                      </b></font></td>
                      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF">LAN Party</font></td>
                    </tr>
                </table></td></tr>
              <tr> 
                <td width="20" height="20" style="background-image:url(images/fond.gif);" ><img src="images/coingbasgauche.gif" width="20" height="20" alt="" /></td>
                <td height="20" style="background-image:url(images/fond.gif);" ><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
                  </font></td>
                <td style="background-image:url(images/fond.gif);" height="20"><img src="images/coingbasdroite.gif" width="20" height="20" alt="" /></td>
              </tr>
              <tr>
                <td height="20" colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td height="20" colspan="3" align="center"><a href="http://www.k1der.net/rss.php" target="_blank"><img border="0" src="images/rss.gif" alt="RSS News"/></a></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
  <td colspan="2" align="center"> <p align="center"><font face="Verdana" size="1">Webmaster 
      :&nbsp;
	  <script language="JavaScript" type="text/javascript">
	  var un="country";
	  var deux = "k1der.net";
	  var texteCrypte="05204A0E420E22556659262B1B1A1A53";
	  var texteCrypte2="1B7F";
	  var texteCrypte3="056E0B58";
	  document.write(decrypte(texteCrypte)+un+"[AT]"+deux+decrypte(texteCrypte2)+"-=K1der=- Country"+decrypte(texteCrypte3));
	  </script>
	  </font><font face="Verdana" size="1"> Dessins 
      :&nbsp; 
	  <script language="JavaScript" type="text/javascript">
	  var un="maxi";
	  var deux = "k1der.net";
	  var texteCrypte="05204A0E420E22556659262B1B1A1A53";
	  var texteCrypte2="1B7F";
	  var texteCrypte3="056E0B58";
	  document.write(decrypte(texteCrypte)+un+"[AT]"+deux+decrypte(texteCrypte2)+"-=K1der=- Maxi"+decrypte(texteCrypte3));
	  </script>
	  </font><br/>
      <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="1">Copyright &copy; 2001-2004 K1der.net (php, design, html, textes, dessins etc.)<br/>
      Reproduction interdite sans autorisation.<br/>
      Site enregistr&eacute; à la CNIL sous le n°1007991<br/>
      Ce site est pr&eacute;vu pour une r&eacute;solution de 1024x768 sur un navigateur 
      de type 5 ou sup&eacute;rieur<br/>
      Il est recommand&eacute; d'ouvrir les yeux et d'allumer l'&eacute;cran pour 
      b&eacute;n&eacute;ficier du contenu de ce site<br/>
<a href="index.php?page=suggest">Suggestion/Bug</a> | <a href="changelog.txt" target="_blank">changelog</a></font></p></td></tr>
</table>
</body>
</html>
<?php
mysql_close();
ob_end_flush(); ?>