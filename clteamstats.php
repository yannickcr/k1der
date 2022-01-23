<?php
/*	clTeamStats(teamid,stats)
 *	-	Récupère les informations Cyberleagues de la team via le script de K1der.net
 *
 *	Auteur: Country
 */
function clTeamStats($teamid,&$stats) {
	// Liste des champs
	$tags=array(
		"TeamID",
		"TeamName",
		"TeamTag",
		"TeamPosition",
		"TeamPositionMoved",
		"TeamPointsMoved",
		"TeamPoints",
		"LastUpdate"
	);
	// Initialisation du parseur et lecture du fichier XML
	$data=@implode("",@file("http://www.k1der.net/clrank.php?id=".$teamid));
	$parser=xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parse_into_struct($parser,$data,$values,$index);
	xml_parser_free($parser);
	for ($i=0;$i<sizeof($tags);$i++) $stats[$tags[$i]]=$values[$index[$tags[$i]][0]]['value'];
}

// Exemple d'utilisation

clTeamStats(228,$team);

/*echo 'L\'équipe <a href="http://www.cyberleagues.fr/main.php?d=1&c=teams&s=profile&ID_Club='.$team['TeamID'].'">'.$team['TeamName'].'</a> 
(Tag: '.$team['TeamTag'].') est classée '.$team['TeamPosition'].'ème avec '.$team['TeamPoints'].' points 
(position précédente: '.$team['TeamPositionMoved'].'ème avec '.$team['TeamPointsMoved'].' points). 
Dernière mise à jour: '.$team['LastUpdate'];*/
?>

