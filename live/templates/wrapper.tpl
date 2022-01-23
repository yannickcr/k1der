<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>CS:Source Server Query</title>
<style type="text/css">
<!--
BODY {
	margin: 10px;
	background: #52A5F2;
	color: #666666;
	font: normal 12px Arial, Helvetica, sans-serif;
}

.main {
	margin-bottom: 25px;
	background: #DDDDDD;
	border: 2px solid #FFFFFF;
}

.main TD.title {
	background: #2A6DAC;
	color: #DBF0FE;
	font: bold 13px Arial, Helvetica, sans-serif;
	letter-spacing: -1px;
	text-transform: uppercase;
}

.main TD {
	background: #F7F7F7;
}

#wrapper {
	width: 500px;
	margin: auto;
}

.full {
	color: #A20000;
}

.notfull {
	color: #44C32B;
}

INPUT {
	font: normal 11px Arial, Helvetica, sans-serif;
	background: #FFFFFF;
	border: 1px solid #AAAAAA;
}

INPUT.submit {
	background: #DDDDDD;
	border: 1px solid #AAAAAA;
}
-->
</style>
</head>

<body>
<div id="wrapper">
	<table class="main" width="100%" border="0" cellpadding="3" cellspacing="1">
		<tr>
			<td class="title">query a server</td>
	    </tr>
		<form action="index.php" method="GET">
		<tr>
			<td>
			<input name="ip" type="text" size="25" /> : <input name="port" type="text" size="10" /> <input class="submit" type="submit" value="Query" />
			</td>
		</tr>
		</form>
	</table>
	<table class="main" width="100%" border="0" cellpadding="3" cellspacing="1">
		<tr>
			<td colspan="2" class="title">server details</td>
	    </tr>
		<tr>
			<td colspan="2"><img src="maps/de_dust.jpg" /></td>
		</tr>
		<tr>
			<td width="15%">Name</td>
			<td width="85%">{$hostname}</td>
		</tr>
		<tr>
			<td>Address</td>
			<td>{$ip}:{$port}</td>
		</tr>
		<tr>
			<td>Map</td>
			<td>{$map}</td>
		</tr>
		<tr>
			<td>Game</td>
			<td>Counter-Strike Source</td>
		</tr>
		<tr>
			<td>Players</td>
			<td><span class="{$player_status}">{$num_players}/{$max_players}</span> ({$bot_players} Bots)</td>
		</tr>
		<tr>
			<td>Password</td>
			<td>{$needpass}</td>
		</tr>
		<tr>
			<td>Protocol</td>
			<td>{$net_protocol}</td>
		</tr>
		<tr>
			<td>Dedicated</td>
			<td>{$dedicated}</td>
		</tr>
		<tr>
			<td>Server OS</td>
			<td>{$server_os}</td>
		</tr>
	</table>
	<table class="main" width="100%" border="0" cellpadding="3" cellspacing="1">
		<tr>
			<td colspan="3" class="title">currently playing</td>
	    </tr>
		<tr>
			<td><b>Name</b></td>
			<td><b>Frags</b></td>
			<td><b>Time</b></td>
		</tr>
{$players}
	</table>
	<table class="main" width="100%" border="0" cellpadding="3" cellspacing="1">
		<tr>
			<td colspan="2" class="title">rules</td>
	    </tr>
{$rules}
	</table>
	<div style="text-align: center; color: #DBF0FE;">Query generated using noginn's CS:Source query class.</div>
</div>
</body>
</html>
