<?
/*----------------------------------------
            K1der Shoutbox 1.7 Beta7
               par Country
              www.k1der.net
----------------------------------------*/

// Description : index du shoutbox (ne contenant que l'iframe correctement dimentionnée)

$url=$_SERVER["SCRIPT_FILENAME"];
$url2 =$_SERVER['HTTP_REFERER'];
if (!$url2) {
	$url2 = $_SERVER['SCRIPT_NAME'];
	if ($url2 == '') {
		$url2 == '/';
	}
	$url2 = 'http://'.$_SERVER['SERVER_NAME'].$url2; 
} 
$path=pathinfo($url);
$path2=pathinfo($url2);
if(!file_exists($path["dirname"]."/config.php")) header("location:".$path["dirname"]."/install.php");
else include $path["dirname"]."/config.php";
include $path["dirname"]."/include/fonctions.php";
$req=sql("SELECT nom,valeur FROM ".$sql["table2"]." WHERE nom=\"hauteur\" or nom=\"largeur\"");
while($info=mysql_fetch_array($req)) $$info["nom"]=$info["valeur"];
?>
<!-- Début K1der Shoutbox 1.7 Beta7 | www.k1der.net -->
<iframe src="<?=$path2["dirname"];?>/board.php" frameborder="0" scrolling="no" width="<?=$largeur;?>" height="<?=$hauteur;?>"></iframe>
<!-- Fin K1der Shoutbox 1.7 Beta7 | www.k1der.net -->