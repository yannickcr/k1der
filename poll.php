<?php
$requete  = "SELECT * FROM sondages ORDER BY id DESC LIMIT 0,1";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());  
$disp = mysql_fetch_array($req);

$ruquete  = "SELECT * FROM config WHERE nom='badip'";
$ruq = mysql_query($ruquete) or die('Erreur SQL !<br/>'.$ruquete.'<br/>'.mysql_error());  
$dusp = mysql_fetch_array($ruq);
if(!isset($vote)) $vote=0;
$dusp['badip']=$dusp['valeur'];

if ((isset($_COOKIE['sondage']) && $_COOKIE['sondage'] == $disp['id']) or ($vote == '1') or ($dusp['badip'] == $_SERVER['REMOTE_ADDR'])) $style='pollgris';
else $style='poll';
?> 
<form method="post" class="<?php echo $style; ?>" action="index.php?page=sondage">
	<input name="vote" type="hidden" value="1" />
	<input name="sondage_id" type="hidden" value="<?php echo $disp['id']; ?>" />
	<div class="polltxt"><?php echo $disp['titre']; ?></div><br />
	<?php
	$i=3;
	$j=1;
	while($disp['nb'] > 0) {
	?>
	<input type="radio" name="le_vote" value="<?php echo $i; ?>" <?php if ($style=='pollgris') echo "disabled=\"disabled\""; ?> />
	<?php
	echo $disp[$i].'<br />';
	$i++;
	$j++;
	$disp['nb']--;
	}
	?>
	<?php if ($style=='pollgris') echo "<div class=\"polltxt\">Merci d'avoir voté</div><br />"; ?>
	<div class="pollend">
	<input type="submit" name="Submit" value="Valider" <?php if ($style=='pollgris') echo "disabled=\"disabled\""; ?> /><br /><br />
	<a href="index.php?page=sondage" target="_blank">Voir les r&eacute;sultats</a><br />
	<a href="index.php?page=sondage&amp;old=1" target="_blank">Anciens sondages</a>
	</div>
</form>