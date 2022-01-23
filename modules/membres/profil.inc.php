<?php
$bbcode = new bbcode;

/**
 * Construction de la page
 */
$template->setFile('centre','membres/profil.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$pseudo=eregi_replace("[^A-Z0-9]",'',$_GET['membre']);

$info=$sql->fetchAssoc($sql->query('
	SELECT 
		mod_membres.id,
		pseudo,
		clan_id,
		clan_nom,
		mail,
		avatar,
		mod_membres.nom,
		prenom,
		natio,
		posts,
		www,
		msn,
		aim,
		yahoo,
		icq,
		skype,
		xfire,
		gtalk,
		hard_1,
		hard_2,
		hard_3,
		hard_4,
		hard_5,
		hard_6,
		hard_7,
		date_nes,
		date_ins,
		last_visit,
		groupes.name AS groupe,
		signature
	FROM 
		mod_membres LEFT JOIN groupes ON groupe=groupes.id
	WHERE 
		pseudo="'.$pseudo.'"
'));
$site->addToTitle(' - Profil de '.$info['pseudo']);

$defTxt='>Aucune information';

if(!empty($info['msn'])) {
	$template->setBlock('centre','msn1');
	$template->setBlock('centre','msn2');
	$template->parse('msn1',true);
	$template->parse('msn2',true);
}
if(!empty($info['yahoo'])) {
	$template->setBlock('centre','yahoo1');
	$template->setBlock('centre','yahoo2');
	$template->parse('yahoo1',true);
	$template->parse('yahoo2',true);
}
if(!empty($info['xfire'])) {
	$template->setBlock('centre','xfire1');
	$template->setBlock('centre','xfire2');
	$template->parse('xfire1',true);
	$template->parse('xfire2',true);
}
if(!empty($info['skype'])) {
	$template->setBlock('centre','skype1');
	$template->setBlock('centre','skype2');
	$template->parse('skype1',true);
	$template->parse('skype2',true);
}
if(!empty($info['gtalk'])) {
	$template->setBlock('centre','gtalk1');
	$template->setBlock('centre','gtalk2');
	$template->parse('gtalk1',true);
	$template->parse('gtalk2',true);
}
if(!empty($info['natio'])) {
	$template->setBlock('centre','natio1');
	$template->setBlock('centre','natio2');
	$template->parse('natio1',true);
	$template->parse('natio2',true);
}
if(!empty($info['www'])) {
	$template->setBlock('centre','www1');
	$template->setBlock('centre','www2');
	$template->parse('www1',true);
	$template->parse('www2',true);
}

$membreStatsMoreAct = $membres->moreAct($info['id']);
$lastAct=$membres->lastAct($info['id']);
if($string->formatDate('%Y',$lastAct)==1970) $lastAct='Inconnue';
else $lastAct=$string->formatDate('%A %d %B %Y',$lastAct,true).'  '.$string->formatDate('%H:%M',$lastAct);

if(!empty($info['clan_nom']) && !ereg('postul_',$info['clan_nom'])) {
	$template->setBlock('centre','clan1');
	$template->setBlock('centre','clan2');
	$template->parse('clan1',true);
	$template->parse('clan2',true);
	$template->setVar('membreClan',$info['clan_nom']);
} else $template->setVar('membreClan','>Aucun');


$natio=array('za'=>'Afrique du Sud','dz'=>'Algerie','de'=>'Allemagne','sa'=>'Arabie Saoudite','ar'=>'Argentine','amM'=>'Armnie','au'=>'Australie','at'=>'Autriche','be'=>'Belgique','by'=>'Bilarussie','br'=>'Brsil','bg'=>'Bulgarie','ca'=>'Canada','cl'=>'Chili','cn'=>'Chine','co'=>'Colombie','kr'=>'Core du Sud','hr'=>'Croatie','dk'=>'Danemark','ae'=>'Emirat Arabe Unis','ec'=>'Equateur','es'=>'Espagne','ee'=>'Estonie','fi'=>'Finlande','fr'=>'France','gr'=>'Grce','gt'=>'Guatemala','hk'=>'Hong Kong','hu'=>'Hongrie','in'=>'Inde','id'=>'Indonesie','ir'=>'Iran','is'=>'Islande','il'=>'Israel','it'=>'Italie','jp'=>'Japon','jo'=>'Jordanie','kz'=>'Kazakhstan','kw'=>'Koweit','lv'=>'Lttonie','lb'=>'Liban','lt'=>'Lituanie','lu'=>'Luxembourg','my'=>'Malsie','ma'=>'Maroc','mx'=>'Mexique','mn'=>'Mongolie','noa'=>'NoA','no'=>'Norvge','nz'=>'Nouvelle Zlande','uz'=>'Ouzbkistan','pa'=>'Panama','nl'=>'Pays-Bas','pe'=>'Prou','ph'=>'Philippines','pl'=>'Pologne','pt'=>'Portugal','pr'=>'Puerto Rico','cz'=>'Rpublique Tchque','ro'=>'Roumanie','uk'=>'Royaume-Uni','ru'=>'Russie','yu'=>'Serbie et Montenegro','sg'=>'Singapour','sk'=>'Slovaquie','se'=>'Suede','ch'=>'Suisse','tw'=>'Taiwan','th'=>'Thailande','tr'=>'Turquie','ua'=>'Ukraine','us'=>'USA','ve'=>'Venezuela','vn'=>'Vietnam');

$template->setVar(array(
	'membrePseudo'				=>	$info['pseudo'],
	'membreAvatar'				=>	$membres->getAvatar($info['avatar'],$info['mail']),
	'membreGroupe'				=>	$info['groupe'],
	'membreCleanClan'			=>	str_replace(' ','-',$info['clan_nom']),
	'membreDateIns'				=>	$string->formatDate('%A %d %B %Y',$info['date_ins'],true).'  '.$string->formatDate('%H:%M',$info['date_ins']),
	'membreDateNes'				=>	($info['date_nes']==0)?$defTxt:'>'.$string->formatDate('%d %B %Y',$info['date_nes'],true),
	'membreDateNesFormat'		=>	($info['date_nes']==0)?$defTxt:$string->formatDate('%Y-%m-%d',$info['date_nes']),
	'membreDateNesClass'		=>	($info['date_nes']==0)?'':' class="bday"',
	'membreAge'					=>	($info['date_nes']==0)?$defTxt:'>'.$membres->getAge($info['date_nes']),
	'membreNom'					=>	(empty($info['nom']))?$defTxt:'>'.$info['nom'],
	'membrehCardNom'			=>	(empty($info['nom']))?'':$info['nom'],
	'membreNomClass'			=>	(empty($info['nom']))?'':' class="family-name"',
	'membrePrenom'				=>	(empty($info['prenom']))?$defTxt:'>'.$info['prenom'],
	'membrehCardPrenom'			=>	(empty($info['prenom']))?'':$info['prenom'],
	'membrePrenomClass'			=>	(empty($info['prenom']))?'':' class="given-name"',
	'membreNatio'				=>	(empty($info['natio']))?$defTxt:$info['natio'],
	'membrePays'				=>	(empty($info['natio']))?'':$natio[$info['natio']],
	
	'membreWww'					=>	(empty($info['www']))?$defTxt:$info['www'],
	'membreMsn'					=>	(empty($info['msn']))?$defTxt:$membres->antiBot($info['msn']),
	'membreAim'					=>	(empty($info['aim']))?$defTxt:'>'.$info['aim'],
	'membreIcq'					=>	(empty($info['icq']))?$defTxt:'>'.$info['icq'],
	'membreYahoo'				=>	(empty($info['yahoo']))?$defTxt:$membres->antiBot($info['yahoo']),
	'membreSkype'				=>	(empty($info['skype']))?$defTxt:$info['skype'],
	'membreXfire'				=>	(empty($info['xfire']))?$defTxt:$info['xfire'],
	'membreGtalk'				=>	(empty($info['gtalk']))?$defTxt:$membres->antiBot($info['gtalk']),
	
	'membreStatsTotal'			=>	$info['posts'],
	'membreStatsMoreAct1'		=>	$membreStatsMoreAct[0],
	'membreStatsMoreAct2'		=>	$membreStatsMoreAct[1],
	'membreStatsLinkMoreAct2'	=>	$string->clean($membreStatsMoreAct[1]),
	'membreStatsLastAct'		=>	$lastAct,
	'membreStatsLastVis'		=>	$string->formatDate('%A %d %B %Y',$info['last_visit'],true).'  '.$string->formatDate('%H:%M',$info['last_visit']),
	'membreStatsStatut'			=>	($membres->statut($info['last_visit']))?'En-ligne':'Hors-ligne',

	'membreHard1'				=>	(empty($info['hard_1']))?$defTxt:'>'.$info['hard_1'],
	'membreHard2'				=>	(empty($info['hard_2']))?$defTxt:'>'.$info['hard_2'],
	'membreHard3'				=>	(empty($info['hard_3']))?$defTxt:'>'.$info['hard_3'],
	'membreHard4'				=>	(empty($info['hard_4']))?$defTxt:'>'.$info['hard_4'],
	'membreHard5'				=>	(empty($info['hard_5']))?$defTxt:'>'.$info['hard_5'],
	'membreHard6'				=>	(empty($info['hard_6']))?$defTxt:'>'.$info['hard_6'],
	'membreHard7'				=>	(empty($info['hard_7']))?$defTxt:'>'.$info['hard_7'],
	
	'membreSignature'			=>	$bbcode->stripBBCode($bbcode->BBCodeToHtml($info['signature']))
));
?>