<?
/*----------------------------------------
            K1der Shoutbox 1.7 Beta7
               par Country
              www.k1der.net
----------------------------------------*/

// Description : Principales fonctions du shoutbox

function sql($req) {
 global $sql;
 $db = mysql_connect($sql["server"],$sql["login"],$sql["pass"]) or die("Server SQL indisponible ou données de connection erronées");
 mysql_select_db($sql["base"],$db) or die("Erreur SQL:<br/>".$sql["base"]."|".mysql_error());
 $req=mysql_query($req) or die("Erreur SQL:<br/>".mysql_error());
 mysql_close();
 return $req;
}

if(mysql_table_exists($sql["table2"],$sql["base"],0)) $config_ok=1;
else $config_ok=0;

// Chargement de la configuration
unset($nb_caracp,$scroll);
if($config_ok==1) {
	$req=sql("SELECT nom,valeur FROM ".$sql["table2"]."");
	while($info=mysql_fetch_array($req)) {
	 $$info["nom"]=$info["valeur"];
	}
}

if(empty($nb_caracp)) {
	sql("INSERT INTO ".$sql["table2"]." (nom,valeur) VALUES (\"nb_caracp\",\"20\")");
	$nb_caracp=20;
}
if(empty($scroll)) {
	sql("INSERT INTO ".$sql["table2"]." (nom,valeur) VALUES (\"scroll\",\"non\")");
	$scroll="non";
}

function mysql_table_exists($table , $db,$connect=1){ 
	global $sql;
	if($connect==0) {
		$dber = mysql_connect($sql["server"],$sql["login"],$sql["pass"]) or die("Server SQL indisponible ou données de connection erronées");
		mysql_select_db($sql["base"],$dber) or die("Erreur SQL:<br/>".$sql["base"]."|".mysql_error());
	}
	$tables=mysql_list_tables($db); 
	while (list($temp)=mysql_fetch_array($tables)) {
		if($temp==$table) return 1;
	} 
	if($connect==0) mysql_close();
	return 0; 
}

/* Génération du tableau des smileys */
$tmp=explode("=>",$smileys);
unset($smileys);
for($i=0;$tmp[$i];$i=$i+2) $smileys[$tmp[$i]]=$tmp[$i+1];
/* Fin génération */

/* Génération du tableau contenant les admins et les modos */
function tab_admin() {
	global $sql;
	unset($lesadmins);
	$req=sql("SELECT valeur FROM ".$sql["table2"]." WHERE nom=\"admin_login\" or nom=\"admin_pass\" ORDER BY nom");
	for($i=0;$info=mysql_fetch_array($req);$i++) {
	 $lesadmins[$i]=$info["valeur"];
	}
	$req=sql("SELECT valeur FROM ".$sql["table2"]." WHERE nom like \"%mod_login\" or nom like \"%mod_pass\" ORDER BY nom");
	for($i=2;$info=mysql_fetch_array($req);$i++) {
	 $lesadmins[$i]=$info["valeur"];
	}
	return $lesadmins;
}

if($config_ok==1) $lesadmins=tab_admin();

function ident($mode=0) {
	global $lesadmins;
	if(!empty($_COOKIE["shoutbox_login"]) && !empty($_COOKIE["shoutbox_pass"])) {
	 for($i=0;count($lesadmins)>$i;$i=$i+2) {
		if($_COOKIE["shoutbox_login"]==$lesadmins[$i] && $_COOKIE["shoutbox_pass"]==$lesadmins[$i+1] && $i==0) {
			if($mode==0) return "admin";
			else if($mode==1) return $_COOKIE["shoutbox_login"];
		} else if($_COOKIE["shoutbox_login"]==$lesadmins[$i] && $_COOKIE["shoutbox_pass"]==$lesadmins[$i+1]) {
			if($mode==0) return "modo";
			else if($mode==1) return $_COOKIE["shoutbox_login"];
		} else if(count($lesadmins)==($i-1)) {
		 return false;
		}
	 }
	} else return false;
}

function login($login,$pass) {
	global $lesadmins;
	for($i=0;count($lesadmins)>$i;$i=$i+2) {
		if($login==$lesadmins[$i] && md5($pass)==$lesadmins[$i+1]) {
			setcookie("shoutbox_login",$lesadmins[$i],time()+62208000);
			setcookie("shoutbox_pass",$lesadmins[$i+1],time()+62208000);
			header("location:".$_SERVER["SCRIPT_NAME"]);
		} else if(count($lesadmins)==($i-1)) {
		 unset($ident);
		 header("location:".$_SERVER["SCRIPT_NAME"]."?perdu=1");
		}
	}
}

function logout() {
	setcookie("shoutbox_login","");
	setcookie("shoutbox_pass","");
	header("location:".$_SERVER["SCRIPT_NAME"]);
}

//Formatage du message avant AFFICHAGE
function replace_aff($text) {
 global $liens,$mails,$smileys,$rep_smileys,$long_max,$nb_carac;
 // Remplacement des Liens
 $text = eregi_replace(
  "([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
  "<a href=\"\\1://\\2\\3\" target=\"_blank\">".$liens."</a>"
 ,$text);
 // Remplacement des Mails
 $text = eregi_replace(
  "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
    "<a href=\"mailto:\\1\">".$mails."</a>"
 ,$text);
 // On coupe les mots trop longs
 $text=wordwrap($text,$long_max," ",1);
 // Et merde ! Sa a couper les liens et mails aussi :(
 // Alors on extrait les liens et mails et on supprime les espaces dedant
 $masque = '`((\<))(.*?)((?<!\\\)(?(2)>))`s';
 $tab1=array(" ","ahref","\"target");
 $tab2=array("","a href","\" target");
 preg_match_all($masque,$text,$array);
 foreach($array[3] as $resultat) $text=str_replace($resultat,str_replace($tab1,$tab2,$resultat),$text);
 // On passe aux smileys
 foreach($smileys as $code => $image) $text=str_replace($code,"<img align=\"top\" alt=\"".$code."\" src=\"".$rep_smileys."/".$image."\"/>",$text);
 // On retourne le texte avec des slashes pour éviter les erreurs
 return $text;
}

//Formatage du message avant INSERTION
function replace_ins($text) {
	global $nb_carac;
	// On vire les tags html et on met http:// devant les liens
	$tab1=array("http://www.","www.");
	$tab2=array("www.","http://www.");
	$text = str_replace($tab1,$tab2,strip_tags($text));
	$text = substr($text, 0, $nb_carac);
	// On retourne le texte avec des slashes pour éviter les erreurs
	return addslashes($text);
}

// Extraction des valeurs des styles CSS
$fp=@fopen("include/styles.css","r");
$css=@fread($fp,@filesize("include/styles.css"));
@fclose($fp);
function css($class,$type,$partie="1") {
	global $css;
	$record=0;
	$partie--;
	$css2=explode("\n",$css);
	for($i=0;$css2[$i];$i++) {
		if(ereg($class,$css2[$i])) $record=1;
		if(ereg("}",$css2[$i]) && $record==1) $record=0;
		if($record==1 && ereg("	".$type.":",$css2[$i])) {
			$tab1=array($type.":",";","#");
			$tab2=array("");
			$tmp=trim(str_replace($tab1,$tab2,$css2[$i]));
			if($type=="border") $valeur=explode(" ",$tmp);
			else $valeur[0]=$tmp;
			return $valeur[$partie];
		}
	}
	return false;
}

function add_mess($valeurs) {
	global $sql,$lesadmins,$secu_pseudo,$nb_caracp;
	$info = mysql_fetch_array(sql("SELECT mess FROM ".$sql["table"]." ORDER BY id DESC LIMIT 0,1"));
	$mess_prec = $info["mess"];
		$text = replace_ins($valeurs["message"]);
		$pseudo = addslashes(strip_tags($valeurs["pseudo"]));
		$pseudo = substr($pseudo,0,$nb_caracp);
	if(!in_array($pseudo,$lesadmins) || (ident() && ident(1)==$pseudo) || $secu_pseudo==0) {
		setcookie("shoutbox_pseudo",$pseudo,time()+62208000);
		$times = date('U');
		if($mess_prec!=$text && !empty($text) && $text!="message" && $_SERVER["REMOTE_ADDR"]!='83.193.42.89' && $pseudo!='Transplants' && !empty($pseudo)) sql("INSERT INTO ".$sql["table"]." (ip,timestamp,pseudo,mess) VALUES (\"".$_SERVER["REMOTE_ADDR"]."\",\"".$times."\",\"".$pseudo."\",\"".$text."\")");
	}
	header("Location:".$_SERVER["SCRIPT_NAME"]);
}

function edit_mess($pseudo,$mess,$id) {
	global $sql;
	sql("UPDATE ".$sql["table"]." SET pseudo=\"".$pseudo."\",mess=\"".replace_ins($mess)."\" WHERE id=\"".$id."\"");
	header("location:".$_SERVER["SCRIPT_NAME"]);
}

function suppr_mess($id) {
	global $sql;
	sql("DELETE FROM ".$sql["table"]." WHERE id=\"".$id."\"");
	header("location:".$_SERVER["SCRIPT_NAME"]);
}

function edit_user($valeurs) {
	global $sql;
	$info=mysql_fetch_array(sql("SELECT nom FROM ".$sql["table2"]." WHERE valeur=\"".$_GET["user"]."\" && nom like \"%_login\""));
	sql("UPDATE ".$sql["table2"]." SET valeur=\"".$valeurs["new_login"]."\" WHERE nom=\"".$info["nom"]."\"");
	$info["nom"]=str_replace("_login","_pass",$info["nom"]);
	if(!empty($valeurs["new_pass"])) sql("UPDATE ".$sql["table2"]." SET valeur=\"".md5($valeurs["new_pass"])."\" WHERE nom=\"".$info["nom"]."\"");
	$lesadmins=tab_admin();
	if(array_search($valeurs["new_login"],$lesadmins)===0) setcookie("shoutbox_login",$lesadmins[0],time()+62208000);
	if(array_search($valeurs["new_login"],$lesadmins)===0 && !empty($valeurs["new_pass"])) setcookie("shoutbox_pass",$lesadmins[$i+1],time()+62208000);
	header("location:".$_SERVER["SCRIPT_NAME"]."?action=util");
}

function suppr_modo($user) {
	global $sql;
	$info=mysql_fetch_array(sql("SELECT nom FROM ".$sql["table2"]." WHERE valeur=\"".$user."\" && nom like \"%mod_login\""));
	sql("DELETE FROM ".$sql["table2"]." WHERE nom=\"".$info["nom"]."\"");
	$info["nom"]=str_replace("_login","_pass",$info["nom"]);
	sql("DELETE FROM ".$sql["table2"]." WHERE nom=\"".$info["nom"]."\"");
	header("location:".$_SERVER["SCRIPT_NAME"]."?action=util");
}

function add_modo($valeurs) {
	global $sql,$lesadmins;
	if(empty($valeurs["new_login"]) || empty($valeurs["new_pass"])) header("location:".$_SERVER["SCRIPT_NAME"]."?action=util&error=empty");
	if(mysql_num_rows(sql("SELECT * FROM ".$sql["table2"]." WHERE valeur=\"".$valeurs["new_login"]."\" && nom like \"%_login\""))==0) {
		$req=sql("SELECT nom FROM ".$sql["table2"]." WHERE nom like \"%mod_login\"");
		$num=mysql_num_rows($req);
		if($num==0) $i=1;
		else {
			$info=mysql_fetch_array($req);
			$i=str_replace("mod_login","",$info["nom"]);
			$i++;
		}
		sql("INSERT INTO ".$sql["table2"]." (nom,valeur) VALUES (\"".$i."mod_login\",\"".$valeurs["new_login"]."\")");
		sql("INSERT INTO ".$sql["table2"]." (nom,valeur) VALUES (\"".$i."mod_pass\",\"".md5($valeurs["new_pass"])."\")");
		header("location:".$_SERVER["SCRIPT_NAME"]."?action=util");
	} else header("location:".$_SERVER["SCRIPT_NAME"]."?action=util&error=used");
}

function make_css($valeurs) {
	$styles.="/*----------------------------------------\n";
	$styles.="            K1der Shoutbox 1.7 Beta7\n";
	$styles.="               par Country\n";
	$styles.="              www.k1der.net\n";
	$styles.="----------------------------------------*/\n";
	$styles.="/* Description : Styles CSS de la shoutbox */\n";
	$styles.="/* Général */\n";
	$styles.="body,table,td,tr { \n";
	$styles.="	background-color:#".$valeurs["ifond"].";\n";
	$styles.="	font-family:".$valeurs["ipolice"].";\n";
	$styles.="	font-size:".$valeurs["itfont"].";\n";
	$styles.="	color:#".$valeurs["icfont"].";\n";
	$styles.="	font-style:normal;\n";
	$styles.="	text-decoration:none;\n";
	$styles.="	margin:0;\n";
	$styles.="	padding:0;\n";
	$styles.="}\n";
	$styles.=".admtab {\n";
	$styles.="	text-align:left;\n";
	$styles.="}\n";
	$styles.=".admtab td {\n";
	$styles.="	padding-left:20px;\n";
	$styles.="}\n";
	$styles.="/* Champs */\n";
	$styles.="input,select,textarea {\n";
	$styles.="	background-color:#".$valeurs["ichamps_bg"].";\n";
	$styles.="	border:".$valeurs["ichamps_bordert"]." ".$valeurs["ichamps_borders"]." #".$valeurs["ichamps_border"].";\n";
	$styles.="	font-family:".$valeurs["ichamps_police"].";\n";
	$styles.="	color:#".$valeurs["ichamps_cfont"].";\n";
	$styles.="	font-size:".$valeurs["ichamps_tfont"].";\n";
	$styles.="}\n";
	$styles.="/* Boutons */\n";
	$styles.=".bouton {\n";
	$styles.="	background-color:#".$valeurs["iboutons_bg"].";\n";
	$styles.="	border:".$valeurs["iboutons_bordert"]." ".$valeurs["iboutons_borders"]." #".$valeurs["iboutons_border"].";\n";
	$styles.="	font-family:".$valeurs["iboutons_police"].";\n";
	$styles.="	color:#".$valeurs["iboutons_cfont"].";\n";
	$styles.="	font-size:".$valeurs["iboutons_tfont"].";\n";
	$styles.="}\n";
	$styles.="/* Liens */\n";
	$styles.="a:link,a:active,a:visited {\n";
	$styles.="	font-family:".$valeurs["iliens_police"].";\n";
	$styles.="	color:#".$valeurs["iliens_c"].";\n";
	$styles.="	font-size:".$valeurs["iliens_tfont"].";\n";
	$styles.="	text-decoration:".$valeurs["iliens_deco"].";\n";
	$styles.="}\n";
	$styles.="/* Liens pointés */\n";
	$styles.="a:hover {\n";
	$styles.="	font-family:".$valeurs["iliens_policep"].";\n";
	$styles.="	color:#".$valeurs["iliens_cp"].";\n";
	$styles.="	font-size:".$valeurs["iliens_tfontp"].";\n";
	$styles.="	text-decoration:".$valeurs["iliens_decop"].";\n";
	$styles.="}\n";
	$styles.="/* Liste */\n";
	$styles.=".liste {\n";
	$styles.="	font-family:".$valeurs["iliste_police"].";\n";
	$styles.="	color:#".$valeurs["iliste_c"].";\n";
	$styles.="	font-size:".$valeurs["iliste_tfont"].";\n";
	$styles.="	background-color:#".$valeurs["iliste_border"].";\n";
	$styles.="	border:0;\n";
	$styles.="}\n";
	$styles.=".liste2 {\n";
	$styles.="	font-family:".$valeurs["iliste_police"].";\n";
	$styles.="	color:#".$valeurs["iliste_c"].";\n";
	$styles.="	font-size:".$valeurs["iliste_tfont"].";\n";
	$styles.="	background-color:#".$valeurs["iliste_border"].";\n";
	$styles.="	border:0;\n";
	$styles.="	margin:20px;\n";
	$styles.="}\n";
	$styles.=".td1 {\n";
	$styles.="	background-color:#".$valeurs["iliste_bg1"].";\n";
	$styles.="	padding:2px;\n";
	$styles.="	text-align:left;\n";
	$styles.="}\n";
	$styles.=".td2 {\n";
	$styles.="	background-color:#".$valeurs["iliste_bg2"].";\n";
	$styles.="	padding:2px;\n";
	$styles.="	text-align:left;\n";
	$styles.="}\n";
	$styles.="/* Pseudos */\n";
	$styles.=".pseudoa,.pseudom,.pseudov {\n";
	$styles.="	font-weight:bold;\n";
	$styles.="	font-size:".$valeurs["iliens_tfont"].";\n";
	$styles.="}\n";
	$styles.="/* Pseudo Admins */\n";
	$styles.=".pseudoa {\n";
	$styles.="	color:#".$valeurs["iliste_cadmin"].";\n";
	$styles.="}\n";
	$styles.="/* Pseudo Modérateurs */\n";
	$styles.=".pseudom {\n";
	$styles.="	color:#".$valeurs["iliste_cmodo"].";\n";
	$styles.="}\n";
	$styles.="/* Pseudo Visiteurs */\n";
	$styles.=".pseudov {\n";
	$styles.="	color:#".$valeurs["iliste_cvisit"].";\n";
	$styles.="}\n";
	$styles.="/* Apparence des bulles */\n";
	$styles.="#bulle {\n";
	$styles.="	background-color:#".$valeurs["ibulle_fond"].";\n";
	$styles.="	font-family:".$valeurs["ibulle_police"].";\n";
	$styles.="	color:#".$valeurs["ibulle_cfont"].";\n";
	$styles.="	font-size:".$valeurs["ibulle_tfont"].";\n";
	$styles.="	border:".$valeurs["ibulle_bordert"]." ".$valeurs["ibulle_borders"]." #".$valeurs["ibulle_border"].";\n";
	$styles.="	text-align: center;\n";
	$styles.="	position:absolute;\n";
	$styles.="	display:none;\n";
	$styles.="	padding:1px;\n";
	$styles.="	z-index:100;\n";
	$styles.="}\n";
	$styles.="/* Formulaire */\n";
	$styles.="form {\n";
	$styles.="	margin:0;\n";
	$styles.="}\n";
	$styles.="/* Erreur */\n";
	$styles.=".erreur {\n";
	$styles.="	font-family:Verdana, Arial, Helvetica, sans-serif;\n";
	$styles.="	font-weight:bold;\n";
	$styles.="	color:#CC0000;\n";
	$styles.="	font-size:10px;\n";
	$styles.="}\n";
	$styles.="/* Titre */\n";
	$styles.=".titre {\n";
	$styles.="	font-family:".$valeurs["ipolice"].";\n";
	$styles.="	font-size:17px;\n";
	$styles.="	font-weight:bold;\n";
	$styles.="	color:#".$valeurs["icfont"].";\n";
	$styles.="	text-decoration:none;\n";
	$styles.="	background-color:#".$valeurs["iliste_border"].";\n";
	$styles.="	height:25px;\n";
	$styles.="	padding-left:20px;\n";
	$styles.="}\n";
	$styles.=".titre2 {\n";
	$styles.="	font-family:".$valeurs["ipolice"].";\n";
	$styles.="	font-size:10px;\n";
	$styles.="	color:#".$valeurs["icfont"].";\n";
	$styles.="	text-decoration:none;\n";
	$styles.="	background-color:#".$valeurs["iliste_border"].";\n";
	$styles.="	height:25px;\n";
	$styles.="	text-align:right;\n";
	$styles.="	padding-right:10px;\n";
	$styles.="}\n";
	$styles.=".titre2 a:link, .titre2 a:visited {\n";
	$styles.="	color:#".$valeurs["icfont"].";\n";
	$styles.="	text-decoration:none;\n";
	$styles.="}\n";
	$styles.=".titre2 a:active, .titre2 a:hover {\n";
	$styles.="	color:#".$valeurs["icfont"].";\n";
	$styles.="	text-decoration:underline;\n";
	$styles.="}";
	$styles.=".titre3 {\n";
	$styles.="	font-family:".$valeurs["ipolice"].";\n";
	$styles.="	font-size:12px;\n";
	$styles.="	font-weight:bold;\n";
	$styles.="	color:#".$valeurs["icfont"].";\n";
	$styles.="	text-decoration:none;\n";
	$styles.="	background-color:#".$valeurs["iboutons_bg"].";\n";
	$styles.="	padding-left:20px;\n";
	$styles.="}\n";
	
	$fp=fopen("include/styles.css","w");
	fwrite($fp,$styles);
	fclose($fp);
	header("location:".$_SERVER["SCRIPT_NAME"]);
}

function maj_conf($valeurs) {
	global $sql;
	unset($text,$tab_sm,$error);
	if(
	$sql["server"]!=$valeurs["sql_server"] ||
	$sql["login"]!=$valeurs["sql_login"] ||
	$sql["pass"]!=$valeurs["sql_pass"] ||
	$sql["base"]!=$valeurs["sql_base"] ||
	$sql["table"]!=$valeurs["sql_table"] ||
	$sql["table2"]!=$valeurs["sql_table2"]
	) {
	
		if(empty($valeurs["sql_server"])) $error.="- Vous devez rentrer l'adresse du serveur SQL.<br/>";
		else if (!$escuel = @mysql_connect($valeurs["sql_server"],$valeurs["sql_login"],$valeurs["sql_pass"])) {
			$error.="- Impossible de se connecter au serveur SQL, cela peut être dû à une mauvaise adresse de serveur, un mauvais login ou un mauvais mot de passe.<br/>";
		} else if(empty($valeurs["sql_base"])) $error.="- Vous devez rentrer le nom de la base SQL.<br/>";
		else if (!@mysql_select_db($valeurs["sql_base"],$escuel) ) {
			$error.="- Base '".$valeurs["sql_base"]."' introuvable<br/>";
		}
		if(empty($valeurs["sql_table"])) $error.="- Vous devez rentrer le nom de la table du shoutbox.<br/>";
		if(empty($valeurs["sql_table2"])) $error.="- Vous devez rentrer le nom de la table de configuration du shoutbox.<br/>";
		if($valeurs["sql_table"]==$valeurs["sql_table2"]) $error.="- Vous devez rentrer une table différente pour le shoutbox et la configuration.<br/>";
		
		if($error) return $error;
		if($sql["table"]!=$valeurs["sql_table"]) sql("ALTER TABLE ".$sql["table"]." RENAME ".$valeurs["sql_table"]."");
		if($sql["table2"]!=$valeurs["sql_table2"]) sql("ALTER TABLE ".$sql["table2"]." RENAME ".$valeurs["sql_table2"]."");
	
		$text="<?\n";
		$text.="// Accès à la base SQL\n";
		$text.="\$sql[\"server\"]=\"".$valeurs["sql_server"]."\";\n";
		$text.="\$sql[\"login\"]=\"".$valeurs["sql_login"]."\";\n";
		$text.="\$sql[\"pass\"]=\"".$valeurs["sql_pass"]."\";\n";
		$text.="\$sql[\"base\"]=\"".$valeurs["sql_base"]."\";\n";
		$text.="\$sql[\"table\"]=\"".$valeurs["sql_table"]."\";\n";
		$text.="\$sql[\"table2\"]=\"".$valeurs["sql_table2"]."\";\n";
		$text.="?>";
		$fp=fopen("config.php","w");
		fwrite($fp,$text);
		fclose($fp);
		
		if(!empty($valeurs["liens"])) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["liens"]."\" WHERE nom=\"liens\"");
		if(!empty($valeurs["mails"])) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["mails"]."\" WHERE nom=\"mails\"");
		
	}
	for($i=1;$valeurs["smileys_".$i];$i++) {
		if($valeurs["smileys_".$i]!="suppr") $smile[$valeurs["smileys_t_".$i]]=$valeurs["smileys_".$i];
	}
		if(!empty($valeurs["new_smileys_t"]) && !empty($valeurs["new_smileys"])) $smile[$valeurs["new_smileys_t"]]=$valeurs["new_smileys"];
	
	foreach($smile as $i => $var) $tab_sm.=$i."=>".$var."=>";

	$tab_sm=substr($tab_sm, 0,(strlen($tab_sm)-2)); 


	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["rep_smileys"]."\" WHERE nom=\"rep_smileys\"");
	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$tab_sm."\" WHERE nom=\"smileys\"");
	
	if(!empty($valeurs["nb_caracp"]) && $valeurs["nb_caracp"]>0) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["nb_caracp"]."\" WHERE nom=\"nb_caracp\"");
	if(!empty($valeurs["nb_carac"]) && $valeurs["nb_carac"]>0) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["nb_carac"]."\" WHERE nom=\"nb_carac\"");
	if(!empty($valeurs["long_max"]) && $valeurs["long_max"]>0) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["long_max"]."\" WHERE nom=\"long_max\"");

	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["scroll"]."\" WHERE nom=\"scroll\"");
	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["nb_posts"]."\" WHERE nom=\"nb_posts\"");
	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["lien_adm"]."\" WHERE nom=\"lien_adm\"");
	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["pl_liens"]."\" WHERE nom=\"pl_liens\"");
	if(!empty($valeurs["nb_mess"]) && $valeurs["nb_mess"]>0) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["nb_mess"]."\" WHERE nom=\"nb_mess\"");
	if(!empty($valeurs["largeur"]) && $valeurs["largeur"]>0) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["largeur"]."\" WHERE nom=\"largeur\"");
	if(!empty($valeurs["hauteur"]) && $valeurs["hauteur"]>0) sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["hauteur"]."\" WHERE nom=\"hauteur\"");
	sql("UPDATE ".$valeurs["sql_table2"]." SET valeur=\"".$valeurs["secu_pseudo"]."\" WHERE nom=\"secu_pseudo\"");
	header("location:".$_SERVER["SCRIPT_NAME"]);
}

function pagination($page,$url,$nb,$nbre) {
	global $sql;
	unset($texte);
	$nb_pages=ceil($nbre/$nb);
	if($nb_pages==1) return false;
	$min=$page-2;
	if($min<=0) $min=1;
	$max=$page+2;
	if ($page>3) $texte.="<a href=\"?".$url."&amp;page=1\">&laquo;</a> ... ";
	for($i=$min;($i<=$max && $i<=$nb_pages);$i++) {
		if($page==$i) $texte.=" <strong>".$i."</strong> ";
		else $texte.=" <a href=\"?".$url."&amp;page=".$i."\">".$i."</a> ";
	}
	if ($i<=$nb_pages) $texte.=" ... <a href=\"?".$url."&amp;page=".$nb_pages."\">&raquo;</a>";
	return $texte;
}
?>