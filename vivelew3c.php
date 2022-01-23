<?php
if(isset($_SERVER["HTTP_ACCEPT"]) && stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	$contentType='application/xhtml+xml; charset=iso-8859-1';
	echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">');
} else {
	$contentType='text/html; charset=iso-8859-1';
	echo ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Vive le W3c</title>
<meta http-equiv="Content-Type" content="<?php echo $contentType; ?>" />
<meta http-equiv="content-language" content="fr" />
</head>
<body>
<div id="contenu">Ceci est une feinte pour faire semblant d'être valide :p</div>
</body>
</html>
<?php
exit();
?>
