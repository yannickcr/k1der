<?
/*----------------------------------------
            K1der Shoutbox 1.7 Beta7
               par Country
              www.k1der.net
----------------------------------------*/

// Description : Interface d'administration du shoutbox

// Chargement de la configuration SQL et des fonctions
if(!file_exists("config.php")) header("location:install.php");
else include "config.php";
include "include/fonctions.php";

/* Identification */
$ident=ident();
/* Login */ 
if ($_POST["login"] && $_POST["pass"]) login($_POST["login"],$_POST["pass"]);
/* Edition d'un message */ 
if ($_POST && $_GET["action"]=="edit" && $_GET["id"] && $ident) edit_mess($_POST["pseudo"],$_POST["mess"],$_GET["id"]);
/* Suppression d'un message */ 
if ($_GET["action"]=="suppr" && $_GET["id"] && $ident) suppr_mess($_GET["id"]);
/* Mise à jour du CSS */ 
if ($_POST && $_GET["action"]=="app" && $ident=="admin") make_css($_POST);
/* Mise à jour de la configuration */ 
if ($_POST && $_GET["action"]=="conf" && $ident=="admin") $error=maj_conf($_POST);
/* Ajout d'un modérateur */ 
if ($_POST && $_GET["action"]=="util" && $ident=="admin") add_modo($_POST);
/* Suppression d'un modérateur */ 
if ($_GET["action"]=="suppr" && $_GET["user"] && $ident=="admin") suppr_modo($_GET["user"]);
/* Edition d'un utilisateur */ 
if ($_POST && $_GET["action"]=="edit" && $_GET["user"] && $ident=="admin") edit_user($_POST);

/* Logout */
if ($_GET["deco"]==1 && $ident) logout();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>K1der Shoutbox 1.7 : Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="include/styles.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="include/scripts.js"></script>
<script language="javascript" type="text/javascript" src="include/admin.js"></script>
</head>
<body>
<div id="bulle"></div>
<!-- Head -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td class="titre">Administration</td>
  <td class="titre2">
<?
  if ($ident=="admin") { 
		if(!$_GET["action"] || $_GET["action"]=="mess") echo "<span style=\"text-decoration:underline;\">Messages</span>&nbsp;|&nbsp;";
		else echo "<a href=\"".$_SERVER["SCRIPT_NAME"]."?action=mess\">Messages</a>&nbsp;|&nbsp;";
		if($_GET["action"]=="conf") echo "<span style=\"text-decoration:underline;\">Configuration</span>&nbsp;|&nbsp;";
		else echo "<a href=\"".$_SERVER["SCRIPT_NAME"]."?action=conf\">Configuration</a>&nbsp;|&nbsp;";
		if($_GET["action"]=="util") echo "<span style=\"text-decoration:underline;\">Utilisateurs</span>&nbsp;|&nbsp;";
		else echo "<a href=\"".$_SERVER["SCRIPT_NAME"]."?action=util\">Utilisateurs</a>&nbsp;|&nbsp;";
		if($_GET["action"]=="app") echo "<span style=\"text-decoration:underline;\">Apparence</span>&nbsp;|&nbsp;";
		else echo "<a href=\"".$_SERVER["SCRIPT_NAME"]."?action=app\">Apparence</a>&nbsp;|&nbsp;";
  }
  if ($ident) echo "<a href=\"".$_SERVER["SCRIPT_NAME"]."?deco=1\">D&eacute;connection</a>";
	?>
  </td>
 </tr>
<!-- Fin Head -->
<? 
// Option 1 : Non identifi&eacute; 
if (!$ident) {
?>
 <tr>
  <td align="center" colspan="2">
  <br/><br/><br/><b>Identification</b><br/><br/>
  <form name="form" action="<?=$_SERVER["SCRIPT_NAME"];?>" method="post">
  <? if ($_GET["perdu"]==1) echo "<span class=\"erreur\">Login ou mot de passe incorrect</span><br/><br/>"; ?>
  <input type="text" name="login" onfocus="this.value=''" value="login" size="17" /><br/>
    <input type="password" name="pass" onfocus="this.value=''" value="pass" size="17" /><br/>
    <input name="Submit" type="submit" class="bouton" value="Valider" />
    <br/><br/>
    </form>
  </td>
 </tr>
 <?
// Option 2 : Liste des messages 
} else if(!empty($error)) {
?>
<table class="admtab" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	  <td colspan="2" height="20px">&nbsp;</td>
  </tr>
	<tr>
    <td colspan="2" class="titre3">&loz;&nbsp;Erreur !</td>
  </tr>
	<tr>
    <td colspan="2">Les erreurs suivantes ont étés détectées dans vos informations.</td>
  </tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
  </tr>
	<tr>
		<td colspan="2"><?=$error;?></td>
	</tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
  </tr>
	<tr>
	  <td colspan="2" align="center"><input class="submit" type="button" onclick="javascript:history.back();" value="Modifier les informations" /></td>
  </tr>
	<tr>
	  <td colspan="2" nowrap="nowrap">&nbsp;</td>
  </tr>
</table>
<?
} else if(!$_GET["action"] || $_GET["action"]=="mess" && $ident) { ?>
 <tr>
  <td colspan="2">
<?
if($_GET["page"]) $page=$_GET["page"];
else $page=1;
$start=100*($page-1);
$req = sql("SELECT * FROM ".$sql["table"]." ORDER BY id DESC  LIMIT ".$start.",100");

$nbre=mysql_fetch_row(sql("SELECT COUNT(*) FROM ".$sql["table"])); //Comptage du nombre de messages
if ($_GET["pseudo"]) {
 $contenu=$_GET["pseudo"]; 
 $lien="?pseudo=".$_GET["pseudo"];
} else {
 $contenu="pseudo";
 unset($lien);
}
$hauteur=count($smileys)*22+45;
if($nbre[0]>1) $esse="s";
else unset($esse);
?>
   <div align="center" style="margin:20px;">
	 <div style="width:90%;" align="right"><?=pagination($page,"action=mess",100,$nbre[0]);?></div>
	 <table width="90%" cellpadding="2" cellspacing="1" class="liste">	 
	 <?
   $style="td1";
   while($disp = mysql_fetch_array($req)) {
    $date = date("d/m/Y",$disp["timestamp"]);
    $heure = date("H:i",$disp["timestamp"]);
    $mess = replace_aff(substr(stripslashes($disp["mess"]),0,$nb_carac));
    $pseudo = stripslashes($disp["pseudo"]) ;
		if(array_search($pseudo,$lesadmins)===0) $type="a";
		else if(in_array($pseudo,$lesadmins)) $type="m";
		else $type="v";
?>
    <tr>
     <td width="90%" class="<?=$style;?>">
      <a class="pseudo<?=$type;?>" onmouseover="affiche('','le <?=$date;?>&lt;br/&gt;&agrave; <?=$heure;?>')" onmouseout="affiche('cache')"><?=$pseudo;?></a> : <?=$mess."\n";?>
     </td>
     <td width="5%" style="text-align:center;font-weight:bold;" class="<?=$style;?>"><a href="<?=$_SERVER["SCRIPT_NAME"];?>?action=edit&amp;id=<?=$disp["id"];?>">Editer</a></td>
		 <td width="5%" style="text-align:center;font-weight:bold;" class="<?=$style;?>"><a href="javascript:supprimer('<?=$disp["id"];?>','<?=$pseudo;?>');">Effacer</a></td>
    </tr>
<?
   if ($style=="td1") $style="td2";
   else $style="td1";
   }
?>
   </table>
	 <div style="width:90%;" align="right"><?=pagination($page,"action=mess",100,$nbre[0]);?></div>
	 </div>
  </td>
 </tr>
<?
// Option 3 : Modification d'un messages 
} else if($_GET["action"]=="edit" && $_GET["id"] && $ident) {
	$info=mysql_fetch_array(sql("SELECT pseudo,mess FROM ".$sql["table"]." WHERE id=\"".$_GET["id"]."\""));
?>
 <tr>
  <td colspan="2">
		<form method="post" action="">
		 <table class="admtab" width="100%" cellpadding="0" cellspacing="0">
			 <tr>
				 <td colspan="2" style="height:20px;">&nbsp;</td>
			 </tr>
			 <tr>
				 <td colspan="2" class="titre3">&loz;&nbsp;Modifier un message</td>
			 </tr>
			<tr>
				 <td nowrap="nowrap">Pseudo :</td>
				 <td width="70%"><input type="text" name="pseudo" value="<?=$info["pseudo"];?>" /></td>
			</tr>
			<tr>
				 <td nowrap="nowrap">Message :</td>
				 <td width="70%"><input type="text" name="mess" style="width:60%;" value="<?=htmlentities(stripslashes($info["mess"]));?>" /></td>
			</tr>
			 <tr>
				 <td colspan="2">&nbsp;</td>
			 </tr>
			<tr>
				 <td colspan="2" style="text-align:center;"><input name="submit" type="submit" class="bouton" value="Valider les modifications" /></td>
			</tr>
		 </table>
	 </form>
	</td>
 </tr>
<?
// Option 4 : Configuration
} else if($_GET["action"]=="conf" && $ident=="admin") {
?>
 <tr>
  <td colspan="2">
	<div align="center">
	<form action="" method="post">
		<table class="admtab" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="2" style="height:20px;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="titre3">&loz;&nbsp;Configuration SQL</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Serveur :</td>
				<td width="70%"><input name="sql_server" type="text" value="<?=$sql["server"];?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Login :</td>
				<td width="70%"><input name="sql_login" type="text" value="<?=$sql["login"];?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Mot de passe :</td>
				<td width="70%"><input name="sql_pass" type="password" value="<?=$sql["pass"];?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Base SQL :</td>
				<td width="70%"><input name="sql_base" type="text" value="<?=$sql["base"];?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Table du shoutbox :</td>
				<td width="70%"><input name="sql_table" type="text" value="<?=$sql["table"];?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Table de configuration du shoutbox :</td>
				<td width="70%"><input name="sql_table2" type="text" value="<?=$sql["table2"];?>" /></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="titre3">&loz;&nbsp;Textes de remplacement</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Liens :</td>
				<td width="70%"><input name="liens" type="text" value="<?=$liens;?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">E-mails :</td>
				<td width="70%"><input name="mails" type="text" value="<?=$mails;?>" /></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="titre3">&loz;&nbsp;Smileys</td>
			</tr>
			<tr>
				<td nowrap="nowrap">R&eacute;pertoire des smileys :</td>
				<td width="70%"><input name="rep_smileys" type="text" value="<?=str_replace($rep_shout,"",$rep_smileys);?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap" valign="top">Smileys :</td>
				<td width="70%">
				<?
				$j=1;
				foreach($smileys as $i => $var) {
					unset($list);
					$list.="<select name=\"smileys_".$j."\">\n";
				$list.="<option value=\"suppr\" style=\"color:#FF0000;\">Supprimer</option>\n";
				$dir = opendir($rep_smileys);
				while($fichier = readdir($dir)) {
					if($fichier==$var && $fichier!=".." && $fichier!=".") $list.="<option value=\"".$fichier."\" selected=\"selected\">".$fichier."</option>\n";
					else if($fichier!=".." && $fichier!=".") $list.="<option value=\"".$fichier."\">".$fichier."</option>\n";
				}
				closedir($dir);
				$list.="</select>\n";
				?>
				<input name="smileys_t_<?=$j;?>" type="text" size="3" value="<?=$i;?>" />&nbsp;<?=$list;?><br />
				<?
				$j++;
				}
				unset($list);
				$list.="<select name=\"new_smileys\">\n";
				$list.="<option value=\"\" selected=\"selected\"></option>\n";
				$dir = opendir($rep_smileys);
				while($fichier = readdir($dir)) {
					if($fichier!=".." && $fichier!=".") $list.="<option value=\"".$fichier."\">".$fichier."</option>\n";
				}
				closedir($dir);
				$list.="</select>\n";
				?>
				</td>
			</tr>
			<tr>
				<td align="right">Nouveau :</td><td><input name="new_smileys_t" type="text" size="3" value="" />&nbsp;<?=$list;?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="titre3">&loz;&nbsp;Messages et pseudos </td>
			</tr>
			<tr>
        <td nowrap="nowrap">Longueur maximum des pseudos :</td>
        <td><input size="3" name="nb_caracp" type="text" value="<?=$nb_caracp;?>" />
    caract&egrave;res</td>
			  </tr>
			<tr>
				<td nowrap="nowrap">Longueur maximum du message :</td>
				<td width="70%"><input size="3" name="nb_carac" type="text" value="<?=$nb_carac;?>" /> caract&egrave;res</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Longueur maximum d'un mot :</td>
				<td width="70%"><input size="3" name="long_max" type="text" value="<?=$long_max;?>" /> caract&egrave;res</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" class="titre3">&loz;&nbsp;Affichage</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Afficher le nombre de messages post&eacute;s :</td>
				<td width="70%"><input name="nb_posts" type="checkbox"<? if($nb_posts=="1") echo " checked=\"checked\"";?> value="1" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Afficher le lien vers l'administration :</td>
				<td width="70%"><input name="lien_adm" type="checkbox"<? if($lien_adm=="1") echo " checked=\"checked\"";?> value="1" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Liens Historique, Aide (et Administration) :</td>
				<td width="70%">
				En <select name="pl_liens">
					<option value="haut"<? if($pl_liens=="haut") echo " selected=\"selected\"";?>>Haut</option>
					<option value="bas"<? if($pl_liens=="bas") echo " selected=\"selected\"";?>>Bas</option>
				</select>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Nombre de message &agrave; afficher :</td>
				<td width="70%"><input size="3" name="nb_mess" type="text" value="<?=$nb_mess;?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Largeur du shoutbox :</td>
				<td width="70%"><input size="3" name="largeur" type="text" value="<?=$largeur;?>" />px</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Hauteur du shoutbox :</td>
				<td width="70%"><input size="3" name="hauteur" type="text" value="<?=$hauteur;?>" />px</td>
			</tr>
			<tr>
        <td nowrap="nowrap">Barre de d&eacute;filement verticale :</td>
        <td><select name="scroll">
              <option value="oui"<? if($scroll=="oui") echo " selected=\"selected\"";?>>Oui</option>
              <option value="non"<? if($scroll=="non") echo " selected=\"selected\"";?>>Non</option>
            </select>
        </td>
			  </tr>
			<tr>
			  <td colspan="2">&nbsp;</td>
			  </tr>
			<tr>
        <td colspan="2" class="titre3">&loz;&nbsp;S&eacute;curit&eacute;</td>
			  </tr>
			<tr>
			  <td>Prot&eacute;ger les pseudos de l'administrateur et des mod&eacute;rateurs<br />
			    (Vous devrez &ecirc;tre identifi&eacute; dans l'administration pour pouvoir poster avec votre pseudo) </td>
			  <td><input name="secu_pseudo" type="checkbox" id="secu_pseudo" value="1"<? if($secu_pseudo=="1") echo " checked=\"checked\"";?> /></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" class="bouton" value="Valider les modifications" /></td>
			</tr>
		</table>
	</form>
	</div>
	</td>
 </tr>
<?
// Option 5 : Gestion des utilisateurs
} else if($_GET["action"]=="util" && $ident=="admin") { ?>
 <tr>
  <td colspan="2">
<?
$nbre=mysql_fetch_row(sql("SELECT COUNT(*) FROM ".$sql["table2"])); //Comptage du nombre de messages
?>
   <div align="center">
	 <table cellpadding="2" cellspacing="1" class="liste" style="margin:20px;">
    <tr>
     <td width="200px" style="text-align:center;font-weight:bold;" class="td2">Login</td>
     <td width="100px" style="text-align:center;font-weight:bold;" class="td2">Type</td>
		 <td width="100px" style="text-align:center;font-weight:bold;" class="td2">Action</td>
    </tr>
<?
   $style="td1";
   for($i=0;$lesadmins[$i];$i=$i+2) {
    $pseudo = stripslashes($disp["valeur"]) ;
?>
    <tr>
     <td width="200px" style="text-align:center;" class="<?=$style;?>"><?=$lesadmins[$i];?></td>
     <td width="100px" style="text-align:center;font-weight:bold;" class="<?=$style;?>">
		 <?
		  if($i==0) echo "<span class=\"pseudoa\">Administrateur</span>";
			else echo "<span class=\"pseudom\">Mod&eacute;rateur</span>";
		 ?>
		 </td>
		 <td width="100px" style="text-align:center;font-weight:bold;" class="<?=$style;?>"><a href="<?=$_SERVER["SCRIPT_NAME"];?>?action=edit&amp;user=<?=$lesadmins[$i];?>">Editer</a><? if($i!=0) echo "&nbsp;|&nbsp;<a href=\"javascript:supprimer_user('".$lesadmins[$i]."');\">Supprimer</a>"; ?></td>
    </tr>
<?
   if ($style=="td1") $style="td2";
   else $style="td1";
   }
?>
   </table>
	 <form method="post" action="">
		 <table class="admtab" width="100%" cellpadding="0" cellspacing="0">
			 <tr>
				 <td colspan="2" class="titre3">&loz;&nbsp;Ajouter un mod&eacute;rateur</td>
			 </tr>
			 <tr>
				 <td nowrap="nowrap">Login&nbsp;:&nbsp;</td>
				 <td width="70%"><input name="new_login" type="text" /></td>
			 </tr>
			 <tr>
				 <td nowrap="nowrap">Mot de passe&nbsp;:&nbsp;</td>
				 <td width="70%"><input name="new_pass" type="password" /></td>
			 </tr>
			 <tr>
				 <td colspan="2" align="center"><input type="submit" class="bouton" value="Ajouter" /></td>
			 </tr>
		 </table>
	 </form>
	 </div>
  </td>
 </tr>
<?
// Option 6 : Edition d'un utilisateur
} else if($_GET["action"]=="edit" && $_GET["user"] && $ident=="admin") {
	$info=mysql_fetch_array(sql("SELECT nom,valeur FROM ".$sql["table2"]." WHERE valeur=\"".$_GET["user"]."\""));
	$info["nom"]=str_replace("_login","_pass",$info["nom"]);
	$info2=mysql_fetch_array(sql("SELECT valeur FROM ".$sql["table2"]." WHERE nom=\"".$info["nom"]."\""));
?>
 <tr>
  <td colspan="2">
	<div align="center">
		<form method="post" action="">
		 <table class="admtab" width="100%" cellpadding="0" cellspacing="0">
			 <tr>
				 <td colspan="2" style="height:20px;">&nbsp;</td>
			 </tr>
			 <tr>
				 <td colspan="2" class="titre3">&loz;&nbsp;Modifier un mod&eacute;rateur</td>
			 </tr>
			<tr>
				 <td nowrap="nowrap">Login :</td>
				 <td width="70%"><input type="text" name="new_login" value="<?=$info["valeur"];?>" /></td>
			</tr>
			<tr>
				 <td nowrap="nowrap" style="vertical-align:top; padding-top:3px;">Mot de passe :</td>
				 <td width="70%"><input type="password" name="new_pass" value="" /><br />(Laissez vide pour ne pas le changer)</td>
			</tr>
			<tr>
				 <td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				 <td colspan="2" style="text-align:center;"><input name="submit" type="submit" class="bouton" value="Valider les modifications" /></td>
			</tr>
		 </table>
		</form>
	</div>
	</td>
 </tr>
<?
// Option 7 : Edition de la feuille de style
} else if($_GET["action"]=="app" && $ident=="admin") { ?>
 <tr>
  <td colspan="2">
	<form method="post" action="">
   <table style="margin-top:20px;" class="admtab" width="100%" cellspacing="0" cellpadding="0">
	 <tr><td colspan="4" class="titre3">&loz;&nbsp;Apparence</td></tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  <td width="50%" rowspan="19" colspan="2" align="center" valign="top"><span style="font-weight:bold; font-size:20px;">Aper&ccedil;u</span>
		   <div id="apercu" style="border-style:1px dashed #AAAAAA;padding:20px;margin:20px;">
        <table width="126" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
         <td id="tab1" align="center" style="padding:0px;">[ <a id="histo" 
				 onmouseout="
				 this.style.fontFamily=document.getElementById('iliens_police').value;
				 this.style.color=document.getElementById('iliens_c').value;
				 this.style.fontSize=document.getElementById('iliens_tfont').value;
				 this.style.textDecoration=document.getElementById('iliens_deco').value;
				 " onmouseover="
				 this.style.fontFamily=document.getElementById('iliens_policep').value;
				 this.style.color=document.getElementById('iliens_cp').value;
				 this.style.fontSize=document.getElementById('iliens_tfontp').value;
				 this.style.textDecoration=document.getElementById('iliens_decop').value;
				 " href="#">Historique</a> : <a id="aide" 
				 onmouseout="
				 this.style.fontFamily=document.getElementById('iliens_police').value;
				 this.style.color=document.getElementById('iliens_c').value;
				 this.style.fontSize=document.getElementById('iliens_tfont').value;
				 this.style.textDecoration=document.getElementById('iliens_deco').value;
				 " onmouseover="
				 this.style.fontFamily=document.getElementById('iliens_policep').value;
				 this.style.color=document.getElementById('iliens_cp').value;
				 this.style.fontSize=document.getElementById('iliens_tfontp').value;
				 this.style.textDecoration=document.getElementById('iliens_decop').value;
				 " href="#">Aide</a> ]<br/><br/></td>
         </tr>
         <tr> 
         <td align="center" id="tab2" style="padding:0px;">
          <b>3</b><br/>messages post&eacute;s<br/><br/>
          <input id="pseudo" name="pseudo" onfocus="this.value=''" value="Country" size="17" /><br/>
          <input id="message" name="message" onfocus="this.value=''" value="message" size="17" maxlength="90" /><br/>
          <input class="bouton" type="button" value="Poster" name="valider" id="valider" /><br/><br/>
          [ <a id="actu" 
				 onmouseout="
				 this.style.fontFamily=document.getElementById('iliens_police').value;
				 this.style.color=document.getElementById('iliens_c').value;
				 this.style.fontSize=document.getElementById('iliens_tfont').value;
				 this.style.textDecoration=document.getElementById('iliens_deco').value;
				 " onmouseover="
				 this.style.fontFamily=document.getElementById('iliens_policep').value;
				 this.style.color=document.getElementById('iliens_cp').value;
				 this.style.fontSize=document.getElementById('iliens_tfontp').value;
				 this.style.textDecoration=document.getElementById('iliens_decop').value;
				 " href="#">Actualiser</a> ] 
          <br/><br/>
         </td>
         </tr>
         <tr>
         <td style="padding:0px;">
          <table width="126" id="liste" class="liste" cellspacing="1" cellpadding="2">
          <tr>
           <td width="126" id="td1" class="td1" style="padding:2px;">
           <a id="admin" class="pseudoa" onmouseover="affiche('','le 12/10/2004&lt;br/&gt;&agrave; 20:08')" onmouseout="affiche('cache')">Admin</a> : Allez voir ce site <a id="lien" 
				 onmouseout="
				 this.style.fontFamily=document.getElementById('iliens_police').value;
				 this.style.color=document.getElementById('iliens_c').value;
				 this.style.fontSize=document.getElementById('iliens_tfont').value;
				 this.style.textDecoration=document.getElementById('iliens_deco').value;
				 " onmouseover="
				 this.style.fontFamily=document.getElementById('iliens_policep').value;
				 this.style.color=document.getElementById('iliens_cp').value;
				 this.style.fontSize=document.getElementById('iliens_tfontp').value;
				 this.style.textDecoration=document.getElementById('iliens_decop').value;
				 " href="http://www.site-inter.net" target="_blank"><?=$liens;?></a>
           </td>
          </tr>
          <tr>
           <td width="126" id="td2" class="td2" style="padding:2px;">
           <a id="modo" class="pseudom" onmouseover="affiche('','le 12/10/2004&lt;br/&gt;&agrave; 19:55')" onmouseout="affiche('cache')">Modo</a> : Ecrit-moi : 
				   <a id="mail" 
				 onmouseout="
				 this.style.fontFamily=document.getElementById('iliens_police').value;
				 this.style.color=document.getElementById('iliens_c').value;
				 this.style.fontSize=document.getElementById('iliens_tfont').value;
				 this.style.textDecoration=document.getElementById('iliens_deco').value;
				 " onmouseover="
				 this.style.fontFamily=document.getElementById('iliens_policep').value;
				 this.style.color=document.getElementById('iliens_cp').value;
				 this.style.fontSize=document.getElementById('iliens_tfontp').value;
				 this.style.textDecoration=document.getElementById('iliens_decop').value;
				 " href="mailto:mon@email.fr" target="_blank"><?=$mails;?></a>
           </td>
          </tr>
          <tr>
           <td width="126" id="td3" class="td1" style="padding:2px;">
           <a id="visit" class="pseudov" onmouseover="affiche('','le 12/10/2004&lt;br/&gt;&agrave; 19:55')" onmouseout="affiche('cache')">Visiteur</a> : Coucou <img align="top" alt=":D" src="smileys/biggrin.gif" />
           </td>
          </tr>
          </table>
         </td>
         </tr>
        </table>
		   </div>
      </td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  </tr>
		<tr><td width="25%">
		Couleur du fond <br/>
		</td>
		  <td width="25%">#<input name="ifond" onkeyup="fond()" id="ifond" type="text" value="<?=css("body","background-color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr><td width="25%">Police</td><td width="25%">
				<select id="ipolice" name="ipolice" onchange="police()">
					<option value="Arial, Helvetica, sans-serif"<? if(css("body","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("body","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("body","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("body","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("body","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("body","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
				</select>
		</td></tr>
		<tr><td width="25%">
		Couleur du texte <br/>
		</td>
		  <td width="25%">#<input name="icfont" onkeyup="cfont()" id="icfont" type="text" value="<?=css("body","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">Taille du texte</td>
		  <td width="25%">
				<select id="itfont" name="itfont" onchange="tfont()">
					<option value="8px"<? if(css("body","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("body","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("body","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("body","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("body","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("body","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("body","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("body","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
				</select>
			</td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  </tr>
		<tr>
		  <td width="25%"><div style="font-size:12px;font-weight:bold;">Champs</div></td>
		  <td width="25%">&nbsp;</td>
		  </tr>
		<tr><td width="25%">
		Fond des champs <br/>
		</td>
		  <td width="25%">#<input name="ichamps_bg" onkeyup="champs_bg()" id="ichamps_bg" type="text" value="<?=css("input","background-color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">Bordure des champs </td>
		  <td width="25%">
			<select id="ichamps_bordert" name="ichamps_bordert" onchange="champs_bordert()">
					<option value="1px"<? if(css("input","border",1)=="1px") echo " selected=\"selected\"";?>>1px</option>
					<option value="2px"<? if(css("input","border",1)=="2px") echo " selected=\"selected\"";?>>2px</option>
					<option value="3px"<? if(css("input","border",1)=="3px") echo " selected=\"selected\"";?>>3px</option>
					<option value="4px"<? if(css("input","border",1)=="4px") echo " selected=\"selected\"";?>>4px</option>
					<option value="5px"<? if(css("input","border",1)=="5px") echo " selected=\"selected\"";?>>5px</option>
					<option value="6px"<? if(css("input","border",1)=="6px") echo " selected=\"selected\"";?>>6px</option>
			</select>
			<select id="ichamps_borders" name="ichamps_borders" onchange="champs_borders()">
        <option value="dashed"<? if(css("input","border",2)=="dashed") echo " selected=\"selected\"";?>>dashed</option>
        <option value="dotted"<? if(css("input","border",2)=="dotted") echo " selected=\"selected\"";?>>dotted</option>
        <option value="double"<? if(css("input","border",2)=="double") echo " selected=\"selected\"";?>>double</option>
        <option value="groove"<? if(css("input","border",2)=="groove") echo " selected=\"selected\"";?>>groove</option>
        <option value="hidden"<? if(css("input","border",2)=="hidden") echo " selected=\"selected\"";?>>hidden</option>
        <option value="inset"<? if(css("input","border",2)=="inset") echo " selected=\"selected\"";?>>inset</option>
        <option value="none"<? if(css("input","border",2)=="none") echo " selected=\"selected\"";?>>none</option>
        <option value="outset"<? if(css("input","border",2)=="outset") echo " selected=\"selected\"";?>>outset</option>
        <option value="ridge"<? if(css("input","border",2)=="ridge") echo " selected=\"selected\"";?>>ridge</option>
        <option value="solid"<? if(css("input","border",2)=="solid") echo " selected=\"selected\"";?>>solid</option>
      </select>
			#<input name="ichamps_border" onkeyup="champs_border()" id="ichamps_border" type="text" value="<?=css("input","border",3);?>" size="8" maxlength="6" />
			</td>
		</tr>
		<tr><td width="25%">Police</td><td width="25%">
				<select id="ichamps_police" name="ichamps_police" onchange="champs_police()">
					<option value="Arial, Helvetica, sans-serif"<? if(css("input","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("input","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("input","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("input","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("input","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("input","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
				</select>
		</td></tr>
		<tr>
		  <td width="25%">Couleur du texte</td>
		  <td width="25%">#<input name="ichamps_cfont" onkeyup="champs_cfont()" id="ichamps_cfont" type="text" value="<?=css("input","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">Taille du texte</td>
		  <td width="25%">
				<select id="ichamps_tfont" name="ichamps_tfont" onchange="champs_tfont()">
					<option value="8px"<? if(css("input","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("input","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("input","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("input","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("input","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("input","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("input","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("input","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
				</select>
			</td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  </tr>
		<tr>
		  <td width="25%"><div style="font-size:12px;font-weight:bold;">Boutons</div></td>
		  <td width="25%">&nbsp;</td>
		  </tr>
		<tr>
		  <td width="25%"> Fond des boutons<br/>
      </td>
		  <td width="25%">#
		      <input name="iboutons_bg" onkeyup="boutons_bg()" id="iboutons_bg" type="text" value="<?=css("bouton","background-color");?>" size="8" maxlength="6" /></td>
		  </tr>
		<tr>
		  <td width="25%">Bordure des boutons</td>
		  <td width="25%">
					<select id="iboutons_bordert" name="iboutons_bordert" onchange="boutons_bordert()">
					<option value="1px"<? if(css("bouton","border",1)=="1px") echo " selected=\"selected\"";?>>1px</option>
					<option value="2px"<? if(css("bouton","border",1)=="2px") echo " selected=\"selected\"";?>>2px</option>
					<option value="3px"<? if(css("bouton","border",1)=="3px") echo " selected=\"selected\"";?>>3px</option>
					<option value="4px"<? if(css("bouton","border",1)=="4px") echo " selected=\"selected\"";?>>4px</option>
					<option value="5px"<? if(css("bouton","border",1)=="5px") echo " selected=\"selected\"";?>>5px</option>
					<option value="6px"<? if(css("bouton","border",1)=="6px") echo " selected=\"selected\"";?>>6px</option>
          </select>
		    <select id="iboutons_borders" name="iboutons_borders" onchange="boutons_borders()">
					<option value="dashed"<? if(css("bouton","border",2)=="dashed") echo " selected=\"selected\"";?>>dashed</option>
					<option value="dotted"<? if(css("bouton","border",2)=="dotted") echo " selected=\"selected\"";?>>dotted</option>
					<option value="double"<? if(css("bouton","border",2)=="double") echo " selected=\"selected\"";?>>double</option>
					<option value="groove"<? if(css("bouton","border",2)=="groove") echo " selected=\"selected\"";?>>groove</option>
					<option value="hidden"<? if(css("bouton","border",2)=="hidden") echo " selected=\"selected\"";?>>hidden</option>
					<option value="inset"<? if(css("bouton","border",2)=="inset") echo " selected=\"selected\"";?>>inset</option>
					<option value="none"<? if(css("bouton","border",2)=="none") echo " selected=\"selected\"";?>>none</option>
					<option value="outset"<? if(css("bouton","border",2)=="outset") echo " selected=\"selected\"";?>>outset</option>
					<option value="ridge"<? if(css("bouton","border",2)=="ridge") echo " selected=\"selected\"";?>>ridge</option>
					<option value="solid"<? if(css("bouton","border",2)=="solid") echo " selected=\"selected\"";?>>solid</option>
        </select>
  #
  <input name="iboutons_border" onkeyup="boutons_border()" id="iboutons_border" type="text" value="<?=css("bouton","border",3);?>" size="8" maxlength="6" />
      </td>
		  </tr>
		<tr>
		  <td width="25%">Police</td>
		  <td width="25%"><select id="iboutons_police" name="iboutons_police" onchange="boutons_police()">
					<option value="Arial, Helvetica, sans-serif"<? if(css("bouton","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("bouton","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("bouton","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("bouton","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("bouton","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("bouton","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
        </select>
      </td>
		  </tr>
		<tr>
		  <td width="25%">Couleur du texte</td>
		  <td width="25%">#
		      <input name="iboutons_cfont" onkeyup="boutons_cfont()" id="iboutons_cfont" type="text" value="<?=css("bouton","color");?>" size="8" maxlength="6" /></td>
		  </tr>
		<tr>
		  <td width="25%">Taille du texte</td>
		  <td width="25%"><select id="iboutons_tfont" name="iboutons_tfont" onchange="boutons_tfont()">
					<option value="8px"<? if(css("bouton","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("bouton","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("bouton","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("bouton","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("bouton","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("bouton","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("bouton","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("bouton","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
        </select>
      </td>
		  <td width="25%"><div style="font-size:12px;font-weight:bold;">Liste des messages</div></td>
		  <td width="25%">&nbsp;</td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">Police</td>
		  <td width="25%"><select id="iliste_police" name="iliste_police" onchange="liste_police()">
					<option value="Arial, Helvetica, sans-serif"<? if(css("liste","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("liste","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("liste","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("liste","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("liste","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("liste","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
        </select>
      </td>
		</tr>
		<tr>
		  <td width="25%"><div style="font-size:12px;font-weight:bold;">Liens</div></td>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">Couleur</td>
		  <td width="25%">#
          <input name="iliste_c" onkeyup="liste_c()" id="iliste_c" type="text" value="<?=css("liste","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr><td width="25%">Police</td><td width="25%">
				<select id="iliens_police" name="iliens_police" onchange="liens_police()">
					<option value="Arial, Helvetica, sans-serif"<? if(css("a:link","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("a:link","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("a:link","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("a:link","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("a:link","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("a:link","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
				</select>
		</td>
		  <td width="25%">Taille</td>
		  <td width="25%"><select id="iliste_tfont" name="iliste_tfont" onchange="liste_tfont()">
					<option value="8px"<? if(css("liste","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("liste","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("liste","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("liste","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("liste","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("liste","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("liste","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("liste","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
        </select>
      </td>
		</tr>
		<tr><td width="25%">Couleur</td><td width="25%">#<input name="iliens_c" onkeyup="liens_c()" id="iliens_c" type="text" value="<?=css("a:link","color");?>" size="8" maxlength="6" /></td>
		  <td width="25%">Bordure</td>
		  <td width="25%"> #
          <input name="iliste_border" onkeyup="liste_border()" id="iliste_border" type="text" value="<?=css("liste","background-color");?>" size="8" maxlength="6" />
      </td>
		</tr>
		<tr>
		  <td width="25%">Taille</td>
		  <td width="25%">
				<select id="iliens_tfont" name="iliens_tfont" onchange="liens_tfont()">
					<option value="8px"<? if(css("a:link","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("a:link","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("a:link","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("a:link","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("a:link","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("a:link","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("a:link","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("a:link","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
				</select>
			</td>
		  <td width="25%">Fond case 1 </td>
		  <td width="25%">#
          <input name="iliste_bg1" onkeyup="liste_bg1()" id="iliste_bg1" type="text" value="<?=css("td1","background-color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">D&eacute;coration</td>
		  <td width="25%">
				<select id="iliens_deco" name="iliens_deco" onchange="liens_deco()">
					<option value="none"<? if(css("a:link","text-decoration")=="none") echo " selected=\"selected\"";?>>Aucune</option>
					<option value="underline"<? if(css("a:link","text-decoration")=="underline") echo " selected=\"selected\"";?>>Soulign&eacute;</option>
					<option value="line-through"<? if(css("a:link","text-decoration")=="line-through") echo " selected=\"selected\"";?>>Barr&eacute;</option>
					<option value="overline"<? if(css("a:link","text-decoration")=="overline") echo " selected=\"selected\"";?>>Surlign&eacute;</option>
				</select>
			</td>
		  <td width="25%">Fond case 2 </td>
		  <td width="25%">#
          <input name="iliste_bg2" onkeyup="liste_bg2()" id="iliste_bg2" type="text" value="<?=css("td2","background-color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
			<td width="25%">Couleur pseudo administrateur</td>
			<td width="25%">#
          <input name="iliste_cadmin" onkeyup="liste_cadmin()" id="iliste_cadmin" type="text" value="<?=css("pseudoa","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%"><div style="font-size:12px;font-weight:bold;">Liens point&eacute;s </div></td>
		  <td width="25%">&nbsp;</td>
			<td width="25%">Couleur pseudo mod&eacute;rateur </td>
			<td width="25%">#
          <input name="iliste_cmodo" onkeyup="liste_cmodo()" id="iliste_cmodo" type="text" value="<?=css("pseudom","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">Police</td>
		  <td width="25%"><select id="iliens_policep" name="iliens_policep">
					<option value="Arial, Helvetica, sans-serif"<? if(css("a:hover","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("a:hover","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("a:hover","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("a:hover","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("a:hover","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("a:hover","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
        </select>
      </td>
		  <td width="25%">Couleur pseudo visiteur </td>
		  <td width="25%">#
          <input name="iliste_cvisit" onkeyup="liste_cvisit()" id="iliste_cvisit" type="text" value="<?=css("pseudov","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">Couleur</td>
		  <td width="25%">#
		      <input name="iliens_cp" id="iliens_cp" type="text" value="<?=css("a:hover","color");?>" size="8" maxlength="6" /></td><td colspan="2">&nbsp;</td>
		  </tr>
		<tr>
		  <td width="25%">Taille</td>
		  <td width="25%"><select id="iliens_tfontp" name="iliens_tfontp">
					<option value="8px"<? if(css("a:hover","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("a:hover","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("a:hover","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("a:hover","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("a:hover","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("a:hover","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("a:hover","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("a:hover","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
        </select>
      </td>
		  <td width="25%"><div style="font-size:12px;font-weight:bold;">Bulles</div></td>
		  <td width="25%">&nbsp;</td>
		</tr>
		<tr>
		  <td width="25%">D&eacute;coration</td>
		  <td width="25%"><select id="iliens_decop" name="iliens_decop">
					<option value="none"<? if(css("a:hover","text-decoration")=="none") echo " selected=\"selected\"";?>>Aucune</option>
					<option value="underline"<? if(css("a:hover","text-decoration")=="underline") echo " selected=\"selected\"";?>>Soulign&eacute;</option>
					<option value="line-through"<? if(css("a:hover","text-decoration")=="line-through") echo " selected=\"selected\"";?>>Barr&eacute;</option>
					<option value="overline"<? if(css("a:hover","text-decoration")=="overline") echo " selected=\"selected\"";?>>Surlign&eacute;</option>
        </select>
      </td>
		  <td width="25%"> Couleur du fond <br/>
      </td>
		  <td width="25%">#
          <input name="ibulle_fond" onkeyup="bulle_fond()" id="ibulle_fond" type="text" value="<?=css("bulle","background-color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
			<td width="25%">Police</td>
			<td width="25%"><select id="ibulle_police" name="ibulle_police" onchange="bulle_police()">
					<option value="Arial, Helvetica, sans-serif"<? if(css("bulle","font-family")=="Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Arial</option>
					<option value="'Times New Roman', Times, serif"<? if(css("bulle","font-family")=="'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Times New Roman</option>
					<option value="'Courier New', Courier, mono"<? if(css("bulle","font-family")=="'Courier New', Courier, mono") echo " selected=\"selected\"";?>>Courier New</option>
					<option value="Georgia, 'Times New Roman', Times, serif"<? if(css("bulle","font-family")=="Georgia, 'Times New Roman', Times, serif") echo " selected=\"selected\"";?>>Georgia</option>
					<option value="Verdana, Arial, Helvetica, sans-serif"<? if(css("bulle","font-family")=="Verdana, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Verdana</option>
					<option value="Geneva, Arial, Helvetica, sans-serif"<? if(css("bulle","font-family")=="Geneva, Arial, Helvetica, sans-serif") echo " selected=\"selected\"";?>>Geneva</option>
        </select>
      </td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
			<td width="25%"> Couleur du texte <br/>
      </td>
			<td width="25%">#
          <input name="ibulle_cfont" onkeyup="bulle_cfont()" id="ibulle_cfont" type="text" value="<?=css("bulle","color");?>" size="8" maxlength="6" /></td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">Taille du texte</td>
		  <td width="25%"><select id="ibulle_tfont" name="ibulle_tfont" onchange="bulle_tfont()">
					<option value="8px"<? if(css("bulle","font-size")=="8px") echo " selected=\"selected\"";?>>8px</option>
					<option value="9px"<? if(css("bulle","font-size")=="9px") echo " selected=\"selected\"";?>>9px</option>
					<option value="10px"<? if(css("bulle","font-size")=="10px") echo " selected=\"selected\"";?>>10px</option>
					<option value="12px"<? if(css("bulle","font-size")=="12px") echo " selected=\"selected\"";?>>12px</option>
					<option value="14px"<? if(css("bulle","font-size")=="14px") echo " selected=\"selected\"";?>>14px</option>
					<option value="16px"<? if(css("bulle","font-size")=="16px") echo " selected=\"selected\"";?>>16px</option>
					<option value="18px"<? if(css("bulle","font-size")=="18px") echo " selected=\"selected\"";?>>18px</option>
					<option value="20px"<? if(css("bulle","font-size")=="20px") echo " selected=\"selected\"";?>>20px</option>
        </select>
      </td>
		</tr>
		<tr>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">&nbsp;</td>
		  <td width="25%">Bordure</td>
		  <td width="25%"><select id="ibulle_bordert" name="ibulle_bordert" onchange="bulle_bordert()">
					<option value="1px"<? if(css("bulle","border",1)=="1px") echo " selected=\"selected\"";?>>1px</option>
					<option value="2px"<? if(css("bulle","border",1)=="2px") echo " selected=\"selected\"";?>>2px</option>
					<option value="3px"<? if(css("bulle","border",1)=="3px") echo " selected=\"selected\"";?>>3px</option>
					<option value="4px"<? if(css("bulle","border",1)=="4px") echo " selected=\"selected\"";?>>4px</option>
					<option value="5px"<? if(css("bulle","border",1)=="5px") echo " selected=\"selected\"";?>>5px</option>
					<option value="6px"<? if(css("bulle","border",1)=="6px") echo " selected=\"selected\"";?>>6px</option>
        </select>
          <select id="ibulle_borders" name="ibulle_borders" onchange="bulle_borders()">
					<option value="dashed"<? if(css("bulle","border",2)=="dashed") echo " selected=\"selected\"";?>>dashed</option>
					<option value="dotted"<? if(css("bulle","border",2)=="dotted") echo " selected=\"selected\"";?>>dotted</option>
					<option value="double"<? if(css("bulle","border",2)=="double") echo " selected=\"selected\"";?>>double</option>
					<option value="groove"<? if(css("bulle","border",2)=="groove") echo " selected=\"selected\"";?>>groove</option>
					<option value="hidden"<? if(css("bulle","border",2)=="hidden") echo " selected=\"selected\"";?>>hidden</option>
					<option value="inset"<? if(css("bulle","border",2)=="inset") echo " selected=\"selected\"";?>>inset</option>
					<option value="none"<? if(css("bulle","border",2)=="none") echo " selected=\"selected\"";?>>none</option>
					<option value="outset"<? if(css("bulle","border",2)=="outset") echo " selected=\"selected\"";?>>outset</option>
					<option value="ridge"<? if(css("bulle","border",2)=="ridge") echo " selected=\"selected\"";?>>ridge</option>
					<option value="solid"<? if(css("bulle","border",2)=="solid") echo " selected=\"selected\"";?>>solid</option>
          </select>
  #
  <input name="ibulle_border" onkeyup="bulle_border()" id="ibulle_border" type="text" value="<?=css("bulle","border",3);?>" size="8" maxlength="6" />
      </td>
		</tr>
		<tr>
		  <td colspan="2" align="center">&nbsp;</td>
		  <td colspan="2" align="center">&nbsp;</td>
		  </tr>
		<tr>
		  <td colspan="4" style="text-align:center;"><input name="Submit" type="submit" class="bouton" value="Valider les modifications" /></td>
		  </tr>
		<tr>
		  <td colspan="4">&nbsp;</td>
		  </tr>
   </table>
	 </form>
  </td>
 </tr>
<? } ?>
<!-- Foot -->
</table>
<!-- Fin Foot -->
</body>
</html>
