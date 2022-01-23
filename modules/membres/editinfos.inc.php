<?php
$site->addToTitle(' - &Eacute;diter mes infos');
$sub_template->setFile('centredroite','membres/editinfos.html');

/**
 * Action envoi formulaire
 */
if($this->action('editInfos')) {
	$membres->editInfos($_POST,$sub_template);

	# Infos persos
	$sub_template->setVar(array(
		'nom'		=>	$_POST['nom'],
		'prenom'	=>	$_POST['prenom'],
		'www'		=>	$_POST['www'],
	
	# Contact
		'mail'		=>	$_POST['mail'],
		'msn'		=>	$_POST['msn'],
		'icq'		=>	$_POST['icq'],
		'aim'		=>	$_POST['aim'],
		'yahoo'		=>	$_POST['yahoo'],
		'skype'		=>	$_POST['skype'],
		'xfire'		=>	$_POST['xfire'],
		'gtalk'		=>	$_POST['gtalk'],
	
	# Materiel
		'hard_1'	=>	$_POST['hard_1'],
		'hard_2'	=>	$_POST['hard_2'],
		'hard_3'	=>	$_POST['hard_3'],
		'hard_4'	=>	$_POST['hard_4'],
		'hard_5'	=>	$_POST['hard_5'],
		'hard_6'	=>	$_POST['hard_6'],
		'hard_7'	=>	$_POST['hard_7']
	));
} else {
	# Infos persos
	$sub_template->setVar(array(
		'nom'		=>	$membres->infos('nom'),
		'prenom'	=>	$membres->infos('prenom'),
		'www'		=>	$membres->infos('www'),
	
	# Contact
		'mail'		=>	$membres->infos('mail'),
		'msn'		=>	$membres->infos('msn'),
		'icq'		=>	$membres->infos('icq'),
		'aim'		=>	$membres->infos('aim'),
		'yahoo'		=>	$membres->infos('yahoo'),
		'skype'		=>	$membres->infos('skype'),
		'xfire'		=>	$membres->infos('xfire'),
		'gtalk'		=>	$membres->infos('gtalk'),
	
	# Materiel
		'hard_1'	=>	$membres->infos('hard_1'),
		'hard_2'	=>	$membres->infos('hard_2'),
		'hard_3'	=>	$membres->infos('hard_3'),
		'hard_4'	=>	$membres->infos('hard_4'),
		'hard_5'	=>	$membres->infos('hard_5'),
		'hard_6'	=>	$membres->infos('hard_6'),
		'hard_7'	=>	$membres->infos('hard_7')
	));
}
# Debut Bloc Jours
$sub_template->setBlock('centredroite','jours');

for($i=1;$i<32;$i++) {
	$sub_template->setVar('jour',$i);
	if(date('j',$membres->infos('date_nes'))==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('jours', true);
}
# Fin Bloc Jours

# Debut Bloc Mois
$sub_template->setBlock('centredroite','moiss');

$mois=array('1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre');

foreach($mois as $i => $var) {
	$sub_template->setVar('mois',$i);
	$sub_template->setVar('nommois',$var);
	if(date('n',$membres->infos('date_nes'))==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('moiss', true);
}
# Fin Bloc Mois

# Debut Bloc Années
$sub_template->setBlock('centredroite','annees');

for($i=(date('Y')-100);$i<date('Y');$i++) {
	$sub_template->setVar('annee',$i);
	if(date('Y',$membres->infos('date_nes'))==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('annees', true);
}
# Fin Bloc Années

# Debut Bloc Nationalités
$natio=array('za'=>'Afrique du Sud','dz'=>'Algerie','de'=>'Allemagne','sa'=>'Arabie Saoudite','ar'=>'Argentine','amM'=>'Arménie','au'=>'Australie','at'=>'Autriche','be'=>'Belgique','by'=>'Biélarussie','br'=>'Brésil','bg'=>'Bulgarie','ca'=>'Canada','cl'=>'Chili','cn'=>'Chine','co'=>'Colombie','kr'=>'Corée du Sud','hr'=>'Croatie','dk'=>'Danemark','ae'=>'Emirat Arabe Unis','ec'=>'Equateur','es'=>'Espagne','ee'=>'Estonie','fi'=>'Finlande','fr'=>'France','gr'=>'Grèce','gt'=>'Guatemala','hk'=>'Hong Kong','hu'=>'Hongrie','in'=>'Inde','id'=>'Indonesie','ir'=>'Iran','is'=>'Islande','il'=>'Israel','it'=>'Italie','jp'=>'Japon','jo'=>'Jordanie','kz'=>'Kazakhstan','kw'=>'Koweit','lv'=>'Léttonie','lb'=>'Liban','lt'=>'Lituanie','lu'=>'Luxembourg','my'=>'Malésie','ma'=>'Maroc','mx'=>'Mexique','mn'=>'Mongolie','noa'=>'NoA','no'=>'Norvège','nz'=>'Nouvelle Zélande','uz'=>'Ouzbékistan','pa'=>'Panama','nl'=>'Pays-Bas','pe'=>'Pérou','ph'=>'Philippines','pl'=>'Pologne','pt'=>'Portugal','pr'=>'Puerto Rico','cz'=>'République Tchèque','ro'=>'Roumanie','uk'=>'Royaume-Uni','ru'=>'Russie','yu'=>'Serbie et Montenegro','sg'=>'Singapour','sk'=>'Slovaquie','se'=>'Suede','ch'=>'Suisse','tw'=>'Taiwan','th'=>'Thailande','tr'=>'Turquie','ua'=>'Ukraine','us'=>'USA','ve'=>'Venezuela','vn'=>'Vietnam');

$sub_template->setBlock('centredroite','natios');

foreach($natio as $i => $var) {
	$sub_template->setVar('natioval',$i);
	$sub_template->setVar('natio',$var);
	if($membres->infos('natio')==$i) $sub_template->setVar('selected',' selected="selected"');
	else $sub_template->unsetVar('selected');
	$sub_template->parse('natios', true);
}
# Fin Bloc Nationalités

?>