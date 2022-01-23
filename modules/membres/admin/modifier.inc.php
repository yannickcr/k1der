<?php
$site->addToTitle(' - &Eacute;diter le profil d\'un membre');
$sub_template->setFile('centredroite','membres/admin/modifier.html');
$site->addCss('templates/'.THEME.'/membres/style.css');

/**
 * Action envoi formulaire
 */
if($this->action('editMembres')) $membres->editMembres($_POST,$sub_template);
	
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
		mod_membres.id="'.$_GET['id'].'"
'));

	# Infos persos
	$sub_template->setVar(array(
		'nom'		=>	$info['nom'],
		'prenom'	=>	$info['prenom'],
		'www'		=>	$info['www'],
	
	# Contact
		'mail'		=>	$info['mail'],
		'msn'		=>	$info['msn'],
		'icq'		=>	$info['icq'],
		'aim'		=>	$info['aim'],
		'yahoo'		=>	$info['yahoo'],
		'skype'		=>	$info['skype'],
		'xfire'		=>	$info['xfire'],
		'gtalk'		=>	$info['gtalk'],

	# Materiel
		'hard_1'	=>	$info['hard_1'],
		'hard_2'	=>	$info['hard_2'],
		'hard_3'	=>	$info['hard_3'],
		'hard_4'	=>	$info['hard_4'],
		'hard_5'	=>	$info['hard_5'],
		'hard_6'	=>	$info['hard_6'],
		'hard_7'	=>	$info['hard_7']
	));

# Debut Bloc Jours
$sub_template->setBlock('centredroite','jours');

for($i=1;$i<32;$i++) {
	$sub_template->setVar('jour',$i);
	if(date('j',$info['date_nes'])==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('jours', true);
}
# Fin Bloc Jours

# Debut Bloc Mois
$sub_template->setBlock('centredroite','moiss');

$mois=array('1'=>'Janvier','2'=>'Fvrier','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Aot','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Dcembre');

foreach($mois as $i => $var) {
	$sub_template->setVar('mois',$i);
	$sub_template->setVar('nommois',$var);
	if(date('n',$info['date_nes'])==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('moiss', true);
}
# Fin Bloc Mois

# Debut Bloc Annes
$sub_template->setBlock('centredroite','annees');

for($i=(date('Y')-100);$i<date('Y');$i++) {
	$sub_template->setVar('annee',$i);
	if(date('Y',$info['date_nes'])==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('annees', true);
}
# Fin Bloc Annes

# Debut Bloc Nationalits
$natio=array('za'=>'Afrique du Sud','dz'=>'Algerie','de'=>'Allemagne','sa'=>'Arabie Saoudite','ar'=>'Argentine','amM'=>'Armnie','au'=>'Australie','at'=>'Autriche','be'=>'Belgique','by'=>'Bilarussie','br'=>'Brsil','bg'=>'Bulgarie','ca'=>'Canada','cl'=>'Chili','cn'=>'Chine','co'=>'Colombie','kr'=>'Core du Sud','hr'=>'Croatie','dk'=>'Danemark','ae'=>'Emirat Arabe Unis','ec'=>'Equateur','es'=>'Espagne','ee'=>'Estonie','fi'=>'Finlande','fr'=>'France','gr'=>'Grce','gt'=>'Guatemala','hk'=>'Hong Kong','hu'=>'Hongrie','in'=>'Inde','id'=>'Indonesie','ir'=>'Iran','is'=>'Islande','il'=>'Israel','it'=>'Italie','jp'=>'Japon','jo'=>'Jordanie','kz'=>'Kazakhstan','kw'=>'Koweit','lv'=>'Lttonie','lb'=>'Liban','lt'=>'Lituanie','lu'=>'Luxembourg','my'=>'Malsie','ma'=>'Maroc','mx'=>'Mexique','mn'=>'Mongolie','noa'=>'NoA','no'=>'Norvge','nz'=>'Nouvelle Zlande','uz'=>'Ouzbkistan','pa'=>'Panama','nl'=>'Pays-Bas','pe'=>'Prou','ph'=>'Philippines','pl'=>'Pologne','pt'=>'Portugal','pr'=>'Puerto Rico','cz'=>'Rpublique Tchque','ro'=>'Roumanie','uk'=>'Royaume-Uni','ru'=>'Russie','yu'=>'Serbie et Montenegro','sg'=>'Singapour','sk'=>'Slovaquie','se'=>'Suede','ch'=>'Suisse','tw'=>'Taiwan','th'=>'Thailande','tr'=>'Turquie','ua'=>'Ukraine','us'=>'USA','ve'=>'Venezuela','vn'=>'Vietnam');

$sub_template->setBlock('centredroite','natios');

foreach($natio as $i => $var) {
	$sub_template->setVar('natioval',$i);
	$sub_template->setVar('natio',$var);
	if($info['natio']==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('natios', true);
}
# Fin Bloc Nationalits

# Debut Bloc Signature

$site->barreMiseEnForme($sub_template,'lite');

if($info['signature']!='') {
	$bbcode = new bbcode;
	$signatureHtml=$bbcode->stripBBCode($bbcode->BBCodeToHtml($info['signature']));
	$sub_template->setVar(array('signatureHtml'=>$signatureHtml,'signature'=>$info['signature']));
	$sub_template->setBlock('centredroite','signature-set');
	$sub_template->parse('signature-set',true);
} else {
	$sub_template->setBlock('centredroite','signature-notset');
	$sub_template->parse('signature-notset',true);
}

# Fin Bloc Signature
?>