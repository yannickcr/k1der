<?php
/* ------------------------------------------------------------- */
// Module CounterStrike Status
// Auteur : Boz (boz@gorets.com)
// D'après les travaux de Henrik Schack Jensen (henrik@schack.dk)
/* ------------------------------------------------------------- */
/*    ATTENTION ! NE MODIFIEZ RIEN EN DESSOUS DE CETTE LIGNE !   */
/* ------------------------------------------------------------- */
require("serveur.php");
require("counterstrike.php");
if(!isset($HTTP_POST_VARS["serveradr"]) AND !isset($HTTP_POST_VARS["serverport"])){
	$serveradr = $ip;
	$serverport= $port;
}
else {
	$serveradr = str_replace(".","",$HTTP_POST_VARS["serveradr"]);
	if(is_numeric($serveradr) AND is_numeric($HTTP_POST_VARS["serverport"])){
    	$serveradr = trim($HTTP_POST_VARS["serveradr"]);
    	$serverport= trim($HTTP_POST_VARS["serverport"]);
	}
	else {
    	$serveradr = $ip;
    	$serverport= $port;
	}
}

$mapdir=array(	"Counter-Strike: Source"=>"css",
				"Deathmatch"=>"hl2dm",
				"Team Deathmatch"=>"hl2dm",
				"Half-Life"=>"hl",
				"Condition Zero"=>"cz",
				"Counter-Strike"=>"cs",
				"Day of Defeat"=>"dod",
				"NS v3.0 beta 5"=>"ns",
				"Team Fortress Classic"=>"tfc",
				"DMC"=>"dmc",
				"Ricochet"=>"r",
				"Digital Paintball"=>"dp",
				"ESF Teamplay"=>"esf",
				"ESF Free Fight"=>"esf",
				"The Trenches"=>"tt",
				"TS Teamplay"=>"ts",
				"TS"=>"ts"
);
/*$gametype=array(	"Counter-Strike: Source"=>"css",
					"Deathmatch"=>"Half-Life²: Deathmatch",
					"Team Deathmatch"=>"hl2dm",
					"Half-Life"=>"hl",
					"Condition Zero"=>"cz",
					"cstrike"=>"Counter-Strike",
					"Day of Defeat"=>"dod",
					"NS v3.0 beta 5"=>"ns",
					"Team Fortress Classic"=>"tfc",
					"DMC"=>"dmc",
					"Ricochet"=>"r",
					"Digital Paintball"=>"dp",
					"ESF Teamplay"=>"esf",
					"ESF Free Fight"=>"esf",
					"The Trenches"=>"tt",
					"TS Teamplay"=>"ts",
					"TS"=>"ts"
);*/


$csinfo = new CounterStrike;
$status = $csinfo->getServerInfo($serveradr,$serverport,1);
/*if ($status && !empty($csinfo->m_servervars["mapname"])) {
    $status = $csinfo->getServerPlayers($serveradr,$serverport,1000);
    $status = $csinfo->getServerRules($serveradr,$serverport,1000);
    $status = $csinfo->getServerStatus($serveradr,$serverport,1000,$rcon);
    $rules = $csinfo->m_serverrules;
	if(isset($csinfo->m_serverstatus)) $type = $csinfo->m_serverstatus;
	else $type = '';
	if(isset($csinfo->m_serverping)) $ping = $csinfo->m_serverping;
	else $ping = 25/1000;
?>
<div class="server">
	<div class="servergame">
		<?php echo $csinfo->m_servervars["gamename"];?><br />
		<img src="images/mappics/<?php if (@filesize("images/mappics/".$mapdir[$csinfo->m_servervars["gamename"]]."/".$csinfo->m_servervars["mapname"].".jpg")!="") echo $mapdir[$csinfo->m_servervars["gamename"]]."/".$csinfo->m_servervars["mapname"]; else echo "nomap"; ?>.jpg" style="border:1px solid black;" width="160" height="80" alt="<?php echo $csinfo->m_servervars["mapname"]?>" title="<?php echo $csinfo->m_servervars["mapname"]?>" /><br />
		<?php echo $csinfo->m_servervars["mapname"]?>
	</div><br />
	<span class="serverinfo">IP </span>: <?php echo $serveradr ?><br />
	<span class="serverinfo">Port </span>: <?php echo $serverport ?><br />
	<span class="serverinfo">Status </span>: <?php echo $type ?><br />
	<span class="serverinfo">Ping </span>: <?php echo round($ping*1000,0); ?>ms<br /><br />
	<span class="serverinfo">Joueurs </span>: <?php echo $csinfo->m_servervars["currentplayers"] ?> sur <?php echo $csinfo->m_servervars["maxplayers"]?><br /><br />
	<span class="serverinfo">Stats </span>: <a href="http://k.online.verygames.net/psychostats/" target="_blank">PsychoStats</a><br />
	<span class="serverinfo">HLTV </span>: <a href="http://213.251.144.10:27015" target="_blank">Jaba TV</a>
</div>
<?php
} else if($status && empty($csinfo->m_servervars["mapname"])) {*/
	include("cssource_class.php");
	$css = new cssource("213.251.146.149","27045");
	//echo $css->status();
?>
<div class="server">
	<div class="servergame">
		<?php echo $css->get_game_type();?>
		<img src="images/mappics/<?php if (@filesize("images/mappics/".$mapdir[$css->get_game_type()]."/".$css->get_map().".jpg")!="") echo $mapdir[$css->get_game_type()]."/".$css->get_map(); else echo "nomap"; ?>.jpg" style="border:1px solid black;" width="160" height="80" alt="<?php echo $css->get_map()?>" title="<?php echo $css->get_map()?>" /><br />
		<?php echo  $css->get_map()?>
	</div><br />
	<span class="serverinfo">IP </span>: <?php echo $css->get_ip() ?><br />
	<span class="serverinfo">Port </span>: <?php echo $css->get_port() ?><br />
	<span class="serverinfo">Ping </span>: <?php echo round($ping*1000,0); ?>ms<br /><br />
	<span class="serverinfo">Joueurs </span>: <?php echo $css->get_num_players() ?> sur <?php echo $css->get_max_players(); ?><br /><br />
	<span class="serverinfo">Stats </span>: <a href="http://chocolat.nitroserv.net/servstats/cs/">PsychoStats</a><br />
</div>
<?php
	/*} else {
?>
<div class="server">
	<div class="servergame">
		Serveur Down !<br />
		C'est abuzer...
	</div>
</div>
<?php
	$down = 1;
} */
?>
