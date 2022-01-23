<?php
$rss=array(
	'matches'	=>	array('Derniers matches','matchesDerniers','		
		SELECT 
			id id,
			adversaire title,
			date date,
			"'.$site->config('clan_default').'" creator,
			"" description,
			"" content
		FROM 
			mod_matches
		ORDER BY
			date DESC,
			id DESC
		LIMIT 
			0,10
'));

function matchesDerniers($info) {
	global $string,$site;
	$cleanClan		=$string->clean($site->config('clan_default'));
	$cleanAdversaire=$string->clean($info['title']);
	return dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/matches/'.$cleanClan.'-vs-'.$cleanAdversaire.'-id'.$info['id'].'.html';
}
?>