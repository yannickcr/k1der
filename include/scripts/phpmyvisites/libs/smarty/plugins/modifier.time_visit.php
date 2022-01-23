<?php
function smarty_modifier_time_visit($ts)
{
	$h =  floor($ts / 3600);
	$ts = $ts + 82800;
	
	$m = (int)strftime("%M", $ts);
	$s = (int)strftime("%S", $ts);
	
	if($m != 0) $return[] = $m;
	$return[] = $s;
	
	// min + sec
	if(sizeof($return) == 2)
	{
		return vsprintf($GLOBALS['lang']['generique_tempsvisite'], $return); 
	}
	// only sec
	else
	{
		return vsprintf($GLOBALS['lang']['visites_sec'], $return); 
	}
}
?>