<?php
$template->setFile('centre','partenaires/index.html');
$template->setVar('header.title',' - Partenaires',true);
$site->addCss('templates/'.THEME.'/partenaires/style.css');

$template->setBlock('centre','partenaires');

$res=$sql->query('SELECT nom,url,descr FROM mod_partenaires ORDER BY nom');

while($info=$sql->fetchAssoc($res)) {
	
	$partenaireUrl=$info['url'];
	$partenaireImgUrl='medias/temp/'.$string->clean($partenaireUrl).'.jpg';
	
	if(!file_exists($partenaireImgUrl)) {
		require_once 'include/scripts/Eicc/Webthumb.php';
		@ini_set('max_execution_time',90);
		
		$webthumb = new Eicc_Webthumb();
		$webthumb->setApiKey('f6c74bf7498127c42407a54648e4f0c6');
		$webthumb->addUrl($partenaireUrl,'medium');
		$webthumb->submitRequests();
	
		while (!$webthumb->readyToDownload()) {
			sleep(1);
			$webthumb->checkJobStatus();
		}
	
		$webthumb->fetchToFile($webthumb->urlsToImage[$partenaireUrl]['job'],$partenaireImgUrl,'medium');
	}
	
	$template->setVar(array(
		'partenaireNom'		=>	$info['nom'],
		'partenaireImg'		=>	$partenaireImgUrl,
		'partenaireUrl'		=>	$info['url'],
		'partenaireDescr'	=>	$info['descr']
	));
	
	$template->parse('partenaires',true);
}
?>