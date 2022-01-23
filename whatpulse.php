<?
function explorer($url) {
	ob_start();
	readfile($url);
	$code = ob_get_contents();
	ob_end_clean();
	return $code;
}
$find=0;
$k=0;
	while($find==0) {
		$code = explorer("http://pulse.whatnet.org/stats/teams.php?page=".$k);
		$code = explode("\n",$code);
		for($i=0;$code[$i] && $find==0;$i++) {
			if(ereg("team.php",$code[$i])) {
				if(ereg("K1der",$code[$i])) {
					$ligne = trim(eregi_replace("<[^>]*>","|",$code[$i]))."\n";
					while(ereg("\|\|",$ligne)) $ligne = str_replace("||","|",$ligne);
					$var = explode("|",$ligne);
					echo "le clan ".$var[2]." est ".$var[1]."ème";
					$find=1;
				}
			}
		}
		$k++;
	}
?>