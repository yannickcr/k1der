<?
session_start();
if($login=='' || $pass=='')
	{
	echo "<p>Vous avez oublié de remplir un champs.</p>";
	exit;
	}
include("config.inc.php3");
	if(!($db = mysql_connect($dbhost,$dblogi,$dbpass))) { 	
		echo "Erreur lors de la connexion";
		exit;
	}
	
	//On choisit sa base
	if(!mysql_select_db($dbbase)) {
		echo "Erreur lors de la sélection de la base";
		exit;
	}
$sql = "SELECT pass FROM equipe WHERE kinder='$login'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

$data = mysql_fetch_array($req);

if($data['pass'] != $pass)
	{
	Header("Location: index.php?page=admin&vautrage=1");
	//echo "<p>Mauvais login / password. Merci de recommencer</p>";
	exit;
	}
else
	{
	$login = strtolower($login);
	setcookie("pymembs", "$login");
	session_register('login');
	Header("Location: index.php?page=admin");
	}
?>