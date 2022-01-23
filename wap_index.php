<?php
require('class.mysql.php');
$sql = new Sql();

//Formatage du message avant INSERTION
function replace_ins($text) {
	// On vire les tags html et on met http:// devant les liens
	$tab1=array("http://www.","www.");
	$tab2=array("www.","http://www.");
	$text = str_replace($tab1,$tab2,strip_tags($text));
	$text = substr($text, 0,90);
	// On retourne le texte avec des slashes pour éviter les erreurs
	return addslashes($text);
}

if(isset($_GET['page']) && $_GET['page']=='reply') {
	$info = $sql->fetchArray($sql->query("SELECT mess FROM shoutbox ORDER BY id DESC LIMIT 0,1"));
	$mess_prec = $info["mess"];
	$text = replace_ins($_GET["message"]);
	$pseudo = addslashes(strip_tags($_GET["pseudo"]));
	$pseudo = substr($pseudo,0,15);
	setcookie("shoutbox_pseudo",$pseudo,time()+62208000);
	$times = date('U');
	if($mess_prec!=$text && !empty($text) && $text!="message" && !empty($pseudo)) $sql->query("INSERT INTO shoutbox (ip,timestamp,pseudo,mess) VALUES (\"".$_SERVER["REMOTE_ADDR"]."\",\"".$times."\",\"".$pseudo."\",\"".$text."\")");
	header("Cache-control: private, no-cache");
	header("location:index.php?page=chat");
}

header("Cache-control: private, no-cache");
header("Content-Type: text/vnd.wap.wml");
echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>';

?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
<wml>
<?php
if(isset($_GET['page']) && $_GET['page']=='chat') {
	if(isset($_GET['p'])) $i=$_GET['p'];
	else $i=0;
?>
<card id="chat">
	<do type="options" label="Menu">
		<go href="#menu"/>
	</do>
	<do type="accept" label="Répondre">
		<go href="#reply"/>
	</do>
	<do type="accept" label="Suite">
		<go href="index.php?page=chat&amp;p=<?php echo ($i+1); ?>"/>
	</do>
	<do type="prev">
		<prev/>
	</do>
	<p align="center" mode="nowrap">
		<img src="images/logo.wbmp" alt="K1der"/><br /><br />
		<b><small>Rapid'Chat</small></b>
	</p>
	<p mode="wrap">
		<small>
		<?php
		$res=$sql->query('SELECT pseudo,mess FROM shoutbox ORDER BY timestamp DESC,id DESC LIMIT '.($i*10).',10');
		while($info=$sql->fetchArray($res)) {
			echo '<i>'.$info['pseudo'].'</i> : '.$info['mess'].'<br />';
		}
		?>........<br />
		<a href="index.php?page=chat&amp;p=<?php echo ($i+1); ?>">Suite</a><br />
		<a href="#reply">Répondre</a>
		</small>
	</p>
</card>
<card id="reply">
	<do type="options" label="Menu">
		<go href="#menu"/>
	</do>
	<do type="accept" label="Valider">
		<go href="index.php" method="get">
			<postfield name="page" value="reply"/>
			<postfield name="pseudo" value="$(pseudo)"/>
			<postfield name="message" value="$(message)"/>
		</go>
	</do>
	<do type="prev">
		<prev/>
	</do>
	<p align="center" mode="nowrap">
		<img src="images/logo.wbmp" alt="K1der"/><br /><br />
		<b><small>Répondre sur le Rapid'Chat</small></b>
	</p>
	<p mode="wrap">
		Pseudo :<br/>
		<input type="text" name="pseudo" size="8" format="15M" /><br />
		Message :<br/>
		<input type="text" name="message" size="15" format="90M" />
	</p>
</card>
<?php
} else if(isset($_GET['page']) && $_GET['page']=='episode') {
?>
<card id="episode">
	<do type="options" label="Menu">
		<go href="#menu"/>
	</do>
	<do type="prev">
		<prev/>
	</do>
	<p align="center" mode="nowrap">
		<img src="images/logo.wbmp" alt="K1der"/><br /><br />
		<?php
		$res=$sql->query('SELECT nom,valeur FROM config WHERE nom="episode" OR nom="ep_scen" OR nom="ep_story" OR nom="ep_flash" OR nom="ep_date" ORDER BY nom');
		while($info=$sql->fetchArray($res)) {
			$$info['nom']=$info['valeur'];
		}
		?>
		<b><small><?php echo $episode; ?></small></b>
	</p>
	<p mode="wrap">
		<small>
		 Scénario : <?php echo $ep_scen; ?>%<br />
		 Story Board : <?php echo $ep_story; ?>%<br />
		 Flash : <?php echo $ep_flash; ?>%<br /><br />
		 
		 Avancement total : <?php echo round(($ep_scen/20)+($ep_story/6.6666666666666666666666667)+($ep_flash/1.25),0); ?>%<br /><br />
		 
		 <?php if($ep_date>0) $ep_date=date('d/m/Y',$ep_date); ?>
		 Date de sortie : <?php echo $ep_date; ?>
		</small>
	</p>
</card>
<?php
} else if(isset($_GET['page']) && $_GET['page']=='cbl') {
include_once 'clteamstats.php';
?>
<card id="cbl">
	<do type="options" label="Menu">
		<go href="#menu"/>
	</do>
	<do type="prev">
		<prev/>
	</do>
	<p align="center" mode="nowrap">
		<img src="images/logo.wbmp" alt="K1der"/><br /><br />
		<b><small>Classement Cyberleagues</small></b>
	</p>
	<p mode="wrap">
		<small>Classement Cyberleagues : <?php echo $team['TeamPosition']; ?>ème</small>
	</p>
</card>
<?php
}
?>
<card id="menu">
	<do type="prev">
		<prev/>
	</do>
	<p align="center" mode="nowrap">
		<img src="images/logo.wbmp" alt="K1der"/><br /><br />
		<b><small>Menu</small></b>
	</p>
	<p mode="nowrap">
		<a href="index.php?page=chat">Rapid'Chat</a><br />
		<a href="index.php?page=episode">Avancement Episode</a><br />
		<a href="index.php?page=cbl">Classement Cyberleagues</a><br />
	</p>
</card>
</wml>
<?php
exit();
?>