<?php
function getCyberleaguesInfos($id,$start=1) {
	$fp = @fopen('http://www.cyberleagues.fr/main.php?d=1&c=rankings&s=global_ranking&start='.$start.'&length=20','r'); //lecture du fichier
	$record=0;
	while(!feof($fp)) { //on parcourt toutes les lignes
		$tmp=fgets($fp,4096); // lecture du contenu de la ligne
		if($record==1) {
			if(ereg('class="Position"',$tmp)) $team['position']=strip_tags(trim($tmp));
			if(ereg('class="Name"',$tmp)) {
				$tmp2=ereg_replace("<td class=\"Name\"><a href='main.php\?d\=1\&c=teams\&s=profile\&ID_Club\=|</a></td>",'',trim($tmp));
				$tmp3=explode("'>",$tmp2);
				$team['id']=$tmp3[0];
				$team['name']=$tmp3[1];
			}
			if(ereg('class="Team"',$tmp)) $team['tag']=strip_tags(trim($tmp));
			if(ereg('class="PositionMoved"',$tmp)) {
				$tab1=array(
					' ; Points ',
					'<td class="PositionMoved">',
					' alt="Position ',
					'<img src=\'img/icons/icon_up.gif\'',
					'<img src=\'img/icons/icon_down.gif\'',
					'<img src=\'img/icons/icon_still.gif\'',
					'<img src=\'img/icons/icon_new.gif\'',
					'"/></td>',
					'/></td>'
				);
				$postemp=str_replace($tab1,array(';',''),trim($tmp));
				$postemp=explode(';',$postemp);
				if(ereg('Position ',$tmp)) $team['positionmoved']=$postemp[0];
				if(ereg('Points ',$tmp) && ereg('Position ',$tmp)) $team['pointsmoved']=$postemp[1];
				else if(ereg('Points ',$tmp)) $team['pointsmoved']=$postemp[0];
				if(!isset($team['positionmoved'])) $team['positionmoved']=0;
				if(!isset($team['pointsmoved'])) $team['pointsmoved']=0;
			}
			if(ereg('class="Points"',$tmp)) $team['points']=trim(strip_tags($tmp));
			if(ereg('class=\'LastUpdate\'',$tmp)) $last_update=str_replace(': ','',strstr(strip_tags(trim($tmp)),':'));
			if(trim($tmp)=='</tr>') {
				$teams[$team['id']]=array(
					'id'=>$team['id'],
					'name'=>$team['name'],
					'tag'=>$team['tag'],
					'position'=>$team['position'],
					'positionmoved'=>$team['positionmoved'],
					'pointsmoved'=>$team['pointsmoved'],
					'points'=>$team['points']
				);				
			}
		}
		if(ereg('class="ligne1"',$tmp)) $record=1;
		if(ereg('class=\'LastUpdate\'',$tmp)) $record=0;
		if(ereg('Le classement est actuellement vide',$tmp)) return false;
	}
	foreach($teams as $i=>$var) $teams[$i]['lastUpdate']=$last_update;
	fclose($fp); //on ferme
	while(!isset($teams[$id]) && $teams!=false) $teams=getCyberleaguesInfos($id,($start+20));
	return $teams;
}

header('Content-type: application/xml');
// Récupération de l'ID de la team
if(!isset($_GET['id'])) $id=228;
else $id=(int)$_GET['id'];

// Vérification du cache
$cache='cache_cyberleagues/'.$id.'.xml';
$expire=time();
if(file_exists($cache) && (filemtime($cache)+3600*12) > $expire) readfile($cache);
else {
	$teams=getCyberleaguesInfos($id);
	if(isset($teams[$id])) {
		ob_start();
echo "<?xml version=\"1.0\"?>\n";
?>
<ClassementTeam>
 <TeamID><?php echo $teams[$id]['id']; ?></TeamID>
 <TeamName><?php echo $teams[$id]['name']; ?></TeamName>
 <TeamTag><?php echo $teams[$id]['tag']; ?></TeamTag>
 <TeamPosition><?php echo $teams[$id]['position']; ?></TeamPosition>
 <TeamPositionMoved><?php echo $teams[$id]['positionmoved']; ?></TeamPositionMoved>
 <TeamPointsMoved><?php echo $teams[$id]['pointsmoved']; ?></TeamPointsMoved>
 <TeamPoints><?php echo $teams[$id]['points']; ?></TeamPoints>
 <LastUpdate><?php echo $teams[$id]['lastUpdate']; ?></LastUpdate>
</ClassementTeam>
<?php
		$page=ob_get_contents();
		ob_end_clean();
		$fp = @fopen($cache,'w');
		@fputs($fp,$page);
		@fclose($fp);
		echo $page;
	}
} 
?>