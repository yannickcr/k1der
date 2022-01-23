<?php

class rss {

	function getInfos($module,$flux) {
		global $site;
		if(file_exists('modules/'.$module.'/rss.inc.php')) require_once('modules/'.$module.'/rss.inc.php');
		else return false;
		
		return $rss[$flux];
	}

}
?>