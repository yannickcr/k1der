<?
if($_POST) {
	$texte="<?php\n";
	foreach ($_POST as $i => $var) if($i!="strat" && $i!="submit") $texte.="\$strat[$i]=\"$var\";\n";
	$texte.="?>";
	$nom = strtolower(eregi_replace("[[:punct:][:space:]]","",$_POST["nom"]));
	if(empty($nom) && !$_GET["selection"]) {
		$k=1;
		$dir = opendir("strats");
		while($fichier = str_replace(".php","",readdir($dir))) {
			if(ereg("strat_",$fichier)) $k++;
		}
		closedir($dir);
		$nom="strat_".$k;
	} else if ($_GET["selection"]) $nom=$_GET["selection"];
	$fp = fopen("strats/".$nom.".php","w");
	fwrite($fp,$texte);
	fclose($fp);
	if($_POST["submit"]=="Enregistrer la strat") {
		$message="<div style=\"margin:20px;font-size:17px;color:#00CC00;\">Strat sauvegardée</div>";
		$selection=$nom;
	} else if($_POST["submit"]=="Supprimer la strat") {
		@unlink("strats/".$nom.".php");
		$message="<div style=\"margin:20px;font-size:17px;color:#CC0000;\">Strat supprimée</div>";
	} else if($_POST["submit"]=="Convertir en image") {
		include ("strats/".$nom.".php"); 
		header("content-type: image/jpeg"); 
		
		$image = imagecreatefrompng("images/overviews/".$strat["map"]); 
		$size = getimagesize("images/overviews/".$strat["map"]); 
		
		$tc = imagecolorallocate ($image, 0, 0, 0);
		$fontname="/home/.sites/65/site113/web/strator/police/verdanab.ttf";
		imagettftext ($image, 14, 0, 5, 20, $tc, $fontname, $strat["nom"]);
		
		$k=1;
		while($strat["k".$k."_x"]) {
			if($strat["k".$k."_nom"]) {
				$watermark = imagecreatefromgif("images/".$strat["k".$k."_nom"].".gif"); 
				$watermark_width = imagesx($watermark); 
				$watermark_height = imagesy($watermark); 
				imagecopymerge($image, $watermark, $strat["k".$k."_x"]-100, $strat["k".$k."_y"]-10, 0, 0, $watermark_width, $watermark_height, 100); 
			} else if($strat["k".$k]) imagettftext ($image, 10, 0, $strat["k".$k."_x"]-100, $strat["k".$k."_y"], $tc, $fontname, $strat["k".$k]);
			$k++;
		}
		$nom=$strat["nom"];
		if(empty($nom) && !$_GET["selection"]) {
			$k=1;
			$dir = opendir("strats");
			while($fichier = str_replace(".php","",readdir($dir))) {
				if(ereg("strat_",$fichier)) $k++;
			}
			closedir($dir);
			$nom="strat_".$k;
		} else if ($_GET["selection"]) $nom=$_GET["selection"];
		imagejpeg($image,"strats/".$nom.".jpg",100); 
		imagedestroy($image); 
		imagedestroy($watermark);
		$html="<img src=\"strats/".$nom.".jpg\" alt=\"\" />";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>K1der : Truc pour faire des strats (test)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
if(($_GET["selection"] || $selection) && $_POST["submit"]!="Supprimer la strat" && $_POST["submit"]!="Convertir en image") {
	if(!empty($_GET["selection"])) $selection=$_GET["selection"];
	include("strats/".$selection.".php");
	$k=1;
	$html="";
	$hidden="";
	$nom=$strat["nom"];
	while($strat["k".$k."_x"]) {
		if($strat["k".$k."_nom"]) {
			$html.="<img class=\"drag\" style=\"top:".$strat["k".$k."_y"]."px;left:".$strat["k".$k."_x"]."px;\" id=\""."k".$k."\" src=\"images/".$strat["k".$k."_nom"].".gif\" alt=\"".$strat["k".$k."_nom"]."\" />\n";
			$hidden.="<input type=\"hidden\" name=\"k".$k."_nom\" value=\"".$strat["k".$k."_nom"]."\">
			<input type=\"hidden\" name=\"k".$k."_x\" id=\"k".$k."_x\" value=\"".$strat["k".$k."_x"]."\">
			<input type=\"hidden\" name=\"k".$k."_y\" id=\"k".$k."_y\" value=\"".$strat["k".$k."_y"]."\">";
		} else if($strat["k".$k]) {
			$html.="<input class=\"drag\" style=\"top:".$strat["k".$k."_y"]."px;left:".$strat["k".$k."_x"]."px;\" onkeyup=\"javascript:adaptsize('k".$k."');\" type=\"text\" id=\""."k".$k."\" name=\""."k".$k."\" size=\"".strlen($strat["k".$k])."\" value=\"".$strat["k".$k]."\" />\n";
			$hidden.="<input type=\"hidden\" name=\"k".$k."_x\" id=\"k".$k."_x\" value=\"".$strat["k".$k."_x"]."\">
			<input type=\"hidden\" name=\"k".$k."_y\" id=\"k".$k."_y\" value=\"".$strat["k".$k."_y"]."\">";
		}
		$k++;
	}
	$map=$strat["map"];
} else if($_POST["submit"]!="Convertir en image") {
	$nom="";
	$html="";
	$hidden="";
	$map="de_dust2.png";
	$k=1;
}
if($_POST["submit"]!="Convertir en image") {
?>
<style type="text/css">
<!--
body {
	background-image:url(images/overviews/<?=$map;?>);
	background-repeat:no-repeat;
	background-position:100px 10px;
	height:888px;
	margin:0px;
	padding:0px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
}
form {
	margin:0px;
	padding:0px;
}
.drag{
	position:absolute;
	cursor:hand;
}
.click{
	position:relative;
	cursor:hand;
}
#boite {
	height:80px;
	width:80px;
	vertical-align:bottom;
	border:1px solid black;
	margin:5px;
}
-->
</style>

<script language="javascript" type="text/javascript">
<!--
var dragapproved=false
var z,x,y
var k=<?=$k;?>;
function move(){
    if (event.button==1&&dragapproved){
        z.style.pixelLeft=temp1+event.clientX-x;
        z.style.pixelTop=temp2+event.clientY-y;
		document.getElementById(z.id+"_x").value=z.style.pixelLeft;
		document.getElementById(z.id+"_y").value=z.style.pixelTop;
        return false
    }
}
function drags(){
	/*if(event.target) var elem=event.target;
	else if(event.srcElement) var elem=event.srcElement;*/
	var elem = event.srcElement;
	
    if (elem.className=="click"){
		if(elem.id!="clickme") document.getElementById("boite").innerHTML+="<input type=\"hidden\" name=\"k"+k+"_nom\" value=\""+elem.alt+"\"><input type=\"hidden\" name=\"k"+k+"_x\" id=\"k"+k+"_x\" value=\"\"><input type=\"hidden\" name=\"k"+k+"_y\" id=\"k"+k+"_y\" value=\"\"><img class=\"drag\" id=\"k"+k+"\" src=\"images/"+elem.alt+".gif\" alt=\""+elem.alt+"\" />";
    	else document.getElementById("boite").innerHTML+="<input type=\"hidden\" name=\"k"+k+"_x\" id=\"k"+k+"_x\" value=\"\"><input type=\"hidden\" name=\"k"+k+"_y\" id=\"k"+k+"_y\" value=\"\"><input class=\"drag\" onkeyup=\"javascript:adaptsize('k"+k+"');\" type=\"text\" id=\"k"+k+"\" name=\"k"+k+"\" size=\"1\" />";
		k++;
	}
    if (elem.className=="drag"){
        dragapproved=true;
        z=elem;
        temp1=z.style.pixelLeft;
        temp2=z.style.pixelTop;
        x=event.clientX;
        y=event.clientY;
        document.onmousemove=move;
    }
}

function findrag() {
	if(event.srcElement.style.pixelTop<=10 && event.srcElement.style.pixelLeft>275 && event.srcElement.style.pixelLeft<315) {
		event.srcElement.style.pixelLeft=-1000;
		event.srcElement.style.pixelTop=-1000;
	} 
	new Function("dragapproved=false");
}

document.onmousedown=drags;
document.onmouseup=findrag;

function changeback() {
	var fond=document.getElementById("map").value;
	document.body.style.backgroundImage="url(images/overviews/"+fond+")";
}

function adaptsize(champ) {
	var valeur=document.getElementById(champ).value;
	var longueur=document.getElementById(champ).size;
	if(valeur.length>(longueur+1)) document.getElementById(champ).size=longueur+1;
	else if(valeur.length<(longueur-1)) document.getElementById(champ).size=longueur-1;
}
//-->
</script>
<? } else { ?>
<style type="text/css">
<!--
body {
	margin:0px;
	padding:0px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	text-align:center;
}
-->
</style>
<? } ?>
</head>
<body>
<?php
if($_POST["submit"]!="Convertir en image") {
	$maps=array("de_aztec","de_cbble","de_dust","de_dust2","de_inferno","de_nuke","de_prodigy","de_train");
	?>
	<form method="post" action="">
	Nom de la stratégie : <input type="text" name="nom" value="<?=$nom;?>" /><img style="margin-left:20px; vertical-align:middle;" src="images/corbeille.gif" alt="Corbeille, glissez un élément dessus pour le supprimer" /><br/>
	Map :
	<select id="map" name="map" onchange="javascript:changeback();">
		<?php
		foreach($maps as $i=>$var) {
			unset($sel);
			if($var.".png"==$map) $sel=" selected=\"selected\"";
			echo "<option value=\"".$var.".png\"".$sel.">".$var."</option>\n";
		}
		?>
	</select>
	<div style="float:left;" id="boite"><?=$html;?></div>
	<div style="float:left;">
	<input style="margin-top:5px; width:135px;" type="submit" name="submit" value="Enregistrer la strat" /><br />
	<input style="margin-top:5px; width:135px;" type="submit" name="submit" value="Supprimer la strat" /><br />
	<input style="margin-top:5px; width:135px;" type="submit" name="submit" value="Convertir en image" />
	</div>
	<?=$hidden;?>
	</form>
	<form method="get" action="">
	<div style="position:absolute; top:0px; right:0px; ">
	<?=$message;?>
	Charger une strat : <select id="selection" name="selection" onchange="submit();">
		<option value="">------</option>
		<?php
			$dir = opendir("strats");
			while($fichier = str_replace(".php","",readdir($dir))) {
				if($fichier!="." && $fichier!=".." && !ereg(".jpg",$fichier)) echo "<option value=\"".$fichier."\">".$fichier."</option>\n";
			}
			closedir($dir);
		?>
	</select>
	</div>
	</form><br /><br /><br /><br /><br /><br /><br />
	<?php
	$k1der=array("Chocobon","Chocofresh","Chocolat","Circus","Country","Delice","Lapin","Maxi King","Maxi Mix",
	"Maxi Surprise","Maxi","Noel","Pingui","Surprise","Venice","ak","m4","awp","he","flash","smoke","c4");
	$k=0;
	foreach($k1der as $i=>$var) {
		echo "<img class=\"click\" src=\"images/".$var.".gif\" alt=\"".$var."\" />\n";
		if($k%2) echo "<br/>";
		$k++;
	}
	echo "<input type=\"text\" id=\"clickme\" class=\"click\" size=\"1\" />\n";
} else echo "Lien : <a href=\"http://www.k1der.net/strator/strats/".$nom.".jpg\">http://www.k1der.net/strator/strats/".$nom.".jpg</a><br/>".$html;
?>
</body>
</html>