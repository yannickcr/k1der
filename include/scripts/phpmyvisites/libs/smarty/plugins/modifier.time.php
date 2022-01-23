<?php
function smarty_modifier_time($ts)
{
	$h =  floor($ts / 3600);
	return setToLength($h, 2) . strftime(":%M:%S", $ts + 82800);
}
?>