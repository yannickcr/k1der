<?php
define('ROOT','');
define('CACHE_DIR','cache/');
define('CACHE_LENGTH', 31356000);
define('CONTENT_TYPE','text/css');

// On file le fichier CSS
if(isset($_GET['file'],$_GET['lastmodified'])) {
	$file = preg_replace('/([^a-f0-9])/','',$_GET['file']);
	$lastModified = (int)$_GET['lastmodified'];
	$sLastModified = gmdate('D, d M Y H:i:s', $lastModified).' GMT';

	// Véfifie si il est en cache
	isCached($sLastModified,$lastModified);

	// Si pas en cache
	//$code = file_get_contents(CACHE_DIR.$file.'-'.$lastModified);

	$fp = fopen ('../'.CACHE_DIR.$file.'-'.$lastModified, "r");
	$code = fread ($fp, filesize ('../'.CACHE_DIR.$file.'-'.$lastModified));
	fclose ($fp);


	header('Expires: '.gmdate('D, d M Y H:i:s', time() + CACHE_LENGTH).' GMT');
	header('Content-Type: '.CONTENT_TYPE);
	header('Content-Length: '.strlen($code));
	header("Last-Modified: $sLastModified");
	header("ETag: ".$lastModified);
	header('Cache-Control: max-age='.CACHE_LENGTH);
	echo $code;
	exit();
}
// On file le code du fichier concerné
$name='';
for($j=1;isset($this->css[$j]) && $j<3;$j++) {
	foreach($this->css[$j] as $file) {
		$aLastModifieds[] = filemtime(ROOT.$file);
		$name.= $file;
	}
}
$md5 = md5($name);
if(isset($aLastModifieds)) {
	rsort($aLastModifieds);
	createCacheFile($this->css,$md5.'-'.$aLastModifieds[0]);
	$css=$md5.'-'.$aLastModifieds[0];
}

function createCacheFile($css,$cacheFile) {
	if(file_exists(CACHE_DIR.$cacheFile)) return true;
	$content='';

	for($j=1;isset($css[$j]) && $j<3;$j++) foreach($css[$j] as $file) $content.= '@import "'.ROOT.$file.'";'."\n";
	$fp=fopen(CACHE_DIR.$cacheFile,'w');
	fwrite($fp,$content);
	fclose($fp);
}



function isCached($sLastModified,$lastModified) {
	if (
		(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $sLastModified) ||
		(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $lastModified)
	) {
		header("{$_SERVER['SERVER_PROTOCOL']} 304 Not Modified");
		exit;
	}
}
?>
