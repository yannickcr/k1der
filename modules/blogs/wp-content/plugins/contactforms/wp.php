<?php
if (defined('ABSPATH')) { 

	require_once(ABSPATH . WPINC . '/class-snoopy.php');
		
		
	function cforms_info($show='') {
		switch($show) {
	    case 'localversion' :
	    	$info = 530;
	    	break;
	    case 'remoteversion':
	    	$info = 'http://www.deliciousdays.com/download/cforms.txt';
	     	break;
	     default:
	     	$info = '';
	     	break;
	     }
	    return $info;
	}
	
	function cforms_remote_version_check() {
		if (class_exists('Snoopy')) {
			$cforms_client = new Snoopy();
			$cforms_client->_fp_timeout = 10;
			if (@$cforms_client->fetch(cforms_info('remoteversion')) === false) {
				return -1;
			}
		   	$remote = $cforms_client->results;
	   		if (!$remote || strlen($remote) > 8 ) {
				return -1;
			}
			if (intval($remote) > intval(cforms_info('localversion'))) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	
} // if (defined('ABSPATH'))
?>
